<?php

declare(strict_types=1);

use Doctrine\Common\Cache\ApcuCache;
use Doctrine\Common\Cache\PredisCache;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Logging\EchoSQLLogger;
use Doctrine\ORM\Cache\DefaultCacheFactory;
use Doctrine\ORM\Cache\Logging\CacheLogger;
use Doctrine\ORM\Cache\Logging\StatisticsCacheLogger;
use Doctrine\ORM\Cache\RegionsConfiguration;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Setup;
use Domynation\Authentication\UserInterface;
use Domynation\Bus\BasicCommandBus;
use Domynation\Bus\CommandBusInterface;
use Domynation\Bus\Middlewares\CachingMiddleware;
use Domynation\Bus\Middlewares\LoggingMiddleware;
use Domynation\Cache\CacheInterface;
use Domynation\Cache\InMemoryCache;
use Domynation\Cache\RedisCache;
use Domynation\Communication\DebugMailer;
use Domynation\Communication\MailerInterface;
use Domynation\Communication\MailgunMailer;
use Domynation\Communication\MarkdownParserInterface;
use Domynation\Communication\NativeMailer;
use Domynation\Communication\ParsedownMarkdownParser;
use Domynation\Config\ConfigInterface;
use Domynation\Eventing\BasicEventDispatcher;
use Domynation\Eventing\EventDispatcherInterface;
use Domynation\Http\Middlewares\HandlingMiddleware;
use Domynation\Http\RouterInterface;
use Domynation\Http\SymfonyRouter;
use Domynation\I18N\Translator;
use Domynation\Security\NativePassword;
use Domynation\Security\PasswordInterface;
use Domynation\Session\SessionInterface;
use Domynation\Storage\AwsS3FileStorage;
use Domynation\Storage\NativeFileStorage;
use Domynation\Storage\RackspaceFileStorage;
use Domynation\Storage\StorageInterface;
use Domynation\Storage\UnitTestStorage;
use Domynation\View\TwigViewFactory;
use Domynation\View\ViewFactoryInterface;
use Invoker\InvokerInterface;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Predis\Client;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Translation\Loader\YamlFileLoader;

return [
    LoggerInterface::class => function (ConfigInterface $config) {
        $loggingConfigs = $config->get('logging');
        $logsPath = $config->get('basePath') . ($loggingConfigs['logsPath'] ?? '/app.log');

        $appLogger = new Logger('App_Logger');
        $appLogger->pushHandler(new StreamHandler($logsPath, Monolog\Logger::DEBUG));

        return $appLogger;
    },

    CacheLogger::class => function () {
        return new StatisticsCacheLogger();
    },

    EntityManager::class => function (ConfigInterface $config, CacheLogger $cacherLogger) {
        $ormConfig = Setup::createAnnotationMetadataConfiguration(
            $config->get('entityDirectories'),
            $config->get('isDevMode'),
            $config->get('basePath') . '/cache/orm'
        );

        if (!$config->get('isDevMode')) {
            $cacheConfig = $config->get('caching');
            $apcuCache = new ApcuCache();
            $redisCache = new PredisCache(new Client([
                'scheme' => 'tcp',
                'host'   => $cacheConfig['redis']['host'],
                'port'   => $cacheConfig['redis']['port']
            ]));

//            $apcuCache->flushAll();
//            $redisCache->flushAll();

            // Second level cache configuration
            $cacheFactory = new DefaultCacheFactory(
                new RegionsConfiguration,
                $redisCache
            );

            $ormConfig->setMetadataCacheImpl($apcuCache);
            $ormConfig->setQueryCacheImpl($apcuCache);
            $ormConfig->setResultCacheImpl($redisCache);
            $ormConfig->setSecondLevelCacheEnabled();
            $ormConfig->getSecondLevelCacheConfiguration()->setCacheFactory($cacheFactory);
            $ormConfig->getSecondLevelCacheConfiguration()->setCacheLogger($cacherLogger);
        }

        // Determine which database environnment to load
        $dbConfig = $config->get('databases');
        $dbEnv = $config->get('environment') === 'test' ? 'test' : 'web';

        // Debug all SQL queries
        if ($dbConfig[$dbEnv]['debugSql'] ?? false) {
            $ormConfig->setSQLLogger(new EchoSQLLogger);
        }

        return EntityManager::create([
            'host'     => $dbConfig[$dbEnv]['host'],
            'driver'   => $dbConfig[$dbEnv]['driver'],
            'dbname'   => $dbConfig[$dbEnv]['name'],
            'user'     => $dbConfig[$dbEnv]['user'],
            'password' => $dbConfig[$dbEnv]['password'],
            'charset'  => 'utf8'
        ], $ormConfig);
    },

    Connection::class => function (EntityManager $em) {
        return $em->getConnection();
    },

    CommandBusInterface::class => function (
        ContainerInterface $container,
        UserInterface $user,
        EventDispatcherInterface $dispatcher,
        CacheInterface $cache,
        ConfigInterface $config
    ) {
        // Configure logger
        $busConfigs = $config->get('bus');

        $busMiddlewares = [
            new CachingMiddleware($cache, $config->get('bus')['cacheDuration']),
        ];

        if ($busConfigs['enableLogging']) {
            $logsPath = $config->get('basePath') . ($busConfigs['logsPath'] ?? '/bus.log');
            $busLogger = new Monolog\Logger('Bus_logger');
            $busLogger->pushHandler(new StreamHandler($logsPath, Monolog\Logger::INFO));

            $busMiddlewares[] = new LoggingMiddleware($busLogger, $user);
        }

        $busMiddlewares[] = new \Domynation\Bus\Middlewares\HandlingMiddleware;

        return new BasicCommandBus($container, $dispatcher, $busMiddlewares);
    },

    RouterInterface::class => function (ContainerInterface $container, ConfigInterface $config, InvokerInterface $invoker, SessionInterface $session) {
        $routingConfig = $config->get('routing');

        // Resolve all middleware through the container
        $middlewares = array_map(function ($middlewareName) use ($container) {
            return $container->get($middlewareName);
        }, $routingConfig[$config->get('environment')]['middlewares']);

        // Append the handling middleware at the end
        $middlewares[] = new HandlingMiddleware($container, $invoker, $session);

        return new SymfonyRouter($middlewares);
    },

    CacheInterface::class => function (ConfigInterface $config) {
        if ($config->get('isDevMode')) {
            return new InMemoryCache;
        }

        $cacheConfig = $config->get('caching');

        switch ($cacheConfig['driver']) {
            case 'redis':
                return new RedisCache($cacheConfig['redis']['host'], $cacheConfig['redis']['port']);
            default:
                return new InMemoryCache;
                break;
        }
    },

    MarkdownParserInterface::class => function () {
        $parsedown = new Parsedown;
        $parsedown->setBreaksEnabled(true);
        $parsedown->setUrlsLinked(true);
        $parsedown->setMarkupEscaped(true);

        return new ParsedownMarkdownParser($parsedown);
    },

    PasswordInterface::class => function () {
        return new NativePassword;
    },

    StorageInterface::class => function (ConfigInterface $config) {
        if ($config->get('environment') === 'test') {
            return new UnitTestStorage;
        }

        $storageConfig = $config->get('storage');

        switch ($storageConfig['driver']) {
            case 'aws':
                return new AwsS3FileStorage($storageConfig['aws']['region'], $storageConfig['aws']['apiKey'], $storageConfig['aws']['secretKey'], '');
                break;

            case 'rackspace':
                return new RackspaceFileStorage($storageConfig['rackspace']['username'], $storageConfig['rackspace']['password']);
                break;

            case 'file':
            default:
                return new NativeFileStorage($storageConfig['file']['directory'], $storageConfig['file']['uri']);
                break;
        }
    },

    MailerInterface::class => function (ConfigInterface $config) {
        $emailConfig = $config->get('emailing');

        switch ($emailConfig['driver']) {
            case 'mailgun':
                $mailer = new MailgunMailer($emailConfig['mailgun']['apiKey'], $emailConfig['mailgun']['domain']);
                break;

            case 'native':
            default:
                $mailer = new NativeMailer;
                break;
        }

        // In dev mode, use the DebugMailer
        return $config->get('isDevMode')
            ? new DebugMailer($mailer, $emailConfig['debugEmail'])
            : $mailer;
    },

    ViewFactoryInterface::class => function (ConfigInterface $config, Translator $translator, Request $request) {
        $options = [
            'cache'            => $config->get('basePath') . '/cache/views',
            'debug'            => false,
            'strict_variables' => true,
        ];

        if ($config->get('isDevMode')) {
            $options['debug'] = true;
            $options['auto_reload'] = true;
        }

        $viewsConfig = $config->get('views');
        $twig = new Twig_Environment(new Twig_Loader_Filesystem($config->get('basePath') . $viewsConfig['path']), $options);
        $instance = new TwigViewFactory($twig, $config->get('basePath') . $viewsConfig['path'], $viewsConfig['fileExtension']);

        require_once __DIR__ . '/twig.php';

        return $instance;
    },

    EventDispatcherInterface::class => function (ConfigInterface $config, InvokerInterface $invoker, ContainerInterface $container) {
        $eventingConfig = $config->get('eventing');

        // Resolve all middleware through the container
        $middlewares = array_map(function ($middlewareName) use ($container) {
            return $container->get($middlewareName);
        }, $eventingConfig[$config->get('environment')]['middlewares']);

        return new BasicEventDispatcher($invoker, $middlewares);
    },

    Translator::class => function (ConfigInterface $config, Request $request) {
        $i18nConfig = $config->get('i18n');
        $sourceDir = $config->get('basePath') . $i18nConfig['sourceDir'];

        $translator = new \Symfony\Component\Translation\Translator($request->getLocale());
        $translator->addLoader('yaml', new YamlFileLoader);

        foreach (new FilesystemIterator($sourceDir, FilesystemIterator::SKIP_DOTS) as $file) {
            // Ignore non-YAML files
            if (!in_array($file->getExtension(), ['yml', 'yaml'], true)) {
                continue;
            }

            // Ignore files whose names are not in the correct format
            if (!preg_match('/^.*?\.(.*?)\.(?:yml|yaml)$/', $file->getRealPath(), $matches)) {
                continue;
            }

            // Add the resource
            $translator->addResource('yaml', $file->getRealPath(), $matches[1], 'messages+intl-icu');
        }

        return new Translator($translator->getLocale(), $i18nConfig['supportedLocales'], $translator);
    }
];

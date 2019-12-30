<?php

return [
    \Psr\Log\LoggerInterface::class => function (\Domynation\Config\ConfigInterface $config) {
        $loggingConfigs = $config->get('logging');
        $logsPath = $config->get('basePath') . ($loggingConfigs['logsPath'] ?? '/app.log');

        $appLogger = new Monolog\Logger('App_Logger');
        $appLogger->pushHandler(new Monolog\Handler\StreamHandler($logsPath, Monolog\Logger::DEBUG));

        return $appLogger;
    },

    \Doctrine\ORM\Cache\Logging\CacheLogger::class => function () {
        return new \Doctrine\ORM\Cache\Logging\StatisticsCacheLogger();
    },

    \Doctrine\ORM\EntityManager::class => function (\Domynation\Config\ConfigInterface $config, \Doctrine\ORM\Cache\Logging\CacheLogger $cacherLogger) {
        $ormConfig = Doctrine\ORM\Tools\Setup::createAnnotationMetadataConfiguration(
            $config->get('entityDirectories'),
            $config->get('isDevMode'),
            $config->get('basePath') . '/cache/orm'
        );

        if (!$config->get('isDevMode')) {
            $cacheConfig = $config->get('caching');
            $apcuCache = new \Doctrine\Common\Cache\ApcuCache();
            $redisCache = new \Doctrine\Common\Cache\PredisCache(new \Predis\Client([
                'scheme' => 'tcp',
                'host'   => $cacheConfig['redis']['host'],
                'port'   => $cacheConfig['redis']['port']
            ]));

//            $apcuCache->flushAll();
//            $redisCache->flushAll();

            // Second level cache configuration
            $cacheFactory = new \Doctrine\ORM\Cache\DefaultCacheFactory(
                new \Doctrine\ORM\Cache\RegionsConfiguration,
                $redisCache
            );

            $ormConfig->setMetadataCacheImpl($apcuCache);
            $ormConfig->setQueryCacheImpl($apcuCache);
            $ormConfig->setResultCacheImpl($redisCache);
            $ormConfig->setSecondLevelCacheEnabled();
            $ormConfig->getSecondLevelCacheConfiguration()->setCacheFactory($cacheFactory);
            $ormConfig->getSecondLevelCacheConfiguration()->setCacheLogger($cacherLogger);
        }

        // Uncomment the following to debug every request made to the DB
//        $ormConfig->setSQLLogger(new Doctrine\DBAL\Logging\EchoSQLLogger);

        // Determine which database environnment to load
        $dbConfig = $config->get('databases');
        $dbEnv = $config->get('environment') === 'test' ? 'test' : 'web';

        return Doctrine\ORM\EntityManager::create([
            'host'     => $dbConfig[$dbEnv]['host'],
            'driver'   => $dbConfig[$dbEnv]['driver'],
            'dbname'   => $dbConfig[$dbEnv]['name'],
            'user'     => $dbConfig[$dbEnv]['user'],
            'password' => $dbConfig[$dbEnv]['password'],
            'charset'  => 'utf8'
        ], $ormConfig);
    },

    \Doctrine\DBAL\Connection::class => function (\Doctrine\ORM\EntityManager $em) {
        return $em->getConnection();
    },

    \Domynation\Bus\CommandBusInterface::class => function (
        Psr\Container\ContainerInterface $container,
        \Domynation\Authentication\UserInterface $user,
        \Domynation\Eventing\EventDispatcherInterface $dispatcher,
        \Domynation\Cache\CacheInterface $cache,
        \Domynation\Config\ConfigInterface $config
    ) {
        // Configure logger
        $busConfigs = $config->get('bus');

        $busMiddlewares = [
            new \Domynation\Bus\Middlewares\CachingMiddleware($cache, $config->get('bus')['cacheDuration']),
        ];

        if ($busConfigs['enableLogging']) {
            $logsPath = $config->get('basePath') . ($busConfigs['logsPath'] ?? '/bus.log');
            $busLogger = new Monolog\Logger('Bus_logger');
            $busLogger->pushHandler(new Monolog\Handler\StreamHandler($logsPath, Monolog\Logger::INFO));

            $busMiddlewares[] = new \Domynation\Bus\Middlewares\LoggingMiddleware($busLogger, $user);
        }

        $busMiddlewares[] = new \Domynation\Bus\Middlewares\HandlingMiddleware;

        return new Domynation\Bus\BasicCommandBus($container, $dispatcher, $busMiddlewares);
    },

    \Domynation\Http\RouterInterface::class => function (Psr\Container\ContainerInterface $container, \Domynation\Config\ConfigInterface $config, \Invoker\InvokerInterface $invoker, \Domynation\Session\SessionInterface $session) {
        $routingConfig = $config->get('routing');

        // Resolve all middleware through the container
        $middlewares = array_map(function ($middlewareName) use ($container) {
            return $container->get($middlewareName);
        }, $routingConfig[$config->get('environment')]['middlewares']);

        // Append the handling middleware at the end
        $middlewares[] = new \Domynation\Http\Middlewares\HandlingMiddleware($invoker, $session);

        return new \Domynation\Http\SymfonyRouter($middlewares);
    },

    \Domynation\Cache\CacheInterface::class => function (\Domynation\Config\ConfigInterface $config) {
        if ($config->get('isDevMode')) {
            return new \Domynation\Cache\InMemoryCache;
        }

        $cacheConfig = $config->get('caching');

        switch ($cacheConfig['driver']) {
            case 'redis':
                return new \Domynation\Cache\RedisCache($cacheConfig['redis']['host'], $cacheConfig['redis']['port']);
            default:
                return new \Domynation\Cache\InMemoryCache;
                break;
        }
    },

    \Domynation\Communication\WebSocketInterface::class => function (\Domynation\Config\ConfigInterface $config) {
        $wsConfig = $config->get('websocket');

        switch ($wsConfig['driver']) {
            case 'pusher':
            default:
                return new \Domynation\Communication\PusherWebSocket($wsConfig['pusher']['apiKey'], $wsConfig['pusher']['secretKey'], $wsConfig['pusher']['appId']);
                break;
        }
    },

    \Domynation\Communication\MarkdownParserInterface::class => function () {
        $parsedown = new Parsedown;
        $parsedown->setBreaksEnabled(true);
        $parsedown->setUrlsLinked(true);
        $parsedown->setMarkupEscaped(true);

        return new \Domynation\Communication\ParsedownMarkdownParser($parsedown);
    },

    \Domynation\Security\PasswordInterface::class => function () {
        return new \Domynation\Security\NativePassword;
    },

    \Domynation\Storage\StorageInterface::class => function (\Domynation\Config\ConfigInterface $config) {
        $storageConfig = $config->get('storage');

        switch ($storageConfig['driver']) {
            case 'aws':
                return new \Domynation\Storage\AwsS3FileStorage($storageConfig['aws']['region'], $storageConfig['aws']['apiKey'], $storageConfig['aws']['secretKey']);
                break;

            case 'rackspace':
                return new Domynation\Storage\RackspaceFileStorage($storageConfig['rackspace']['username'], $storageConfig['rackspace']['password']);
                break;

            case 'file':
            default:
                return new \Domynation\Storage\NativeFileStorage($storageConfig['file']['directory'], $storageConfig['file']['uri']);
                break;
        }
    },

    \Domynation\Communication\MailerInterface::class => function (\Domynation\Config\ConfigInterface $config) {
        $emailConfig = $config->get('emailing');

        switch ($emailConfig['driver']) {
            case 'aws':
                return new \Domynation\Communication\AwsSesMailer($emailConfig['domain'], $emailConfig['region'], $emailConfig['apiKey'], $emailConfig['secretKey']);
                break;

            case 'mailgun':
                $mailer = new Domynation\Communication\MailgunMailer($emailConfig['mailgun']['apiKey'], $emailConfig['mailgun']['domain']);
                break;

            case 'native':
            default:
                $mailer = new \Domynation\Communication\NativeMailer;
                break;
        }

        // In dev mode, use the DebugMailer
        return $config->get('isDevMode')
            ? new \Domynation\Communication\DebugMailer($mailer, $emailConfig['debugEmail'])
            : $mailer;
    },

    \Domynation\View\ViewFactoryInterface::class => function (\Domynation\Config\ConfigInterface $config) {
        $devConfig = $config->get('dev');

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
        $instance = new \Domynation\View\TwigViewFactory($twig, $config->get('basePath') . $viewsConfig['path'], $viewsConfig['fileExtension']);

        require_once __DIR__ . '/twig.php';

        return $instance;
    },

    \Domynation\Eventing\EventDispatcherInterface::class => function (\Domynation\Config\ConfigInterface $config, \Invoker\InvokerInterface $invoker, Psr\Container\ContainerInterface $container) {
        $eventingConfig = $config->get('eventing');

        // Resolve all middleware through the container
        $middlewares = array_map(function ($middlewareName) use ($container) {
            return $container->get($middlewareName);
        }, $eventingConfig['middlewares'] ?? []);

        return new \Domynation\Eventing\BasicEventDispatcher($invoker, $middlewares);
    },

    \Domynation\Entities\EntityRegistry::class => function () {
        return new \Domynation\Entities\EntityRegistry;
    },

    \Domynation\Search\SearchInterface::class => function () {
        return new \Domynation\Search\ElasticSearch(
            \Elasticsearch\ClientBuilder::create()->build()
        );
    },
];

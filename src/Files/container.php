<?php

return [
    \Psr\Log\LoggerInterface::class                => function (\Domynation\Config\ConfigInterface $config) {
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
            !IS_PRODUCTION,
            $config->get('basePath') . '/cache/orm'
        );

        if (IS_PRODUCTION) {
            $apcuCache = new \Doctrine\Common\Cache\ApcuCache();
            $redisCache = new \Doctrine\Common\Cache\PredisCache(new \Predis\Client([
                'scheme' => 'tcp',
                'host'   => REDIS_HOST,
                'port'   => REDIS_PORT
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

    \Doctrine\DBAL\Connection::class => function (\Domynation\Config\ConfigInterface $config) {
        $dbConfig = $config->get('databases');
        $dbEnv = $config->get('environment') === 'test' ? 'test' : 'web';

        $db = \Doctrine\DBAL\DriverManager::getConnection([
            'host'     => $dbConfig[$dbEnv]['host'],
            'driver'   => $dbConfig[$dbEnv]['driver'],
            'dbname'   => $dbConfig[$dbEnv]['name'],
            'user'     => $dbConfig[$dbEnv]['user'],
            'password' => $dbConfig[$dbEnv]['password'],
            'charset'  => 'utf8'
        ], new \Doctrine\DBAL\Configuration());

        // @todo: Extremely ugly hack until all the event listeners are refactored.
        \Event::setDatabase($db);

        return $db;
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
        $logsPath = $config->get('basePath') . ($busConfigs['logsPath'] ?? '/bus.log');
        $busLogger = new Monolog\Logger('Bus_logger');
        $busLogger->pushHandler(new Monolog\Handler\StreamHandler($logsPath, Monolog\Logger::INFO));

        return new Domynation\Bus\BasicCommandBus(
            $container,
            $dispatcher,
            [
                new \Domynation\Bus\Middlewares\AuthorizationMiddleware,
                new \Domynation\Bus\Middlewares\CachingMiddleware($cache, $config->get('bus')['cacheDuration']),
                new \Domynation\Bus\Middlewares\LoggingMiddleware($busLogger, $user),
                new \Domynation\Bus\Middlewares\HandlingMiddleware
            ]
        );
    },

    \Domynation\Http\RouterInterface::class => function (Psr\Container\ContainerInterface $container, \Domynation\Config\ConfigInterface $config, \Invoker\InvokerInterface $invoker, \Domynation\Session\SessionInterface $session) {
        $routingConfig = $config->get('routing');

        // Resolve all middleware through the container
        $middlewares = array_map(function ($middlewareName) use ($container) {
            return $container->get($middlewareName);
        }, $routingConfig['middlewares']);

        // Append the handling middleware at the end
        $middlewares[] = new \Domynation\Http\Middlewares\HandlingMiddleware($invoker, $session);

        return new \Domynation\Http\SymfonyRouter($middlewares);
    },

    \Domynation\Cache\CacheInterface::class => function () {
        if (!IS_PRODUCTION) {
            return new \Domynation\Cache\InMemoryCache;
        }
       
        switch (CACHE_DRIVER) {
            case 'redis':
                return new \Domynation\Cache\RedisCache(REDIS_HOST, REDIS_PORT);
            default:
                return new \Domynation\Cache\InMemoryCache;
                break;
        }
    },

    \Domynation\Communication\WebSocketInterface::class => function () {
        switch (WEBSOCKET_DRIVER) {
            case 'pusher':
            default:
                return new \Domynation\Communication\PusherWebSocket(PUSHER_API_KEY, PUSHER_API_SECRET_KEY, PUSHER_APP_ID);
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

    \Domynation\Storage\StorageInterface::class => function () {
        switch (STORAGE_DRIVER) {
            case 'aws':
                return new \Domynation\Storage\AwsS3FileStorage(AWS_REGION, AWS_API_KEY, AWS_SECRET_KEY);
                break;

            case 'rackspace':
                return new Domynation\Storage\RackspaceFileStorage(RACKSPACE_USERNAME, RACKSPACE_PASSWORD);
                break;

            case 'file':
            default:
                return new \Domynation\Storage\NativeFileStorage(STORAGE_FILE_DIRECTORY, STORAGE_FILE_URI);
                break;
        }
    },

    \Domynation\Communication\MailerInterface::class => function () {
        switch (EMAIL_DRIVER) {
            case 'aws':
                return new \Domynation\Communication\AwsSesMailer(AWS_SES_DOMAIN, AWS_REGION, AWS_API_KEY, AWS_SECRET_KEY);
                break;

            case 'mailgun':
                $mailer = new Domynation\Communication\MailgunMailer(MAILGUN_API_KEY, MAILGUN_DEFAULT_DOMAIN, EMAIL_DEFAULT_SENDER);
                break;

            case 'native':
            default:
                $mailer = new \Domynation\Communication\NativeMailer;
                break;
        }

        if (IS_PRODUCTION) {
            return $mailer;
        }

        // In a dev environment, wrap the mailer implementation into the DebugMailer
        return new \Domynation\Communication\DebugMailer($mailer, EMAIL_DEBUG);
    },

    \Domynation\View\ViewFactoryInterface::class => function (\Domynation\Config\ConfigInterface $config) {
        $options = [
            'cache'            => $config->get('basePath') . '/cache',
            'debug'            => false,
            'strict_variables' => true,
        ];

        if (!IS_PRODUCTION) {
            $options['debug'] = true;
            $options['auto_reload'] = true;
        }

        $viewsConfig = $config->get('views');
        $twig = new Twig_Environment(new Twig_Loader_Filesystem($config->get('basePath') . $viewsConfig['path']), $options);
        $instance = new \Domynation\View\TwigViewFactory($twig, $config->get('basePath') . $viewsConfig['path'], $viewsConfig['fileExtension']);

        include_once __DIR__ . '/twig.php';

        return $instance;
    },

    \Domynation\Eventing\EventDispatcherInterface::class => function (\Invoker\InvokerInterface $invoker) {
        return new \Domynation\Eventing\BasicEventDispatcher($invoker);
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

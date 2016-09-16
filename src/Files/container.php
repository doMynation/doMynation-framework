<?php

return [
    \Interop\Container\ContainerInterface::class => function (\Interop\Container\ContainerInterface $container) {
        return $container;
    },

    \Doctrine\ORM\EntityManager::class => function (\Domynation\Config\ConfigInterface $config) {
        $devMode = !IS_PRODUCTION;

        $config = Doctrine\ORM\Tools\Setup::createAnnotationMetadataConfiguration($config->get('entityDirectories'), $devMode);

        if (IS_PRODUCTION) {
            $config->setMetadataCacheImpl(new \Doctrine\Common\Cache\ApcuCache());
            $config->setQueryCacheImpl(new \Doctrine\Common\Cache\ApcuCache());
        }

        // Uncomment the following to debug every request made to the DB
        //$config->setSQLLogger(new Doctrine\DBAL\Logging\EchoSQLLogger);

        return Doctrine\ORM\EntityManager::create([
            'driver'   => DB_DRIVER,
            'dbname'   => DB_DATABASE,
            'user'     => DB_USER,
            'password' => DB_PASSWORD,
            'charset'  => 'utf8'
        ], $config);
    },

    \Doctrine\DBAL\Connection::class => function () {
        $db = \Doctrine\DBAL\DriverManager::getConnection([
            'driver'   => DB_DRIVER,
            'dbname'   => DB_DATABASE,
            'user'     => DB_USER,
            'password' => DB_PASSWORD,
            'charset'  => 'utf8'
        ], new \Doctrine\DBAL\Configuration());

        // @todo: Extremely ugly hack until all the event listeners are refactored.
        \Event::setDatabase($db);

        return $db;
    },

    \Domynation\Bus\CommandBusInterface::class => function (
        \Interop\Container\ContainerInterface $container,
        \Domynation\Authentication\AuthenticatorInterface $auth,
        \Domynation\Eventing\EventDispatcherInterface $dispatcher,
        \Domynation\Cache\CacheInterface $cache
    ) {
        $busLogger = new Monolog\Logger('Bus_logger');
        $busLogger->pushHandler(new Monolog\Handler\StreamHandler(PATH_BASE . '/logs/bus.log', Monolog\Logger::INFO));

        return new Domynation\Bus\BasicCommandBus(
            $container,
            $dispatcher,
            [
                new \Domynation\Bus\Middlewares\AuthorizationMiddleware($auth),
                new \Domynation\Bus\Middlewares\CachingMiddleware($cache),
                new \Domynation\Bus\Middlewares\LoggingMiddleware($busLogger, $auth),
                new \Domynation\Bus\Middlewares\HandlingMiddleware
            ]);
    },

    \Domynation\Http\Router::class => function (\Interop\Container\ContainerInterface $container, \Domynation\Authentication\UserInterface $user) {
        $routerLogger = new Monolog\Logger('Router_logger');
        $routerLogger->pushHandler(new Monolog\Handler\StreamHandler(PATH_BASE . '/logs/router.log', Monolog\Logger::INFO));

        return new \Domynation\Http\Router(
            $container,
            new \Domynation\Http\AuthenticationMiddleware($user),
            new \Domynation\Http\AuthorizationMiddleware($user),
            new \Domynation\Http\ValidationMiddleware($container),
            new \Domynation\Http\LoggingMiddleware($routerLogger, $user),
            new \Domynation\Http\HandlingMiddleware($container)
        );
    },

    \Domynation\Http\RouterInterface::class => function (\Interop\Container\ContainerInterface $container, \Domynation\Config\ConfigInterface $config) {
        // Resolve all middleware through the container
        $middlewares = array_map(function ($middlewareName) use ($container) {
            return $container->get($middlewareName);
        }, $config->get('routeMiddlewares'));

        // Append the handling middleware at the end
        $middlewares[] = new \Domynation\Http\Middlewares\HandlingMiddleware($container);

        return new \Domynation\Http\SymfonyRouter($middlewares);
    },

    \Domynation\Cache\CacheInterface::class => function () {
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
        switch (PASSWORD_DRIVER) {
            case 'md5':
                return new \Domynation\Security\Md5Password;
                break;
            case 'native':
            default:
                return new \Domynation\Security\NativePassword;
                break;
        }
    },

    \Domynation\Security\PasswordGeneratorInterface::class => function () {
        return new \Domynation\Security\BasicPasswordGenerator;
    },

    \Domynation\Authentication\AuthenticatorInterface::class => function (\Doctrine\DBAL\Connection $db, \Domynation\Session\SessionInterface $session, \Domynation\Security\PasswordInterface $password) {
        $instance = new \Domynation\Authentication\BasicAuthenticator($db, $session, $password);

        // Attempt to remmember an active user session
        $instance->remember();

        return $instance;
    },

    \Domynation\Authentication\UserInterface::class => function (\Domynation\Authentication\AuthenticatorInterface $auth) {
        if (!$auth->isAuthenticated()) {
            return new \Domynation\Authentication\NullUser;
        }

        return $auth->getUser();
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
            'cache'            => PATH_BASE . '/cache',
            'debug'            => false,
            'strict_variables' => true,
            'charset'          => 'iso-8859-1'
        ];

        if (!IS_PRODUCTION) {
            $options['debug']       = true;
            $options['auto_reload'] = true;
        }

        $twig = new Twig_Environment(new Twig_Loader_Filesystem(PATH_HTML), $options);

        $instance = new \Domynation\View\TwigViewFactory($twig, $config->get('viewFileExtension'));

        include_once __DIR__ . '/twig.php';

        return $instance;
    },

    \Domynation\Eventing\EventDispatcherInterface::class => function (\Interop\Container\ContainerInterface $container) {
        return new \Domynation\Eventing\BasicEventDispatcher($container);
    },

    \Domynation\Entities\EntityRegistry::class => function () {
        return new \Domynation\Entities\EntityRegistry();
    },

    \Domynation\Search\SearchInterface::class => function () {
        return new \Domynation\Search\ElasticSearch(
            \Elasticsearch\ClientBuilder::create()->build()
        );
    },

    // aliases
    'view'                                    => \DI\get(\Domynation\View\ViewFactoryInterface::class),
];

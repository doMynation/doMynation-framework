<?php

return [
    // Settings this to `true` will deactivate caching mechanisms and enable debug mode on
    // various modules/components of the application. This should be set to `false` in production.
    'isDevMode' => true,

    'views'             => [
        // The path where views are located
        'path'          => '/src/views',

        // The default extension for view files. Domynation will automatically append
        // this value when rendering a view through the ViewFactoryInterface.
        'fileExtension' => 'html.twig',
    ],

    // The folders where doctrine domain entities are located. Used for caching entity metadata.
    'entityDirectories' => [],

    'routing' => [
        'logsPath' => '/storage/logs/router.log',
        'web'      => [
            // The middlewares chain (from top to bottom) that will intercept each request in a Web environment
            'middlewares' => [
                Domynation\Http\Middlewares\AuthenticationMiddleware::class,
                Domynation\Http\Middlewares\AuthorizationMiddleware::class,
                Domynation\Http\Middlewares\ValidationMiddleware::class,
            ],
        ],
        'test'     => [
            // The middlewares chain (from top to bottom) that will intercept each request in automated tests
            'middlewares' => [
                Domynation\Http\Middlewares\ValidationMiddleware::class,
            ],
        ],
    ],

    'eventing' => [
        'web'  => [
            // The middlewares chain (from top to bottom) that will intercept each event in a Web environment
            'middlewares' => []
        ],
        'test' => [
            // The middlewares chain (from top to bottom) that will intercept each event in automated tests
            'middlewares' => [],
        ],
    ],

    // The modules to load.
    'modules'  => [
        App\MyModule::class
    ],

    'caching' => [
        'driver' => 'memory', // one of: 'memory', 'redis'
        'redis'  => [
            'host' => getenv('REDIS_HOSTNAME'),
            'port' => getenv('REDIS_PORT'),
        ]
    ],

    'emailing' => [
        'driver'     => 'native', // one of: 'native', 'mailgun', 'aws'
        'debugEmail' => 'my@email.com', // The email to use when debugging. Forces this email as the recipient for all emails sent.

        'mailgun' => [
            'domain' => getenv('MAILGUN_DOMAIN'),
            'apiKey' => getenv('MAILGUN_API_KEY'),
        ],

        'aws' => [
            'domain'    => getenv('AWS_SES_DOMAIN'),
            'region'    => getenv('AWS_SES_REGION'),
            'apiKey'    => getenv('AWS_SES_API_KEY'),
            'secretKey' => getenv('AWS_SES_SECRET_KEY'),
        ],
    ],

    'storage' => [
        'driver' => 'file', // one of: 'file', 'rackspace', 'aws'

        'file' => [
            'directory' => __DIR__ . '/../storage/files', // Path where to store uploaded files
            'uri'       => '/storage/files', // URL to load uploaded files
        ],

        'aws' => [
            'region'    => getenv('AWS_S3_REGION'),
            'apiKey'    => getenv('AWS_S3_API_KEY'),
            'secretKey' => getenv('AWS_S3_SECRET_KEY'),
            'bucket'    => getenv('AWS_S3_BUCKET'),
        ]
    ],

    'logging' => [
        'logsPath' => '/storage/logs/app.log',
    ],

    'bus' => [
        'enableLogging' => false,
        'logsPath'      => '/storage/logs/bus.log',
        'cacheDuration' => 60,
    ],

    'databases' => [
        'web'  => [
            'driver'   => 'pdo_mysql',
            'host'     => getenv("DATABASE_HOSTNAME"),
            'user'     => getenv("DATABASE_USER"),
            'password' => getenv("DATABASE_PASSWORD"),
            'name'     => getenv("DATABASE_NAME"),
        ],
        'test' => [
            'driver'   => 'pdo_mysql',
            'host'     => getenv("DATABASE_HOSTNAME"),
            'user'     => getenv("DATABASE_USER"),
            'password' => getenv("DATABASE_PASSWORD"),
            'name'     => 'sushi_test',
        ],
    ],
];

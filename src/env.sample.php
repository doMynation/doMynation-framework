<?php

/*
 * ------------------------------------------------------
 *  Environment configurations
 * ------------------------------------------------------
 */

// Application version
define('APP_VERSION', '1.0');
define('APP_BUILD', 'b22');

// Settings this to `true` will activate caching mechanisms and disable debug mode on various
// modules/components of the application.
define('IS_PRODUCTION', false);

// The server's timezone
define('DATE_TIMEZONE', 'America/New_York');

define('DEFAULT_LANG', 'fr');

// The base url of the application
define('BASEURL', 'http://fill.me/');

// Drivers
define('EMAIL_DRIVER', 'native'); // Choices: native, mailgun
define('WEBSOCKET_DRIVER', 'pusher'); // Choices: pusher
define('CACHE_DRIVER', 'redis'); // Choices: memory (default), redis
define('DB_DRIVER', 'pdo_mysql');
define('PASSWORD_DRIVER', 'native'); // Choices: native, md5

// Database
define('DB_HOST', 'localhost');
define('DB_DATABASE', 'fill_me');
define('DB_USER', 'fill_me');
define('DB_PASSWORD', 'fill_me');

// Redis configuration
define('REDIS_HOST', '127.0.0.1');
define('REDIS_PORT', '6379');

// Pusher API
define('PUSHER_APP_ID', 'fill_me');
define('PUSHER_API_KEY', 'fill_me');
define('PUSHER_API_SECRET_KEY', 'fill_me');

// Mailgun API
define('MAILGUN_API_KEY', 'fill_me');
define('MAILGUN_DEFAULT_DOMAIN', 'fill_me');

// Email API
define('EMAIL_DEBUG', 'fill_me');
define('EMAIL_DEFAULT_SENDER', 'fill_me <fill@me.com>');


// Possible values: file, rackspace
define('STORAGE_DRIVER', 'file');
define('STORAGE_FILE_DIRECTORY', __DIR__ . '/fill_me');
define('STORAGE_FILE_URI', BASEURL . 'fill_me');

// Google Map's API
define('GOOGLE_MAP_API_KEY', 'fill_me');

// Rackspace cloudfiles API. Uncomment this if you chose `rackspace` as the storage driver.
define('RACKSPACE_USERNAME', 'fill_me');
define('RACKSPACE_PASSWORD', 'fill_me');

// Amazon AWS credentials
define('AWS_REGION', 'fill_me');
define('AWS_API_KEY', 'fill_me');
define('AWS_SECRET_KEY', 'fill_me');
define('AWS_SES_DOMAIN', 'fill_me');


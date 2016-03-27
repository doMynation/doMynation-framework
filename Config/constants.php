<?php

/**
 * @todo: Move those constant inside a future Response class OR use Symfony's Response class.
 */

// HTTP STATUS CODES
define('HTTP_OK', 200);
define('HTTP_NOT_MODIFIED', 304);
define('HTTP_FORCE_REDIRECT', 307);
define('HTTP_BAD_REQUEST', 400);
define('HTTP_AUTHENTICATION_REQUIRED', 401); // custom
define('HTTP_FORBIDDEN', 403);
define('HTTP_NOT_FOUND', 404);
define('HTTP_UNPROCESSABLE_ENTITY', 422);

define('IS_AJAX', isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest');

<?php

// Include Composer autoloader
include __DIR__ . '/../vendor/autoload.php';

// Load environment variables
$dotenv = new Dotenv\Dotenv(realpath(__DIR__ . '/../'));
$dotenv->load();

/**
 * @const ENV_PRODUCTION Application production stage
 */
define('ENV_PRODUCTION', 'production');

/**
 * @const ENV_STAGING Application staging stage
 */
define('ENV_STAGING', 'staging');

/**
 * @const ENV_DEVELOPMENT Application development stage
 */
define('ENV_DEVELOPMENT', 'development');

/**
 * @const ENV_TEST Application test stage
 */
define('ENV_TEST', 'test');

/**
 * @const APPLICATION_ENV Current application stage
 */
define('APPLICATION_ENV', getenv('APP_ENV'));

/**
 * @const APP_START_TIME The start time of the application, used for profiling
 */
define('APP_START_TIME', microtime(true));

/**
 * @const APP_START_MEMORY The memory usage at the start of the application, used for profiling
 */
define('APP_START_MEMORY', memory_get_usage(defined('HHVM_VERSION')));

/**
 * @const DS Shortcut for DIRECTORY_SEPARATOR
 */
define('DS', DIRECTORY_SEPARATOR);

/**
 * @const NAMESPACE_SEPARATOR Namespace Separator
 */
define('NAMESPACE_SEPARATOR', '\\');

/**
 * @const NS Shortcut for NAMESPACE_SEPARATOR
 */
define('NS', NAMESPACE_SEPARATOR);

/**
 * @const BASE_DIR Document root
 */
define('BASE_DIR', getenv('BASE_DIR'));

/**
 * @const DEV_IP Developer IP mask
 */
define('DEV_IP', getenv('DEV_IP'));

// Set the default locale
setlocale(LC_ALL, getenv('LOCALE'));

// Sets the default timezone used by all date/time function
date_default_timezone_set(getenv('TIMEZONE'));

if (function_exists('mb_internal_encoding')) {
    // Set the MB extension encoding to the same character set
    mb_internal_encoding('utf-8');
}

if (function_exists('mb_substitute_character')) {
    // Set the mb_substitute_character to "none"
    mb_substitute_character('none');
}

if (ENV_PRODUCTION === APPLICATION_ENV) {
    header_remove('X-Powered-By');

    // assertion code will not be generated, making the assertions zero-cost
    if (PHP_VERSION_ID >= 70000) {
        ini_set('zend.assertions', -1);
    }
}

// Enable xdebug parameter collection in development mode to improve fatal stack traces.
// Highly recommends use at least XDebug 2.2.3 for a better compatibility with Phalcon
if (ENV_DEVELOPMENT === APPLICATION_ENV && extension_loaded('xdebug')) {
    ini_set('xdebug.collect_params', 4);
}

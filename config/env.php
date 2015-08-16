<?php

// Include Composer autoloader
include __DIR__ . '/../vendor/autoload.php';

// Load environment variables
$dotenv = new Dotenv\Dotenv(realpath(__DIR__ . '/../'));
$dotenv->load();

// Check phalcon framework installation.
if (!extension_loaded('phalcon')) {
    printf('Install Phalcon framework %s', getenv('PHALCON_VERSION_REQUIRED'));
    exit(1);
}

/**
 * @const APP_PRODUCTION Application production stage
 */
define('PRODUCTION_STAGE', 'production');

/**
 * @const APP_DEVELOPMENT Application development stage
 */
define('DEVELOPMENT_STAGE', 'development');

/**
 * @const APP_TEST Application test stage
 */
define('TEST_STAGE', 'test');

/**
 * @const APP_DEVELOPMENT Current application stage
 */
define('APP_STAGE', getenv('APP_ENV'));

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
 * @const DS Shortcut for NAMESPACE_SEPARATOR
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

if (function_exists('mb_internal_encoding')) {
    // Set the MB extension encoding to the same character set
    mb_internal_encoding('utf-8');
}

if (function_exists('mb_substitute_character')) {
    // Set the mb_substitute_character to "none"
    mb_substitute_character('none');
}

if (APP_STAGE == DEVELOPMENT_STAGE) {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL | E_STRICT);
} else {
    ini_set('display_errors', 0);
    ini_set('display_startup_errors', 0);
    header_remove('X-Powered-By');
    error_reporting(E_ALL ^ E_NOTICE);
}

// Enable xdebug parameter collection in development mode to improve fatal stack traces.
// Highly recommends use at least XDebug 2.2.3 for a better compatibility with Phalcon
if (APP_STAGE == DEVELOPMENT_STAGE && extension_loaded('xdebug')) {
    ini_set('xdebug.collect_params', 4);
}

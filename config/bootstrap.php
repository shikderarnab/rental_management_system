<?php
/**
 * CakePHP Bootstrap File
 */
use Cake\Cache\Cache;
use Cake\Core\Configure;
use Cake\Core\Configure\Engine\PhpConfig;
use Cake\Datasource\ConnectionManager;
use Cake\Error\ErrorTrap;
use Cake\Error\ExceptionTrap;
use Cake\Log\Log;
use Cake\Mailer\Mailer;
use Cake\Mailer\TransportFactory;
use Cake\Routing\Router;
use Cake\Utility\Security;

require dirname(__DIR__) . '/vendor/autoload.php';

if (!defined('DS')) {
    define('DS', DIRECTORY_SEPARATOR);
}

define('ROOT', dirname(__DIR__));
define('APP_DIR', 'src');
define('APP', ROOT . DS . APP_DIR . DS);
define('WEBROOT_DIR', 'webroot');
define('WWW_ROOT', ROOT . DS . WEBROOT_DIR . DS);
define('TESTS', ROOT . DS . 'tests' . DS);
define('CACHE', ROOT . DS . 'tmp' . DS . 'cache' . DS);
define('LOGS', ROOT . DS . 'logs' . DS);
define('CONFIG', ROOT . DS . 'config' . DS);
define('RESOURCES', ROOT . DS . 'resources' . DS);
define('TMP', ROOT . DS . 'tmp' . DS);

require ROOT . '/config/paths.php';

date_default_timezone_set('UTC');

// Load configuration files first
Configure::load('app', 'default', false);
Configure::load('plugins', 'default', true);

if (file_exists(CONFIG . 'app_local.php')) {
    Configure::load('app_local', 'default');
}

mb_internal_encoding(Configure::read('App.encoding') ?: 'UTF-8');

// Set up cache
$cacheConfig = Configure::consume('Cache');
if ($cacheConfig) {
    Cache::setConfig($cacheConfig);
}

// Set up database connections
$datasources = Configure::consume('Datasources');
if ($datasources) {
    ConnectionManager::setConfig($datasources);
}

// Set up logging
$logConfig = Configure::consume('Log');
if ($logConfig) {
    Log::setConfig($logConfig);
}

// Set security salt
$salt = Configure::consume('Security.salt');
if ($salt && $salt !== '__SALT__' && $salt !== '__CHANGE_THIS_SALT__') {
    Security::setSalt($salt);
} else {
    // Generate a default salt if not set
    Security::setSalt('default_salt_change_in_production_' . md5(__FILE__));
}

$isCli = PHP_SAPI === 'cli';
if ($isCli) {
    $errorConfig = Configure::read('Error');
    if ($errorConfig) {
        (new ErrorTrap($errorConfig))->register();
        (new ExceptionTrap($errorConfig))->register();
    }
}

if (!$isCli) {
    $errorConfig = Configure::read('Error');
    if ($errorConfig) {
        (new ErrorTrap($errorConfig))->register();
        (new ExceptionTrap($errorConfig))->register();
    }
}

if (Configure::read('debug')) {
    Configure::write('Cache._cake_model_.duration', '+2 minutes');
    Configure::write('Cache._cake_core_.duration', '+2 minutes');
}

if (Configure::read('debug') && file_exists(ROOT . '/vendor/cakephp/cakephp-codesniffer/CakePHP')) {
    Configure::write('CodeSniffer', [
        'phpExecutable' => PHP_BINARY,
    ]);
}

// Initialize Router before creating Application
\Cake\Routing\Router::resetRoutes();

return new \App\Application(CONFIG);

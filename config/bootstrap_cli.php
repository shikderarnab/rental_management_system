<?php
/**
 * CakePHP CLI Bootstrap
 */
require dirname(__DIR__) . '/vendor/autoload.php';

use Cake\Core\Configure;
use Cake\Core\Configure\Engine\PhpConfig;
use Cake\Datasource\ConnectionManager;

require dirname(__DIR__) . '/config/paths.php';

date_default_timezone_set('UTC');
mb_internal_encoding(Configure::read('App.encoding'));

Configure::load('app', 'default', false);
Configure::load('plugins', 'default', true);

if (file_exists(CONFIG . 'app_local.php')) {
    Configure::load('app_local', 'default');
}

ConnectionManager::setConfig(Configure::consume('Datasources'));


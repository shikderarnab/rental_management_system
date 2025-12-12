<?php
/**
 * CakePHP Entry Point
 */
use Cake\Http\Server;

require dirname(__DIR__) . '/vendor/autoload.php';

// Use CakePHP's default base path detection. We don't override SCRIPT_NAME here.
// App.base is set to false in config/app.php so CakePHP will auto-detect the base
// from the server environment and .htaccess configuration.

$app = require dirname(__DIR__) . '/config/bootstrap.php';

$server = new Server($app);
$server->emit($server->run());

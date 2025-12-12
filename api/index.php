<?php
/**
 * Vercel Serverless Function Entry Point for CakePHP
 * This file routes all requests to the CakePHP application
 */
use Cake\Http\Server;

require dirname(__DIR__) . '/vendor/autoload.php';

// Bootstrap the CakePHP application
$app = require dirname(__DIR__) . '/config/bootstrap.php';

// Create and run the server
$server = new Server($app);
$server->emit($server->run());


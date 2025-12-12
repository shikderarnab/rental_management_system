#!/usr/bin/env php
<?php
/**
 * CakePHP Console Bootstrap
 */
require dirname(__DIR__) . '/vendor/autoload.php';

use Cake\Console\CommandRunner;
use App\Application;

$app = new Application(dirname(__DIR__) . '/config');
$runner = new CommandRunner($app);
exit($runner->run($argv));


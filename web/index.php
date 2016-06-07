<?php

use Symfony\Component\ClassLoader\DebugClassLoader;
use Symfony\Component\HttpKernel\Debug\ErrorHandler;
use Symfony\Component\HttpKernel\Debug\ExceptionHandler;

ini_set('display_errors', 0);
require_once __DIR__.'/../vendor/autoload.php';

// For interal PHP server
$filename = __DIR__.preg_replace('#(\?.*)$#', '', $_SERVER['REQUEST_URI']);
if (php_sapi_name() === 'cli-server' && is_file($filename)) {
    return false;
}

if (getenv('APP_DEBUG')) {
    ini_set('display_errors', 1);
    error_reporting(-1);
    DebugClassLoader::enable();
    ErrorHandler::register();
    ExceptionHandler::register();
}

$app = require __DIR__.'/../src/app.php';
require __DIR__.'/../config/dev.php';
require __DIR__.'/../src/controllers.php';
$app->run();

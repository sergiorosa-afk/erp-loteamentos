<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Laravel root fica 2 níveis acima de public_html/sistemadegestao/
// __DIR__ = /home/fionco36/public_html/sistemadegestao
// dirname(__DIR__, 2) = /home/fionco36
$appPath = dirname(__DIR__, 2) . '/erp_app3';

if (file_exists($maintenance = $appPath . '/storage/framework/maintenance.php')) {
    require $maintenance;
}

require $appPath . '/vendor/autoload.php';

/** @var Application $app */
$app = require_once $appPath . '/bootstrap/app.php';

$app->handleRequest(Request::capture());

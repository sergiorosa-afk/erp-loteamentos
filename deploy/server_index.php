<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// index.php fica em: elizeu.targetup.com.br/public/sistemadegestao/index.php
// dirname(__DIR__, 3) = /home3/fionco36
$appPath = '/home3/fionco36/erp_elizeu';

if (file_exists($maintenance = $appPath . '/storage/framework/maintenance.php')) {
    require $maintenance;
}

require $appPath . '/vendor/autoload.php';

/** @var Application $app */
$app = require_once $appPath . '/bootstrap/app.php';

$app->handleRequest(Request::capture());

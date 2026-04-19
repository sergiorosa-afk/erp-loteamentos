<?php
$token = $_GET['token'] ?? '';
if ($token !== 'erp2026setup') { http_response_code(403); die('Acesso negado.'); }

$appPath = '/home3/fionco36/erp_elizeu';

echo "<pre>\n=== ERP Setup ===\n";
echo "PHP: " . PHP_VERSION . "\n";
echo "App: $appPath\n";
echo "Artisan: " . (file_exists("$appPath/artisan") ? 'OK' : 'NAO ENCONTRADO') . "\n";
echo ".env: " . (file_exists("$appPath/.env") ? 'OK' : 'NAO') . "\n\n";

if (!file_exists("$appPath/.env"))
    copy("$appPath/.env.production", "$appPath/.env");

require $appPath . '/vendor/autoload.php';
$app = require $appPath . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

foreach (['key:generate --force', 'migrate --force', 'config:cache', 'route:cache', 'view:cache'] as $cmd) {
    echo "$ php artisan $cmd\n";
    Illuminate\Support\Facades\Artisan::call($cmd);
    echo Illuminate\Support\Facades\Artisan::output() . "\n";
}

echo "=== CONCLUIDO — apague este arquivo! ===\n</pre>";

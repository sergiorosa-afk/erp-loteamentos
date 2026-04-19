<?php
$token = $_GET['token'] ?? '';
if ($token !== 'erp2026setup') {
    http_response_code(403);
    die('Acesso negado.');
}

$appPath = dirname(__DIR__, 2) . '/erp_app3';

// Encontra o binário PHP disponível
$phpBin = null;
foreach (['/usr/local/bin/php8.5', '/usr/local/bin/php8.4', '/usr/local/bin/php8.3', '/usr/bin/php'] as $bin) {
    if (file_exists($bin)) { $phpBin = $bin; break; }
}

echo "<pre>\n=== ERP Setup ===\n\n";
echo "App path: $appPath\n";
echo "PHP bin: $phpBin\n";
echo ".env existe: " . (file_exists("$appPath/.env") ? 'SIM' : 'NAO') . "\n";

if (!file_exists("$appPath/.env")) {
    copy("$appPath/.env.production", "$appPath/.env");
    echo "✓ .env criado\n";
}

$storageLink = __DIR__ . '/storage';
if (!file_exists($storageLink)) {
    symlink("$appPath/storage/app/public", $storageLink);
    echo "✓ symlink storage\n";
}

if ($phpBin) {
    foreach (['key:generate --force', 'migrate --force', 'config:cache', 'route:cache', 'view:cache'] as $cmd) {
        echo "\n$ php artisan $cmd\n";
        echo shell_exec("$phpBin $appPath/artisan $cmd 2>&1");
    }
} else {
    echo "ERRO: nenhum binário PHP encontrado!\n";
}

echo "\n=== CONCLUIDO — apague este arquivo! ===\n</pre>";

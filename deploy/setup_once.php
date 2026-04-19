<?php
// ATENÇÃO: apagar este arquivo imediatamente após usar!
// Acesse: elizeu.targetup.com.br/sistemadegestao/setup_once.php?token=erp2026setup

$token = $_GET['token'] ?? '';
if ($token !== 'erp2026setup') {
    http_response_code(403);
    die('Acesso negado.');
}

$appPath = dirname(__DIR__, 2) . '/erp_app';

echo "<pre>\n";
echo "=== ERP Loteamentos — Setup Inicial ===\n\n";

// 1. Copiar .env se não existir
if (!file_exists("$appPath/.env")) {
    if (file_exists("$appPath/.env.production")) {
        copy("$appPath/.env.production", "$appPath/.env");
        echo "✓ .env criado\n";
    } else {
        echo "✗ .env.production não encontrado!\n";
    }
} else {
    echo "✓ .env já existe\n";
}

// 2. Symlink storage
$storageLink = __DIR__ . '/storage';
$storageTarget = "$appPath/storage/app/public";
if (!file_exists($storageLink)) {
    symlink($storageTarget, $storageLink);
    echo "✓ symlink storage criado\n";
} else {
    echo "✓ symlink storage já existe\n";
}

// 3. Rodar artisan via PHP CLI
$php = '/usr/local/bin/php8.3';
$artisan = "$appPath/artisan";

$commands = [
    'key:generate --force',
    'migrate --force',
    'config:cache',
    'route:cache',
    'view:cache',
];

foreach ($commands as $cmd) {
    echo "\n$ php artisan $cmd\n";
    $output = shell_exec("$php $artisan $cmd 2>&1");
    echo $output;
}

echo "\n=== CONCLUIDO ===\n";
echo "APAGUE este arquivo agora!\n";
echo "</pre>";

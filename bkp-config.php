<?php
// Função simples para ler o .env manual
function loadEnv($path) {
    if (!file_exists($path)) return [];
    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $data = [];
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        list($name, $value) = explode('=', $line, 2);
        $data[trim($name)] = trim($value);
    }
    return $data;
}

$env = loadEnv(__DIR__ . '/.env');
$apiBaseUrl = $env['API_BASE_URL'] ?? 'http://192.168.0.38:9092/routers/'; // fallback
?>
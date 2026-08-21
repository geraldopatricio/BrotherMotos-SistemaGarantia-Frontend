<?php
header('Content-Type: application/json');

$sessionId = $_GET['session_id'] ?? '';
// Segurança: permite apenas caracteres alfanuméricos no nome do arquivo
$sessionId = preg_replace('/[^a-zA-Z0-9_-]/', '', $sessionId);

if (empty($sessionId)) {
    echo json_encode(['success' => false, 'message' => 'ID de sessão não fornecido']);
    exit;
}

$basePath = __DIR__ . '/garantiaTemp/';
$jsonFile = $basePath . "sessao_{$sessionId}.json";

if (file_exists($jsonFile)) {
    $conteudo = file_get_contents($jsonFile);
    echo json_encode([
        'success' => true, 
        'temp_data' => $conteudo
    ]);
} else {
    echo json_encode([
        'success' => false, 
        'message' => 'Arquivo não encontrado: ' . "sessao_{$sessionId}.json"
    ]);
}
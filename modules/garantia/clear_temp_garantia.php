<?php
$sessionId = $_POST['session_id'] ?? '';
$jsonFile = "garantiaTemp/sessao_{$sessionId}.json";

if (file_exists($jsonFile)) {
    $data = json_decode(file_get_contents($jsonFile), true);
    // Lógica para deletar imagens associadas àquela sessão se desejar limpar tudo
    unlink($jsonFile);
}
echo json_encode(['success' => true]);
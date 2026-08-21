<?php
// Define o caminho absoluto baseado na localização deste arquivo PHP
$basePath = __DIR__ . '/garantiaTemp/';
$imgPath = $basePath . 'imagens/';

// Cria as pastas se não existirem
if (!file_exists($imgPath)) {
    mkdir($imgPath, 0777, true);
}

$sessionId = $_POST['session_id'] ?? 'default';
// Sanitiza o sessionId para evitar ataques de diretório
$sessionId = preg_replace('/[^a-zA-Z0-9_-]/', '', $sessionId);

$tempData = json_decode($_POST['temp_data'] ?? '', true);

if ($tempData) {
    if (isset($tempData['garantiaItens'])) {
        foreach ($tempData['garantiaItens'] as &$item) {
            if (isset($item['anexos'])) {
                foreach ($item['anexos'] as &$anexo) {
                    if (isset($anexo['data']) && strpos($anexo['data'], 'data:') === 0) {
                        $nomeArquivo = $anexo['anexo'];
                        $caminhoCompleto = $imgPath . $nomeArquivo;
                        
                        $dataParts = explode(',', $anexo['data']);
                        if (isset($dataParts[1])) {
                            file_put_contents($caminhoCompleto, base64_decode($dataParts[1]));
                            $anexo['data'] = 'FILE_SAVED'; // Remove o base64 pesado do JSON
                        }
                    }
                }
            }
        }
    }

    $jsonFile = $basePath . "sessao_{$sessionId}.json";
    $salvou = file_put_contents($jsonFile, json_encode($tempData));

    header('Content-Type: application/json');
    echo json_encode(['success' => $salvou !== false]);
} else {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Sem dados para salvar']);
}
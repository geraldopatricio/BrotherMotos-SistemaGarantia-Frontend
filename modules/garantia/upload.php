<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Configurações de upload
$maxFileSize = 50 * 1024 * 1024; // 50MB
$allowedTypes = [
    'image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp',
    'video/mp4', 'video/avi', 'video/mov', 'video/wmv', 'video/mkv'
];

// Verifica se a pasta uploads existe, se não, cria
$uploadDir = '/var/www/html/uploads/';
if (!is_dir($uploadDir)) {
    if (!mkdir($uploadDir, 0755, true)) {
        echo json_encode([
            'success' => false, 
            'message' => 'Erro ao criar diretório de uploads'
        ]);
        exit;
    }
}

// Verifica se o diretório é gravável
if (!is_writable($uploadDir)) {
    echo json_encode([
        'success' => false, 
        'message' => 'Diretório de uploads não tem permissão de escrita'
    ]);
    exit;
}

// Log para debug
error_log("Upload iniciado - Método: " . $_SERVER['REQUEST_METHOD']);
error_log("Files recebidos: " . print_r($_FILES, true));
error_log("POST data: " . print_r($_POST, true));

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    // Responde para requisições preflight CORS
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
        $errorMsg = 'Nenhum arquivo recebido ou erro no upload';
        if (isset($_FILES['file'])) {
            switch ($_FILES['file']['error']) {
                case UPLOAD_ERR_INI_SIZE:
                case UPLOAD_ERR_FORM_SIZE:
                    $errorMsg = 'Arquivo muito grande';
                    break;
                case UPLOAD_ERR_PARTIAL:
                    $errorMsg = 'Upload parcialmente completado';
                    break;
                case UPLOAD_ERR_NO_FILE:
                    $errorMsg = 'Nenhum arquivo selecionado';
                    break;
                case UPLOAD_ERR_NO_TMP_DIR:
                    $errorMsg = 'Diretório temporário não encontrado';
                    break;
                case UPLOAD_ERR_CANT_WRITE:
                    $errorMsg = 'Erro ao escrever no disco';
                    break;
                case UPLOAD_ERR_EXTENSION:
                    $errorMsg = 'Extensão PHP interrompeu o upload';
                    break;
            }
        }
        
        error_log("Erro no upload: " . $errorMsg);
        echo json_encode([
            'success' => false, 
            'message' => $errorMsg
        ]);
        exit;
    }

    $file = $_FILES['file'];
    $filename = $_POST['filename'] ?? $file['name'];
    
    // Validações de segurança
    if ($file['size'] > $maxFileSize) {
        error_log("Arquivo muito grande: " . $file['size']);
        echo json_encode([
            'success' => false, 
            'message' => 'Arquivo muito grande. Tamanho máximo: 50MB'
        ]);
        exit;
    }
    
    // Verifica o tipo MIME
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);
    
    if (!in_array($mimeType, $allowedTypes)) {
        error_log("Tipo de arquivo não permitido: " . $mimeType);
        echo json_encode([
            'success' => false, 
            'message' => 'Tipo de arquivo não permitido: ' . $mimeType
        ]);
        exit;
    }
    
    // Sanitiza o nome do arquivo
    $filename = preg_replace('/[^a-zA-Z0-9\.\_\-]/', '_', $filename);
    $targetPath = $uploadDir . $filename;
    
    // Verifica se o arquivo já existe e adiciona sufixo se necessário
    $counter = 1;
    $originalFilename = $filename;
    while (file_exists($targetPath)) {
        $fileInfo = pathinfo($originalFilename);
        $filename = $fileInfo['filename'] . '_' . $counter . '.' . $fileInfo['extension'];
        $targetPath = $uploadDir . $filename;
        $counter++;
    }
    
    // Tenta mover o arquivo
    if (move_uploaded_file($file['tmp_name'], $targetPath)) {
        error_log("Arquivo uploadado com sucesso: " . $filename . " para " . $targetPath);
        echo json_encode([
            'success' => true, 
            'message' => 'Arquivo uploadado com sucesso',
            'filename' => $filename,
            'path' => $targetPath
        ]);
    } else {
        error_log("Erro ao mover arquivo: " . $filename);
        echo json_encode([
            'success' => false, 
            'message' => 'Erro ao salvar arquivo no servidor'
        ]);
    }
} else {
    error_log("Método não permitido: " . $_SERVER['REQUEST_METHOD']);
    echo json_encode([
        'success' => false, 
        'message' => 'Método não permitido'
    ]);
}
?>
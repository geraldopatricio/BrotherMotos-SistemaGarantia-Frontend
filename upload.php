<?php
session_start();

// if (!isset($_SESSION['codusr'])) {
//     die(json_encode(['success' => false, 'message' => 'Usuário não autenticado']));
// }

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die(json_encode(['success' => false, 'message' => 'Método não permitido']));
}

if (!isset($_FILES['file']) || $_FILES['file']['error'] == UPLOAD_ERR_NO_FILE) { // Adicionado verificação de erro também
    die(json_encode(['success' => false, 'message' => 'Nenhum arquivo enviado ou erro no upload do arquivo.']));
}

// if (!isset($_FILES['file']) {
//     die(json_encode(['success' => false, 'message' => 'Nenhum arquivo enviado']));
// }

$uploadDir = '../uploads/';
if (!file_exists($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

$filename = $_POST['filename'] ?? basename($_FILES['file']['name']);
$targetPath = $uploadDir . $filename;

if (move_uploaded_file($_FILES['file']['tmp_name'], $targetPath)) {
    echo json_encode(['success' => true, 'filename' => $filename]);
} else {
    $uploadError = $_FILES['file']['error']; // Obter o código de erro
    $errorMessage = 'Erro ao mover o arquivo.';
    // Adicionar mais detalhes sobre o erro de upload se disponível
    // https://www.php.net/manual/en/features.file-upload.errors.php
    switch ($uploadError) {
        case UPLOAD_ERR_INI_SIZE:
            $errorMessage .= " O arquivo excede a diretiva upload_max_filesize no php.ini.";
            break;
        case UPLOAD_ERR_FORM_SIZE:
            $errorMessage .= " O arquivo excede a diretiva MAX_FILE_SIZE especificada no formulário HTML.";
            break;
        case UPLOAD_ERR_PARTIAL:
            $errorMessage .= " O upload do arquivo foi feito parcialmente.";
            break;
        case UPLOAD_ERR_NO_TMP_DIR:
            $errorMessage .= " Faltando uma pasta temporária.";
            break;
        case UPLOAD_ERR_CANT_WRITE:
            $errorMessage .= " Falha ao escrever o arquivo em disco.";
            break;
        case UPLOAD_ERR_EXTENSION:
            $errorMessage .= " Uma extensão do PHP interrompeu o upload do arquivo.";
            break;
    }
    echo json_encode(['success' => false, 'message' => $errorMessage, 'php_error_code' => $uploadError]);
}
?>
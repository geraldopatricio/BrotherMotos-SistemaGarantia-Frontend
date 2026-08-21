<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die(json_encode(['success' => false, 'message' => 'Método não permitido']));
}

if (!isset($_POST['garantia_data'])) {
    die(json_encode(['success' => false, 'message' => 'Dados da garantia não recebidos']));
}

$garantiaData = json_decode($_POST['garantia_data'], true);
if (json_last_error() !== JSON_ERROR_NONE) {
    die(json_encode(['success' => false, 'message' => 'Dados da garantia inválidos']));
}

// ==================================================================
// 1. REGERAÇÃO DE IDS (LIMITAR A 9 DIGITOS E SOMENTE NÚMEROS)
// ==================================================================

// Gera um novo ID de 9 dígitos para a garantia
$novoIdGarantia = mt_rand(100000000, 999999999);
$oldIdGarantia = $garantiaData['garantia']['id'];
$garantiaData['garantia']['id'] = $novoIdGarantia;

// Mapeamento para atualizar os IDs dos itens e seus respectivos anexos
$mapaItens = [];

if (isset($garantiaData['garantia_itens']) && is_array($garantiaData['garantia_itens'])) {
    foreach ($garantiaData['garantia_itens'] as $key => $item) {
        $oldItemId = $item['id']; // Aqui está o valor com "_" (ex: 1759843317986_0)
        
        // Criamos um novo ID do item concatenando o ID da garantia com a sequência da array
        // Usamos (string) para concatenar e depois garantimos que é numérico com str_replace
        $novoItemId_str = $novoIdGarantia . $key; 
        
        // Remove qualquer caractere que não seja número (garantia extra)
        $novoItemId = preg_replace('/[^0-9]/', '', $novoItemId_str);
        
        // Salva de volta no objeto (como string ou int, mas sem o "_")
        $garantiaData['garantia_itens'][$key]['id'] = $novoItemId;
        $garantiaData['garantia_itens'][$key]['fk_garantia'] = $novoIdGarantia;
        
        // Guarda a relação De -> Para para atualizar os anexos corretamente
        $mapaItens[$oldItemId] = $novoItemId;
    }
}

// Atualiza os anexos para apontarem para os novos IDs puramente numéricos
if (isset($garantiaData['garantia_itens_anexos']) && is_array($garantiaData['garantia_itens_anexos'])) {
    foreach ($garantiaData['garantia_itens_anexos'] as $key => $anexo) {
        $oldFkItem = $anexo['fk_item'];
        
        if (isset($mapaItens[$oldFkItem])) {
            $garantiaData['garantia_itens_anexos'][$key]['fk_item'] = $mapaItens[$oldFkItem];
        }
    }
}

// ==================================================================
// 2. IDENTIFICAÇÃO DO USUÁRIO
// ==================================================================
$usuarioLogado = 'Sistema';

if (isset($_SESSION['user_usuario']) && !empty($_SESSION['user_usuario'])) {
    $usuarioLogado = $_SESSION['user_usuario'];
} elseif (isset($_SESSION['usuario']) && !empty($_SESSION['usuario'])) {
    $usuarioLogado = $_SESSION['usuario'];
} elseif (isset($_SESSION['codusr']) && !empty($_SESSION['codusr'])) {
    $usuarioLogado = $_SESSION['codusr'];
}

if (!isset($garantiaData['garantia'])) {
    $garantiaData['garantia'] = [];
}
$garantiaData['garantia']['user_cad'] = $usuarioLogado;

// ==================================================================
// 3. SALVAMENTO DO ARQUIVO
// ==================================================================

$jsonDir = 'json/';
if (!file_exists($jsonDir)) {
    mkdir($jsonDir, 0777, true);
}

// O nome do arquivo usará o novo ID de 9 dígitos
$filename = $jsonDir . 'garantia_' . $novoIdGarantia . '.json';

if (file_put_contents($filename, json_encode($garantiaData, JSON_PRETTY_PRINT))) {
    $response = ['success' => true];
    
    if (isset($_POST['action']) && $_POST['action'] === 'finalizar') {
        $response['message'] = 'Garantia finalizada com sucesso. ID: ' . $novoIdGarantia;
    } else {
        $response['message'] = 'Garantia gravada em digitação. ID: ' . $novoIdGarantia;
    }
    
    echo json_encode($response);
} else {
    echo json_encode(['success' => false, 'message' => 'Erro ao salvar o arquivo JSON']);
}
?>
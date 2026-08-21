<?php

$rootPath = dirname(__DIR__, 3); // Sobe 3 níveis a partir de frontend/modules/workflow/
$vendorPath = $rootPath . '/backend/vendor/autoload.php';

if (!file_exists($vendorPath)) {
    die(json_encode(['success' => false, 'message' => "Vendor nao encontrado em: " . $vendorPath]));
}

require_once $vendorPath;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Iniciar sessao para pegar o email
session_start();

// Configuracoes do .env - tambem corrigir caminho
$envFile = $rootPath . '/backend/.env';
if (file_exists($envFile)) {
    $envVars = parse_ini_file($envFile);
    foreach ($envVars as $key => $value) {
        $_ENV[$key] = $value;
    }
} else {
    die(json_encode(['success' => false, 'message' => "Arquivo .env nao encontrado em: " . $envFile]));
}

// Log para debug
error_log("Send email script accessed - Method: " . $_SERVER['REQUEST_METHOD']);
error_log("Session user_email: " . ($_SESSION['user_email'] ?? 'NOT SET'));
error_log("All session vars: " . print_r($_SESSION, true));

function sendGarantiaEmail($garantiaData, $userEmail) {
    $mail = new PHPMailer(true);
    
    try {
        // Configuracoes do servidor
        $mail->isSMTP();
        $mail->Host = $_ENV['MAIL_SERVER'];
        $mail->SMTPAuth = true;
        $mail->Username = $_ENV['MAIL_USERNAME'];
        $mail->Password = $_ENV['MAIL_PASSWORD'];
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = $_ENV['MAIL_PORT'];
        
        // Remetente e destinatarios
        $mail->setFrom($_ENV['MAIL_DEFAULT_SENDER'], 'Sistema de Garantia');
        $mail->addAddress($userEmail);
        
        // CCO
        if (!empty($_ENV['MAIL_CCO'])) {
            $ccoEmails = explode(',', $_ENV['MAIL_CCO']);
            foreach ($ccoEmails as $ccoEmail) {
                $mail->addBCC(trim($ccoEmail));
            }
        }
        
        // Conteudo do e-mail
        $mail->isHTML(true);
        $mail->Subject = 'Nova Garantia Registrada - ' . date('d/m/Y H:i');
        
        // Gerar o HTML do e-mail
        $mail->Body = generateEmailHTML($garantiaData);
        $mail->AltBody = generateEmailText($garantiaData);
        
        $mail->send();
        error_log("E-mail enviado com sucesso para: " . $userEmail);
        return ['success' => true, 'message' => 'E-mail enviado com sucesso'];
        
    } catch (Exception $e) {
        error_log("Erro ao enviar e-mail: " . $mail->ErrorInfo);
        return ['success' => false, 'message' => "Erro ao enviar e-mail: {$mail->ErrorInfo}"];
    }
}

// ... as funcoes generateEmailHTML e generateEmailText permanecem iguais ...
function generateEmailHTML($garantiaData) {
    $garantia = $garantiaData['garantia'];
    $itens = $garantiaData['garantia_itens'];
    
    $html = '
    <!DOCTYPE html>
    <html lang="pt-BR">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Nova Garantia</title>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
        <style>
            * {
                margin: 0;
                padding: 0;
                box-sizing: border-box;
            }
            
            body {
                font-family: "Inter", -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
                line-height: 1.6;
                color: #333;
                background-color: #f8fafc;
            }
            
            .email-container {
                max-width: 600px;
                margin: 0 auto;
                background: #ffffff;
                border-radius: 16px;
                overflow: hidden;
                border: 1px solid #bec0c4ff;
                box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            }
            
            .email-header {
                background: linear-gradient(135deg, #7A5AF8 0%, #674ED7 100%);
                color: white;
                padding: 30px;
                text-align: center;
                border-bottom: 1px solid #b0b3b6ff;
            }
            
            .email-header h1 {
                font-size: 24px;
                font-weight: 700;
                margin-bottom: 8px;
            }
            
            .email-header p {
                font-size: 14px;
                opacity: 0.9;
                font-weight: 400;
            }
            
            .email-content {
                padding: 30px;
            }
            
            .section {
                margin-bottom: 24px;
                border: 1px solid #e2e8f0;
                border-radius: 12px;
                overflow: hidden;
            }
            
            .section-header {
                background-color: #f8fafc;
                padding: 16px 20px;
                border-bottom: 1px solid #e2e8f0;
                font-weight: 600;
                color: #374151;
                font-size: 16px;
            }
            
            .section-body {
                padding: 20px;
            }
            
            .info-grid {
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 16px;
            }
            
            .info-item {
                margin-bottom: 12px;
            }
            
            .info-label {
                font-size: 12px;
                font-weight: 500;
                color: #6b7280;
                text-transform: uppercase;
                letter-spacing: 0.5px;
                margin-bottom: 4px;
            }
            
            .info-value {
                font-size: 14px;
                font-weight: 500;
                color: #111827;
            }
            
            .table {
                width: 100%;
                border-collapse: collapse;
                font-size: 14px;
            }
            
            .table th {
                background-color: #f8fafc;
                padding: 12px;
                text-align: left;
                font-weight: 600;
                color: #374151;
                border-bottom: 1px solid #e2e8f0;
            }
            
            .table td {
                padding: 12px;
                border-bottom: 1px solid #f1f5f9;
            }
            
            .table tr:last-child td {
                border-bottom: none;
            }
            
            .badge {
                display: inline-block;
                padding: 4px 12px;
                border-radius: 20px;
                font-size: 12px;
                font-weight: 500;
                text-transform: uppercase;
                letter-spacing: 0.5px;
            }
            
            .badge-success {
                background-color: #dcfce7;
                color: #166534;
            }
            
            .badge-warning {
                background-color: #fef3c7;
                color: #92400e;
            }
            
            .email-footer {
                background-color: #f8fafc;
                padding: 20px 30px;
                text-align: center;
                border-top: 1px solid #e2e8f0;
                font-size: 12px;
                color: #6b7280;
            }
            
            .footer-text {
                margin-bottom: 8px;
            }
            
            .timestamp {
                font-size: 11px;
                color: #9ca3af;
            }
            
            @media (max-width: 600px) {
                .email-content {
                    padding: 20px;
                }
                
                .info-grid {
                    grid-template-columns: 1fr;
                }
                
                .table {
                    font-size: 12px;
                }
                
                .table th,
                .table td {
                    padding: 8px;
                }
            }
        </style>
    </head>
    <body>
        <div class="email-container">
            <div class="email-header">
                <h1>Nova Garantia Registrada</h1>
                <p>Sistema de Gestao de Garantias</p>
            </div>
            
            <div class="email-content">
                <div class="section">
                    <div class="section-header">Informacoes da Garantia</div>
                    <div class="section-body">
                        <div class="info-grid">
                            <div class="info-item">
                                <div class="info-label">Numero da Garantia</div>
                                <div class="info-value">#' . $garantia['id'] . '</div>
                            </div>
                            <div class="info-item">
                                <div class="info-label">Data de Criacao</div>
                                <div class="info-value">' . date('d/m/Y H:i', strtotime($garantia['dt_cad'])) . '</div>
                            </div>
                            <div class="info-item">
                                <div class="info-label">Codigo do Cliente</div>
                                <div class="info-value">' . $garantia['codcli'] . '</div>
                            </div>
                            <div class="info-item">
                                <div class="info-label">Vendedor</div>
                                <div class="info-value">' . $garantia['codusur'] . '</div>
                            </div>
                            <div class="info-item">
                                <div class="info-label">Responsavel</div>
                                <div class="info-value">' . $garantia['responsavel'] . '</div>
                            </div>
                            <div class="info-item">
                                <div class="info-label">Dispositivo</div>
                                <div class="info-value">' . ucfirst($garantia['dispositivo']) . '</div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="section">
                    <div class="section-header">Itens da Garantia</div>
                    <div class="section-body">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Produto</th>
                                    <th>Descricao</th>
                                    <th>Qtd</th>
                                    <th>Motivo</th>
                                    <th>Obs</th>
                                </tr>
                            </thead>
                            <tbody>';
    
    foreach ($itens as $item) {
        $html .= '
                                <tr>
                                    <td>' . htmlspecialchars($item['codprod']) . '</td>
                                    <td>' . htmlspecialchars($item['descricao'] ?? 'N/A') . '</td>
                                    <td>' . $item['qtd'] . '</td>
                                    <td>' . htmlspecialchars($item['motivo']) . '</td>
                                    <td>' . htmlspecialchars($item['observacao'] ?? '-') . '</td>
                                </tr>';
    }
    
    $html .= '
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <div class="section">
                    <div class="section-header">Status do Pedido</div>
                    <div class="section-body">
                        <span class="badge ' . (isset($garantia['status']) && $garantia['status'] === 'Aberto' ? 'badge-success' : 'badge-warning') . '">
                            ' . (isset($garantia['status']) ? $garantia['status'] : 'Em Digitacao') . '
                        </span>
                        <p style="margin-top: 12px; font-size: 14px; color: #6b7280;">
                            ' . (isset($garantia['status']) && $garantia['status'] === 'Aberto' ? 
                            'A garantia foi finalizada e esta aguardando analise.' : 
                            'A garantia foi salva em modo de digitacao e pode ser editada posteriormente.') . '
                        </p>
                    </div>
                </div>
            </div>
            
            <div class="email-footer">
                <div class="footer-text">
                    Este e um e-mail automatico do Sistema de Garantias.
                </div>
                <div class="timestamp">
                    Gerado em ' . date('d/m/Y H:i:s') . '
                </div>
            </div>
        </div>
    </body>
    </html>';
    
    return $html;
}

function generateEmailText($garantiaData) {
    $garantia = $garantiaData['garantia'];
    $itens = $garantiaData['garantia_itens'];
    
    $text = "NOVA GARANTIA REGISTRADA\n";
    $text .= "=====================\n\n";
    $text .= "Numero: #" . $garantia['id'] . "\n";
    $text .= "Data: " . date('d/m/Y H:i', strtotime($garantia['dt_cad'])) . "\n";
    $text .= "Cliente: " . $garantia['codcli'] . "\n";
    $text .= "Vendedor: " . $garantia['codusur'] . "\n";
    $text .= "Responsavel: " . $garantia['responsavel'] . "\n";
    $text .= "Dispositivo: " . $garantia['dispositivo'] . "\n\n";
    
    $text .= "ITENS:\n";
    $text .= "------\n";
    foreach ($itens as $item) {
        $text .= "- " . $item['codprod'] . " | " . ($item['descricao'] ?? 'N/A') . " | Qtd: " . $item['qtd'] . " | Motivo: " . $item['motivo'] . "\n";
    }
    
    $text .= "\nStatus: " . (isset($garantia['status']) ? $garantia['status'] : 'Em Digitacao') . "\n";
    $text .= "\nE-mail automatico - " . date('d/m/Y H:i:s');
    
    return $text;
}

// Processar requisicao
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ler o input JSON
    $jsonInput = file_get_contents('php://input');
    error_log("Raw input: " . $jsonInput);
    
    $input = json_decode($jsonInput, true);
    
    if (json_last_error() !== JSON_ERROR_NONE) {
        error_log("JSON decode error: " . json_last_error_msg());
        echo json_encode(['success' => false, 'message' => 'Erro ao decodificar JSON: ' . json_last_error_msg()]);
        exit;
    }
    
    // CORREcaO AQUI: usar 'user_email' em vez de 'email'
    if (!isset($input['garantia_data']) || !isset($_SESSION['user_email'])) {
        error_log("Dados incompletos - garantia_data: " . (isset($input['garantia_data']) ? 'SET' : 'NOT SET') . ", session_user_email: " . ($_SESSION['user_email'] ?? 'NOT SET'));
        echo json_encode(['success' => false, 'message' => 'Dados incompletos - usuario nao logado ou dados da garantia faltando']);
        exit;
    }
    
    $garantiaData = $input['garantia_data'];
    $userEmail = $_SESSION['user_email']; // CORREcaO AQUI
    
    error_log("Processando e-mail para: " . $userEmail);
    error_log("Garantia ID: " . $garantiaData['garantia']['id']);
    
    $result = sendGarantiaEmail($garantiaData, $userEmail);
    echo json_encode($result);
    
} else {
    error_log("Metodo nao permitido: " . $_SERVER['REQUEST_METHOD']);
    echo json_encode(['success' => false, 'message' => 'Metodo nao permitido']);
}
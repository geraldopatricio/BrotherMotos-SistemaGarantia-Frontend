<?php
// 1. Inicia a sessão para validar permissões de RCA/Cliente
session_start();

// 2. Carrega variáveis de ambiente (Caminho ajustado para sua estrutura)
$envPath = __DIR__ . '/../../../backend/.env'; // Verifique se este caminho está correto
$env = @parse_ini_file($envPath);

if (!$env) {
    die("Erro crítico: Arquivo de configuração .env não encontrado.");
}

$dbHost = $env['MYSQL_HOST'] ?? '';
$dbUser = $env['MYSQL_USER'] ?? '';
$dbPassword = $env['MYSQL_PASSWORD'] ?? '';
$dbName = $env['MYSQL_NAME'] ?? '';
$dbPort = $env['MYSQL_PORT'] ?? '3306';

// 3. Parâmetros de Filtro vindos da URL (GET)
$codigoCliente = isset($_GET['codigoCliente']) ? trim($_GET['codigoCliente']) : '';
$codigoRCA = isset($_GET['codigoRCA']) ? trim($_GET['codigoRCA']) : '';

// 4. Lógica de Segurança (Igual ao seu backend original)
$sessaoRca = $_SESSION['user_rca'] ?? 0;
$sessaoCliente = $_SESSION['user_cliente'] ?? 0;

if ($sessaoRca > 0) { $codigoRCA = $sessaoRca; }
if ($sessaoCliente > 0) { $codigoCliente = $sessaoCliente; }

$dados = [];

try {
    $conn = new PDO("mysql:host=$dbHost;port=$dbPort;dbname=$dbName;charset=utf8mb4", $dbUser, $dbPassword);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 5. Construção da Query
    $sql = "
        SELECT 
            g.id AS protocolo,
            DATE_FORMAT(g.dt_cad, '%d/%m/%Y') AS dt_cad,
            g.codcli AS CODCLI,
            c.cliente AS CLIENTE,
            g.status,
            g.codusur,
            u.usuario AS NOME
        FROM garantia g
        LEFT JOIN winthor_pcclient c ON g.codcli = c.codcli
        LEFT JOIN login u ON g.codusur = u.codusur
        WHERE 1=1
    ";

    $params = [];
    if (!empty($codigoCliente)) {
        $sql .= " AND g.codcli = :codcli";
        $params[':codcli'] = $codigoCliente;
    }
    if (!empty($codigoRCA)) {
        $sql .= " AND g.codusur = :rca";
        $params[':rca'] = $codigoRCA;
    }

    $sql .= " ORDER BY g.id DESC";
    
    $stmt = $conn->prepare($sql);
    foreach ($params as $key => $val) {
        $stmt->bindValue($key, $val);
    }
    
    $stmt->execute();
    $dados = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Erro ao processar relatório: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Impressão de Garantias</title>
    <style>
    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        padding: 30px;
        color: #1e293b;
        background: white;
        line-height: 1.4;
    }

    /* Cabeçalho do Relatório */
    .header {
        border-bottom: 3px solid #1e293b;
        padding-bottom: 15px;
        margin-bottom: 25px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .header h1 {
        margin: 0;
        font-size: 22px;
        text-transform: uppercase;
        letter-spacing: -0.5px;
    }

    .header-meta {
        font-size: 11px;
        color: #64748b;
        text-align: right;
    }

    /* Filtros aplicados (Informativo) */
    .filter-info {
        background: #f1f5f9;
        padding: 10px 15px;
        border-radius: 8px;
        font-size: 12px;
        margin-bottom: 20px;
        color: #475569;
    }

    /* Tabela */
    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 10px;
    }

    th {
        background: #f8fafc;
        text-align: left;
        padding: 12px 10px;
        border-bottom: 2px solid #e2e8f0;
        font-size: 11px;
        text-transform: uppercase;
        color: #64748b;
    }

    td {
        padding: 12px 10px;
        border-bottom: 1px solid #f1f5f9;
        font-size: 13px;
    }

    tr:nth-child(even) {
        background-color: #fcfcfc;
    }

    /* Badges de Status */
    .status {
        padding: 4px 10px;
        border-radius: 20px;
        font-weight: 700;
        font-size: 10px;
        text-transform: uppercase;
        display: inline-block;
    }

    .status-aprovado {
        background: #dcfce7;
        color: #15803d;
    }

    .status-pendente {
        background: #fef9c3;
        color: #a16207;
    }

    .status-reprovado {
        background: #fee2e2;
        color: #b91c1c;
    }

    .status-padrao {
        background: #f1f5f9;
        color: #475569;
    }

    .text-bold {
        font-weight: 600;
    }

    .text-small {
        font-size: 11px;
        color: #64748b;
    }

    @media print {
        body {
            padding: 0;
        }

        .no-print {
            display: none;
        }

        @page {
            margin: 1.5cm;
        }
    }
    </style>
</head>

<body>

    <div class="header">
        <div>
            <h1>Relatório de Garantias</h1>
            <div class="header-meta">Sistema GNV | Gerência de Pós-Venda</div>
        </div>
        <div class="header-meta">
            Data de Emissão: <?= date('d/m/Y H:i') ?><br>
            Total de Registros: <?= count($dados) ?>
        </div>
    </div>

    <div class="filter-info">
        <strong>Filtros Aplicados:</strong>
        Cliente: <?= $codigoCliente ?: 'Todos' ?> |
        RCA/Vendedor: <?= $codigoRCA ?: 'Todos' ?>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 80px;">Protocolo</th>
                <th style="width: 100px;">Data</th>
                <th>Cliente</th>
                <th>RCA / Vendedor</th>
                <th style="text-align: center;">Status</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($dados) > 0): ?>
            <?php foreach ($dados as $row): 
                    // Lógica para cor do status
                    $statusTxt = $row['status'] ?? 'N/D';
                    $statusClass = 'status-padrao';
                    $statusLower = strtolower($statusTxt);
                    
                    if (strpos($statusLower, 'aprovado') !== false) $statusClass = 'status-aprovado';
                    elseif (strpos($statusLower, 'pendente') !== false) $statusClass = 'status-pendente';
                    elseif (strpos($statusLower, 'reprovado') !== false) $statusClass = 'status-reprovado';
                ?>
            <tr>
                <td class="text-bold">#<?= $row['protocolo'] ?></td>
                <td><?= $row['dt_cad'] ?></td>
                <td>
                    <div class="text-bold"><?= htmlspecialchars($row['CLIENTE']) ?></div>
                    <div class="text-small">Cód: <?= $row['CODCLI'] ?></div>
                </td>
                <td>
                    <div><?= htmlspecialchars($row['NOME'] ?: 'Não Identificado') ?></div>
                    <div class="text-small">RCA: <?= $row['codusur'] ?></div>
                </td>
                <td style="text-align: center;">
                    <span class="status <?= $statusClass ?>"><?= $statusTxt ?></span>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php else: ?>
            <tr>
                <td colspan="5" style="text-align: center; padding: 40px; color: #94a3b8;">
                    Nenhuma garantia encontrada para os filtros selecionados.
                </td>
            </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <div
        style="margin-top: 50px; border-top: 1px solid #eee; padding-top: 10px; font-size: 10px; color: #94a3b8; text-align: center;">
        Este documento é uma representação digital das garantias registradas no sistema.
    </div>

</body>

</html>
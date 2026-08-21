<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }

// 1. Configurações de Banco
$envPath = __DIR__ . '/../../../backend/.env';
$env = @parse_ini_file($envPath);
$conn = new PDO("mysql:host={$env['MYSQL_HOST']};port={$env['MYSQL_PORT']};dbname={$env['MYSQL_NAME']};charset=utf8mb4", $env['MYSQL_USER'], $env['MYSQL_PASSWORD']);

// 2. Parâmetros e Segurança
$protocolo = $_GET['protocolo'] ?? '';
$codigoCliente = $_GET['codigoCliente'] ?? '';
$codigoRCA = $_GET['codigoRCA'] ?? '';
$dataDe = $_GET['dataDe'] ?? '';
$dataAte = $_GET['dataAte'] ?? '';
$exportExcel = isset($_GET['export']) && $_GET['export'] == 'excel';

if (($_SESSION['user_rca'] ?? 0) > 0) $codigoRCA = $_SESSION['user_rca'];
if (($_SESSION['user_cliente'] ?? 0) > 0) $codigoCliente = $_SESSION['user_cliente'];

// 3. Query (Adicionado g.status e ajuste no agrupamento)
$sql = "SELECT g.id AS protocolo, DATE_FORMAT(g.dt_cad, '%d/%m/%Y') AS dt_cad, 
    g.codcli, c.CLIENTE AS cliente, g.codusur, u.NOME AS rca, g.status,
    ROUND(COALESCE(SUM(CASE WHEN (CAST(a.parecer_tec AS CHAR) COLLATE utf8mb4_general_ci = 'Aprovado') THEN (i.qtd * a.valor) ELSE 0 END), 0), 2) AS Aprovado,
    ROUND(COALESCE(SUM(CASE WHEN (CAST(a.parecer_tec AS CHAR) COLLATE utf8mb4_general_ci = 'Reprovado') THEN (i.qtd * a.valor) ELSE 0 END), 0), 2) AS Reprovado,
    ROUND(COALESCE(SUM(CASE WHEN (CAST(a.parecer_tec AS CHAR) COLLATE utf8mb4_general_ci = 'Aberto') THEN (i.qtd * a.valor) ELSE 0 END), 0), 2) AS Aberto,
    ROUND(COALESCE(SUM(CASE WHEN (CAST(a.parecer_tec AS CHAR) COLLATE utf8mb4_general_ci = 'Pendente') THEN (i.qtd * a.valor) ELSE 0 END), 0), 2) AS Pendente
    FROM garantia g
    INNER JOIN garantia_itens i ON CAST(g.id AS CHAR) = CAST(i.fk_garantia AS CHAR)
    LEFT JOIN garantia_itens_automacao a ON CAST(a.fk_item AS CHAR) = CAST(i.id AS CHAR)
    LEFT JOIN winthor_pcclient c ON CAST(c.CODCLI AS CHAR) = CAST(g.codcli AS CHAR)
    LEFT JOIN winthor_pcusuari u ON CAST(u.CODUSUR AS CHAR) = CAST(g.codusur AS CHAR)
    WHERE 1=1";

$params = [];
if($protocolo) { $sql .= " AND g.id = :p"; $params[':p'] = $protocolo; }
if($codigoCliente) { $sql .= " AND g.codcli = :c"; $params[':c'] = $codigoCliente; }
if($codigoRCA) { $sql .= " AND g.codusur = :r"; $params[':r'] = $codigoRCA; }
if($dataDe) { $sql .= " AND g.dt_cad >= :d1"; $params[':d1'] = $dataDe; }
if($dataAte) { $sql .= " AND g.dt_cad <= :d2"; $params[':d2'] = $dataAte . ' 23:59:59'; }

$sql .= " GROUP BY 1, 2, 3, 4, 5, 6, 7 ORDER BY g.id DESC";

$stmt = $conn->prepare($sql);
$stmt->execute($params);
$dados = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($exportExcel) {
    header("Content-Type: application/vnd.ms-excel; charset=utf-8");
    header("Content-Disposition: attachment; filename=Relatorio_Status_Valores_Colunas.xls");
    header("Pragma: no-cache");
    header("Expires: 0");
}

function fmt($v) { return 'R$ ' . number_format($v, 2, ',', '.'); }
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        /* Configuração Paisagem */
        @page { size: landscape; margin: 1cm; }
        
        body { font-family: 'Segoe UI', Arial; padding: 20px; background: white; color: #1e293b; margin: 0; font-size: 11px; }
        .header { border-bottom: 2px solid #1e293b; padding-bottom: 10px; margin-bottom: 15px; display: flex; justify-content: space-between; align-items: flex-end; }
        .header h1 { margin: 0; font-size: 18px; text-transform: uppercase; }
        
        .ribbon { background: #f8fafc; padding: 8px 12px; border-radius: 6px; font-size: 10px; color: #475569; margin-bottom: 15px; border: 1px solid #e2e8f0; }
        
        table { width: 100%; border-collapse: collapse; table-layout: fixed; }
        th { background: #f1f5f9; color: #475569; font-size: 9px; text-transform: uppercase; padding: 8px 4px; border: 1px solid #e2e8f0; text-align: left; }
        td { padding: 6px 4px; border: 1px solid #f1f5f9; word-wrap: break-word; font-size: 10px; }
        tr:nth-child(even) { background: #fafafa; }

        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .bold { font-weight: 700; }
        
        .status-badge { padding: 2px 4px; border-radius: 4px; background: #e2e8f0; font-size: 9px; font-weight: bold; }
        
        /* Cores dos Valores */
        .val-aprovado { color: #15803d; }
        .val-pendente { color: #b45309; }
        .val-reprovado { color: #b91c1c; }
        
        .footer-total { background: #1e293b !important; color: white; font-weight: bold; }
        @media print { .no-print { display: none; } body { padding: 0; } }
    </style>
</head>
<body>
    <div class="header">
        <div>
            <h1>Relatório de Garantia - Status e Valores (Visão Horizontal)</h1>
            <div style="font-size: 10px; color: #64748b;">Módulo de Análise Pós-Venda | GNV</div>
        </div>
        <div style="text-align: right; font-size: 10px; color: #64748b;">
            Emissão: <?= date('d/m/Y H:i') ?> | Registros: <?= count($dados) ?>
        </div>
    </div>

    <div class="ribbon">
        <strong>FILTROS:</strong> 
        Período: <?= $dataDe ? date('d/m/Y', strtotime($dataDe)) : 'Início' ?> - <?= $dataAte ? date('d/m/Y', strtotime($dataAte)) : 'Hoje' ?> | 
        Cliente: <?= $codigoCliente ?: 'Todos' ?> | RCA: <?= $codigoRCA ?: 'Todos' ?>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 55px;">Protoc.</th>
                <th style="width: 65px;">Data</th>
                <th style="width: 50px;">CodCli</th>
                <th>Nome do Cliente</th>
                <th style="width: 50px;">CodUsur</th>
                <th>Representante (RCA)</th>
                <th style="width: 70px;" class="text-center">Status</th>
                <th class="text-right" style="width: 80px;">Aprovado</th>
                <th class="text-right" style="width: 80px;">Reprovado</th>
                <th class="text-right" style="width: 80px;">Aberto</th>
                <th class="text-right" style="width: 80px;">Pendente</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $totA = $totP = $totR = $totAp = 0;
            foreach ($dados as $row): 
                $totA += $row['Aberto']; $totP += $row['Pendente']; 
                $totR += $row['Reprovado']; $totAp += $row['Aprovado'];
            ?>
            <tr>
                <td class="bold">#<?= $row['protocolo'] ?></td>
                <td class="text-center"><?= $row['dt_cad'] ?></td>
                <td><?= $row['codcli'] ?></td>
                <td style="font-size: 9px;"><?= htmlspecialchars($row['cliente']) ?></td>
                <td><?= $row['codusur'] ?></td>
                <td style="font-size: 9px;"><?= htmlspecialchars($row['rca']) ?></td>
                <td class="text-center">
                    <span class="status-badge"><?= $row['status'] ?></span>
                </td>
                <td class="text-right val-aprovado bold"><?= fmt($row['Aprovado']) ?></td>
                <td class="text-right val-reprovado"><?= fmt($row['Reprovado']) ?></td>
                <td class="text-right"><?= fmt($row['Aberto']) ?></td>
                <td class="text-right val-pendente"><?= fmt($row['Pendente']) ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
        <tfoot>
            <tr class="footer-total">
                <td colspan="7" class="text-right">TOTAIS ACUMULADOS:</td>
                <td class="text-right"><?= fmt($totAp) ?></td>
                <td class="text-right"><?= fmt($totR) ?></td>
                <td class="text-right"><?= fmt($totA) ?></td>
                <td class="text-right"><?= fmt($totP) ?></td>
            </tr>
        </tfoot>
    </table>
</body>
</html>

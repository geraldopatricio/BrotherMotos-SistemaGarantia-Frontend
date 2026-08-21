<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }

// 1. Configurações de Banco (mantenha seu código original de conexão)
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

// 3. Query (Mesma sua original)
$sql = "SELECT g.id AS protocolo, DATE_FORMAT(g.dt_cad, '%d/%m/%Y') AS dt_cad, g.codcli, c.CLIENTE AS cliente, g.codusur, u.NOME AS rca,
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
$sql .= " GROUP BY 1, 2, 3, 4, 5, 6 ORDER BY g.id DESC";

$stmt = $conn->prepare($sql);
$stmt->execute($params);
$dados = $stmt->fetchAll(PDO::FETCH_ASSOC);

// 4. Lógica de Exportação Excel
if ($exportExcel) {
    header("Content-Type: application/vnd.ms-excel; charset=utf-8");
    header("Content-Disposition: attachment; filename=Relatorio_Garantia_Valores.xls");
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
        body { font-family: 'Segoe UI', Arial; padding: 30px; background: white; color: #1e293b; margin: 0; }
        .header { display: flex; justify-content: space-between; align-items: center; border-bottom: 3px solid #1e293b; padding-bottom: 15px; margin-bottom: 20px; }
        .header h1 { margin: 0; font-size: 22px; text-transform: uppercase; }
        .meta-info { font-size: 11px; color: #64748b; text-align: right; }
        
        /* Fita cinza igual ao segundo print */
        .ribbon { background: #f1f5f9; padding: 12px 15px; border-radius: 8px; font-size: 11px; color: #475569; margin-bottom: 20px; border: 1px solid #e2e8f0; }
        
        table { width: 100%; border-collapse: collapse; }
        th { background: #f8fafc; color: #64748b; font-size: 10px; text-transform: uppercase; padding: 12px 10px; border-bottom: 2px solid #e2e8f0; text-align: left; }
        td { padding: 12px 10px; font-size: 12px; border-bottom: 1px solid #f1f5f9; }
        tr:nth-child(even) { background: #fafafa; }

        .text-right { text-align: right; }
        .bold { font-weight: 700; }
        .small { font-size: 10px; color: #94a3b8; }
        
        /* Cores de Status para Valores */
        .val-aprovado { color: #15803d; font-weight: bold; }
        .val-pendente { color: #b45309; font-weight: bold; }
        .val-reprovado { color: #b91c1c; font-weight: bold; }
        .val-aberto { color: #1e293b; }

        .footer-total { background: #1e293b !important; color: white; font-weight: bold; }
        @media print { body { padding: 0; } .no-print { display: none; } }
    </style>
</head>
<body>
    <div class="header">
        <div>
            <h1>Relatório de Garantia por Status e Valores</h1>
            <div style="font-size: 12px; color: #64748b;">Sistema GNV | Gerência de Pós-Venda</div>
        </div>
        <div class="meta-info">
            Emissão: <?= date('d/m/Y H:i') ?><br>
            Registros: <?= count($dados) ?>
        </div>
    </div>

    <div class="ribbon">
        <strong>Filtros Aplicados:</strong> 
        Período: <?= $dataDe ? date('d/m/Y', strtotime($dataDe)) : 'Início' ?> até <?= $dataAte ? date('d/m/Y', strtotime($dataAte)) : 'Hoje' ?> | 
        Cliente: <?= $codigoCliente ?: 'Todos' ?> | RCA: <?= $codigoRCA ?: 'Todos' ?>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 80px;">Protocolo</th>
                <th style="width: 80px;">Data</th>
                <th>Cliente / RCA</th>
                <th class="text-right">Aberto</th>
                <th class="text-right">Pendente</th>
                <th class="text-right">Reprovado</th>
                <th class="text-right">Aprovado</th>
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
                <td><?= $row['dt_cad'] ?></td>
                <td>
                    <div class="bold"><?= htmlspecialchars($row['cliente']) ?></div>
                    <div class="small">RCA: <?= htmlspecialchars($row['rca']) ?> (<?= $row['codusur'] ?>)</div>
                </td>
                <td class="text-right val-aberto"><?= fmt($row['Aberto']) ?></td>
                <td class="text-right val-pendente"><?= fmt($row['Pendente']) ?></td>
                <td class="text-right val-reprovado"><?= fmt($row['Reprovado']) ?></td>
                <td class="text-right val-aprovado"><?= fmt($row['Aprovado']) ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
        <tfoot>
            <tr class="footer-total">
                <td colspan="3" class="text-right">TOTAIS:</td>
                <td class="text-right"><?= fmt($totA) ?></td>
                <td class="text-right"><?= fmt($totP) ?></td>
                <td class="text-right"><?= fmt($totR) ?></td>
                <td class="text-right"><?= fmt($totAp) ?></td>
            </tr>
        </tfoot>
    </table>
</body>
</html>

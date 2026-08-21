<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }

// 1. Configurações de Banco
$envPath = __DIR__ . '/../../../backend/.env';
$env = @parse_ini_file($envPath);
$conn = new PDO("mysql:host={$env['MYSQL_HOST']};port={$env['MYSQL_PORT']};dbname={$env['MYSQL_NAME']};charset=utf8mb4", $env['MYSQL_USER'], $env['MYSQL_PASSWORD']);

// 2. Parâmetros
$dataDe = $_GET['dataDe'] ?? date('Y-m-01');
$dataAte = $_GET['dataAte'] ?? date('Y-m-d');
$exportExcel = isset($_GET['export']) && $_GET['export'] == 'excel';

// 3. Query baseada na view vw_garantia_resumo3
$sql = "SELECT 
            codfab, 
            descricao, 
            quantidade, 
            valor_aprovado, 
            uf, 
            DATE_FORMAT(data_garantia, '%d/%m/%Y') AS data_garantia_fmt
        FROM vw_garantia_resumo3 
        WHERE data_garantia BETWEEN :d1 AND :d2
        ORDER BY data_garantia DESC, codfab ASC";

$stmt = $conn->prepare($sql);
$stmt->execute([
    ':d1' => $dataDe,
    ':d2' => $dataAte
]);
$dados = $stmt->fetchAll(PDO::FETCH_ASSOC);

// 4. Lógica de Exportação Excel
if ($exportExcel) {
    header("Content-Type: application/vnd.ms-excel; charset=utf-8");
    header("Content-Disposition: attachment; filename=Relatorio_Valores_Aprovados.xls");
    header("Pragma: no-cache");
    header("Expires: 0");
    // Forçar saída em modo tabela simples para Excel
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
        .header h1 { margin: 0; font-size: 20px; text-transform: uppercase; letter-spacing: -1px; }
        .meta-info { font-size: 11px; color: #64748b; text-align: right; }
        
        .ribbon { background: #f1f5f9; padding: 12px 15px; border-radius: 8px; font-size: 11px; color: #475569; margin-bottom: 20px; border: 1px solid #e2e8f0; }
        
        table { width: 100%; border-collapse: collapse; }
        th { background: #f8fafc; color: #64748b; font-size: 10px; text-transform: uppercase; padding: 12px 10px; border-bottom: 2px solid #e2e8f0; text-align: left; }
        td { padding: 10px; font-size: 11px; border-bottom: 1px solid #f1f5f9; }
        tr:nth-child(even) { background: #fafafa; }

        .text-right { text-align: right; }
        .bold { font-weight: 700; }
        
        .footer-total { background: #1e293b !important; color: white; font-weight: bold; }
        .val-aprovado { color: #15803d; font-weight: bold; }
        
        @media print { 
            body { padding: 0; } 
            .no-print { display: none; }
            .header { border-bottom-color: #000; }
        }
    </style>
</head>
<body>
    <div class="header">
        <div>
            <h1>Relatório de Valores Aprovados</h1>
            <div style="font-size: 11px; color: #64748b; font-weight: bold; text-transform: uppercase; letter-spacing: 1px;">Base de Dados: vw_garantia_resumo3</div>
        </div>
        <div class="meta-info">
            Emissão: <?= date('d/m/Y H:i') ?><br>
            Período: <?= date('d/m/Y', strtotime($dataDe)) ?> à <?= date('d/m/Y', strtotime($dataAte)) ?>
        </div>
    </div>

    <div class="ribbon">
        <strong>Filtros Aplicados:</strong> 
        Resultados entre <u><?= date('d/m/Y', strtotime($dataDe)) ?></u> e <u><?= date('d/m/Y', strtotime($dataAte)) ?></u>. 
        Total de itens localizados: <strong><?= count($dados) ?></strong>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 100px;">Cód. Fab</th>
                <th>Descrição do Item</th>
                <th style="width: 60px;" class="text-right">Qtd</th>
                <th style="width: 40px;">UF</th>
                <th style="width: 90px;">Data Gar.</th>
                <th style="width: 120px;" class="text-right">Valor Aprovado</th>
            </tr>
        </thead>

<tbody>
            <?php 
            $totQtd = 0;
            $totValor = 0;

            if (count($dados) > 0):
                foreach ($dados as $row): 
                    // Limpeza e conversão
                    $qtdFormatada = str_replace(',', '.', $row['quantidade']);
                    $qtdNumerica = (float)$qtdFormatada;

                    $totQtd += $qtdNumerica;
                    $totValor += (float)$row['valor_aprovado'];
                ?>
                <tr>
                    <td class="bold"><?= htmlspecialchars($row['codfab']) ?></td>
                    <td><?= htmlspecialchars($row['descricao'] ?? '') ?></td>
                    <td class="text-right"><?= number_format($qtdNumerica, 2, ',', '.') ?></td>
                    <td class="bold"><?= $row['uf'] ?></td>
                    <td><?= $row['data_garantia_fmt'] ?></td>
                    <td class="text-right val-aprovado"><?= fmt($row['valor_aprovado']) ?></td>
                </tr>
                <?php endforeach; ?> <!-- Fim do Foreach -->

            <?php else: ?> <!-- Este else agora pertence ao IF da linha 192 -->
                <tr>
                    <td colspan="6" style="text-align: center; padding: 40px; color: #94a3b8;">
                        Nenhum registro encontrado para o período selecionado.
                    </td>
                </tr>
            <?php endif; ?> <!-- Único endif para fechar o IF principal -->
        </tbody>



        <?php if (count($dados) > 0): ?>

<tfoot>
    <tr class="footer-total">
        <td colspan="2" class="text-right">TOTAIS:</td>
        <!-- Usando ponto no milhar e vírgula no decimal (Padrão Brasileiro) -->
        <td class="text-right"><?= number_format($totQtd, 2, ',', '.') ?></td>
        <td colspan="2"></td>
        <td class="text-right"><?= fmt($totValor) ?></td>
    </tr>
</tfoot>

        <?php endif; ?>
    </table>

    <div style="margin-top: 20px; font-size: 9px; color: #94a3b8; text-align: center;">
        Relatório gerado automaticamente pelo Sistema GNV - Gerência de Pós-Venda.
    </div>
</body>
</html>

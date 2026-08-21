<?php

// 1. INICIAR A SESSÃO (Deve ser a primeira coisa no arquivo)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 2. VALIDAR SE O USUÁRIO ESTÁ LOGADO
// Caso não esteja, redireciona para a tela de login (index.php)
if (!isset($_SESSION['user_logged_in']) || $_SESSION['user_logged_in'] !== true) {
    header('Location: ../index.php'); // Ajuste o caminho conforme a localização deste arquivo
    exit;
}

// 3. CAPTURAR DADOS DA SESSÃO (Opcional, caso queira usar no relatório)
$usuario_logado = $_SESSION['user_usuario'] ?? 'Desconhecido';

$host = 'garantia_db';
$port = '3306';
$database = 'db_garantia';
$username = 'root';
$password = 'odlareg';

$conn = new mysqli($host, $username, $password, $database, $port);


$conn->set_charset("utf8mb4"); 

if ($conn->connect_error) {
    die("Erro de conexão: " . $conn->connect_error);
}

if(isset($_GET['id'])){
    $id_protocolo = intval($_GET['id']);

    // Suas queries permanecem as mesmas
$queryCabecalho = "SELECT distinct g.id as protocolo
                            , g.user_cad AS user_cad
                            , date_format(g.dt_cad, '%d/%m/%Y') AS dt_cad
                            , (SELECT cc.codcli FROM winthor_pcclient cc WHERE cc.codcli = g.codcli) AS codcli
                            , (SELECT cc.cliente FROM winthor_pcclient cc WHERE cc.codcli = g.codcli) AS cliente
                            , (SELECT u.codusur FROM winthor_pcusuari u WHERE u.codusur = g.codusur) AS codrca 
                            , (SELECT u.nome FROM winthor_pcusuari u WHERE u.codusur = g.codusur) AS rca 
                            , g.responsavel
                            , g.`status`     
                        FROM garantia g, winthor_pcclient c, garantia_itens n
                        left join garantia_itens_automacao a ON a.fk_item = n.id COLLATE utf8mb4_general_ci 
                             AND a.codFAB = n.codFAB COLLATE utf8mb4_general_ci
                        WHERE g.id = n.fk_garantia
                        AND g.codcli = c.CODCLI
                        AND g.id = $id_protocolo
                        AND n.fk_garantia = g.id";

    $resultCabecalho = $conn->query($queryCabecalho);

     // --- INICIO DO NOVO CÓDIGO ---
    $user_cad = ""; // Define vazio por padrão para não dar erro
    
    // Verifica se trouxe resultado
    if ($resultCabecalho && $resultCabecalho->num_rows > 0) {
        // Pega a primeira linha de dados
        $linha_temp = $resultCabecalho->fetch_assoc();
        
        // Salva o valor do usuário na variável
        $user_cad = $linha_temp['user_cad'];
        
        // IMPORTANTE: Volta o ponteiro para o início (0)
        // Se não fizer isso, o quadro "Informações do Protocolo" ficará vazio
        $resultCabecalho->data_seek(0);
    }
    // --- FIM DO NOVO CÓDIGO ---

$queryConteudo = "SELECT distinct g.id as codpro
            , n.id
            , n.codprod
            , n.codfab
            , (SELECT descricao 
                FROM winthor_pcprodut p 
                WHERE p.codfab = n.codfab COLLATE utf8mb4_general_ci
                LIMIT 1) as produto
            , n.qtd
            , n.motivo
            , n.observacao   
            , a.nf
            , a.pedido
            , DATE_FORMAT(a.dt_fatura, '%d/%m/%Y') as dt_fatura
            , a.saldo AS qtd_tot
            , (a.saldo - n.qtd) AS saldo_rest
            , a.valor
            , (n.qtd * a.valor) AS valor_aprovar
            , a.parecer_tec AS parecer
            , a.parecer_detalhes    
        FROM garantia g, winthor_pcclient c, garantia_itens n
        left join garantia_itens_automacao a ON a.fk_item = n.id COLLATE utf8mb4_general_ci
        WHERE g.id = n.fk_garantia
        AND g.codcli = c.CODCLI
        AND g.id = $id_protocolo
        AND n.fk_garantia = g.id";

    $resultConteudo = $conn->query($queryConteudo);

$query_soma = "SELECT SUM(qtd) AS qtd,
                          SUM(qtd_tot) AS qtd_tot,
                          SUM(valor) AS valor
                    FROM (SELECT sum(n.qtd) AS qtd
                               , sum(a.saldo) AS qtd_tot
                               , (sum(a.valor) * n.qtd) AS valor
                            FROM garantia g, winthor_pcclient c, garantia_itens n
                            left join garantia_itens_automacao a ON a.fk_item = n.id COLLATE utf8mb4_general_ci 
                              AND a.codfab = n.codfab COLLATE utf8mb4_general_ci
                              AND a.parecer_tec = 'Aprovado'
                            WHERE g.id = n.fk_garantia
                              AND g.codcli = c.codcli
                              AND g.id = $id_protocolo
                              AND n.fk_garantia = g.id
                            GROUP BY n.qtd, a.saldo, a.valor) tab";

    $result_soma = $conn->query($query_soma);

$queryRodape = "SELECT distinct g.id as codpro1
            , a.fk_item
            , a.codprod
            , a.codfab
            , a.parecer_tec
            , a.parecer_detalhes
            , a.usuario
            , a.dt_hora
        FROM garantia g, winthor_pcclient c, garantia_itens n
        left join garantia_itens_automacao_workflow a ON a.fk_item = n.id COLLATE utf8mb4_general_ci
        WHERE g.id = n.fk_garantia
        AND g.codcli = c.CODCLI
        AND g.id = $id_protocolo
        AND n.fk_garantia = g.id    
        AND a.fk_item IS NOT NULL ";

    $resultRodape = $conn->query($queryRodape);

?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pedido de Garantia - Protocolo #<?php echo $id_protocolo; ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
    :root {
        --primary-color: #2c3e50;
        --secondary-color: #3498db;
        --success-color: #27ae60;
        --danger-color: #e74c3c;
        --warning-color: #f39c12;
        --light-gray: #f8f9fa;
        --border-color: #dee2e6;
        --text-dark: #2c3e50;
        --text-light: #6c757d;
    }

    * {
        box-sizing: border-box;
        margin: 0;
        padding: 0;
    }

    body {
        font-family: 'Roboto', sans-serif;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        margin: 0;
        padding: 30px;
        min-height: 100vh;
    }

    .invoice-container {
        max-width: 1200px;
        margin: 0 auto;
        background: white;
        border-radius: 15px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        overflow: hidden;
        position: relative;
    }

    .invoice-header {
        background: var(--primary-color);
        color: white;
        padding: 30px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-bottom: 4px solid var(--secondary-color);
    }

    .company-info h1 {
        font-size: 28px;
        font-weight: 700;
        margin-bottom: 5px;
    }

    .company-info p {
        font-size: 14px;
        opacity: 0.9;
    }

    .invoice-title {
        text-align: right;
    }

    .invoice-title h2 {
        font-size: 24px;
        font-weight: 600;
        margin-bottom: 5px;
    }

    .invoice-title .protocol {
        font-size: 18px;
        opacity: 0.9;
    }

    .invoice-details {
        padding: 30px;
        background: var(--light-gray);
        border-bottom: 1px solid var(--border-color);
    }

    .details-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 20px;
    }

    .detail-item {
        background: white;
        padding: 15px;
        border-radius: 10px;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    }

    .detail-label {
        font-size: 12px;
        color: var(--text-light);
        text-transform: uppercase;
        font-weight: 600;
        margin-bottom: 5px;
    }

    .detail-value {
        font-size: 16px;
        font-weight: 600;
        color: var(--text-dark);
    }

    .items-section {
        padding: 30px;
    }

    .section-title {
        font-size: 20px;
        font-weight: 600;
        color: var(--primary-color);
        margin-bottom: 20px;
        padding-bottom: 10px;
        border-bottom: 2px solid var(--secondary-color);
    }

    .invoice-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        margin: 20px 0;
        background: white;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    }

    .invoice-table th {
        background: var(--primary-color);
        color: white;
        padding: 15px 12px;
        text-align: left;
        font-weight: 600;
        font-size: 13px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .invoice-table td {
        padding: 12px;
        border-bottom: 1px solid var(--border-color);
        font-size: 13px;
        vertical-align: top;
    }

    .invoice-table tbody tr:last-child td {
        border-bottom: none;
    }

    .invoice-table tbody tr:hover {
        background-color: rgba(52, 152, 219, 0.05);
    }

    .status-badge {
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
    }

    .status-aprovado {
        background: #d4edda;
        color: #155724;
    }

    .status-reprovado {
        background: #f8d7da;
        color: #721c24;
    }

    .status-pendente {
        background: #fff3cd;
        color: #856404;
    }

    .totals-section {
        padding: 20px 30px;
        background: var(--light-gray);
        border-top: 2px solid var(--border-color);
    }

    .totals-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
        max-width: 400px;
        margin-left: auto;
    }

    .total-item {
        background: white;
        padding: 15px;
        border-radius: 10px;
        text-align: center;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    }

    .total-label {
        font-size: 12px;
        color: var(--text-light);
        text-transform: uppercase;
        margin-bottom: 5px;
    }

    .total-value {
        font-size: 24px;
        font-weight: 700;
        color: var(--success-color);
    }

    .workflow-section {
        padding: 30px;
        border-top: 1px solid var(--border-color);
    }

    .timeline {
        position: relative;
        padding-left: 30px;
        margin-top: 20px;
    }

    .timeline::before {
        content: '';
        position: absolute;
        left: 10px;
        top: 0;
        bottom: 0;
        width: 2px;
        background: var(--secondary-color);
    }

    .timeline-item {
        position: relative;
        margin-bottom: 20px;
        padding: 15px;
        background: white;
        border-radius: 10px;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    }

    .timeline-item::before {
        content: '';
        position: absolute;
        left: -25px;
        top: 20px;
        width: 12px;
        height: 12px;
        border-radius: 50%;
        background: var(--secondary-color);
        border: 3px solid white;
    }

    .timeline-date {
        font-size: 12px;
        color: var(--text-light);
        margin-bottom: 5px;
    }

    .timeline-content {
        font-size: 14px;
        color: var(--text-dark);
    }

    .invoice-footer {
        background: var(--primary-color);
        color: white;
        padding: 20px 30px;
        text-align: center;
        font-size: 12px;
        opacity: 0.9;
    }

    .print-btn {
        position: fixed;
        top: 20px;
        right: 20px;
        background: var(--success-color);
        color: white;
        border: none;
        padding: 12px 24px;
        border-radius: 25px;
        cursor: pointer;
        font-weight: 600;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        transition: all 0.3s ease;
        z-index: 1000;
    }

    .print-btn:hover {
        background: #219653;
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.3);
    }

    @media print {
        body {
            background: white;
            padding: 0;
        }

        .invoice-container {
            box-shadow: none;
            border-radius: 0;
        }

        .print-btn {
            display: none;
        }
    }

    .text-right {
        text-align: right;
    }

    .text-center {
        text-align: center;
    }

    .font-weight-bold {
        font-weight: 600;
    }

    .mb-3 {
        margin-bottom: 15px;
    }

    .mt-3 {
        margin-top: 15px;
    }
    </style>
</head>

<body>
    <button class="print-btn" onclick="window.print()">
        📄 Imprimir Garantia
    </button>

    <div class="invoice-container">
        <!-- Cabeçalho -->
        <div class="invoice-header">
            <div class="company-info">
                <h1>RELATÓRIO DE GARANTIA</h1>
                <p>Sistema de Gestão de Garantias</p>
            </div>
            <div class="invoice-title">
                <h2>CODIGO #<?php echo $id_protocolo; ?></h2>
                <div class="protocol">Garantia aberta por: <?php echo isset($user_cad) ? $user_cad : 'N/D'; ?></div>
            </div>
        </div>

        <!-- Informações do Protocolo -->
        <div class="invoice-details">
            <div class="details-grid">
                <?php if ($resultCabecalho->num_rows > 0) : ?>
                <?php while ($row = $resultCabecalho->fetch_assoc()) : ?>
                <div class="detail-item">
                    <div class="detail-label">Data do Protocolo</div>
                    <div class="detail-value"><?php echo htmlspecialchars($row["dt_cad"]); ?></div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Código do Cliente</div>
                    <div class="detail-value"><?php echo htmlspecialchars($row["codcli"]); ?></div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Nome do Cliente</div>
                    <div class="detail-value"><?php echo htmlspecialchars($row["cliente"]); ?></div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Status da Solicitação</div>
                    <div class="detail-value">
                        <span
                            class="status-badge status-<?php echo strtolower(htmlspecialchars($row["status"] ?? '')); ?>">
                            <strong><?php echo htmlspecialchars($row["status"]); ?></strong>
                        </span>
                    </div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Código RCA</div>
                    <div class="detail-value"><?php echo htmlspecialchars($row["codrca"]); ?></div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Responsável (RCA)</div>
                    <div class="detail-value"><?php echo htmlspecialchars($row["rca"]); ?></div>
                </div>
                <?php endwhile; ?>
                <?php endif; ?>
            </div>
        </div>

        <!-- Itens da Garantia -->
        <div class="items-section">
            <div class="section-title">Itens da Garantia</div>
            <table class="invoice-table">
                <thead>
                    <tr>
                        <!-- <th>ID</th> -->
                        <th>Cód. Fab.</th>
                        <th>Descrição</th>
                        <th>QTD</th>
                        <th>Detalhes</th>
                        <th>NF</th>
                        <th>Data Fat.</th>
                        <th>Valor Unit.</th>
                        <th>Valor Total</th>
                        <th>Parecer</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $totalR1 = 0; 
                    if ($resultConteudo->num_rows > 0) : 
                        // Buscar todos os resultados e inverter a ordem
                        $rows = [];
                        while ($row = $resultConteudo->fetch_assoc()) {
                            $rows[] = $row;
                        }
                        $rows = array_reverse($rows);
                    ?>
                    <?php foreach ($rows as $row) : 
                        $q1 = $row["qtd"] ?? 0;
                        $v1 = $row["valor"] ?? 0; // Se for nulo, vira 0
                        $r1 = $q1 * $v1;
                        $totalR1 += $r1;
                        
                        // CORREÇÃO: Adicionado ?? '' para evitar erro no strtolower
                        $statusClass = strtolower($row["parecer"] ?? ''); 
                    ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row["codfab"] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($row["produto"] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($row["qtd"] ?? 0); ?></td>

                        <!-- CORREÇÃO: parecer_detalhes -->
                        <td><?php echo htmlspecialchars($row["parecer_detalhes"] ?? ''); ?></td>

                        <td><?php echo htmlspecialchars($row["nf"] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($row["dt_fatura"] ?? ''); ?></td>

                        <!-- CORREÇÃO: number_format com cast (float) e valor padrão -->
                        <td>R$ <?php echo number_format((float)($row["valor"] ?? 0), 2, ',', '.'); ?></td>
                        <td>R$ <?php echo number_format((float)($r1 ?? 0), 2, ',', '.'); ?></td>

                        <td>
                            <span class="status-badge status-<?php echo $statusClass; ?>">
                                <?php echo htmlspecialchars($row["parecer"] ?? ''); ?>
                            </span>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php else : ?>
                    <tr>
                        <td colspan="10" class="text-center">Nenhum item encontrado.</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Totais -->
        <div class="totals-section">
            <div class="totals-grid">
                <div class="total-item">
                    <div class="total-label">Valor Total</div>
                    <div class="total-value">R$ <?php echo number_format($totalR1, 2, ',', '.'); ?></div>
                </div>
                <?php if ($result_soma->num_rows > 0) : ?>
                <?php while ($row = $result_soma->fetch_assoc()) : ?>
                <div class="total-item">
                    <div class="total-label">Valor Aprovado</div>
                    <div class="total-value">R$ <?= number_format((float) ($row['valor'] ?? 0), 2, ',', '.'); ?></div>
                </div>
                <?php endwhile; ?>
                <?php endif; ?>
            </div>
        </div>

        <!-- Workflow -->
        <div class="workflow-section">
            <div class="section-title">Workflow - Histórico</div>
            <div class="timeline">
                <?php
                if ($resultRodape->num_rows > 0) :
                    $lastUsuario = null;
                    $colorIndex = 0;
                    $colors = [
                        "#e3f2fd", // Azul claro
                        "#e8f5e9", // Verde claro
                        "#fffde7", // Amarelo bem suave
                        "#f3e5f5", // Lilás claro
                        "#f1f8e9"  // Verde-amarelado
                    ];



                    while ($row = $resultRodape->fetch_assoc()) :
                        $usuarioAtual = $row["usuario"];

                        // Muda de cor apenas se o usuário for diferente da linha anterior
                        if ($usuarioAtual !== $lastUsuario) {
                            $colorIndex = ($colorIndex + 1) % count($colors);
                            $lastUsuario = $usuarioAtual;
                        }

                        $corFundo = $colors[$colorIndex];
                ?>
                <div class="timeline-item" style="background-color: <?= $corFundo ?>;">
                    <div class="timeline-date"><?= htmlspecialchars($row["dt_hora"] ?? '') ?></div>
                    <div class="timeline-content">
                        <strong><?= htmlspecialchars($row["usuario"] ?? 'Sistema') ?></strong> -
                        <?= htmlspecialchars($row["parecer_detalhes"] ?? '') ?>
                    </div>
                </div>
                <?php endwhile; ?>
                <?php else : ?>
                <div class="timeline-item">
                    <div class="timeline-content">Nenhum registro de workflow encontrado.</div>
                </div>
                <?php endif; ?>
            </div>

        </div>

        <!-- Rodapé -->
        <div class="invoice-footer">
            <p>Protocolo gerado automaticamente em <?php echo date('d/m/Y H:i:s'); ?> | Sistema de Garantias</p>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Adiciona data de impressão
        const printDate = new Date().toLocaleString('pt-BR');
        console.log('Invoice gerado em:', printDate);
    });
    </script>
</body>

</html>

<?php
} else {
    echo "<div style='padding: 20px; text-align: center; color: red;'>Parâmetro 'id' não encontrado na URL.</div>";
    exit;
}
?>

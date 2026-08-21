<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);

$host = '192.168.0.25';
$port = '3306';
$database = 'db-garantia';
$username = 'user-garantia';
$password = 'odlareg';

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
try {
    $conn = new mysqli($host, $username, $password, $database, $port);
    $conn->set_charset("utf8mb4");
} catch (mysqli_sql_exception $e) {
    if (isset($_GET['action'])) {
        header('Content-Type: application/json');
        http_response_code(500);
        echo json_encode(['error' => 'Erro de conexão com o banco de dados: ' . $e->getMessage()]);
        exit;
    }
    die("Erro de conexão: " . $e->getMessage());
}

function getDashboardData($conn, $ano, $mes) {
    $data = [
        'cards' => [
            'aprovado'  => ['valor' => 0, 'qtd' => 0],
            'reprovado' => ['valor' => 0, 'qtd' => 0],
            'aberto'    => ['valor' => 0, 'qtd' => 0],
            'pendente'  => ['valor' => 0, 'qtd' => 0],
        ],
        'donutChart' => ['labels' => [], 'data' => []],
        'polarChart' => ['labels' => [], 'data' => []],
        'barChart'   => [
            'labels' => ['Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho', 'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro'],
            'datasets' => []
        ]
    ];

    $sql_cards = "SELECT 
                    parecer_tec AS status,
                    COUNT(*) AS quantidade,
                    COALESCE(SUM(qtd * valor), 0) AS valor_total
                  FROM v_rel_valor_status
                  WHERE SUBSTRING(dt_cad, 7, 4) = ? AND SUBSTRING(dt_cad, 4, 2) = ?
                  GROUP BY parecer_tec";
    
    $stmt_cards = $conn->prepare($sql_cards);
    $stmt_cards->bind_param("ss", $ano, $mes);
    $stmt_cards->execute();
    $result_cards = $stmt_cards->get_result();

    while ($row = $result_cards->fetch_assoc()) {
        $status = strtolower($row['status']);
        if (array_key_exists($status, $data['cards'])) {
            $data['cards'][$status]['valor'] = round($row['valor_total'], 2);
            $data['cards'][$status]['qtd'] = (int)$row['quantidade'];
        }
    }
    $stmt_cards->close();
    
    foreach ($data['cards'] as $status => $values) {
        if ($values['qtd'] > 0) {
            $data['donutChart']['labels'][] = ucfirst($status);
            $data['donutChart']['data'][] = $values['qtd'];
        }
    }
    
    $sql_polar = "SELECT LEFT(cliente, 20) AS cliente, ROUND(SUM(aprovado), 2) AS valor
                  FROM v_rel_report_valor_status
                  WHERE SUBSTRING(dt_cad, 7, 4) = ? AND SUBSTRING(dt_cad, 4, 2) = ?
                  GROUP BY LEFT(cliente, 20)
                  ORDER BY valor DESC
                  LIMIT 5";
    $stmt_polar = $conn->prepare($sql_polar);
    $stmt_polar->bind_param("ss", $ano, $mes);
    $stmt_polar->execute();
    $result_polar = $stmt_polar->get_result();

    while($row = $result_polar->fetch_assoc()) {
        $data['polarChart']['labels'][] = $row['cliente'];
        $data['polarChart']['data'][] = (float)$row['valor'];
    }
    $stmt_polar->close();
    
    $sql_bar = "SELECT 
                    DATE_FORMAT(dt_cad, '%m') AS mes_num,
                    status,
                    COUNT(id) AS total
                FROM 
                    garantia
                    WHERE YEAR(dt_cad) = ?
                GROUP BY 
                    status, mes_num
                ORDER BY 
                    mes_num, STATUS";
    
    $stmt_bar = $conn->prepare($sql_bar);
    $stmt_bar->bind_param("s", $ano);
    $stmt_bar->execute();
    $result_bar = $stmt_bar->get_result();

    $datasets_temp = [];
    $status_colors = [
        'aprovado'  => 'rgba(105, 108, 255, 0.8)', 'reprovado' => 'rgba(255, 171, 0, 0.8)',
        'aberto'    => 'rgba(255, 62, 29, 0.8)', 'pendente'  => 'rgba(23, 207, 213, 0.8)'
    ];

    while($row = $result_bar->fetch_assoc()) {
        $status = strtolower($row['status']);
        $month_index = (int)$row['mes_num'] - 1;
        $total = (int)$row['total'];

        if (!isset($datasets_temp[$status])) {
            $datasets_temp[$status] = [
                'label' => ucfirst($status),
                'data' => array_fill(0, 12, 0),
                'backgroundColor' => $status_colors[$status] ?? 'rgba(150, 150, 150, 0.8)'
            ];
        }
        $datasets_temp[$status]['data'][$month_index] = $total;
    }
    
    $data['barChart']['datasets'] = array_values($datasets_temp);
    $stmt_bar->close();

    if ($ano == date("Y")) {
        $mes_atual_num = (int)date("n");

        $data['barChart']['labels'] = array_slice($data['barChart']['labels'], 0, $mes_atual_num);

        foreach ($data['barChart']['datasets'] as &$dataset) {
            $dataset['data'] = array_slice($dataset['data'], 0, $mes_atual_num);
        }
        unset($dataset);
    }

    return $data;
}

if (isset($_GET['action']) && $_GET['action'] == 'get_data') {
    $ano = htmlspecialchars(trim($_GET['ano'] ?? date("Y")));
    $mes = htmlspecialchars(trim($_GET['mes'] ?? date("m")));
    
    $dashboardData = getDashboardData($conn, $ano, $mes);
    
    $conn->close();
    
    header('Content-Type: application/json');
    echo json_encode($dashboardData);
    exit();
}

$ano_inicial = date("Y");
$mes_inicial = date("m");
$initialData = getDashboardData($conn, $ano_inicial, $mes_inicial);
$conn->close();
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard de Garantias</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
    body {
        font-family: 'roboto', ui-sans-serif, system-ui, sans-serif;
        background-color: #f4f6f9;
        color: #333;
        margin: 0;
    }

    .container-fluid {
        margin: 0 auto;
        padding: 20px;
        max-width: 1800px;
    }

    .filters-container {
        background-color: #fff;
        padding: 15px 20px;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        display: flex;
        align-items: center;
        gap: 20px;
        margin-bottom: 25px;
        flex-wrap: wrap;
    }

    .filters-container label {
        font-weight: 600;
        color: #555;
    }

    .filters-container select {
        padding: 8px 12px;
        border: 1px solid #ddd;
        border-radius: 8px;
        background-color: #f8f9fa;
        font-size: 1rem;
        font-weight: 500;
        transition: border-color 0.2s, box-shadow 0.2s;
        cursor: pointer;
    }

    .filters-container select:focus {
        outline: none;
        border-color: #007bff;
        box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.2);
    }

    .card-modern {
        background-color: #fff;
        border: none;
        border-radius: 16px;
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.07);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        height: 100%;
        overflow: hidden;
        display: flex;
        flex-direction: column;
    }

    .card-modern:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
    }

    .card-modern .card-body {
        padding: 25px;
        display: flex;
        flex-direction: column;
        flex-grow: 1;
    }

    .card-modern .card-top {
        display: flex;
        align-items: center;
        margin-bottom: 15px;
        gap: 15px;
    }

    .card-modern .card-icon svg {
        width: 50px;
        height: 50px;
    }

    .card-modern .card-info h4 {
        font-size: 1.8rem;
        font-weight: 700;
        margin: 0;
        line-height: 1.2;
    }

    .card-modern .card-info small {
        font-size: 0.9rem;
        font-weight: 600;
        color: #6c757d;
    }

    .card-modern .card-title {
        font-size: 1rem;
        font-weight: 600;
        margin: 0 0 5px 0;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .bg-label-primary {
        border-left: 5px solid #696cff;
    }

    .bg-label-primary .card-icon svg {
        stroke: #696cff;
    }

    .bg-label-warning {
        border-left: 5px solid #ffab00;
    }

    .bg-label-warning .card-icon svg {
        stroke: #ffab00;
    }

    .bg-label-danger {
        border-left: 5px solid #ff3e1d;
    }

    .bg-label-danger .card-icon svg {
        stroke: #ff3e1d;
    }

    .bg-label-info {
        border-left: 5px solid #17cfd5;
    }

    .bg-label-info .card-icon svg {
        stroke: #17cfd5;
    }

    .chart-container {
        background-color: #fff;
        padding: 25px;
        border-radius: 16px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        height: 450px;
        width: 100%;
        display: flex;
        flex-direction: column;
    }

    .chart-container h5 {
        margin: 0 0 15px 0;
        text-align: center;
        font-size: 1.1rem;
        color: #555;
    }

    .chart-container canvas {
        flex-grow: 1;
        max-height: 360px;
    }

    .row {
        display: flex;
        flex-wrap: wrap;
        margin: 0 -15px;
    }

    .col-lg-3,
    .col-lg-6 {
        padding: 0 15px;
        margin-bottom: 30px;
        box-sizing: border-box;
        display: flex;
    }

    .col-lg-3 {
        width: 25%;
    }

    .col-lg-6 {
        width: 50%;
    }

    #loading-indicator {
        font-weight: bold;
        color: #007bff;
        transition: opacity 0.3s;
        opacity: 0;
    }

    @media (max-width: 1200px) {
        .col-lg-3 {
            width: 50%;
        }

        .col-lg-6 {
            width: 100%;
        }
    }

    @media (max-width: 768px) {

        .col-lg-3,
        .col-lg-6 {
            width: 100%;
        }
    }

    /* ========================================= */
    /* CSS PARA LAYOUT FLEXÍVEL E DE TELA CHEIA */
    /* ========================================= */

    /* 1. Fundação: Garante que o HTML e o Body possam ocupar a tela inteira */
    html,
    body {
        height: 100%;
        /* Essencial para o layout de altura total */
        margin: 0;
        padding: 0;
    }

    /* 2. Wrapper Principal: Transforma a página em um contêiner flexível vertical */
    .content-wrapper {
        display: flex;
        flex-direction: column;
        /* Organiza os filhos em coluna (filtros, rows, etc.) */
        min-height: 100vh;
        /* Garante que o wrapper tenha no mínimo a altura da tela */
    }


    /* 4. Linha que deve esticar: A .row com os gráficos */
    .row.row-stretch {
        flex-grow: 1;
        /* Faz esta linha específica crescer para preencher o espaço na vertical */
    }

    /* 5. Colunas: Elas já são flexíveis, apenas garantimos a direção */
    .col-lg-3,
    .col-lg-6 {
        display: flex;
        flex-direction: column;
        /* Garante que o card/gráfico dentro dela possa crescer */
    }

    /* 6. MODIFICAÇÃO: Remove a altura fixa do chart-container */
    .chart-container {
        background-color: #fff;
        padding: 25px;
        border-radius: 16px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        /* height: 450px; --- REMOVA OU COMENTE ESTA LINHA --- */
        width: 100%;
        display: flex;
        flex-direction: column;
        flex-grow: 1;
        /* NOVO: Faz o contêiner do gráfico crescer para preencher a coluna */
    }

    /* 7. MODIFICAÇÃO: Garante que o card também cresça (ele já tinha height: 100%)
        .card-modern {
            background-color: #fff;
            border: none;
            border-radius: 16px;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.07);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            flex-grow: 1; 
        } 
        */
    </style>
</head>

<body>

    <div class="content-wrapper">
        <div class="container-fluid">

            <div class="filters-container">
                <form id="filterForm" onsubmit="return false;">
                    <label for="ano">Dados analíticos do mês atual</label>
                    <!-- 
            <label for="ano">Ano:</label>
            <select name="ano" id="ano">
                <option value="2024" <?= $ano_inicial == '2024' ? 'selected' : '' ?>>2024</option>
                <option value="2025" <?= $ano_inicial == '2025' ? 'selected' : '' ?>>2025</option>
            </select>
            <label for="mes">Mês:</label>
            <select name="mes" id="mes">
                <?php
                /*
                    $meses = ['01' => 'Janeiro', '02' => 'Fevereiro', '03' => 'Março', '04' => 'Abril', '05' => 'Maio', '06' => 'Junho', '07' => 'Julho', '08' => 'Agosto', '09' => 'Setembro', '10' => 'Outubro', '11' => 'Novembro', '12' => 'Dezembro'];
                    foreach ($meses as $num => $nome) {
                        $selected = ($num == $mes_inicial) ? 'selected' : '';
                        echo "<option value=\"$num\" $selected>$nome</option>";
                    }
                        */
                ?>
            </select>
                  -->
                </form>
                <div id="loading-indicator">Carregando...</div>
            </div>

            <div class="row">
                <!-- Card Aprovados -->
                <div class="col-lg-3">
                    <div class="card-modern bg-label-primary">
                        <div class="card-body">
                            <div class="card-top">
                                <div class="card-icon"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                        viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path
                                            d="M7 11v8a1 1 0 0 1 -1 1h-2a1 1 0 0 1 -1 -1v-7a1 1 0 0 1 1 -1h3a4 4 0 0 0 4 -4v-1a2 2 0 0 1 4 0v5h3a2 2 0 0 1 2 2l-1 5a2 3 0 0 1 -2 2h-7a3 3 0 0 1 -3 -3" />
                                    </svg></div>
                                <div class="card-info">
                                    <h4 id="valor-aprovado">
                                        <?= 'R$ ' . number_format($initialData['cards']['aprovado']['valor'], 2, ',', '.') ?>
                                    </h4>
                                    <small>QTD: <span
                                            id="qtd-aprovado"><?= $initialData['cards']['aprovado']['qtd'] ?></span></small>
                                </div>
                            </div>
                            <p class="card-title">APROVADOS</p>
                        </div>
                    </div>
                </div>
                <!-- Card Reprovados -->
                <div class="col-lg-3">
                    <div class="card-modern bg-label-warning">
                        <div class="card-body">
                            <div class="card-top">
                                <div class="card-icon"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                        viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path
                                            d="M7 13v-8a1 1 0 0 0 -1 -1h-2a1 1 0 0 0 -1 1v7a1 1 0 0 0 1 1h3a4 4 0 0 1 4 4v1a2 2 0 0 0 4 0v-5h3a2 2 0 0 0 2 -2l-1 -5a2 3 0 0 0 -2 -2h-7a3 3 0 0 0 -3 3" />
                                    </svg></div>
                                <div class="card-info">
                                    <h4 id="valor-reprovado">
                                        <?= 'R$ ' . number_format($initialData['cards']['reprovado']['valor'], 2, ',', '.') ?>
                                    </h4>
                                    <small>QTD: <span
                                            id="qtd-reprovado"><?= $initialData['cards']['reprovado']['qtd'] ?></span></small>
                                </div>
                            </div>
                            <p class="card-title">REPROVADOS</p>
                        </div>
                    </div>
                </div>
                <!-- Card Abertos -->
                <div class="col-lg-3">
                    <div class="card-modern bg-label-danger">
                        <div class="card-body">
                            <div class="card-top">
                                <div class="card-icon"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                        viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path
                                            d="M5 19l2.757 -7.351a1 1 0 0 1 .936 -.649h12.307a1 1 0 0 1 .986 1.164l-.996 5.211a2 2 0 0 1 -1.964 1.625h-14.026a2 2 0 0 1 -2 -2v-11a2 2 0 0 1 2 -2h4l3 3h7a2 2 0 0 1 2 2v2" />
                                    </svg></div>
                                <div class="card-info">
                                    <h4 id="valor-aberto">
                                        <?= 'R$ ' . number_format($initialData['cards']['aberto']['valor'], 2, ',', '.') ?>
                                    </h4>
                                    <small>QTD: <span
                                            id="qtd-aberto"><?= $initialData['cards']['aberto']['qtd'] ?></span></small>
                                </div>
                            </div>
                            <p class="card-title">ABERTOS</p>
                        </div>
                    </div>
                </div>
                <!-- Card Pendentes -->
                <div class="col-lg-3">
                    <div class="card-modern bg-label-info">
                        <div class="card-body">
                            <div class="card-top">
                                <div class="card-icon"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                        viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path d="M12 3c7.2 0 9 1.8 9 9s-1.8 9 -9 9s-9 -1.8 -9 -9s1.8 -9 9 -9z" />
                                        <path d="M12 8v4" />
                                        <path d="M12 16h.01" />
                                    </svg></div>
                                <div class="card-info">
                                    <h4 id="valor-pendente">
                                        <?= 'R$ ' . number_format($initialData['cards']['pendente']['valor'], 2, ',', '.') ?>
                                    </h4>
                                    <small>QTD: <span
                                            id="qtd-pendente"><?= $initialData['cards']['pendente']['qtd'] ?></span></small>
                                </div>
                            </div>
                            <p class="card-title">PENDENTES</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-3">
                    <div class="chart-container">
                        <h5>Status (Quantidade)</h5>
                        <canvas id="donutChart"></canvas>
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="chart-container">
                        <h5>Top 5 Clientes (Valor Aprovado)</h5>
                        <canvas id="polarAreaChart"></canvas>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="chart-container">
                        <h5 id="bar-chart-title">Status por Mês (Ano <?= $ano_inicial ?>)</h5>
                        <canvas id="stackedBarChart"></canvas>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Referências aos elementos do formulário e de feedback
        const anoSelect = document.getElementById('ano');
        const mesSelect = document.getElementById('mes');
        const loadingIndicator = document.getElementById('loading-indicator');
        const barChartTitle = document.getElementById('bar-chart-title'); // NOVO: Referência ao título

        // Variáveis para guardar as instâncias dos gráficos
        let donutChart, polarChart, stackedBarChart;

        // Função para formatar números como moeda brasileira (R$)
        const formatCurrency = (value) => new Intl.NumberFormat('pt-BR', {
            style: 'currency',
            currency: 'BRL'
        }).format(value);

        // Função para inicializar os gráficos com os dados da primeira carga
        function initCharts(initialData) {
            // --- GRÁFICO 1: DOUGHNUT ---
            const donutCtx = document.getElementById('donutChart').getContext('2d');
            donutChart = new Chart(donutCtx, {
                type: 'doughnut',
                data: {
                    labels: initialData.donutChart.labels.length > 0 ? initialData.donutChart.labels : [
                        'Nenhum dado'
                    ],
                    datasets: [{
                        data: initialData.donutChart.data.length > 0 ? initialData.donutChart
                            .data : [1],
                        backgroundColor: ['rgba(105, 108, 255, 0.8)', 'rgba(255, 171, 0, 0.8)',
                            'rgba(255, 62, 29, 0.8)', 'rgba(23, 207, 213, 0.8)'
                        ],
                        borderColor: '#fff',
                        borderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top'
                        }
                    }
                }
            });

            // --- GRÁFICO 2: POLAR AREA ---
            const polarCtx = document.getElementById('polarAreaChart').getContext('2d');
            polarChart = new Chart(polarCtx, {
                type: 'polarArea',
                data: {
                    labels: initialData.polarChart.labels.length > 0 ? initialData.polarChart.labels : [
                        'Nenhum dado'
                    ],
                    datasets: [{
                        data: initialData.polarChart.data.length > 0 ? initialData.polarChart
                            .data : [1],
                        backgroundColor: ['rgba(255, 99, 132, 0.7)', 'rgba(75, 192, 192, 0.7)',
                            'rgba(255, 205, 86, 0.7)', 'rgba(54, 162, 235, 0.7)',
                            'rgba(153, 102, 255, 0.7)'
                        ]
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        r: {
                            beginAtZero: true
                        }
                    }
                }
            });

            // --- GRÁFICO 3: BARRAS (AGORA DINÂMICO) ---
            const barCtx = document.getElementById('stackedBarChart').getContext('2d');
            stackedBarChart = new Chart(barCtx, {
                type: 'bar',
                data: {
                    // Usa os dados iniciais do PHP
                    labels: initialData.barChart.labels,
                    datasets: initialData.barChart.datasets
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        title: {
                            display: false
                        }, // O título está no H5
                        tooltip: {
                            mode: 'index',
                            intersect: false
                        }
                    },
                    scales: {
                        x: {
                            stacked: true
                        },
                        y: {
                            stacked: true,
                            beginAtZero: true
                        }
                    }
                }
            });
        }

        // Função que busca os dados via AJAX e atualiza a interface
        async function updateDashboard() {
            loadingIndicator.style.opacity = '1';
            const ano = anoSelect.value;
            const mes = mesSelect.value;

            try {
                const response = await fetch(`?action=get_data&ano=${ano}&mes=${mes}`);
                if (!response.ok) throw new Error(`Erro na rede: ${response.statusText}`);
                const data = await response.json();

                // Atualiza os cards (sem alterações aqui)
                document.getElementById('valor-aprovado').textContent = formatCurrency(data.cards.aprovado
                    .valor);
                document.getElementById('qtd-aprovado').textContent = data.cards.aprovado.qtd;
                document.getElementById('valor-reprovado').textContent = formatCurrency(data.cards.reprovado
                    .valor);
                document.getElementById('qtd-reprovado').textContent = data.cards.reprovado.qtd;
                document.getElementById('valor-aberto').textContent = formatCurrency(data.cards.aberto
                    .valor);
                document.getElementById('qtd-aberto').textContent = data.cards.aberto.qtd;
                document.getElementById('valor-pendente').textContent = formatCurrency(data.cards.pendente
                    .valor);
                document.getElementById('qtd-pendente').textContent = data.cards.pendente.qtd;

                // Atualiza o gráfico Donut (sem alterações aqui)
                donutChart.data.labels = data.donutChart.labels.length > 0 ? data.donutChart.labels : [
                    'Nenhum dado'
                ];
                donutChart.data.datasets[0].data = data.donutChart.data.length > 0 ? data.donutChart.data :
                    [1];
                donutChart.update();

                // Atualiza o gráfico Polar (sem alterações aqui)
                polarChart.data.labels = data.polarChart.labels.length > 0 ? data.polarChart.labels : [
                    'Nenhum dado'
                ];
                polarChart.data.datasets[0].data = data.polarChart.data.length > 0 ? data.polarChart.data :
                    [1];
                polarChart.update();

                // --- NOVO: Atualiza o gráfico de Barras ---
                barChartTitle.textContent = `Status por Mês no Ano de ${ano}`; // Atualiza o título
                stackedBarChart.data.labels = data.barChart.labels;
                stackedBarChart.data.datasets = data.barChart.datasets; // Substitui todos os datasets
                stackedBarChart.update();

            } catch (error) {
                console.error('Falha ao atualizar o dashboard:', error);
                alert('Não foi possível carregar os dados. Verifique o console para mais detalhes.');
            } finally {
                loadingIndicator.style.opacity = '0';
            }
        }

        // Inicializa os gráficos com os dados da página
        initCharts(<?php echo json_encode($initialData); ?>);

        // Adiciona os "escutadores" de evento para os filtros
        anoSelect.addEventListener('change', updateDashboard);
        mesSelect.addEventListener('change', updateDashboard);
    });
    </script>

</body>

</html>
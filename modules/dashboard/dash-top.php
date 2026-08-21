<?php
// session_start();
$cliente_session = $_SESSION['user_cliente'] ?? '0';
$rca_session     = $_SESSION['user_rca'] ?? '0';
$data_inicio_default = date('Y-m-01');
$data_fim_default    = date('Y-m-d');
$url_backend = "SEU_ENDPOINT_AQUI"; // Certifique-se de definir esta variável
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Premium Warranty Dashboard</title>

    <!-- Google Fonts & Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
    :root {
        --primary: #6366f1;
        --warning: #f59e0b;
        --danger: #ef4444;
        --info: #06b6d4;
        --bg: #f8fafc;
        --card-bg: #ffffff;
        --text-main: #1e293b;
        --text-muted: #64748b;
    }

    body {
        font-family: 'Poppins', sans-serif;
        background: var(--bg);
        color: var(--text-main);
        margin: 0;
        padding: 20px;
    }

    .container-fluid {
        max-width: 1600px;
        margin: 0 auto;
    }

    /* Header & Filters */
    .header-flex {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 30px;
    }

    .filters {
        background: var(--card-bg);
        padding: 12px 24px;
        border-radius: 16px;
        display: flex;
        gap: 20px;
        align-items: center;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    }

    .filters input {
        border: 1px solid #e2e8f0;
        padding: 8px 12px;
        border-radius: 8px;
        font-family: inherit;
        color: var(--text-main);
    }

    /* Grid */
    .row {
        display: flex;
        flex-wrap: wrap;
        margin: -12px;
    }

    .col {
        padding: 12px;
        box-sizing: border-box;
    }

    .col-3 {
        width: 25%;
    }

    .col-4 {
        width: 33.33%;
    }

    .col-6 {
        width: 50%;
    }

    .col-8 {
        width: 66.66%;
    }

    /* Enhanced Cards */
    .card {
        background: var(--card-bg);
        padding: 24px;
        border-radius: 24px;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05);
        position: relative;
        overflow: hidden;
        transition: transform 0.2s;
        border: 1px solid rgba(0, 0, 0, 0.03);
    }

    .card:hover {
        transform: translateY(-5px);
    }

    .card-icon {
        position: absolute;
        right: 20px;
        top: 20px;
        font-size: 2.5rem;
        opacity: 0.2;
    }

    .card-title {
        font-size: 0.85rem;
        font-weight: 600;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: 1px;
        display: block;
        margin-bottom: 8px;
    }

    .card h4 {
        font-size: 2.2rem;
        margin: 0;
        font-weight: 700;
    }

    /* Border colors for cards */
    .status-aberto {
        border-bottom: 5px solid var(--primary);
    }

    .status-processamento {
        border-bottom: 5px solid var(--warning);
    }

    .status-aguardando {
        border-bottom: 5px solid var(--danger);
    }

    .status-analise {
        border-bottom: 5px solid var(--info);
    }

    /* Chart Containers */
    .chart-container {
        background: var(--card-bg);
        padding: 24px;
        border-radius: 24px;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05);
        height: 400px;
        position: relative;
    }

    .chart-container h5 {
        margin: 0 0 20px 0;
        font-size: 1.1rem;
        font-weight: 600;
        color: var(--text-main);
        display: flex;
        align-items: center;
        gap: 10px;
    }

    #loading {
        font-size: 0.8rem;
        color: var(--primary);
        font-weight: 600;
        display: none;
    }

    @media (max-width: 1024px) {

        .col-3,
        .col-4,
        .col-6,
        .col-8 {
            width: 100%;
        }
    }
    </style>
</head>

<body>

    <div class="container-fluid">
        <div class="header-flex">
            <div>
                <h2 style="margin:0">Dashboard de Garantias</h2>
                <p style="color:var(--text-muted); margin:0">Análise em tempo real de processos</p>
            </div>
            <div class="filters">
                <label><i class='bx bx-calendar'></i> Período:</label>
                <input type="date" id="dt_inicio" value="<?= $data_inicio_default ?>">
                <input type="date" id="dt_fim" value="<?= $data_fim_default ?>">
                <span id="loading"><i class='bx bx-loader-alt bx-spin'></i> Atualizando...</span>
            </div>
        </div>

        <!-- Cards Row -->
        <div class="row">
            <div class="col col-3">
                <div class="card status-aberto">
                    <i class='bx bx-door-open card-icon' style="color: var(--primary)"></i>
                    <span class="card-title">Aberto</span>
                    <h4 id="qtd-aberto">0</h4>
                </div>
            </div>
            <div class="col col-3">
                <div class="card status-processamento">
                    <i class='bx bx-cog card-icon' style="color: var(--warning)"></i>
                    <span class="card-title">Processamento</span>
                    <h4 id="qtd-processamento">0</h4>
                </div>
            </div>
            <div class="col col-3">
                <div class="card status-aguardando">
                    <i class='bx bx-time-five card-icon' style="color: var(--danger)"></i>
                    <span class="card-title">Aguardando</span>
                    <h4 id="qtd-aguardando">0</h4>
                </div>
            </div>
            <div class="col col-3">
                <div class="card status-analise">
                    <i class='bx bx-search-alt card-icon' style="color: var(--info)"></i>
                    <span class="card-title">Em Análise</span>
                    <h4 id="qtd-analise">0</h4>
                </div>
            </div>
        </div>

        <!-- Charts Row 1 -->
        <div class="row" style="margin-top: 20px;">
            <div class="col col-4">
                <div class="chart-container">
                    <h5><i class='bx bx-pie-chart-alt-2'></i> Distribuição Atual</h5>
                    <canvas id="donutChart"></canvas>
                </div>
            </div>
            <div class="col col-8">
                <div class="chart-container">
                    <h5><i class='bx bx-bar-chart-square'></i> Tendência e Volume</h5>
                    <canvas id="mixedChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Charts Row 2 (Novos Gráficos) -->
        <div class="row" style="margin-top: 20px;">
            <div class="col col-6">
                <div class="chart-container">
                    <h5><i class='bx bx-radar'></i> Equilíbrio de Status (Radar)</h5>
                    <canvas id="radarChart"></canvas>
                </div>
            </div>
            <div class="col col-6">
                <div class="chart-container">
                    <h5><i class='bx bx-analyse'></i> Proporcionalidade (Polar)</h5>
                    <canvas id="polarChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const colors = {
            'ABERTO': '#6366f1',
            'EM PROCESSAMENTO': '#f59e0b',
            'AGUARDANDO APROVAÇÃO': '#ef4444',
            'EM ANÁLISE': '#06b6d4'
        };

        let charts = {};

        function initCharts() {
            const commonOptions = {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 20,
                            usePointStyle: true,
                            font: {
                                family: 'Poppins',
                                size: 12
                            }
                        }
                    }
                }
            };

            // 1. Donut Chart (Mais grosso/espesso)
            charts.donut = new Chart(document.getElementById('donutChart'), {
                type: 'doughnut',
                data: {
                    labels: [],
                    datasets: [{
                        data: [],
                        backgroundColor: Object.values(colors),
                        borderWidth: 0,
                        hoverOffset: 20
                    }]
                },
                options: {
                    ...commonOptions,
                    cutout: '60%', // Deixa o gráfico mais "gordo"
                    borderRadius: 10
                }
            });

            // 2. Mixed Chart (Stacked Bar + Line)
            charts.mixed = new Chart(document.getElementById('mixedChart'), {
                type: 'bar',
                data: {
                    labels: [],
                    datasets: [{
                            label: 'Volume',
                            data: [],
                            backgroundColor: '#6366f1',
                            borderRadius: 8,
                            order: 2
                        },
                        {
                            label: 'Tendência',
                            data: [],
                            type: 'line',
                            borderColor: '#ef4444',
                            tension: 0.4,
                            borderWidth: 3,
                            pointBackgroundColor: '#ef4444',
                            order: 1
                        }
                    ]
                },
                options: {
                    ...commonOptions,
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                display: false
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            }
                        }
                    }
                }
            });

            // 3. Radar Chart (Novo)
            charts.radar = new Chart(document.getElementById('radarChart'), {
                type: 'radar',
                data: {
                    labels: [],
                    datasets: [{
                        label: 'Score de Status',
                        data: [],
                        backgroundColor: 'rgba(99, 102, 241, 0.2)',
                        borderColor: '#6366f1',
                        pointBackgroundColor: '#6366f1'
                    }]
                },
                options: commonOptions
            });

            // 4. Polar Area Chart (Novo)
            charts.polar = new Chart(document.getElementById('polarChart'), {
                type: 'polarArea',
                data: {
                    labels: [],
                    datasets: [{
                        data: [],
                        backgroundColor: Object.values(colors).map(c => c + 'CC')
                    }]
                },
                options: commonOptions
            });
        }

        async function fetchData() {
            document.getElementById('loading').style.display = 'inline-block';
            // Simulação de chamada (Substitua pelo seu fetch real conforme o código original)
            // const response = await fetch(...);

            // Mock de dados para demonstração se necessário
            const mockData = [{
                    status_agrupado: 'ABERTO',
                    qtd: 45
                },
                {
                    status_agrupado: 'EM PROCESSAMENTO',
                    qtd: 28
                },
                {
                    status_agrupado: 'AGUARDANDO APROVAÇÃO',
                    qtd: 15
                },
                {
                    status_agrupado: 'EM ANÁLISE',
                    qtd: 32
                }
            ];

            setTimeout(() => { // Simulando delay
                updateUI(mockData);
                document.getElementById('loading').style.display = 'none';
            }, 500);
        }

        function updateUI(dados) {
            const ids = {
                'ABERTO': 'qtd-aberto',
                'EM PROCESSAMENTO': 'qtd-processamento',
                'AGUARDANDO APROVAÇÃO': 'qtd-aguardando',
                'EM ANÁLISE': 'qtd-analise'
            };

            const labels = [],
                valores = [],
                bgColors = [];

            dados.forEach(item => {
                const status = item.status_agrupado;
                if (ids[status]) document.getElementById(ids[status]).innerText = item.qtd;
                labels.push(status);
                valores.push(item.qtd);
                bgColors.push(colors[status]);
            });

            // Update Donut
            charts.donut.data.labels = labels;
            charts.donut.data.datasets[0].data = valores;
            charts.donut.data.datasets[0].backgroundColor = bgColors;
            charts.donut.update();

            // Update Mixed
            charts.mixed.data.labels = labels;
            charts.mixed.data.datasets[0].data = valores;
            charts.mixed.data.datasets[1].data = valores; // Linha seguindo os dados
            charts.mixed.update();

            // Update Radar
            charts.radar.data.labels = labels;
            charts.radar.data.datasets[0].data = valores;
            charts.radar.update();

            // Update Polar
            charts.polar.data.labels = labels;
            charts.polar.data.datasets[0].data = valores;
            charts.polar.update();
        }

        initCharts();
        fetchData();

        document.getElementById('dt_inicio').addEventListener('change', fetchData);
        document.getElementById('dt_fim').addEventListener('change', fetchData);
    });
    </script>
</body>

</html>
<?php
// Não precisamos de session_start() aqui pois este arquivo é incluído no index.php que já tem a sessão.

// Capturamos o código do cliente da sessão
$codcli_session = $_SESSION['user_codcli'] ?? ''; 

$data_inicio_default = date('Y-m-01');
$data_fim_default    = date('Y-m-d');
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
        font-family: 'Segoe UI', Roboto, sans-serif;
        background: #f4f6f9;
        margin: 0;
        padding: 0;
    }

    .container-fluid {
        width: 100%;
        padding: 20px;
        box-sizing: border-box;
    }

    .filters {
        background: #fff;
        padding: 15px 20px;
        border-radius: 12px;
        margin-bottom: 25px;
        display: flex;
        gap: 15px;
        align-items: center;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        flex-wrap: wrap;
    }

    .filters label {
        font-weight: 600;
        color: #555;
    }

    input[type="date"],
    input[type="text"],
    input[type="number"] {
        padding: 8px;
        border: 1px solid #ddd;
        border-radius: 8px;
        outline: none;
        background: white;
    }

    #codcli_filtro {
        width: 100px;
    }

    .row {
        display: flex;
        flex-wrap: wrap;
        margin: 0 -10px;
    }

    .col {
        padding: 10px;
        box-sizing: border-box;
    }

    .col-3 {
        width: 25%;
    }

    .col-4 {
        width: 33.333%;
    }

    .col-8 {
        width: 66.666%;
    }

    .card {
        background: #fff;
        padding: 25px;
        border-radius: 16px;
        box-shadow: 0 6px 15px rgba(0, 0, 0, 0.05);
        border-left: 6px solid #ddd;
        height: 100%;
        box-sizing: border-box;
    }

    .card h4 {
        margin: 10px 0 0;
        font-size: 2.8rem;
        font-weight: 800;
        color: #333;
    }

    .card-title {
        font-size: 0.9rem;
        font-weight: 700;
        color: #888;
        text-transform: uppercase;
    }

    .status-aberto {
        border-left-color: #696cff;
    }

    .status-processamento {
        border-left-color: #ffab00;
    }

    .status-aguardando {
        border-left-color: #ff3e1d;
    }

    .status-analise {
        border-left-color: #17cfd5;
    }

    .chart-container {
        background: #fff;
        padding: 25px;
        border-radius: 16px;
        box-shadow: 0 6px 15px rgba(0, 0, 0, 0.05);
        height: 500px;
        position: relative;
    }

    h5 {
        margin: 0 0 20px;
        text-align: center;
        color: #444;
        text-transform: uppercase;
    }

    #loading {
        font-weight: bold;
        color: #696cff;
        opacity: 0;
        transition: 0.3s;
        margin-left: auto;
    }

    @media (max-width: 1200px) {
        .col-3 {
            width: 50%;
        }

        .col-4,
        .col-8 {
            width: 100%;
        }
    }

    @media (max-width: 768px) {
        .col-3 {
            width: 100%;
        }

        .filters {
            flex-direction: column;
            align-items: flex-start;
        }
    }
    </style>
</head>

<body>

    <div class="container-fluid">
        <div class="filters">
            <label>Período de:</label>
            <input type="date" id="dt_inicio" value="<?= $data_inicio_default ?>">

            <label>Até:</label>
            <input type="date" id="dt_fim" value="<?= $data_fim_default ?>">

            <label>Cód. Cliente:</label>
            <!-- Aqui inserimos o código da sessão no value -->
            <input type="number" id="codcli_filtro" placeholder="Ex: 123"
                value="<?= htmlspecialchars($codcli_session) ?>">

            <span id="loading">Sincronizando...</span>
        </div>

        <div class="row">
            <div class="col col-3">
                <div class="card status-aberto"><span class="card-title">Aberto</span>
                    <h4 id="qtd-aberto">0</h4>
                </div>
            </div>
            <div class="col col-3">
                <div class="card status-processamento"><span class="card-title">Em Processamento</span>
                    <h4 id="qtd-processamento">0</h4>
                </div>
            </div>
            <div class="col col-3">
                <div class="card status-aguardando"><span class="card-title">Aguardando Aprovação</span>
                    <h4 id="qtd-aguardando">0</h4>
                </div>
            </div>
            <div class="col col-3">
                <div class="card status-analise"><span class="card-title">Em Análise</span>
                    <h4 id="qtd-analise">0</h4>
                </div>
            </div>
        </div>

        <div class="row" style="margin-top: 10px;">
            <div class="col col-4">
                <div class="chart-container">
                    <h5>Distribuição por Status</h5><canvas id="donutChart"></canvas>
                </div>
            </div>
            <div class="col col-8">
                <div class="chart-container">
                    <h5>Comparativo Quantitativo</h5><canvas id="barChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const ENDPOINT = '../backend/routers/dash.php';
        const colors = {
            'ABERTO': '#696cff',
            'EM PROCESSAMENTO': '#ffab00',
            'AGUARDANDO APROVAÇÃO': '#ff3e1d',
            'EM ANÁLISE': '#17cfd5'
        };
        let charts = {};

        function initCharts() {
            const opt = {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            usePointStyle: true,
                            padding: 20
                        }
                    }
                }
            };
            charts.donut = new Chart(document.getElementById('donutChart'), {
                type: 'doughnut',
                data: {
                    labels: [],
                    datasets: [{
                        data: [],
                        backgroundColor: [],
                        borderWidth: 2
                    }]
                },
                options: {
                    ...opt,
                    cutout: '65%'
                }
            });
            charts.bar = new Chart(document.getElementById('barChart'), {
                type: 'bar',
                data: {
                    labels: [],
                    datasets: [{
                        label: 'Quantidade',
                        data: [],
                        backgroundColor: [],
                        borderRadius: 6
                    }]
                },
                options: {
                    ...opt,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                precision: 0
                            }
                        }
                    }
                }
            });
        }

        async function fetchData() {
            const loading = document.getElementById('loading');
            loading.style.opacity = 1;

            const start = document.getElementById('dt_inicio').value;
            const end = document.getElementById('dt_fim').value;
            const cliente = document.getElementById('codcli_filtro').value;

            // Debug para ver se o valor está sendo capturado no console
            console.log(`Buscando dados para Cliente: ${cliente}, De: ${start}, Até: ${end}`);

            const url = `${ENDPOINT}?dt_inicio=${start}&dt_fim=${end}&codcli=${cliente}`;

            try {
                const response = await fetch(url);
                const json = await response.json();
                if (json.success) {
                    updateUI(json.data);
                } else {
                    console.warn("API retornou sucesso falso:", json);
                    resetUI();
                }
            } catch (e) {
                console.error("Erro ao buscar dados:", e);
            } finally {
                loading.style.opacity = 0;
            }
        }

        function resetUI() {
            const ids = ['qtd-aberto', 'qtd-processamento', 'qtd-aguardando', 'qtd-analise'];
            ids.forEach(id => document.getElementById(id).innerText = '0');
            charts.donut.data.labels = [];
            charts.donut.data.datasets[0].data = [];
            charts.donut.update();
            charts.bar.data.labels = [];
            charts.bar.data.datasets[0].data = [];
            charts.bar.update();
        }

        function updateUI(dados) {
            const cardIds = {
                'ABERTO': 'qtd-aberto',
                'EM PROCESSAMENTO': 'qtd-processamento',
                'AGUARDANDO APROVAÇÃO': 'qtd-aguardando',
                'EM ANÁLISE': 'qtd-analise'
            };

            // Zera os cards antes de atualizar
            Object.values(cardIds).forEach(id => document.getElementById(id).innerText = '0');

            const labels = [],
                valores = [],
                bgColors = [];

            if (dados && dados.length > 0) {
                dados.forEach(item => {
                    const status = item.status_agrupado;
                    if (cardIds[status]) document.getElementById(cardIds[status]).innerText = item.qtd;
                    labels.push(status);
                    valores.push(item.qtd);
                    bgColors.push(colors[status] || '#888');
                });
            }

            charts.donut.data.labels = labels;
            charts.donut.data.datasets[0].data = valores;
            charts.donut.data.datasets[0].backgroundColor = bgColors;
            charts.donut.update();

            charts.bar.data.labels = labels;
            charts.bar.data.datasets[0].data = valores;
            charts.bar.data.datasets[0].backgroundColor = bgColors;
            charts.bar.update();
        }

        // Eventos
        document.getElementById('dt_inicio').addEventListener('change', fetchData);
        document.getElementById('dt_fim').addEventListener('change', fetchData);

        const inputCli = document.getElementById('codcli_filtro');
        inputCli.addEventListener('change', fetchData);
        inputCli.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') fetchData();
        });

        initCharts();

        // Executa a busca inicial
        fetchData();
    });
    </script>
</body>

</html>
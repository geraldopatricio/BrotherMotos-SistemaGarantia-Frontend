<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }

// --- CAPTURA E TRATAMENTO DE VARIÁVEIS DE SESSÃO ---
$user_usuario   = $_SESSION['user_usuario'] ?? ''; 
$user_tipo      = $_SESSION['user_tipo']    ?? '';

$codcli_sessao  = ($_SESSION['user_codcli'] === 'sem dados') ? '' : ($_SESSION['user_codcli'] ?? '');
$codusur_sessao = ($_SESSION['user_rca'] === 'sem dados')    ? '' : ($_SESSION['user_rca']    ?? '');

$data_inicio_default = '2023-01-01';
$data_fim_default    = date('Y-m-d');
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard de Garantias</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

    <style>
    body { font-family: 'Segoe UI', Roboto, sans-serif; background: #f4f6f9; margin: 0; padding: 15px; color: #333; }
    
    .filters {
        background: #fff; padding: 20px; border-radius: 12px; margin-bottom: 25px;
        display: flex; flex-wrap: wrap; gap: 15px; align-items: center; box-shadow: 0 4px 12px rgba(0,0,0,0.05);
    }

    .filter-group { display: flex; align-items: center; gap: 10px; flex: 1; min-width: 200px; }
    .filter-group label { font-weight: 600; font-size: 0.9rem; color: #555; }

    input[type="date"] {
        padding: 10px; border: 1px solid #dce1e6; border-radius: 8px; outline: none;
        flex: 1; font-size: 1rem; background: #f8f9fa;
    }

    #loading { font-weight: bold; color: #696cff; opacity: 0; transition: 0.3s; display: flex; align-items: center; gap: 5px; }

    .row-cards { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 15px; margin-bottom: 25px; }
    .card {
        background: #fff; padding: 20px; border-radius: 16px; box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        border-left: 6px solid #ddd; display: flex; justify-content: space-between; align-items: center;
        cursor: pointer; transition: 0.2s ease-in-out;
    }
    .card:hover { transform: translateY(-3px); }
    .card-info h4 { margin: 0; font-size: 1.8rem; font-weight: 800; }
    .card-title { font-size: 0.75rem; font-weight: 700; color: #888; text-transform: uppercase; }
    .card-icon { font-size: 2.2rem; opacity: 0.2; }

    .status-aberto { border-left-color: #696cff; }
    .status-concluido { border-left-color: #71dd37; }
    .status-reprovado { border-left-color: #8592a3; }
    .status-pendencia { border-left-color: #e83e8c; }

    .row-charts { display: flex; gap: 20px; margin-bottom: 25px; flex-wrap: wrap; }
    .chart-col { background: #fff; padding: 20px; border-radius: 16px; box-shadow: 0 6px 15px rgba(0,0,0,0.05); flex: 1; min-width: 300px; }
    .chart-full { width: 100%; flex: 100%; }
    .canvas-container { position: relative; height: 300px; width: 100%; }
    h5 { margin: 0 0 20px 0; text-align: center; color: #555; text-transform: uppercase; font-size: 0.9rem; }

    .modal-overlay {
        position: fixed; top: 0; left: 0; width: 100%; height: 100%;
        background: rgba(0,0,0,0.5); display: none; align-items: center; justify-content: center; z-index: 2000; padding: 10px;
    }
    .modal-content { background: #fff; border-radius: 16px; width: 100%; max-width: 650px; display: flex; flex-direction: column; max-height: 90vh; }
    .modal-header { padding: 20px; border-bottom: 1px solid #eee; display: flex; justify-content: space-between; align-items: center; }
    .modal-body { padding: 15px; overflow-y: auto; flex: 1; }
    #modal-list { list-style: none; padding: 0; margin: 0; }
    #modal-list li { padding: 15px; border-bottom: 1px solid #f1f3f5; display: flex; flex-direction: column; font-size: 0.9rem; }

    @media (max-width: 768px) {
        .filters { flex-direction: column; align-items: stretch; }
        .row-cards { grid-template-columns: 1fr 1fr; }
    }
    @media (max-width: 480px) { .row-cards { grid-template-columns: 1fr; } }
    </style>
</head>

<body>

    <div class="filters">
        <div class="filter-group">
            <label><i class="fa-solid fa-calendar-day"></i> Início:</label>
            <input type="date" id="dt_inicio" value="<?= $data_inicio_default ?>">
        </div>
        <div class="filter-group">
            <label><i class="fa-solid fa-calendar-check"></i> Fim:</label>
            <input type="date" id="dt_fim" value="<?= $data_fim_default ?>">
        </div>
        <div id="loading"><i class="fa-solid fa-sync fa-spin"></i> <span>Sincronizando</span></div>
    </div>

    <div class="row-cards">
        <div class="card status-aberto" onclick="openDetails('ABERTO')">
            <div class="card-info"><span class="card-title">Aberto</span><h4 id="qtd-aberto">0</h4></div>
            <div class="card-icon" style="color:#696cff"><i class="fa-solid fa-folder-open"></i></div>
        </div>
        <div class="card status-concluido" onclick="openDetails('CONCLUÍDO')">
            <div class="card-info"><span class="card-title">Concluído</span><h4 id="qtd-concluido">0</h4></div>
            <div class="card-icon" style="color:#71dd37"><i class="fa-solid fa-circle-check"></i></div>
        </div>
        <div class="card status-reprovado" onclick="openDetails('REPROVADO')">
            <div class="card-info"><span class="card-title">Reprovado</span><h4 id="qtd-reprovado">0</h4></div>
            <div class="card-icon" style="color:#8592a3"><i class="fa-solid fa-circle-xmark"></i></div>
        </div>
        <div class="card status-pendencia" onclick="openDetails('PENDÊNCIA DO CLIENTE')">
            <div class="card-info"><span class="card-title">Pend. Cliente</span><h4 id="qtd-pendencia">0</h4></div>
            <div class="card-icon" style="color:#e83e8c"><i class="fa-solid fa-clock-rotate-left"></i></div>
        </div>
    </div>

    <div class="row-charts">
        <div class="chart-col"><h5>Distribuição de Status</h5><div class="canvas-container"><canvas id="donutChart"></canvas></div></div>
        <div class="chart-col"><h5>Volumes por Status</h5><div class="canvas-container"><canvas id="barChart"></canvas></div></div>
    </div>

    <div class="row-charts">
        <div class="chart-col chart-full"><h5>Evolução Histórica Anual</h5><div class="canvas-container"><canvas id="yearChart"></canvas></div></div>
    </div>

    <div class="modal-overlay" id="modalOverlay" onclick="closeModal()">
        <div class="modal-content" onclick="event.stopPropagation()">
            <div class="modal-header">
                <h3 id="modalTitle" style="margin:0; font-size: 1.1rem;">Detalhes</h3>
                <span onclick="closeModal()" style="cursor:pointer; font-size:1.8rem; color:#aaa">&times;</span>
            </div>
            <div class="modal-body"><ul id="modal-list"></ul></div>
        </div>
    </div>

    <script>
    const ENDPOINT = '../backend/routers/dash.php';
    const ENDPOINT_DETAILS = '../backend/routers/details.php';
    
    // Parâmetros da Sessão
    const userCadValue  = "<?= $user_usuario ?>";
    const userTipoValue = "<?= $user_tipo ?>"; // ADMIN, SAC, etc
    const codcliValue   = "<?= $codcli_sessao ?>";
    const codusurValue  = "<?= $codusur_sessao ?>";

    const colorsMap = {
        'ABERTO': '#696cff', 'CONCLUÍDO': '#71dd37', 'REPROVADO': '#8592a3',
        'PENDÊNCIA DO CLIENTE': '#e83e8c', 'EM PROCESSAMENTO': '#ffab00',
        'AGUARDANDO APROVAÇÃO': '#ff3e1d', 'EM ANÁLISE': '#17cfd5', 'OUTROS': '#d9dee3'
    };

    let charts = {};

    document.addEventListener('DOMContentLoaded', function() {
        initCharts();
        fetchData();
        document.getElementById('dt_inicio').addEventListener('change', fetchData);
        document.getElementById('dt_fim').addEventListener('change', fetchData);
    });

    async function fetchData() {
        const loading = document.getElementById('loading');
        loading.style.opacity = 1;

        const start = document.getElementById('dt_inicio').value;
        const end   = document.getElementById('dt_fim').value;
        
        // Enviamos o user_tipo para o backend decidir o filtro
        const url = `${ENDPOINT}?dt_inicio=${start}&dt_fim=${end}&user_cad=${userCadValue}&codcli=${codcliValue}&codusur=${codusurValue}&user_tipo=${userTipoValue}`;

        try {
            const response = await fetch(url);
            const json = await response.json();
            if (json.success) updateUI(json);
        } catch (e) { console.error(e); } finally { loading.style.opacity = 0; }
    }

    function updateUI(res) {
        const cardMap = { 'ABERTO': 'qtd-aberto', 'CONCLUÍDO': 'qtd-concluido', 'REPROVADO': 'qtd-reprovado', 'PENDÊNCIA DO CLIENTE': 'qtd-pendencia' };
        Object.values(cardMap).forEach(id => document.getElementById(id).innerText = '0');

        const labels = [], valores = [], bgColors = [];
        res.data.forEach(item => {
            if (cardMap[item.status_agrupado]) document.getElementById(cardMap[item.status_agrupado]).innerText = item.qtd;
            labels.push(item.status_agrupado);
            valores.push(item.qtd);
            bgColors.push(colorsMap[item.status_agrupado] || '#ddd');
        });

        charts.donut.data.labels = labels;
        charts.donut.data.datasets[0].data = valores;
        charts.donut.data.datasets[0].backgroundColor = bgColors;
        charts.donut.update();

        charts.bar.data.labels = labels;
        charts.bar.data.datasets[0].data = valores;
        charts.bar.data.datasets[0].backgroundColor = bgColors;
        charts.bar.update();

        if (res.annual_data) {
            const anosUnicos = [...new Set(res.annual_data.map(d => d.ano))].sort();
            const statusDesejados = ['ABERTO', 'CONCLUÍDO', 'REPROVADO', 'PENDÊNCIA DO CLIENTE'];
            charts.year.data.labels = anosUnicos;
            charts.year.data.datasets = statusDesejados.map(status => ({
                label: status,
                data: anosUnicos.map(ano => {
                    const r = res.annual_data.find(d => d.ano == ano && d.status_agrupado == status);
                    return r ? r.qtd : 0;
                }),
                borderColor: colorsMap[status],
                backgroundColor: colorsMap[status] + '20',
                fill: true, tension: 0.4, pointRadius: 4
            }));
            charts.year.update();
        }
    }

    async function openDetails(status) {
        const modal = document.getElementById('modalOverlay');
        const list = document.getElementById('modal-list');
        const start = document.getElementById('dt_inicio').value;
        const end   = document.getElementById('dt_fim').value;

        document.getElementById('modalTitle').innerText = `Garantias: ${status}`;
        list.innerHTML = '<li style="text-align:center"><i class="fa-solid fa-circle-notch fa-spin"></i> Buscando...</li>';
        modal.style.display = 'flex';

        try {
            const url = `${ENDPOINT_DETAILS}?status=${status}&dt_inicio=${start}&dt_fim=${end}&user_cad=${userCadValue}&codcli=${codcliValue}&codusur=${codusurValue}&user_tipo=${userTipoValue}`;
            const response = await fetch(url);
            const json = await response.json();
            list.innerHTML = '';
            if (json.success && json.data.length > 0) {
                json.data.forEach(item => {
                    const li = document.createElement('li');
                    li.innerHTML = `<div><strong>Protocolo:</strong> ${item.Protocolo}</div><div style="color:#666; font-size: 0.85rem;">Cli: ${item.Codigo_Cliente} | RCA: ${item.RCA}</div>`;
                    list.appendChild(li);
                });
            } else { list.innerHTML = '<li>Nenhum item encontrado.</li>'; }
        } catch (e) { list.innerHTML = '<li>Erro ao carregar.</li>'; }
    }

    function closeModal() { document.getElementById('modalOverlay').style.display = 'none'; }

    function initCharts() {
        const opt = { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'bottom', labels: { usePointStyle: true, boxWidth: 8, font: { size: 11 } } } } };
        charts.donut = new Chart(document.getElementById('donutChart'), { type: 'doughnut', data: { labels: [], datasets: [{ data: [] }] }, options: { ...opt, cutout: '70%' } });
        charts.bar = new Chart(document.getElementById('barChart'), { type: 'bar', data: { labels: [], datasets: [{ label: 'Qtd', data: [], borderRadius: 5 }] }, options: { ...opt, scales: { y: { beginAtZero: true, ticks: { precision: 0 } } } } });
        charts.year = new Chart(document.getElementById('yearChart'), { type: 'line', data: { labels: [], datasets: [] }, options: { ...opt, interaction: { mode: 'index', intersect: false }, scales: { y: { beginAtZero: true, ticks: { precision: 0 } } } } });
    }
    </script>
</body>
</html>

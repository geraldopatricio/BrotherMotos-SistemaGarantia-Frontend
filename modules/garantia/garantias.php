<?php
/**
 * O index.php já faz o session_start() e define as variáveis:
 * $usuario, $tipo, $rca, $cliente, etc.
 * Como este arquivo é um include do index, as variáveis já estão disponíveis.
 */
$usuario_logado_login = $usuario ?? ''; // $_SESSION['user_usuario']
$rca_sessao = $rca ?? '';
$cliente_sessao = $cliente ?? '';
$tipo_usuario_sessao = $tipo ?? '';

// URL base do backend (vinda da configuração global se houver)
$url_api_base = isset($url_backend) ? $url_backend : ''; 
?>

<style>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;700&display=swap');

* {
    box-sizing: border-box;
    font-family: 'Inter', ui-sans-serif, system-ui, sans-serif;
}

h1 {
    color: #333;
    margin-top: 0;
}

.controls {
    display: flex;
    justify-content: space-between;
    margin-bottom: 25px;
    flex-wrap: wrap;
    gap: 15px;
    align-items: center;
}

.search-box {
    flex-grow: 1;
}

.search-box input {
    width: 100%;
    max-width: 400px;
    padding: 12px 18px;
    border: 1px solid #e0e0e0;
    border-radius: 12px;
    font-size: 14px;
    outline: none;
    transition: all 0.3s ease;
    background: #f9f9f9;
}

.search-box input:focus {
    border-color: #675cf8;
    background: #fff;
    box-shadow: 0 0 0 4px rgba(103, 92, 248, 0.1);
}

/* Estilização dos Botões */
.btn-custom {
    padding: 10px 22px;
    border: none;
    border-radius: 30px;
    cursor: pointer;
    font-size: 14px;
    font-weight: 500;
    display: flex;
    align-items: center;
    gap: 8px;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
}

.export-btn {
    background-color: #e8f5e9;
    color: #2e7d32;
}

.export-btn:hover {
    background-color: #2e7d32;
    color: #fff;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(46, 125, 50, 0.2);
}

.novo-btn {
    background-color: #eeecff;
    color: #675cf8;
}

.novo-btn:hover {
    background-color: #675cf8;
    color: #fff;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(103, 92, 248, 0.2);
}

/* Tabela */
table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
    margin-bottom: 20px;
    background: white;
}

th {
    background-color: #fcfcfd;
    color: #666;
    font-weight: 600;
    font-size: 11px;
    text-transform: uppercase;
    letter-spacing: 0.8px;
    padding: 16px 12px;
    border-bottom: 1px solid #eee;
    text-align: left;
}

td {
    padding: 14px 12px;
    font-size: 13.5px;
    color: #444;
    border-bottom: 1px solid #f8f8f8;
}

tr:hover td {
    background-color: #fbfbff;
}

/* Status */
[class^="status-"] {
    font-weight: 600;
    font-size: 11px;
    padding: 6px 14px;
    border-radius: 30px;
    display: inline-block;
    white-space: nowrap;
}

.status-Em-Analise,
.status-Em-Análise {
    color: #f4f8f4;
    background: #27adeb;
}

.status-Em-processamento {
    color: #c1c5c1;
    background: #15921f;
}

.status-Em-Digitacao,
.status-Em-Digitação {
    color: #ecf5ed;
    background: #c2c5c2;
}

.status-Reprovado {
    color: #d32f2f;
    background: #ffebee;
}

.status-Concluido,
.status-Concluído {
    color: #1565c0;
    background: #e3f2fd;
}

.acoes a {
    color: #9e9e9e;
    font-size: 18px;
    transition: 0.2s;
    padding: 6px;
    border-radius: 8px;
}

.acoes a:hover {
    color: #675cf8;
    background: #eeecff;
}

.pagination button {
    padding: 8px 14px;
    border: 1px solid #eee;
    background: #fff;
    cursor: pointer;
    border-radius: 10px;
    margin: 0 3px;
    transition: 0.2s;
    font-size: 13px;
}

.pagination button.active {
    background: #675cf8;
    color: white;
    border-color: #675cf8;
}

.pagination-wrapper {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 15px;
    padding: 20px 0 10px 0;
    border-top: 1px solid #f0f0f0;
    margin-top: 10px;
}
</style>

<div class="container-fluid flex-grow-1 container-p-y">
    <div class="card"
        style="padding: 25px; border: none; border-radius: 16px; box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);">
        <div class="card-datatable table-responsive pt-0">
            <h4 style="margin-bottom: 25px; color: #2c3e50; font-weight: 700;">Garantias Abertas</h4>

            <div class="controls">
                <div class="search-box">
                    <input type="text" id="searchInput" placeholder="Pesquisar por protocolo, cliente ou status...">
                </div>
                <div style="display:flex; gap:12px;">
                    <button class="btn-custom export-btn" id="exportExcel">
                        <i class="fas fa-file-excel"></i> Exportar
                    </button>
                    <button class="btn-custom novo-btn" onclick="window.location.href='index.php?page=garantiaAdd'">
                        <i class="fas fa-plus-circle"></i> Nova Garantia
                    </button>
                </div>
            </div>

            <div id="tableContainer">
                <div class="loading">Carregando dados...</div>
            </div>

            <div class="pagination-wrapper">
                <div class="rows-per-page">
                    <span style="font-size: 13px; color: #888;">Mostrar </span>
                    <select id="itemsPerPageSelect"
                        style="padding: 6px; border-radius: 8px; border: 1px solid #eee; outline: none; color: #666;">
                        <option value="10" selected>10 registros</option>
                        <option value="30">30 registros</option>
                        <option value="50">50 registros</option>
                        <option value="todos">Todos</option>
                    </select>
                </div>
                <div class="pagination" id="pagination"></div>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<script>
// Dados da Sessão injetados pelo PHP (vindos do index.php)
const sessionUserLogin = '<?php echo $usuario_logado_login; ?>';
const sessionRca = '<?php echo $rca_sessao; ?>';
const sessionCliente = '<?php echo $cliente_sessao; ?>';
const userTipo = '<?php echo strtoupper($tipo_usuario_sessao); ?>';
const urlBackend = '<?php echo $url_api_base; ?>';

document.addEventListener('DOMContentLoaded', function() {
    let itemsPerPage = 10;
    let currentPage = 1;
    let allData = [];
    let filteredData = [];
    let currentSortColumn = 'dt_cadastro';
    let currentSortDirection = 'desc';

    function buildGarantiasUrl() {
        const baseUrl = urlBackend + '/routers/garantias.php';
        const params = new URLSearchParams();

        // 1. Filtro para Clientes
        if (userTipo === 'CLIENTE') {
            if (sessionCliente && sessionCliente !== '0') {
                params.append('cliente', sessionCliente);
            }
        }
        // 2. Filtro para RCA, TEL ou TELEVENDAS
        else if (['RCA', 'TEL', 'TELEVENDAS'].includes(userTipo)) {
            if (sessionRca && sessionRca !== '0') {
                params.append('rca', sessionRca);
            }

            // NOVO: Se for TEL ou TELEVENDAS, envia também o login do usuário para filtro OR no backend
            if (['TEL', 'TELEVENDAS'].includes(userTipo) && sessionUserLogin) {
                params.append('user_cad', sessionUserLogin);
            }
        }

        return baseUrl + (params.toString() ? '?' + params.toString() : '');
    }

    async function fetchData() {
        try {
            const url = buildGarantiasUrl();
            const response = await fetch(url);
            const result = await response.json();

            if (result.success && result.data) {
                allData = result.data;
                filteredData = sortData([...allData], currentSortColumn, currentSortDirection);
                renderTable();
                renderPagination();
            } else {
                document.getElementById('tableContainer').innerHTML = "Nenhum dado encontrado.";
            }
        } catch (error) {
            console.error(error);
            document.getElementById('tableContainer').innerHTML = "Erro ao conectar com o servidor.";
        }
    }

    function parseDate(str) {
        if (!str) return 0;
        try {
            const parts = str.split(' ');
            const d = parts[0].split('/');
            let h = 0,
                m = 0,
                s = 0;
            if (parts[1]) {
                const t = parts[1].split(':');
                h = parseInt(t[0]) || 0;
                m = parseInt(t[1]) || 0;
                s = parseInt(t[2]) || 0;
            }
            return new Date(d[2], d[1] - 1, d[0], h, m, s).getTime();
        } catch (e) {
            return 0;
        }
    }

    function sortData(data, column, direction) {
        return data.sort((a, b) => {
            let valA = a[column];
            let valB = b[column];
            if (column === 'dt_cadastro') {
                valA = parseDate(valA);
                valB = parseDate(valB);
            } else if (!isNaN(valA) && valA !== '' && valA !== null && column !== 'protocolo') {
                valA = parseFloat(valA);
                valB = parseFloat(valB);
            } else {
                valA = String(valA || '').toLowerCase();
                valB = String(valB || '').toLowerCase();
            }
            if (valA < valB) return direction === 'asc' ? -1 : 1;
            if (valA > valB) return direction === 'asc' ? 1 : -1;
            return 0;
        });
    }

    function renderTable() {
        const start = (currentPage - 1) * itemsPerPage;
        const pageData = filteredData.slice(start, start + itemsPerPage);
        const getIcon = (col) => {
            if (currentSortColumn !== col) return '<i class="fas fa-sort sort-icon"></i>';
            return currentSortDirection === 'asc' ? '<i class="fas fa-caret-up sort-icon"></i>' :
                '<i class="fas fa-caret-down sort-icon"></i>';
        };

        let html = `<table><thead><tr>
            <th class="sortable ${currentSortColumn==='protocolo'?'active-sort':''}" onclick="handleSort('protocolo')">PROTOCOLO ${getIcon('protocolo')}</th>
            <th class="sortable ${currentSortColumn==='dt_cadastro'?'active-sort':''}" onclick="handleSort('dt_cadastro')">DATA ${getIcon('dt_cadastro')}</th>
            <th class="sortable ${currentSortColumn==='CODCLI'?'active-sort':''}" onclick="handleSort('CODCLI')">CODCLI ${getIcon('CODCLI')}</th>
            <th class="mobile-hide sortable ${currentSortColumn==='CLIENTE'?'active-sort':''}" onclick="handleSort('CLIENTE')">CLIENTE ${getIcon('CLIENTE')}</th>
            <th class="mobile-hide sortable ${currentSortColumn==='codusur'?'active-sort':''}" onclick="handleSort('codusur')">CODRCA ${getIcon('codusur')}</th>
            <th class="mobile-hide sortable ${currentSortColumn==='NOME'?'active-sort':''}" onclick="handleSort('NOME')">REPRESENTANTE ${getIcon('NOME')}</th>
            <th class="sortable ${currentSortColumn==='status'?'active-sort':''}" onclick="handleSort('status')">STATUS ${getIcon('status')}</th>
            <th style="text-align:center">AÇÕES</th>
        </tr></thead><tbody>`;

        if (pageData.length === 0) {
            html +=
                '<tr><td colspan="8" style="text-align:center; padding: 40px; color: #999;">Nenhum registro encontrado.</td></tr>';
        } else {
            pageData.forEach(item => {
                const statusClass = (item.status || 'default').replace(/\s+/g, '-');
                html += `<tr>
                    <td><b>${item.protocolo}</b></td>
                    <td style="font-size:12px">${item.dt_cadastro}</td>
                    <td>${item.CODCLI}</td>
                    <td class="mobile-hide">${item.CLIENTE}</td>
                    <td class="mobile-hide">${item.codusur}</td>
                    <td class="mobile-hide">${item.NOME || '---'}</td>
                    <td><div class="status-${statusClass}">${item.status}</div></td>
                    <td><div class="acoes">
                        <a href="modules/relatorios/rptGarFull.php?id=${item.protocolo}" title="Imprimir" target="_blank"><i class="fas fa-print"></i></a>
                        <a href="index.php?page=garantiaEdit&id=${item.protocolo}" title="Editar"><i class="fas fa-pen-to-square"></i></a>
                    </div></td>
                </tr>`;
            });
        }
        html += '</tbody></table>';
        document.getElementById('tableContainer').innerHTML = html;
    }

    window.handleSort = function(column) {
        if (currentSortColumn === column) {
            currentSortDirection = currentSortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            currentSortColumn = column;
            currentSortDirection = (column === 'dt_cadastro' || column === 'protocolo') ? 'desc' : 'asc';
        }
        filteredData = sortData(filteredData, currentSortColumn, currentSortDirection);
        currentPage = 1;
        renderTable();
        renderPagination();
    };

    function renderPagination() {
        const totalPages = Math.ceil(filteredData.length / itemsPerPage);
        const container = document.getElementById('pagination');
        if (totalPages <= 1) {
            container.innerHTML = '';
            return;
        }
        let html = '';
        for (let i = 1; i <= totalPages; i++) {
            if (i === 1 || i === totalPages || (i >= currentPage - 1 && i <= currentPage + 1)) {
                html +=
                    `<button onclick="goToPage(${i})" class="${i === currentPage ? 'active' : ''}">${i}</button>`;
            } else if (i === currentPage - 2 || i === currentPage + 2) {
                html += `<span>...</span>`;
            }
        }
        container.innerHTML = html;
    }

    window.goToPage = (p) => {
        currentPage = p;
        renderTable();
        renderPagination();
    };

    document.getElementById('searchInput').addEventListener('input', (e) => {
        const val = e.target.value.toLowerCase();
        filteredData = allData.filter(item =>
            Object.values(item).some(v => String(v).toLowerCase().includes(val))
        );
        filteredData = sortData(filteredData, currentSortColumn, currentSortDirection);
        currentPage = 1;
        renderTable();
        renderPagination();
    });

    document.getElementById('itemsPerPageSelect').addEventListener('change', function() {
        itemsPerPage = this.value === 'todos' ? filteredData.length : parseInt(this.value);
        currentPage = 1;
        renderTable();
        renderPagination();
    });

    // Exportação Excel/CSV
    document.getElementById('exportExcel').addEventListener('click', function() {
        if (filteredData.length === 0) return;
        const headers = ["PROTOCOLO", "DATA", "CODCLI", "CLIENTE", "CODRCA", "REPRESENTANTE", "STATUS"];
        const rows = filteredData.map(item => [
            item.protocolo, item.dt_cadastro, item.CODCLI, `"${item.CLIENTE}"`, item.codusur,
            `"${item.NOME || ''}"`, item.status
        ]);
        let csvContent = "\uFEFF" + headers.join(";") + "\r\n";
        rows.forEach(row => {
            csvContent += row.join(";") + "\r\n";
        });
        const blob = new Blob([csvContent], {
            type: 'text/csv;charset=utf-8;'
        });
        const link = document.createElement("a");
        link.setAttribute("href", URL.createObjectURL(blob));
        link.setAttribute("download", "garantias_abertas.csv");
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    });

    fetchData();
});
</script>

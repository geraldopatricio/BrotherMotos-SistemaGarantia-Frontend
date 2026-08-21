<?php
// $envPath = __DIR__ . '/../backend/.env'; 
// if (file_exists($envPath)) {
//     $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
//     foreach ($lines as $line) {
//         if (strpos(trim($line), '#') === 0) continue; // Pula comentários
//         list($name, $value) = explode('=', $line, 2);
//         $_ENV[trim($name)] = trim($value);
//     }
// }

// $url_backend = $_ENV['URL_BACKEND'];
// $url_frontend = $_ENV['URL_FRONTEND'];
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trilha de Auditoria</title>

    <style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;700&display=swap');

    * {
        box-sizing: border-box;
        font-family: 'Inter', 'roboto', sans-serif;
    }

    body {
        background-color: #f5f5f9;
        margin: 0;
        padding: 20px;
    }

    h4 {
        color: #333;
        margin-top: 0;
        font-weight: 600;
    }

    /* --- CONTROLES E FILTROS --- */
    .controls {
        display: flex;
        justify-content: flex-start;
        margin-bottom: 20px;
        flex-wrap: wrap;
        gap: 10px;
        align-items: center;
    }

    .search-box {
        flex: 1;
        min-width: 200px;
    }

    .search-box input {
        width: 100%;
        padding: 10px;
        border: 1px solid #ddd;
        border-radius: 8px;
        font-size: 14px;
    }

    .btn-refresh {
        padding: 10px 15px;
        background-color: rgb(103, 92, 248);
        color: #fff;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        font-size: 14px;
        font-weight: 500;
        display: flex;
        align-items: center;
        gap: 5px;
    }

    .btn-refresh:hover {
        background-color: rgb(33, 74, 136);
    }

    /* --- TABELA --- */
    .card {
        background: #fff;
        border-radius: 8px;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.04);
        padding: 20px;
    }

    table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        margin-bottom: 20px;
        border: 1px solid #e0e0e0;
        border-radius: 8px;
        overflow: hidden;
    }

    th,
    td {
        padding: 10px 12px;
        font-size: 13px;
        text-align: left;
        border-bottom: 1px solid #f0f0f0;
    }

    th {
        background-color: #f8f9fa;
        font-weight: 600;
        color: #555;
        text-transform: uppercase;
        font-size: 12px;
    }

    tr:hover {
        background-color: #f8f9ff;
    }

    /* --- BADGES DE AÇÃO --- */
    .status-badge {
        border-radius: 6px;
        padding: 4px 8px;
        font-size: 10px;
        font-weight: 700;
        display: inline-block;
        text-transform: uppercase;
    }

    .badge-insert {
        background-color: #e8f5e9;
        color: #2e7d32;
    }

    /* Verde */
    .badge-update {
        background-color: #e3f2fd;
        color: #1565c0;
    }

    /* Azul */
    .badge-delete {
        background-color: #ffebee;
        color: #c62828;
    }

    /* Vermelho */
    .badge-default {
        background-color: #f3f4f6;
        color: #4b5563;
    }

    /* Cinza */

    /* --- PAGINAÇÃO E LOADING --- */
    .pagination {
        display: flex;
        justify-content: center;
        gap: 5px;
        margin-top: 20px;
    }

    .pagination button {
        padding: 6px 12px;
        border: 1px solid #ddd;
        background: #fff;
        cursor: pointer;
        border-radius: 6px;
        font-size: 12px;
    }

    .pagination button.active {
        background-color: rgb(103, 92, 248);
        color: white;
        border-color: rgb(103, 92, 248);
    }

    .loading {
        text-align: center;
        padding: 20px;
        color: #777;
        font-size: 14px;
    }

    /* --- BOTÕES DE AÇÃO --- */
    .acoes {
        display: flex;
        gap: 8px;
        justify-content: center;
    }

    .acoes button {
        border: none;
        background: none;
        cursor: pointer;
        transition: transform 0.2s;
    }

    .acoes button:hover {
        transform: scale(1.1);
    }

    .icone-view-bg {
        background-color: #e2e6ea;
        color: #495057;
        padding: 6px;
        border-radius: 6px;
        font-size: 12px;
    }

    /* --- MODAL --- */
    .modal-overlay {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100vw;
        height: 100vh;
        background: rgba(0, 0, 0, 0.6);
        z-index: 9999;
        justify-content: center;
        align-items: center;
        backdrop-filter: blur(2px);
    }

    .modal-content {
        background: #fff;
        padding: 20px;
        border-radius: 12px;
        width: 700px !important;
        max-width: 95% !important;
        height: auto !important;
        max-height: 90vh;
        overflow-y: auto;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
        animation: slideDown 0.3s ease-out;
    }

    @keyframes slideDown {
        from {
            transform: translateY(-20px);
            opacity: 0;
        }

        to {
            transform: translateY(0);
            opacity: 1;
        }
    }

    .modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        padding-bottom: 10px;
        border-bottom: 1px solid #eee;
    }

    .modal-header h4 {
        margin: 0;
        font-size: 18px;
        color: #333;
    }

    .form-group {
        margin-bottom: 12px;
    }

    .form-group label {
        display: block;
        margin-bottom: 4px;
        font-weight: 500;
        font-size: 13px;
        color: #444;
    }

    .form-group input,
    .form-group textarea {
        width: 100%;
        padding: 8px 12px;
        border: 1px solid #ddd;
        border-radius: 6px;
        font-size: 13px;
        background-color: #f9f9f9;
        /* Readonly look */
    }

    textarea {
        resize: vertical;
        min-height: 80px;
        font-family: monospace;
        /* Melhor para ver JSON/Dados */
    }

    .diff-container {
        display: flex;
        gap: 15px;
    }

    .diff-box {
        flex: 1;
    }

    .modal-footer {
        display: flex;
        justify-content: flex-end;
        margin-top: 20px;
        padding-top: 10px;
        border-top: 1px solid #eee;
    }

    .btn-close-modal {
        background: #f1f3f5;
        color: #555;
        border: none;
        padding: 8px 16px;
        border-radius: 6px;
        cursor: pointer;
        font-size: 13px;
        font-weight: 500;
    }

    .btn-close-modal:hover {
        background: #e9ecef;
    }
    </style>
</head>

<body>

    <div class="container-fluid flex-grow-1 container-p-y">
        <div class="card">
            <div class="card-datatable table-responsive pt-0">

                <div>
                    <h4>Trilha de Auditoria do Sistema</h4>
                </div>

                <div class="controls">
                    <!-- Filtro de Usuário -->
                    <div class="search-box">
                        <input type="text" id="searchUser" placeholder="Filtrar por Usuário...">
                    </div>

                    <!-- Filtro de Data -->
                    <div class="search-box" style="flex: 0 0 200px;">
                        <input type="date" id="searchDate" title="Filtrar por data">
                    </div>

                    <!-- Botão Atualizar -->
                    <div>
                        <button class="btn-refresh" id="btnRefresh" onclick="location.reload()">
                            <i class="fas fa-sync-alt"></i> Atualizar
                        </button>
                    </div>
                </div>

                <div id="tableContainer">
                    <div class="loading">Carregando registros...</div>
                </div>

                <div class="pagination" id="pagination"></div>

                <div style="margin-top: 10px; font-size: 11px; color: #888; text-align: right;">
                    * Exibindo os últimos 1000 registros por segurança.
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL DE DETALHES (READ ONLY) -->
    <div class="modal-overlay" id="detailsModal">
        <div class="modal-content">
            <div class="modal-header">
                <h4>Detalhes da Auditoria <span id="viewId"
                        style="font-weight:normal; font-size:14px; color:#777;"></span></h4>
                <button type="button" style="border:none; background:none; font-size:20px; cursor:pointer;"
                    onclick="closeModal()">&times;</button>
            </div>

            <form id="viewForm">
                <div class="row" style="display:flex; gap:10px;">
                    <div class="form-group" style="flex:1;">
                        <label>Data/Hora</label>
                        <input type="text" id="viewDate" readonly>
                    </div>
                    <div class="form-group" style="flex:1;">
                        <label>Usuário</label>
                        <input type="text" id="viewUser" readonly>
                    </div>
                </div>

                <div class="row" style="display:flex; gap:10px;">
                    <div class="form-group" style="flex:1;">
                        <label>Script / Origem</label>
                        <input type="text" id="viewScript" readonly>
                    </div>
                    <div class="form-group" style="flex:1;">
                        <label>Ação</label>
                        <input type="text" id="viewAction" readonly>
                    </div>
                </div>

                <div class="row" style="display:flex; gap:10px;">
                    <div class="form-group" style="flex:1;">
                        <label>Tabela Afetada</label>
                        <input type="text" id="viewTable" readonly>
                    </div>
                    <div class="form-group" style="flex:1;">
                        <label>Campo / Chave Primária</label>
                        <input type="text" id="viewField" readonly>
                    </div>
                </div>

                <div class="diff-container">
                    <div class="form-group diff-box">
                        <label style="color: #c62828;">Valor Antigo (Old)</label>
                        <textarea id="viewOld" readonly></textarea>
                    </div>
                    <div class="form-group diff-box">
                        <label style="color: #2e7d32;">Novo Valor (New)</label>
                        <textarea id="viewNew" readonly></textarea>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn-close-modal" onclick="closeModal()">Fechar</button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <script>
    // CONFIGURAÇÃO DA API
    // Substitua pelo caminho real do arquivo audittrail.php que você criou
    const API_URL = '<?php echo $url_backend; ?>/routers/audittrail.php';

    document.addEventListener('DOMContentLoaded', function() {
        const itemsPerPage = 10; // Mostrar mais itens por página na auditoria
        let currentPage = 1;
        let allData = [];
        let filteredData = [];

        // --- FUNÇÕES DE DADOS ---

        async function fetchData() {
            try {
                // Chama API com limit=1000 por padrão para não travar
                const response = await fetch(`${API_URL}?limit=1000`);
                const result = await response.json();

                if (result.success && result.data) {
                    allData = result.data;
                    filteredData = [...allData];
                    renderTable();
                    renderPagination();
                } else {
                    document.getElementById('tableContainer').innerHTML =
                        '<div class="loading">Nenhum registro encontrado ou erro na API.</div>';
                }
            } catch (error) {
                console.error(error);
                document.getElementById('tableContainer').innerHTML =
                    '<div class="loading">Erro de conexão com API.</div>';
            }
        }

        // --- FUNÇÕES DE RENDERIZAÇÃO ---

        function renderTable() {
            const startIndex = (currentPage - 1) * itemsPerPage;
            const endIndex = startIndex + itemsPerPage;
            const pageData = filteredData.slice(startIndex, endIndex);

            if (pageData.length === 0) {
                document.getElementById('tableContainer').innerHTML =
                    '<div class="loading">Nenhum registro encontrado com os filtros atuais.</div>';
                return;
            }

            let tableHTML = `
            <table>
                <thead>
                    <tr>
                        <th width="5%">ID</th>
                        <th width="15%">DATA/HORA</th>
                        <th width="15%">USUÁRIO</th>
                        <th width="10%">AÇÃO</th>
                        <th width="15%">TABELA</th>
                        <th width="15%">SCRIPT</th>
                        <th width="15%">CAMPO/REF</th>
                        <th width="10%" style="text-align:center;">DETALHES</th>
                    </tr>
                </thead>
                <tbody>
        `;

            pageData.forEach(item => {
                // Definição de cores das Badges
                let badgeClass = 'badge-default';
                const actionUpper = (item.Action || '').toUpperCase();

                if (actionUpper.includes('INSERT')) badgeClass = 'badge-insert';
                else if (actionUpper.includes('UPDATE')) badgeClass = 'badge-update';
                else if (actionUpper.includes('DELETE')) badgeClass = 'badge-delete';

                // Formatação de valores nulos
                const scriptName = item.Script ? item.Script : '-';
                const tableName = item.Table ? item.Table : '-';
                const fieldInfo = item.Field === 'ALL' ? 'Registro Completo' : (item.Field || item
                    .KeyValue || '-');

                tableHTML += `
                <tr>
                    <td>${item.Id}</td>
                    <td>${item.DataHora}</td>
                    <td><b>${item.User || 'Sistema'}</b></td>
                    <td><span class="status-badge ${badgeClass}">${item.Action || 'UNK'}</span></td>
                    <td>${tableName}</td>
                    <td style="font-size:12px; color:#666;">${scriptName}</td>
                    <td style="font-size:12px;">${fieldInfo}</td>
                    <td>
                        <div class="acoes">
                            <button class="btn-view" onclick="viewDetails(${item.Id})" title="Ver Detalhes">
                                <i class="fas fa-eye icone-view-bg"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            `;
            });

            tableHTML += `</tbody></table>`;
            document.getElementById('tableContainer').innerHTML = tableHTML;
        }

        function renderPagination() {
            const totalPages = Math.ceil(filteredData.length / itemsPerPage);
            const container = document.getElementById('pagination');

            if (totalPages <= 1) {
                container.innerHTML = '';
                return;
            }

            let html = '';
            // Botão anterior
            html +=
                `<button onclick="changePage(${currentPage - 1})" ${currentPage === 1 ? 'disabled' : ''}>&laquo;</button>`;

            // Lógica para não mostrar botões demais se houver muitas páginas
            let startPage = Math.max(1, currentPage - 2);
            let endPage = Math.min(totalPages, currentPage + 2);

            if (startPage > 1) html += `<button onclick="changePage(1)">1</button><span>...</span>`;

            for (let i = startPage; i <= endPage; i++) {
                html +=
                    `<button onclick="changePage(${i})" class="${i === currentPage ? 'active' : ''}">${i}</button>`;
            }

            if (endPage < totalPages) html +=
                `<span>...</span><button onclick="changePage(${totalPages})">${totalPages}</button>`;

            // Botão próximo
            html +=
                `<button onclick="changePage(${currentPage + 1})" ${currentPage === totalPages ? 'disabled' : ''}>&raquo;</button>`;

            container.innerHTML = html;
        }

        window.changePage = function(page) {
            const totalPages = Math.ceil(filteredData.length / itemsPerPage);
            if (page < 1 || page > totalPages) return;
            currentPage = page;
            renderTable();
            renderPagination();
        };

        // --- LÓGICA DE FILTRAGEM ---

        function applyFilters() {
            const userTerm = document.getElementById('searchUser').value.toLowerCase();
            const dateTerm = document.getElementById('searchDate').value; // Formato yyyy-mm-dd

            // Converter a data do input (yyyy-mm-dd) para o formato de exibição da tabela (dd/mm/yyyy) para busca simples
            let formattedDateSearch = '';
            if (dateTerm) {
                const [y, m, d] = dateTerm.split('-');
                formattedDateSearch = `${d}/${m}/${y}`;
            }

            filteredData = allData.filter(item => {
                // Filtro de Usuário (busca parcial)
                const matchUser = (item.User || '').toLowerCase().includes(userTerm);

                // Filtro de Data (busca se a string DataHora contém a data selecionada)
                const matchDate = dateTerm ? (item.DataHora || '').includes(formattedDateSearch) : true;

                return matchUser && matchDate;
            });

            currentPage = 1;
            renderTable();
            renderPagination();
        }

        // Event Listeners para os filtros
        document.getElementById('searchUser').addEventListener('input', applyFilters);
        document.getElementById('searchDate').addEventListener('change', applyFilters);

        // --- MODAL DETAILS ---

        const modal = document.getElementById('detailsModal');

        window.viewDetails = function(id) {
            const item = allData.find(i => i.Id == id);
            if (!item) return;

            // Preenche os campos
            document.getElementById('viewId').innerText = '#' + item.Id;
            document.getElementById('viewDate').value = item.DataHora;
            document.getElementById('viewUser').value = item.User;
            document.getElementById('viewScript').value = item.Script;
            document.getElementById('viewAction').value = item.Action;
            document.getElementById('viewTable').value = item.Table;
            document.getElementById('viewField').value = item.Field + ' (Key: ' + (item.KeyValue || 'N/A') +
                ')';

            // Preenche textareas de diff
            document.getElementById('viewOld').value = item.OldValue || 'NULL/Vazio';
            document.getElementById('viewNew').value = item.NewValue || 'NULL/Vazio';

            modal.style.display = 'flex';
        };

        window.closeModal = function() {
            modal.style.display = 'none';
        };

        // Fecha modal clicando fora
        window.onclick = function(event) {
            if (event.target == modal) {
                closeModal();
            }
        }

        // Inicializa
        fetchData();
    });
    </script>

</body>

</html>
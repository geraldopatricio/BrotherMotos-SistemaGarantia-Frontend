<?php

$envPath = realpath(__DIR__ . '/../../../backend/.env');

if (!$envPath || !file_exists($envPath)) {
    die('Arquivo .env não encontrado');
}

$linhas = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

foreach ($linhas as $linha) {
    if (str_starts_with(trim($linha), '#')) {
        continue;
    }

    [$chave, $valor] = explode('=', $linha, 2);
    $_ENV[trim($chave)] = trim($valor);
}

$url_backend  = $_ENV['URL_BACKEND']  ?? 'http://192.168.0.38:9091/backend';
$url_frontend = $_ENV['URL_FRONTEND'] ?? 'http://192.168.0.38:9091/frontend';

?>

<style>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;700&display=swap');

* {
    box-sizing: border-box;
    font-family: 'Inter', 'roboto', sans-serif;
    /* Ajuste para fonte mais limpa */
}

/* --- ESTILOS DA PÁGINA (MANTIDOS) --- */
h4 {
    color: #333;
    margin-top: 0;
    font-weight: 600;
}

.controls {
    display: flex;
    justify-content: space-between;
    margin-bottom: 20px;
    flex-wrap: wrap;
    gap: 10px;
}

.search-box {
    flex-grow: 1;
}

.search-box input {
    width: 300px;
    /* Largura fixa para pesquisa para não esticar demais */
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 8px;
    font-size: 14px;
}

.novo-btn {
    padding: 10px 15px;
    background-color: rgb(103, 92, 248);
    color: #fff;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    font-size: 14px;
    font-weight: 500;
}

.novo-btn:hover {
    background-color: rgb(33, 74, 136);
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

/* STATUS BADGES */
.status-badge {
    border-radius: 6px;
    padding: 4px 10px;
    font-size: 11px;
    font-weight: 600;
    display: inline-block;
}

.status-1 {
    color: #2e7d32;
    background-color: #e8f5e9;
}

.status-0 {
    color: #c62828;
    background-color: #ffebee;
}

/* PAGINAÇÃO E LOADING */
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

/* AÇÕES */
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

.icone-edit-bg {
    background-color: #e2e6ea;
    color: #0d6efd;
    padding: 6px;
    border-radius: 6px;
    font-size: 12px;
}

.icone-delete-bg {
    background-color: #ffebee;
    color: #dc3545;
    padding: 6px;
    border-radius: 6px;
    font-size: 12px;
}


/* --- CORREÇÃO DO MODAL (CENTRALIZADO E MENOR) --- */

.modal-overlay {
    display: none;
    /* Controlado via JS */
    position: fixed;
    /* Fixado na janela, ignora scroll */
    top: 0;
    left: 0;
    width: 100vw;
    /* Largura total da viewport */
    height: 100vh;
    /* Altura total da viewport */
    background: rgba(0, 0, 0, 0.6);
    /* Fundo escuro transparente */
    z-index: 9999;
    /* ACIMA DE TUDO (Sidebar geralmente é 1000-2000) */
    justify-content: center;
    /* Centraliza horizontalmente */
    align-items: center;
    /* Centraliza verticalmente */
    backdrop-filter: blur(2px);
    /* Desfoque suave no fundo */
}

/* --- MODAL (Janela Flutuante) --- */

.modal-overlay {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100vw;
    height: 100vh;
    background: rgba(0, 0, 0, 0.6);
    z-index: 9999;
    /* Garante que fique acima de tudo */
    justify-content: center;
    align-items: center;
    backdrop-filter: blur(2px);
}

.modal-content {
    background: #fff;

    /* 1. Reduza o padding interno (antes estava 20px ou 25px) */
    padding: 15px;

    border-radius: 12px;

    /* Largura mantida (ou ajuste conforme sua preferência) */
    width: 600px !important;
    max-width: 90% !important;

    /* 2. MUDE AQUI: Deixe a altura automática para se ajustar ao conteúdo */
    height: auto !important;

    /* Opcional: Garante que não ultrapasse a tela se o monitor for pequeno */
    max-height: 90vh;
    overflow-y: auto;
    /* Cria barra de rolagem se necessário */

    margin: 0 auto !important;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
    position: relative;
    animation: slideDown 0.3s ease-out;
    display: block;
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

/* Ajustes internos para ficar mais compacto */
.form-group {
    margin-bottom: 10px;
}

.form-group input,
.form-group select {
    padding: 6px 10px;
    font-size: 13px;
}

.modal-footer {
    margin-top: 15px;
    padding-top: 10px;
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
    color: rgba(243, 242, 247, 1);
}

.close-btn {
    background: none;
    border: none;
    font-size: 24px;
    color: #999;
    cursor: pointer;
}

.close-btn:hover {
    color: #333;
}

/* Estilização dos inputs do formulário */
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
.form-group select {
    width: 100%;
    padding: 8px 12px;
    border: 1px solid #ddd;
    border-radius: 6px;
    font-size: 13px;
    transition: border 0.3s;
}

.form-group input:focus,
.form-group select:focus {
    border-color: rgb(103, 92, 248);
    outline: none;
}

/* Footer do Modal */
.modal-footer {
    display: flex;
    justify-content: flex-end;
    gap: 10px;
    margin-top: 20px;
    padding-top: 10px;
    border-top: 1px solid #eee;
}

.btn-cancel {
    background: #f1f3f5;
    color: #555;
    border: none;
    padding: 8px 16px;
    border-radius: 6px;
    cursor: pointer;
    font-size: 13px;
    font-weight: 500;
}

.btn-cancel:hover {
    background: #e9ecef;
}

.btn-save {
    background: rgb(103, 92, 248);
    color: white;
    border: none;
    padding: 8px 16px;
    border-radius: 6px;
    cursor: pointer;
    font-size: 13px;
    font-weight: 500;
}

.btn-save:hover {
    background: rgb(83, 72, 228);
}

.icone-preview {
    font-size: 18px;
    color: #666;
    margin-left: 8px;
}
</style>

<div class="container-fluid flex-grow-1 container-p-y">
    <div class="card" style="padding: 20px;">
        <div class="card-datatable table-responsive pt-0">

            <div>
                <h4>Gerenciamento de Menus do Sistema</h4>
            </div>

            <div class="controls">
                <div class="search-box">
                    <input type="text" id="searchInput" placeholder="Pesquisar menu...">
                </div>
                <div>
                    <!-- Botão abre Modal em vez de redirecionar -->
                    <button class="novo-btn" id="btnNovo">
                        <i class="fas fa-plus"></i> Novo Item
                    </button>
                </div>
            </div>

            <div id="tableContainer">
                <div class="loading">Carregando dados...</div>
            </div>

            <div class="pagination" id="pagination"></div>
        </div>
    </div>
</div>

<!-- MODAL DE CADASTRO/EDIÇÃO -->
<div class="modal-overlay" id="menuModal">
    <div class="modal-content">
        <div class="modal-header">
            <h4 id="modalTitle">Novo Item de Menu</h4>
            <button type="button" style="border:none; background:none; font-size:20px; cursor:pointer;"
                onclick="closeModal()">&times;</button>
        </div>
        <form id="menuForm">
            <input type="hidden" id="menuId">

            <div class="form-group">
                <label>Título</label>
                <input type="text" id="title" required placeholder="Ex: Financeiro">
            </div>

            <div class="row" style="display:flex; gap:10px;">
                <div class="form-group" style="flex:1;">
                    <label>Tipo</label>
                    <select id="type" onchange="toggleFields()">
                        <option value="link">Link Simples</option>
                        <option value="dropdown">Menu Pai (Dropdown)</option>
                        <option value="header">Separador (Header)</option>
                    </select>
                </div>
                <div class="form-group" style="flex:1;">
                    <label>Menu Pai</label>
                    <select id="parent_id">
                        <option value="0">Nenhum (Raiz)</option>
                        <!-- Preenchido via JS -->
                    </select>
                </div>
            </div>

            <div class="form-group" id="groupLink">
                <label>Link / URL</label>
                <input type="text" id="link" placeholder="Ex: index.php?page=financeiro">
            </div>

            <div class="form-group" id="groupPage">
                <label>ID da Página (Para active class)</label>
                <input type="text" id="page_name" placeholder="Ex: financeiro">
            </div>

            <div class="form-group">
                <label>Ícone (Classe CSS)</label>
                <div style="display:flex; align-items:center;">
                    <input type="text" id="icon" placeholder="Ex: ti tabler-home" oninput="updateIconPreview()">
                    <i id="iconPreview" class="icone-preview"></i>
                </div>
            </div>

            <div class="row" style="display:flex; gap:10px;">
                <div class="form-group" style="flex:1;">
                    <label>Posição (Ordem)</label>
                    <input type="number" id="position" value="0">
                </div>
                <div class="form-group" style="flex:1;">
                    <label>Status</label>
                    <select id="status">
                        <option value="1">Ativo</option>
                        <option value="0">Inativo</option>
                    </select>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn-cancel" onclick="closeModal()">Cancelar</button>
                <button type="submit" class="btn-save">Salvar</button>
            </div>
        </form>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<!-- Inclua também a biblioteca de ícones que você usa no sidebar (ex: Tabler Icons) se não for FontAwesome -->

<script>
// CONFIGURAÇÃO DA API
const API_URL = '<?php echo $url_backend; ?>/routers/menu_geral.php';

document.addEventListener('DOMContentLoaded', function() {
    const itemsPerPage = 10;
    let currentPage = 1;
    let allData = [];
    let filteredData = [];

    // --- FUNÇÕES DE DADOS ---

    async function fetchData() {
        try {
            const response = await fetch(API_URL);
            const result = await response.json();

            if (result.success && result.data) {
                allData = result.data;
                filteredData = [...allData];
                renderTable();
                renderPagination();
            } else {
                document.getElementById('tableContainer').innerHTML =
                    '<div class="loading">Erro ao carregar dados.</div>';
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
                '<div class="loading">Nenhum menu encontrado</div>';
            return;
        }

        let tableHTML = `
            <table>
                <thead>
                    <tr>
                        <th width="5%">ID</th>
                        <th width="20%">TÍTULO</th>
                        <th width="10%">TIPO</th>
                        <th width="15%">MENU PAI</th>
                        <th width="20%">LINK</th>
                        <th width="5%" style="text-align:center;">ÍCONE</th>
                        <th width="5%" style="text-align:center;">POS.</th>
                        <th width="10%" style="text-align:center;">STATUS</th>
                        <th width="10%" style="text-align:center;">AÇÕES</th>
                    </tr>
                </thead>
                <tbody>
        `;

        pageData.forEach(item => {
            const statusClass = item.status == 1 ? 'status-1' : 'status-0';
            const statusText = item.status == 1 ? 'Ativo' : 'Inativo';
            const parentName = item.parent_name ? item.parent_name : '-';
            const iconClass = item.icon ? item.icon : '';

            // Tratamento visual para tipos
            let typeLabel = item.type;
            if (item.type === 'header') typeLabel = '<b>SEPARADOR</b>';
            if (item.type === 'dropdown') typeLabel = 'Dropdown';

            tableHTML += `
                <tr>
                    <td>${item.id}</td>
                    <td><b>${item.title}</b></td>
                    <td>${typeLabel}</td>
                    <td>${parentName}</td>
                    <td style="font-size:12px; color:#666;">${item.link || ''}</td>
                    <td style="text-align:center;"><i class="${iconClass}" title="${iconClass}"></i></td>
                    <td style="text-align:center;">${item.position}</td>
                    <td style="text-align:center;">
                        <span class="status-badge ${statusClass}">${statusText}</span>
                    </td>
                    <td>
                        <div class="acoes">
                            <button class="btn-edit" onclick="editItem(${item.id})" title="Editar">
                                <i class="fas fa-edit icone-edit-bg"></i>
                            </button>
                            <button class="btn-delete" onclick="deleteItem(${item.id})" title="Excluir">
                                <i class="fas fa-trash icone-delete-bg"></i>
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
        html +=
            `<button onclick="changePage(${currentPage - 1})" ${currentPage === 1 ? 'disabled' : ''}>&laquo;</button>`;

        for (let i = 1; i <= totalPages; i++) {
            html +=
                `<button onclick="changePage(${i})" class="${i === currentPage ? 'active' : ''}">${i}</button>`;
        }

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

    // --- FILTRO ---
    document.getElementById('searchInput').addEventListener('input', (e) => {
        const term = e.target.value.toLowerCase();
        filteredData = allData.filter(item =>
            item.title.toLowerCase().includes(term) ||
            (item.link && item.link.toLowerCase().includes(term))
        );
        currentPage = 1;
        renderTable();
        renderPagination();
    });

    // --- MODAL & CRUD ---

    const modal = document.getElementById('menuModal');
    const form = document.getElementById('menuForm');

    // Popular o Select de Menu Pai
    function populateParents(currentId = null) {
        const select = document.getElementById('parent_id');
        select.innerHTML = '<option value="0">Nenhum (Raiz)</option>';

        // Filtra apenas itens que podem ser pais (headers não costumam ser, mas dropdowns sim)
        // E evita que o item seja pai dele mesmo
        const potentialParents = allData.filter(i => i.type === 'dropdown' && i.id != currentId);

        potentialParents.forEach(p => {
            select.innerHTML += `<option value="${p.id}">${p.title}</option>`;
        });
    }

    window.toggleFields = function() {
        const type = document.getElementById('type').value;
        const groupLink = document.getElementById('groupLink');
        const groupPage = document.getElementById('groupPage');

        if (type === 'header') {
            groupLink.style.display = 'none';
            groupPage.style.display = 'none';
        } else if (type === 'dropdown') {
            groupLink.style.display = 'none'; // Dropdown pai geralmente não tem link direto, só toggle
            groupPage.style.display = 'none';
        } else {
            groupLink.style.display = 'block';
            groupPage.style.display = 'block';
        }
    }

    window.updateIconPreview = function() {
        const iconClass = document.getElementById('icon').value;
        document.getElementById('iconPreview').className = 'icone-preview ' + iconClass;
    }

    document.getElementById('btnNovo').addEventListener('click', () => {
        document.getElementById('modalTitle').innerText = 'Novo Item';
        form.reset();
        document.getElementById('menuId').value = '';
        populateParents();
        toggleFields();
        updateIconPreview();
        modal.style.display = 'flex';
    });

    window.editItem = function(id) {
        const item = allData.find(i => i.id == id);
        if (!item) return;

        document.getElementById('modalTitle').innerText = 'Editar Item';
        document.getElementById('menuId').value = item.id;
        document.getElementById('title').value = item.title;
        document.getElementById('type').value = item.type;
        document.getElementById('link').value = item.link;
        document.getElementById('page_name').value = item.page_name;
        document.getElementById('icon').value = item.icon;
        document.getElementById('position').value = item.position;
        document.getElementById('status').value = item.status;

        populateParents(id); // Popula pais excluindo o próprio ID
        document.getElementById('parent_id').value = item.parent_id || 0;

        toggleFields();
        updateIconPreview();
        modal.style.display = 'flex';
    };

    window.deleteItem = async function(id) {
        if (!confirm(
                'Tem certeza que deseja excluir este menu? Se for um pai, os filhos também serão excluídos.'
            )) return;

        try {
            const res = await fetch(`${API_URL}?id=${id}`, {
                method: 'DELETE'
            });
            const json = await res.json();
            if (json.success) {
                alert('Item excluído!');
                fetchData();
            } else {
                alert('Erro: ' + json.message);
            }
        } catch (e) {
            alert('Erro de conexão');
        }
    };

    window.closeModal = function() {
        modal.style.display = 'none';
    };

    // Submissão do Formulário (CREATE / UPDATE)
    form.addEventListener('submit', async (e) => {
        e.preventDefault();

        const id = document.getElementById('menuId').value;
        const method = id ? 'PUT' : 'POST';

        const payload = {
            id: id ? id : null,
            title: document.getElementById('title').value,
            type: document.getElementById('type').value,
            parent_id: document.getElementById('parent_id').value,
            link: document.getElementById('link').value,
            page_name: document.getElementById('page_name').value,
            icon: document.getElementById('icon').value,
            position: document.getElementById('position').value,
            status: document.getElementById('status').value
        };

        try {
            const res = await fetch(API_URL, {
                method: method,
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(payload)
            });

            const json = await res.json();

            if (json.success) {
                // alert('Salvo com sucesso!'); // Opcional
                closeModal();
                fetchData();
            } else {
                alert('Erro ao salvar: ' + json.message);
            }
        } catch (error) {
            console.error(error);
            alert('Erro ao comunicar com servidor.');
        }
    });

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
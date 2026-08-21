<?php
$rca = htmlspecialchars($_SESSION['user_rca']);
$cliente = htmlspecialchars($_SESSION['user_cliente']);
?>

<style>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;700&display=swap');

* {
    box-sizing: border-box;
    font-family: 'Inter', 'roboto', sans-serif;
}

/* --- ESTILOS GERAIS --- */
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

.status-sim {
    color: #2e7d32;
    background-color: #e8f5e9;
}

/* Ativo */
.status-nao {
    color: #c62828;
    background-color: #ffebee;
}

/* Inativo */

/* TIPO BADGES */
.badge-tipo {
    padding: 2px 8px;
    border-radius: 4px;
    font-size: 11px;
    background: #eee;
    color: #555;
    text-transform: uppercase;
    font-weight: bold;
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

/* Form inputs */
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

.help-text {
    font-size: 11px;
    color: #888;
    margin-top: 2px;
}

/* Footer */
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
</style>

<div class="container-fluid flex-grow-1 container-p-y">
    <div class="card" style="padding: 20px;">
        <div class="card-datatable table-responsive pt-0">

            <div>
                <h4>Gerenciamento de Usuários</h4>
            </div>

            <div class="controls">
                <div class="search-box">
                    <input type="text" id="searchInput" placeholder="Pesquisar usuário, email ou código...">
                </div>
                <div>
                    <button class="novo-btn" id="btnNovo">
                        <i class="fas fa-plus"></i> Novo Usuário
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
<div class="modal-overlay" id="userModal">
    <div class="modal-content">
        <div class="modal-header">
            <h4 id="modalTitle">Novo Usuário</h4>
            <button type="button" class="close-btn" onclick="closeModal()">&times;</button>
        </div>
        <form id="userForm">
            <input type="hidden" id="userId">

            <div class="row" style="display:flex; gap:10px;">
                <div class="form-group" style="flex:1;">
                    <label>Usuário (Login)</label>
                    <input type="text" id="usuario" required placeholder="Ex: joao.silva">
                </div>
                <div class="form-group" style="flex:1;">
                    <label>Tipo de Acesso</label>
                    <select id="tipo">
                        <option value="ADMIN" disabled>Administrador</option>
                        <option value="SAC">SAC</option>
                        <option value="RCA">Vendedor (RCA)</option>
                        <option value="Cliente">Cliente</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label>Senha</label>
                <input type="password" id="senha" placeholder="******">
                <div class="help-text" id="senhaHelp">Obrigatório no cadastro.</div>
            </div>

            <div class="row" style="display:flex; gap:10px;">
                <div class="form-group" style="flex:1;">
                    <label>Email</label>
                    <input type="email" id="email" placeholder="email@exemplo.com">
                </div>
                <div class="form-group" style="flex:1;">
                    <label>WhatsApp</label>
                    <input type="text" id="zap" placeholder="(00) 00000-0000">
                </div>
            </div>

            <div class="row" style="display:flex; gap:10px;">
                <div class="form-group" style="flex:1;">
                    <label>Cód. RCA (WinThor)</label>
                    <input type="number" id="codusur" placeholder="Opcional">
                </div>
                <div class="form-group" style="flex:1;">
                    <label>Cód. Cliente (WinThor)</label>
                    <input type="number" id="codcli" placeholder="Opcional">
                </div>
            </div>

            <div class="form-group">
                <label>Status</label>
                <select id="ativo">
                    <option value="SIM">Ativo</option>
                    <option value="NAO">Inativo</option>
                </select>
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

<script>
// Ajuste o caminho para onde salvou o api/usuarios.php
const API_URL = '<?php echo $url_backend; ?>/routers/usuarios.php';

document.addEventListener('DOMContentLoaded', function() {
    const itemsPerPage = 8;
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

    // --- RENDERIZAÇÃO ---
    function renderTable() {
        const startIndex = (currentPage - 1) * itemsPerPage;
        const endIndex = startIndex + itemsPerPage;
        const pageData = filteredData.slice(startIndex, endIndex);

        if (pageData.length === 0) {
            document.getElementById('tableContainer').innerHTML =
                '<div class="loading">Nenhum usuário encontrado</div>';
            return;
        }

        let tableHTML = `
            <table>
                <thead>
                    <tr>
                        <th width="5%">ID</th>
                        <th width="15%">USUÁRIO</th>
                        <th width="20%">EMAIL</th>
                        <th width="10%">TIPO</th>
                        <th width="25%">VINCULO (RCA / CLI)</th>
                        <th width="10%" style="text-align:center;">STATUS</th>
                        <th width="15%" style="text-align:center;">AÇÕES</th>
                    </tr>
                </thead>
                <tbody>
        `;

        pageData.forEach(item => {
            const statusClass = item.ativo === 'SIM' ? 'status-sim' : 'status-nao';
            const statusText = item.ativo === 'SIM' ? 'Ativo' : 'Inativo';

            // Lógica para mostrar o vínculo vindo do backend (Subqueries do Winthor)
            let vinculo = '-';
            if (item.codusur) vinculo = `RCA: ${item.codusur} - ${item.nome_rca}`;
            else if (item.codcli) vinculo = `CLI: ${item.codcli} - ${item.nome_cliente}`;

            tableHTML += `
                <tr>
                    <td>${item.id}</td>
                    <td><b>${item.usuario}</b></td>
                    <td style="color:#666;">${item.email || '-'}</td>
                    <td><span class="badge-tipo">${item.tipo}</span></td>
                    <td style="font-size:12px;">${vinculo}</td>
                    <td style="text-align:center;">
                        <span class="status-badge ${statusClass}">${statusText}</span>
                    </td>
                    <td>
                        <div class="acoes">
                            <button onclick="editItem(${item.id})" title="Editar">
                                <i class="fas fa-edit icone-edit-bg"></i>
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

        // 1. Botão ANTERIOR
        html +=
            `<button onclick="changePage(${currentPage - 1})" ${currentPage === 1 ? 'disabled' : ''}>&laquo;</button>`;

        // CONFIGURAÇÃO DA JANELA DE PAGINAÇÃO
        const maxVisibleButtons = 6; // Quantos botões numéricos aparecem no meio
        let startPage = 1;
        let endPage = totalPages;

        if (totalPages > maxVisibleButtons) {
            // Calcula o início e fim para centralizar a página atual
            const half = Math.floor(maxVisibleButtons / 2);
            startPage = Math.max(1, currentPage - half);
            endPage = startPage + maxVisibleButtons - 1;

            // Se o fim ultrapassar o total, ajusta para trás
            if (endPage > totalPages) {
                endPage = totalPages;
                startPage = Math.max(1, endPage - maxVisibleButtons + 1);
            }
        }

        // 2. Botão da PRIMEIRA página e "..." (se necessário)
        if (startPage > 1) {
            html += `<button onclick="changePage(1)">1</button>`;
            if (startPage > 2) {
                html += `<span style="padding: 6px 4px; color: #888;">...</span>`;
            }
        }

        // 3. Loop das páginas CENTRAIS (Janela deslizante)
        for (let i = startPage; i <= endPage; i++) {
            html +=
                `<button onclick="changePage(${i})" class="${i === currentPage ? 'active' : ''}">${i}</button>`;
        }

        // 4. Botão da ÚLTIMA página e "..." (se necessário)
        if (endPage < totalPages) {
            if (endPage < totalPages - 1) {
                html += `<span style="padding: 6px 4px; color: #888;">...</span>`;
            }
            html += `<button onclick="changePage(${totalPages})">${totalPages}</button>`;
        }

        // 5. Botão PRÓXIMO
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
            item.usuario.toLowerCase().includes(term) ||
            (item.email && item.email.toLowerCase().includes(term)) ||
            (item.codusur && String(item.codusur).includes(term)) ||
            (item.codcli && String(item.codcli).includes(term))
        );
        currentPage = 1;
        renderTable();
        renderPagination();
    });

    // --- MODAL & CRUD ---
    const modal = document.getElementById('userModal');
    const form = document.getElementById('userForm');

    document.getElementById('btnNovo').addEventListener('click', () => {
        document.getElementById('modalTitle').innerText = 'Novo Usuário';
        form.reset();
        document.getElementById('userId').value = '';
        document.getElementById('senhaHelp').innerText = 'Obrigatório no cadastro.';
        modal.style.display = 'flex';
    });

    window.editItem = function(id) {
        const item = allData.find(i => i.id == id);
        if (!item) return;

        document.getElementById('modalTitle').innerText = 'Editar Usuário';
        document.getElementById('userId').value = item.id;
        document.getElementById('usuario').value = item.usuario;
        document.getElementById('tipo').value = item.tipo;
        document.getElementById('email').value = item.email;
        document.getElementById('zap').value = item.zap;
        document.getElementById('codusur').value = item.codusur;
        document.getElementById('codcli').value = item.codcli;
        document.getElementById('ativo').value = item.ativo;

        // Limpa campo de senha na edição
        document.getElementById('senha').value = '';
        document.getElementById('senhaHelp').innerText = 'Deixe em branco para manter a senha atual.';

        modal.style.display = 'flex';
    };

    window.deleteItem = async function(id) {
        if (!confirm('Tem certeza que deseja excluir este usuário?')) return;

        try {
            const res = await fetch(`${API_URL}?id=${id}`, {
                method: 'DELETE'
            });
            const json = await res.json();
            if (json.success) {
                alert('Usuário excluído!');
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

    // Submissão do Formulário
    form.addEventListener('submit', async (e) => {
        e.preventDefault();

        const id = document.getElementById('userId').value;
        const method = id ? 'PUT' : 'POST';

        const payload = {
            id: id ? id : null,
            usuario: document.getElementById('usuario').value,
            tipo: document.getElementById('tipo').value,
            email: document.getElementById('email').value,
            zap: document.getElementById('zap').value,
            codusur: document.getElementById('codusur').value,
            codcli: document.getElementById('codcli').value,
            ativo: document.getElementById('ativo').value,
            senha: document.getElementById('senha')
                .value // Backend só atualiza se não for vazio
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

    window.onclick = function(event) {
        if (event.target == modal) closeModal();
    }

    // Inicializa
    fetchData();
});
</script>
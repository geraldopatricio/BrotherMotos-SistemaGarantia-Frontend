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

<style>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;700&display=swap');

* {
    box-sizing: border-box;
    font-family: 'Inter', 'roboto', sans-serif;
}

/* REUTILIZANDO ESTILOS DE USUARIOS.PHP */
h4 {
    color: #333;
    margin-top: 0;
    font-weight: 600;
}

.card {
    padding: 20px;
    background: #fff;
    border-radius: 8px;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
}

.controls {
    display: flex;
    justify-content: space-between;
    margin-bottom: 20px;
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
    padding: 12px 15px;
    font-size: 14px;
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

.loading {
    text-align: center;
    padding: 20px;
    color: #777;
}

.badge-role {
    background: #e7e7ff;
    color: #696cff;
    padding: 4px 10px;
    border-radius: 6px;
    font-weight: bold;
    text-transform: uppercase;
    font-size: 12px;
}

.btn-manage {
    background: #696cff;
    color: white;
    border: none;
    padding: 6px 12px;
    border-radius: 6px;
    cursor: pointer;
    font-size: 13px;
    display: flex;
    align-items: center;
    gap: 5px;
    text-decoration: none;
}

.btn-manage:hover {
    background: #5a5fd8;
}

/* MODAL ESPECÍFICO PARA PERMISSÕES */
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
    padding: 0;
    border-radius: 12px;

    /* AUMENTAMOS A LARGURA AQUI: */
    width: 900px !important;
    max-width: 95% !important;

    height: auto !important;
    max-height: 90vh;
    display: flex;
    flex-direction: column;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
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
    padding: 20px;
    border-bottom: 1px solid #eee;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.modal-title {
    margin: 0;
    font-size: 18px;
    color: #333;
}

.close-btn {
    background: none;
    border: none;
    font-size: 24px;
    color: #999;
    cursor: pointer;
}

/* Container com scroll e Grid */
.modal-body {
    padding: 20px;
    overflow-y: auto;
    flex-grow: 1;
    background-color: #f8f9fa;
    /* Fundo levemente cinza para destacar os cards */
}

/* O Grid principal: define as 4 colunas */
#permList {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    /* 4 Colunas de tamanhos iguais */
    gap: 15px;
    /* Espaço entre os cartões */
    align-items: start;
    /* Impede que os cartões estiquem a altura */
}

/* O "Cartão" de cada grupo de menu */
.perm-group {
    background: #fff;
    border: 1px solid #e0e0e0;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.03);
    display: flex;
    flex-direction: column;
    height: 100%;
    /* Opcional: faz todos terem altura igual se usar align-items stretch */
}

/* Cabeçalho do Cartão (Menu Pai) */
.perm-header {
    background: #696cff;
    /* Destaque visual para o pai */
    color: #fff;
    padding: 8px 12px;
    font-weight: 600;
    font-size: 13px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.perm-header label {
    cursor: pointer;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    font-size: 12px;
}

.perm-header small {
    display: none;
    /* Esconde o tipo (header/dropdown) para limpar o visual */
}

/* Corpo do Cartão (Filhos) */
.perm-children {
    padding: 10px;
    background: #fff;
    display: flex;
    flex-direction: column;
    /* Filhos um embaixo do outro DENTRO do cartão */
    gap: 8px;
}

/* Item individual (Filho) */
.perm-item {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 13px;
    color: #555;
    cursor: pointer;
    padding: 4px 0;
    border-bottom: 1px dashed #f0f0f0;
}

.perm-item:last-child {
    border-bottom: none;
}

.perm-item input[type="checkbox"],
.perm-header input[type="checkbox"] {
    width: 14px;
    height: 14px;
    accent-color: #fff;
    /* Checkbox branco no header */
    cursor: pointer;
}

/* Checkbox dos filhos com cor padrão */
.perm-item input[type="checkbox"] {
    accent-color: #696cff;
}

/* RESPONSIVIDADE: Ajusta colunas em telas menores */
@media (max-width: 992px) {
    #permList {
        grid-template-columns: repeat(3, 1fr);
    }
}

@media (max-width: 768px) {
    #permList {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 500px) {
    #permList {
        grid-template-columns: 1fr;
    }
}

.modal-footer {
    padding: 20px;
    border-top: 1px solid #eee;
    display: flex;
    justify-content: flex-end;
    gap: 10px;
    background: #f9f9f9;
    border-radius: 0 0 12px 12px;
}


.btn-cancel {
    background: #f1f3f5;
    color: #555;
    border: none;
    padding: 8px 16px;
    border-radius: 6px;
    cursor: pointer;
    font-weight: 500;
}

.btn-save {
    background: #696cff;
    color: white;
    border: none;
    padding: 8px 16px;
    border-radius: 6px;
    cursor: pointer;
    font-weight: 500;
}
</style>

<div class="container-fluid flex-grow-1 container-p-y">
    <div class="card">
        <div>
            <h4>Controle de Acessos e Permissões</h4>
            <p style="color:#777; font-size:14px;">Gerencie quais menus cada perfil de usuário pode visualizar.</p>
        </div>

        <div id="tableContainer">
            <div class="loading">Carregando perfis...</div>
        </div>
    </div>
</div>

<!-- MODAL DE PERMISSÕES -->
<div class="modal-overlay" id="permModal">
    <div class="modal-content">
        <div class="modal-header">
            <h4 class="modal-title">Editar Permissões: <span id="roleName" style="color:#696cff;"></span></h4>
            <button type="button" class="close-btn" onclick="closeModal()">&times;</button>
        </div>

        <div class="modal-body" id="permList">
            <div class="loading">Carregando estrutura de menus...</div>
        </div>

        <div class="modal-footer">
            <button type="button" class="btn-cancel" onclick="closeModal()">Cancelar</button>
            <button type="button" class="btn-save" id="btnSavePerms">Salvar Permissões</button>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
const API_URL = '<?php echo $url_backend; ?>/routers/access.php';
let currentRole = '';

$(document).ready(function() {
    loadRoles();
});

// 1. Carrega a lista de Perfis (Roles)
function loadRoles() {
    $.ajax({
        url: API_URL,
        method: 'GET',
        dataType: 'json',
        success: function(res) {
            if (res.success) {
                renderRolesTable(res.data);
            } else {
                $('#tableContainer').html('<div class="loading">Erro ao carregar dados.</div>');
            }
        },
        error: function() {
            $('#tableContainer').html('<div class="loading">Erro de conexão.</div>');
        }
    });
}

function renderRolesTable(roles) {
    if (!roles || roles.length === 0) {
        $('#tableContainer').html('<div class="loading">Nenhum perfil encontrado na tabela de usuários.</div>');
        return;
    }

    let html = `
        <table>
            <thead>
                <tr>
                    <th>Perfil (Role)</th>
                    <th>Menus Permitidos</th>
                    <th>Status</th>
                    <th style="text-align:center;">Ação</th>
                </tr>
            </thead>
            <tbody>
    `;

    roles.forEach(r => {
        html += `
            <tr>
                <td><span class="badge-role">${r.role}</span></td>
                <td>${r.count} menus ativos</td>
                <td><span style="color:green; font-weight:500;">Ativo</span></td>
                <td style="text-align:center; display:flex; justify-content:center;">
                    <button class="btn-manage" onclick="openPermissions('${r.role}')">
                        <i class="fa fa-lock"></i> Gerenciar
                    </button>
                </td>
            </tr>
        `;
    });

    html += `</tbody></table>`;
    $('#tableContainer').html(html);
}

// 2. Abre Modal e Carrega Matriz de Permissões
window.openPermissions = function(role) {
    currentRole = role;
    $('#roleName').text(role.toUpperCase());
    $('#permModal').css('display', 'flex');
    $('#permList').html('<div class="loading">Carregando menus...</div>');

    $.ajax({
        url: `${API_URL}?action=matrix&role=${role}`,
        method: 'GET',
        success: function(res) {
            if (res.success) {
                renderMenuTree(res.menus, res.permissions);
            }
        }
    });
}

function renderMenuTree(menus, allowedIds) {
    let html = '';

    // Separa Pais e Filhos
    let parents = menus.filter(m => !m.parent_id);
    let children = menus.filter(m => m.parent_id);

    // Mapa de filhos por pai
    let childrenMap = {};
    children.forEach(c => {
        if (!childrenMap[c.parent_id]) childrenMap[c.parent_id] = [];
        childrenMap[c.parent_id].push(c);
    });

    parents.forEach(p => {
        const isChecked = allowedIds.includes(p.id) ? 'checked' : '';
        const hasChildren = childrenMap[p.id] && childrenMap[p.id].length > 0;

        html += `<div class="perm-group">`;

        // Header do Grupo (Menu Pai)
        html += `
            <div class="perm-header">
                <input type="checkbox" id="menu_${p.id}" value="${p.id}" ${isChecked} onchange="toggleGroup(${p.id})">
                <label for="menu_${p.id}" style="cursor:pointer; flex-grow:1;">
                    <i class="${p.icon || 'fa fa-bars'}"></i> ${p.title} 
                    <small style="font-weight:normal; color:#888;">(${p.type})</small>
                </label>
            </div>
        `;

        // Filhos
        if (hasChildren) {
            html += `<div class="perm-children">`;
            childrenMap[p.id].forEach(c => {
                const childChecked = allowedIds.includes(c.id) ? 'checked' : '';
                html += `
                    <label class="perm-item">
                        <input type="checkbox" class="child-of-${p.id}" value="${c.id}" ${childChecked}>
                        ${c.title}
                    </label>
                `;
            });
            html += `</div>`;
        }

        html += `</div>`;
    });

    $('#permList').html(html);
}

// Helper: Selecionar pai seleciona todos os filhos (UX Opcional)
window.toggleGroup = function(parentId) {
    const isChecked = $(`#menu_${parentId}`).is(':checked');
    $(`.child-of-${parentId}`).prop('checked', isChecked);
}

// 3. Salvar
$('#btnSavePerms').on('click', function() {
    let selectedIds = [];

    // Pega todos os checkboxes marcados dentro do modal
    $('#permList input[type="checkbox"]:checked').each(function() {
        selectedIds.push($(this).val());
    });

    $.ajax({
        url: API_URL,
        method: 'POST',
        contentType: 'application/json',
        data: JSON.stringify({
            role: currentRole,
            menu_ids: selectedIds
        }),
        success: function(res) {
            if (res.success) {
                alert('Permissões atualizadas!');
                closeModal();
                loadRoles(); // Atualiza contagem
            } else {
                alert('Erro: ' + res.message);
            }
        },
        error: function() {
            alert('Erro ao salvar.');
        }
    });
});

window.closeModal = function() {
    $('#permModal').css('display', 'none');
}

// Fechar ao clicar fora
$(window).on('click', function(e) {
    if ($(e.target).hasClass('modal-overlay')) closeModal();
});
</script>
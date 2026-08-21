<!-- Adicione isso no topo do arquivo se não houver -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<?php
// ==========================================================
// CONFIGURAÇÕES INICIAIS
// ==========================================================
ini_set('display_errors', 0);
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verifica se tem ID
if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo "<script>window.location.href='index.php?page=garantias';</script>";
    exit();
}

$garantiaId = htmlspecialchars($_GET['id']);
$userRca = $_SESSION['user_rca'] ?? 'Sistema';

// CAPTURA DADOS DE SESSÃO
$userTipo = $_SESSION['user_tipo'] ?? '';      // Ex: ADMIN, SAC, RCA, CLIENTE, GERENTE
$userUsuario = $_SESSION['user_usuario'] ?? ''; // Nome do usuário logado
?>

<!-- ==========================================================
     ESTILOS CSS
     ========================================================== -->
<style>
    :root {
        --primary-color: rgb(103, 92, 248);
        --primary-hover: rgb(33, 74, 136);
    }

    .attach-box a {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 100%;
        height: 100%;
        text-decoration: none;
    }

    .attach-box i {
        display: block;
        line-height: 1;
    }

    .garantia-container {
        display: flex;
        flex-direction: column;
        justify-content: flex-start !important;
        gap: 20px;
        width: 100%;
    }

    .card-edit {
        background: #fff;
        border-radius: 8px;
        box-shadow: 0 2px 6px rgba(67, 89, 113, 0.12);
        padding: 24px;
        margin-bottom: 24px;
    }

    .section-title {
        color: var(--primary-color);
        font-weight: 700;
        margin-bottom: 15px;
        font-size: 16px;
        border-left: 4px solid var(--primary-color);
        padding-left: 10px;
    }

    .form-label {
        font-size: 11px;
        font-weight: 700;
        color: #566a7f;
        text-transform: uppercase;
        margin-bottom: 5px;
    }

    .form-control-custom {
        width: 100%;
        padding: 10px;
        border: 1px solid #d9dee3;
        border-radius: 6px;
        font-size: 14px;
        transition: border 0.3s;
    }

    .form-control-custom:focus {
        outline: none;
        border-color: var(--primary-color);
    }

    .form-control-custom[readonly],
    .form-control-custom[disabled] {
        background-color: #f5f5f9;
        cursor: not-allowed;
        opacity: 0.8;
    }

    .item-card {
        border: 1px solid #e0e0e0;
        border-radius: 8px;
        margin-bottom: 15px;
        background: #fff;
        overflow: hidden;
    }

    .item-header {
        padding: 15px 20px;
        background: #fdfdfe;
        cursor: pointer;
        display: flex;
        justify-content: space-between;
        align-items: center;
        transition: background 0.2s;
    }

    .item-header:hover {
        background: #f8f9fa;
    }

    .item-body {
        padding: 20px;
        display: none;
        border-top: 1px solid #eee;
        background-color: #fff;
    }

    .item-card.open .item-body {
        display: block;
    }

    .item-card.open .item-header {
        background: #f4f6f8;
        border-bottom: 1px solid #ddd;
    }

    .section-separator {
        border-bottom: 1px dashed #ccc;
        margin: 25px 0 15px 0;
        display: flex;
        justify-content: space-between;
        align-items: center;
        color: var(--primary-color);
        font-weight: bold;
        font-size: 13px;
        padding-bottom: 5px;
    }

    .attachments-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(90px, 1fr));
        gap: 10px;
    }

    .attach-box {
        position: relative;
        border: 1px solid #ddd;
        border-radius: 6px;
        aspect-ratio: 1;
        overflow: hidden;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #f9f9f9;
    }

    .attach-box img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.3s;
    }

    .attach-box:hover img {
        transform: scale(1.1);
    }

    .attach-del {
        position: absolute;
        top: 4px;
        right: 4px;
        background: rgba(220, 53, 69, 0.9);
        color: white;
        width: 22px;
        height: 22px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        font-size: 10px;
        z-index: 2;
    }

    .attach-add-btn {
        border: 2px dashed #ccc;
        cursor: pointer;
        color: #aaa;
        flex-direction: column;
        font-size: 24px;
        transition: 0.3s;
    }

    .attach-add-btn:hover {
        border-color: var(--primary-color);
        color: var(--primary-color);
        background: #fff;
    }

    .wf-timeline {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .wf-item {
        padding-left: 20px;
        border-left: 2px solid #e0e0e0;
        position: relative;
        margin-bottom: 20px;
    }

    .wf-item::before {
        content: '';
        width: 12px;
        height: 12px;
        background: var(--primary-color);
        border-radius: 50%;
        position: absolute;
        left: -7px;
        top: 0px;
        border: 2px solid #fff;
        box-shadow: 0 0 0 1px var(--primary-color);
    }

    .wf-meta {
        font-size: 12px;
        color: #999;
        margin-bottom: 4px;
    }

    .wf-content {
        background: #f8f9fa;
        padding: 12px;
        border-radius: 6px;
        font-size: 14px;
        border: 1px solid #eee;
    }

    .custom-modal-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        z-index: 1050;
        display: none;
        justify-content: center;
        align-items: center;
    }

    .custom-modal-overlay.active {
        display: flex;
        animation: fadeIn 0.2s;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(5px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .custom-modal-content {
        background: #fff;
        width: 90%;
        max-width: 500px;
        padding: 25px;
        border-radius: 10px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
    }

    .btn-custom {
        padding: 8px 16px;
        border-radius: 6px;
        border: none;
        font-weight: 500;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: 0.2s;
        text-decoration: none;
        font-size: 14px;
    }

    .btn-primary-custom {
        background: var(--primary-color);
        color: #fff;
    }

    .btn-primary-custom:hover {
        background: var(--primary-hover);
        color: #fff;
    }

    .btn-secondary-custom {
        background: #8592a3;
        color: #fff;
    }

    .btn-success-custom {
        background: #28a745;
        color: #fff;
    }

    .btn-warning-custom {
        background: #ffc107;
        color: #000;
    }

    .btn-danger-custom {
        background: #ff3e1d;
        color: #fff;
    }

    .status-badge {
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: bold;
        background: #eee;
    }

    .admin-actions-container {
        background: #f8f9fa;
        border: 1px solid #e0e0e0;
        border-radius: 8px;
        padding: 20px;
        margin-top: 20px;
        display: flex;
        justify-content: flex-end;
        gap: 15px;
    }

    .autocomplete-wrapper {
        position: relative;
        width: 100%;
    }

    .suggestions-list {
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        background: #fff;
        border: 1px solid #d9dee3;
        border-top: none;
        border-radius: 0 0 6px 6px;
        max-height: 200px;
        overflow-y: auto;
        z-index: 1100;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        display: none;
    }

    .suggestions-list.active {
        display: block;
    }

    .suggestion-item {
        padding: 10px;
        cursor: pointer;
        border-bottom: 1px solid #f0f0f0;
        font-size: 13px;
        display: flex;
        justify-content: space-between;
    }

    .suggestion-item:hover {
        background-color: #f5f5f9;
        color: var(--primary-color);
    }
</style>

<!-- ==========================================================
     HTML PRINCIPAL
     ========================================================== -->
<div class="container-fluid flex-grow-1 container-p-y garantia-container">

    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <h3 class="fw-bold py-3 mb-1">Garantia <span
                    style="color: var(--primary-color);">#<?php echo $garantiaId; ?></span></h3>
            <small class="text-muted" id="header-cliente">Carregando dados...</small>
        </div>

        <div class="d-flex flex-wrap gap-2">
            <button class="btn-custom btn-danger-custom" id="btn-excluir-garantia" style="display: none;"
                onclick="deleteGarantia()">
                <i class="fas fa-trash"></i> Excluir Garantia
            </button>

            <a href="index.php?page=garantias" class="btn-custom btn-secondary-custom">
                <i class="fas fa-arrow-left"></i> Voltar
            </a>

            <button class="btn-custom btn-primary-custom" id="btn-save-digitacao"
                onclick="saveGarantiaHeader('Em Digitação')">
                <i class="fas fa-save"></i> Salvar (Em Digitação)
            </button>

            <button class="btn-custom btn-warning-custom" id="btn-save-aprovacao"
                onclick="saveGarantiaHeader('Enviado para Aprovação')">
                <i class="fas fa-paper-plane"></i> Salvar - Enviar Aprovação
            </button>

            <button class="btn-custom btn-success-custom" id="btn-save-finalizar"
                onclick="saveGarantiaHeader('Aberto')">
                <i class="fas fa-check"></i> Salvar - Finalizar Garantia
            </button>
        </div>
    </div>

    <div class="card-edit">
        <div class="mb-4">
            <h5 class="section-title"><i class="far fa-file-alt me-2"></i>Dados Gerais</h5>
            <div class="row g-3">
                <div class="col-md-3"><label class="form-label">Data Cadastro</label><input type="text" id="dt_cadastro"
                        class="form-control-custom" readonly></div>
                <div class="col-md-2"><label class="form-label">Cód. Cli</label><input type="text" id="codcli"
                        class="form-control-custom" readonly></div>
                <div class="col-md-7"><label class="form-label">Cliente</label><input type="text" id="nome_cliente"
                        class="form-control-custom" readonly></div>
                <div class="col-md-12"><label class="form-label">RCA / Vendedor</label><input type="text" id="nome_rca"
                        class="form-control-custom" readonly></div>
                <div class="col-12">
                    <hr class="my-2 text-muted">
                </div>
                <div class="col-md-6"><label class="form-label">Responsável</label><input type="text" id="responsavel"
                        class="form-control-custom"></div>
                <div class="col-md-3">
                    <label class="form-label">Status</label>
                    <select id="status_garantia" class="form-control-custom">
                        <option value="Pendente">Em Processamento</option>
                        <option value="Enviado para Aprovação">Aguardando Aprovação do Gestor</option>
                        <option value="Concluído">Disponível</option>
                        <option value="Reprovado">Reprovado</option>
                        <option value="Em Análise">Aguardando Reanálise</option>
                        <option value="Aberto">Aberto</option>
                        <option value="Em Digitacao">Em Digitação</option>
                        <option value="Finalizado">Finalizado</option>
                    </select>
                </div>
                <div class="col-md-3"><label class="form-label">Dispositivo</label><input type="text" id="dispositivo"
                        class="form-control-custom" readonly></div>
            </div>
        </div>

        <hr class="my-5" style="border-top: 2px dashed #eceef1;">

        <div class="mb-2">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="section-title mb-0"><i class="fas fa-boxes me-2"></i>Itens & Detalhes</h5>
                <button class="btn-custom btn-primary-custom" id="btn-novo-item" onclick="openNewItemModal()">
                    <i class="fas fa-plus"></i> Novo Item
                </button>
            </div>
            <div id="items-container">
                <div class="text-center p-5 text-muted"><i class="fas fa-spinner fa-spin fa-2x"></i><br>Carregando
                    itens...</div>
            </div>

            <div id="admin-actions-footer" class="admin-actions-container" style="display: none;">
                <button class="btn-custom btn-warning-custom" onclick="revisarGarantia()">
                    <i class="fas fa-search"></i> Revisar Garantia
                </button>
                <button class="btn-custom btn-success-custom" onclick="gravarGarantiaAnalista()">
                    <i class="fas fa-save"></i> Gravar Garantia (Analista)
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Novo Item -->
<div id="modalNewItem" class="custom-modal-overlay">
    <div class="custom-modal-content">
        <h4 class="mb-3">Adicionar Novo Item</h4>
        <div class="row g-3">
            <div class="col-md-12">
                <label class="form-label">Código Fábrica / Produto</label>
                <div class="autocomplete-wrapper">
                    <input type="text" id="new_codfab" class="form-control-custom" placeholder="Ex: 55100-BF0-CG01"
                        autocomplete="off" maxlength="14" oninput="handleCodFabInput(this)" required>
                    <div id="suggestions-list" class="suggestions-list"></div>
                </div>
                <div id="feedback_produto" class="mt-2 text-primary fw-bold" style="min-height:20px; font-size:13px;">
                </div>
            </div>
            <div class="col-md-12">
                <label class="form-label">Quantidade</label>
                <input type="number" id="new_qtd" class="form-control-custom" value="1" min="1" required>
            </div>
            <div class="col-md-12">
                <label class="form-label">Motivo</label>
                <select id="new_motivo" class="form-control-custom" required>
                    <option value="">-- Selecione o Motivo --</option>
                    <option value="Arranhado">Arranhado</option>
                    <option value="Com ruidos">Com ruídos</option>
                    <option value="Descentralizado">Descentralizado</option>
                    <option value="Deslizando">Deslizando</option>
                    <option value="Falha na aceleracao">Falha na aceleração</option>
                    <option value="Falha na vulcanizacao">Falha na vulcanização</option>
                    <option value="Falhando">Falhando</option>
                    <option value="Fumacando">Fumaçando</option>
                    <option value="Nao aciona">Não aciona</option>
                    <option value="Nao encaixa">Não encaixa</option>
                    <option value="Nao injeta combustível">Não injeta combustível</option>
                    <option value="Nao regula">Não regula</option>
                    <option value="Oscilando">Oscilando</option>
                    <option value="Sem corrente eletrica">Sem corrente elétrica</option>
                    <option value="Sem pressao">Sem pressão</option>
                    <option value="Sem pulso">Sem pulso</option>
                    <option value="Travando">Travando</option>
                    <option value="Vazando">Vazando</option>
                    <option value="Outros">Outros</option>
                </select>
            </div>
            <div class="col-md-12"><label class="form-label">Observação</label><textarea id="new_obs"
                    class="form-control-custom" rows="3" required></textarea></div>
            <div class="col-md-12 border-top pt-3 mt-2">
                <label class="form-label d-flex justify-content-between align-items-center">
                    Fotos / Anexos
                    <button class="btn btn-sm btn-outline-primary"
                        onclick="document.getElementById('newItemFile').click()" required><i class="fas fa-plus"></i>
                        Adicionar</button>
                </label>
                <input type="file" id="newItemFile" multiple class="d-none" onchange="handleNewItemFiles(this)">
                <div id="new_item_previews" class="d-flex gap-2 flex-wrap mt-2">
                    <small class="text-muted fst-italic">Nenhum arquivo selecionado.</small>
                </div>
            </div>
        </div>
        <div class="mt-4 text-end">
            <button class="btn-custom btn-secondary-custom" id="btn-cancel-new-item"
                onclick="closeModal('modalNewItem')">Cancelar</button>
            <button class="btn-custom btn-primary-custom" id="btn-submit-new-item" onclick="submitNewItem()">Salvar
                Item</button>
        </div>
    </div>
</div>

<!-- Modal para Visualização de Mídia -->
<div id="mediaModal" class="custom-modal-overlay" style="z-index: 2000;">
    <div class="custom-modal-content"
        style="max-width: 800px; width: 800px; height: 600px; display: flex; flex-direction: column; padding: 10px;">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <h5 class="mb-0" id="mediaModalTitle">Visualizar Anexo</h5>
            <button class="btn-custom btn-secondary-custom" onclick="closeMediaModal()"
                style="padding: 5px 10px;">×</button>
        </div>
        <div id="mediaModalBody"
            style="flex-grow: 1; display: flex; align-items: center; justify-content: center; overflow: hidden; background: #000; border-radius: 6px;">
            <!-- Conteúdo injetado via JS -->
        </div>
    </div>
</div>

<input type="file" id="globalFileUpload" class="d-none">

<!-- ==========================================================
     JAVASCRIPT
     ========================================================== -->
<script>
    const API_URL = '<?php echo $url_backend; ?>/routers/garantia_edit.php';
    const UPLOAD_URL = '<?php echo $url_backend; ?>/routers/upload.php';
    const PRODUTO_URL = '<?php echo $url_backend; ?>/routers/produtos.php';
    const EMAIL_GERAL_URL = '<?php echo $url_backend; ?>/routers/enviar_email_geral.php';
    const ID_GARANTIA = '<?php echo $garantiaId; ?>';
    const USER_RCA = '<?php echo $userRca; ?>';
    const USER_TIPO = '<?php echo strtoupper($userTipo); ?>';
    const USER_NAME = '<?php echo $userUsuario; ?>';

    const PERFIS_INTERNOS = ['ADMIN', 'SAC', 'GERENTE'];
    const IS_INTERNAL = PERFIS_INTERNOS.includes(USER_TIPO);

    let GLOBAL_DATA = {};
    let NEW_ITEM_FILES = [];
    let CURRENT_UPLOAD_CONTEXT = {};
    let searchTimeout = null;
    let openItemIds = new Set();

    document.addEventListener('DOMContentLoaded', () => {
        loadAllData();
        document.addEventListener('click', (e) => {
            if (!e.target.closest('.autocomplete-wrapper')) {
                document.getElementById('suggestions-list').classList.remove('active');
            }
        });
    });

    // ========== MODAL DE MÍDIA ==========
    function openMediaModal(url, filename) {
        const modal = document.getElementById('mediaModal');
        const body = document.getElementById('mediaModalBody');
        const title = document.getElementById('mediaModalTitle');
        const ext = filename.split('.').pop().toLowerCase();

        title.innerText = filename;
        body.innerHTML = '';

        const isImg = ['jpg', 'jpeg', 'png', 'gif', 'webp'].includes(ext);
        const isVid = ['mp4', 'mov', 'avi'].includes(ext);

        if (isImg) {
            body.innerHTML = `<img src="${url}" style="max-width: 100%; max-height: 100%; object-fit: contain;">`;
        } else if (isVid) {
            body.innerHTML = `<video src="${url}" controls autoplay style="max-width: 100%; max-height: 100%;"></video>`;
        } else {
            body.innerHTML = `<iframe src="${url}" style="width: 100%; height: 100%; border: none;"></iframe>`;
        }
        modal.classList.add('active');
    }

    function closeMediaModal() {
        const body = document.getElementById('mediaModalBody');
        body.innerHTML = '';
        document.getElementById('mediaModal').classList.remove('active');
    }

    // ========== CARREGAMENTO DE DADOS ==========
    async function loadAllData() {
        try {
            const res = await fetch(`${API_URL}?scope=garantia&id=${ID_GARANTIA}`);
            const json = await res.json();
            if (!json.success) throw new Error(json.message);
            GLOBAL_DATA = json.data;

            document.getElementById('header-cliente').innerText = `${GLOBAL_DATA.codcli} - ${GLOBAL_DATA.nome_cliente}`;
            document.getElementById('dt_cadastro').value = GLOBAL_DATA.dt_cadastro;
            document.getElementById('codcli').value = GLOBAL_DATA.codcli;
            document.getElementById('nome_cliente').value = GLOBAL_DATA.nome_cliente;
            document.getElementById('nome_rca').value = GLOBAL_DATA.nome_rca;
            document.getElementById('responsavel').value = GLOBAL_DATA.responsavel;
            // document.getElementById('status_garantia').value = GLOBAL_DATA.status;
            selectValueSmart('status_garantia', GLOBAL_DATA.status);
            document.getElementById('dispositivo').value = GLOBAL_DATA.dispositivo;

            renderItems(GLOBAL_DATA.itens || []);
            applyPermissions();
        } catch (e) {
            alert('Erro ao carregar: ' + e.message);
        }
    }

    // Função para selecionar o valor no Select ignorando acentos
    function selectValueSmart(elementId, valueToSet) {
        const select = document.getElementById(elementId);
        if (!select) return;

        // Tenta o match exato primeiro
        select.value = valueToSet;

        // Se não selecionou nada (ficou em branco ou resetou), faz a busca inteligente
        if (select.selectedIndex === -1 || select.value !== valueToSet) {
            const normalize = (str) => str.normalize("NFD").replace(/[\u0300-\u036f]/g, "").toLowerCase();
            const searchVal = normalize(valueToSet);

            for (let i = 0; i < select.options.length; i++) {
                if (normalize(select.options[i].value) === searchVal) {
                    select.selectedIndex = i;
                    break;
                }
            }
        }
    }


    function getFileIcon(filename) {
        if (!filename) return {
            icon: 'fa-file',
            color: '#95a5a6'
        };
        const ext = filename.split(/[#?]/)[0].split('.').pop().trim().toLowerCase();
        const types = {
            'jpg': {
                icon: 'fa-file-image',
                color: '#4caf50',
                isImg: true
            },
            'jpeg': {
                icon: 'fa-file-image',
                color: '#4caf50',
                isImg: true
            },
            'png': {
                icon: 'fa-file-image',
                color: '#4caf50',
                isImg: true
            },
            'gif': {
                icon: 'fa-file-image',
                color: '#4caf50',
                isImg: true
            },
            'webp': {
                icon: 'fa-file-image',
                color: '#4caf50',
                isImg: true
            },
            'mp4': {
                icon: 'fa-file-video',
                color: '#2196f3',
                isVid: true
            },
            'mov': {
                icon: 'fa-file-video',
                color: '#2196f3',
                isVid: true
            },
            'avi': {
                icon: 'fa-file-video',
                color: '#2196f3',
                isVid: true
            },
            'pdf': {
                icon: 'fa-file-pdf',
                color: '#e74c3c'
            },
            'doc': {
                icon: 'fa-file-word',
                color: '#2b579a'
            },
            'docx': {
                icon: 'fa-file-word',
                color: '#2b579a'
            },
            'xls': {
                icon: 'fa-file-excel',
                color: '#1d6f42'
            },
            'xlsx': {
                icon: 'fa-file-excel',
                color: '#1d6f42'
            }
        };
        return types[ext] || {
            icon: 'fa-file',
            color: '#95a5a6'
        };
    }

    // ========== RENDERIZAÇÃO DE ITENS ==========
    function renderItems(itens) {
        const container = document.getElementById('items-container');
        container.innerHTML = '';

        const btnExcluirGarantia = document.getElementById('btn-excluir-garantia');
        btnExcluirGarantia.style.display = (itens.length === 0) ? 'inline-flex' : 'none';

        const statusGeral = document.getElementById('status_garantia').value;
        const statusPermitidos = ['Em Digitação', 'Em Digitacao', 'Aberto', 'Pendencia do Cliente', 'Pendência do Cliente'];
        const isLockedForExternal = !IS_INTERNAL && !statusPermitidos.includes(statusGeral);

        // NOVA REGRA: Apenas SAC e ADMIN podem editar campos críticos de decisão
        const CAN_EDIT_DECISION = ['ADMIN', 'SAC'].includes(USER_TIPO);

        const canEditWorkflow = IS_INTERNAL || (!isLockedForExternal) || (statusGeral === 'Pendente');

        itens.forEach(item => {
            const auto = item.automacao || {};
            const workflows = item.workflow || [];
            const anexos = item.anexos || [];
            const BASE_URL = '<?php echo $url_base; ?>/uploads/';

            let anexosHTML = anexos.map(anx => {
                const urlAbs = anx.anexo.startsWith("http") ? anx.anexo : `${BASE_URL}${anx.anexo}`;
                const fileInfo = getFileIcon(anx.anexo);
                const btnDel = !isLockedForExternal ?
                    `<div class="attach-del" onclick="deleteAnexo(${anx.id})"><i class="fas fa-times"></i></div>` :
                    '';

                let contentHtml = '';
                if (fileInfo.isImg) {
                    contentHtml = `<img src="${urlAbs}">`;
                } else if (fileInfo.isVid) {
                    contentHtml =
                        `<video src="${urlAbs}" style="width:100%; height:100%; object-fit: cover;"></video><i class="fas fa-play-circle" style="position:absolute; color:white; font-size:22px; opacity:0.8;"></i>`;
                } else {
                    contentHtml =
                        `<i class="fas ${fileInfo.icon} fa-2x" style="color: ${fileInfo.color}"></i>`;
                }

                return `<div class="attach-box"><a href="javascript:void(0)" onclick="openMediaModal('${urlAbs}', '${anx.anexo}')" title="${anx.anexo}">${contentHtml}</a>${btnDel}</div>`;
            }).join('');

            if (!isLockedForExternal) {
                anexosHTML +=
                    `<div class="attach-box attach-add-btn" onclick="triggerUpload('anexo', '${item.id}')"><i class="fas fa-plus"></i></div>`;
            }

            let wfHTML = workflows.map(wf => {
                let filesWfHtml = '';
                if (wf.arquivos && wf.arquivos.length > 0) {
                    filesWfHtml = '<div class="mt-2 pt-2 border-top d-flex flex-wrap gap-2">';
                    wf.arquivos.forEach(file => {
                        const urlFile = file.anexo.startsWith("http") ? file.anexo :
                            `${BASE_URL}${file.anexo}`;
                        const fileInfoWf = getFileIcon(file.anexo);
                        let mediaWfHtml = fileInfoWf.isImg ?
                            `<img src="${urlFile}" style="width:100%; height:100%; object-fit:cover;">` :
                            (fileInfoWf.isVid ?
                                `<video src="${urlFile}" style="width:100%; height:100%; object-fit:cover;"></video><i class="fas fa-play" style="position:absolute; font-size:10px; color:white;"></i>` :
                                `<i class="fas ${fileInfoWf.icon} fa-lg" style="color: ${fileInfoWf.color}"></i>`
                            );

                        filesWfHtml +=
                            `<a href="javascript:void(0)" onclick="openMediaModal('${urlFile}', '${file.anexo}')" class="d-flex align-items-center justify-content-center border rounded bg-white overflow-hidden position-relative" style="width: 50px; height: 50px;">${mediaWfHtml}</a>`;
                    });
                    filesWfHtml += '</div>';
                }
                return `<li class="wf-item"><div class="wf-meta"><b>${wf.usuario || 'Sistema'}</b> - ${wf.dt_hora}</div><div class="wf-content"><div><strong>${wf.parecer_tec}:</strong> ${wf.parecer_detalhes || ''}</div>${filesWfHtml}</div></li>`;
            }).join('');

            const isOpen = openItemIds.has(String(item.id)) ? 'open' : '';
            const cardHTML = `
        <div class="item-card ${isOpen}" id="item-${item.id}">
            <div class="item-header" onclick="toggleItem(this, '${item.id}')">
                <div>
                    <span class="fw-bold">Item #${String(item.id).substring(0,8)}... - ${item.codfab} <span id="desc-item-${item.id}" style="font-weight: normal; color: #666; margin-left: 5px;"></span></span><br>
                    <small>${item.motivo} (Qtd: ${item.qtd})</small>
                </div>
                <div class="d-flex align-items-center gap-3">
                    <!-- ALTERAÇÃO 1: Mostra o Parecer no lugar do Status com cor dinâmica -->
                    <span class="status-badge" style="background-color: ${getParecerColor(auto.parecer_tec)}; border: 1px solid #ccc;">
                        ${auto.parecer_tec || 'Pendente'}
                    </span>
                    <i class="fas fa-chevron-down"></i>
                </div>
            </div>
            <div class="item-body">
                ${!isLockedForExternal ? `<button class="btn-custom btn-danger-custom mb-3" onclick="deleteItem('${item.id}')"><i class="fas fa-trash"></i> Excluir</button>` : ''}
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Status Item</label>
                        <!-- ALTERAÇÃO 2: Verificação CAN_EDIT_DECISION para Status Item -->
                        <select id="status_item_${item.id}" class="form-control-custom" ${!CAN_EDIT_DECISION ? 'disabled' : ''}>
                            <option value="Em Digitacao" ${(item.status == 'Em Digitacao' || item.status == 'Em Digitação') ? 'selected' : ''}>Em Digitação</option>
                            <option value="Em Analise" ${(item.status == 'Em Analise' || item.status == 'Em Análise') ? 'selected' : ''}>Em Análise</option>
                            <option value="Concluido" ${(item.status == 'Concluido' || item.status == 'Concluído') ? 'selected' : ''}>Concluído</option>
                        </select>
                    </div>
                    <div class="col-md-9">
                        <label class="form-label">Obs. Interna</label>
                        <div class="d-flex gap-2">
                            <input type="text" id="obs_item_${item.id}" class="form-control-custom" value="${item.observacao || ''}" ${isLockedForExternal ? 'readonly' : ''}>
                            ${!isLockedForExternal ? `<button class="btn-custom btn-primary-custom" onclick="updateItemBasic('${item.id}')">OK</button>` : ''}
                        </div>
                    </div>
                </div>
                <div class="section-separator"><span>DADOS TÉCNICOS</span></div>
                <div class="p-3 bg-light border rounded">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">NF</label>
                            <input type="text" id="auto_nf_${item.id}" class="form-control-custom" value="${auto.nf||''}" ${!IS_INTERNAL ? 'readonly' : ''} onchange="saveAutomacao('${item.id}', ${auto.id || 'null'}, false)">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Valor</label>
                            <input type="text" id="auto_valor_${item.id}" class="form-control-custom" value="${formatMoney(auto.valor)}" oninput="maskCurrency(this)" onchange="saveAutomacao('${item.id}', ${auto.id || 'null'}, false)" ${!IS_INTERNAL ? 'readonly' : ''}>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Parecer</label>
                            <!-- ALTERAÇÃO 2: Verificação CAN_EDIT_DECISION para Parecer -->
                            <select id="auto_parecer_${item.id}" class="form-control-custom" 
                                ${!CAN_EDIT_DECISION ? 'disabled' : ''} 
                                style="background-color: ${getParecerColor(auto.parecer_tec)};" 
                                onchange="handleParecerChange('${item.id}', this.value, ${auto.id || 'null'})">
                                <option value="" ${(!auto.parecer_tec) ? 'selected' : ''}>-- Selecione --</option>
                                <option value="Aprovado" ${(auto.parecer_tec == 'Aprovado') ? 'selected' : ''}>Aprovado</option>
                                <option value="Reprovado" ${(auto.parecer_tec == 'Reprovado') ? 'selected' : ''}>Reprovado</option>
                                <option value="Pendente" ${(auto.parecer_tec == 'Pendente' || auto.parecer_tec == 'Aberto' || auto.parecer_tec == 'Em Analise') ? 'selected' : ''}>Pendente</option>
                            </select>
                        </div>
                    </div>
                </div>
                    <div class="section-separator"><span>ANEXOS</span></div>
                    <div class="attachments-grid">${anexosHTML}</div>
                    <div class="section-separator"><span>WORKFLOW</span></div>
                    <div class="row">
                        <div class="col-md-8"><ul class="wf-timeline">${wfHTML}</ul></div>
                        <div class="col-md-4">
                            <div class="p-3 border rounded bg-white">
                                <label class="form-label text-primary">Notas > Workflow</label>
                                <textarea id="wf_new_detalhe_${item.id}" class="form-control-custom mb-2" rows="2" placeholder="Digite sua nota..." ${!canEditWorkflow ? 'readonly' : ''}></textarea>
                                <div class="d-flex justify-content-between align-items-center">
                                    <button class="btn-custom btn-secondary-custom btn-sm" onclick="triggerUpload('workflow', '${item.id}')" ${!canEditWorkflow ? 'disabled' : ''}><i class="fas fa-paperclip"></i> Anexar Imagem</button>
                                    <button class="btn-custom btn-primary-custom btn-sm" onclick="addWorkflow('${item.id}')" ${!canEditWorkflow ? 'disabled' : ''}>Enviar <i class="fas fa-paper-plane ms-1"></i></button>
                                </div>
                                <input type="hidden" id="wf_temp_file_${item.id}"><small id="wf_file_feedback_${item.id}" class="text-success d-none mt-2"><i class="fas fa-check"></i> Imagem pronta para envio!</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>`;
            container.insertAdjacentHTML('beforeend', cardHTML);
            loadItemDescription(item.codfab, item.id);
        });
    }

    // ========== REGRAS DE STATUS E SALVAMENTO ==========
    async function saveGarantiaHeader(targetStatus) {
        const selectElement = document.getElementById('status_garantia');
        const statusFinal = targetStatus ? targetStatus : selectElement.value;

        const res = await sendRequest('garantia', 'PUT', {
            id: ID_GARANTIA,
            status: statusFinal,
            responsavel: USER_NAME
        }, false);

        if (res.success) {
            let newItemStatus = 'Concluido';
            if (statusFinal === 'Em Digitação' || statusFinal === 'Em Digitacao') {
                newItemStatus = 'Em Digitacao';
            } else if (['Aberto', 'Pendente', 'Em Análise', 'Em Analise', 'Enviado para Aprovação'].includes(
                    statusFinal)) {
                newItemStatus = 'Em Analise';
            }

            if (GLOBAL_DATA.itens) {
                for (let item of GLOBAL_DATA.itens) {
                    await sendRequest('item', 'PUT', {
                        id: item.id,
                        status: newItemStatus,
                        user_cad: USER_NAME
                    }, false);
                }
            }

            if (USER_TIPO === 'SAC' && statusFinal === 'Aberto') await dispararEmailGenerico('finalizado_sac');
            if (statusFinal === 'Enviado para Aprovação') {
                await fetch(`${API_URL.replace('garantia_edit.php','enviar_email_garantia.php')}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        id: ID_GARANTIA,
                        status: 'Enviado para Aprovação',
                        codcli: GLOBAL_DATA.codcli,
                        nome_cliente: GLOBAL_DATA.nome_cliente,
                        dt_cadastro: GLOBAL_DATA.dt_cadastro,
                        nome_rca: GLOBAL_DATA.nome_rca,
                        itens: GLOBAL_DATA.itens
                    })
                });
            }

            alert('Garantia e itens atualizados!');
            await loadAllData();
        }
    }

    async function handleParecerChange(itemId, valor, autoId) {
        const selP = document.getElementById(`auto_parecer_${itemId}`);
        if (selP) selP.style.backgroundColor = getParecerColor(valor);
        const selS = document.getElementById(`status_item_${itemId}`);
        if (selS) selS.value = (valor === 'Aprovado' || valor === 'Reprovado') ? 'Concluido' : 'Em Analise';
        await saveAutomacao(itemId, autoId, false);
    }

    async function saveAutomacao(itemId, autoId, mostrarAlerta = true) {
        const payload = {
            fk_item: itemId,
            nf: document.getElementById(`auto_nf_${itemId}`).value,
            valor: parseCurrency(document.getElementById(`auto_valor_${itemId}`).value),
            parecer_tec: document.getElementById(`auto_parecer_${itemId}`).value,
            codcli: GLOBAL_DATA.codcli,
            parecer_detalhes: document.getElementById(`auto_parecer_${itemId}`).value + " (Analista)"
        };
        const method = (autoId && autoId !== 'null') ? 'PUT' : 'POST';
        if (method === 'PUT') payload.id = autoId;
        const res = await sendRequest('automacao', method, payload, mostrarAlerta);
        if (res.success && !mostrarAlerta) await updateItemBasic(itemId, false);
    }

    // ========== FUNÇÕES AUXILIARES ==========
    async function updateItemBasic(id, mostrarAlerta = true) {
        await sendRequest('item', 'PUT', {
            id,
            status: document.getElementById(`status_item_${id}`).value,
            observacao: document.getElementById(`obs_item_${id}`).value,
            user_cad: USER_NAME
        }, mostrarAlerta);
    }

    async function loadAllData() {
        try {
            const res = await fetch(`${API_URL}?scope=garantia&id=${ID_GARANTIA}`);
            const json = await res.json();
            if (!json.success) throw new Error(json.message);
            GLOBAL_DATA = json.data;
            document.getElementById('header-cliente').innerText = `${GLOBAL_DATA.codcli} - ${GLOBAL_DATA.nome_cliente}`;
            document.getElementById('dt_cadastro').value = GLOBAL_DATA.dt_cadastro;
            document.getElementById('codcli').value = GLOBAL_DATA.codcli;
            document.getElementById('nome_cliente').value = GLOBAL_DATA.nome_cliente;
            document.getElementById('nome_rca').value = GLOBAL_DATA.nome_rca;
            document.getElementById('responsavel').value = GLOBAL_DATA.responsavel;
            // document.getElementById('status_garantia').value = GLOBAL_DATA.status;
            selectValueSmart('status_garantia', GLOBAL_DATA.status);
            document.getElementById('dispositivo').value = GLOBAL_DATA.dispositivo;
            renderItems(GLOBAL_DATA.itens || []);
            applyPermissions();
        } catch (e) {
            alert('Erro ao carregar: ' + e.message);
        }
    }

    async function sendRequest(scope, method, body, alertOk = true) {
        try {
            document.body.style.cursor = 'wait';
            const res = await fetch(`${API_URL}?scope=${scope}`, {
                method,
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(body)
            });
            const json = await res.json();
            if (json.success) {
                if (alertOk) alert('Salvo!');
                return {
                    success: true,
                    id: json.id
                };
            }
            throw new Error(json.message);
        } catch (e) {
            alert('Erro: ' + e.message);
            return {
                success: false
            };
        } finally {
            document.body.style.cursor = 'default';
        }
    }


    function applyPermissions() {
        const statusGeral = document.getElementById('status_garantia').value;
        document.getElementById('admin-actions-footer').style.display = IS_INTERNAL ? 'flex' : 'none';

        // AJUSTE AQUI: Adicionado 'TEL' e garantindo que apenas este botão suma
        if (['CLIENTE', 'RCA', 'TELEVENDAS', 'TEL'].includes(USER_TIPO)) {
            const btnAprov = document.getElementById('btn-save-aprovacao');
            if (btnAprov) btnAprov.style.display = 'none';
        }

        document.getElementById('status_garantia').disabled = !IS_INTERNAL;
        document.getElementById('responsavel').readOnly = !IS_INTERNAL;

        const btnsHeader = [
            'btn-save-digitacao',
            'btn-save-aprovacao',
            'btn-save-finalizar',
            'btn-novo-item',
            'btn-excluir-garantia'
        ];

        btnsHeader.forEach(id => {
            const el = document.getElementById(id);
            if (el) {
                // Atualizamos a lista aqui também para manter a consistência
                const statusPermitidos = [
                    'Em Digitação',
                    'Em Digitacao',
                    'Aberto',
                    'Pendencia do Cliente',
                    'Pendência do Cliente'
                ];
                const podeEditar = IS_INTERNAL || statusPermitidos.includes(statusGeral);
                el.disabled = !podeEditar;
            }
        });
    }


    async function dispararEmailGenerico(acao) {
        try {
            await fetch(EMAIL_GERAL_URL, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    id_garantia: ID_GARANTIA,
                    acao,
                    usuario: USER_NAME,
                    tipo_usuario: USER_TIPO,
                    codcli: GLOBAL_DATA.codcli,
                    nome_cliente: GLOBAL_DATA.nome_cliente
                })
            });
        } catch (e) {
            console.error("Erro email:", e);
        }
    }

    async function revisarGarantia() {
        if (confirm("Enviar para revisão?")) {
            await saveGarantiaHeader('Em Análise');
            await dispararEmailGenerico('revisar_garantia');
            alert("Revisão solicitada!");
        }
    }
    async function gravarGarantiaAnalista() {
        await saveGarantiaHeader();
        await dispararEmailGenerico('analista_gravou');
    }
    async function addWorkflow(itemId) {
        const detalhe = document.getElementById(`wf_new_detalhe_${itemId}`).value;
        const fileUrl = document.getElementById(`wf_temp_file_${itemId}`).value;
        if (!detalhe && !fileUrl) return alert('Digite algo.');
        const res = await sendRequest('workflow', 'POST', {
            fk_item: itemId,
            parecer_tec: "Interação",
            parecer_detalhes: detalhe,
            usuario: USER_NAME
        }, false);
        if (res.success) {
            if (fileUrl) await sendRequest('workflow_file', 'POST', {
                fk_item_work: res.id,
                anexo: fileUrl
            }, false);
            await saveGarantiaHeader('Em Análise');
            alert('Enviado!');
            await loadAllData();
        }
    }

    function openNewItemModal() {
        document.getElementById('new_codfab').value = '';
        document.getElementById('new_qtd').value = '1';
        document.getElementById('new_motivo').value = '';
        document.getElementById('new_obs').value = '';
        NEW_ITEM_FILES = [];
        document.getElementById('new_item_previews').innerHTML =
            '<small class="text-muted fst-italic">Nenhum arquivo.</small>';
        document.getElementById('modalNewItem').classList.add('active');
    }

    function handleNewItemFiles(input) {
        if (input.files.length > 0) {
            Array.from(input.files).forEach(f => NEW_ITEM_FILES.push(f));
            renderNewItemPreviews();
        }
        input.value = '';
    }

    function renderNewItemPreviews() {
        const container = document.getElementById('new_item_previews');
        container.innerHTML = '';
        NEW_ITEM_FILES.forEach((file, index) => {
            const div = document.createElement('div');
            div.className = 'attach-box';
            div.style.width = '60px';
            div.style.height = '60px';
            if (file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = (e) => div.innerHTML =
                    `<img src="${e.target.result}"><div class="attach-del" onclick="removeNewFile(${index})">×</div>`;
                reader.readAsDataURL(file);
            } else {
                div.innerHTML =
                    `<i class="fas fa-file"></i><div class="attach-del" onclick="removeNewFile(${index})">×</div>`;
            }
            container.appendChild(div);
        });
    }

    function removeNewFile(i) {
        NEW_ITEM_FILES.splice(i, 1);
        renderNewItemPreviews();
    }

    async function submitNewItem() {
        const codfab = document.getElementById('new_codfab').value;
        const qtd = document.getElementById('new_qtd').value;
        const motivo = document.getElementById('new_motivo').value;
        if (!/^[A-Z0-9]{5}-[A-Z0-9]{3}-[A-Z0-9]{4}$/.test(codfab)) return alert('Código inválido!');
        const res = await sendRequest('item', 'POST', {
            fk_garantia: ID_GARANTIA,
            codcli: GLOBAL_DATA.codcli,
            codfab,
            qtd,
            motivo,
            observacao: document.getElementById('new_obs').value,
            status: 'Em Digitacao',
            user_cad: USER_RCA
        }, false);
        if (res.success) {
            for (let file of NEW_ITEM_FILES) {
                const fd = new FormData();
                fd.append('file', file);
                const upRes = await fetch(UPLOAD_URL, {
                    method: 'POST',
                    body: fd
                });
                const upJson = await upRes.json();
                if (upJson.success) await sendRequest('anexo', 'POST', {
                    fk_item: res.id,
                    anexo: upJson.url
                }, false);
            }
            closeModal('modalNewItem');
            await loadAllData();
        }
    }

    function triggerUpload(ctx, id) {
        CURRENT_UPLOAD_CONTEXT = {
            ctx,
            id
        };
        document.getElementById('globalFileUpload').click();
    }
    document.getElementById('globalFileUpload').addEventListener('change', async function() {
        if (this.files.length === 0) return;
        const fd = new FormData();
        fd.append('file', this.files[0]);
        try {
            const res = await fetch(UPLOAD_URL, {
                method: 'POST',
                body: fd
            });
            const json = await res.json();
            if (json.success) {
                if (CURRENT_UPLOAD_CONTEXT.ctx === 'anexo') {
                    await sendRequest('anexo', 'POST', {
                        fk_item: CURRENT_UPLOAD_CONTEXT.id,
                        anexo: json.url
                    }, false);
                    await loadAllData();
                } else {
                    document.getElementById(`wf_temp_file_${CURRENT_UPLOAD_CONTEXT.id}`).value = json.url;
                    document.getElementById(`wf_file_feedback_${CURRENT_UPLOAD_CONTEXT.id}`).classList.remove(
                        'd-none');
                }
            }
        } catch (e) {
            alert('Erro upload.');
        } finally {
            this.value = '';
        }
    });

    async function deleteAnexo(id) {
        if (confirm('Excluir imagem?')) {
            const res = await sendRequest(`anexo&id=${id}`, 'DELETE', null, false);
            if (res.success) await loadAllData();
        }
    }

    function formatMoney(v) {
        return v ? parseFloat(v).toLocaleString('pt-BR', {
            minimumFractionDigits: 2
        }) : '';
    }

    function maskCurrency(i) {
        let v = i.value.replace(/\D/g, "");
        v = (parseInt(v) / 100).toLocaleString("pt-BR", {
            minimumFractionDigits: 2
        });
        i.value = v;
    }

    function parseCurrency(s) {
        return s ? parseFloat(s.replace(/\./g, '').replace(',', '.')) : 0;
    }

    function getParecerColor(s) {
        return s === 'Aprovado' ? '#cbffdf' : s === 'Reprovado' ? '#fde7e8' : s === 'Pendente' ? '#fffad8' : '#fff';
    }

    function handleCodFabInput(input) {
        let value = input.value.toUpperCase().replace(/[^A-Z0-9]/g, '');
        if (value.length > 5) value = value.substring(0, 5) + '-' + value.substring(5);
        if (value.length > 9) value = value.substring(0, 9) + '-' + value.substring(9);
        input.value = value;
        clearTimeout(searchTimeout);
        if (value.length < 3) return;
        searchTimeout = setTimeout(async () => {
            const res = await fetch(`${PRODUTO_URL}?CODFAB=${encodeURIComponent(input.value)}`);
            const json = await res.json();
            const list = document.getElementById('suggestions-list');
            list.innerHTML = '';
            if (json.success && json.data.length > 0) {
                json.data.slice(0, 6).forEach(p => {
                    const d = document.createElement('div');
                    d.className = 'suggestion-item';
                    d.innerHTML = `<span>${p.CODFAB}</span><small>${p.DESCRICAO}</small>`;
                    d.onclick = () => {
                        document.getElementById('new_codfab').value = p.CODFAB;
                        document.getElementById('feedback_produto').innerText = p.DESCRICAO;
                        list.classList.remove('active');
                    };
                    list.appendChild(d);
                });
                list.classList.add('active');
            }
        }, 300);
    }

    async function deleteItem(id) {
        if (confirm('Excluir item?')) {
            const res = await sendRequest(`item&id=${id}`, 'DELETE', null, false);
            if (res.success) await loadAllData();
        }
    }
    async function deleteGarantia() {
        if (confirm('Excluir Garantia?')) {
            const res = await sendRequest(`garantia&id=${ID_GARANTIA}`, 'DELETE', null, false);
            if (res.success) window.location.href = 'index.php?page=garantias';
        }
    }

    function toggleItem(h, id) {
        const card = h.parentElement;
        card.classList.toggle('open');
        if (card.classList.contains('open')) {
            openItemIds.add(String(id));
        } else {
            openItemIds.delete(String(id));
        }
    }

    function closeModal(id) {
        document.getElementById(id).classList.remove('active');
    }
    async function loadItemDescription(cod, id) {
        const res = await fetch(`${PRODUTO_URL}?CODFAB=${encodeURIComponent(cod)}`);
        const json = await res.json();
        if (json.success && json.data.length > 0) {
            const desc = json.data[0].DESCRICAO;

            // 1. Atualiza a tela (o que já fazia)
            document.getElementById(`desc-item-${id}`).innerText = " - " + desc;

            // 2. ADICIONE ISSO: Salva no objeto global para ser enviado no e-mail
            const itemObj = GLOBAL_DATA.itens.find(i => String(i.id) === String(id));
            if (itemObj) {
                itemObj.descricao = desc;
            }
        }
    }
</script>
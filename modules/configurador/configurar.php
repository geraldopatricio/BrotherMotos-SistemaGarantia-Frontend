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
    <title>Configurador - Sistema</title>

    <style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;700&display=swap');

    /* --- GERAL E RESPONSIVIDADE BASE --- */
    * {
        box-sizing: border-box;
        font-family: 'Inter', sans-serif;
    }

    body {
        background-color: #f5f5f9;
        margin: 0;
        padding: 10px;
        /* Menos padding no mobile */
    }

    @media (min-width: 768px) {
        body {
            padding: 25px;
        }
    }

    h4 {
        color: #333;
        margin: 0;
        font-weight: 600;
        font-size: 18px;
    }

    /* --- CONTROLES --- */
    .controls {
        display: flex;
        flex-direction: column;
        /* Stack no mobile */
        gap: 15px;
        margin-bottom: 20px;
    }

    @media (min-width: 600px) {
        .controls {
            flex-direction: row;
            justify-content: space-between;
            align-items: center;
        }
    }

    .btn-primary {
        padding: 10px 18px;
        background-color: rgb(103, 92, 248);
        color: #fff;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        font-size: 14px;
        font-weight: 500;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        transition: background 0.2s;
        width: 100%;
        /* Full width no mobile */
    }

    @media (min-width: 600px) {
        .btn-primary {
            width: auto;
        }
    }

    .btn-primary:hover {
        background-color: rgb(80, 70, 220);
    }

    /* --- CARD E TABELA RESPONSIVA --- */
    .card {
        background: #fff;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        padding: 15px;
    }

    @media (min-width: 768px) {
        .card {
            padding: 25px;
        }
    }

    /* Container para permitir scroll na tabela se o nome for muito longo no mobile */
    .table-responsive {
        width: 100%;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
        margin-top: 10px;
        border: 1px solid #e0e0e0;
        border-radius: 8px;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        min-width: 500px;
        /* Garante que não esprema demais os dados */
    }

    th,
    td {
        padding: 12px 15px;
        font-size: 13px;
        text-align: left;
        border-bottom: 1px solid #f0f0f0;
    }

    th {
        background-color: #f8f9fa;
        font-weight: 600;
        color: #666;
        text-transform: uppercase;
        font-size: 11px;
        letter-spacing: 0.5px;
    }

    /* --- BADGES --- */
    .status-badge {
        border-radius: 6px;
        padding: 4px 10px;
        font-size: 10px;
        font-weight: 700;
        display: inline-block;
        text-transform: uppercase;
        white-space: nowrap;
    }

    .badge-active {
        background-color: #e8f5e9;
        color: #2e7d32;
    }

    /* --- MODAL RESPONSIVO --- */
    .modal-overlay {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        z-index: 9999;
        justify-content: center;
        align-items: center;
        backdrop-filter: blur(3px);
        padding: 20px;
        /* Padding para o modal não encostar na borda no mobile */
    }

    .modal-content {
        background: #fff;
        padding: 20px;
        border-radius: 12px;
        width: 100%;
        max-width: 380px;
        /* Tamanho compacto no Desktop */
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
        animation: slideUp 0.3s ease-out;
    }

    @keyframes slideUp {
        from {
            transform: translateY(20px);
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
        border-bottom: 1px solid #f0f0f0;
        padding-bottom: 10px;
    }

    .modal-header h4 {
        font-size: 16px;
    }

    .form-group {
        margin-bottom: 20px;
    }

    .form-group label {
        display: block;
        margin-bottom: 8px;
        font-weight: 500;
        font-size: 13px;
        color: #555;
    }

    .form-group input {
        width: 100%;
        padding: 12px;
        border: 1px solid #ddd;
        border-radius: 8px;
        font-size: 15px;
        /* Melhor para touch no mobile */
        outline: none;
        transition: border-color 0.2s;
    }

    .form-group input:focus {
        border-color: rgb(103, 92, 248);
    }

    .modal-footer {
        display: flex;
        flex-direction: column-reverse;
        /* Botão principal em cima no mobile */
        gap: 10px;
        border-top: 1px solid #f0f0f0;
        padding-top: 15px;
    }

    @media (min-width: 480px) {
        .modal-footer {
            flex-direction: row;
            justify-content: flex-end;
        }
    }

    .btn-save {
        background-color: #2e7d32;
        color: white;
        border: none;
        padding: 12px 20px;
        border-radius: 8px;
        cursor: pointer;
        font-weight: 600;
        font-size: 14px;
    }

    .btn-cancel {
        background-color: #f8f9fa;
        color: #666;
        border: 1px solid #ddd;
        padding: 12px 20px;
        border-radius: 8px;
        cursor: pointer;
        font-size: 14px;
    }

    .loading-text {
        text-align: center;
        color: #888;
        padding: 40px;
        font-size: 14px;
    }
    </style>
</head>

<body>
    <div class="container-fluid flex-grow-1 container-p-y">
        <div class="card">
            <div class="controls">
                <div>
                    <h4>Aprovador do Sistema</h4>
                    <p style="font-size: 12px; color: #888; margin: 5px 0 0 0;">Configuração obrigatória de registro
                        único.</p>
                </div>
                <button class="btn-primary" id="btnAction" onclick="openModal()">
                    <i class="fas fa-plus"></i> Configurar
                </button>
            </div>

            <div id="contentArea">
                <div class="loading-text">
                    <i class="fas fa-spinner fa-spin"></i> Carregando...
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL -->
    <div class="modal-overlay" id="configModal">
        <div class="modal-content">
            <div class="modal-header">
                <h4 id="modalTitle">Configurar Aprovador</h4>
                <button type="button" style="border:none; background:none; font-size:24px; cursor:pointer; color:#bbb;"
                    onclick="closeModal()">&times;</button>
            </div>

            <form id="configForm">
                <div class="form-group">
                    <label for="aprovador">Nome do Aprovador</label>
                    <input type="text" id="aprovador" name="aprovador" placeholder="Ex: João Silva" required
                        autocomplete="off">
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn-cancel" onclick="closeModal()">Cancelar</button>
                    <button type="submit" class="btn-save" id="btnSave">Salvar</button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <script>
    const API_URL = '<?php echo $url_backend; ?>/routers/configurador.php';

    let currentConfig = null;

    async function fetchConfig() {
        try {
            const response = await fetch(API_URL);
            const result = await response.json();

            if (result.success) {
                currentConfig = result.data;
                renderContent();
            } else {
                showError("Erro ao obter dados.");
            }
        } catch (error) {
            showError("Servidor offline ou erro de rede.");
        }
    }

    function renderContent() {
        const container = document.getElementById('contentArea');
        const btnAction = document.getElementById('btnAction');

        if (!currentConfig) {
            btnAction.innerHTML = '<i class="fas fa-plus"></i> Novo Registro';
            container.innerHTML = `
                <div style="text-align: center; padding: 40px; border: 2px dashed #eee; border-radius: 12px; margin-top:10px;">
                    <i class="fas fa-user-shield" style="font-size: 30px; color: #ddd; margin-bottom: 10px;"></i>
                    <p style="color: #999; font-size:13px;">Nenhum aprovador definido.</p>
                </div>`;
            return;
        }

        btnAction.innerHTML = '<i class="fas fa-edit"></i> Editar';
        container.innerHTML = `
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th width="80">ID</th>
                            <th>NOME DO APROVADOR</th>
                            <th width="150">STATUS</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>#${currentConfig.id}</td>
                            <td><strong>${currentConfig.aprovador}</strong></td>
                            <td><span class="status-badge badge-active">Ativo</span></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        `;
    }

    function openModal() {
        if (currentConfig) {
            document.getElementById('modalTitle').innerText = 'Editar Aprovador';
            document.getElementById('aprovador').value = currentConfig.aprovador;
        } else {
            document.getElementById('modalTitle').innerText = 'Novo Aprovador';
            document.getElementById('aprovador').value = '';
        }
        document.getElementById('configModal').style.display = 'flex';
        document.getElementById('aprovador').focus();
    }

    function closeModal() {
        document.getElementById('configModal').style.display = 'none';
    }

    document.getElementById('configForm').addEventListener('submit', async (e) => {
        e.preventDefault();
        const btnSave = document.getElementById('btnSave');
        const aprovadorValue = document.getElementById('aprovador').value;

        btnSave.disabled = true;
        btnSave.innerText = 'Processando...';

        const method = currentConfig ? 'PUT' : 'POST';

        try {
            const response = await fetch(API_URL, {
                method: method,
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    aprovador: aprovadorValue
                })
            });

            const result = await response.json();

            if (result.success) {
                closeModal();
                fetchConfig();
            } else {
                alert(result.message);
            }
        } catch (error) {
            alert("Erro na comunicação com o servidor.");
        } finally {
            btnSave.disabled = false;
            btnSave.innerText = 'Salvar';
        }
    });

    function showError(msg) {
        document.getElementById('contentArea').innerHTML =
            `<div class="loading-text" style="color:#c62828;">${msg}</div>`;
    }

    document.addEventListener('DOMContentLoaded', fetchConfig);

    window.onclick = function(event) {
        if (event.target == document.getElementById('configModal')) closeModal();
    }
    </script>
</body>

</html>
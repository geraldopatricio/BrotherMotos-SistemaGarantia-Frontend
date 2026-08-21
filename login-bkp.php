<?php

ini_set('session.cookie_path', '/');

session_start();

if (isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'] === true) {
    header('Location: dashboard/garantias.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login de Acesso</title>
    <!-- Fontes e Ícones -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

    <style>
    /* --- ESTILOS GERAIS E FUNDO --- */
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        font-family: 'Poppins', sans-serif;
    }

    body {
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 100vh;
        background: linear-gradient(to top, #e6e9f0 0%, #eef1f5 100%);
        background: rgb(167, 195, 240);
        background: -webkit-linear-gradient(to right, #65C7F7, rgb(202, 207, 228), rgb(137, 177, 240));
        background: linear-gradient(to right, #65C7F7, rgb(219, 224, 243), rgb(178, 203, 243));
    }

    /* --- CONTAINER PRINCIPAL DO LOGIN --- */
    .login-container {
        width: 100%;
        max-width: 400px;
        background-color: #fff;
        padding: 40px 30px;
        border-radius: 20px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        text-align: center;
        position: relative;
        /* Adicionado para posicionamento do GIF */
        z-index: 1;
        /* Garante que o container esteja abaixo do overlay */
    }

    .login-container h2 {
        margin-bottom: 25px;
        color: #333;
        font-weight: 600;
    }

    /* --- ESTILO "MATERIAL UI" PARA INPUTS --- */
    .input-group {
        position: relative;
        margin-bottom: 30px;
    }

    .input-group input {
        width: 100%;
        padding: 10px 5px;
        border: none;
        border-bottom: 2px solid #ccc;
        background-color: transparent;
        font-size: 16px;
        outline: none;
        color: #333;
    }

    .input-group label {
        position: absolute;
        top: 50%;
        left: 5px;
        transform: translateY(-50%);
        font-size: 16px;
        color: #aaa;
        pointer-events: none;
        transition: all 0.3s ease;
    }

    /* Animação do Label */
    .input-group input:focus+label,
    .input-group input:valid+label {
        top: -5px;
        font-size: 12px;
        color: #4364F7;
    }

    .input-group input:focus {
        border-bottom-color: #4364F7;
    }

    /* Ícone do Olho para Senha */
    .password-group {
        position: relative;
    }

    .password-group .toggle-password {
        position: absolute;
        top: 50%;
        right: 10px;
        transform: translateY(-50%);
        cursor: pointer;
        color: #aaa;
    }

    /* --- BOTÕES E LINKS --- */
    .btn-submit {
        width: 100%;
        padding: 12px;
        border: none;
        border-radius: 8px;
        background: linear-gradient(to right, #4364F7, #0052D4);
        color: white;
        font-size: 16px;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .btn-submit:hover {
        opacity: 0.9;
        box-shadow: 0 5px 15px rgba(67, 100, 247, 0.4);
    }

    .links {
        margin-top: 20px;
        display: flex;
        justify-content: space-around;
        flex-wrap: wrap;
    }

    .links a {
        color: #4364F7;
        text-decoration: none;
        font-size: 14px;
        transition: color 0.3s ease;
        margin: 5px;
    }

    .links a:hover {
        text-decoration: underline;
        color: #0052D4;
    }

    /* --- ESTILOS DOS MODAIS --- */
    .modal {
        display: none;
        position: fixed;
        z-index: 1000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        overflow: auto;
        background-color: rgba(0, 0, 0, 0.5);
        align-items: center;
        justify-content: center;
    }

    .modal.active {
        display: flex;
    }

    .modal-content {
        background-color: #fefefe;
        margin: auto;
        padding: 20px 30px 30px 30px;
        border-radius: 15px;
        width: 90%;
        max-width: 450px;
        position: relative;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.2);
    }

    .modal-header {
        padding: 15px 20px;
        margin: -20px -30px 25px -30px;
        background-color: #0052D4;
        color: white;
        border-radius: 15px 15px 0 0;
        text-align: center;
    }

    .modal-footer {
        padding: 10px 20px;
        margin: 25px -30px -30px -30px;
        background-color: #f1f1f1;
        border-radius: 0 0 15px 15px;
        text-align: right;
    }

    .close-button {
        color: white;
        position: absolute;
        top: 10px;
        right: 20px;
        font-size: 28px;
        font-weight: bold;
        cursor: pointer;
    }

    .close-button:hover {
        color: #ccc;
    }

    /* --- NOTIFICAÇÕES (TOAST) --- */
    #toast-container {
        position: fixed;
        top: 20px;
        left: 50%;
        transform: translateX(-50%);
        z-index: 2000;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 10px;
    }

    .toast {
        padding: 12px 20px;
        border-radius: 8px;
        color: #fff;
        font-size: 15px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        opacity: 0;
        animation: slideDown 0.5s forwards;
    }

    @keyframes slideDown {
        from {
            opacity: 0;
            transform: translateY(-50px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .toast.fade-out {
        animation: fadeOut 0.5s forwards;
    }

    @keyframes fadeOut {
        from {
            opacity: 1;
        }

        to {
            opacity: 0;
        }
    }

    .toast.success {
        background-color: #28a745;
    }

    .toast.error {
        background-color: #dc3545;
    }

    .toast.warning {
        background-color: #ffc107;
        color: #333;
    }

    .toast.info {
        background-color: #17a2b8;
    }

    .input-group {
        position: relative;
    }

    .fa-search {
        position: absolute;
        right: 10px;
        top: 50%;
        transform: translateY(-50%);
        cursor: pointer;
        color: #555;
    }

    /* --- ESTILOS DO OVERLAY E GIF --- */
    #gif-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(255, 255, 255, 0.9);
        /* Fundo semi-transparente branco */
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 9999;
        opacity: 0;
        visibility: hidden;
        transition: opacity 0.5s ease, visibility 0.5s ease;
    }

    #gif-overlay.show {
        opacity: 1;
        visibility: visible;
    }

    #gif-overlay img {
        width: 300px;
        /* Largura desejada para o GIF */
        height: auto;
        border-radius: 10px;
        /* Bordas arredondadas para o GIF */
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.3);
        /* Sombra para destacar */
    }
    </style>
</head>

<body>

    <!-- Overlay para o GIF de carregamento -->
    <div id="gif-overlay">
        <img src="assets/img/moto.gif" alt="Carregando...">
    </div>

    <div class="login-container">
        <img src="assets/img/logo.png" alt="Logo" style="width: 150px; margin-bottom: 20px;">
        <p style="color: #555; font-size: 14px; margin-bottom: 20px;">Bem-vindo ao SYSGAR<br>Sistema de Garantias
            BrotherMotos</p>
        <br>
        <h2>Área de Acesso</h2>
        <form id="loginForm">
            <div class="input-group">
                <input type="text" id="loginUsuario" name="usuario" required>
                <label for="loginUsuario">Usuário</label>
            </div>
            <div class="input-group password-group">
                <input type="password" id="loginSenha" name="senha" autocomplete="off" required>
                <label for="loginSenha">Senha</label>
                <i class="fas fa-eye toggle-password" data-target="loginSenha"></i>
            </div>
            <button type="submit" class="btn-submit">Entrar</button>
        </form>
        <div class="links">
            <a href="#" class="modal-trigger" data-modal-target="#modalTrocarSenha">Trocar Senha</a>
            <a href="#" class="modal-trigger" data-modal-target="#modalEnviarSenha">Esqueci a Senha</a>
            <a href="#" class="modal-trigger" data-modal-target="#modalCadastro">Cadastrar-se</a>
        </div>
    </div>

    <div id="toast-container"></div>

    <div id="modalTrocarSenha" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Alterar Senha</h2>
                <span class="close-button">×</span>
            </div>
            <form id="trocaSenhaForm">
                <div class="input-group">
                    <input type="text" id="trocaUsuario" name="usuario" required>
                    <label for="trocaUsuario">Seu Usuário</label>
                </div>
                <div class="input-group password-group">
                    <input type="password" id="senhaAntiga" name="senha_antiga" required>
                    <label for="senhaAntiga">Senha Atual</label>
                    <i class="fas fa-eye toggle-password" data-target="senhaAntiga"></i>
                </div>
                <div class="input-group password-group">
                    <input type="password" id="senhaNova" name="senha_nova" required>
                    <label for="senhaNova">Nova Senha</label>
                    <i class="fas fa-eye toggle-password" data-target="senhaNova"></i>
                </div>
                <div class="input-group password-group">
                    <input type="password" id="senhaConfirma" name="senha_confirma" required>
                    <label for="senhaConfirma">Confirmar Nova Senha</label>
                    <i class="fas fa-eye toggle-password" data-target="senhaConfirma"></i>
                </div>
                <button type="submit" class="btn-submit">Alterar</button>
            </form>
        </div>
    </div>

    <div id="modalEnviarSenha" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Recuperar Senha</h2>
                <span class="close-button">×</span>
            </div>
            <p style="text-align:center; margin: 15px 0; color: #555;">Informe seu e-mail para receber uma nova senha.
            </p>
            <form id="enviarSenhaForm">
                <div class="input-group">
                    <input type="email" id="recuperaEmail" name="email" required>
                    <label for="recuperaEmail">E-mail de Cadastro</label>
                </div>
                <button type="submit" class="btn-submit">Enviar</button>
            </form>
        </div>
    </div>

    <div id="modalCadastro" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Novo Cadastro</h2>
                <span class="close-button">×</span>
            </div>
            <form id="cadastroForm" style="max-height: 50vh; overflow-y: auto; padding-right: 15px;">
                <!-- Campos ocultos para uso interno -->
                <input type="hidden" id="cadCodusur" name="codusur">
                <input type="hidden" id="cadCodcli" name="codcli">
                <p>Este é apenas um pré-cadastro, que será analisado pelo time técnico de <b>prevenção a fraude</b> para
                    ser aprovado ou não.</p>
                <br>
                <!-- Campo CNPJ com pesquisa -->
                <div class="input-group">
                    <input type="text" id="cadCnpj" name="cnpj" required>
                    <label for="cadCnpj">CNPJ do Cliente ou Representante</label>
                    <i class="fas fa-search cnpj-search" id="cnpjSearchBtn"></i>
                </div>

                <div class="input-group"><input type="text" id="cadUsuario" name="usuario" required><label
                        for="cadUsuario">Nome de Usuário</label></div>
                <div class="input-group password-group">
                    <input type="password" id="cadSenha" name="senha" required>
                    <label for="cadSenha">Senha</label>
                    <i class="fas fa-eye toggle-password" data-target="cadSenha"></i>
                </div>
                <div class="input-group password-group">
                    <input type="password" id="cadSenhaConfirma" name="senha_confirma" required>
                    <label for="cadSenhaConfirma">Confirmar Senha</label>
                    <i class="fas fa-eye toggle-password" data-target="cadSenhaConfirma"></i>
                </div>
                <div class="input-group"><input type="email" id="cadEmail" name="email" required><label
                        for="cadEmail">E-mail</label></div>
                <div class="input-group"><input type="text" id="cadZap" name="zap"><label for="cadZap">WhatsApp
                        (Opcional)</label></div>
                <div class="input-group"><input type="text" id="cadTipo" name="tipo" required><label for="cadTipo">Tipo
                        de Usuário</label></div>

                <button type="submit" class="btn-submit">Cadastrar</button>
            </form>
        </div>
    </div>

    <script>
    function clearPasswordField() {
        document.getElementById("password").value = "";
    }


    document.addEventListener('DOMContentLoaded', () => {

        const API_BASE_URL = '../backend/routers/';
        const gifOverlay = document.getElementById('gif-overlay');

        function showToast(message, type = 'success', duration = 4000) {
            const container = document.getElementById('toast-container');
            const toast = document.createElement('div');
            toast.className = `toast ${type}`;
            toast.textContent = message;

            container.appendChild(toast);

            setTimeout(() => {
                toast.classList.add('fade-out');
                toast.addEventListener('animationend', () => toast.remove());
            }, duration);
        }

        const modalTriggers = document.querySelectorAll('.modal-trigger');
        const modals = document.querySelectorAll('.modal');
        const closeButtons = document.querySelectorAll('.close-button');

        modalTriggers.forEach(trigger => {
            trigger.addEventListener('click', (e) => {
                e.preventDefault();
                const modalId = trigger.getAttribute('data-modal-target');
                document.querySelector(modalId).classList.add('active');
            });
        });

        function closeModal() {
            modals.forEach(modal => modal.classList.remove('active'));
        }

        closeButtons.forEach(button => button.addEventListener('click', closeModal));

        modals.forEach(modal => {
            modal.addEventListener('click', (e) => {
                if (e.target === modal) {
                    closeModal();
                }
            });
        });

        document.querySelectorAll('.toggle-password').forEach(toggle => {
            toggle.addEventListener('click', () => {
                const targetId = toggle.getAttribute('data-target');
                const passwordInput = document.getElementById(targetId);

                if (passwordInput.type === 'password') {
                    passwordInput.type = 'text';
                    toggle.classList.remove('fa-eye');
                    toggle.classList.add('fa-eye-slash');
                } else {
                    passwordInput.type = 'password';
                    toggle.classList.remove('fa-eye-slash');
                    toggle.classList.add('fa-eye');
                }
            });
        });

        const handleFormSubmit = async (url, body) => {
            try {
                const response = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(body)
                });
                return await response.json();
            } catch (error) {
                console.error('Fetch Error:', error);
                return {
                    success: false,
                    message: 'Erro de comunicação com o servidor.'
                };
            }
        };

        document.getElementById('loginForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const formData = new FormData(e.target);
            const data = Object.fromEntries(formData.entries());

            const result = await handleFormSubmit(`${API_BASE_URL}login`, data);

            if (result.success) {
                // Exibe o GIF de carregamento
                gifOverlay.classList.add('show');
                setTimeout(() => {
                    window.location.href =
                        'index.php'; // direcionar para dashboard assim que logar-se
                }, 2000);
            } else {
                showToast(result.message, 'error');
            }
        });

        document.getElementById('trocaSenhaForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const formData = new FormData(e.target);
            const data = Object.fromEntries(formData.entries());

            if (data.senha_nova !== data.senha_confirma) {
                showToast('A nova senha e a confirmação não coincidem.', 'error');
                return;
            }

            const result = await handleFormSubmit(`${API_BASE_URL}trocaSenha.php`, {
                usuario: data.usuario,
                senha_antiga: data.senha_antiga,
                senha_nova: data.senha_nova
            });

            if (result.success) {
                showToast(result.message, 'success');
                e.target.reset();
                closeModal();
            } else {
                showToast(result.message, 'error');
            }
        });

        document.getElementById('enviarSenhaForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const formData = new FormData(e.target);
            const data = Object.fromEntries(formData.entries());

            showToast('Processando sua solicitação...', 'info');
            const result = await handleFormSubmit(`${API_BASE_URL}enviarSenha.php`, data);

            // Sempre mostramos a mesma mensagem por segurança
            showToast(result.message, 'info');
            e.target.reset();
            closeModal();
        });

        document.getElementById('cadastroForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const formData = new FormData(e.target);
            const data = Object.fromEntries(formData.entries());

            if (data.senha !== data.senha_confirma) {
                showToast('A senha e a confirmação não coincidem.', 'error');
                return;
            }

            // Preparando o objeto no formato esperado pelo endpoint
            const payload = {
                codusur: data.codusur,
                codcli: data.codcli,
                usuario: data.usuario,
                senha: data.senha,
                email: data.email,
                tipo: data.tipo,
                cnpj: data.cnpj,
                zap: data.zap || '' // Envia vazio se não tiver WhatsApp
            };

            const result = await handleFormSubmit(`${API_BASE_URL}usersAdd.php`, payload);

            if (result.success) {
                showToast(result.message, 'success');
                e.target.reset();
                closeModal();
            } else {
                showToast(result.message, 'error');
            }
        });

        document.getElementById('cnpjSearchBtn').addEventListener('click', async (e) => {
            e.preventDefault();
            const cnpj = document.getElementById('cadCnpj').value.trim();

            if (!cnpj) {
                showToast('Por favor, informe um CNPJ válido.', 'error');
                return;
            }

            try {
                showToast('Buscando informações do CNPJ...', 'info');

                // Primeiro tenta buscar no endpoint de Cliente
                const clienteResponse = await fetch(
                    `../backend/routers/consultaClienteOra?cgcent=${cnpj}`);
                const clienteData = await clienteResponse.json();

                if (clienteData.success && clienteData.count > 0) {
                    // Preenche os campos com dados do cliente
                    const cliente = clienteData.data[0];
                    document.getElementById('cadCodusur').value = cliente.CODUSUR1 || '';
                    document.getElementById('cadCodcli').value = cliente.CODCLI || '';
                    document.getElementById('cadCnpj').value = cliente.CGCENT || '';
                    document.getElementById('cadEmail').value = cliente.EMAIL || '';
                    document.getElementById('cadZap').value = cliente.TELENT || '';
                    document.getElementById('cadTipo').value = 'Cliente';

                    // Define o nome de usuário como o nome da empresa (sem espaços e caracteres especiais)
                    const username = cliente.CLIENTE ?
                        cliente.CLIENTE.toLowerCase().split(' ')
                        .filter(Boolean) // Remove itens vazios
                        .slice(0, 2) // Pega apenas os 2 primeiros nomes
                        .join('.') // Junta com ponto
                        .replace(/[^a-z0-9.]/g, '') // Remove caracteres inválidos
                        :
                        '';
                    document.getElementById('cadUsuario').value = username;

                    showToast('Dados do cliente carregados com sucesso!', 'success');
                    return;
                }

                // Se não encontrou no endpoint de Cliente, tenta no endpoint de RCA
                const rcaResponse = await fetch(`../backend/routers/consultaRCAOra?cgc=${cnpj}`);
                const rcaData = await rcaResponse.json();

                if (rcaData.success && rcaData.count > 0) {
                    // Preenche os campos com dados do RCA
                    const rca = rcaData.data[0];
                    document.getElementById('cadCodusur').value = rca.CODUSUR || '';
                    document.getElementById('cadCodcli').value = '';
                    document.getElementById('cadCnpj').value = rca.CGC || '';
                    document.getElementById('cadEmail').value = rca.EMAIL || '';
                    document.getElementById('cadZap').value = rca.TELEFONE1 || '';
                    document.getElementById('cadTipo').value = 'RCA';

                    // Define o nome de usuário como o nome da RCA (sem espaços e caracteres especiais)
                    const username = rca.NOME ?
                        rca.NOME.toLowerCase().split(' ')
                        .filter(Boolean)
                        .slice(0, 2)
                        .join('.')
                        .replace(/[^a-z0-9.]/g, '') :
                        '';
                    document.getElementById('cadUsuario').value = username;

                    showToast('Dados do RCA carregados com sucesso!', 'success');
                    return;
                }

                showToast('CNPJ não encontrado nos sistemas.', 'error');

            } catch (error) {
                console.error('Erro ao buscar CNPJ:', error);
                showToast('Erro ao consultar CNPJ. Tente novamente.', 'error');
            }
        });
    });
    </script>

</body>

</html>

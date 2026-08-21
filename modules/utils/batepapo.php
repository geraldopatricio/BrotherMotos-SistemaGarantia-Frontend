<?php
// Inicia sessão para pegar a variável, caso o navbar não inicie
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Simulação de sessão para teste (REMOVA EM PRODUÇÃO se já vier do login)
// $_SESSION['user_usuario'] = 'Admin'; 

if (!isset($_SESSION['user_usuario'])) {
    echo "Erro: Usuário não logado.";
    exit;
}

$meuUsuario = $_SESSION['user_usuario'];
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat Interno</title>

    <!-- Inclua seu Navbar aqui -->
    <?php include 'navbar.php'; ?>

    <style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;700&display=swap');

    * {
        box-sizing: border-box;
        font-family: 'Inter', sans-serif;
    }

    body {
        background-color: #f5f5f9;
        margin: 0;
        /* Se o navbar ocupar espaço, ajuste o padding-top aqui */
        height: 100vh;
        display: flex;
        flex-direction: column;
    }

    /* Layout Principal do Chat */
    .chat-container {
        flex: 1;
        display: flex;
        margin: 20px;
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
        overflow: hidden;
        border: 1px solid #e0e0e0;
        height: calc(100vh - 100px);
        /* Ajuste conforme altura do navbar */
    }

    /* --- SIDEBAR (LISTA DE USUÁRIOS) --- */
    .sidebar {
        width: 280px;
        background-color: #fff;
        border-right: 1px solid #eee;
        display: flex;
        flex-direction: column;
    }

    .sidebar-header {
        padding: 20px;
        border-bottom: 1px solid #eee;
        background-color: #f8f9fa;
    }

    .sidebar-header h4 {
        margin: 0;
        font-size: 16px;
        color: #333;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .user-list {
        flex: 1;
        overflow-y: auto;
        padding: 10px;
    }

    .user-item {
        padding: 12px 15px;
        margin-bottom: 5px;
        border-radius: 8px;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 10px;
        transition: background 0.2s;
    }

    .user-item:hover {
        background-color: #f3f4f6;
    }

    .user-item.active {
        background-color: rgb(235, 233, 255);
        border: 1px solid rgb(103, 92, 248);
    }

    .avatar-circle {
        width: 35px;
        height: 35px;
        background-color: rgb(103, 92, 248);
        color: white;
        border-radius: 50%;
        display: flex;
        justify-content: center;
        align-items: center;
        font-weight: 600;
        font-size: 14px;
    }

    .user-info {
        display: flex;
        flex-direction: column;
    }

    .user-name {
        font-size: 14px;
        font-weight: 500;
        color: #333;
    }

    .user-status {
        font-size: 11px;
        color: #2e7d32;
        /* Verde */
        display: flex;
        align-items: center;
        gap: 4px;
    }

    .status-dot {
        width: 8px;
        height: 8px;
        background-color: #2e7d32;
        border-radius: 50%;
    }

    /* --- ÁREA DO CHAT --- */
    .chat-area {
        flex: 1;
        display: flex;
        flex-direction: column;
        background-color: #fff;
    }

    .chat-header {
        padding: 15px 20px;
        border-bottom: 1px solid #eee;
        background-color: #fff;
        height: 60px;
        display: flex;
        align-items: center;
    }

    .chat-header h3 {
        margin: 0;
        font-size: 16px;
        color: #333;
    }

    /* Área vazia antes de selecionar */
    .empty-state {
        flex: 1;
        display: flex;
        justify-content: center;
        align-items: center;
        color: #999;
        font-size: 14px;
        flex-direction: column;
        gap: 10px;
    }

    .messages-box {
        flex: 1;
        padding: 20px;
        overflow-y: auto;
        background-color: #fafafa;
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    /* BALÕES DE MENSAGEM */
    .msg-row {
        display: flex;
        width: 100%;
    }

    .msg-row.sent {
        justify-content: flex-end;
    }

    .msg-row.received {
        justify-content: flex-start;
    }

    .msg-bubble {
        max-width: 70%;
        padding: 10px 14px;
        border-radius: 12px;
        font-size: 13px;
        line-height: 1.4;
        position: relative;
    }

    .msg-row.sent .msg-bubble {
        background-color: rgb(103, 92, 248);
        color: white;
        border-bottom-right-radius: 2px;
    }

    .msg-row.received .msg-bubble {
        background-color: #e9ecef;
        color: #333;
        border-bottom-left-radius: 2px;
    }

    .msg-time {
        font-size: 10px;
        margin-top: 4px;
        opacity: 0.7;
        text-align: right;
        display: block;
    }

    /* INPUT AREA */
    .input-area {
        padding: 15px;
        border-top: 1px solid #eee;
        background-color: #fff;
        display: flex;
        gap: 10px;
    }

    .input-area input {
        flex: 1;
        padding: 10px 15px;
        border: 1px solid #ddd;
        border-radius: 20px;
        outline: none;
        transition: border 0.2s;
    }

    .input-area input:focus {
        border-color: rgb(103, 92, 248);
    }

    .btn-send {
        background-color: rgb(103, 92, 248);
        color: white;
        border: none;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        cursor: pointer;
        display: flex;
        justify-content: center;
        align-items: center;
        font-size: 16px;
    }

    .btn-send:hover {
        background-color: rgb(83, 72, 228);
    }

    /* Scrollbar bonitinha */
    ::-webkit-scrollbar {
        width: 6px;
    }

    ::-webkit-scrollbar-track {
        background: #f1f1f1;
    }

    ::-webkit-scrollbar-thumb {
        background: #ccc;
        border-radius: 3px;
    }
    </style>
</head>

<body>

    <div class="chat-container">

        <!-- SIDEBAR -->
        <div class="sidebar">
            <div class="sidebar-header">
                <h4><i class="fas fa-users"></i> Online Agora</h4>
                <div style="font-size:11px; color:#666; margin-top:5px;">
                    Você: <b><?php echo htmlspecialchars($meuUsuario); ?></b>
                </div>
            </div>
            <div class="user-list" id="userList">
                <div style="padding:15px; text-align:center; color:#999; font-size:12px;">Carregando usuários...</div>
            </div>
        </div>

        <!-- AREA DE CHAT -->
        <div class="chat-area">

            <!-- Estado Inicial (Sem seleção) -->
            <div id="noChatSelected" class="empty-state">
                <i class="fas fa-comments" style="font-size: 40px; color: #ddd;"></i>
                <p>Selecione um usuário na lateral para iniciar o bate-papo.</p>
            </div>

            <!-- Conversa Ativa -->
            <div id="chatWindow" style="display:none; flex:1; flex-direction:column; height:100%;">
                <div class="chat-header">
                    <div class="avatar-circle" id="headerAvatar"
                        style="margin-right:10px; width:30px; height:30px; font-size:12px;">U</div>
                    <h3 id="chatWithUser">Usuário</h3>
                </div>

                <div class="messages-box" id="messagesBox">
                    <!-- Mensagens entram aqui via JS -->
                </div>

                <form class="input-area" id="msgForm">
                    <input type="text" id="msgInput" placeholder="Digite sua mensagem..." autocomplete="off">
                    <button type="submit" class="btn-send"><i class="fas fa-paper-plane"></i></button>
                </form>
            </div>

        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <script>
    $(document).ready(function() {
        const currentUser = "<?php echo $meuUsuario; ?>";
        let selectedUser = null;
        let pollingInterval = null;

        // URL da API
        const API_URL = '../backend/routers/chat_api.php';

        // 1. ATUALIZA LISTA DE USUÁRIOS ONLINE (A cada 3 segundos)
        function updateOnlineUsers() {
            $.post(API_URL, {
                action: 'heartbeat'
            }, function(response) {
                const data = JSON.parse(response);
                const users = data.users;

                const $list = $('#userList');
                // Não limpamos tudo para não perder o scroll, vamos atualizar inteligentemente
                // Mas para simplicidade deste exemplo, vamos refazer o HTML

                if (users.length === 0) {
                    $list.html(
                        '<div style="padding:15px; text-align:center; color:#999; font-size:12px;">Ninguém mais online.</div>'
                    );
                    return;
                }

                let html = '';
                users.forEach(u => {
                    const activeClass = (u === selectedUser) ? 'active' : '';
                    const initial = u.charAt(0).toUpperCase();

                    html += `
                        <div class="user-item ${activeClass}" onclick="selectUser('${u}')">
                            <div class="avatar-circle">${initial}</div>
                            <div class="user-info">
                                <span class="user-name">${u}</span>
                                <span class="user-status"><div class="status-dot"></div> Online</span>
                            </div>
                        </div>
                    `;
                });
                $list.html(html);

            }).fail(function() {
                console.log("Erro ao conectar no servidor de chat.");
            });
        }

        // Inicia o heartbeat
        updateOnlineUsers();
        setInterval(updateOnlineUsers, 3000);

        // 2. SELECIONAR USUÁRIO
        window.selectUser = function(user) {
            selectedUser = user;
            $('#noChatSelected').hide();
            $('#chatWindow').css('display', 'flex');
            $('#chatWithUser').text(user);
            $('#headerAvatar').text(user.charAt(0).toUpperCase());

            // Renderiza visualmente a seleção na sidebar
            updateOnlineUsers();

            // Carrega mensagens imediatamente
            loadMessages();

            // Foca no input
            $('#msgInput').focus();
        };

        // 3. CARREGAR MENSAGENS (Polling a cada 2 segundos se houver user selecionado)
        function loadMessages() {
            if (!selectedUser) return;

            $.post(API_URL, {
                action: 'fetch_chat',
                target: selectedUser
            }, function(response) {
                const data = JSON.parse(response);
                const messages = data.messages;
                const $box = $('#messagesBox');

                // Armazena a posição atual do scroll
                // Lógica simples: Se o usuário estiver no fim, auto-scroll. Se subiu para ler histórico, não desce.
                const isAtBottom = $box.scrollTop() + $box.innerHeight() >= $box[0].scrollHeight - 50;

                let html = '';
                messages.forEach(msg => {
                    const type = msg.is_me ? 'sent' : 'received';
                    html += `
                        <div class="msg-row ${type}">
                            <div class="msg-bubble">
                                ${msg.msg}
                                <span class="msg-time">${msg.date} ${msg.time}</span>
                            </div>
                        </div>
                    `;
                });

                // Só atualiza o DOM se mudou algo (para evitar flash), mas aqui vamos simplificar substituindo
                // Uma melhoria seria verificar se o HTML mudou antes de dar replace
                if ($box.html().length !== html.length) {
                    $box.html(html);
                    // Scroll para o fim se necessário
                    $box.scrollTop($box[0].scrollHeight);
                }

            });
        }

        // Loop de mensagens
        setInterval(function() {
            if (selectedUser) loadMessages();
        }, 2000);

        // 4. ENVIAR MENSAGEM
        $('#msgForm').on('submit', function(e) {
            e.preventDefault();
            const msg = $('#msgInput').val().trim();

            if (!msg || !selectedUser) return;

            // Limpa input imediatamente
            $('#msgInput').val('');

            $.post(API_URL, {
                action: 'send',
                target: selectedUser,
                message: msg
            }, function(response) {
                // Ao enviar, força recarregamento imediato para ver sua mensagem
                loadMessages();
            });
        });
    });
    </script>

</body>

</html>
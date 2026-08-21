<?php
// $envPath = __DIR__ . '/../backend/.env'; // Caminho relativo do frontend para o backend
// if (file_exists($envPath)) {
//     $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
//     foreach ($lines as $line) {
//         if (strpos(trim($line), '#') === 0) continue; // Pula comentários
//         list($name, $value) = explode('=', $line, 2);
//         $_ENV[trim($name)] = trim($value);
//     }
// }

// $url_frontend = $_ENV['URL_FRONTEND'];
?>

<footer class="content-footer footer bg-footer-theme">
    <div class="container-fluid">
        <div class="footer-container d-flex align-items-center justify-content-between py-4 flex-md-row flex-column">
            <div class="text-body">
                ©
                <script>
                document.write(new Date().getFullYear());
                </script> by <a href="https://brothermotos.com.br" target="_blank" class="footer-link">BrotherMotos</a>
            </div>
            <div class="d-none d-lg-inline-block">
                <a href="https://brothermotos.com.br" class="footer-link me-4" target="_blank">Licença de uso</a>
                <a href="https://brothermotos.com.br/" class="footer-link me-4" target="_blank">Documentação</a>
                <a href="https://brothermotos.com.br/" class="footer-link me-4" target="_blank">Suporte</a>
            </div>
        </div>
    </div>
</footer>

<!-- Botão Mensagem Flutuante -->
<div class="whatsapp-float" id="whatsappFloat">
    <div class="whatsapp-icon">
        <svg width="30" height="30" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path
                d="M20 2H4C2.9 2 2.01 2.9 2.01 4L2 22L6 18H20C21.1 18 22 17.1 22 16V4C22 2.9 21.1 2 20 2ZM20 16H5.17L4 17.17V4H20V16Z"
                fill="white" />
            <path d="M7 9H17V11H7V9ZM7 12H15V14H7V12Z" fill="white" />
        </svg>
        <div class="pulse-effect"></div>
    </div>
</div>

<!-- Modal do Chatbot -->
<div class="whatsapp-modal" id="whatsappModal">
    <div class="modal-content">
        <div class="modal-header">
            <h5>Chat de Atendimento</h5>
            <button class="close-btn" id="closeModal">&times;</button>
        </div>
        <div class="modal-body">
            <div class="chat-container">
                <div class="chat-messages" id="chatMessages">
                    <!-- Mensagens serão adicionadas aqui via JavaScript -->
                </div>
                <div class="chat-input-container">
                    <input type="text" id="chatInput" placeholder="Digite sua resposta..." autocomplete="off">
                    <button id="sendChatBtn">Enviar</button>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="google_translate_element" style="position:absolute; top:-9999px; left:-9999px;"></div>

<style>
.goog-te-banner-frame {
    display: none !important;
}

body {
    top: 0px !important;
    position: static !important;
}

.goog-logo-link,
.goog-te-gadget {
    display: none !important;
}

/* Estilos do Botão Mensagem */
.whatsapp-float {
    position: fixed;
    bottom: 80px;
    right: 20px;
    z-index: 1000;
    cursor: pointer;
    animation: float 3s ease-in-out infinite;
}

.whatsapp-icon {
    width: 60px;
    height: 60px;
    background: #007bff;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 4px 20px rgba(0, 123, 255, 0.4);
    position: relative;
    transition: all 0.3s ease;
}

.whatsapp-icon:hover {
    transform: scale(1.1);
    box-shadow: 0 6px 25px rgba(0, 123, 255, 0.6);
    background: #0056b3;
}

.pulse-effect {
    position: absolute;
    width: 100%;
    height: 100%;
    border-radius: 50%;
    background: #007bff;
    animation: pulse 2s infinite;
    z-index: -1;
}

@keyframes float {

    0%,
    100% {
        transform: translateY(0);
    }

    50% {
        transform: translateY(-10px);
    }
}

@keyframes pulse {
    0% {
        transform: scale(1);
        opacity: 1;
    }

    70% {
        transform: scale(1.5);
        opacity: 0;
    }

    100% {
        transform: scale(1.5);
        opacity: 0;
    }
}

/* Estilos do Modal e Chat - AGORA NO CANTO INFERIOR DIREITO */
.whatsapp-modal {
    display: none;
    position: fixed;
    bottom: 150px;
    /* Posicionado acima do botão */
    right: 20px;
    /* Alinhado com o botão */
    width: 380px;
    height: 500px;
    z-index: 1001;
    animation: modalSlideIn 0.3s ease;
}

@keyframes modalSlideIn {
    from {
        opacity: 0;
        transform: translateY(20px) scale(0.9);
    }

    to {
        opacity: 1;
        transform: translateY(0) scale(1);
    }
}

.modal-content {
    background: white;
    border-radius: 15px;
    width: 100%;
    height: 100%;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
    display: flex;
    flex-direction: column;
    overflow: hidden;
    border: 1px solid #e0e0e0;
}

.modal-header {
    padding: 15px 20px;
    border-bottom: 1px solid #eee;
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: #007bff;
    color: white;
    border-radius: 15px 15px 0 0;
    flex-shrink: 0;
}

.modal-header h5 {
    margin: 0;
    font-size: 16px;
    font-weight: 600;
}

.close-btn {
    background: none;
    border: none;
    font-size: 24px;
    cursor: pointer;
    color: white;
    line-height: 1;
    transition: all 0.3s ease;
}

.close-btn:hover {
    color: #f8f9fa;
    transform: scale(1.1);
}

.modal-body {
    padding: 0;
    flex: 1;
    display: flex;
    flex-direction: column;
    min-height: 0;
}

.chat-container {
    display: flex;
    flex-direction: column;
    height: 100%;
    min-height: 0;
}

.chat-messages {
    flex: 1;
    padding: 15px;
    overflow-y: auto;
    background: #f8f9fa;
    display: flex;
    flex-direction: column;
    gap: 10px;
    min-height: 0;
    max-height: 100%;
}

/* Custom scrollbar para a área de mensagens */
.chat-messages::-webkit-scrollbar {
    width: 6px;
}

.chat-messages::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 3px;
}

.chat-messages::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 3px;
}

.chat-messages::-webkit-scrollbar-thumb:hover {
    background: #a8a8a8;
}

.message {
    padding: 10px 15px;
    border-radius: 18px;
    max-width: 85%;
    word-wrap: break-word;
    animation: messageAppear 0.3s ease;
    flex-shrink: 0;
    line-height: 1.4;
}

@keyframes messageAppear {
    from {
        opacity: 0;
        transform: translateY(10px);
    }

    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.bot-message {
    background: #007bff;
    color: white;
    align-self: flex-start;
    border-bottom-left-radius: 5px;
    box-shadow: 0 2px 5px rgba(0, 123, 255, 0.2);
}

.user-message {
    background: #e9ecef;
    color: #333;
    align-self: flex-end;
    border-bottom-right-radius: 5px;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
}

.options-message {
    background: #007bff;
    color: white;
    align-self: flex-start;
    border-bottom-left-radius: 5px;
}

.option-button {
    display: block;
    width: 100%;
    padding: 10px 12px;
    margin: 5px 0;
    background: rgba(255, 255, 255, 0.2);
    border: 1px solid rgba(255, 255, 255, 0.3);
    border-radius: 8px;
    color: white;
    text-align: left;
    cursor: pointer;
    transition: all 0.3s ease;
    font-size: 14px;
    font-weight: 500;
}

.option-button:hover {
    background: rgba(255, 255, 255, 0.3);
    transform: translateX(5px);
}

.link-button {
    display: inline-block;
    padding: 10px 20px;
    background: #28a745;
    color: white;
    text-decoration: none;
    border-radius: 8px;
    margin-top: 10px;
    text-align: center;
    transition: all 0.3s ease;
    font-size: 14px;
    font-weight: 500;
    box-shadow: 0 2px 5px rgba(40, 167, 69, 0.3);
}

.link-button:hover {
    background: #218838;
    transform: scale(1.05);
    box-shadow: 0 4px 8px rgba(40, 167, 69, 0.4);
}

.chat-input-container {
    padding: 15px;
    border-top: 1px solid #eee;
    display: flex;
    gap: 10px;
    background: white;
    flex-shrink: 0;
}

#chatInput {
    flex: 1;
    padding: 12px;
    border: 1px solid #ddd;
    border-radius: 25px;
    outline: none;
    font-size: 14px;
    background: #f8f9fa;
    transition: all 0.3s ease;
}

#chatInput:focus {
    border-color: #007bff;
    background: white;
    box-shadow: 0 0 0 2px rgba(0, 123, 255, 0.1);
}

#sendChatBtn {
    padding: 12px 20px;
    background: #007bff;
    color: white;
    border: none;
    border-radius: 25px;
    cursor: pointer;
    transition: all 0.3s ease;
    font-size: 14px;
    font-weight: 500;
    min-width: 80px;
}

#sendChatBtn:hover {
    background: #0056b3;
    transform: scale(1.05);
}

/* Responsivo */
@media (max-width: 768px) {
    .whatsapp-float {
        bottom: 70px;
        right: 15px;
    }

    .whatsapp-icon {
        width: 55px;
        height: 55px;
    }

    .whatsapp-modal {
        width: calc(100vw - 40px);
        height: 450px;
        bottom: 140px;
        right: 20px;
        left: 20px;
    }

    .message {
        max-width: 90%;
    }

    .chat-messages {
        padding: 10px;
    }
}

@media (max-width: 480px) {
    .whatsapp-modal {
        width: calc(100vw - 30px);
        right: 15px;
        left: 15px;
        height: 400px;
    }

    .modal-header {
        padding: 12px 15px;
    }

    .chat-input-container {
        padding: 12px;
    }

    #chatInput {
        padding: 10px;
    }

    #sendChatBtn {
        padding: 10px 15px;
        min-width: 70px;
    }
}
</style>

<script type="text/javascript">
function googleTranslateElementInit() {
    new google.translate.TranslateElement({
        pageLanguage: 'pt',
        includedLanguages: 'pt,en,zh-CN',
        autoDisplay: false
    }, 'google_translate_element');
}

function changeLanguage(lang) {
    var interval = setInterval(function() {
        var selectField = document.querySelector("#google_translate_element select");
        if (selectField && selectField.options.length > 0) {
            for (var i = 0; i < selectField.options.length; i++) {
                if (selectField.options[i].value === lang) {
                    selectField.selectedIndex = i;
                    selectField.dispatchEvent(new Event("change"));
                    clearInterval(interval);
                    break;
                }
            }
        }
    }, 200);
}

// Força ocultar a barra sempre que o Google tentar exibir
document.addEventListener('DOMContentLoaded', function() {
    var interval = setInterval(function() {
        var banner = document.querySelector('.goog-te-banner-frame');
        if (banner) {
            banner.style.display = 'none';
            document.body.style.top = '0px';
        }
    }, 300);

    // Código do Chatbot
    const whatsappFloat = document.getElementById('whatsappFloat');
    const whatsappModal = document.getElementById('whatsappModal');
    const closeModal = document.getElementById('closeModal');
    const chatMessages = document.getElementById('chatMessages');
    const chatInput = document.getElementById('chatInput');
    const sendChatBtn = document.getElementById('sendChatBtn');

    let userName = '';
    let currentStep = 'welcome';

    // Abrir modal ao clicar no botão
    whatsappFloat.addEventListener('click', function() {
        whatsappModal.style.display = 'block';
        resetChat();
        startChat();
        setTimeout(() => chatInput.focus(), 500);
    });

    // Fechar modal
    function closeWhatsappModal() {
        whatsappModal.style.display = 'none';
        resetChat();
    }

    closeModal.addEventListener('click', closeWhatsappModal);

    // Resetar chat
    function resetChat() {
        chatMessages.innerHTML = '';
        userName = '';
        currentStep = 'welcome';
    }

    // Iniciar chat
    function startChat() {
        addBotMessage('Olá, seja bem vindo! Qual seu nome?');
    }

    // Adicionar mensagem do bot
    function addBotMessage(text, isOptions = false) {
        const messageDiv = document.createElement('div');
        messageDiv.className = `message ${isOptions ? 'options-message' : 'bot-message'}`;
        messageDiv.innerHTML = text;
        chatMessages.appendChild(messageDiv);
        scrollToBottom();
    }

    // Adicionar mensagem do usuário
    function addUserMessage(text) {
        const messageDiv = document.createElement('div');
        messageDiv.className = 'message user-message';
        messageDiv.textContent = text;
        chatMessages.appendChild(messageDiv);
        scrollToBottom();
    }

    // Scroll automático para o final
    function scrollToBottom() {
        setTimeout(() => {
            chatMessages.scrollTop = chatMessages.scrollHeight;
        }, 100);
    }

    // Processar resposta do usuário
    function processUserInput() {
        const input = chatInput.value.trim();
        if (!input) return;

        addUserMessage(input);
        chatInput.value = '';

        switch (currentStep) {
            case 'welcome':
                userName = input;
                showOptions();
                break;

            case 'options':
                handleOption(input);
                break;

            case 'invalid_option':
                showOptions();
                break;
        }
    }

    // Mostrar opções
    function showOptions() {
        currentStep = 'options';
        const optionsHTML = `
            Bem vindo <strong>${userName}</strong>, escolha uma das opções:<br><br>
            <button class="option-button" onclick="selectOption('1')">1 - Criar uma Garantia</button>
            <button class="option-button" onclick="selectOption('2')">2 - Tutorial da Garantia</button>
            <button class="option-button" onclick="selectOption('3')">3 - Atendimento Humanizado</button>
        `;
        addBotMessage(optionsHTML, true);
    }

    // Lidar com opção selecionada
    function handleOption(option) {
        switch (option) {
            case '1':
                addBotMessage(`Ótimo ${userName}! Aqui está o link para criar uma garantia:`);
                addBotMessage(
                    `<a href="<?php echo $url_frontend; ?>/index.php?page=garantiaAdd" target="_blank" class="link-button">Abrir Criador de Garantia</a>`
                );
                setTimeout(showOptions, 3000);
                break;

            case '2':
                addBotMessage(`Perfeito ${userName}! Aqui estão os tutoriais da garantia:`);
                addBotMessage(
                    `<a href="<?php echo $url_frontend; ?>/index.php?page=videos" target="_blank" class="link-button">Ver Tutoriais</a>`
                );
                setTimeout(showOptions, 3000);
                break;

            case '3':
                addBotMessage(
                    `Entendi ${userName}! Vou conectar você com nosso atendimento humanizado no WhatsApp.`);
                setTimeout(() => {
                    const whatsappUrl =
                        `https://wa.me/5585991994652?text=Olá, meu nome é ${userName} e gostaria de um atendimento humanizado.`;
                    window.open(whatsappUrl, '_blank');
                    closeWhatsappModal();
                }, 2000);
                break;

            default:
                currentStep = 'invalid_option';
                addBotMessage('Desculpe, opção inválida. Por favor, escolha uma das opções abaixo:');
                setTimeout(showOptions, 1000);
                break;
        }
    }

    // Selecionar opção via botão
    window.selectOption = function(option) {
        chatInput.value = option;
        processUserInput();
    };

    // Event listeners
    sendChatBtn.addEventListener('click', processUserInput);

    chatInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            processUserInput();
        }
    });

    // Fechar modal clicando fora
    document.addEventListener('click', function(e) {
        if (whatsappModal.style.display === 'block' &&
            !whatsappModal.contains(e.target) &&
            !whatsappFloat.contains(e.target)) {
            closeWhatsappModal();
        }
    });

    // Efeito de piscar randomico
    function randomBlink() {
        const randomDelay = Math.random() * 5000 + 2000; // 2-7 segundos
        setTimeout(function() {
            whatsappFloat.style.animation = 'none';
            setTimeout(function() {
                whatsappFloat.style.animation = 'float 3s ease-in-out infinite';
            }, 300);
            randomBlink();
        }, randomDelay);
    }

    randomBlink();
});
</script>

<script type="text/javascript" src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit">
</script>
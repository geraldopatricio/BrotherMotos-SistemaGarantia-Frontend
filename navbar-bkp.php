<?php
$env = parse_ini_file(__DIR__ . '/../.env');
$baseUrl = rtrim($env['ALLOWED_ORIGIN'], '/');
$appBaseUrl = $baseUrl . '/garantiaHom';
?>


<!-- Navbar -->

<!-- 1. ESTILOS DO CHAT (Adicione isso no início ou no seu arquivo CSS principal) -->
<style>
/* Animação de pulso para mensagens novas */
@keyframes pulse-red {
    0% {
        box-shadow: 0 0 0 0 rgba(220, 53, 69, 0.7);
    }

    70% {
        box-shadow: 0 0 0 10px rgba(220, 53, 69, 0);
    }

    100% {
        box-shadow: 0 0 0 0 rgba(220, 53, 69, 0);
    }
}

.msg-alert {
    animation: pulse-red 1.5s infinite;
    background-color: #dc3545 !important;
    /* Vermelho */
}

/* Ajuste fino para posicionar o badge no botão arredondado do seu template */
.chat-badge-counter {
    position: absolute;
    top: 4px;
    right: 4px;
    background-color: #2e7d32;
    /* Verde padrão */
    color: white;
    border-radius: 50%;
    height: 16px;
    min-width: 16px;
    padding: 0 4px;
    font-size: 10px;
    font-weight: 700;
    line-height: 16px;
    text-align: center;
    border: 2px solid #fff;
    /* Borda branca para separar do ícone */
    display: none;
    /* Escondido por padrão */
}
</style>

<nav class="layout-navbar container-fluid navbar-detached navbar navbar-expand-xl align-items-center bg-navbar-theme"
    id="layout-navbar">

    <div class="layout-menu-toggle navbar-nav align-items-xl-center me-3 me-xl-0 d-xl-none">
        <a class="nav-item nav-link px-0 me-xl-6" href="javascript:void(0)">
            <i class="icon-base ti tabler-menu-2 icon-md"></i>
        </a>
    </div>

    <div class="navbar-nav-right d-flex align-items-center justify-content-end" id="navbar-collapse">
        <!-- Search -->
        <div class="navbar-nav align-items-center">
            <div class="nav-item navbar-search-wrapper px-md-0 px-2 mb-0">
                <a class="nav-item nav-link search-toggler d-flex align-items-center px-0" href="javascript:void(0);">
                    <span class="d-inline-block text-body-secondary fw-normal" id="autocomplete"></span>
                </a>
            </div>
        </div>
        <!-- /Search -->

        <ul class="navbar-nav flex-row align-items-center ms-md-auto">

            <!-- Language Selector -->
            <li class="nav-item dropdown-language dropdown">
                <a class="nav-link dropdown-toggle hide-arrow btn btn-icon btn-text-secondary rounded-pill"
                    href="javascript:void(0);" data-bs-toggle="dropdown">
                    <i class="icon-base ti tabler-language icon-22px text-heading"></i>
                </a>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item" href="javascript:void(0);"
                            onclick="changeLanguage('pt')"><span>Português (Brasil)</span></a></li>
                    <li><a class="dropdown-item" href="javascript:void(0);"
                            onclick="changeLanguage('en')"><span>English</span></a></li>
                    <li><a class="dropdown-item" href="javascript:void(0);" onclick="changeLanguage('zh-CN')"><span>中文
                                (Chinês)</span></a></li>
                </ul>
            </li>
            <!--/ Language -->

            <!-- ============================================= -->
            <!-- 2. NOVO ÍCONE DE CHAT IMPLEMENTADO AQUI       -->
            <!-- ============================================= -->
            <li class="nav-item">
                <a class="nav-link btn btn-icon btn-text-secondary rounded-pill position-relative"
                    href="<?= $baseUrl ?>/garantiaHom/frontend/index.php?page=chat" title="Bate-papo Interno">
                    <!-- Ícone de mensagens -->
                    <i class="icon-base ti tabler-message-dots icon-22px text-heading"></i>

                    <!-- Badge Contador -->
                    <span id="navChatBadge" class="chat-badge-counter">0</span>
                </a>
            </li>
            <!-- ============================================= -->

            <!-- Style Switcher -->
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle hide-arrow btn btn-icon btn-text-secondary rounded-pill"
                    id="nav-theme" href="javascript:void(0);" data-bs-toggle="dropdown">
                    <i class="icon-base ti tabler-sun icon-22px theme-icon-active text-heading"></i>
                    <span class="d-none ms-2" id="nav-theme-text">Toggle theme</span>
                </a>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="nav-theme-text">
                    <li>
                        <button type="button" class="dropdown-item align-items-center active"
                            data-bs-theme-value="light" aria-pressed="false">
                            <span><i class="icon-base ti tabler-sun icon-22px me-3" data-icon="sun"></i>Light</span>
                        </button>
                    </li>
                    <li>
                        <button type="button" class="dropdown-item align-items-center" data-bs-theme-value="dark"
                            aria-pressed="true">
                            <span><i class="icon-base ti tabler-moon-stars icon-22px me-3"
                                    data-icon="moon-stars"></i>Dark</span>
                        </button>
                    </li>
                    <li>
                        <button type="button" class="dropdown-item align-items-center" data-bs-theme-value="system"
                            aria-pressed="false">
                            <span><i class="icon-base ti tabler-device-desktop-analytics icon-22px me-3"
                                    data-icon="device-desktop-analytics"></i>System</span>
                        </button>
                    </li>
                </ul>
            </li>

            <!-- User -->
            <li class="nav-item navbar-dropdown dropdown-user dropdown">
                <a class="nav-link dropdown-toggle hide-arrow p-0" href="javascript:void(0);" data-bs-toggle="dropdown">
                    <div class="avatar avatar-online">
                        <?php 
                            // Define o caminho base
                            $base_url_avatar = "<?= $baseUrl ?>/garantiaHom/backend/routers/avatar/";
                        // Verifica se existe o avatar, se não, usa a imagem padrão
                        $img_path = !empty($_SESSION['user_avatar'])
                        ? $base_url_avatar . $_SESSION['user_avatar']
                        : 'assets/img/avatars/1.png';
                        ?>
                        <img src="<?php echo htmlspecialchars($img_path); ?>" alt="Avatar" class="rounded-circle" />
                    </div>
                </a>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li>
                        <a class="dropdown-item mt-0" href="pages-account-settings-account.html">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0 me-2">
                                    <div class="flex-shrink-0 me-2">
                                        <div class="avatar avatar-online">
                                            <?php 
                                                // Reutiliza a lógica para garantir que a imagem seja a mesma
                                                $img_path = !empty($_SESSION['user_avatar']) 
                                                            ? "<?= $baseUrl ?>/garantiaHom/backend/routers/avatar/" .
                                            $_SESSION['user_avatar']
                                            : 'assets/img/avatars/1.png';
                                            ?>
                                            <img src="<?php echo htmlspecialchars($img_path); ?>" alt="Avatar"
                                                class="rounded-circle" />
                                        </div>
                                    </div>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-0">Usuário: <?php echo htmlspecialchars($_SESSION['user_usuario']); ?>
                                    </h6>
                                    <small class="text-body-secondary">Tipo:
                                        <?php echo htmlspecialchars($_SESSION['user_tipo']); ?></small>
                                </div>
                            </div>
                        </a>
                    </li>
                    <li>
                        <div class="dropdown-divider my-1 mx-n2"></div>
                    </li>
                    <li>
                        <a class="dropdown-item" href="pages-profile-user.html">
                            <i class="icon-base ti tabler-users me-3 icon-md"></i>
                            <span class="align-middle">Cliente: <?php echo isset($codcli) ? $codcli : ''; ?></span>
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item" href="pages-profile-user.html">
                            <i class="icon-base ti tabler-user me-3 icon-md"></i>
                            <span class="align-middle">RCA:
                                <?php echo htmlspecialchars($_SESSION['user_codusur']); ?></span>
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item" href="javascript:void(0);">
                            <i class="icon-base ti tabler-mail me-3 icon-md"></i>
                            <span class="align-middle"><?php echo htmlspecialchars($_SESSION['user_email']); ?></span>
                        </a>
                    </li>
                    <?php if (!empty($_SESSION['user_zap'])): ?>
                    <li>
                        <a class="dropdown-item" href="javascript:void(0);">
                            <i class="icon-base ti tabler-brand-whatsapp me-3 icon-md"></i>
                            <span class="align-middle"><?php echo htmlspecialchars($_SESSION['user_zap']); ?></span>
                        </a>
                    </li>
                    <?php endif; ?>
                    <li>
                        <div class="dropdown-divider my-1 mx-n2"></div>
                    </li>
                    <li>
                        <div class="d-grid px-2 pt-2 pb-1">
                            <a class="btn btn-sm btn-danger d-flex" href="logout.php">
                                <small class="align-middle">Logout</small>
                                <i class="icon-base ti tabler-logout ms-2 icon-14px"></i>
                            </a>
                        </div>
                    </li>
                </ul>
            </li>
            <!--/ User -->
        </ul>
    </div>
</nav>

<!-- 3. SCRIPT PARA MONITORAR ONLINE E MENSAGENS -->
<script>
document.addEventListener("DOMContentLoaded", function() {
    // CAMINHO CORRETO DA API CONFORME SUA SOLICITAÇÃO
    const CHAT_API = '<?= $baseUrl ?>/garantiaHom/backend/routers/chat_api.php';

    const badge = document.getElementById('navChatBadge');

    function checkGlobalChat() {
        // Usa fetch para não depender de jQuery neste componente
        const formData = new FormData();
        formData.append('action', 'global_status');

        fetch(CHAT_API, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                // 1. Atualiza contador de usuários online
                if (data.online_count > 0) {
                    badge.style.display = 'block';
                    badge.innerText = data.online_count;

                    // Reset para estilo padrão (Verde) se não houver msg nova
                    if (!data.new_message) {
                        badge.style.backgroundColor = '#2e7d32';
                        badge.classList.remove('msg-alert');
                    }
                } else {
                    // Se não tem ninguém online
                    if (!data.new_message) {
                        badge.style.display = 'none';
                    }
                }

                // 2. Alerta de Nova Mensagem (Prioridade Visual)
                if (data.new_message) {
                    badge.style.display = 'block';
                    badge.innerText = '!'; // Mostra exclamação
                    badge.classList.add('msg-alert'); // Ativa o pisca-pisca vermelho
                }
            })
            .catch(err => {
                // Silencioso no console para não sujar log em caso de erro de rede momentâneo
                // console.error('Chat check error', err); 
            });
    }

    // Verifica a cada 5 segundos
    setInterval(checkGlobalChat, 5000);

    // Verifica uma vez ao carregar a página
    checkGlobalChat();
});
</script>
<!-- / Navbar -->
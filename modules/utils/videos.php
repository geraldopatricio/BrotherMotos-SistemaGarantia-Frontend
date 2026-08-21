<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);

// É importante tratar null e 0 de forma consistente.
// Se as variáveis de sessão não existirem ou forem vazias, trataremos como '0' ou null para removê-las.
$codcli_raw = $_SESSION['user_cliente'] ?? null;
$rca_raw = $_SESSION['user_rca'] ?? null;

// Convertemos para int para facilitar a verificação de 0 ou null
$codcli = ($codcli_raw !== null && $codcli_raw !== '') ? (int) htmlspecialchars($codcli_raw) : null;
$rca = ($rca_raw !== null && $rca_raw !== '') ? (int) htmlspecialchars($rca_raw) : null;

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard de Garantias</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
    body {
        font-family: 'roboto', ui-sans-serif, system-ui, sans-serif;
        background-color: #f4f6f9;
        color: #333;
        margin: 0;
    }

    .container-fluid {
        margin: 0 auto;
        padding: 20px;
        max-width: 1800px;
    }

    .section-header {
        margin-bottom: 30px;
        text-align: center;
    }

    .section-header h2 {
        font-size: 2.2rem;
        font-weight: 700;
        color: #333;
        margin-bottom: 10px;
    }

    .section-header p {
        font-size: 1.1rem;
        color: #6c757d;
        max-width: 700px;
        margin: 0 auto;
    }

    .tabs-container {
        display: flex;
        justify-content: center;
        margin-bottom: 30px;
        border-bottom: 1px solid #e0e0e0;
    }

    .tab-button {
        background: none;
        border: none;
        padding: 12px 24px;
        font-size: 1rem;
        font-weight: 600;
        color: #6c757d;
        cursor: pointer;
        transition: all 0.3s ease;
        position: relative;
    }

    .tab-button.active {
        color: #696cff;
    }

    .tab-button.active::after {
        content: '';
        position: absolute;
        bottom: -1px;
        left: 0;
        width: 100%;
        height: 3px;
        background-color: #696cff;
        border-radius: 3px 3px 0 0;
    }

    .tab-content {
        display: none;
    }

    .tab-content.active {
        display: block;
    }

    .video-card {
        background-color: #fff;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        margin-bottom: 25px;
        cursor: pointer;
    }

    .video-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.12);
    }

    .video-thumbnail {
        position: relative;
        width: 100%;
        height: 0;
        padding-bottom: 56.25%;
        /* Aspect ratio 16:9 */
        background-color: #f0f0f0;
        overflow: hidden;
    }

    .video-thumbnail img {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.5s ease;
    }

    .video-card:hover .video-thumbnail img {
        transform: scale(1.05);
    }

    .video-play-button {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: 70px;
        height: 70px;
        background-color: rgba(0, 0, 0, 0.7);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }

    .video-play-button:hover {
        background-color: rgba(0, 0, 0, 0.9);
    }

    .video-play-button::after {
        content: '';
        display: block;
        width: 0;
        height: 0;
        border-style: solid;
        border-width: 10px 0 10px 18px;
        border-color: transparent transparent transparent #ffffff;
        margin-left: 4px;
    }

    .video-duration {
        position: absolute;
        bottom: 10px;
        right: 10px;
        background-color: rgba(0, 0, 0, 0.8);
        color: white;
        padding: 3px 8px;
        border-radius: 4px;
        font-size: 0.8rem;
        font-weight: 500;
    }

    .video-info {
        padding: 16px;
    }

    .video-title {
        font-size: 1.1rem;
        font-weight: 600;
        margin-bottom: 8px;
        line-height: 1.4;
        color: #333;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .video-meta {
        display: flex;
        justify-content: space-between;
        color: #6c757d;
        font-size: 0.9rem;
    }

    .manual-card {
        background-color: #fff;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        margin-bottom: 25px;
        display: flex;
        flex-direction: column;
        height: 100%;
    }

    .manual-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.12);
    }

    .manual-icon {
        height: 120px;
        background: linear-gradient(135deg, #696cff 0%, #17cfd5 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
    }

    .manual-icon svg {
        width: 60px;
        height: 60px;
    }

    .manual-info {
        padding: 20px;
        flex-grow: 1;
        display: flex;
        flex-direction: column;
    }

    .manual-title {
        font-size: 1.2rem;
        font-weight: 600;
        margin-bottom: 10px;
        color: #333;
    }

    .manual-description {
        color: #6c757d;
        margin-bottom: 15px;
        flex-grow: 1;
    }

    .manual-download {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 8px 16px;
        background-color: #696cff;
        color: white;
        border-radius: 6px;
        text-decoration: none;
        font-weight: 500;
        transition: background-color 0.3s ease;
        align-self: flex-start;
    }

    .manual-download:hover {
        background-color: #5a5fd8;
        color: white;
    }

    .resources-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 25px;
    }

    .view-all-container {
        text-align: center;
        margin-top: 30px;
    }

    .view-all-button {
        display: inline-block;
        padding: 12px 30px;
        background-color: transparent;
        color: #696cff;
        border: 2px solid #696cff;
        border-radius: 8px;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.3s ease;
    }

    .view-all-button:hover {
        background-color: #696cff;
        color: white;
    }

    /* Modal Styles */
    .video-modal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.9);
        z-index: 1000;
        justify-content: center;
        align-items: center;
    }

    .video-modal.active {
        display: flex;
    }

    .modal-content {
        position: relative;
        width: 90%;
        max-width: 900px;
        background: #000;
        border-radius: 8px;
        overflow: hidden;
    }

    .modal-video {
        width: 100%;
        height: auto;
        display: block;
    }

    .modal-close {
        position: absolute;
        top: 15px;
        right: 15px;
        background: rgba(0, 0, 0, 0.7);
        color: white;
        border: none;
        border-radius: 50%;
        width: 40px;
        height: 40px;
        font-size: 20px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: background-color 0.3s ease;
    }

    .modal-close:hover {
        background: rgba(0, 0, 0, 0.9);
    }

    .video-controls {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        background: linear-gradient(transparent, rgba(0, 0, 0, 0.7));
        padding: 20px;
        display: flex;
        align-items: center;
        gap: 15px;
    }

    .control-btn {
        background: none;
        border: none;
        color: white;
        font-size: 18px;
        cursor: pointer;
        padding: 5px;
    }

    .progress-bar {
        flex: 1;
        height: 5px;
        background: rgba(255, 255, 255, 0.3);
        border-radius: 3px;
        overflow: hidden;
        cursor: pointer;
    }

    .progress {
        height: 100%;
        background: #696cff;
        width: 0%;
        transition: width 0.1s ease;
    }

    .time-display {
        color: white;
        font-size: 14px;
        min-width: 100px;
    }
    </style>
</head>

<body>

    <div class="content-wrapper">
        <div class="container-fluid">
            <!-- Seção de Vídeos e Manuais -->
            <div class="section-header">
                <h2>Recursos de Aprendizado</h2>
                <p>Explore nossos tutoriais em vídeo e manuais para aproveitar ao máximo nossa plataforma</p>
            </div>

            <div class="tabs-container">
                <button class="tab-button active" data-tab="videos">Vídeos Tutoriais</button>
                <button class="tab-button" data-tab="manuals">Manuais e Documentação</button>
            </div>

            <div class="tab-content active" id="videos-tab">
                <div class="resources-grid">
                    <!-- Vídeo 1 - Dashboard -->
                    <div class="video-card"
                        data-video-src="<?php echo $url_frontend; ?>/modules/utils/videos/garantia.mp4"
                        data-video-title="Como usar o Sistema de Garantias - Guia Completo">
                        <div class="video-thumbnail">
                            <img src="https://placehold.co/600x340/696cff/ffffff?text=Overview+Completo"
                                alt="Overview Completo">
                            <div class="video-play-button"></div>
                            <span class="video-duration">2:59</span>
                        </div>
                        <div class="video-info">
                            <h3 class="video-title">Overview - Guia Completo</h3>
                            <div class="video-meta">
                                <span>Visualizações: 1.2K</span>
                                <span>Há 2 semanas</span>
                            </div>
                        </div>
                    </div>

                    <!-- Vídeo 2 -->
                    <div class="video-card"
                        data-video-src="<?php echo $url_frontend; ?>/modules/utils/videos/usergarantia.mp4"
                        data-video-title="Cadastrar Usuário">
                        <div class="video-thumbnail">
                            <img src="https://placehold.co/600x340/17cfd5/ffffff?text=Cadastrar+Usuarios"
                                alt="Filtros Avançados">
                            <div class="video-play-button"></div>
                            <span class="video-duration">7:42</span>
                        </div>
                        <div class="video-info">
                            <h3 class="video-title">Cadastrando novo usuario - Cliente ou RCA</h3>
                            <div class="video-meta">
                                <span>Visualizações: 856</span>
                                <span>Há 1 mês</span>
                            </div>
                        </div>
                    </div>

                    <!-- Vídeo 3 -->
                    <div class="video-card"
                        data-video-src="<?php echo $url_frontend; ?>/modules/utils/videos/consultagarantia.mp4"
                        data-video-title="Consultar Garantias">
                        <div class="video-thumbnail">
                            <img src="https://placehold.co/600x340/ffab00/ffffff?text=Consulta+e+Impressão"
                                alt="Exportação de Dados">
                            <div class="video-play-button"></div>
                            <span class="video-duration">0:11</span>
                        </div>
                        <div class="video-info">
                            <h3 class="video-title">Consulta e Impressão de Relatório</h3>
                            <div class="video-meta">
                                <span>Visualizações: 642</span>
                                <span>Há 3 semanas</span>
                            </div>
                        </div>
                    </div>

                    <!-- Vídeo 4 -->
                    <div class="video-card"
                        data-video-src="<?php echo $url_frontend; ?>/modules/utils/videos/gravagarantia.mp4"
                        data-video-title="Cadastrando sua Primeira Garantia">
                        <div class="video-thumbnail">
                            <img src="https://placehold.co/600x340/ff3e1d/ffffff?text=Cadastrando+Garantia"
                                alt="Cadastrando sua Garantia">
                            <div class="video-play-button"></div>
                            <span class="video-duration">00:25</span>
                        </div>
                        <div class="video-info">
                            <h3 class="video-title">Cadastrando sua Primeira Garantia</h3>
                            <div class="video-meta">
                                <span>Visualizações: 1.5K</span>
                                <span>Há 2 meses</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="view-all-container">
                    <a href="#" class="view-all-button">Ver Todos os Vídeos</a>
                </div>
            </div>

            <div class="tab-content" id="manuals-tab">
                <div class="resources-grid">
                    <!-- Manual 1 -->
                    <div class="manual-card">
                        <div class="manual-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                <polyline points="14,2 14,8 20,8"></polyline>
                                <line x1="16" y1="13" x2="8" y2="13"></line>
                                <line x1="16" y1="17" x2="8" y2="17"></line>
                                <polyline points="10,9 9,9 8,9"></polyline>
                            </svg>
                        </div>
                        <div class="manual-info">
                            <h3 class="manual-title">Manual - Cadastrar Garantia</h3>
                            <p class="manual-description">Guia completo com todas as funcionalidades do sistema de
                                garantias, desde o cadastro até inclusão de itens, anexos, etc.</p>
                            <a href="<?php echo $url_frontend; ?>/uploads/manual-garantia-cadastro.pdf" target="_blank"
                                class="manual-download">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2">
                                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                    <polyline points="7,10 12,15 17,10"></polyline>
                                    <line x1="12" y1="15" x2="12" y2="3"></line>
                                </svg>
                                Baixar PDF
                            </a>
                        </div>
                    </div>

                    <!-- Manual 2 -->
                    <div class="manual-card">
                        <div class="manual-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                <circle cx="9" cy="7" r="4"></circle>
                                <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                                <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                            </svg>
                        </div>
                        <div class="manual-info">
                            <h3 class="manual-title">Guia de Cadastro de Usuários</h3>
                            <p class="manual-description">Aprenda a cadastrar novos usuários, definir permissões e
                                gerenciar acessos ao sistema.</p>
                            <a href="<?php echo $url_frontend; ?>/uploads/manual-novo-usuario.pdf" target="_blank"
                                class="manual-download">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2">
                                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                    <polyline points="7,10 12,15 17,10"></polyline>
                                    <line x1="12" y1="15" x2="12" y2="3"></line>
                                </svg>
                                Baixar PDF
                            </a>
                        </div>
                    </div>


                    <!-- Manual 3 -->
                    <div class="manual-card">
                        <div class="manual-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M9 11l3 3L22 4"></path>
                                <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path>
                            </svg>

                        </div>
                        <div class="manual-info">
                            <h3 class="manual-title">Regras - Cadastro de Garantia</h3>
                            <p class="manual-description">Veja quais são as regras de negócio do cadastro de garntia.
                            </p>
                            <a href="<?php echo $url_frontend; ?>/uploads/Regras-Garantia-Cadastro.pdf" target="_blank"
                                class="manual-download">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2">
                                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                    <polyline points="7,10 12,15 17,10"></polyline>
                                    <line x1="12" y1="15" x2="12" y2="3"></line>
                                </svg>
                                Baixar PDF
                            </a>
                        </div>
                    </div>


                    <!-- Manual 4 -->
                    <div class="manual-card">
                        <div class="manual-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M12 20h9"></path>
                                <path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4 12.5-12.5z"></path>
                            </svg>


                        </div>
                        <div class="manual-info">
                            <h3 class="manual-title">Regras - Edição de Garantia</h3>
                            <p class="manual-description">Veja quais são as regras de negócio para Editar uma garantia.
                            </p>
                            <a href="<?php echo $url_frontend; ?>/uploads/Regras-Garantia-Edicao.pdf" target="_blank"
                                class="manual-download">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2">
                                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                    <polyline points="7,10 12,15 17,10"></polyline>
                                    <line x1="12" y1="15" x2="12" y2="3"></line>
                                </svg>
                                Baixar PDF
                            </a>
                        </div>
                    </div>


                    <!-- Manual 4 -->
                    <div class="manual-card">
                        <div class="manual-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round">
                                <polygon points="12 2 15 8.5 22 9.3 17 14 18.5 21 12 17.5 5.5 21 7 14 2 9.3 9 8.5 12 2">
                                </polygon>
                            </svg>
                        </div>
                        <div class="manual-info">
                            <h3 class="manual-title">Features 01 - Garantia</h3>
                            <p class="manual-description">Veja algumas mudanças novas implementadas no sistema de
                                garantia.
                            </p>
                            <a href="<?php echo $url_frontend; ?>/uploads/features-01.pdf" target="_blank"
                                class="manual-download">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2">
                                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                    <polyline points="7,10 12,15 17,10"></polyline>
                                    <line x1="12" y1="15" x2="12" y2="3"></line>
                                </svg>
                                Baixar PDF
                            </a>
                        </div>
                    </div>



                </div>

                <div class="view-all-container">
                    <a href="#" class="view-all-button">Ver Toda a Documentação</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para o Vídeo -->
    <div class="video-modal" id="videoModal">
        <div class="modal-content">
            <button class="modal-close" id="modalClose">&times;</button>
            <video class="modal-video" id="modalVideo" controls>
                <source src="" type="video/mp4">
                Seu navegador não suporta o elemento de vídeo.
            </video>
            <div class="video-controls">
                <button class="control-btn" id="playPauseBtn">⏯</button>
                <div class="progress-bar" id="progressBar">
                    <div class="progress" id="progress"></div>
                </div>
                <div class="time-display" id="timeDisplay">00:00 / 00:00</div>
                <button class="control-btn" id="fullscreenBtn">⛶</button>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
    $(document).ready(function() {
        const videoModal = $('#videoModal');
        const modalVideo = $('#modalVideo')[0];
        const modalClose = $('#modalClose');
        const playPauseBtn = $('#playPauseBtn');
        const progressBar = $('#progressBar');
        const progress = $('#progress');
        const timeDisplay = $('#timeDisplay');
        const fullscreenBtn = $('#fullscreenBtn');

        // Navegação entre abas
        $('.tab-button').on('click', function() {
            const tabId = $(this).data('tab');

            // Remove classe active de todos os botões e conteúdos
            $('.tab-button').removeClass('active');
            $('.tab-content').removeClass('active');

            // Adiciona classe active ao botão clicado
            $(this).addClass('active');

            // Mostra o conteúdo correspondente
            $(`#${tabId}-tab`).addClass('active');
        });

        // Abrir modal quando clicar em QUALQUER card de vídeo
        $('.video-card').on('click', function() {
            const videoSrc = $(this).data('video-src');
            const videoTitle = $(this).data('video-title');

            modalVideo.src = videoSrc;
            modalVideo.setAttribute('title', videoTitle);
            videoModal.addClass('active');
            modalVideo.play();
        });

        // Fechar modal
        modalClose.on('click', function() {
            videoModal.removeClass('active');
            modalVideo.pause();
            modalVideo.currentTime = 0;
        });

        // Fechar modal ao clicar fora do conteúdo
        videoModal.on('click', function(e) {
            if (e.target === videoModal[0]) {
                videoModal.removeClass('active');
                modalVideo.pause();
                modalVideo.currentTime = 0;
            }
        });

        // Controles customizados do vídeo
        playPauseBtn.on('click', function() {
            if (modalVideo.paused) {
                modalVideo.play();
            } else {
                modalVideo.pause();
            }
        });

        modalVideo.addEventListener('play', function() {
            playPauseBtn.text('⏸');
        });

        modalVideo.addEventListener('pause', function() {
            playPauseBtn.text('⏯');
        });

        modalVideo.addEventListener('timeupdate', function() {
            const currentTime = modalVideo.currentTime;
            const duration = modalVideo.duration;
            const progressPercent = (currentTime / duration) * 100;
            progress.css('width', progressPercent + '%');

            // Atualizar display de tempo
            const currentMinutes = Math.floor(currentTime / 60);
            const currentSeconds = Math.floor(currentTime % 60);
            const durationMinutes = Math.floor(duration / 60);
            const durationSeconds = Math.floor(duration % 60);

            timeDisplay.text(
                `${currentMinutes.toString().padStart(2, '0')}:${currentSeconds.toString().padStart(2, '0')} / ${durationMinutes.toString().padStart(2, '0')}:${durationSeconds.toString().padStart(2, '0')}`
            );
        });

        progressBar.on('click', function(e) {
            const progressWidth = progressBar.width();
            const clickPosition = e.offsetX;
            const duration = modalVideo.duration;
            modalVideo.currentTime = (clickPosition / progressWidth) * duration;
        });

        fullscreenBtn.on('click', function() {
            if (modalVideo.requestFullscreen) {
                modalVideo.requestFullscreen();
            } else if (modalVideo.webkitRequestFullscreen) {
                modalVideo.webkitRequestFullscreen();
            } else if (modalVideo.msRequestFullscreen) {
                modalVideo.msRequestFullscreen();
            }
        });

        // Fechar modal com tecla ESC
        $(document).on('keydown', function(e) {
            if (e.key === 'Escape' && videoModal.hasClass('active')) {
                videoModal.removeClass('active');
                modalVideo.pause();
                modalVideo.currentTime = 0;
            }
        });
    });
    </script>

</body>

</html>
<?php

$user_id = $_SESSION['user_id'] ?? null;

if (!$user_id) {
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil do Usuário - Dashboard</title>
    <style>
    body {
        font-family: 'roboto', ui-sans-serif, system-ui, sans-serif;
        background-color: #f4f6f9;
        color: #333;
        margin: 0;
    }

    /* 1. MODO FLUIDO: Ocupa 100% da largura */
    .container-fluid {
        width: 100%;
        padding: 20px 40px;
        box-sizing: border-box;
    }

    .content-wrapper {
        display: flex;
        flex-direction: column;
        min-height: 100vh;
    }

    /* AVATAR COM DISPARADOR CORRIGIDO */
    .avatar-wrapper {
        position: relative;
        width: 140px;
        height: 140px;
        margin: 0 auto 20px;
        cursor: pointer;
        border-radius: 50%;
        z-index: 10;
    }

    .profile-avatar {
        width: 100%;
        height: 100%;
        border-radius: 50%;
        background: linear-gradient(135deg, #696cff 0%, #17cfd5 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 3rem;
        font-weight: 600;
        overflow: hidden;
        border: 4px solid #fff;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.15);
        position: relative;
    }

    .profile-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }

    /* Camada que aparece no hover */
    .avatar-overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.6);
        display: flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        font-size: 11px;
        font-weight: bold;
        text-align: center;
        opacity: 0;
        transition: opacity 0.3s ease;
        border-radius: 50%;
        pointer-events: none;
        /* Importante para o clique passar para o pai */
    }

    .avatar-wrapper:hover .avatar-overlay {
        opacity: 1;
    }

    /* LAYOUT FLUIDO */
    .profile-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 30px;
    }

    .profile-container {
        display: grid;
        grid-template-columns: 320px 1fr;
        /* Sidebar fixa e conteúdo expandindo */
        gap: 30px;
        width: 100%;
    }

    .profile-sidebar {
        background-color: #fff;
        border-radius: 16px;
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.07);
        padding: 40px 30px;
        text-align: center;
        height: fit-content;
    }

    .profile-content {
        display: flex;
        flex-direction: column;
        gap: 30px;
    }

    /* CARDS */
    .profile-card {
        background-color: #fff;
        border-radius: 16px;
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.07);
        padding: 30px;
    }

    .card-title {
        font-size: 1.4rem;
        font-weight: 700;
        margin-bottom: 25px;
        border-bottom: 2px solid #f0f2f5;
        padding-bottom: 10px;
    }

    .info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 25px;
    }

    .info-label {
        font-weight: 600;
        color: #8897aa;
        font-size: 0.85rem;
        text-transform: uppercase;
        margin-bottom: 5px;
    }

    .info-value {
        font-size: 1.1rem;
        color: #333;
        font-weight: 500;
    }

    /* BOTÕES */
    .profile-action-btn {
        width: 100%;
        padding: 14px;
        border: none;
        border-radius: 8px;
        font-weight: 600;
        cursor: pointer;
        margin-bottom: 15px;
        transition: 0.3s;
        display: block;
    }

    .btn-primary {
        background-color: #696cff;
        color: white;
        box-shadow: 0 4px 10px rgba(105, 108, 255, 0.3);
    }

    .btn-danger {
        background-color: #f8f9fa;
        color: #ff3e1d;
        border: 1px solid #ff3e1d;
    }

    .btn-danger:hover {
        background-color: #ff3e1d;
        color: white;
    }

    /* MODAL */
    .modal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.6);
        z-index: 9999;
        align-items: center;
        justify-content: center;
    }

    .modal.active {
        display: flex;
    }

    .modal-content {
        background-color: white;
        border-radius: 16px;
        width: 95%;
        max-width: 500px;
        padding: 30px;
    }

    .form-group {
        margin-bottom: 20px;
    }

    .form-control {
        width: 100%;
        padding: 12px;
        border: 1px solid #d9dee3;
        border-radius: 8px;
        box-sizing: border-box;
    }

    @media (max-width: 992px) {
        .profile-container {
            grid-template-columns: 1fr;
        }

        .container-fluid {
            padding: 20px;
        }
    }
    </style>
</head>

<body>

    <div class="content-wrapper">
        <div class="container-fluid">

            <div class="profile-header">
                <h1 style="color: #566a7f;">Meu Perfil</h1>
                <a href="index.php?page=dashboard" style="text-decoration:none; color:#696cff; font-weight:bold;">←
                    Voltar</a>
            </div>

            <div id="loadingOverlay" style="text-align:center; padding:100px; font-size: 1.2rem; color: #666;">
                Buscando dados do servidor...
            </div>

            <div class="profile-container" id="profileContainer" style="display:none;">

                <!-- Sidebar -->
                <div class="profile-sidebar">

                    <!-- AREA DA FOTO (CLIQUE AQUI) -->
                    <div class="avatar-wrapper" id="btnClickPhoto">
                        <div class="profile-avatar">
                            <span id="avatarLetter">?</span>
                            <img id="avatarImage" src="" style="display:none;">
                            <div class="avatar-overlay">ALTERAR FOTO</div>
                        </div>
                        <!-- Input escondido -->
                        <input type="file" id="fileInput" accept="image/png, image/jpeg, image/jpg"
                            style="display:none;">
                    </div>

                    <h2 class="profile-name" id="sideName" style="margin: 10px 0 5px 0;">--</h2>
                    <p class="profile-role" id="sideRole" style="margin-top: 0; font-size: 0.9rem; color: #888;">--</p>

                    <div style="margin: 30px 0;">
                        <button class="profile-action-btn btn-primary" id="openEditModal">Editar Informações</button>
                        <button class="profile-action-btn btn-danger" id="openPassModal">Alterar Minha Senha</button>
                    </div>
                </div>

                <!-- Conteúdo -->
                <div class="profile-content">

                    <div class="profile-card">
                        <div class="card-title">Dados Pessoais</div>
                        <div class="info-grid">
                            <div class="info-item">
                                <div class="info-label">Nome de Usuário</div>
                                <div class="info-value" id="viewUser">--</div>
                            </div>
                            <div class="info-item">
                                <div class="info-label">E-mail de Contato</div>
                                <div class="info-value" id="viewEmail">--</div>
                            </div>
                            <div class="info-item">
                                <div class="info-label">WhatsApp</div>
                                <div class="info-value" id="viewZap">--</div>
                            </div>
                            <div class="info-item">
                                <div class="info-label">Tipo de Conta</div>
                                <div class="info-value" id="viewTipo">--</div>
                            </div>
                        </div>
                    </div>

                    <div class="profile-card">
                        <div class="card-title">Integração WinThor</div>
                        <div class="info-grid">
                            <div class="info-item">
                                <div class="info-label">Código Cliente</div>
                                <div class="info-value" id="viewCodCli">--</div>
                            </div>
                            <div class="info-item">
                                <div class="info-label">Código RCA</div>
                                <div class="info-value" id="viewCodUsur">--</div>
                            </div>
                            <div class="info-item">
                                <div class="info-label">Status no Sistema</div>
                                <div class="info-value" id="viewAtivo">--</div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <!-- MODAL EDITAR DADOS -->
    <div class="modal" id="modalEdit">
        <div class="modal-content">
            <h3 style="margin-top:0;">Atualizar Dados</h3>
            <div class="form-group">
                <label>Usuário</label>
                <input type="text" id="inUser" class="form-control" disabled>
            </div>
            <div class="form-group">
                <label>E-mail</label>
                <input type="email" id="inEmail" class="form-control">
            </div>
            <div class="form-group">
                <label>WhatsApp</label>
                <input type="text" id="inZap" class="form-control">
            </div>
            <div style="display:flex; gap:10px; margin-top:30px;">
                <button class="profile-action-btn btn-primary" id="btnSaveData" style="margin:0;">Salvar
                    Alterações</button>
                <button class="profile-action-btn" id="btnCloseEdit"
                    style="margin:0; background:#eee;">Cancelar</button>
            </div>
        </div>
    </div>

    <!-- MODAL SENHA -->
    <div class="modal" id="modalPass">
        <div class="modal-content">
            <h3 style="margin-top:0;">Nova Senha</h3>
            <div class="form-group">
                <label>Digite a nova senha</label>
                <input type="password" id="pass1" class="form-control">
            </div>
            <div class="form-group">
                <label>Confirme a senha</label>
                <input type="password" id="pass2" class="form-control">
            </div>
            <div style="display:flex; gap:10px; margin-top:30px;">
                <button class="profile-action-btn btn-primary" id="btnSavePass" style="margin:0;">Atualizar
                    Senha</button>
                <button class="profile-action-btn" id="btnClosePass"
                    style="margin:0; background:#eee;">Cancelar</button>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
    const API_URL = '<?php echo $url_backend; ?>/routers/usuarios.php';
    const AVATAR_PATH = '<?php echo $url_backend; ?>/routers/avatar/';
    const USER_ID = <?php echo $user_id; ?>;
    let base64Photo = null;

    $(document).ready(function() {
        fetchUser();

        // --- CORREÇÃO DO CLIQUE NA FOTO ---
        $('#btnClickPhoto').on('click', function(e) {
            console.log("Avatar clicado, abrindo seletor...");
            $('#fileInput').trigger('click');
        });

        // Prevenir que o input de arquivo pare a propagação indevidamente
        $('#fileInput').on('click', function(e) {
            e.stopPropagation();
        });

        // Quando selecionar o arquivo
        $('#fileInput').on('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(event) {
                    base64Photo = event.target.result;
                    // Preview
                    $('#avatarImage').attr('src', base64Photo).show();
                    $('#avatarLetter').hide();

                    if (confirm("Deseja aplicar esta nova foto de perfil agora?")) {
                        saveProfileData({
                            foto: base64Photo
                        });
                    }
                };
                reader.readAsDataURL(file);
            }
        });

        // Funções de carregamento
        function fetchUser() {
            $.get(`${API_URL}?id=${USER_ID}`, function(res) {
                if (res.success) {
                    const u = res.data;
                    $('#sideName, #viewUser, #inUser').text(u.usuario).val(u.usuario);
                    $('#sideRole, #viewTipo').text(u.tipo);
                    $('#viewEmail, #inEmail').text(u.email || '---').val(u.email);
                    $('#viewPhone, #viewZap, #inZap').text(u.zap || '---').val(u.zap);
                    $('#viewCodCli').text(u.codcli || 'N/A');
                    $('#viewCodUsur').text(u.codusur || 'N/A');
                    $('#viewAtivo').text(u.ativo === 'SIM' ? 'Ativo' : 'Inativo');

                    if (u.foto && u.foto !== 'default.png') {
                        $('#avatarImage').attr('src', AVATAR_PATH + u.foto + '?v=' + Math.random())
                            .show();
                        $('#avatarLetter').hide();
                    } else {
                        $('#avatarLetter').text(u.usuario.charAt(0).toUpperCase()).show();
                        $('#avatarImage').hide();
                    }

                    $('#loadingOverlay').hide();
                    $('#profileContainer').fadeIn();
                }
            });
        }

        function saveProfileData(data) {
            data.id = USER_ID;
            $.ajax({
                url: API_URL,
                method: 'PUT',
                contentType: 'application/json',
                data: JSON.stringify(data),
                success: function(res) {
                    alert(res.message || "Sucesso!");
                    fetchUser();
                    closeAllModals();
                },
                error: function(err) {
                    alert("Erro ao salvar dados.");
                }
            });
        }

        // Eventos de Botões
        $('#btnSaveData').click(function() {
            saveProfileData({
                usuario: $('#inUser').val(),
                email: $('#inEmail').val(),
                zap: $('#inZap').val()
            });
        });

        $('#btnSavePass').click(function() {
            const p1 = $('#pass1').val();
            const p2 = $('#pass2').val();
            if (p1 && p1 === p2) {
                saveProfileData({
                    senha: p1
                });
            } else {
                alert("As senhas não conferem!");
            }
        });

        // Modais
        $('#openEditModal').click(() => $('#modalEdit').addClass('active'));
        $('#openPassModal').click(() => $('#modalPass').addClass('active'));
        $('#btnCloseEdit, #btnClosePass').click(closeAllModals);

        function closeAllModals() {
            $('.modal').removeClass('active');
            $('#pass1, #pass2').val('');
        }
    });
    </script>
</body>

</html>
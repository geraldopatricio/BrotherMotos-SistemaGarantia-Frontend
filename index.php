<?php

ini_set('session.cookie_path', '/');
session_start();

if (!isset($_SESSION['user_logged_in']) || $_SESSION['user_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

$usuario = $_SESSION['user_usuario'];
$avatar = $_SESSION['user_avatar'] ?? '';
$email = $_SESSION['user_email'];
$tipo = $_SESSION['user_tipo'];
$whatsapp = $_SESSION['user_zap'];
$rca = $_SESSION['user_rca'];
$codusur1 = $_SESSION['user_codusur1'];
$cliente = $_SESSION['user_cliente'];
$codusur = $_SESSION['user_codusur'];
$codcli = $_SESSION['user_codcli'];

$usuario_logado = htmlspecialchars($_SESSION['user_usuario']);
$tipo_usuario = htmlspecialchars($_SESSION['user_tipo']);

// Define a página padrão como 'dashboard'
$page = isset($_GET['page']) ? $_GET['page'] : 'garantias';

// Lista de páginas permitidas
$allowedPages = [
    'dashboard' => 'modules/dashboard/dash.php', 
    'garantias' => 'modules/garantia/garantias.php',
    'garantiaEdit' => 'modules/garantia/garantiaEdit.php',
    'garantiaAdd' => 'modules/garantia/garantiaAdd.php',
    'relGar' => 'modules/relatorios/rptFiltroGarantia.php',
    'rptGarStatusValores' => 'modules/relatorios/rptGarStatusValores.php',
    'rptGarStatusValores_2' => 'modules/relatorios/rptGarStatusValores_2.php',
    'rptValoresAprovados' => 'modules/relatorios/rptValoresAprovados.php',
    'perfil' => 'modules/utils/perfil.php',
    'videos' => 'modules/utils/videos.php',
    'menu' => 'modules/utils/menu.php',
    'usuarios' => 'modules/acesso/usuarios.php',
    'acesso' => 'modules/acesso/acesso.php',
    'audit' => 'modules/acesso/audit.php',
    'chat' => 'modules/utils/batepapo.php',
    'configurar' => 'modules/configurador/configurar.php'
];

// Verifica se a página solicitada existe, senão usa a dashboard
if (!array_key_exists($page, $allowedPages)) {
    $page = 'dashboard';
}

// Agora o restante do HTML...
?>

<!doctype html>
<html lang="en" class="layout-navbar-fixed layout-menu-fixed layout-wide" dir="ltr" data-skin="default"
    data-assets-path="assets/" data-template="vertical-menu-template" data-bs-theme="light">

<head>
    <meta charset="utf-8" />
    <meta name="viewport"
        content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
    <title>SYSGAR - Garantia BrotherMotos</title>
    <meta name="description" content="" />
    <link rel="icon" type="image/x-icon" href="assets/img/ico2.png" />

    <link rel="stylesheet" href="assets/vendor/fonts/iconify-icons.css" />
    <link rel="stylesheet" href="assets/vendor/libs/pickr/pickr-themes.css" />
    <link rel="stylesheet" href="assets/vendor/css/core.css" />
    <link rel="stylesheet" href="assets/css/demo.css" />
    <link rel="stylesheet" href="assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />
    <script src="assets/vendor/js/helpers.js"></script>
    <script src="assets/vendor/js/template-customizer.js"></script>
    <script src="assets/js/config.js"></script>

</head>

<body>
    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">

            <?php require("sidebar.php"); ?>

            <div class="layout-page">

                <?php require("navbar.php"); ?>

                <div class="content-wrapper">
                    <?php include $allowedPages[$page]; ?>
                </div>

                <?php if ($page !== 'videos'): 
                    ?>
                <?php require("footer.php"); ?>
                <?php endif; ?>

                <div class="content-backdrop fade"></div>
            </div>
        </div>
    </div>
    <div class="layout-overlay layout-menu-toggle"></div>
    <div class="drag-target"></div>
    </div>

    <script src="assets/vendor/js/bootstrap.js"></script>
    <script src="assets/vendor/libs/@algolia/autocomplete-js.js"></script>
    <script src="assets/vendor/libs/pickr/pickr.js"></script>
    <script src="assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>
    <script src="assets/vendor/libs/hammer/hammer.js"></script>
    <script src="assets/vendor/libs/i18n/i18n.js"></script>
    <script src="assets/vendor/js/menu.js"></script>
    <script src="assets/js/main.js"></script>

    <!-- Início do Código do Assistente Virtual -->
    <!-- <script type="text/javascript" nonce="{place_your_nonce_value_here}" src="https://gc.zohopublic.com/org/870215967/flows/18677000000005001/embed/script" defer></script> -->
    <!-- Fim do Código do Assistente Virtual -->
</body>

</html>

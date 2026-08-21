<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Caminho correto: .env dentro do frontend
$path_env = realpath(__DIR__ . '/.env');

if ($path_env === false || !file_exists($path_env)) {
    http_response_code(500);
    die("Erro crítico: Arquivo de configuração .env não encontrado.");
}

$env = parse_ini_file($path_env, false, INI_SCANNER_TYPED);

if ($env === false) {
    http_response_code(500);
    die("Erro crítico: Falha ao ler o arquivo .env");
}

try {
    $pdo_sidebar = new PDO(
        "mysql:host={$env['MYSQL_HOST']};port={$env['MYSQL_PORT']};dbname={$env['MYSQL_NAME']};charset=utf8",
        $env['MYSQL_USER'],
        $env['MYSQL_PASSWORD']
    );
    $pdo_sidebar->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erro na conexão com o banco de dados: " . $e->getMessage());
}

$current_page = $_GET['page'] ?? 'dashboard';
$user_role    = $_SESSION['user_tipo'] ?? '';

// 3. Função para buscar menus com base no PERFIL (Role)
function getMenuTree($conn, $role) {
    $role = strtolower($role);

    if ($role === 'admin') {
        $sql = "SELECT * FROM sidebar_menu WHERE status = 1 ORDER BY position ASC, id ASC";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
    } else {
        $sql = "SELECT DISTINCT m.* FROM sidebar_menu m
                INNER JOIN permissions p ON m.id = p.menu_id
                WHERE m.status = 1 AND p.role = :role
                ORDER BY m.position ASC, m.id ASC";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(':role', $role);
        $stmt->execute();
    }

    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $menuTree = [];
    $ref = [];

    // Indexa itens - USANDO REFERÊNCIA
    foreach ($items as &$item) {
        $item['children'] = [];
        $ref[$item['id']] = &$item;
    }
    unset($item); // LIMPEZA DE REFERÊNCIA IMPORTANTE

    // Monta a árvore - USANDO REFERÊNCIA
    foreach ($items as &$item) {
        if ($item['parent_id'] == null) {
            $menuTree[] = &$item;
        } else {
            if (isset($ref[$item['parent_id']])) {
                $ref[$item['parent_id']]['children'][] = &$item;
            }
        }
    }
    unset($item); // LIMPEZA DE REFERÊNCIA IMPORTANTE
    
    return $menuTree;
}

function isChildActive($children, $currentPage) {
    foreach ($children as $child) {
        if ($child['page_name'] === $currentPage) return true;
    }
    return false;
}

$menus = getMenuTree($pdo_sidebar, $user_role);

// =========================================================================
// INÍCIO DA INJEÇÃO DOS MENUS MANUAIS
// =========================================================================
$user_role_lower = strtolower($user_role);
if (in_array($user_role_lower, ['sac', 'admin'])) {
    foreach ($menus as &$menu) { // USANDO REFERÊNCIA
        if ($menu['title'] === 'Consultar Garantia') {

            $subMenuStatusValor = [
                'id' => 'manual_status_valor',
                'title' => 'Status Valor',
                'link' => 'index.php?page=rptGarStatusValores',
                'page_name' => 'rptGarStatusValores',
                'icon' => '',
                'type' => 'link',
                'children' => []
            ];

            $subMenuStatusValorV2 = [
                'id' => 'manual_status_valor_v2',
                'title' => 'Status Valor v2',
                'link' => 'index.php?page=rptGarStatusValores_2',
                'page_name' => 'rptGarStatusValores_2',
                'icon' => '',
                'type' => 'link',
                'children' => []
            ];

            $subMenuValoresAprovados = [
                'id' => 'manual_valores_aprovados',
                'title' => 'Valores Aprovados',
                'link' => 'index.php?page=rptValoresAprovados',
                'page_name' => 'rptValoresAprovados',
                'icon' => '',
                'type' => 'link',
                'children' => []
            ];

            $posicaoPesquisar = -1;
            foreach ($menu['children'] as $index => $sub) {
                if ($sub['title'] === 'Pesquisar') {
                    $posicaoPesquisar = $index;
                    break;
                }
            }

            if ($posicaoPesquisar !== -1) {
                array_splice($menu['children'], $posicaoPesquisar + 1, 0, [
                    $subMenuStatusValor,
                    $subMenuStatusValorV2,
                    $subMenuValoresAprovados
                ]);
            } else {
                $menu['children'][] = $subMenuStatusValor;
                $menu['children'][] = $subMenuStatusValorV2;
                $menu['children'][] = $subMenuValoresAprovados;
            }
        }
    }
    unset($menu); // LIMPEZA DE REFERÊNCIA - ISSO RESOLVE O ÚLTIMO ITEM DUPLICADO
}
// =========================================================================
// FIM DA INJEÇÃO
// =========================================================================

?>

<!-- Menu HTML -->
<aside id="layout-menu" class="layout-menu menu-vertical menu">
    <div class="app-brand demo">
        <a href="index.php?page=dashboard" class="app-brand-link">
            <span class="app-brand-logo demo">
                <span class="text-primary">
                    <img src="assets/img/ico.png" alt="Logo SYSGAR" class="icon-base"
                        style="width: 30px; height: 30px;">
                </span>
            </span>
            <img src="assets/img/logo.png" alt="Logo SYSGAR" class="app-brand-text demo menu-text fw-bolder ms-2"
                style="width: 100px; height: 30px;">
        </a>

        <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto">
            <i class="icon-base ti menu-toggle-icon d-none d-xl-block"></i>
            <i class="icon-base ti tabler-x d-block d-xl-none"></i>
        </a>
    </div>

    <div class="menu-inner-shadow"></div>

    <ul class="menu-inner py-1">

        <?php foreach ($menus as $menu): ?>

        <?php 
            // -- TIPO: HEADER (Separador de seção) --
            // Dica: Poderíamos verificar se o header tem itens abaixo, mas vamos exibir se tiver permissão
            if ($menu['type'] === 'header'): 
            ?>
        <li class="menu-header small">
            <span class="menu-header-text" data-i18n="<?php echo htmlspecialchars($menu['title']); ?>">
                <?php echo htmlspecialchars($menu['title']); ?>
            </span>
        </li>

        <?php 
            // -- TIPO: DROPDOWN (Menu com filhos) --
            elseif ($menu['type'] === 'dropdown'): 
                
                // IMPORTANTE: Se o usuário tem permissão para ver o menu PAI, mas NENHUM FILHO,
                // ocultamos o menu pai para não ficar um dropdown vazio.
                if (empty($menu['children'])) {
                    continue; 
                }

                $isActiveOpen = isChildActive($menu['children'], $current_page);
                $openClass = $isActiveOpen ? 'active open' : '';
                $styleBlock = $isActiveOpen ? 'display: block;' : '';
            ?>
        <li class="menu-item <?php echo $openClass; ?>">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon icon-base <?php echo htmlspecialchars($menu['icon']); ?>"></i>
                <div data-i18n="<?php echo htmlspecialchars($menu['title']); ?>">
                    <?php echo htmlspecialchars($menu['title']); ?>
                </div>
            </a>
            <ul class="menu-sub" style="<?php echo $styleBlock; ?>">
                <?php foreach ($menu['children'] as $sub): 
                            $activeClass = ($sub['page_name'] === $current_page) ? 'active' : '';
                        ?>
                <li class="menu-item <?php echo $activeClass; ?>">
                    <a href="<?php echo htmlspecialchars($sub['link']); ?>" class="menu-link">
                        <div data-i18n="<?php echo htmlspecialchars($sub['title']); ?>">
                            <?php echo htmlspecialchars($sub['title']); ?>
                        </div>
                    </a>
                </li>
                <?php endforeach; ?>
            </ul>
        </li>

        <?php 
            // -- TIPO: LINK (Link direto no nível raiz) --
            else: 
                $activeClass = ($menu['page_name'] === $current_page) ? 'active' : '';
            ?>
        <li class="menu-item <?php echo $activeClass; ?>">
            <a href="<?php echo htmlspecialchars($menu['link']); ?>" class="menu-link">
                <?php if(!empty($menu['icon'])): ?>
                <i class="menu-icon icon-base <?php echo htmlspecialchars($menu['icon']); ?>"></i>
                <?php endif; ?>
                <div data-i18n="<?php echo htmlspecialchars($menu['title']); ?>">
                    <?php echo htmlspecialchars($menu['title']); ?>
                </div>
            </a>
        </li>

        <?php endif; ?>

        <?php endforeach; ?>

    </ul>
</aside>

<div class="menu-mobile-toggler d-xl-none rounded-1">
    <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large text-bg-secondary p-2 rounded-1">
        <i class="ti tabler-menu icon-base"></i>
        <i class="ti tabler-chevron-right icon-base"></i>
    </a>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const activeMenuItems = document.querySelectorAll('.menu-item.active.open');
    activeMenuItems.forEach(function(menuItem) {
        const menuSub = menuItem.querySelector('.menu-sub');
        if (menuSub) {
            menuSub.style.display = 'block';
        }
    });
});
</script>

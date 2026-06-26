<?php
// Require database connection and authentication
require_once dirname(__DIR__) . '/config/db.php';
require_once __DIR__ . '/auth.php';
requireAdminAuth();

$currentPage = basename($_SERVER['SCRIPT_NAME']);

$menuItems = [
    [
        'menuTitle' => 'Dashboard',
        'icon' => 'fas fa-home',
        'pages' => [
            ['title' => 'Home', 'url' => 'index.php']
        ],
    ],
    [
        'menuTitle' => 'Clients',
        'icon' => 'fas fa-building',
        'pages' => [
            ['title' => 'All Clients', 'url' => 'clients.php'],
            ['title' => 'Add Client', 'url' => 'client-add.php']
        ],
    ],
    [
        'menuTitle' => 'Products',
        'icon' => 'fas fa-box',
        'pages' => [
            ['title' => 'All Products', 'url' => 'products.php'],
            ['title' => 'Add Product', 'url' => 'product-add.php']
        ],
    ],
    [
        'menuTitle' => 'Settings',
        'icon' => 'fas fa-cog',
        'pages' => [
            ['title' => 'Profile', 'url' => 'profile.php']
        ],
    ]
];

$active_pageInfo = null;
foreach ($menuItems as $menuItem) {
    foreach ($menuItem['pages'] as $page) {
        if ($currentPage === $page['url']) {
            $active_pageInfo = [
                'breadcrumb_Items' => [
                    ['title' => $menuItem['menuTitle'], 'url' => '#'],
                    ['title' => $page['title'], 'url' => $page['url']]
                ],
                'page_title' => $page['title'],
                'active_menu' => $menuItem,
                'active_page' => $page
            ];
            break 2;
        }
    }
}

$breadcrumb_Items = $active_pageInfo['breadcrumb_Items'] ?? [];
$page_title = $active_pageInfo['page_title'] ?? '';
$active_menu = $active_pageInfo['active_menu'] ?? null;
$active_page = $active_pageInfo['active_page'] ?? null;

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($page_title) ?> - YTech Panels</title>
    <link rel="icon" href="../assets/logo.png" type="image/png">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.10.5/dist/sweetalert2.all.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
    <script src="https://cdn.tiny.cloud/1/6hgmjx715ksgmj4wbwn70jmo6doz07dpvutf1735hpbw73n3/tinymce/8/tinymce.min.js" referrerpolicy="origin" crossorigin="anonymous"></script>
    <style>
        :root{--wp-admin-bg:#f0f0f1;--wp-white:#fff;--wp-dark:#1d2327;--wp-text:#3c434a;--wp-text-light:#646970;--wp-border:#c3c4c7;--wp-blue:#2271b1;--wp-blue-dark:#135e96;--wp-green:#00a32a;--wp-red:#b32d2e;--wp-yellow:#dba617;--wp-sidebar-bg:#1d2327;--wp-sidebar-text:#f0f0f1;--wp-sidebar-hover:#2c3338;--wp-sidebar-active:#2271b1}
        body{font-family:"Inter",-apple-system,BlinkMacSystemFont,sans-serif;background:var(--wp-admin-bg);color:var(--wp-text)}
        .main-sidebar{background:var(--wp-sidebar-bg)!important;border-right:none!important;box-shadow:none!important}
        .brand-link{background:var(--wp-sidebar-bg)!important;border-bottom:1px solid rgba(255,255,255,.08)!important;padding:10px 20px!important;display:flex!important;justify-content:center!important;align-items:center!important;margin:0!important;height:65px}
        .brand-link .brand-image{float:none!important;margin:0!important;max-height:45px;width:auto;}
        .user-panel{border-bottom:1px solid rgba(255,255,255,.08)!important;margin:0!important;padding:14px 16px!important}
        .user-panel .image img{width:35px;height:35px;border-radius:50%;border:2px solid rgba(255,255,255,.2)}
        .user-panel .info{color:var(--wp-sidebar-text)!important;font-weight:500;font-size:.85rem}
        .nav-sidebar>.nav-item{margin-bottom:0!important;border-bottom:1px solid rgba(255,255,255,.05)}
        .nav-sidebar .nav-link{color:rgba(255,255,255,.75)!important;padding:11px 16px!important;border-radius:0!important;margin:0!important;position:relative;transition:all .15s;font-size:.875rem;font-weight:400}
        .nav-sidebar .nav-link:hover{background:var(--wp-sidebar-hover)!important;color:#fff!important}
        .nav-sidebar .nav-link.active{background:var(--wp-sidebar-active)!important;color:#fff!important;box-shadow:none!important}
        .nav-sidebar .nav-item.has-treeview > .nav-link.active{background:transparent!important;}
        .nav-sidebar .nav-link.active::before{content:"";position:absolute;left:0;top:0;bottom:0;width:4px;background:#fff}
        .nav-sidebar .nav-link.active i.nav-icon{color:#fff!important}
        .nav-sidebar .nav-icon{color:rgba(255,255,255,.5);margin-right:10px!important;width:20px;text-align:center;font-size:.9rem;transition:color .15s}
        .nav-link:hover .nav-icon,.nav-link.active .nav-icon,.menu-open>.nav-link .nav-icon{color:#fff!important}
        .nav-treeview{background:rgba(0,0,0,.15)!important;padding:0!important}
        .nav-treeview>.nav-item{border-bottom:1px dashed rgba(255,255,255,.15);}
        .nav-treeview>.nav-item:last-child{border-bottom:none;}
        .nav-treeview>.nav-item>.nav-link{padding-left:48px!important;font-size:.8rem;color:rgba(255,255,255,.6)!important}
        .nav-treeview>.nav-item>.nav-link:hover{color:#fff!important;background:rgba(255,255,255,.05)!important}
        .nav-treeview>.nav-item>.nav-link.active{color:#fff!important;background:var(--wp-sidebar-active)!important;font-weight:500}
        .nav-treeview>.nav-item>.nav-link.active::after{content:"";position:absolute;right:0;top:10px;bottom:10px;width:3px;background:var(--wp-yellow);border-radius:2px 0 0 2px}
        .submenu-icon{font-size:10px!important;opacity:.5;margin-right:14px!important;width:auto!important}
        .nav-link.active .submenu-icon,.nav-link:hover .submenu-icon{opacity:1}
        .nav-sidebar .right{font-size:.75rem!important;top:1.1rem!important;color:rgba(255,255,255,.4)!important;transition:all .2s;margin-top:-2px}
        .nav-sidebar .right::before{content:"\f067";font-family:"Font Awesome 6 Free";font-weight:900}
        .menu-open>.nav-link .right::before{content:"\f068"}
        .menu-open>.nav-link .right{transform:none!important;color:#fff!important}
        .sidebar-collapse .main-sidebar{width:60px!important}
        .sidebar-collapse .brand-link{padding:10px!important}
        .sidebar-collapse .brand-link .brand-image{max-height:28px}
        .sidebar-collapse .user-panel{padding:12px 0!important;display:flex!important;justify-content:center!important}
        .sidebar-collapse .user-panel a{display:flex!important;justify-content:center!important;width:100%!important;padding:0!important}
        .sidebar-collapse .user-panel .image{padding:0!important;margin:0!important;display:flex!important;justify-content:center!important}
        .sidebar-collapse .user-panel .info{display:none!important}
        .sidebar-collapse .nav-sidebar .nav-link{padding:12px 0!important;display:flex!important;justify-content:center!important}
        .sidebar-collapse .nav-sidebar .nav-icon{margin:0!important}
        .sidebar-collapse .nav-sidebar .nav-link::before{display:none}
        .sidebar-collapse .nav-sidebar .nav-link.active{border-left:3px solid var(--wp-sidebar-active);background:transparent!important}
        @media(min-width:992px){.sidebar-mini.sidebar-collapse .main-sidebar:not(.sidebar-no-expand) .nav-item:hover>.nav-link>p{display:block!important;position:absolute;left:60px;top:0;width:200px;margin:0!important;padding:11px 16px!important;background:var(--wp-sidebar-hover)!important;color:#fff!important;border-radius:0 4px 4px 0;box-shadow:2px 2px 10px rgba(0,0,0,.3);z-index:1000;pointer-events:none;font-weight:500;font-size:.875rem}.sidebar-mini.sidebar-collapse .main-sidebar:not(.sidebar-no-expand) .nav-item:hover>.nav-treeview{display:block!important;position:absolute;left:60px;top:42px;width:200px;background:var(--wp-sidebar-hover)!important;box-shadow:2px 5px 10px rgba(0,0,0,.3);border:1px solid rgba(255,255,255,.1);border-left:none;z-index:999}.sidebar-mini.sidebar-collapse .main-sidebar:not(.sidebar-no-expand) .nav-item:hover>.nav-treeview .nav-link{padding-left:16px!important;justify-content:flex-start!important}}
        .main-header.navbar{background:var(--wp-white)!important;border-bottom:1px solid var(--wp-border)!important;padding:0 16px!important;height:46px!important;box-shadow:none!important}
        .main-header.navbar .nav-link{color:var(--wp-text)!important;height:46px;display:flex;align-items:center}
        .main-header.navbar .nav-link:hover{color:var(--wp-blue)!important}
        .navbar-nav.form-inline,.navbar-nav.ml-auto,.navbar-nav.ms-auto{display:none!important}
        .content-header{background:var(--wp-white)!important;border-bottom:1px solid var(--wp-border)!important;padding:12px 20px!important}
        .content-header h1{font-size:1.25rem;font-weight:600;color:var(--wp-dark);margin:0}
        .breadcrumb{background:transparent!important;padding:0!important;margin:0!important;font-size:13px}
        .breadcrumb-item a{color:var(--wp-blue)!important;text-decoration:none}
        .breadcrumb-item a:hover{text-decoration:underline}
        .breadcrumb-item.active{color:var(--wp-text-light)!important}
        .content-wrapper{background:var(--wp-admin-bg)!important}
        .content-wrapper .content{padding:20px!important}
        .main-footer{background:var(--wp-white)!important;border-top:1px solid var(--wp-border)!important;color:var(--wp-text-light);padding:12px 20px;font-size:13px}
        .main-footer a{color:var(--wp-blue);text-decoration:none}
        .main-footer a:hover{text-decoration:underline}
        .nav-item-logout .nav-link{border-top:1px solid rgba(255,255,255,.08)!important;margin-top:4px}
    </style>
</head>
<body class="hold-transition sidebar-mini">
    <div class="wrapper">
        <nav class="main-header navbar navbar-expand navbar-white navbar-light">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars" style="font-size:16px"></i></a>
                </li>
            </ul>
        </nav>

        <div class="content-header">
            <div class="row mb-0 align-items-center">
                <div class="col-sm-6"><h1 class="m-0"><?= htmlspecialchars($page_title) ?></h1></div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="./"><i class="fas fa-home"></i></a></li>
                        <?php foreach ($breadcrumb_Items as $item): ?>
                            <li class="breadcrumb-item <?= $item['url'] === '#' ? 'active' : '' ?>">
                                <?= $item['url'] === '#' ? htmlspecialchars($item['title']) : "<a href='" . htmlspecialchars($item['url']) . "'>" . htmlspecialchars($item['title']) . "</a>" ?>
                            </li>
                        <?php endforeach; ?>
                    </ol>
                </div>
            </div>
        </div>

        <aside class="main-sidebar sidebar-dark-primary elevation-0">
            <a href="./" class="brand-link">
                <img src="../assets/logo.png" alt="YTech Panels" class="brand-image">
            </a>
            <div class="sidebar">
                <div class="user-panel mt-3 pb-3 mb-3">
                    <a href="./profile.php" class="d-flex align-items-center">
                        <div class="image">
                            <?php $headerProfilePic = !empty($_SESSION['admin_profile_pic']) ? './src/images/profile_picture/' . htmlspecialchars($_SESSION['admin_profile_pic']) : './src/images/user-avtar.png'; ?>
                            <img src="<?= $headerProfilePic ?>" class="img-circle" alt="User">
                        </div>
                        <div class="info"><?= htmlspecialchars($_SESSION['admin_name'] ?? 'Admin') ?></div>
                    </a>
                </div>
                <nav class="mt-2">
                    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu">
                        <?php foreach ($menuItems as $menuItem): ?>
                            <li class="nav-item has-treeview <?= $menuItem === $active_menu ? 'menu-open' : '' ?>">
                                <a class="nav-link <?= $menuItem === $active_menu ? 'active' : '' ?>" href="#">
                                    <i class="nav-icon <?= $menuItem['icon'] ?>"></i>
                                    <p>
                                        <?= $menuItem['menuTitle'] ?>
                                        <?php if (!empty($menuItem['pages'])): ?>
                                            <i class="right fas toggle-icon"></i>
                                        <?php endif; ?>
                                    </p>
                                </a>
                                <?php if (!empty($menuItem['pages'])): ?>
                                    <ul class="nav nav-treeview">
                                        <?php foreach ($menuItem['pages'] as $page): ?>
                                            <li class="nav-item">
                                                <a href="<?= $page['url'] ?>" class="nav-link <?= $page === $active_page ? 'active' : '' ?>">
                                                    <i class="fas fa-angle-right nav-icon submenu-icon"></i>
                                                    <p><?= $page['title'] ?></p>
                                                </a>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                <?php endif; ?>
                            </li>
                        <?php endforeach; ?>
                        <li class="nav-item nav-item-logout" onclick="logout()">
                            <a href="javascript:void(0);" class="nav-link">
                                <i class="nav-icon fas fa-sign-out-alt"></i>
                                <p>Logout</p>
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>
        </aside>

        <div class="content-wrapper">
            <section class="content">
                <div class="container-fluid">

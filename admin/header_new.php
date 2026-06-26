<?php
// Require authentication
require_once __DIR__ . '/auth.php';
requireAdminAuth();

$currentPage = basename($_SERVER['SCRIPT_NAME']);

$menuItems = [
    [
        "menuTitle" => "Dashboard",
        "icon" => "fas fa-home",
        "pages" => [
            ["title" => "Home", "url" => "index.php"]
        ],
    ],
    [
        "menuTitle" => "Clients",
        "icon" => "fas fa-building",
        "pages" => [
            ["title" => "All Clients", "url" => "clients.php"],
            ["title" => "Add Client", "url" => "client-add.php"]
        ],
    ],
    [
        "menuTitle" => "Settings",
        "icon" => "fas fa-cog",
        "pages" => [
            ["title" => "Profile", "url" => "profile.php"]
        ],
    ]
];

$active_pageInfo = null;
foreach ($menuItems as $menuItem) {
    foreach ($menuItem['pages'] as $page) {
        if ($currentPage === $page['url']) {
            $active_pageInfo = [
                "breadcrumb_Items" => [
                    ["title" => $menuItem['menuTitle'], "url" => "#"],
                    ["title" => $page['title'], "url" => $page['url']]
                ],
                "page_title" => $page['title'],
                "active_menu" => $menuItem,
                "active_page" => $page
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
    <style>
        :root {
            --wp-admin-bg: #f0f0f1;
            --wp-white: #ffffff;
            --wp-dark: #1d2327;
            --wp-text: #3c434a;
            --wp-text-light: #646970;
            --wp-border: #c3c4c7;
            --wp-blue: #2271b1;
            --wp-blue-dark: #135e96;
            --wp-green: #00a32a;
            --wp-red: #b32d2e;
            --wp-yellow: #dba617;
            --wp-sidebar-bg: #1d2327;
            --wp-sidebar-text: #f0f0f1;
            --wp-sidebar-hover: #2c3338;
            --wp-sidebar-active: #2271b1;
        }
        body { font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif; background: var(--wp-admin-bg); color: var(--wp-text); }

        /* Sidebar */
        .main-sidebar { background: var(--wp-sidebar-bg) !important; border-right: none !important; box-shadow: none !important; }
        .brand-link { background: var(--wp-sidebar-bg) !important; border-bottom: 1px solid rgba(255,255,255,0.08) !important; padding: 14px 20px !important; display: flex !important; justify-content: center !important; align-items: center !important; margin: 0 !important; height: 58px; }
        .brand-link .brand-image { float: none !important; margin: 0 !important; max-height: 30px; width: auto; filter: brightness(0) invert(1); }
        .user-panel { border-bottom: 1px solid rgba(255,255,255,0.08) !important; margin: 0 !important; padding: 14px 16px !important; }
        .user-panel .image img { width: 35px; height: 35px; border-radius: 50%; border: 2px solid rgba(255,255,255,0.2); }
        .user-panel .info { color: var(--wp-sidebar-text) !important; font-weight: 500; font-size: 0.85rem; }

        /* Nav Items */
        .nav-sidebar > .nav-item { margin-bottom: 0 !important; border-bottom: 1px solid rgba(255,255,255,0.05); }
        .nav-sidebar .nav-link { color: rgba(255,255,255,0.75) !important; padding: 11px 16px !important; border-radius: 0 !important; margin: 0 !important; position: relative; transition: all 0.15s; font-size: 0.875rem; font-weight: 400; }
        .nav-sidebar .nav-link:hover { background: var(--wp-sidebar-hover) !important; color: #fff !important; }
        .nav-sidebar .nav-link.active { background: var(--wp-sidebar-active) !important; color: #fff !important; box-shadow: none !important; }
        .nav-sidebar .nav-link.active::before { content: ''; position: absolute; left: 0; top: 0; bottom: 0; width: 4px; background: #fff; }
        .nav-sidebar .nav-link.active i.nav-icon { color: #fff !important; }
        .nav-sidebar .nav-icon { color: rgba(255,255,255,0.5); margin-right: 10px !important; width: 20px; text-align: center; font-size: 0.9rem; transition: color 0.15s; }
        .nav-link:hover .nav-icon, .nav-link.active .nav-icon, .menu-open > .nav-link .nav-icon { color: #fff !important; }

        /* Submenu */
        .nav-treeview { background: rgba(0,0,0,0.15) !important; padding: 0 !important; }
        .nav-treeview > .nav-item > .nav-link { padding-left: 48px !important; font-size: 0.8rem; color: rgba(255,255,255,0.6) !important; }
        .nav-treeview > .nav-item > .nav-link:hover { color: #fff !important; background: rgba(255,255,255,0.05) !important; }
        .nav-treeview > .nav-item > .nav-link.active { color: #fff !important; background: var(--wp-sidebar-active) !important; font-weight: 500; }
        .nav-treeview > .nav-item > .nav-link.active::after { content: ''; position: absolute; right: 0; top: 10px; bottom: 10px; width: 3px; background: var(--wp-yellow); border-radius: 2px 0 0 2px; }
        .submenu-icon { font-size: 6px !important; opacity: 0.5; margin-right: 14px !important; width: auto !important; }
        .nav-link.active .submenu-icon, .nav-link:hover .submenu-icon { opacity: 1; }

        /* Toggle */
        .nav-sidebar .right { font-size: 0.75rem !important; top: 1.1rem !important; color: rgba(255,255,255,0.4) !important; transition: all 0.2s; margin-top: -2px; }
        .nav-sidebar .right::before { content: "\f067"; font-family: "Font Awesome 6 Free"; font-weight: 900; }
        .menu-open > .nav-link .right::before { content: "\f068"; }
        .menu-open > .nav-link .right { transform: none !important; color: #fff !important; }

        /* Collapsed */
        .sidebar-collapse .main-sidebar { width: 60px !important; }
        .sidebar-collapse .brand-link { padding: 10px !important; }
        .sidebar-collapse .brand-link .brand-image { max-height: 28px; }
        .sidebar-collapse .user-panel { padding: 12px 0 !important; display: flex !important; justify-content: center !important; }
        .sidebar-collapse .user-panel a { display: flex !important; justify-content: center !important; width: 100% !important; padding: 0 !important; }
        .sidebar-collapse .user-panel .image { padding: 0 !important; margin: 0 !important; display: flex !important; justify-content: center !important; }
        .sidebar-collapse .user-panel .info { display: none !important; }
        .sidebar-collapse .nav-sidebar .nav-link { padding: 12px 0 !important; display: flex !important; justify-content: center !important; }
        .sidebar-collapse .nav-sidebar .nav-icon { margin: 0 !important; }
        .sidebar-collapse .nav-sidebar .nav-link::before { display: none; }
        .sidebar-collapse .nav-sidebar .nav-link.active { border-left: 3px solid var(--wp-sidebar-active); background: transparent !impo

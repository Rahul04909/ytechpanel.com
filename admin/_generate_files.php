<?php
/**
 * Temporary script to generate header.php and profile.php
 * Run once: php admin/_generate_files.php
 * Then delete: rm admin/_generate_files.php
 */

// ===== HEADER.PHP =====
$header = <<<EOT
<?php
// Require authentication
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
    <style>
        :root{--wp-admin-bg:#f0f0f1;--wp-white:#fff;--wp-dark:#1d2327;--wp-text:#3c434a;--wp-text-light:#646970;--wp-border:#c3c4c7;--wp-blue:#2271b1;--wp-blue-dark:#135e96;--wp-green:#00a32a;--wp-red:#b32d2e;--wp-yellow:#dba617;--wp-sidebar-bg:#1d2327;--wp-sidebar-text:#f0f0f1;--wp-sidebar-hover:#2c3338;--wp-sidebar-active:#2271b1}
        body{font-family:"Inter",-apple-system,BlinkMacSystemFont,sans-serif;background:var(--wp-admin-bg);color:var(--wp-text)}
        .main-sidebar{background:var(--wp-sidebar-bg)!important;border-right:none!important;box-shadow:none!important}
        .brand-link{background:var(--wp-sidebar-bg)!important;border-bottom:1px solid rgba(255,255,255,.08)!important;padding:14px 20px!important;display:flex!important;justify-content:center!important;align-items:center!important;margin:0!important;height:58px}
        .brand-link .brand-image{float:none!important;margin:0!important;max-height:30px;width:auto;filter:brightness(0) invert(1)}
        .user-panel{border-bottom:1px solid rgba(255,255,255,.08)!important;margin:0!important;padding:14px 16px!important}
        .user-panel .image img{width:35px;height:35px;border-radius:50%;border:2px solid rgba(255,255,255,.2)}
        .user-panel .info{color:var(--wp-sidebar-text)!important;font-weight:500;font-size:.85rem}
        .nav-sidebar>.nav-item{margin-bottom:0!important;border-bottom:1px solid rgba(255,255,255,.05)}
        .nav-sidebar .nav-link{color:rgba(255,255,255,.75)!important;padding:11px 16px!important;border-radius:0!important;margin:0!important;position:relative;transition:all .15s;font-size:.875rem;font-weight:400}
        .nav-sidebar .nav-link:hover{background:var(--wp-sidebar-hover)!important;color:#fff!important}
        .nav-sidebar .nav-link.active{background:var(--wp-sidebar-active)!important;color:#fff!important;box-shadow:none!important}
        .nav-sidebar .nav-link.active::before{content:"";position:absolute;left:0;top:0;bottom:0;width:4px;background:#fff}
        .nav-sidebar .nav-link.active i.nav-icon{color:#fff!important}
        .nav-sidebar .nav-icon{color:rgba(255,255,255,.5);margin-right:10px!important;width:20px;text-align:center;font-size:.9rem;transition:color .15s}
        .nav-link:hover .nav-icon,.nav-link.active .nav-icon,.menu-open>.nav-link .nav-icon{color:#fff!important}
        .nav-treeview{background:rgba(0,0,0,.15)!important;padding:0!important}
        .nav-treeview>.nav-item>.nav-link{padding-left:48px!important;font-size:.8rem;color:rgba(255,255,255,.6)!important}
        .nav-treeview>.nav-item>.nav-link:hover{color:#fff!important;background:rgba(255,255,255,.05)!important}
        .nav-treeview>.nav-item>.nav-link.active{color:#fff!important;background:var(--wp-sidebar-active)!important;font-weight:500}
        .nav-treeview>.nav-item>.nav-link.active::after{content:"";position:absolute;right:0;top:10px;bottom:10px;width:3px;background:var(--wp-yellow);border-radius:2px 0 0 2px}
        .submenu-icon{font-size:6px!important;opacity:.5;margin-right:14px!important;width:auto!important}
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
                            <img src="./src/images/<?= htmlspecialchars($_SESSION['admin_profile_pic'] ?? 'user-avtar.png') ?>" class="img-circle" alt="User">
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
                                                    <i class="fas fa-circle nav-icon submenu-icon"></i>
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
EOT;

file_put_contents(__DIR__ . '/header.php', $header);
echo "header.php written (" . strlen($header) . " bytes)\n";


// ===== PROFILE.PHP =====
$profile = <<<EOT
<?php
/**
 * YTech Panels - Admin Profile Page
 */
include './header.php';

$db = getDB();
$adminId = (int) $_SESSION['admin_id'];

$stmt = $db->prepare("SELECT id, name, email, mobile, username, profile_pic, last_login, created_at FROM admin_users WHERE id = :id");
$stmt->execute([':id' => $adminId]);
$admin = $stmt->fetch();

$profilePicPath = !empty($admin['profile_pic'])
    ? './src/images/profile_picture/' . htmlspecialchars($admin['profile_pic'])
    : './src/images/user-avtar.png';
?>

<div class="row">
    <div class="col-lg-4">
        <div class="profile-avatar-section">
            <div class="profile-avatar-wrapper">
                <img src="<?= $profilePicPath ?>" alt="Profile" class="profile-avatar" id="avatarPreview">
                <label class="avatar-overlay" for="avatarInput" title="Change profile picture"><i class="fas fa-camera"></i></label>
            </div>
            <div class="profile-name-display"><?= htmlspecialchars($admin['name']) ?></div>
            <div class="profile-email-display"><?= htmlspecialchars($admin['email']) ?></div>
        </div>
        <div class="profile-card">
            <div class="profile-card-header"><i class="fas fa-info-circle"></i> Account Information</div>
            <div class="profile-card-body">
                <div class="info-grid">
                    <div class="info-item"><label>Username</label><span><?= htmlspecialchars($admin['username']) ?></span></div>
                    <div class="info-item"><label>Mobile</label><span><?= htmlspecialchars($admin['mobile'] ?? '---') ?></span></div>
                    <div class="info-item"><label>Last Login</label><span><?= $admin['last_login'] ? date('d M Y, h:i A', strtotime($admin['last_login'])) : '---' ?></span></div>
                    <div class="info-item"><label>Member Since</label><span><?= $admin['created_at'] ? date('d M Y', strtotime($admin['created_at'])) : '---' ?></span></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-8">
        <div class="profile-card">
            <div class="profile-card-header"><i class="fas fa-user-edit"></i> Edit Profile</div>
            <div class="profile-card-body">
                <form id="profileForm" enctype="multipart/form-data">
                    <input type="hidden" name="action" value="update_profile">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
                    <input type="file" name="profile_pic" id="avatarInput" accept="image/*" style="display:none;">
                    <div class="row">
                        <div class="col-md-6"><div class="form-group"><label for="name">Full Name *</label><input type="text" class="form-control" id="name" name="name" value="<?= htmlspecialchars($admin['name']) ?>" required maxlength="255"></div></div>
                        <div class="col-md-6"><div class="form-group"><label for="email">Email Address *</label><input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($admin['email']) ?>" required></div></div>
                        <div class="col-md-6"><div class="form-group"><label for="mobile">Mobile Number</label><input type="text" class="form-control" id="mobile" name="mobile" value="<?= htmlspecialchars($admin['mobile'] ?? '') ?>" placeholder="+91-XXXXXXXXXX"></div></div>
                        <div class="col-md-6"><div class="form-group"><label for="username">Username *</label><input type="text" class="form-control" id="username" name="username" value="<?= htmlspecialchars($admin['username']) ?>" required minlength="3" maxlength="50" pattern="[a-zA-Z0-9_]+"><small style="color:#646970;">Letters, numbers, underscores only.</small></div></div>
                    </div>
                    <div class="mt-3"><button type="submit" class="wp-btn-primary" id="profileSubmitBtn"><i class="fas fa-save"></i> Save Changes</button></div>
                </form>
            </div>
        </div>
        <div class="profile-card">
            <div class="profile-card-header"><i class="fas fa-lock"></i> Change Password</div>
            <div class="profile-card-body">
                <form id="passwordForm">
                    <input type="hidden" name="action" value="change_password">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
                    <div class="row">
                        <div class="col-md-12"><div class="form-group"><label for="current_password">Current Password *</label><input type="password" class="form-control" id="current_password" name="current_password" required autocomplete="current-password"></div></div>
                        <div class="col-md-6"><div class="form-group"><label for="new_password">New Password *</label><input type="password" class="form-control" id="new_password" name="new_password" required autocomplete="new-password"><div class="wp-password-strength"><div class="wp-password-strength-bar" id="strengthBar"></div></div><ul class="wp-password-reqs" id="passwordRequirements"><li id="req-length" class="unmet"><i class="fas fa-times-circle"></i> At least 8 characters</li><li id="req-upper" class="unmet"><i class="fas fa-times-circle"></i> One uppercase letter</li><li id="req-lower" class="unmet"><i class="fas fa-times-circle"></i> One lowercase letter</li><li id="req-number" class="unmet"><i class="fas fa-times-circle"></i> One number</li><li id="req-special" class="unmet"><i class="fas fa-times-circle"></i> One special character</li></ul></div></div>
                        <div class="col-md-6"><div class="form-group"><label for="confirm_password">Confirm New Password *</label><input type="password" class="form-control" id="confirm_password" name="confirm_password" required autocomplete="new-password"><small id="matchHint" style="display:none;margin-top:4px;"></small></div></div>
                    </div>
                    <div class="mt-3"><button type="submit" class="wp-btn-danger" id="passwordSubmitBtn"><i class="fas fa-key"></i> Update Password</button></div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
    .profile-card{background:#fff;border:1px solid #e2e8f0;margin-bottom:24px}
    .profile-card-header{background:#f1f1f1;border-bottom:1px solid #e2e8f0;padding:16px 24px;font-weight:600;font-size:15px;color:#1d2327;display:flex;align-items:center;gap:10px}
    .profile-card-header i{color:#2271b1}
    .profile-card-body{padding:24px}
    .profile-avatar-section{text-align:center;padding:30px 24px;background:#fff;border:1px solid #e2e8f0;margin-bottom:24px}
    .profile-avatar-wrapper{position:relative;display:inline-block;margin-bottom:16px}
    .profile-avatar{width:120px;height:120px;border-radius:50%;object-fit:cover;border:4px solid #e2e8f0}
    .avatar-overlay{position:absolute;bottom:4px;right:4px;background:#2271b1;color:#fff;width:32px;height:32px;border-radius:50%;display:flex;align-items:center;justify-content:center;cursor:pointer;border:2px solid #fff;font-size:13px;transition:background .2s}
    .avatar-overlay:hover{background:#135e96}
    .profile-name-display{font-size:20px;font-weight:700;color:#1d2327;margin-bottom:4px}
    .profile-email-display{font-size:13px;color:#646970}
    .info-grid{display:grid;grid-template-columns:1fr 1fr;gap:16px 24px}
    .info-item{margin-bottom:4px}
    .info-item label{font-size:11px;font-weight:600;color:#646970;text-transform:uppercase;letter-spacing:.5px;margin-bottom:4px;display:block}
    .info-item span{font-size:14px;color:#1d2327;font-weight:500}
    .profile-card .form-group label{font-size:13px;font-weight:600;color:#1d2327;margin-bottom:6px;display:block}
    .profile-card .form-control{border:1px solid #8c8f94;border-radius:4px;padding:10px 14px;font-size:14px;transition:border-color .2s;width:100%}
    .profile-card .form-control:focus{border-color:#2271b1;box-shadow:0 0 0 1px #2271b1;outline:none}
    .wp-btn-primary{background:#2271b1;color:#fff;border:1px solid #2271b1;padding:8px 16px;font-weight:600;font-size:13px;cursor:pointer;border-radius:3px;transition:background .2s}
    .wp-btn-primary:hover{background:#135e96;border-color:#135e96}
    .wp-btn-danger{background:#b32d2e;color:#fff;border:1px solid #b32d2e;padding:8px 16px;font-weight:600;font-size:13px;cursor:pointer;border-radius:3px;transition:background .2s}
    .wp-btn-danger:hover{background:#a00;border-color:#a00}
    .wp-password-strength{height:4px;background:#e2e8f0;margin-top:8px;border-radius:2px;overflow:hidden}
    .wp-password-strength-bar{height:100%;width:0;transition:width .3s,background .3s;border-radius:2px}
    .wp-password-reqs{margin-top:8px;font-size:12px;color:#646970;padding-left:0;list-style:none}
    .wp-password-reqs li{margin-bottom:2px}
    .wp-password-reqs li i{margin-right:6px;font-size:11px}
    .wp-password-reqs li.met{color:#00a32a}
    .wp-password-reqs li.unmet{color:#b32d2e}
    @media(max-width:768px){.info-grid{grid-template-columns:1fr}}
</style>

<script>
$(document).ready(function(){
    $("#avatarInput").on("change",function(e){
        var file=e.target.files[0];
        if(file){var r=new FileReader();r.onload=function(ev){$("#avatarPreview").attr("src",ev.target.result)};r.readAsDataURL(file)}
    });
    $("#profileForm").on("submit",function(e){
        e.preventDefault();var btn=$("#profileSubmitBtn");btn.prop("disabled",true).html("<i class=\"fas fa-spinner fa-spin\"></i> Saving...");
        $.ajax({url:"handlers/profile-handler.php",type:"POST",data:new FormData(this),processData:false,contentType:false,dataType:"json",
            success:function(res){if(res.success){Swal.fire({icon:"success",title:"Updated!",text:res.message,timer:2000,showConfirmButton:false}).then(function(){if(res.profile_pic){$(".user-panel img").attr("src","./src/images/profile_picture/"+res.profile_pic)}location.reload()})}else{Swal.fire({icon:"error",title:"Error",text:res.message})}},
            error:function(){Swal.fire({icon:"error",title:"Error",text:"Network error."})},
            complete:function(){btn.prop("disabled",false).html("<i class=\"fas fa-save\"></i> Save Changes")}
        });
    });
    $("#new_password").on("input",function(){
        var pwd=$(this).val(),score=0;
        var checks=[{el:"req-length",test:pwd.length>=8},{el:"req-upper",test:/[A-Z]/.test(pwd)},{el:"req-lower",test:/[a-z]/.test(pwd)},{el:"req-number",test:/\\d/.test(pwd)},{el:"req-special",test:/[^A-Za-z0-9]/.test(pwd)}];
        checks.forEach(function(c){if(c.test){$("#"+c.el).removeClass("unmet").addClass("met").find("i").removeClass("fa-times-circle").addClass("fa-check-circle");score++}else{$("#"+c.el).removeClass("met").addClass("unmet").find("i").removeClass("fa-check-circle").addClass("fa-times-circle")}});
        var pct=(score/5)*100,colors=["#b32d2e","#b32d2e","#dba617","#dba617","#00a32a","#00a32a"];
        $("#strengthBar").css({width:pct+"%",background:colors[score]});
    });
    $("#confirm_password").on("input",function(){
        var np=$("#new_password").val(),c=$(this).val(),h=$("#matchHint");
        if(c.length>0){h.show();if(np===c){h.html("<i class=\"fas fa-check-circle\" style=\"color:#00a32a\"></i> Passwords match").css("color","#00a32a")}else{h.html("<i class=\"fas fa-times-circle\" style=\"color:#b32d2e\"></i> Passwords do not match").css("color","#b32d2e")}}else{h.hide()}
    });
    $("#passwordForm").on("submit",function(e){
        e.preventDefault();var btn=$("#passwordSubmitBtn"),np=$("#new_password").val(),cp=$("#confirm_password").val();
        if(np!==cp){Swal.fire({icon:"error",title:"Mismatch",text:"Passwords do not match."});return}
        if(np.length<8){Swal.fire({icon:"error",title:"Too Short",text:"Password must be at least 8 characters."});return}
        btn.prop("disabled",true).html("<i class=\"fas fa-spinner fa-spin\"></i> Updating...");
        $.ajax({url:"handlers/profile-handler.php",type:"POST",data:$(this).serialize(),dataType:"json",
            success:function(res){if(res.success){Swal.fire({icon:"success",title:"Password Updated!",text:res.message,timer:2000,showConfirmButton:false}).then(function(){$("#passwordForm")[0].reset();$("#strengthBar").css({width:"0%"});$(".wp-password-reqs li").removeClass("met").addClass("unmet").find("i").removeClass("fa-check-circle").addClass("fa-times-circle");$("#matchHint").hide()})}else{Swal.fire({icon:"error",title:"Error",text:res.message})}},
            error:function(){Swal.fire({icon:"error",title:"Error",text:"Network error."})},
            complete:function(){btn.prop("disabled",false).html("<i class=\"fas fa-key\"></i> Update Password")}
        });
    });
});
</script>

<?php include './footer.php'; ?>
EOT;

file_put_contents(__DIR__ . '/profile.php', $profile);
echo "profile.php written (" . strlen($profile) . " bytes)\n";

echo "\nDone! Files generated successfully.\n";
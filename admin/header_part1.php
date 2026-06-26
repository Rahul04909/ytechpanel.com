<?php
// Require authentication
require_once __DIR__ . "/auth.php";
requireAdminAuth();

$currentPage = basename($_SERVER["SCRIPT_NAME"]);

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
    foreach ($menuItem["pages"] as $page) {
        if ($currentPage === $page["url"]) {
            $active_pageInfo = [
                "breadcrumb_Items" => [
                    ["title" => $menuItem["menuTitle"], "url" => "#"],
                    ["title" => $page["title"], "url" => $page["url"]]
                ],
                "page_title" => $page["title"],
                "active_menu" => $menuItem,
                "active_page" => $page
            ];
            break 2;
        }
    }
}

$breadcrumb_Items = $active_pageInfo["breadcrumb_Items"] ?? [];
$page_title = $active_pageInfo["page_title"] ?? "";
$active_menu = $active_pageInfo["active_menu"] ?? null;
$active_page = $active_pageInfo["active_page"] ?? null;

if (empty($_SESSION["csrf_token"])) {
    $_SESSION["csrf_token"] = bin2hex(random_bytes(32));
}
?>
<?php
/**
 * YTech Panels — Dynamic XML Sitemap
 * Generates a fully automated XML sitemap with all static pages and dynamic products.
 * Access via: /sitemap.xml (via .htaccess rewrite) or /sitemap.php
 */

header('Content-Type: application/xml; charset=utf-8');

require_once __DIR__ . '/config/db.php';

$db = getDB();

// ─── Site Configuration ───
$baseUrl = 'https://ytechpanels.com';
$today   = date('Y-m-d');

// ─── Static Pages ───
$staticPages = [
    ['loc' => '',                          'priority' => '1.0', 'changefreq' => 'daily',   'lastmod' => file_exists(__DIR__ . '/index.php') ? date('Y-m-d', filemtime(__DIR__ . '/index.php')) : $today],
    ['loc' => 'about.php',                 'priority' => '0.9', 'changefreq' => 'monthly', 'lastmod' => file_exists(__DIR__ . '/about.php') ? date('Y-m-d', filemtime(__DIR__ . '/about.php')) : $today],
    ['loc' => 'products.php',              'priority' => '0.9', 'changefreq' => 'weekly',  'lastmod' => file_exists(__DIR__ . '/products.php') ? date('Y-m-d', filemtime(__DIR__ . '/products.php')) : $today],
    ['loc' => 'clients.php',               'priority' => '0.7', 'changefreq' => 'monthly', 'lastmod' => file_exists(__DIR__ . '/clients.php') ? date('Y-m-d', filemtime(__DIR__ . '/clients.php')) : $today],
    ['loc' => 'manufacturing.php',         'priority' => '0.8', 'changefreq' => 'monthly', 'lastmod' => file_exists(__DIR__ . '/manufacturing.php') ? date('Y-m-d', filemtime(__DIR__ . '/manufacturing.php')) : $today],
    ['loc' => 'quality.php',               'priority' => '0.8', 'changefreq' => 'monthly', 'lastmod' => file_exists(__DIR__ . '/quality.php') ? date('Y-m-d', filemtime(__DIR__ . '/quality.php')) : $today],
    ['loc' => 'contact.php',               'priority' => '0.7', 'changefreq' => 'monthly', 'lastmod' => file_exists(__DIR__ . '/contact.php') ? date('Y-m-d', filemtime(__DIR__ . '/contact.php')) : $today],
    ['loc' => 'html-sitemap.php',          'priority' => '0.5', 'changefreq' => 'monthly', 'lastmod' => file_exists(__DIR__ . '/html-sitemap.php') ? date('Y-m-d', filemtime(__DIR__ . '/html-sitemap.php')) : $today],
];

// ─── Fetch Active Products ───
$products = [];
try {
    $stmt = $db->prepare("SELECT id, title, updated_at FROM products WHERE status = 1 ORDER BY id");
    $stmt->execute();
    $products = $stmt->fetchAll();
} catch (Exception $e) {
    // If DB fails, sitemap still works with static pages
}

// ─── Build XML ───
echo '<?xml version="1.0" encoding="UTF-8"?>';
?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
        xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
        xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9
                            http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">
<?php foreach ($staticPages as $page): ?>
    <url>
        <loc><?= htmlspecialchars($baseUrl . '/' . $page['loc'], ENT_QUOTES | ENT_XML1, 'UTF-8') ?></loc>
        <lastmod><?= htmlspecialchars($page['lastmod']) ?></lastmod>
        <changefreq><?= $page['changefreq'] ?></changefreq>
        <priority><?= $page['priority'] ?></priority>
    </url>
<?php endforeach; ?>

<?php foreach ($products as $product): ?>
    <?php $lastmod = !empty($product['updated_at']) ? date('Y-m-d', strtotime($product['updated_at'])) : $today; ?>
    <url>
        <loc><?= htmlspecialchars($baseUrl . '/product-details.php?id=' . $product['id'], ENT_QUOTES | ENT_XML1, 'UTF-8') ?></loc>
        <lastmod><?= $lastmod ?></lastmod>
        <changefreq>weekly</changefreq>
        <priority>0.8</priority>
    </url>
<?php endforeach; ?>
</urlset>

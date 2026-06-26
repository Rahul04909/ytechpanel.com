<?php
/**
 * YTech Panels — Products Table Creator
 * 
 * Usage:
 *   CLI:   php sql-insert/insert-products.php
 *   Web:   Visit sql-insert/insert-products.php (requires SETUP_TOKEN in .env)
 */

require_once dirname(__DIR__) . '/config/db.php';

$isCli = (php_sapi_name() === 'cli');

if (!$isCli) {
    session_start();

    try {
        $db = getDB();
        $adminCheck = $db->query("SELECT COUNT(*) FROM admin_users")->fetchColumn();
        
        if ($adminCheck > 0) {
            $expectedToken = $_ENV['SETUP_TOKEN'] ?? '';
            $providedToken = $_GET['token'] ?? '';
            
            if (empty($expectedToken) || $providedToken !== $expectedToken) {
                http_response_code(403);
                echo '<!DOCTYPE html><html><head><title>Access Denied</title></head><body>';
                echo '<h2>🔒 Access Denied</h2>';
                echo '<p>SETUP_TOKEN is not configured or invalid.</p>';
                echo '<p>Visit: sql-insert/insert-products.php?token=YOUR_TOKEN</p>';
                echo '</body></html>';
                exit();
            }
        }
    } catch (Exception $e) {
        // DB might not exist yet, that's fine
    }
}

function out($msg)
{
    global $isCli;
    if ($isCli) {
        echo $msg . "\n";
    } else {
        echo $msg . '<br>';
    }
}

if (!$isCli) {
    echo '<!DOCTYPE html><html><head><title>Create Products Table</title><style>body{font-family:monospace;padding:40px;background:#f5f5f5;}h2{color:#003a8c;}</style></head><body>';
    echo '<h2>YTech Panels — Products Table Creator</h2>';
    echo '<hr>';
}

out('Starting products table creation...');
out('');

try {
    $db = getDB();

    $db->exec("CREATE TABLE IF NOT EXISTS `products` (
        `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
        `title` VARCHAR(255) NOT NULL,
        `short_description` TEXT DEFAULT NULL,
        `description` LONGTEXT DEFAULT NULL,
        `featured_image` VARCHAR(500) NOT NULL DEFAULT '',
        `gallery_images` TEXT DEFAULT NULL,
        `enable_catalog` TINYINT(1) NOT NULL DEFAULT 0,
        `catalog_pdf` VARCHAR(500) NOT NULL DEFAULT '',
        `meta_title` VARCHAR(255) DEFAULT NULL,
        `meta_description` TEXT DEFAULT NULL,
        `meta_keywords` VARCHAR(255) DEFAULT NULL,
        `og_title` VARCHAR(255) DEFAULT NULL,
        `og_description` TEXT DEFAULT NULL,
        `og_image` VARCHAR(500) DEFAULT NULL,
        `schema_json` TEXT DEFAULT NULL,
        `status` TINYINT(1) NOT NULL DEFAULT 1,
        `sort_order` INT NOT NULL DEFAULT 0,
        `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        KEY `idx_status` (`status`),
        KEY `idx_sort_order` (`sort_order`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    
    out('✓ Table "products" created or already exists.');
    out('');
    out('Setup complete.');

} catch (PDOException $e) {
    out('❌ Error: ' . $e->getMessage());
}

if (!$isCli) echo '</body></html>';

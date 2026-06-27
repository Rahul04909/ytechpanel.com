<?php
/**
 * YTech Panels — Product Enquiries & Reviews Tables Creator
 * 
 * Usage:
 *   CLI:   php sql-insert/insert-product-enquiries-reviews.php
 *   Web:   Visit sql-insert/insert-product-enquiries-reviews.php (requires SETUP_TOKEN in .env)
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
                echo '<p>Visit: sql-insert/insert-product-enquiries-reviews.php?token=YOUR_TOKEN</p>';
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
    echo '<!DOCTYPE html><html><head><title>Create Tables</title><style>body{font-family:monospace;padding:40px;background:#f5f5f5;}h2{color:#003a8c;}</style></head><body>';
    echo '<h2>YTech Panels — Product Enquiries & Reviews Tables Creator</h2>';
    echo '<hr>';
}

out('Starting table creation...');
out('');

try {
    $db = getDB();

    // ── Product Enquiries Table ──
    $db->exec("CREATE TABLE IF NOT EXISTS `product_enquiries` (
        `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
        `product_id` INT UNSIGNED NOT NULL,
        `name` VARCHAR(255) NOT NULL,
        `email` VARCHAR(255) NOT NULL,
        `phone` VARCHAR(20) DEFAULT '',
        `message` TEXT DEFAULT NULL,
        `quantity` VARCHAR(100) DEFAULT '',
        `is_verified` TINYINT(1) NOT NULL DEFAULT 0,
        `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        KEY `idx_product_id` (`product_id`),
        KEY `idx_created_at` (`created_at`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    
    out('✓ Table \"product_enquiries\" created or already exists.');

    // ── Product Reviews Table ──
    $db->exec("CREATE TABLE IF NOT EXISTS `product_reviews` (
        `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
        `product_id` INT UNSIGNED NOT NULL,
        `name` VARCHAR(255) NOT NULL,
        `email` VARCHAR(255) DEFAULT '',
        `rating` TINYINT(1) NOT NULL DEFAULT 5,
        `review` TEXT DEFAULT NULL,
        `status` TINYINT(1) NOT NULL DEFAULT 0 COMMENT '0 = pending, 1 = approved',
        `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        KEY `idx_product_id` (`product_id`),
        KEY `idx_status` (`status`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    
    out('✓ Table \"product_reviews\" created or already exists.');
    out('');
    out('Setup complete.');

} catch (PDOException $e) {
    out('❌ Error: ' . $e->getMessage());
}

if (!$isCli) echo '</body></html>';

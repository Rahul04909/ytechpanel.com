<?php
/**
 * YTech Panels — Enquiry Management Tables Creator
 * Creates: quotes, callback_requests, general_enquiries tables
 * 
 * Usage:
 *   CLI:   php sql-insert/insert-enquiry-tables.php
 *   Web:   Visit with ?token=SETUP_TOKEN
 */
require_once dirname(__DIR__) . '/config/db.php';

$isCli = (php_sapi_name() === 'cli');

if (!$isCli) {
    session_start();
    $expectedToken = $_ENV['SETUP_TOKEN'] ?? '';
    $providedToken = $_GET['token'] ?? '';
    if (!empty($expectedToken) && $providedToken !== $expectedToken) {
        http_response_code(403);
        echo '<h2>Access Denied</h2>'; exit;
    }
}

function out($msg) {
    global $isCli;
    echo $msg . ($isCli ? "\n" : '<br>');
}

if (!$isCli) echo '<!DOCTYPE html><html><head><title>Create Tables</title><style>body{font-family:monospace;padding:40px;background:#f5f5f5;}</style></head><body><h2>Creating Enquiry Tables...</h2><hr>';

try {
    $db = getDB();

    // ── Quotes Table (Get A Quote / Custom Quote) ──
    $db->exec("CREATE TABLE IF NOT EXISTS `quotes` (
        `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
        `name` VARCHAR(255) NOT NULL,
        `email` VARCHAR(255) NOT NULL,
        `phone` VARCHAR(20) DEFAULT '',
        `company` VARCHAR(255) DEFAULT '',
        `product_interest` VARCHAR(255) DEFAULT '',
        `quantity` VARCHAR(100) DEFAULT '',
        `message` TEXT DEFAULT NULL,
        `status` TINYINT(1) NOT NULL DEFAULT 0 COMMENT '0 = new, 1 = contacted, 2 = quoted',
        `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        KEY `idx_status` (`status`),
        KEY `idx_created_at` (`created_at`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    out('✓ Table "quotes" created or already exists.');

    // ── Callback Requests Table ──
    $db->exec("CREATE TABLE IF NOT EXISTS `callback_requests` (
        `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
        `name` VARCHAR(255) NOT NULL,
        `phone` VARCHAR(20) NOT NULL,
        `email` VARCHAR(255) DEFAULT '',
        `preferred_time` VARCHAR(100) DEFAULT '',
        `message` TEXT DEFAULT NULL,
        `status` TINYINT(1) NOT NULL DEFAULT 0 COMMENT '0 = new, 1 = contacted, 2 = completed',
        `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        KEY `idx_status` (`status`),
        KEY `idx_created_at` (`created_at`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    out('✓ Table "callback_requests" created or already exists.');

    // ── General Enquiries Table ──
    $db->exec("CREATE TABLE IF NOT EXISTS `general_enquiries` (
        `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
        `name` VARCHAR(255) NOT NULL,
        `email` VARCHAR(255) NOT NULL,
        `phone` VARCHAR(20) DEFAULT '',
        `subject` VARCHAR(255) DEFAULT '',
        `message` TEXT DEFAULT NULL,
        `status` TINYINT(1) NOT NULL DEFAULT 0 COMMENT '0 = new, 1 = read, 2 = replied',
        `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        KEY `idx_status` (`status`),
        KEY `idx_created_at` (`created_at`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    out('✓ Table "general_enquiries" created or already exists.');

    out('');
    out('All tables created successfully.');

} catch (PDOException $e) {
    out('❌ Error: ' . $e->getMessage());
}

if (!$isCli) echo '</body></html>';

<?php
/**
 * YTech Panels — Admin Users Table Schema
 * 
 * Run this SQL to create the admin_users table.
 * Usage: Import via phpMyAdmin, MySQL CLI, or run this PHP file directly.
 *
 *   CLI:  mysql -u root -p <your_database> < sql-insert/admin_users_table.sql
 *   PHP:  php sql-insert/admin_users_table.sql   (won't work — use CLI or phpMyAdmin)
 *
 * Or visit: http://yoursite.com/sql-insert/admin_users_table.sql (only for initial setup)
 */

// If accessed via browser, redirect or block after setup
if (php_sapi_name() !== 'cli') {
    echo '<h2>SQL Schema File</h2>';
    echo '<p>This file is intended to be imported via MySQL CLI or phpMyAdmin.</p>';
    echo '<pre>';
}

$sql = "
-- ============================================================
-- Table: admin_users
-- Description: Stores admin panel user accounts
-- ============================================================

CREATE TABLE IF NOT EXISTS `admin_users` (
    `id`            INT UNSIGNED        NOT NULL AUTO_INCREMENT,
    `name`          VARCHAR(100)        NOT NULL COMMENT 'Full name of the admin',
    `email`         VARCHAR(255)        NOT NULL COMMENT 'Email address (unique)',
    `mobile`        VARCHAR(20)         NOT NULL DEFAULT '' COMMENT 'Mobile number with country code',
    `username`      VARCHAR(50)         NOT NULL COMMENT 'Login username (unique)',
    `password`      VARCHAR(255)        NOT NULL COMMENT 'Hashed password (bcrypt)',
    `profile_pic`   VARCHAR(500)        NOT NULL DEFAULT 'default-avatar.png' COMMENT 'Profile picture filename',
    `status`        TINYINT(1)          NOT NULL DEFAULT 1 COMMENT '1 = Active, 0 = Disabled',
    `last_login`    DATETIME            NULL DEFAULT NULL COMMENT 'Timestamp of last successful login',
    `created_at`    DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`    DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_admin_email` (`email`),
    UNIQUE KEY `uk_admin_username` (`username`)

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Admin panel user accounts';
";

echo htmlspecialchars($sql);
echo '</pre>';

// When run via CLI, output only SQL
if (php_sapi_name() === 'cli') {
    echo $sql;
}

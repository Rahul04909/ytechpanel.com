<?php
/**
 * YTech Panels — Insert Default Admin
 *
 * This script creates the admin_users table (if it doesn't exist)
 * and inserts the default admin account.
 *
 * IMPORTANT: This script has a safety guard — it will NOT run if an admin
 * already exists in the database. Remove the guard only if you want to
 * force-reinsert.
 *
 * USAGE:
 *   CLI:   php sql-insert/insert-admin.php
 *   Web:   Visit http://yoursite.com/sql-insert/insert-admin.php (for initial setup only)
 *
 * SECURITY: Delete or restrict access to this file after first run on production!
 */

// Bootstrap the application
require_once dirname(__DIR__) . '/config/db.php';

// ---- Safety: only allow CLI or first-time web run ----
$isCli = (php_sapi_name() === 'cli');

if (!$isCli) {
    // Web access — show output but require a setup token for safety
    $setupToken = $_GET['token'] ?? '';
    $expectedToken = $_ENV['SETUP_TOKEN'] ?? '';

    if (empty($expectedToken)) {
        echo '<h2>⚠️ Security Notice</h2>';
        echo '<p>SETUP_TOKEN is not configured in your <code>.env</code> file.</p>';
        echo '<p>To use this via browser, add <code>SETUP_TOKEN=your_secret_key</code> to your <code>.env</code>.</p>';
        echo '<p>Then visit: <code>sql-insert/insert-admin.php?token=your_secret_key</code></p>';
        echo '<hr>';
        echo '<p><strong>Or run via CLI:</strong> <code>php sql-insert/insert-admin.php</code></p>';
        exit(1);
    }

    if (!hash_equals($expectedToken, $setupToken)) {
        http_response_code(403);
        echo '<h2>⛔ Forbidden</h2><p>Invalid setup token.</p>';
        exit(1);
    }
}

// ---- Default Admin Credentials ----
// ⚠️ CHANGE THESE BEFORE RUNNING ON PRODUCTION!
$adminData = [
    'name'         => 'Admin',
    'email'        => 'admin@ytechpanel.com',
    'mobile'       => '+91-9999999999',
    'username'     => 'admin',
    'password'     => 'Admin@123',  // Will be hashed with password_hash()
    'profile_pic'  => 'default-avatar.png',
];

// ---- Allow env overrides (for CI/CD pipelines) ----
$adminData['name']        = $_ENV['ADMIN_NAME']        ?? $adminData['name'];
$adminData['email']       = $_ENV['ADMIN_EMAIL']       ?? $adminData['email'];
$adminData['mobile']      = $_ENV['ADMIN_MOBILE']      ?? $adminData['mobile'];
$adminData['username']    = $_ENV['ADMIN_USERNAME']     ?? $adminData['username'];
$adminData['password']    = $_ENV['ADMIN_PASSWORD']     ?? $adminData['password'];
$adminData['profile_pic'] = $_ENV['ADMIN_PROFILE_PIC'] ?? $adminData['profile_pic'];

// ---- Output helper ----
function out(string $msg): void
{
    global $isCli;
    if ($isCli) {
        echo $msg . PHP_EOL;
    } else {
        echo '<p>' . htmlspecialchars($msg) . '</p>';
    }
}

if (!$isCli) {
    echo '<!DOCTYPE html><html><head><title>Insert Admin</title>';
    echo '<style>body{font-family:monospace;padding:20px;max-width:700px;margin:0 auto;}</style>';
    echo '</head><body><h1>🔧 YTech Panels — Insert Default Admin</h1>';
}

out('---');
out('Starting admin setup...');
out('');

try {
    $db = getDB();

    // ---- Step 1: Create table if not exists ----
    out('Step 1: Creating admin_users table (if not exists)...');

    $createTableSQL = "
        CREATE TABLE IF NOT EXISTS `admin_users` (
            `id`            INT UNSIGNED        NOT NULL AUTO_INCREMENT,
            `name`          VARCHAR(100)        NOT NULL,
            `email`         VARCHAR(255)        NOT NULL,
            `mobile`        VARCHAR(20)         NOT NULL DEFAULT '',
            `username`      VARCHAR(50)         NOT NULL,
            `password`      VARCHAR(255)        NOT NULL,
            `profile_pic`   VARCHAR(500)        NOT NULL DEFAULT 'default-avatar.png',
            `status`        TINYINT(1)          NOT NULL DEFAULT 1,
            `last_login`    DATETIME            NULL DEFAULT NULL,
            `created_at`    DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP,
            `updated_at`    DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            UNIQUE KEY `uk_admin_email` (`email`),
            UNIQUE KEY `uk_admin_username` (`username`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ";

    $db->exec($createTableSQL);
    out('   ✅ Table admin_users ready.');

    // ---- Step 2: Check if admin already exists ----
    out('Step 2: Checking for existing admin...');

    $checkStmt = $db->prepare("SELECT COUNT(*) FROM admin_users WHERE username = :username LIMIT 1");
    $checkStmt->execute([':username' => $adminData['username']]);
    $exists = (int)$checkStmt->fetchColumn() > 0;

    if ($exists) {
        out('   ⚠️  Admin user "' . $adminData['username'] . '" already exists. Skipping insert.');
        out('');
        out('   To force re-create, drop the user first or modify this script.');
        out('');
        out('Setup complete (no changes made).');
        if (!$isCli) echo '</body></html>';
        exit(0);
    }

    // ---- Step 3: Insert default admin ----
    out('Step 3: Inserting default admin...');

    $hashedPassword = password_hash($adminData['password'], PASSWORD_BCRYPT, ['cost' => 12]);

    $insertSQL = "
        INSERT INTO admin_users (name, email, mobile, username, password, profile_pic, status, created_at)
        VALUES (:name, :email, :mobile, :username, :password, :profile_pic, 1, NOW())
    ";

    $insertStmt = $db->prepare($insertSQL);
    $insertStmt->execute([
        ':name'        => $adminData['name'],
        ':email'       => $adminData['email'],
        ':mobile'      => $adminData['mobile'],
        ':username'    => $adminData['username'],
        ':password'    => $hashedPassword,
        ':profile_pic' => $adminData['profile_pic'],
    ]);

    $insertedId = $db->lastInsertId();

    out('   ✅ Default admin inserted successfully! (ID: ' . $insertedId . ')');

    // ---- Step 4: Verify ----
    out('Step 4: Verifying insert...');

    $verifyStmt = $db->prepare("SELECT id, name, email, mobile, username, profile_pic, status FROM admin_users WHERE id = :id");
    $verifyStmt->execute([':id' => $insertedId]);
    $admin = $verifyStmt->fetch();

    if ($admin) {
        out('   ✅ Verification passed. Admin details:');
        out('      ID:          ' . $admin['id']);
        out('      Name:        ' . $admin['name']);
        out('      Email:       ' . $admin['email']);
        out('      Mobile:      ' . $admin['mobile']);
        out('      Username:    ' . $admin['username']);
        out('      Profile Pic: ' . $admin['profile_pic']);
        out('      Status:      ' . ($admin['status'] == 1 ? 'Active' : 'Disabled'));
    } else {
        out('   ❌ Verification failed. Could not read back the inserted row.');
    }

    out('');
    out('================================================');
    out('  ✅ SETUP COMPLETE!');
    out('================================================');
    out('');
    out('  Login URL:  /admin/login.php');
    out('  Username:   ' . $adminData['username']);
    out('  Password:   ' . $adminData['password']);
    out('');
    out('  ⚠️  CHANGE THE DEFAULT PASSWORD AFTER FIRST LOGIN!');
    out('  ⚠️  DELETE THIS FILE OR RESTRICT ACCESS IN PRODUCTION!');
    out('');

} catch (PDOException $e) {
    out('❌ Database Error: ' . $e->getMessage());
    out('');
    out('Please check your .env database configuration.');
    exit(1);
} catch (Exception $e) {
    out('❌ Error: ' . $e->getMessage());
    exit(1);
}

if (!$isCli) {
    echo '</body></html>';
}

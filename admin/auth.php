<?php
/**
 * YTech Panels Admin — Authentication Guard
 * Include at the very TOP of every admin page (after header.php loads it).
 * Redirects to login if session is not valid.
 */

require_once dirname(__DIR__) . '/config/bootstrap.php';

// Secure session setup (must happen before session_start)
$sessionName = $_ENV['ADMIN_SESSION_NAME'] ?? 'ytechpanel_admin';
$sessionLifetime = (int)($_ENV['ADMIN_SESSION_LIFETIME'] ?? 7200);

if (session_status() === PHP_SESSION_NONE) {
    session_name($sessionName);
    session_set_cookie_params([
        'lifetime' => $sessionLifetime,
        'path'     => '/',
        'secure'   => false,        // Set to true on HTTPS production
        'httponly' => true,          // Prevent JS access
        'samesite' => 'Strict',      // CSRF protection
    ]);
    session_start();
}

/**
 * Require admin to be authenticated.
 * Call this at the top of any protected admin page.
 */
function requireAdminAuth(): void
{
    if (empty($_SESSION['admin_id']) || empty($_SESSION['admin_logged_in'])) {
        // Destroy any incomplete session
        $_SESSION = [];
        session_destroy();
        header('Location: login.php?reason=unauthorized');
        exit();
    }

    // Session lifetime / idle timeout check
    $lifetime = (int)($_ENV['ADMIN_SESSION_LIFETIME'] ?? 7200);
    if (isset($_SESSION['admin_last_activity']) &&
        (time() - $_SESSION['admin_last_activity']) > $lifetime) {
        $_SESSION = [];
        session_destroy();
        header('Location: login.php?reason=timeout');
        exit();
    }

    // Renew activity timestamp on every page load
    $_SESSION['admin_last_activity'] = time();

    // Regenerate session ID periodically to prevent fixation
    if (empty($_SESSION['admin_session_created'])) {
        $_SESSION['admin_session_created'] = time();
    } elseif ((time() - $_SESSION['admin_session_created']) > 1800) {
        session_regenerate_id(true);
        $_SESSION['admin_session_created'] = time();
    }
}

/**
 * Check if admin is already logged in (used on login page to redirect away).
 */
function isAdminLoggedIn(): bool
{
    return !empty($_SESSION['admin_id']) && !empty($_SESSION['admin_logged_in']);
}

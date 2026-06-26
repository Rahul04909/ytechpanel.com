<?php
/**
 * YTech Panels Admin — Login Page
 * Production-level authentication with rate limiting & brute-force protection.
 */

require_once __DIR__ . '/auth.php';
require_once dirname(__DIR__) . '/config/db.php';

// If already logged in, redirect to dashboard
if (isAdminLoggedIn()) {
    header('Location: index.php');
    exit();
}

$error   = '';
$success = '';
$reason  = $_GET['reason'] ?? '';

// --- Login Form Processing ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // CSRF token check
    if (empty($_POST['csrf_token']) || $_POST['csrf_token'] !== ($_SESSION['csrf_token'] ?? '')) {
        $error = 'Invalid request. Please try again.';
    } else {
        $username = trim($_POST['username'] ?? '');
        $password = trim($_POST['password'] ?? '');

        if (empty($username) || empty($password)) {
            $error = 'Username and password are required.';
        } else {

            // Rate limiting: max 5 attempts per 15 minutes
            $attemptKey = 'login_attempts_' . md5($_SERVER['REMOTE_ADDR']);
            $attempts   = $_SESSION[$attemptKey] ?? ['count' => 0, 'first_at' => time()];

            // Reset counter if 15 minutes have passed
            if ((time() - $attempts['first_at']) > 900) {
                $attempts = ['count' => 0, 'first_at' => time()];
            }

            if ($attempts['count'] >= 5) {
                $waitSecs = 900 - (time() - $attempts['first_at']);
                $error = "Too many failed attempts. Please wait " . ceil($waitSecs / 60) . " minute(s) before trying again.";
            } else {

                try {
                    $db   = getDB();
                    $stmt = $db->prepare("SELECT id, name, email, username, password, profile_pic FROM admin_users WHERE username = :username AND status = 1 LIMIT 1");
                    $stmt->execute([':username' => $username]);
                    $admin = $stmt->fetch();

                    if ($admin && password_verify($password, $admin['password'])) {
                        // Successful login — reset attempts, set session
                        unset($_SESSION[$attemptKey]);

                        session_regenerate_id(true);
                        $_SESSION['admin_id']              = $admin['id'];
                        $_SESSION['admin_name']            = $admin['name'];
                        $_SESSION['admin_email']           = $admin['email'];
                        $_SESSION['admin_username']        = $admin['username'];
                        $_SESSION['admin_profile_pic']     = $admin['profile_pic'] ?? '';
                        $_SESSION['admin_logged_in']       = true;
                        $_SESSION['admin_last_activity']   = time();
                        $_SESSION['admin_session_created'] = time();

                        // Update last_login timestamp
                        $db->prepare("UPDATE admin_users SET last_login = NOW() WHERE id = :id")
                           ->execute([':id' => $admin['id']]);

                        header('Location: index.php');
                        exit();
                    } else {
                        // Failed attempt — increment counter
                        $attempts['count']++;
                        $_SESSION[$attemptKey] = $attempts;
                        $remaining = 5 - $attempts['count'];
                        $error = "Invalid username or password." . ($remaining > 0 ? " {$remaining} attempt(s) remaining." : '');
                    }
                } catch (Exception $e) {
                    $error = 'A system error occurred. Please try again later.';
                }
            }
        }
    }
}

// Generate fresh CSRF token on each page load
$_SESSION['csrf_token'] = bin2hex(random_bytes(32));

// Reason-based messages
$reasonMessages = [
    'unauthorized' => 'You must be logged in to access the admin panel.',
    'timeout'      => 'Your session has expired. Please log in again.',
    'logout'       => 'You have been successfully logged out.',
];
$infoMsg = $reasonMessages[$reason] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login — YTech Panels</title>
    <meta name="robots" content="noindex, nofollow">
    <link rel="icon" href="../assets/logo.png" type="image/png">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Outfit:wght@600;700;800&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --red:        #dc2626;
            --red-dark:   #b91c1c;
            --red-light:  #fee2e2;
            --white:      #ffffff;
            --bg:         #f1f5f9;
            --card:       #ffffff;
            --text:       #1e293b;
            --muted:      #64748b;
            --border:     #e2e8f0;
            --input-bg:   #f8fafc;
            --shadow:     0 20px 60px rgba(0,0,0,0.12);
            --transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--bg);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            background-image:
                radial-gradient(circle at 20% 20%, rgba(220,38,38,0.06) 0%, transparent 50%),
                radial-gradient(circle at 80% 80%, rgba(220,38,38,0.04) 0%, transparent 50%);
        }

        .login-wrapper {
            width: 100%;
            max-width: 420px;
        }

        /* --- Logo / Brand Block --- */
        .brand-block {
            text-align: center;
            margin-bottom: 28px;
        }

        .brand-logo {
            max-height: 64px;
            width: auto;
            margin-bottom: 12px;
        }

        .brand-title {
            font-family: 'Outfit', sans-serif;
            font-size: 22px;
            font-weight: 800;
            color: var(--text);
            letter-spacing: 0.3px;
        }

        .brand-subtitle {
            font-size: 13px;
            color: var(--muted);
            margin-top: 4px;
        }

        /* --- Card --- */
        .login-card {
            background: var(--card);
            border-radius: 0;
            box-shadow: var(--shadow);
            border-top: 4px solid var(--red);
            padding: 36px 40px 32px;
        }

        .login-card h1 {
            font-family: 'Outfit', sans-serif;
            font-size: 20px;
            font-weight: 700;
            color: var(--text);
            margin-bottom: 6px;
        }

        .login-card .card-desc {
            font-size: 13px;
            color: var(--muted);
            margin-bottom: 28px;
        }

        /* --- Alert Messages --- */
        .alert {
            padding: 11px 14px;
            border-radius: 0;
            font-size: 13.5px;
            margin-bottom: 20px;
            display: flex;
            align-items: flex-start;
            gap: 8px;
            border-left: 4px solid;
        }

        .alert-error {
            background: #fef2f2;
            border-color: var(--red);
            color: #991b1b;
        }

        .alert-info {
            background: #eff6ff;
            border-color: #3b82f6;
            color: #1e40af;
        }

        .alert-success {
            background: #f0fdf4;
            border-color: #22c55e;
            color: #15803d;
        }

        .alert svg {
            flex-shrink: 0;
            width: 16px;
            height: 16px;
            margin-top: 1px;
        }

        /* --- Form Fields --- */
        .form-group {
            margin-bottom: 18px;
        }

        label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: var(--text);
            margin-bottom: 6px;
        }

        .input-wrap {
            position: relative;
        }

        .input-wrap svg {
            position: absolute;
            left: 13px;
            top: 50%;
            transform: translateY(-50%);
            width: 16px;
            height: 16px;
            stroke: var(--muted);
            fill: none;
            stroke-width: 2;
            stroke-linecap: round;
            stroke-linejoin: round;
        }

        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 11px 14px 11px 40px;
            border: 1.5px solid var(--border);
            background: var(--input-bg);
            font-family: 'Inter', sans-serif;
            font-size: 14px;
            color: var(--text);
            border-radius: 0;
            transition: var(--transition);
            outline: none;
        }

        input[type="text"]:focus,
        input[type="password"]:focus {
            border-color: var(--red);
            background: var(--white);
            box-shadow: 0 0 0 3px rgba(220,38,38,0.08);
        }

        /* Password toggle */
        .password-toggle {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
            padding: 4px;
            display: flex;
            align-items: center;
            color: var(--muted);
            transition: color 0.2s;
        }

        .password-toggle:hover { color: var(--red); }

        .password-toggle svg {
            position: static;
            transform: none;
            width: 16px;
            height: 16px;
            stroke: currentColor;
        }

        /* --- Remember & Forgot --- */
        .form-footer-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
        }

        .remember-label {
            display: flex;
            align-items: center;
            gap: 7px;
            font-size: 13px;
            color: var(--muted);
            cursor: pointer;
        }

        .remember-label input[type="checkbox"] {
            width: 14px;
            height: 14px;
            accent-color: var(--red);
            cursor: pointer;
        }

        .forgot-link {
            font-size: 13px;
            color: var(--red);
            text-decoration: none;
            font-weight: 500;
        }

        .forgot-link:hover { text-decoration: underline; }

        /* --- Submit Button --- */
        .btn-login {
            width: 100%;
            padding: 13px;
            background: var(--red);
            color: var(--white);
            border: none;
            border-radius: 0;
            font-family: 'Inter', sans-serif;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            letter-spacing: 0.3px;
        }

        .btn-login:hover { background: var(--red-dark); }
        .btn-login:active { transform: scale(0.99); }

        .btn-login svg {
            width: 16px;
            height: 16px;
            stroke: currentColor;
            fill: none;
            stroke-width: 2.5;
            stroke-linecap: round;
            stroke-linejoin: round;
        }

        /* --- Bottom Bar --- */
        .login-bottom {
            text-align: center;
            margin-top: 24px;
            font-size: 12px;
            color: var(--muted);
        }

        @media (max-width: 480px) {
            .login-card { padding: 28px 24px; }
        }
    </style>
</head>
<body>
<div class="login-wrapper">

    <!-- Brand -->
    <div class="brand-block">
        <img src="../assets/logo.png" alt="YTech Panels Logo" class="brand-logo">
        <div class="brand-title">YTech Panels</div>
        <div class="brand-subtitle">Admin Control Panel — Secure Login</div>
    </div>

    <!-- Card -->
    <div class="login-card">
        <h1>Sign In</h1>
        <p class="card-desc">Enter your credentials to access the dashboard.</p>

        <!-- Info / Reason Message -->
        <?php if ($infoMsg): ?>
            <div class="alert <?= $reason === 'logout' ? 'alert-success' : 'alert-info' ?>">
                <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                <?= htmlspecialchars($infoMsg) ?>
            </div>
        <?php endif; ?>

        <!-- Error Message -->
        <?php if ($error): ?>
            <div class="alert alert-error">
                <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <!-- Login Form -->
        <form method="POST" action="login.php" autocomplete="on" novalidate>
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">

            <!-- Username -->
            <div class="form-group">
                <label for="username">Username</label>
                <div class="input-wrap">
                    <svg viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                    <input
                        type="text"
                        id="username"
                        name="username"
                        placeholder="Enter your username"
                        value="<?= htmlspecialchars($_POST['username'] ?? '') ?>"
                        autocomplete="username"
                        required
                    >
                </div>
            </div>

            <!-- Password -->
            <div class="form-group">
                <label for="password">Password</label>
                <div class="input-wrap">
                    <svg viewBox="0 0 24 24"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                    <input
                        type="password"
                        id="password"
                        name="password"
                        placeholder="Enter your password"
                        autocomplete="current-password"
                        required
                    >
                    <button type="button" class="password-toggle" id="togglePassword" aria-label="Toggle password visibility">
                        <svg id="eyeIcon" viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                    </button>
                </div>
            </div>

            <!-- Remember + Forgot -->
            <div class="form-footer-row">
                <label class="remember-label">
                    <input type="checkbox" name="remember" id="remember">
                    Remember me
                </label>
                <a href="#" class="forgot-link">Forgot password?</a>
            </div>

            <!-- Submit -->
            <button type="submit" class="btn-login" id="loginBtn">
                <svg viewBox="0 0 24 24"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/><polyline points="10 17 15 12 10 7"/><line x1="15" y1="12" x2="3" y2="12"/></svg>
                Sign In to Dashboard
            </button>
        </form>
    </div>

    <!-- Bottom Info -->
    <div class="login-bottom">
        &copy; <?= date('Y') ?> YTech Panels. All rights reserved. &nbsp;|&nbsp;
        Unauthorized access is prohibited.
    </div>
</div>

<script>
// Toggle password visibility
const toggleBtn = document.getElementById('togglePassword');
const pwdInput  = document.getElementById('password');
const eyeIcon   = document.getElementById('eyeIcon');

toggleBtn.addEventListener('click', function () {
    const isHidden = pwdInput.type === 'password';
    pwdInput.type = isHidden ? 'text' : 'password';
    eyeIcon.innerHTML = isHidden
        ? '<path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/>'
        : '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>';
});

// Disable submit on click to prevent double-submit
document.querySelector('form').addEventListener('submit', function() {
    const btn = document.getElementById('loginBtn');
    btn.disabled = true;
    btn.innerHTML = '<svg viewBox="0 0 24 24" style="animation:spin 1s linear infinite"><path d="M21 12a9 9 0 1 1-6.219-8.56"/></svg> Signing In...';
});
</script>

<style>
@keyframes spin { to { transform: rotate(360deg); } }
</style>

</body>
</html>

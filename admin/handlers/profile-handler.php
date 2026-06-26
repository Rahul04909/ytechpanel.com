<?php
/**
 * YTech Panels — Admin Profile Handler (AJAX)
 * Handles profile updates and password changes via POST requests.
 */

require_once dirname(__DIR__) . '/auth.php';
requireAdminAuth();
require_once dirname(__DIR__, 2) . '/config/db.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed.']);
    exit();
}

$action = $_POST['action'] ?? '';

// CSRF token validation
if (($_POST['csrf_token'] ?? '') !== ($_SESSION['csrf_token'] ?? '')) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Invalid security token. Please refresh and try again.']);
    exit();
}

try {
    $db = getDB();
    $adminId = (int) $_SESSION['admin_id'];

    // ── Update Profile Info ──
    if ($action === 'update_profile') {

        $name    = trim($_POST['name'] ?? '');
        $email   = trim($_POST['email'] ?? '');
        $mobile  = trim($_POST['mobile'] ?? '');
        $username = trim($_POST['username'] ?? '');

        // Validation
        if (empty($name) || empty($email) || empty($username)) {
            echo json_encode(['success' => false, 'message' => 'Name, email, and username are required.']);
            exit();
        }

        if (strlen($name) > 255) {
            echo json_encode(['success' => false, 'message' => 'Name is too long (max 255 characters).']);
            exit();
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo json_encode(['success' => false, 'message' => 'Please enter a valid email address.']);
            exit();
        }

        if (strlen($username) < 3 || strlen($username) > 50) {
            echo json_encode(['success' => false, 'message' => 'Username must be 3-50 characters.']);
            exit();
        }

        if (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
            echo json_encode(['success' => false, 'message' => 'Username can only contain letters, numbers, and underscores.']);
            exit();
        }

        if (!empty($mobile) && !preg_match('/^[+]?[\d\s\-()]{7,20}$/', $mobile)) {
            echo json_encode(['success' => false, 'message' => 'Please enter a valid mobile number.']);
            exit();
        }

        // Check if email or username is taken by another admin
        $stmt = $db->prepare("SELECT id FROM admin_users WHERE (email = :email OR username = :username) AND id != :id LIMIT 1");
        $stmt->execute([':email' => $email, ':username' => $username, ':id' => $adminId]);
        if ($stmt->fetch()) {
            echo json_encode(['success' => false, 'message' => 'Email or username is already taken by another account.']);
            exit();
        }

        // Handle profile picture upload
        $profilePic = $_SESSION['admin_profile_pic'] ?? 'user-avtar.png';

        if (!empty($_FILES['profile_pic']['name'])) {
            $file = $_FILES['profile_pic'];
            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            $maxSize = 2 * 1024 * 1024; // 2MB

            if (!in_array($file['type'], $allowedTypes)) {
                echo json_encode(['success' => false, 'message' => 'Profile picture must be JPG, PNG, GIF, or WebP.']);
                exit();
            }

            if ($file['size'] > $maxSize) {
                echo json_encode(['success' => false, 'message' => 'Profile picture must be under 2MB.']);
                exit();
            }

            $uploadDir = dirname(__DIR__) . '/src/images/profile_picture/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            $filename = 'admin_' . $adminId . '_' . time() . '.' . $ext;
            $destination = $uploadDir . $filename;

            if (move_uploaded_file($file['tmp_name'], $destination)) {
                // Delete old profile pic if it's not the default
                if (!empty($profilePic) && $profilePic !== 'user-avtar.png' && $profilePic !== 'default.png') {
                    $oldFile = $uploadDir . $profilePic;
                    if (file_exists($oldFile)) {
                        unlink($oldFile);
                    }
                }
                $profilePic = $filename;
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to upload profile picture.']);
                exit();
            }
        }

        // Update database
        $stmt = $db->prepare("UPDATE admin_users SET name = :name, email = :email, mobile = :mobile, username = :username, profile_pic = :profile_pic WHERE id = :id");
        $stmt->execute([
            ':name'        => $name,
            ':email'       => $email,
            ':mobile'      => $mobile,
            ':username'    => $username,
            ':profile_pic' => $profilePic,
            ':id'          => $adminId,
        ]);

        // Update session
        $_SESSION['admin_name']        = $name;
        $_SESSION['admin_email']       = $email;
        $_SESSION['admin_username']    = $username;
        $_SESSION['admin_profile_pic'] = $profilePic;

        echo json_encode(['success' => true, 'message' => 'Profile updated successfully.', 'profile_pic' => $profilePic]);

    // ── Change Password ──
    } elseif ($action === 'change_password') {

        $currentPassword = $_POST['current_password'] ?? '';
        $newPassword     = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        if (empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
            echo json_encode(['success' => false, 'message' => 'All password fields are required.']);
            exit();
        }

        if ($newPassword !== $confirmPassword) {
            echo json_encode(['success' => false, 'message' => 'New password and confirmation do not match.']);
            exit();
        }

        if (strlen($newPassword) < 8) {
            echo json_encode(['success' => false, 'message' => 'New password must be at least 8 characters.']);
            exit();
        }

        if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^A-Za-z0-9]).{8,}$/', $newPassword)) {
            echo json_encode(['success' => false, 'message' => 'Password must contain uppercase, lowercase, number, and special character.']);
            exit();
        }

        // Verify current password
        $stmt = $db->prepare("SELECT password FROM admin_users WHERE id = :id");
        $stmt->execute([':id' => $adminId]);
        $admin = $stmt->fetch();

        if (!$admin || !password_verify($currentPassword, $admin['password'])) {
            echo json_encode(['success' => false, 'message' => 'Current password is incorrect.']);
            exit();
        }

        // Hash and update new password
        $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT, ['cost' => 12]);
        $stmt = $db->prepare("UPDATE admin_users SET password = :password, updated_at = NOW() WHERE id = :id");
        $stmt->execute([':password' => $hashedPassword, ':id' => $adminId]);

        echo json_encode(['success' => true, 'message' => 'Password changed successfully.']);

    } else {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid action.']);
    }

} catch (Exception $e) {
    http_response_code(500);
    $debug = ($_ENV['APP_DEBUG'] ?? 'false') === 'true';
    echo json_encode([
        'success' => false,
        'message' => $debug ? $e->getMessage() : 'An unexpected error occurred. Please try again.',
    ]);
}

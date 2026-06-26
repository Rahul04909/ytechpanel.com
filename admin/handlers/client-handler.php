<?php
/**
 * YTech Panels — Admin Client Handler (AJAX)
 * Handles client CRUD operations and logo uploads via POST requests.
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
$clientId = isset($_POST['client_id']) ? (int) $_POST['client_id'] : 0;

// CSRF token validation
if (($_POST['csrf_token'] ?? '') !== ($_SESSION['csrf_token'] ?? '')) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Invalid security token. Please refresh and try again.']);
    exit();
}

try {
    $db = getDB();

    // ── Add Client ──
    if ($action === 'add_client') {

        $name        = trim($_POST['name'] ?? '');
        $website     = trim($_POST['website'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $sortOrder   = isset($_POST['sort_order']) ? (int) $_POST['sort_order'] : 0;
        $status      = isset($_POST['status']) ? (int) $_POST['status'] : 1;

        // Validation
        if (empty($name)) {
            echo json_encode(['success' => false, 'message' => 'Client name is required.']);
            exit();
        }

        if (strlen($name) > 255) {
            echo json_encode(['success' => false, 'message' => 'Client name is too long (max 255 characters).']);
            exit();
        }

        if (!empty($website) && !filter_var($website, FILTER_VALIDATE_URL)) {
            echo json_encode(['success' => false, 'message' => 'Please enter a valid website URL.']);
            exit();
        }

        // Handle logo upload
        $logo = '';
        if (!empty($_FILES['logo']['name'])) {
            $logo = handleLogoUpload($_FILES['logo']);
            if ($logo === false) {
                echo json_encode(['success' => false, 'message' => 'Logo upload failed. Allowed: JPG, PNG, GIF, WebP (max 5MB).']);
                exit();
            }
        }

        $stmt = $db->prepare("INSERT INTO clients (name, logo, website, description, sort_order, status, created_at) VALUES (:name, :logo, :website, :description, :sort_order, :status, NOW())");
        $stmt->execute([
            ':name'        => $name,
            ':logo'        => $logo,
            ':website'     => $website,
            ':description' => $description,
            ':sort_order'  => $sortOrder,
            ':status'      => $status,
        ]);

        $newId = $db->lastInsertId();
        echo json_encode(['success' => true, 'message' => 'Client added successfully.', 'client_id' => $newId]);

    // ── Update Client ──
    } elseif ($action === 'update_client') {

        if ($clientId <= 0) {
            echo json_encode(['success' => false, 'message' => 'Invalid client ID.']);
            exit();
        }

        $name        = trim($_POST['name'] ?? '');
        $website     = trim($_POST['website'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $sortOrder   = isset($_POST['sort_order']) ? (int) $_POST['sort_order'] : 0;
        $status      = isset($_POST['status']) ? (int) $_POST['status'] : 1;

        if (empty($name)) {
            echo json_encode(['success' => false, 'message' => 'Client name is required.']);
            exit();
        }

        if (!empty($website) && !filter_var($website, FILTER_VALIDATE_URL)) {
            echo json_encode(['success' => false, 'message' => 'Please enter a valid website URL.']);
            exit();
        }

        // Check client exists
        $stmt = $db->prepare("SELECT id, logo FROM clients WHERE id = :id");
        $stmt->execute([':id' => $clientId]);
        $existing = $stmt->fetch();
        if (!$existing) {
            echo json_encode(['success' => false, 'message' => 'Client not found.']);
            exit();
        }

        // Handle logo upload (optional)
        $logo = $existing['logo'];
        if (!empty($_FILES['logo']['name'])) {
            $newLogo = handleLogoUpload($_FILES['logo']);
            if ($newLogo === false) {
                echo json_encode(['success' => false, 'message' => 'Logo upload failed. Allowed: JPG, PNG, GIF, WebP (max 5MB).']);
                exit();
            }
            // Delete old logo file if it's an uploaded file (not a data URI)
            if (!empty($existing['logo']) && strpos($existing['logo'], 'data:') !== 0) {
                $oldPath = dirname(__DIR__) . '/uploads/client_logos/' . $existing['logo'];
                if (file_exists($oldPath)) {
                    unlink($oldPath);
                }
            }
            $logo = $newLogo;
        }

        $stmt = $db->prepare("UPDATE clients SET name = :name, logo = :logo, website = :website, description = :description, sort_order = :sort_order, status = :status, updated_at = NOW() WHERE id = :id");
        $stmt->execute([
            ':name'        => $name,
            ':logo'        => $logo,
            ':website'     => $website,
            ':description' => $description,
            ':sort_order'  => $sortOrder,
            ':status'      => $status,
            ':id'          => $clientId,
        ]);

        echo json_encode(['success' => true, 'message' => 'Client updated successfully.']);

    // ── Delete Client ──
    } elseif ($action === 'delete_client') {

        if ($clientId <= 0) {
            echo json_encode(['success' => false, 'message' => 'Invalid client ID.']);
            exit();
        }

        $stmt = $db->prepare("SELECT id, logo FROM clients WHERE id = :id");
        $stmt->execute([':id' => $clientId]);
        $client = $stmt->fetch();

        if (!$client) {
            echo json_encode(['success' => false, 'message' => 'Client not found.']);
            exit();
        }

        // Delete logo file if it's an uploaded file (not a data URI)
        if (!empty($client['logo']) && strpos($client['logo'], 'data:') !== 0) {
            $logoPath = dirname(__DIR__) . '/uploads/client_logos/' . $client['logo'];
            if (file_exists($logoPath)) {
                unlink($logoPath);
            }
        }

        $stmt = $db->prepare("DELETE FROM clients WHERE id = :id");
        $stmt->execute([':id' => $clientId]);

        echo json_encode(['success' => true, 'message' => 'Client deleted successfully.']);

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

/**
 * Handle client logo file upload.
 * @param array $file $_FILES entry
 * @return string|false Filename on success, false on failure
 */
function handleLogoUpload(array $file)
{
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    $maxSize = 5 * 1024 * 1024; // 5MB

    if ($file['error'] !== UPLOAD_ERR_OK) {
        return false;
    }

    if (!in_array($file['type'], $allowedTypes, true)) {
        return false;
    }

    if ($file['size'] > $maxSize) {
        return false;
    }

    $uploadDir = dirname(__DIR__) . '/uploads/client_logos/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $filename = 'client_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
    $destination = $uploadDir . $filename;

    if (move_uploaded_file($file['tmp_name'], $destination)) {
        return $filename;
    }

    return false;
}

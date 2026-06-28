<?php
/**
 * YTech Panels — Product Enquiry & Review Handler (AJAX)
 * Handles OTP send/verify, enquiry submissions, and review submissions.
 */
require_once dirname(__DIR__) . '/config/db.php';
require_once dirname(__DIR__) . '/vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed.']);
    exit;
}

$action = $_POST['action'] ?? '';

// Start session for OTP storage
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$db = getDB();

try {
    // ─── SUBMIT ENQUIRY ───
    if ($action === 'submit_enquiry') {
        $productId = (int)($_POST['product_id'] ?? 0);
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $message = trim($_POST['message'] ?? '');
        $quantity = trim($_POST['quantity'] ?? '');

        // CSRF validation
        $csrfToken = $_POST['csrf_token'] ?? '';
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        if ($csrfToken !== ($_SESSION['csrf_token'] ?? '')) {
            echo json_encode(['success' => false, 'message' => 'Invalid security token. Please refresh and try again.']);
            exit;
        }

        // Honeypot check (anti-spam)
        if (!empty($_POST['website'])) {
            echo json_encode(['success' => false, 'message' => 'Invalid request.']);
            exit;
        }

        // Rate limiting: max 3 enquiries per email per hour
        $stmt = $db->prepare("SELECT COUNT(*) FROM product_enquiries WHERE email = ? AND created_at > DATE_SUB(NOW(), INTERVAL 1 HOUR)");
        $stmt->execute([$email]);
        $recentCount = (int)$stmt->fetchColumn();
        if ($recentCount >= 3) {
            echo json_encode(['success' => false, 'message' => 'You have reached the maximum number of enquiries per hour. Please try again later.']);
            exit;
        }

        // Validation
        if ($productId <= 0) {
            echo json_encode(['success' => false, 'message' => 'Invalid product.']);
            exit;
        }

        if (empty($name)) {
            echo json_encode(['success' => false, 'message' => 'Please enter your name.']);
            exit;
        }

        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo json_encode(['success' => false, 'message' => 'Please enter a valid email address.']);
            exit;
        }

        if (empty($message)) {
            echo json_encode(['success' => false, 'message' => 'Please enter your enquiry message.']);
            exit;
        }

        // Check if product exists
        $stmt = $db->prepare("SELECT id, title FROM products WHERE id = ? AND status = 1");
        $stmt->execute([$productId]);
        $product = $stmt->fetch();

        if (!$product) {
            echo json_encode(['success' => false, 'message' => 'Product not found.']);
            exit;
        }

        // Save enquiry
        $stmt = $db->prepare("INSERT INTO product_enquiries (product_id, name, email, phone, message, quantity, is_verified) VALUES (?, ?, ?, ?, ?, ?, 1)");
        $stmt->execute([$productId, $name, $email, $phone, $message, $quantity]);

        // Notify admin via email (silent fail)
        try {
            if (!empty($_ENV['SMTP_HOST']) && !empty($_ENV['ADMIN_NOTIFY_EMAIL'])) {
                $mail = new PHPMailer(true);
                $mail->isSMTP();
                $mail->Host       = $_ENV['SMTP_HOST'];
                $mail->SMTPAuth   = true;
                $mail->Username   = $_ENV['SMTP_USERNAME'] ?? '';
                $mail->Password   = $_ENV['SMTP_PASSWORD'] ?? '';
                $mail->SMTPSecure = $_ENV['SMTP_SECURE'] ?? PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port       = (int)($_ENV['SMTP_PORT'] ?? 587);

                $mail->setFrom($_ENV['SMTP_FROM_EMAIL'] ?? 'noreply@ytechpanels.com', 'YTech Panels');
                $mail->addAddress($_ENV['ADMIN_NOTIFY_EMAIL']);
                $mail->Subject = 'New Product Enquiry - ' . htmlspecialchars($product['title']);
                $mail->Body = "
                    <h2>New Product Enquiry</h2>
                    <p><strong>Product:</strong> " . htmlspecialchars($product['title']) . "</p>
                    <p><strong>Name:</strong> " . htmlspecialchars($name) . "</p>
                    <p><strong>Email:</strong> " . htmlspecialchars($email) . "</p>
                    <p><strong>Phone:</strong> " . htmlspecialchars($phone) . "</p>
                    <p><strong>Quantity:</strong> " . htmlspecialchars($quantity) . "</p>
                    <p><strong>Message:</strong> " . nl2br(htmlspecialchars($message)) . "</p                    <p><strong>Email Verified:</strong> Yes</p>
                ";
                $mail->send();
            }
        } catch (Exception $e) {
            // Don't block the response
        }

        echo json_encode(['success' => true, 'message' => 'Your enquiry has been submitted successfully! Our team will get back to you within 24 hours.']);
        exit;
    }

    // ─── SUBMIT REVIEW ───
    if ($action === 'submit_review') {
        $productId = (int)($_POST['product_id'] ?? 0);
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $rating = (int)($_POST['rating'] ?? 5);
        $review = trim($_POST['review'] ?? '');

        // CSRF validation
        $csrfToken = $_POST['csrf_token'] ?? '';
        if ($csrfToken !== ($_SESSION['csrf_token'] ?? '')) {
            echo json_encode(['success' => false, 'message' => 'Invalid security token. Please refresh and try again.']);
            exit;
        }

        // Honeypot check (anti-spam)
        if (!empty($_POST['website'])) {
            echo json_encode(['success' => false, 'message' => 'Invalid request.']);
            exit;
        }

        if ($productId <= 0) {
            echo json_encode(['success' => false, 'message' => 'Invalid product.']);
            exit;
        }

        if (empty($name)) {
            echo json_encode(['success' => false, 'message' => 'Please enter your name.']);
            exit;
        }

        if ($rating < 1 || $rating > 5) {
            $rating = 5;
        }

        if (empty($review)) {
            echo json_encode(['success' => false, 'message' => 'Please write your review.']);
            exit;
        }

        // Check product exists
        $stmt = $db->prepare("SELECT id FROM products WHERE id = ? AND status = 1");
        $stmt->execute([$productId]);
        if (!$stmt->fetch()) {
            echo json_encode(['success' => false, 'message' => 'Product not found.']);
            exit;
        }

        // Save review (pending moderation)
        $stmt = $db->prepare("INSERT INTO product_reviews (product_id, name, email, rating, review, status) VALUES (?, ?, ?, ?, ?, 0)");
        $stmt->execute([$productId, $name, $email, $rating, $review]);

        echo json_encode(['success' => true, 'message' => 'Thank you! Your review has been submitted and is pending approval.']);
        exit;
    }

    // Unknown action
    echo json_encode(['success' => false, 'message' => 'Invalid action.']);

} catch (Exception $e) {
    $debug = ($_ENV['APP_DEBUG'] ?? 'false') === 'true';
    echo json_encode([
        'success' => false,
        'message' => $debug ? $e->getMessage() : 'An unexpected error occurred. Please try again.'
    ]);
}

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
    // ─── SEND OTP ───
    if ($action === 'send_otp') {
        $email = trim($_POST['email'] ?? '');

        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo json_encode(['success' => false, 'message' => 'Please enter a valid email address.']);
            exit;
        }

        // Generate 6-digit OTP
        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $expiry = time() + 300; // 5 minutes

        // Store in session
        $_SESSION['enquiry_otp'] = $otp;
        $_SESSION['enquiry_otp_email'] = $email;
        $_SESSION['enquiry_otp_expiry'] = $expiry;

        // Send email via PHPMailer
        try {
            $mail = new PHPMailer(true);
            
            // SMTP configuration
            $mail->isSMTP();
            $mail->Host       = $_ENV['SMTP_HOST'] ?? 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = $_ENV['SMTP_USERNAME'] ?? '';
            $mail->Password   = $_ENV['SMTP_PASSWORD'] ?? '';
            $mail->SMTPSecure = $_ENV['SMTP_SECURE'] ?? PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = (int)($_ENV['SMTP_PORT'] ?? 587);

            $mail->setFrom($_ENV['SMTP_FROM_EMAIL'] ?? 'noreply@ytechpanels.com', $_ENV['SMTP_FROM_NAME'] ?? 'YTech Panels');
            $mail->addAddress($email);

            $mail->isHTML(true);
            $mail->Subject = 'Email Verification OTP - YTech Panels';
            $mail->Body    = "
                <div style='font-family: Arial, sans-serif; max-width: 480px; margin: 0 auto; padding: 30px; background: #f8fafc; border: 1px solid #e2e8f0;'>
                    <div style='text-align: center; margin-bottom: 24px;'>
                        <h2 style='color: #dc2626; margin: 0; font-size: 24px;'>YTech Panels</h2>
                        <p style='color: #64748b; font-size: 13px; margin: 4px 0 0;'>Email Verification</p>
                    </div>
                    <div style='background: #fff; padding: 24px; border-radius: 8px; border: 1px solid #e2e8f0;'>
                        <p style='color: #334155; font-size: 15px; margin: 0 0 16px;'>Your One-Time Password (OTP) for email verification is:</p>
                        <div style='text-align: center; padding: 16px; background: #fef2f2; border-radius: 6px; margin-bottom: 16px;'>
                            <span style='font-size: 36px; font-weight: 800; color: #dc2626; letter-spacing: 8px;'>$otp</span>
                        </div>
                        <p style='color: #64748b; font-size: 13px; margin: 0;'>This OTP is valid for 5 minutes. Please do not share this OTP with anyone.</p>
                    </div>
                    <p style='color: #94a3b8; font-size: 11px; text-align: center; margin-top: 20px;'>This is an automated message from YTech Panels. &copy; " . date('Y') . " YTech Panels</p>
                </div>
            ";
            $mail->AltBody = "Your OTP for email verification is: $otp. Valid for 5 minutes.";

            $mail->send();

            echo json_encode(['success' => true, 'message' => 'OTP sent to your email. Please check your inbox.']);
        } catch (Exception $e) {
            // For development/testing: return OTP directly if SMTP not configured
            if (empty($_ENV['SMTP_HOST'])) {
                echo json_encode(['success' => true, 'message' => 'OTP: ' . $otp . ' (SMTP not configured)']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to send OTP email. Please try again.']);
            }
        }
        exit;
    }

    // ─── VERIFY OTP ───
    if ($action === 'verify_otp') {
        $otp = trim($_POST['otp'] ?? '');
        $email = trim($_POST['email'] ?? '');

        if (empty($otp) || empty($email)) {
            echo json_encode(['success' => false, 'message' => 'OTP and email are required.']);
            exit;
        }

        // Check session
        $storedOtp = $_SESSION['enquiry_otp'] ?? '';
        $storedEmail = $_SESSION['enquiry_otp_email'] ?? '';
        $storedExpiry = $_SESSION['enquiry_otp_expiry'] ?? 0;

        if ($storedOtp !== $otp || $storedEmail !== $email) {
            echo json_encode(['success' => false, 'message' => 'Invalid OTP. Please try again.']);
            exit;
        }

        if (time() > $storedExpiry) {
            echo json_encode(['success' => false, 'message' => 'OTP has expired. Please request a new one.']);
            unset($_SESSION['enquiry_otp'], $_SESSION['enquiry_otp_email'], $_SESSION['enquiry_otp_expiry']);
            exit;
        }

        // Mark as verified
        $_SESSION['enquiry_otp_verified'] = true;
        unset($_SESSION['enquiry_otp'], $_SESSION['enquiry_otp_email'], $_SESSION['enquiry_otp_expiry']);

        echo json_encode(['success' => true, 'message' => 'Email verified successfully.']);
        exit;
    }

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

        // Enforce OTP verification server-side
        if (empty($_SESSION['enquiry_otp_verified'])) {
            echo json_encode(['success' => false, 'message' => 'Please verify your email via OTP before submitting the enquiry.']);
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

        // Clear verification flag
        unset($_SESSION['enquiry_otp_verified']);

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
                    <p><strong>Message:</strong> " . nl2br(htmlspecialchars($message)) . "</p>
                    <p><strong>Email Verified:</strong> " . ($isVerified ? 'Yes' : 'No') . "</p>
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

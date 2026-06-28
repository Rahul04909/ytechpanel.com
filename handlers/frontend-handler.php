<?php
/**
 * YTech Panels — Frontend Popup Forms Handler
 * Handles AJAX submissions for Get A Quote, Custom Quote, Request Call Back, and Submit Enquiry.
 */
require_once dirname(__DIR__) . '/config/db.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed.']);
    exit;
}

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$action = $_POST['action'] ?? '';
$csrfToken = $_POST['csrf_token'] ?? '';

// CSRF validation
if ($csrfToken !== ($_SESSION['csrf_token'] ?? '')) {
    echo json_encode(['success' => false, 'message' => 'Invalid security token. Please refresh and try again.']);
    exit;
}

// Honeypot check (anti-spam)
if (!empty($_POST['website'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid request.']);
    exit;
}

$db = getDB();

try {

    // ─── GET A QUOTE / CUSTOM QUOTE ───
    if ($action === 'submit_quote') {
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $company = trim($_POST['company'] ?? '');
        $productInterest = trim($_POST['product_interest'] ?? '');
        $quantity = trim($_POST['quantity'] ?? '');
        $message = trim($_POST['message'] ?? '');

        if (empty($name) || empty($email) || empty($message)) {
            echo json_encode(['success' => false, 'message' => 'Please fill in all required fields (Name, Email, Message).']);
            exit;
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo json_encode(['success' => false, 'message' => 'Please enter a valid email address.']);
            exit;
        }

        $stmt = $db->prepare("INSERT INTO quotes (name, email, phone, company, product_interest, quantity, message, status) VALUES (?, ?, ?, ?, ?, ?, ?, 0)");
        $stmt->execute([$name, $email, $phone, $company, $productInterest, $quantity, $message]);

        echo json_encode(['success' => true, 'message' => 'Your quote request has been submitted successfully! Our team will get back to you within 24 hours.']);
        exit;
    }

    // ─── REQUEST CALL BACK ───
    if ($action === 'submit_callback') {
        $name = trim($_POST['name'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $preferredTime = trim($_POST['preferred_time'] ?? '');
        $message = trim($_POST['message'] ?? '');

        if (empty($name) || empty($phone)) {
            echo json_encode(['success' => false, 'message' => 'Please enter your name and phone number.']);
            exit;
        }

        $stmt = $db->prepare("INSERT INTO callback_requests (name, phone, email, preferred_time, message, status) VALUES (?, ?, ?, ?, ?, 0)");
        $stmt->execute([$name, $phone, $email, $preferredTime, $message]);

        echo json_encode(['success' => true, 'message' => 'Callback request submitted! We will call you back shortly.']);
        exit;
    }

    // ─── SUBMIT GENERAL ENQUIRY ───
    if ($action === 'submit_enquiry_popup') {
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $subject = trim($_POST['subject'] ?? '');
        $message = trim($_POST['message'] ?? '');

        if (empty($name) || empty($email) || empty($message)) {
            echo json_encode(['success' => false, 'message' => 'Please fill in all required fields (Name, Email, Message).']);
            exit;
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo json_encode(['success' => false, 'message' => 'Please enter a valid email address.']);
            exit;
        }

        $stmt = $db->prepare("INSERT INTO general_enquiries (name, email, phone, subject, message, status) VALUES (?, ?, ?, ?, ?, 0)");
        $stmt->execute([$name, $email, $phone, $subject, $message]);

        echo json_encode(['success' => true, 'message' => 'Your enquiry has been submitted! We will get back to you within 24 hours.']);
        exit;
    }

    echo json_encode(['success' => false, 'message' => 'Invalid action.']);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'An error occurred. Please try again later.']);
}

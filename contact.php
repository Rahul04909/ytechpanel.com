<?php
/**
 * YTech Panels — Contact Us Page
 * Professional B2B contact page with info cards, enquiry form, and map.
 */
session_start();
require_once __DIR__ . '/config/db.php';

$db = getDB();

// Handle form submission — store in product_enquiries table
$contactSuccess = false;
$contactError = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'contact_enquiry') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');
    $csrf = $_POST['csrf_token'] ?? '';

    if ($csrf !== ($_SESSION['csrf_token'] ?? '')) {
        $contactError = 'Invalid security token. Please refresh and try again.';
    } elseif (empty($name) || empty($email) || empty($message)) {
        $contactError = 'Please fill in all required fields (Name, Email, Message).';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $contactError = 'Please enter a valid email address.';
    } else {
        try {
            $stmt = $db->prepare("INSERT INTO product_enquiries (name, email, phone, message, product_id, created_at) VALUES (?, ?, ?, ?, 0, NOW())");
            $stmt->execute([$name, $email, $phone, $message]);
            $contactSuccess = true;
        } catch (\Exception $e) {
            $contactError = 'Failed to send message. Please try again later.';
        }
    }
}

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - YTech Panels | Electrical Control Panel Manufacturers</title>
    <meta name="description" content="Get in touch with YTech Panels. Call us at +91-85271-13372 or email sales@ytechpanels.com. Visit our facility in Gurugram, Haryana.">
    <meta name="keywords" content="contact YTech Panels, electrical control panel manufacturer, panel manufacturer Gurugram, India panel manufacturer">
    <link rel="icon" href="assets/logo.png" type="image/png">
    <link rel="stylesheet" href="assets/css/header.css">
    <link rel="stylesheet" href="assets/css/footer.css">
    <link rel="stylesheet" href="assets/css/about.css">
    <style>
        /* Contact-specific overrides */
        .ct-hero { background: linear-gradient(135deg, #141414 0%, #1a1a2e 50%, #16213e 100%); padding: 60px 0 50px; position: relative; overflow: hidden; }
        .ct-hero::before { content: ''; position: absolute; top: -50%; right: -20%; width: 600px; height: 600px; background: radial-gradient(circle, rgba(220,38,38,0.08) 0%, transparent 70%); border-radius: 50%; pointer-events: none; }
        .ct-hero .container { max-width: 1200px; margin: 0 auto; padding: 0 20px; position: relative; z-index: 1; }
        .ct-breadcrumb { display: flex; align-items: center; gap: 8px; margin-bottom: 16px; font-size: 13px; color: rgba(255,255,255,0.6); }
        .ct-breadcrumb a { color: rgba(255,255,255,0.7); text-decoration: none; transition: color 0.2s; }
        .ct-breadcrumb a:hover { color: #fff; }
        .ct-bc-sep { color: rgba(255,255,255,0.3); }
        .ct-bc-current { color: #dc2626; font-weight: 600; }
        .ct-hero h1 { font-family: 'Outfit', sans-serif; font-size: 36px; font-weight: 800; color: #fff; margin: 0 0 8px; letter-spacing: -0.5px; }
        .ct-hero p { font-size: 16px; color: rgba(255,255,255,0.7); max-width: 600px; margin: 0; line-height: 1.6; }

        .ct-section { padding: 60px 0; background: #f8fafc; }
        .ct-section .container { max-width: 1200px; margin: 0 auto; padding: 0 20px; }

        .ct-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 40px; align-items: start; }

        /* Info Cards */
        .ct-info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        .ct-info-card { background: #fff; border: 1px solid #e2e8f0; padding: 24px; transition: all 0.3s; }
        .ct-info-card:hover { border-color: #dc2626; box-shadow: 0 8px 20px rgba(0,0,0,0.06); }
        .ct-info-card .ct-ic-icon { width: 44px; height: 44px; display: flex; align-items: center; justify-content: center; background: #fef2f2; margin-bottom: 14px; }
        .ct-info-card .ct-ic-icon svg { width: 22px; height: 22px; }
        .ct-info-card h4 { font-family: 'Outfit', sans-serif; font-size: 14px; font-weight: 700; color: #1e293b; margin: 0 0 6px; }
        .ct-info-card p { font-size: 13px; color: #64748b; margin: 0; line-height: 1.6; }
        .ct-info-card a { color: #64748b; text-decoration: none; transition: color 0.2s; }
        .ct-info-card a:hover { color: #dc2626; }
        .ct-info-card.ct-full { grid-column: 1 / -1; }

        /* Map */
        .ct-map-wrap { border: 1px solid #e2e8f0; overflow: hidden; margin-top: 24px; }
        .ct-map-wrap iframe { width: 100%; height: 280px; border: 0; display: block; }

        /* Form */
        .ct-form-card { background: #fff; border: 1px solid #e2e8f0; overflow: hidden; }
        .ct-form-header { padding: 18px 24px; background: #fafbfc; border-bottom: 1px solid #e2e8f0; display: flex; align-items: center; gap: 10px; font-family: 'Outfit', sans-serif; font-size: 16px; font-weight: 700; color: #1e293b; }
        .ct-form-body { padding: 24px; }
        .ct-f-row { margin-bottom: 16px; }
        .ct-f-row label { display: block; font-size: 12px; font-weight: 600; color: #334155; margin-bottom: 4px; }
        .ct-f-row label .req { color: #dc2626; }
        .ct-f-input { width: 100%; padding: 10px 14px; border: 1.5px solid #e2e8f0; border-radius: 4px; font-size: 14px; font-family: inherit; color: #1e293b; box-sizing: border-box; transition: all 0.2s; }
        .ct-f-input:focus { outline: none; border-color: #dc2626; box-shadow: 0 0 0 3px rgba(220,38,38,0.06); }
        .ct-f-textarea { resize: vertical; min-height: 100px; }
        .ct-f-row-inline { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
        
        .ct-submit-btn { display: inline-flex; align-items: center; justify-content: center; gap: 8px; width: 100%; padding: 12px 24px; background: #dc2626; color: #fff; border: none; font-size: 14px; font-weight: 600; font-family: inherit; cursor: pointer; transition: all 0.2s; }
        .ct-submit-btn:hover { background: #b91c1c; box-shadow: 0 4px 12px rgba(220,38,38,0.25); }
        .ct-submit-btn:disabled { background: #fca5a5; cursor: not-allowed; }

        .ct-success { text-align: center; padding: 40px 24px; }
        .ct-success svg { width: 48px; height: 48px; color: #16a34a; margin-bottom: 16px; }
        .ct-success h3 { font-family: 'Outfit', sans-serif; font-size: 20px; color: #1e293b; margin: 0 0 8px; }
        .ct-success p { font-size: 14px; color: #64748b; margin: 0; }

        .ct-error { background: #fef2f2; border: 1px solid #fecaca; color: #991b1b; padding: 12px 16px; margin-bottom: 16px; font-size: 13px; border-radius: 4px; display: <?= !empty($contactError) ? 'block' : 'none' ?>; }

        .ct-whatsapp-section { padding: 40px 0; background: #fff; }
        .ct-whatsapp-section .container { max-width: 1200px; margin: 0 auto; padding: 0 20px; }
        .ct-whatsapp-box { text-align: center; padding: 40px; border: 1px solid #e2e8f0; }
        .ct-whatsapp-box h3 { font-family: 'Outfit', sans-serif; font-size: 20px; color: #1e293b; margin: 0 0 8px; }
        .ct-whatsapp-box p { font-size: 14px; color: #64748b; margin: 0 0 20px; }
        .ct-wa-btn { display: inline-flex; align-items: center; gap: 10px; background: #25d366; color: #fff; padding: 14px 32px; font-size: 15px; font-weight: 600; text-decoration: none; border-radius: 4px; transition: all 0.2s; }
        .ct-wa-btn:hover { background: #1ebe5a; color: #fff; transform: translateY(-2px); box-shadow: 0 6px 16px rgba(37,211,102,0.3); }
        .ct-wa-btn svg { width: 20px; height: 20px; fill: currentColor; }
        .ct-map-full { width: 100%; }

        @media (max-width: 1024px) {
            .ct-grid { grid-template-columns: 1fr; }
            .ct-hero h1 { font-size: 30px; }
        }
        @media (max-width: 768px) {
            .ct-hero { padding: 40px 0 36px; }
            .ct-hero h1 { font-size: 26px; }
            .ct-hero p { font-size: 14px; }
            .ct-info-grid { grid-template-columns: 1fr; }
            .ct-f-row-inline { grid-template-columns: 1fr; }
            .ct-section { padding: 40px 0; }
        }
        @media (max-width: 480px) {
            .ct-hero h1 { font-size: 22px; }
            .ct-form-header { padding: 14px 18px; font-size: 14px; }
            .ct-form-body { padding: 18px; }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header class="main-header" id="site-header">
        <?php include 'includes/header.php'; ?>
    </header>

    <!-- ===== HERO BANNER ===== -->
    <section class="ct-hero">
        <div class="container">
            <div class="ct-breadcrumb">
                <a href="index.php">Home</a>
                <span class="ct-bc-sep">/</span>
                <span class="ct-bc-current">Contact Us</span>
            </div>
            <h1>Contact Us</h1>
            <p>Have a project in mind? Get in touch with our team for a free consultation, quote, or technical discussion.</p>
        </div>
    </section>

    <!-- ===== CONTACT SECTION ===== -->
    <section class="ct-section">
        <div class="container">
            <div class="ct-grid">

                <!-- Left: Info + Map -->
                <div>
                    <div class="ct-info-grid">
                        <div class="ct-info-card">
                            <div class="ct-ic-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="#dc2626" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                            </div>
                            <h4>Registered Office</h4>
                            <p>Plot No. 123, Sector 5, IMT Manesar,<br>Gurugram, Haryana - 122050</p>
                        </div>
                        <div class="ct-info-card">
                            <div class="ct-ic-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="#dc2626" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.69 12 19.79 19.79 0 0 1 1.61 3.4 2 2 0 0 1 3.6 1.21h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L7.91 8.91a16 16 0 0 0 6 6l.91-.91a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                            </div>
                            <h4>Phone / Helpline</h4>
                            <p>
                                <a href="tel:+918527113372">+91-85271-13372</a><br>
                                <a href="tel:18001234567">1800-123-4567 (Toll Free)</a>
                            </p>
                        </div>
                        <div class="ct-info-card">
                            <div class="ct-ic-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="#dc2626" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                            </div>
                            <h4>Email</h4>
                            <p>
                                <a href="mailto:sales@ytechpanels.com">sales@ytechpanels.com</a><br>
                                <a href="mailto:info@ytechpanels.com">info@ytechpanels.com</a>
                            </p>
                        </div>
                        <div class="ct-info-card">
                            <div class="ct-ic-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="#dc2626" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                            </div>
                            <h4>Business Hours</h4>
                            <p>Monday – Saturday: 9:00 AM – 6:00 PM<br>Sunday: Closed</p>
                        </div>
                        <div class="ct-info-card ct-full">
                            <div class="ct-ic-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="#dc2626" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
                            </div>
                            <h4>GSTIN</h4>
                            <p>06DKQPM5749K1ZC</p>
                        </div>
                    </div>

                    <!-- Map -->
                    <div class="ct-map-wrap">
                        <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d224345.83978556186!2d76.97137864775829!3d28.458231567899152!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x390d0300e3aaa29b%3A0x1f4ce2e75d23f0b5!2sManesar%2C%20Haryana!5e0!3m2!1sen!2sin!4v1690000000000!5m2!1sen!2sin" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                    </div>
                </div>

                <!-- Right: Contact Form -->
                <div class="ct-form-card">
                    <div class="ct-form-header">
                        <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="#dc2626" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                        Send us a Message
                    </div>
                    <div class="ct-form-body">
                        <?php if ($contactSuccess): ?>
                            <div class="ct-success">
                                <svg viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                                <h3>Message Sent Successfully!</h3>
                                <p>Thank you for reaching out. Our team will get back to you within 24 hours.</p>
                            </div>
                        <?php else: ?>
                            <div class="ct-error" id="ctErrorMsg"><?= htmlspecialchars($contactError) ?></div>
                            <form method="POST" action="contact.php" id="ctContactForm">
                                <input type="hidden" name="action" value="contact_enquiry">
                                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
                                <div class="ct-f-row-inline">
                                    <div class="ct-f-row">
                                        <label>Your Name <span class="req">*</span></label>
                                        <input type="text" class="ct-f-input" name="name" required>
                                    </div>
                                    <div class="ct-f-row">
                                        <label>Email Address <span class="req">*</span></label>
                                        <input type="email" class="ct-f-input" name="email" required>
                                    </div>
                                </div>
                                <div class="ct-f-row-inline">
                                    <div class="ct-f-row">
                                        <label>Phone Number</label>
                                        <input type="tel" class="ct-f-input" name="phone" placeholder="+91-XXXXXXXXXX">
                                    </div>
                                    <div class="ct-f-row">
                                        <label>Subject</label>
                                        <input type="text" class="ct-f-input" name="subject" placeholder="e.g. Enquiry about PCC Panel">
                                    </div>
                                </div>
                                <div class="ct-f-row">
                                    <label>Message <span class="req">*</span></label>
                                    <textarea class="ct-f-input ct-f-textarea" name="message" required></textarea>
                                </div>
                                <button type="submit" class="ct-submit-btn" id="ctSubmitBtn">
                                    <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="#fff" stroke-width="2.5"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
                                    Send Message
                                </button>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- ===== WHATSAPP CTA ===== -->
    <section class="ct-whatsapp-section">
        <div class="container">
            <div class="ct-whatsapp-box">
                <h3>Prefer WhatsApp?</h3>
                <p>Chat directly with our sales team on WhatsApp for quick enquiries and instant responses.</p>
                <a href="https://wa.me/918527113372" target="_blank" rel="noopener noreferrer" class="ct-wa-btn">
                    <svg viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 0 1-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 0 1-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 0 1 2.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0 0 12.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 0 0 5.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 0 0-3.48-8.413z"/></svg>
                    Chat on WhatsApp
                </a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <?php include 'includes/footer.php'; ?>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('ctContactForm');
        if (form) {
            form.addEventListener('submit', function(e) {
                const btn = document.getElementById('ctSubmitBtn');
                btn.disabled = true;
                btn.innerHTML = '<svg viewBox="0 0 24 24" style="animation:spin 1s linear infinite;width:16px;height:16px;"><path d="M12 2v4"/></svg> Sending...';
            });
        }
    });
    </script>
</body>
</html>

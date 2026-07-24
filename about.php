<?php
/**
 * YTech Panels — About Us Page
 * Professional B2B company overview with story, stats, certifications, and contact CTA.
 */
require_once __DIR__ . '/config/db.php';
$db = getDB();

// Fetch client count for stats
$clientCount = 0;
try {
    $stmt = $db->query("SELECT COUNT(*) FROM clients WHERE status = 1");
    $clientCount = (int)$stmt->fetchColumn();
} catch (Exception $e) {}

$productCount = 0;
try {
    $stmt = $db->query("SELECT COUNT(*) FROM products WHERE status = 1");
    $productCount = (int)$stmt->fetchColumn();
} catch (Exception $e) {}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - YTech Panels | Electrical Control Panel Manufacturers</title>
    <meta name="description" content="YTech Panels is India's leading manufacturer of electrical control panels. 10+ years of expertise, ISO 9001 certified, 500+ B2B clients across India and 12+ countries.">
    <meta name="keywords" content="about YTech Panels, electrical control panel manufacturer India, PCC panel manufacturer, ISO certified panel manufacturer">
    <link rel="icon" href="assets/logo.png" type="image/png">
    <link rel="stylesheet" href="assets/css/header.css">
    <link rel="stylesheet" href="assets/css/footer.css">
    <link rel="stylesheet" href="assets/css/about.css">
</head>
<body>
    <!-- Header -->
    <header class="main-header" id="site-header">
        <?php include 'includes/header.php'; ?>
    </header>

    <!-- ===== HERO BANNER ===== -->
    <section class="ab-hero">
        <div class="container">
            <div class="ab-breadcrumb">
                <a href="index.php">Home</a>
                <span class="ab-bc-sep">/</span>
                <span class="ab-bc-current">About Us</span>
            </div>
            <h1>About YTech Panels</h1>
            <p>India's trusted manufacturer of premium electrical control panels — delivering quality, innovation, and reliability since 2015.</p>
        </div>
    </section>

    <!-- ===== COMPANY STORY ===== -->
    <section class="ab-story">
        <div class="container">
            <div class="ab-story-grid">
                <div class="ab-story-content">
                    <span class="ab-tagline">Our Story</span>
                    <h2>Powering Industries with Precision &amp; Reliability</h2>
                    <div class="ab-divider"></div>
                    <p>Founded in 2015, <strong>YTech Panels</strong> has rapidly grown into one of India's most trusted manufacturers of power distribution and control panels. What started as a small workshop with a vision to deliver uncompromising quality has today become a state-of-the-art manufacturing facility serving clients across India and 12+ countries worldwide.</p>
                    <p>Our journey has been driven by a singular mission — to provide industries with electrical control solutions that are not only reliable and compliant but also cost-effective and customized to their exact needs. From small-scale industries to Fortune 500 companies, our panels power operations across manufacturing, infrastructure, energy, and commercial sectors.</p>
                    <p>At YTech Panels, every product we ship undergoes rigorous quality checks at our in-house testing facility. We are committed to meeting the highest standards of safety, performance, and durability — because we know that our panels are the backbone of our clients' operations.</p>
                </div>
                <div class="ab-story-visual">
                    <div class="ab-image-frame">
                        <img src="assets/images/engineer_control_room.png" alt="YTech Panels Engineer Control Room" loading="lazy">
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ===== STATS COUNTER ===== -->
    <section class="ab-stats">
        <div class="container">
            <div class="ab-stats-grid">
                <div class="ab-stat-item">
                    <span class="ab-stat-num">10+</span>
                    <span class="ab-stat-label">Years of Excellence</span>
                </div>
                <div class="ab-stat-item">
                    <span class="ab-stat-num"><?= max($clientCount, 500) ?>+</span>
                    <span class="ab-stat-label">B2B Clients Served</span>
                </div>
                <div class="ab-stat-item">
                    <span class="ab-stat-num"><?= max($productCount, 50) ?>+</span>
                    <span class="ab-stat-label">Panel Configurations</span>
                </div>
                <div class="ab-stat-item">
                    <span class="ab-stat-num">12+</span>
                    <span class="ab-stat-label">Countries Exported</span>
                </div>
            </div>
        </div>
    </section>

    <!-- ===== MISSION & VISION ===== -->
    <section class="ab-mv">
        <div class="container">
            <div class="ab-mv-grid">
                <div class="ab-mv-card">
                    <div class="ab-mv-icon">
                        <svg viewBox="0 0 24 24" width="32" height="32" fill="none" stroke="#0b4a83" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                        </svg>
                    </div>
                    <h3>Our Mission</h3>
                    <p>To deliver superior-quality electrical control panels that ensure safety, efficiency, and reliability for every application. We strive to be the preferred partner for industries seeking customized, compliant, and cost-effective power distribution solutions.</p>
                </div>
                <div class="ab-mv-card">
                    <div class="ab-mv-icon">
                        <svg viewBox="0 0 24 24" width="32" height="32" fill="none" stroke="#0b4a83" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/>
                            <path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/>
                        </svg>
                    </div>
                    <h3>Our Vision</h3>
                    <p>To be a globally recognized brand in electrical control panel manufacturing — known for innovation, quality excellence, and customer-centric solutions that empower industries across the world.</p>
                </div>
                <div class="ab-mv-card">
                    <div class="ab-mv-icon">
                        <svg viewBox="0 0 24 24" width="32" height="32" fill="none" stroke="#0b4a83" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 22c5.523 0 10-4.477 10-10S17.523 2 12 2 2 6.477 2 12s4.477 10 10 10z"/>
                            <path d="M12 6v6l4 2"/>
                        </svg>
                    </div>
                    <h3>Our Values</h3>
                    <p>Integrity, precision, and customer focus are at the core of everything we do. We believe in building long-term partnerships through transparency, on-time delivery, and uncompromising quality standards.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- ===== WHY CHOOSE US ===== -->
    <section class="ab-wcu">
        <div class="container">
            <div class="ab-section-header">
                <span class="ab-tagline">Why YTech Panels</span>
                <h2>What Sets Us Apart</h2>
            </div>
            <div class="ab-wcu-grid">
                <div class="ab-wcu-item">
                    <div class="ab-wcu-icon">
                        <svg viewBox="0 0 24 24" width="28" height="28" fill="none" stroke="#0b4a83" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                            <polyline points="9 11 12 14 22 4"/>
                        </svg>
                    </div>
                    <h4>ISO 9001:2015 Certified</h4>
                    <p>Our quality management systems meet the highest international standards, ensuring consistency and excellence in every panel we manufacture.</p>
                </div>
                <div class="ab-wcu-item">
                    <div class="ab-wcu-icon">
                        <svg viewBox="0 0 24 24" width="28" height="28" fill="none" stroke="#0b4a83" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="4" y="4" width="16" height="16" rx="2"/>
                            <path d="M9 1v3"/>
                            <path d="M15 1v3"/>
                            <path d="M9 15V9h6l-4 4"/>
                        </svg>
                    </div>
                    <h4>In-House Testing Facility</h4>
                    <p>Complete type testing including temperature rise, short circuit withstand, and IP verification — all performed at our own facility.</p>
                </div>
                <div class="ab-wcu-item">
                    <div class="ab-wcu-icon">
                        <svg viewBox="0 0 24 24" width="28" height="28" fill="none" stroke="#0b4a83" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                            <polyline points="14 2 14 8 20 8"/>
                            <line x1="16" y1="13" x2="8" y2="13"/>
                            <line x1="16" y1="17" x2="8" y2="17"/>
                            <polyline points="10 9 9 9 8 9"/>
                        </svg>
                    </div>
                    <h4>Custom Engineered Solutions</h4>
                    <p>Every panel is designed and built as per your single-line diagram (SLD) and project specifications — no one-size-fits-all approach.</p>
                </div>
                <div class="ab-wcu-item">
                    <div class="ab-wcu-icon">
                        <svg viewBox="0 0 24 24" width="28" height="28" fill="none" stroke="#0b4a83" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/>
                            <circle cx="12" cy="10" r="3"/>
                        </svg>
                    </div>
                    <h4>Pan-India Service</h4>
                    <p>With a dedicated project management team and on-site commissioning support, we ensure seamless delivery and installation across India.</p>
                </div>
                <div class="ab-wcu-item">
                    <div class="ab-wcu-icon">
                        <svg viewBox="0 0 24 24" width="28" height="28" fill="none" stroke="#0b4a83" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="23 7 16 12 23 17 23 7"/>
                            <rect x="1" y="5" width="15" height="14" rx="2"/>
                        </svg>
                    </div>
                    <h4>10+ Years of Expertise</h4>
                    <p>A decade of hands-on experience in designing, manufacturing, and delivering electrical control solutions for diverse industrial applications.</p>
                </div>
                <div class="ab-wcu-item">
                    <div class="ab-wcu-icon">
                        <svg viewBox="0 0 24 24" width="28" height="28" fill="none" stroke="#0b4a83" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 20h9"/>
                            <path d="M16.376 3.622a1 1 0 0 1 3.002 3.002L7.368 18.635a2 2 0 0 1-.855.506l-2.872.838a.5.5 0 0 1-.62-.62l.838-2.872a2 2 0 0 1 .506-.854z"/>
                        </svg>
                    </div>
                    <h4>Direct Manufacturer Pricing</h4>
                    <p>No middlemen — get factory-direct pricing with complete transparency. Competitive rates without compromising on quality or delivery timelines.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- ===== CERTIFICATIONS ===== -->
    <section class="ab-cert">
        <div class="container">
            <div class="ab-section-header">
                <span class="ab-tagline">Certifications &amp; Compliance</span>
                <h2>Standards We Adhere To</h2>
            </div>
            <div class="ab-cert-grid">
                <div class="ab-cert-item">
                    <svg viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="1.5">
                        <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                        <polyline points="9 11 12 14 22 4"/>
                    </svg>
                    <span>ISO 9001:2015</span>
                </div>
                <div class="ab-cert-item">
                    <svg viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="1.5">
                        <path d="M9 12l2 2 4-4"/>
                        <path d="M12 22c5.523 0 10-4.477 10-10S17.523 2 12 2 2 6.477 2 12s4.477 10 10 10z"/>
                    </svg>
                    <span>IEC 61439</span>
                </div>
                <div class="ab-cert-item">
                    <svg viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="1.5">
                        <path d="M9 12l2 2 4-4"/>
                        <path d="M12 22c5.523 0 10-4.477 10-10S17.523 2 12 2 2 6.477 2 12s4.477 10 10 10z"/>
                    </svg>
                    <span>IS 8623</span>
                </div>
                <div class="ab-cert-item">
                    <svg viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="1.5">
                        <path d="M9 12l2 2 4-4"/>
                        <path d="M12 22c5.523 0 10-4.477 10-10S17.523 2 12 2 2 6.477 2 12s4.477 10 10 10z"/>
                    </svg>
                    <span>IS 4237</span>
                </div>
                <div class="ab-cert-item">
                    <svg viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="1.5">
                        <path d="M12 2v4"/>
                        <path d="M12 22v-4"/>
                        <path d="M2 12h4"/>
                        <path d="M22 12h-4"/>
                    </svg>
                    <span>IEC TR 61641</span>
                </div>
                <div class="ab-cert-item">
                    <svg viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="1.5">
                        <path d="M12 2a10 10 0 0 1 10 10"/>
                        <path d="M12 22a10 10 0 0 1-10-10"/>
                        <path d="M12 6a6 6 0 0 1 6 6"/>
                        <path d="M12 18a6 6 0 0 1-6-6"/>
                    </svg>
                    <span>RoHS Compliant</span>
                </div>
            </div>
        </div>
    </section>

    <!-- ===== CTA SECTION ===== -->
    <section class="ab-cta">
        <div class="container">
            <div class="ab-cta-box">
                <h2>Ready to Partner with YTech Panels?</h2>
                <p>Get in touch with our team for a free consultation, customized quote, or to discuss your project requirements.</p>
                <div class="ab-cta-buttons">
                    <a href="contact.php" class="ab-btn ab-btn-primary">
                        <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="#fff" stroke-width="2.5"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                        Contact Us
                    </a>
                    <a href="products.php" class="ab-btn ab-btn-secondary">
                        <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="#0b4a83" stroke-width="2.5"><rect x="2" y="3" width="20" height="14" rx="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>
                        Explore Products
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <?php include 'includes/footer.php'; ?>
</body>
</html>

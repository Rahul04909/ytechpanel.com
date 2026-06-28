<?php
/**
 * YTech Panels — Our Clients Page
 * Professional showcase of all valued clients added by admin.
 */
require_once __DIR__ . '/config/db.php';

$db = getDB();

// Fetch all active clients
$clients = [];
try {
    $stmt = $db->prepare("SELECT id, name, logo, website, description FROM clients WHERE status = 1 ORDER BY sort_order ASC, id ASC");
    $stmt->execute();
    $clients = $stmt->fetchAll();
} catch (Exception $e) {
    $clients = [];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Our Clients - YTech Panels | Electrical Control Panel Manufacturers</title>
    <meta name="description" content="YTech Panels is trusted by 500+ B2B clients across India and 12+ countries. View our valued clients from industrial, commercial, and government sectors.">
    <meta name="keywords" content="YTech Panels clients, electrical panel clients, B2B clients India, panel manufacturer clients">
    <link rel="icon" href="assets/logo.png" type="image/png">
    <link rel="stylesheet" href="assets/css/header.css">
    <link rel="stylesheet" href="assets/css/footer.css">
    <link rel="stylesheet" href="assets/css/clients.css">
    <style>
        /* Page-specific overrides */
        .cl-hero {
            background: linear-gradient(135deg, #141414 0%, #1a1a2e 50%, #16213e 100%);
            padding: 60px 0 50px;
            position: relative;
            overflow: hidden;
        }
        .cl-hero::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -20%;
            width: 600px;
            height: 600px;
            background: radial-gradient(circle, rgba(220,38,38,0.08) 0%, transparent 70%);
            border-radius: 50%;
            pointer-events: none;
        }
        .cl-hero::after {
            content: '';
            position: absolute;
            bottom: -30%;
            left: -10%;
            width: 400px;
            height: 400px;
            background: radial-gradient(circle, rgba(220,38,38,0.05) 0%, transparent 70%);
            border-radius: 50%;
            pointer-events: none;
        }
        .cl-hero .container { max-width: 1200px; margin: 0 auto; padding: 0 20px; position: relative; z-index: 1; }

        .cl-breadcrumb {
            display: flex; align-items: center; gap: 8px; margin-bottom: 16px;
            font-size: 13px; color: rgba(255,255,255,0.6);
        }
        .cl-breadcrumb a { color: rgba(255,255,255,0.7); text-decoration: none; transition: color 0.2s; }
        .cl-breadcrumb a:hover { color: #fff; }
        .cl-bc-sep { color: rgba(255,255,255,0.3); }
        .cl-bc-current { color: #dc2626; font-weight: 600; }

        .cl-hero h1 {
            font-family: 'Outfit', sans-serif;
            font-size: 36px; font-weight: 800; color: #fff;
            margin: 0 0 8px; letter-spacing: -0.5px;
        }
        .cl-hero p {
            font-size: 16px; color: rgba(255,255,255,0.7);
            max-width: 650px; margin: 0; line-height: 1.6;
        }

        .cl-section { padding: 60px 0; background: #f8fafc; }
        .cl-section .container { max-width: 1200px; margin: 0 auto; padding: 0 20px; }

        .cl-stats {
            display: grid; grid-template-columns: repeat(3, 1fr); gap: 24px;
            margin-bottom: 40px;
        }
        .cl-stat {
            text-align: center; padding: 28px 20px; background: #fff;
            border: 1px solid #e2e8f0;
        }
        .cl-stat-num {
            font-family: 'Outfit', sans-serif;
            font-size: 32px; font-weight: 800; color: #dc2626;
            display: block; margin-bottom: 4px;
        }
        .cl-stat-label { font-size: 14px; color: #64748b; }

        .cl-empty {
            text-align: center; padding: 80px 20px; color: #94a3b8;
        }
        .cl-empty svg { width: 64px; height: 64px; margin-bottom: 16px; color: #cbd5e1; }
        .cl-empty h3 { font-family: 'Outfit', sans-serif; font-size: 20px; color: #334155; margin: 0 0 8px; }
        .cl-empty p { font-size: 14px; color: #94a3b8; margin: 0; }

        /* Client cards with description */
        .client-card-wrapper {
            background: #fff;
            border: 1px solid #e2e8f0;
            padding: 16px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            aspect-ratio: 2 / 1;
            transition: all 0.3s;
        }
        .client-card-wrapper:hover {
            border-color: #dc2626;
            box-shadow: 0 8px 20px rgba(0,0,0,0.06);
            transform: translateY(-3px);
        }
        .client-card-wrapper .client-logo-img {
            width: 100%; height: 100%; object-fit: contain;
        }
        .client-card-wrapper .client-text-fallback {
            font-family: 'Outfit', sans-serif;
            font-size: 13px; font-weight: 700; color: #1e293b;
            text-align: center; padding: 8px;
        }

        @media (max-width: 1024px) {
            .cl-hero h1 { font-size: 30px; }
            .cl-stats { grid-template-columns: repeat(2, 1fr); }
        }
        @media (max-width: 768px) {
            .cl-hero { padding: 40px 0 36px; }
            .cl-hero h1 { font-size: 26px; }
            .cl-hero p { font-size: 14px; }
            .cl-section { padding: 40px 0; }
        }
        @media (max-width: 480px) {
            .cl-hero h1 { font-size: 22px; }
            .cl-stats { grid-template-columns: 1fr; gap: 12px; }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header class="main-header" id="site-header">
        <?php include 'includes/header.php'; ?>
    </header>

    <!-- ===== HERO BANNER ===== -->
    <section class="cl-hero">
        <div class="container">
            <div class="cl-breadcrumb">
                <a href="index.php">Home</a>
                <span class="cl-bc-sep">/</span>
                <span class="cl-bc-current">Our Clients</span>
            </div>
            <h1>Our Valued Clients</h1>
            <p>Trusted by 500+ B2B clients across India and 12+ countries — from manufacturing plants to Fortune 500 companies.</p>
        </div>
    </section>

    <!-- ===== CLIENTS GRID ===== -->
    <section class="cl-section">
        <div class="container">

            <!-- Stats Row -->
            <div class="cl-stats">
                <div class="cl-stat">
                    <span class="cl-stat-num"><?= count($clients) ?: '500' ?>+</span>
                    <span class="cl-stat-label">Trusted Clients</span>
                </div>
                <div class="cl-stat">
                    <span class="cl-stat-num">12+</span>
                    <span class="cl-stat-label">Countries Served</span>
                </div>
                <div class="cl-stat">
                    <span class="cl-stat-num">10+</span>
                    <span class="cl-stat-label">Years of Excellence</span>
                </div>
            </div>

            <?php if (!empty($clients)): ?>
                <div class="clients-grid">
                    <?php foreach ($clients as $client): ?>
                        <div class="client-card-wrapper" <?php if (!empty($client['description'])): ?>title="<?= htmlspecialchars($client['description']) ?>"<?php endif; ?>>
                            <?php
                            $logoSrc = '';
                            if (!empty($client['logo'])) {
                                if (strpos($client['logo'], 'data:') === 0) {
                                    $logoSrc = $client['logo'];
                                } else {
                                    $logoSrc = 'admin/uploads/client_logos/' . htmlspecialchars($client['logo']);
                                }
                            }
                            ?>
                            <?php if (!empty($logoSrc)): ?>
                                <img src="<?= $logoSrc ?>" alt="<?= htmlspecialchars($client['name']) ?>" class="client-logo-img">
                            <?php else: ?>
                                <div class="client-text-fallback"><?= htmlspecialchars($client['name']) ?></div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="cl-empty">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="2" y="2" width="20" height="20" rx="2" ry="2"/><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"/></svg>
                    <h3>No Clients Yet</h3>
                    <p>Our client list is being updated. Please check back soon.</p>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- Footer -->
    <?php include 'includes/footer.php'; ?>
</body>
</html>

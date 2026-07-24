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
        html { overflow-x: hidden; }
        body { overflow-x: hidden; }

        /* ===== HERO SECTION ===== */
        .cl-hero {
            background: linear-gradient(135deg, #0a1628 0%, #0f1d33 50%, #16213e 100%);
            padding: 70px 0 60px;
            position: relative;
            overflow: hidden;
        }
        .cl-hero::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -15%;
            width: 700px;
            height: 700px;
            background: radial-gradient(circle, rgba(11,74,131,0.1) 0%, transparent 70%);
            border-radius: 50%;
            pointer-events: none;
        }
        .cl-hero::after {
            content: '';
            position: absolute;
            bottom: -40%;
            left: -10%;
            width: 500px;
            height: 500px;
            background: radial-gradient(circle, rgba(11,74,131,0.06) 0%, transparent 70%);
            border-radius: 50%;
            pointer-events: none;
        }
        .cl-hero .container { max-width: 1200px; margin: 0 auto; padding: 0 20px; position: relative; z-index: 1; }

        .cl-breadcrumb {
            display: flex; align-items: center; gap: 8px; margin-bottom: 16px;
            font-size: 13px; color: rgba(255,255,255,0.5);
        }
        .cl-breadcrumb a { color: rgba(255,255,255,0.6); text-decoration: none; transition: color 0.2s; }
        .cl-breadcrumb a:hover { color: #fff; }
        .cl-bc-sep { color: rgba(255,255,255,0.25); }
        .cl-bc-current { color: #f26522; font-weight: 600; }

        .cl-hero h1 {
            font-family: 'Outfit', sans-serif;
            font-size: 38px; font-weight: 800; color: #fff;
            margin: 0 0 10px; letter-spacing: -0.5px;
        }
        .cl-hero p {
            font-size: 16px; color: rgba(255,255,255,0.65);
            max-width: 650px; margin: 0; line-height: 1.7;
        }

        /* ===== MAIN SECTION ===== */
        .cl-section {
            padding: 60px 0;
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
            position: relative;
        }
        .cl-section .container { max-width: 1200px; margin: 0 auto; padding: 0 20px; }

        /* ===== STATS ROW ===== */
        .cl-stats {
            display: grid; grid-template-columns: repeat(3, 1fr); gap: 24px;
            margin-bottom: 50px;
        }
        .cl-stat {
            text-align: center; padding: 32px 20px; background: #fff;
            border: 1px solid #e8edf4; border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.04);
            transition: all 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94);
            position: relative; overflow: hidden;
        }
        .cl-stat::before {
            content: '';
            position: absolute; top: 0; left: 0; right: 0;
            height: 3px;
            background: linear-gradient(90deg, var(--primary-color), var(--accent-orange));
        }
        .cl-stat:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 28px rgba(11,74,131,0.1);
            border-color: var(--primary-color);
        }
        .cl-stat-icon {
            width: 48px; height: 48px;
            margin: 0 auto 12px;
            display: flex; align-items: center; justify-content: center;
            background: rgba(11,74,131,0.08);
            border-radius: 50%;
            color: var(--primary-color);
        }
        .cl-stat-icon svg {
            width: 22px; height: 22px;
            fill: none; stroke: currentColor; stroke-width: 2;
        }
        .cl-stat-num {
            font-family: 'Outfit', sans-serif;
            font-size: 34px; font-weight: 800; color: #0b4a83;
            display: block; margin-bottom: 4px;
        }
        .cl-stat-label { font-size: 14px; color: #64748b; font-weight: 500; }

        /* ===== CLIENT CARDS GRID ===== */
        .cl-cards-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 24px;
        }

        /* ===== EMPTY STATE ===== */
        .cl-empty {
            text-align: center; padding: 80px 20px; color: #94a3b8;
        }
        .cl-empty svg { width: 64px; height: 64px; margin-bottom: 16px; color: #cbd5e1; }
        .cl-empty h3 { font-family: 'Outfit', sans-serif; font-size: 20px; color: #334155; margin: 0 0 8px; }
        .cl-empty p { font-size: 14px; color: #94a3b8; margin: 0; }

        /* ===== RESPONSIVE ===== */
        @media (max-width: 1024px) {
            .cl-hero h1 { font-size: 32px; }
            .cl-stats { grid-template-columns: repeat(2, 1fr); }
            .cl-cards-grid { grid-template-columns: repeat(3, 1fr); gap: 18px; }
        }
        @media (max-width: 768px) {
            .cl-hero { padding: 40px 0 36px; }
            .cl-hero h1 { font-size: 26px; }
            .cl-hero p { font-size: 14px; }
            .cl-section { padding: 40px 0; }
            .cl-cards-grid { grid-template-columns: repeat(2, 1fr); gap: 14px; }
        }
        @media (max-width: 480px) {
            .cl-hero h1 { font-size: 22px; }
            .cl-stats { grid-template-columns: 1fr; gap: 14px; }
            .cl-cards-grid { grid-template-columns: 1fr; gap: 14px; }
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
                    <div class="cl-stat-icon">
                        <svg viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                    </div>
                    <span class="cl-stat-num"><?= count($clients) ?: '500' ?>+</span>
                    <span class="cl-stat-label">Trusted Clients</span>
                </div>
                <div class="cl-stat">
                    <div class="cl-stat-icon">
                        <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>
                    </div>
                    <span class="cl-stat-num">12+</span>
                    <span class="cl-stat-label">Countries Served</span>
                </div>
                <div class="cl-stat">
                    <div class="cl-stat-icon">
                        <svg viewBox="0 0 24 24"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
                    </div>
                    <span class="cl-stat-num">10+</span>
                    <span class="cl-stat-label">Years of Excellence</span>
                </div>
            </div>

            <!-- Client Cards -->
            <?php if (!empty($clients)): ?>
                <div class="cl-cards-grid">
                    <?php foreach ($clients as $client):
                        $logoSrc = '';
                        if (!empty($client['logo'])) {
                            if (strpos($client['logo'], 'data:') === 0) {
                                $logoSrc = $client['logo'];
                            } else {
                                $logoSrc = 'admin/uploads/client_logos/' . htmlspecialchars($client['logo']);
                            }
                        }
                        $hasWebsite = !empty($client['website']);
                    ?>
                        <div class="client-card-wrapper">
                            <?php if ($hasWebsite): ?><a href="<?= htmlspecialchars($client['website']) ?>" target="_blank" rel="noopener noreferrer" class="client-logo-link"><?php endif; ?>
                                <div class="client-logo-content" style="padding:20px 14px 8px;">
                                    <?php if (!empty($logoSrc)): ?>
                                        <img src="<?= $logoSrc ?>" alt="<?= htmlspecialchars($client['name']) ?>" class="client-logo-img" loading="lazy">
                                    <?php else: ?>
                                        <div class="client-text-fallback"><?= htmlspecialchars($client['name']) ?></div>
                                    <?php endif; ?>
                                </div>
                                <span class="client-card-name"><?= htmlspecialchars($client['name']) ?></span>

                                <!-- Overlay on hover -->
                                <div class="client-overlay">
                                    <span class="client-overlay-name"><?= htmlspecialchars($client['name']) ?></span>
                                    <?php if (!empty($client['description'])): ?>
                                        <span class="client-overlay-desc"><?= htmlspecialchars($client['description']) ?></span>
                                    <?php endif; ?>
                                    <?php if ($hasWebsite): ?>
                                        <span class="client-overlay-link">
                                            Visit Website
                                            <svg viewBox="0 0 24 24"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
                                        </span>
                                    <?php endif; ?>
                                </div>
                            <?php if ($hasWebsite): ?></a><?php endif; ?>
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
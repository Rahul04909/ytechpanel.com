<?php
/**
 * YTech Panels — HTML Sitemap
 * Professional, SEO-friendly, user-facing HTML sitemap page.
 */
require_once __DIR__ . '/config/db.php';
$db = getDB();

// ─── Fetch Active Products ───
$products = [];
try {
    $stmt = $db->prepare("SELECT id, title FROM products WHERE status = 1 ORDER BY sort_order ASC, id DESC LIMIT 50");
    $stmt->execute();
    $products = $stmt->fetchAll();
} catch (Exception $e) {
    $products = [];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HTML Sitemap - YTech Panels</title>
    <meta name="description" content="Complete sitemap of YTech Panels — browse all pages, products, and resources for electrical control panels and power distribution solutions.">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="https://ytechpanels.com/html-sitemap.php">
    <link rel="icon" href="assets/logo.png" type="image/png">
    <link rel="stylesheet" href="assets/css/header.css">
    <link rel="stylesheet" href="assets/css/footer.css">
    <style>
        /* ===== HTML SITEMAP STYLES ===== */
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: #f8fafc;
            color: #1e293b;
            line-height: 1.6;
        }

        .sm-wrapper {
            max-width: 960px;
            margin: 0 auto;
            padding: 48px 24px 64px;
        }

        /* Header */
        .sm-header {
            margin-bottom: 40px;
            padding-bottom: 24px;
            border-bottom: 2px solid #e2e8f0;
        }

        .sm-header h1 {
            font-size: 28px;
            font-weight: 800;
            color: #0f172a;
            margin-bottom: 6px;
        }

        .sm-header p {
            font-size: 14px;
            color: #64748b;
        }

        .sm-header a {
            color: #dc2626;
            text-decoration: none;
        }

        .sm-header a:hover {
            text-decoration: underline;
        }

        /* Section */
        .sm-section {
            margin-bottom: 36px;
        }

        .sm-section-title {
            font-size: 17px;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 16px;
            padding-bottom: 8px;
            border-bottom: 1px dashed #cbd5e1;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .sm-section-title svg {
            width: 20px;
            height: 20px;
            flex-shrink: 0;
        }

        .sm-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 8px 24px;
        }

        .sm-grid.sm-grid-3col {
            grid-template-columns: 1fr 1fr 1fr;
        }

        .sm-link {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 8px 12px;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            color: #334155;
            text-decoration: none;
            font-size: 13.5px;
            transition: all 0.2s ease;
        }

        .sm-link:hover {
            border-color: #dc2626;
            background: #fef2f2;
            color: #dc2626;
            transform: translateX(2px);
        }

        .sm-link .sm-arrow {
            color: #dc2626;
            font-size: 15px;
            font-weight: 700;
            flex-shrink: 0;
        }

        .sm-link .sm-label {
            flex: 1;
        }

        .sm-link .sm-badge {
            font-size: 10px;
            font-weight: 600;
            color: #ffffff;
            background: #dc2626;
            padding: 2px 8px;
            border-radius: 10px;
            flex-shrink: 0;
        }

        /* Back to home */
        .sm-back {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin-top: 12px;
            color: #64748b;
            text-decoration: none;
            font-size: 14px;
            transition: color 0.2s ease;
        }

        .sm-back:hover {
            color: #dc2626;
        }

        @media (max-width: 640px) {
            .sm-wrapper { padding: 32px 16px 48px; }
            .sm-header h1 { font-size: 22px; }
            .sm-grid { grid-template-columns: 1fr; }
            .sm-grid.sm-grid-3col { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
    <header class="main-header" id="site-header">
        <?php include 'includes/header.php'; ?>
    </header>

    <main class="sm-wrapper">
        <!-- Header -->
        <div class="sm-header">
            <h1>HTML Sitemap</h1>
            <p>
                <a href="index.php">YTech Panels</a> — Complete site navigation. 
                Also available as <a href="sitemap.xml">XML Sitemap</a> for search engines.
            </p>
        </div>

        <!-- Main Pages -->
        <div class="sm-section">
            <h2 class="sm-section-title">
                <svg viewBox="0 0 24 24" fill="none" stroke="#dc2626" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                    <polyline points="9 22 9 12 15 12 15 22"/>
                </svg>
                Main Pages
            </h2>
            <div class="sm-grid">
                <a href="index.php" class="sm-link"><span class="sm-arrow">›</span><span class="sm-label">Home</span></a>
                <a href="about.php" class="sm-link"><span class="sm-arrow">›</span><span class="sm-label">About Us</span></a>
                <a href="products.php" class="sm-link"><span class="sm-arrow">›</span><span class="sm-label">Products</span><span class="sm-badge"><?= count($products) ?></span></a>
                <a href="clients.php" class="sm-link"><span class="sm-arrow">›</span><span class="sm-label">Our Clients</span></a>
                <a href="manufacturing.php" class="sm-link"><span class="sm-arrow">›</span><span class="sm-label">Manufacturing Facilities</span></a>
                <a href="quality.php" class="sm-link"><span class="sm-arrow">›</span><span class="sm-label">Quality Assurance</span></a>
                <a href="contact.php" class="sm-link"><span class="sm-arrow">›</span><span class="sm-label">Contact Us</span></a>
                <a href="html-sitemap.php" class="sm-link"><span class="sm-arrow">›</span><span class="sm-label">HTML Sitemap</span></a>
            </div>
        </div>

        <!-- Products -->
        <?php if (!empty($products)): ?>
        <div class="sm-section">
            <h2 class="sm-section-title">
                <svg viewBox="0 0 24 24" fill="none" stroke="#dc2626" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                </svg>
                All Products (<?= count($products) ?>)
            </h2>
            <div class="sm-grid sm-grid-3col">
                <?php foreach ($products as $product): ?>
                <a href="product-details.php?id=<?= (int)$product['id'] ?>" class="sm-link">
                    <span class="sm-arrow">›</span>
                    <span class="sm-label"><?= htmlspecialchars($product['title']) ?></span>
                </a>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- Resources -->
        <div class="sm-section">
            <h2 class="sm-section-title">
                <svg viewBox="0 0 24 24" fill="none" stroke="#dc2626" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                    <polyline points="14 2 14 8 20 8"/>
                </svg>
                Resources
            </h2>
            <div class="sm-grid">
                <a href="sitemap.xml" class="sm-link"><span class="sm-arrow">›</span><span class="sm-label">XML Sitemap</span></a>
                <a href="robots.txt" class="sm-link"><span class="sm-arrow">›</span><span class="sm-label">Robots.txt</span></a>
            </div>
        </div>

        <a href="index.php" class="sm-back">
            <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/>
            </svg>
            Back to Home
        </a>
    </main>

    <?php include 'includes/footer.php'; ?>
</body>
</html>

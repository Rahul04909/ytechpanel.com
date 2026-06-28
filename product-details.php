<?php
/**
 * YTech Panels — Product Details Page
 * IndiaMART-style professional B2B product layout with gallery, highlights, company details, enquiry form.
 */
require_once __DIR__ . '/config/db.php';

$db = getDB();
$productId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($productId <= 0) {
    header('Location: products.php');
    exit;
}

// Fetch product
$stmt = $db->prepare("SELECT * FROM products WHERE id = ? AND status = 1");
$stmt->execute([$productId]);
$product = $stmt->fetch();

if (!$product) {
    header('Location: products.php');
    exit;
}

// Parse gallery
$gallery = json_decode($product['gallery_images'] ?: '[]', true);
if (!is_array($gallery)) $gallery = [];

// Fetch reviews
$stmt = $db->prepare("SELECT id, name, rating, review, created_at FROM product_reviews WHERE product_id = ? AND status = 1 ORDER BY created_at DESC LIMIT 10");
$stmt->execute([$productId]);
$reviews = $stmt->fetchAll();

$avgRating = 0;
$ratingCounts = [5 => 0, 4 => 0, 3 => 0, 2 => 0, 1 => 0];
if (!empty($reviews)) {
    $ratings = array_column($reviews, 'rating');
    $avgRating = round(array_sum($ratings) / count($ratings), 1);
    foreach ($ratings as $r) {
        $r = (int)$r;
        if ($r >= 1 && $r <= 5) $ratingCounts[$r]++;
    }
}
$totalReviews = count($reviews);

// For display, if no reviews yet, show 4.7 with 214 as sample stats
$displayAvgRating = $totalReviews > 0 ? $avgRating : 4.7;
$displayReviewText = $totalReviews > 0 ? "$totalReviews review" . ($totalReviews !== 1 ? 's' : '') : 'Based on 214 ratings';

// Fetch related products
$stmt = $db->prepare("SELECT id, title, featured_image, short_description FROM products WHERE status = 1 AND id != ? ORDER BY RAND() LIMIT 4");
$stmt->execute([$productId]);
$relatedProducts = $stmt->fetchAll();

// Build all images
$allImages = [];
if (!empty($product['featured_image'])) {
    $allImages[] = ['type' => 'featured', 'src' => $product['featured_image']];
}
foreach ($gallery as $gImg) {
    $allImages[] = ['type' => 'gallery', 'src' => $gImg];
}

// Parse features from short_description
$features = [];
if (!empty($product['short_description'])) {
    $lines = explode("\n", $product['short_description']);
    foreach ($lines as $line) {
        $line = trim($line);
        if (!empty($line)) $features[] = $line;
    }
}
// Default features if none
if (empty($features)) {
    $features = ['Modular Design', 'Verified Quality', 'Quick Installation', 'Customizable Options', 'Industrial Grade'];
}

// Star helpers
function starSvg($filled = true, $size = 14) {
    $fill = $filled ? '#f59e0b' : '#d1d5db';
    return '<svg viewBox="0 0 24 24" width="' . $size . '" height="' . $size . '" fill="' . $fill . '" stroke="' . $fill . '" stroke-width="1"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>';
}
function starRow($rating, $size = 14) {
    $html = '';
    for ($i = 1; $i <= 5; $i++) $html .= starSvg($i <= round($rating), $size);
    return $html;
}

$schemaJson = !empty($product['schema_json']) ? $product['schema_json'] : '';
// Price display removed as requested
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($product['meta_title'] ?: $product['title']) ?> - YTech Panels</title>
    <meta name="description" content="<?= htmlspecialchars($product['meta_description'] ?: strip_tags($product['short_description'] ?: $product['title'])) ?>">
    <?php if (!empty($product['meta_keywords'])): ?>
    <meta name="keywords" content="<?= htmlspecialchars($product['meta_keywords']) ?>">
    <?php endif; ?>
    <?php if (!empty($product['og_image'])): ?>
    <meta property="og:image" content="<?= 'uploads/products/og/' . htmlspecialchars($product['og_image']) ?>">
    <?php elseif (!empty($product['featured_image'])): ?>
    <meta property="og:image" content="<?= 'uploads/products/featured/' . htmlspecialchars($product['featured_image']) ?>">
    <?php endif; ?>
    <?php if (!empty($schemaJson)): ?>
    <script type="application/ld+json"><?= $schemaJson ?></script>
    <?php endif; ?>
    <link rel="icon" href="assets/logo.png" type="image/png">
    <link rel="stylesheet" href="assets/css/header.css">
    <link rel="stylesheet" href="assets/css/footer.css">
    <link rel="stylesheet" href="assets/css/product-details.css">
</head>
<body>
    <header class="main-header" id="site-header">
        <?php include 'includes/header.php'; ?>
    </header>

    <!-- ===== BREADCRUMB ===== -->
    <div class="im-breadcrumb-bar">
        <div class="container">
            <div class="im-breadcrumb">
                <a href="index.php">Home</a>
                <span class="im-bc-sep">›</span>
                <a href="products.php">Products</a>
                <span class="im-bc-sep">›</span>
                <span class="im-bc-current"><?= htmlspecialchars($product['title']) ?></span>
            </div>
        </div>
    </div>

    <!-- ===== PRODUCT HEADER ===== -->
    <section class="im-header-section">
        <div class="container">
            <h1 class="im-product-title"><?= htmlspecialchars($product['title']) ?></h1>
            <div class="im-header-meta">
                <div class="im-rating-block">
                    <span class="im-stars"><?= $totalReviews > 0 ? starRow($avgRating, 16) : starRow(0, 16) ?></span>
                    <span class="im-rating-value"><?= $totalReviews > 0 ? $avgRating : '0.0' ?>/5</span>
                    <span class="im-rating-count">(<?= $totalReviews > 0 ? $totalReviews : '0' ?> reviews)</span>
                </div>
                <div class="im-status-badge">
                    <svg viewBox="0 0 24 24" width="12" height="12" fill="#16a34a"><circle cx="12" cy="12" r="6"/></svg>
                    In Stock
                </div>
            </div>
            <?php if (!empty($reviews)): ?>
            <div class="im-quote-snippets">
                <?php $quoteCount = 0; foreach ($reviews as $review): if ($quoteCount >= 2) break; $quoteCount++; ?>
                    <div class="im-quote-item">
                        <svg viewBox="0 0 24 24" width="12" height="12" fill="#dc2626"><path d="M6 17h3l2-4V7H5v6h3zm8 0h3l2-4V7h-6v6h3z"/></svg>
                        <span>"<?= htmlspecialchars(substr(strip_tags($review['review']), 0, 80)) ?><?= strlen(strip_tags($review['review'])) > 80 ? '...' : '' ?>"</span>
                    </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- ===== MAIN CONTENT: Gallery + Two Right Columns ===== -->
    <section class="im-main-section">
        <div class="container">
            <div class="im-main-grid">

                <!-- LEFT: Gallery -->
                <div class="im-gallery-col">
                    <div class="im-gallery-main" id="imGalleryMain">
                        <?php if (!empty($allImages)): ?>
                            <img src="uploads/products/<?= $allImages[0]['type'] ?>/<?= htmlspecialchars($allImages[0]['src']) ?>" alt="<?= htmlspecialchars($product['title']) ?>" id="imMainImg">
                        <?php else: ?>
                            <div class="im-no-img">
                                <svg viewBox="0 0 24 24" width="56" height="56" fill="none" stroke="#cbd5e1" stroke-width="1"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                                <span>No Image Available</span>
                            </div>
                        <?php endif; ?>
                    </div>
                    <?php if (count($allImages) > 1): ?>
                    <div class="im-thumb-strip" id="imThumbStrip">
                        <?php foreach ($allImages as $idx => $img): ?>
                            <div class="im-thumb <?= $idx === 0 ? 'active' : '' ?>" data-src="<?= htmlspecialchars($img['src']) ?>" data-type="<?= $img['type'] ?>">
                                <img src="uploads/products/<?= $img['type'] ?>/<?= htmlspecialchars($img['src']) ?>" alt="Thumbnail <?= $idx + 1 ?>" loading="lazy">
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- RIGHT: Two sub-columns -->
                <div class="im-right-col">
                    <div class="im-right-grid">

                        <!-- Column 1: Highlights & Price -->
                        <div class="im-highlights-col">
                                <!-- Key Features -->
                            <div class="im-feat-box">
                                <h3 class="im-feat-title">Product Highlights</h3>
                                <ul class="im-feat-list">
                                    <?php foreach ($features as $feat): ?>
                                    <li>
                                        <svg viewBox="0 0 24 24" width="16" height="16" fill="#16a34a"><circle cx="12" cy="12" r="6"/></svg>
                                        <?= htmlspecialchars($feat) ?>
                                    </li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>

                            <!-- CTA Buttons -->
                            <div class="im-cta-group">
                                <a href="#imEnquirySection" class="im-btn im-btn-primary" onclick="event.preventDefault();document.getElementById('imEnquirySection').scrollIntoView({behavior:'smooth'});">
                                    <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="#fff" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                                    Get Best Price
                                </a>
                                <a href="#imEnquirySection" class="im-btn im-btn-secondary" onclick="event.preventDefault();document.getElementById('imEnquirySection').scrollIntoView({behavior:'smooth'});">
                                    <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="#dc2626" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.69 12 19.79 19.79 0 0 1 1.61 3.4 2 2 0 0 1 3.6 1.21h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L7.91 8.91a16 16 0 0 0 6 6l.91-.91a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                                    Contact Supplier
                                </a>
                            </div>

                            <?php if ($product['enable_catalog'] && !empty($product['catalog_pdf'])): ?>
                            <div class="im-catalog-link">
                                <a href="uploads/products/catalogs/<?= htmlspecialchars($product['catalog_pdf']) ?>" target="_blank">
                                    <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="#dc2626" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                                    Download Product Catalog (PDF)
                                </a>
                            </div>
                            <?php endif; ?>
                        </div>

                        <!-- Column 2: Company Details + Enquiry -->
                        <div class="im-company-col">
                            <!-- Company Card -->
                            <div class="im-company-card">
                                <div class="im-company-badge">
                                    <svg viewBox="0 0 24 24" width="18" height="18" fill="#2563eb"><path d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0 1 12 2.944a11.955 11.955 0 0 1-8.618 3.04A12.02 12.02 0 0 0 3 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                                    <span>Verified Supplier</span>
                                </div>
                                <h3 class="im-company-name">YTech Panels</h3>

                                <!-- Logo added above GSTIN -->
                                <div class="im-company-logo">
                                    <img src="assets/logo.png" alt="YTech Panels Logo">
                                </div>

                                <div class="im-company-details">
                                    <div class="im-cd-row">
                                        <svg viewBox="0 0 24 24" width="15" height="15" fill="none" stroke="#64748b" stroke-width="2"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
                                        <span><strong>GSTIN</strong>: 06DKQPM5749K1ZC</span>
                                    </div>
                                    <div class="im-cd-row">
                                        <svg viewBox="0 0 24 24" width="15" height="15" fill="none" stroke="#64748b" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                                        <span><strong>Registered Office</strong>: 502, J/T-5, 5th Floor, Happy Homes, Budena, Sector 86, Faridabad, Haryana - 121002</span>
                                    </div>
                                    <div class="im-cd-row">
                                        <svg viewBox="0 0 24 24" width="15" height="15" fill="none" stroke="#64748b" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.69 12 19.79 19.79 0 0 1 1.61 3.4 2 2 0 0 1 3.6 1.21h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L7.91 8.91a16 16 0 0 0 6 6l.91-.91a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                                        <span><strong>24x7 Helpline</strong>: <a href="tel:+918527113372">1800-123-4567</a></span>
                                    </div>
                                </div>

                                <div class="im-company-links">
                                    <a href="#" class="im-cl-item">
                                        <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="#dc2626" stroke-width="2"><polygon points="23 7 16 12 23 17 23 7"/><rect x="1" y="5" width="15" height="14" rx="2"/></svg>
                                        View Company Video
                                    </a>
                                    <div class="im-cl-item">
                                        <svg viewBox="0 0 24 24" width="16" height="16" fill="#f59e0b"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                                        <span>Supplier Ratings: <strong>4.9/5</strong></span>
                                    </div>
                                </div>
                            </div>

                            <!-- Enquiry Form (Compact) -->
                            <div class="im-enquiry-card" id="imEnquirySection">
                                <div class="im-enq-header">
                                    <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="#dc2626" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                                    <span>Send Enquiry</span>
                                </div>
                                <div class="im-enq-body">
                                    <!-- Success -->
                                    <div class="im-enq-success" id="imEnquirySuccess">
                                        <svg viewBox="0 0 24 24" width="36" height="36" fill="none" stroke="#16a34a" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                                        <h4>Enquiry Submitted!</h4>
                                        <p>Our team will get back to you within 24 hours.</p>
                                    </div>
                                    <!-- Form -->
                                    <form id="imEnquiryForm">
                                        <input type="hidden" name="action" value="submit_enquiry">
                                        <input type="hidden" name="product_id" value="<?= $productId ?>">

                                        <div class="im-enq-field">
                                            <input type="text" class="im-enq-input" name="name" placeholder="Your Name *" required>
                                        </div>
                                        <div class="im-enq-field">
                                            <input type="email" class="im-enq-input" name="email" id="imEnquiryEmail" placeholder="Email Address *" required>
                                        </div>
                                        <div class="im-enq-field">
                                            <input type="tel" class="im-enq-input" name="phone" placeholder="Phone Number">
                                        </div>
                                        <div class="im-enq-field">
                                            <input type="text" class="im-enq-input" name="quantity" placeholder="Quantity Required (e.g. 2 units)">
                                        </div>
                                        <div class="im-enq-field">
                                            <textarea class="im-enq-input im-enq-textarea" name="message" placeholder="Your Message / Requirements *" required></textarea>
                                        </div>

                                        <br>
                                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">

                                        <button type="submit" class="im-btn im-btn-primary im-enq-submit" id="imSubmitEnquiryBtn">
                                            <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="#fff" stroke-width="2.5"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                                            Submit Enquiry
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ===== DESCRIPTION + REVIEWS SIDE-BY-SIDE ===== -->
    <section class="im-dr-section">
        <div class="container">
            <div class="im-dr-grid">

                <!-- LEFT: Description (narrower) -->
                <div class="im-dr-desc">
                    <div class="im-desc-card">
                        <h2 class="im-desc-heading">
                            <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="#dc2626" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
                            Product Description
                        </h2>
                        <div class="im-desc-content">
                            <?php if (!empty($product['description'])): ?>
                                <?= $product['description'] ?>
                            <?php else: ?>
                                <div class="im-desc-placeholder">
                                    <p><strong>About this Product</strong></p>
                                    <div class="im-desc-section-block">
                                        <h3>About <?= htmlspecialchars($product['title']) ?></h3>
                                        <p><?= htmlspecialchars($product['title']) ?> is a premium-grade electrical control panel engineered by YTech Panels for demanding industrial and commercial applications. Manufactured in our ISO-certified facility in Gurugram, each panel undergoes rigorous quality assurance at every stage of production.</p>
                                    </div>
                                    <div class="im-desc-section-block">
                                        <h3>Key Specifications</h3>
                                        <ul>
                                            <li><strong>Rated Voltage:</strong> 415V ±10%, 3-Phase, 50Hz</li>
                                            <li><strong>Rated Current:</strong> 100A to 3200A (as per configuration)</li>
                                            <li><strong>Busbar Rating:</strong> 630A to 3200A, Aluminium / Electrolytic Copper</li>
                                            <li><strong>Enclosure Protection:</strong> IP55 / IP65 (as per requirement)</li>
                                            <li><strong>Short Circuit Withstand:</strong> 50kA for 1 second</li>
                                            <li><strong>Standards Compliance:</strong> IS 8623, IEC 61439, IS 4237</li>
                                            <li><strong>Material:</strong> CRCA Steel Sheet, powder-coated (RAL 7032 / RAL 7035)</li>
                                            <li><strong>Ambient Temperature:</strong> Up to 50°C</li>
                                        </ul>
                                    </div>
                                    <div class="im-desc-section-block">
                                        <h3>Construction &amp; Design</h3>
                                        <ul>
                                            <li>Modular, compartmentalized design for safe maintenance and easy expansion</li>
                                            <li>Sheet steel enclosure with anti-corrosive powder coating</li>
                                            <li>Internal arc fault protection as per IEC TR 61641</li>
                                            <li>Shrouded busbar system for enhanced operator safety</li>
                                            <li>Cable entry from top / bottom with gland plate arrangement</li>
                                            <li>Dust-tight and vermin-proof construction suitable for harsh environments</li>
                                        </ul>
                                    </div>
                                    <div class="im-desc-section-block">
                                        <h3>Applications</h3>
                                        <ul>
                                            <li>Power distribution in industrial plants and manufacturing facilities</li>
                                            <li>Commercial complexes, shopping malls, and high-rise buildings</li>
                                            <li>Data centers, hospitals, and infrastructure projects</li>
                                            <li>Water treatment plants, pumping stations, and sewage systems</li>
                                            <li>Oil &amp; gas, cement, steel, textile, and pharmaceutical industries</li>
                                            <li>Renewable energy installations (solar farms, wind power)</li>
                                        </ul>
                                    </div>
                                    <div class="im-desc-section-block">
                                        <h3>Why Choose YTech Panels?</h3>
                                        <ul>
                                            <li><strong>10+ Years of Expertise</strong> — Trusted by 500+ B2B clients across India and 12+ international markets</li>
                                            <li><strong>ISO 9001:2015 Certified</strong> — Stringent quality management systems in place</li>
                                            <li><strong>In-House Testing Facility</strong> — Complete type testing including temperature rise, short circuit, and IP verification</li>
                                            <li><strong>Custom Engineered Solutions</strong> — Panels designed as per your SLD and project specifications</li>
                                            <li><strong>Pan-India Service</strong> — Dedicated project management and on-site commissioning support</li>
                                            <li><strong>Competitive Pricing</strong> — Direct manufacturer pricing with no middlemen</li>
                                        </ul>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- RIGHT: Reviews Sidebar (IndiaMART style) -->
                <div class="im-dr-rev">
                    <div class="im-rev-sidebar">
                        <div class="im-rsb-header">
                            <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="#dc2626" stroke-width="2"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                            <span>Customer Reviews</span>
                            <?php if ($totalReviews > 0): ?>
                                <span class="im-rsb-count">(<?= $totalReviews ?>)</span>
                            <?php endif; ?>
                        </div>
                        <div class="im-rsb-body">
                            <?php if ($totalReviews > 0): ?>
                                <!-- Rating Summary -->
                                <div class="im-rsb-summary">
                                    <div class="im-rsb-rating">
                                        <span class="im-rsb-big"><?= $displayAvgRating ?></span>
                                        <span class="im-rsb-stars"><?= starRow($displayAvgRating, 13) ?></span>
                                    </div>
                                    <div class="im-rsb-bars">
                                        <?php for ($i = 5; $i >= 1; $i--): ?>
                                            <?php $count = $ratingCounts[$i] ?? 0; $pct = $totalReviews > 0 ? round($count / $totalReviews * 100) : 0; ?>
                                            <div class="im-rsb-bar-row">
                                                <span class="im-rsb-bar-label"><?= $i ?></span>
                                                <div class="im-rsb-bar-track"><div class="im-rsb-bar-fill" style="width:<?= $pct ?>%"></div></div>
                                                <span class="im-rsb-bar-count"><?= $count ?></span>
                                            </div>
                                        <?php endfor; ?>
                                    </div>
                                </div>

                                <!-- Review List (compact) -->
                                <div class="im-rsb-list">
                                    <?php foreach ($reviews as $review): ?>
                                    <div class="im-rsb-item">
                                        <div class="im-rsb-item-top">
                                            <div class="im-rsb-avatar"><?= strtoupper(substr($review['name'], 0, 1)) ?></div>
                                            <div>
                                                <div class="im-rsb-name"><?= htmlspecialchars($review['name'])?></div>
                                                <div class="im-rsb-item-stars"><?= starRow($review['rating'], 10) ?></div>
                                            </div>
                                        </div>
                                        <p class="im-rsb-text <?= strlen(htmlspecialchars($review['review'])) > 100 ? 'im-rsb-text-clamp' : '' ?>" onclick="this.classList.toggle('im-rsb-text-clamp')">
                                            <?= htmlspecialchars($review['review']) ?>
                                            <?php if (strlen(htmlspecialchars($review['review'])) > 100): ?>
                                                <span class="im-rsb-readmore" onclick="event.stopPropagation();this.parentElement.classList.toggle('im-rsb-text-clamp')">Read more</span>
                                            <?php endif; ?>
                                        </p>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php else: ?>
                                <div class="im-rsb-empty">
                                    <svg viewBox="0 0 24 24" width="28" height="28" fill="none" stroke="#cbd5e1" stroke-width="1.5"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                                    <p>No reviews yet.</p>
                                </div>
                            <?php endif; ?>

                            <!-- Write Review Button (triggers inline form) -->
                            <button class="im-rsb-write-btn" id="imRsbWriteBtn" onclick="document.getElementById('imRsbForm').classList.toggle('active');this.style.display='none'">
                                <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                Write a Review
                            </button>

                            <!-- Inline Review Form -->
                            <div class="im-rsb-form" id="imRsbForm">
                                <form id="imReviewForm" onsubmit="submitReview(event)">
                                    <input type="hidden" name="action" value="submit_review">
                                    <input type="hidden" name="product_id" value="<?= $productId ?>">
                                    <div class="im-rsb-f-row">
                                        <label>Rating</label>
                                        <div class="im-star-input im-star-input-sm">
                                            <input type="radio" name="rating" value="5" id="im-s5"><label for="im-s5"><svg viewBox="0 0 24 24"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg></label>
                                            <input type="radio" name="rating" value="4" id="im-s4"><label for="im-s4"><svg viewBox="0 0 24 24"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg></label>
                                            <input type="radio" name="rating" value="3" id="im-s3"><label for="im-s3"><svg viewBox="0 0 24 24"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg></label>
                                            <input type="radio" name="rating" value="2" id="im-s2"><label for="im-s2"><svg viewBox="0 0 24 24"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg></label>
                                            <input type="radio" name="rating" value="1" id="im-s1" checked><label for="im-s1"><svg viewBox="0 0 24 24"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg></label>
                                        </div>
                                    </div>
                                    <div class="im-rsb-f-row">
                                        <input type="text" class="im-rsb-input" name="name" placeholder="Your Name *" required>
                                    </div>
                                    <div class="im-rsb-f-row">
                                        <input type="email" class="im-rsb-input" name="email" placeholder="Email (optional)">
                                    </div>
                                    <div class="im-rsb-f-row">
                                        <textarea class="im-rsb-input im-rsb-textarea" name="review" placeholder="Share your experience *" required></textarea>
                                    </div>
                                    <div style="position:absolute;left:-9999px;opacity:0;height:0;overflow:hidden;">
                                        <input type="text" name="website" tabindex="-1" autocomplete="off">
                                    </div>
                                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
                                    <button type="submit" class="im-btn im-btn-primary im-rsb-submit" id="imReviewSubmitBtn">Submit Review</button>
                                    <div class="im-rf-success" id="imReviewSuccess"></div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- ===== RELATED PRODUCTS ===== -->
    <?php if (!empty($relatedProducts)): ?>
    <section class="im-related-section">
        <div class="container">
            <div class="im-related-header">
                <h2>Related Products</h2>
                <a href="products.php" class="im-view-all">
                    View All
                    <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                </a>
            </div>
            <div class="im-related-grid">
                <?php foreach ($relatedProducts as $rel): ?>
                <div class="im-related-card">
                    <a href="product-details.php?id=<?= $rel['id'] ?>" class="im-rc-img">
                        <?php if (!empty($rel['featured_image'])): ?>
                            <img src="uploads/products/featured/<?= htmlspecialchars($rel['featured_image']) ?>" alt="<?= htmlspecialchars($rel['title']) ?>" loading="lazy">
                        <?php else: ?>
                            <div class="im-rc-noimg">
                                <svg viewBox="0 0 24 24" width="32" height="32" fill="none" stroke="#cbd5e1" stroke-width="1.5"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                            </div>
                        <?php endif; ?>
                    </a>
                    <div class="im-rc-body">
                        <h3 class="im-rc-title"><?= htmlspecialchars($rel['title']) ?></h3>
                        <?php if (!empty($rel['short_description'])): ?>
                            <p class="im-rc-desc"><?= htmlspecialchars(substr(strip_tags($rel['short_description']), 0, 80)) ?><?= strlen(strip_tags($rel['short_description'])) > 80 ? '...' : '' ?></p>
                        <?php endif; ?>
                        <a href="product-details.php?id=<?= $rel['id'] ?>" class="im-rc-link">Enquire Now →</a>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- ===== LIGHTBOX MODAL ===== -->
    <div class="im-lightbox" id="imLightbox" onclick="closeLightbox(event)">
        <button class="im-lb-close" onclick="closeLightbox()" aria-label="Close">
            <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="#fff" stroke-width="2.5" stroke-linecap="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
        </button>
        <button class="im-lb-nav im-lb-prev" id="imLbPrev" onclick="navigateLightbox(-1)" aria-label="Previous">
            <svg viewBox="0 0 24 24" width="28" height="28" fill="none" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
        </button>
        <button class="im-lb-nav im-lb-next" id="imLbNext" onclick="navigateLightbox(1)" aria-label="Next">
            <svg viewBox="0 0 24 24" width="28" height="28" fill="none" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
        </button>
        <div class="im-lb-counter" id="imLbCounter"></div>
        <div class="im-lb-content" onclick="event.stopPropagation()">
            <img id="imLbImg" src="" alt="Product Image">
        </div>
    </div>

    <!-- Footer -->
    <?php include 'includes/footer.php'; ?>

    <script>
    // ─── Lightbox Data ───
    const lightboxImages = [
        <?php foreach ($allImages as $img): ?>
        { src: 'uploads/products/<?= $img['type'] ?>/<?= htmlspecialchars($img['src']) ?>' },
        <?php endforeach; ?>
    ];
    let lightboxIndex = 0;

    function openLightbox(index) {
        if (!lightboxImages.length) return;
        lightboxIndex = index;
        const lb = document.getElementById('imLightbox');
        const img = document.getElementById('imLbImg');
        const counter = document.getElementById('imLbCounter');
        const prevBtn = document.getElementById('imLbPrev');
        const nextBtn = document.getElementById('imLbNext');

        img.src = lightboxImages[lightboxIndex].src;
        counter.textContent = (lightboxIndex + 1) + ' / ' + lightboxImages.length;
        prevBtn.style.display = lightboxImages.length > 1 ? 'flex' : 'none';
        nextBtn.style.display = lightboxImages.length > 1 ? 'flex' : 'none';
        lb.classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    function closeLightbox(e) {
        if (e && e.target !== e.currentTarget) return;
        document.getElementById('imLightbox').classList.remove('active');
        document.body.style.overflow = '';
    }

    function navigateLightbox(dir) {
        lightboxIndex += dir;
        if (lightboxIndex < 0) lightboxIndex = lightboxImages.length - 1;
        if (lightboxIndex >= lightboxImages.length) lightboxIndex = 0;
        document.getElementById('imLbImg').src = lightboxImages[lightboxIndex].src;
        document.getElementById('imLbCounter').textContent = (lightboxIndex + 1) + ' / ' + lightboxImages.length;
    }

    document.addEventListener('keydown', function(e) {
        const lb = document.getElementById('imLightbox');
        if (!lb.classList.contains('active')) return;
        if (e.key === 'Escape') closeLightbox();
        if (e.key === 'ArrowLeft') navigateLightbox(-1);
        if (e.key === 'ArrowRight') navigateLightbox(1);
    });

    // ─── Gallery Thumbnail Switcher ───
    document.addEventListener('DOMContentLoaded', function() {
        const thumbs = document.querySelectorAll('.im-thumb');
        const mainImg = document.getElementById('imMainImg');
        const galleryMain = document.getElementById('imGalleryMain');

        // Click main image to open lightbox
        if (galleryMain && lightboxImages.length) {
            galleryMain.addEventListener('click', function() {
                // Compare using endsWith since mainImg.src is absolute vs relative paths
                const src = mainImg ? mainImg.src : '';
                const idx = lightboxImages.findIndex(i => src.endsWith(i.src));
                openLightbox(idx >= 0 ? idx : 0);
            });
        }

        thumbs.forEach(thumb => {
            thumb.addEventListener('click', function() {
                thumbs.forEach(t => t.classList.remove('active'));
                this.classList.add('active');
                if (mainImg) {
                    mainImg.style.opacity = '0';
                    const newSrc = 'uploads/products/' + this.dataset.type + '/' + this.dataset.src;
                    setTimeout(() => {
                        mainImg.src = newSrc;
                        mainImg.style.opacity = '1';
                    }, 150);
                }
            });
        });
    });

    // ─── Enquiry Form ───
    document.getElementById('imEnquiryForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const btn = document.getElementById('imSubmitEnquiryBtn');
        btn.disabled=true; btn.innerHTML='<svg viewBox="0 0 24 24" style="animation:spin 1s linear infinite;width:16px;height:16px;"><path d="M12 2v4"/></svg> Submitting...';
        fetch('handlers/product-enquiry-handler.php', {
            method:'POST', body:new URLSearchParams(new FormData(this))
        }).then(r=>r.json()).then(data=>{
            if(data.success){
                document.getElementById('imEnquiryForm').style.display='none';
                document.getElementById('imEnquirySuccess').classList.add('active');
            } else {
                Swal.fire({icon:'error',title:'Error',text:data.message});
                btn.disabled=false; btn.innerHTML='<svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="#fff" stroke-width="2.5"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg> Submit Enquiry';
            }
        }).catch(()=>{
            Swal.fire({icon:'error',title:'Network Error',text:'Please try again.'});
            btn.disabled=false; btn.innerHTML='<svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="#fff" stroke-width="2.5"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg> Submit Enquiry';
        });
    });

    // ─── Review Form ───
    function submitReview(e) {
        e.preventDefault();
        const btn = document.getElementById('imReviewSubmitBtn');
        btn.disabled=true; btn.textContent='Submitting...';
        fetch('handlers/product-enquiry-handler.php', {
            method:'POST', body:new URLSearchParams(new FormData(document.getElementById('imReviewForm')))
        }).then(r=>r.json()).then(data=>{
            const msg=document.getElementById('imReviewSuccess');
            if(data.success){
                msg.textContent=data.message;
                msg.className='im-rf-success active';
                msg.style.borderColor='#bbf7d0'; msg.style.background='#f0fdf4'; msg.style.color='#16a34a';
                document.getElementById('imReviewForm').reset();
                document.getElementById('im-s1').checked=true;
            } else {
                msg.textContent=data.message;
                msg.className='im-rf-success active';
                msg.style.borderColor='#fecaca'; msg.style.background='#fef2f2'; msg.style.color='#dc2626';
            }
            btn.disabled=false; btn.textContent='Submit Review';
            setTimeout(()=>{msg.className='im-rf-success';},6000);
        }).catch(()=>{
            Swal.fire({icon:'error',title:'Network Error',text:'Please try again.'});
            btn.disabled=false; btn.textContent='Submit Review';
        });
    }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</body>
</html>

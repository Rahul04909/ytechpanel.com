<?php
/**
 * YTech Panels — Product Details Page
 * Professional e-commerce style product details with gallery, enquiry form, reviews, and related products.
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

// Calculate average rating
$avgRating = 0;
if (!empty($reviews)) {
    $ratings = array_column($reviews, 'rating');
    $avgRating = round(array_sum($ratings) / count($ratings), 1);
}

// Rating distribution
$ratingCounts = [1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0];
foreach ($reviews as $review) {
    $r = (int)$review['rating'];
    if (isset($ratingCounts[$r])) $ratingCounts[$r]++;
}
$totalReviews = count($reviews);

// Fetch related products (same category/random active products excluding current)
$stmt = $db->prepare("SELECT id, title, featured_image, short_description, created_at FROM products WHERE status = 1 AND id != ? ORDER BY RAND() LIMIT 4");
$stmt->execute([$productId]);
$relatedProducts = $stmt->fetchAll();

// Build all images array for gallery
$allImages = [];
if (!empty($product['featured_image'])) {
    $allImages[] = ['type' => 'featured', 'src' => $product['featured_image'], 'alt' => htmlspecialchars($product['title'])];
}
foreach ($gallery as $gImg) {
    $allImages[] = ['type' => 'gallery', 'src' => $gImg, 'alt' => htmlspecialchars($product['title']) . ' - Gallery'];
}

// Parse key features from short description (split by newlines or commas)
$features = [];
if (!empty($product['short_description'])) {
    $lines = explode("\n", $product['short_description']);
    foreach ($lines as $line) {
        $line = trim($line);
        if (!empty($line)) $features[] = $line;
    }
}

// Star SVG helper
function starSvg($filled = true, $size = 18) {
    $fill = $filled ? '#f59e0b' : '#e2e8f0';
    $stroke = $filled ? '#f59e0b' : '#e2e8f0';
    return '<svg viewBox="0 0 24 24" width="' . $size . '" height="' . $size . '" fill="' . $fill . '" stroke="' . $stroke . '" stroke-width="1"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>';
}

function starRow($rating, $size = 18) {
    $html = '';
    for ($i = 1; $i <= 5; $i++) {
        $html .= starSvg($i <= round($rating), $size);
    }
    return $html;
}

// Product schema JSON
$schemaJson = !empty($product['schema_json']) ? $product['schema_json'] : '';
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

    <!-- Header -->
    <header class="main-header" id="site-header">
        <?php include 'includes/header.php'; ?>
    </header>

    <!-- Breadcrumb Bar -->
    <div class="pd-breadcrumb-bar">
        <div class="container">
            <div class="pd-breadcrumb">
                <a href="index.php">Home</a>
                <span class="pd-breadcrumb-sep">›</span>
                <a href="products.php">Products</a>
                <span class="pd-breadcrumb-sep">›</span>
                <span class="pd-breadcrumb-current"><?= htmlspecialchars($product['title']) ?></span>
            </div>
        </div>
    </div>

    <!-- ============================================
         PRODUCT SHOWCASE (Two-column e-commerce layout)
         ============================================ -->
    <section class="pd-showcase">
        <div class="container">
            <div class="pd-showcase-layout">

                <!-- LEFT: Product Gallery -->
                <div class="pd-gallery-col">
                    <div class="pd-gallery">
                        <div class="pd-gallery-main" id="pdMainGallery">
                            <?php if (!empty($allImages)): ?>
                                <img src="uploads/products/<?= $allImages[0]['type'] ?>/<?= htmlspecialchars($allImages[0]['src']) ?>"
                                     alt="<?= $allImages[0]['alt'] ?>"
                                     id="pdMainImg"
                                     class="pd-main-img">
                                <!-- Zoom lens -->
                                <div class="pd-zoom-lens" id="pdZoomLens"></div>
                            <?php else: ?>
                                <div class="pd-no-image">
                                    <svg viewBox="0 0 24 24" width="64" height="64" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round">
                                        <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                                        <circle cx="8.5" cy="8.5" r="1.5"></circle>
                                        <polyline points="21 15 16 10 5 21"></polyline>
                                    </svg>
                                    <span>No Image Available</span>
                                </div>
                            <?php endif; ?>
                        </div>

                        <?php if (count($allImages) > 1): ?>
                        <div class="pd-thumb-strip" id="pdThumbStrip">
                            <?php foreach ($allImages as $index => $img): ?>
                                <div class="pd-thumb <?= $index === 0 ? 'active' : '' ?>"
                                     data-src="<?= htmlspecialchars($img['src']) ?>"
                                     data-type="<?= $img['type'] ?>">
                                    <img src="uploads/products/<?= $img['type'] ?>/<?= htmlspecialchars($img['src']) ?>"
                                         alt="Thumbnail <?= $index + 1 ?>"
                                         loading="lazy">
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- RIGHT: Product Info -->
                <div class="pd-info-col">
                    <!-- Brand / Category Tag -->
                    <div class="pd-brand-tag">
                        <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="3" y="3" width="18" height="18" rx="2"></rect></svg>
                        YTech Panels
                    </div>

                    <!-- Product Title -->
                    <h1 class="pd-title"><?= htmlspecialchars($product['title']) ?></h1>

                    <!-- Rating Row -->
                    <div class="pd-rating-row">
                        <?php if ($totalReviews > 0): ?>
                            <div class="pd-stars"><?= starRow($avgRating, 16) ?></div>
                            <span class="pd-rating-value"><?= $avgRating ?></span>
                            <span class="pd-rating-count">(<?= $totalReviews ?> reviews)</span>
                        <?php else: ?>
                            <div class="pd-stars"><?= starRow(0, 16) ?></div>
                            <span class="pd-rating-count">No reviews yet</span>
                        <?php endif; ?>
                        <span class="pd-status-badge available">
                            <svg viewBox="0 0 24 24" width="12" height="12" fill="currentColor"><circle cx="12" cy="12" r="6"></circle></svg>
                            Available
                        </span>
                    </div>

                    <!-- Short Description -->
                    <?php if (!empty($product['short_description'])): ?>
                        <p class="pd-short-desc"><?= htmlspecialchars(strip_tags($product['short_description'])) ?></p>
                    <?php endif; ?>

                    <!-- Key Features / Highlights -->
                    <?php if (!empty($features)): ?>
                    <div class="pd-features">
                        <h3 class="pd-features-title">Key Highlights</h3>
                        <ul class="pd-features-list">
                            <?php foreach (array_slice($features, 0, 6) as $feature): ?>
                                <li>
                                    <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="#16a34a" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                    <?= htmlspecialchars($feature) ?>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <?php endif; ?>

                    <!-- Action Buttons -->
                    <div class="pd-actions">
                        <a href="#enquirySection" class="pd-btn pd-btn-primary" onclick="event.preventDefault();document.getElementById('enquirySection').scrollIntoView({behavior:'smooth'});">
                            <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>
                            Enquire Now
                        </a>
                        <?php if ($product['enable_catalog'] && !empty($product['catalog_pdf'])): ?>
                        <a href="uploads/products/catalogs/<?= htmlspecialchars($product['catalog_pdf']) ?>" target="_blank" class="pd-btn pd-btn-secondary">
                            <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                            Download Catalog
                        </a>
                        <?php endif; ?>
                    </div>

                    <!-- Quick Contact -->
                    <div class="pd-quick-contact">
                        <div class="pd-qc-item">
                            <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.69 12 19.79 19.79 0 0 1 1.61 3.4 2 2 0 0 1 3.6 1.21h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L7.91 8.91a16 16 0 0 0 6 6l.91-.91a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path></svg>
                            <a href="tel:+918527113372">+91-85271-13372</a>
                        </div>
                        <div class="pd-qc-item">
                            <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>
                            <a href="mailto:sales@ytechpanels.com">sales@ytechpanels.com</a>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- ============================================
         PRODUCT DETAILS TABS
         ============================================ -->
    <section class="pd-tabs-section">
        <div class="container">
            <div class="pd-tabs-card">
                <div class="pd-tabs-nav" id="pdTabsNav">
                    <button class="pd-tab-btn active" data-tab="description">
                        <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line></svg>
                        Description
                    </button>
                    <?php if (!empty($features)): ?>
                    <button class="pd-tab-btn" data-tab="features">
                        <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"></polyline></svg>
                        Key Features
                    </button>
                    <?php endif; ?>
                    <button class="pd-tab-btn" data-tab="reviews">
                        <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
                        Reviews <?= $totalReviews > 0 ? '(' . $totalReviews . ')' : '' ?>
                    </button>
                </div>

                <div class="pd-tabs-content">
                    <!-- Tab: Description -->
                    <div class="pd-tab-pane active" id="tab-description">
                        <?php if (!empty($product['description'])): ?>
                            <div class="pd-desc-content">
                                <?= $product['description'] ?>
                            </div>
                        <?php else: ?>
                            <div class="pd-desc-empty">
                                <svg viewBox="0 0 24 24" width="32" height="32" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><polyline points="21 15 16 10 5 21"></polyline></svg>
                                <p>Detailed description not available for this product.</p>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Tab: Key Features -->
                    <?php if (!empty($features)): ?>
                    <div class="pd-tab-pane" id="tab-features">
                        <ul class="pd-features-full">
                            <?php foreach ($features as $feature): ?>
                                <li>
                                    <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="#16a34a" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                    <?= htmlspecialchars($feature) ?>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <?php endif; ?>

                    <!-- Tab: Reviews -->
                    <div class="pd-tab-pane" id="tab-reviews">
                        <?php if ($totalReviews > 0): ?>
                        <div class="pd-reviews-summary">
                            <div class="pd-rs-left">
                                <div class="pd-rs-big"><?= $avgRating ?></div>
                                <div class="pd-rs-stars"><?= starRow($avgRating, 20) ?></div>
                                <div class="pd-rs-total"><?= $totalReviews ?> review<?= $totalReviews !== 1 ? 's' : '' ?></div>
                            </div>
                            <div class="pd-rs-bars">
                                <?php for ($i = 5; $i >= 1; $i--): ?>
                                    <?php
                                    $count = $ratingCounts[$i] ?? 0;
                                    $pct = $totalReviews > 0 ? round($count / $totalReviews * 100) : 0;
                                    ?>
                                    <div class="pd-rs-bar-row">
                                        <span class="pd-rs-bar-label"><?= $i ?> <svg viewBox="0 0 24 24" width="12" height="12" fill="#f59e0b" stroke="#f59e0b"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg></span>
                                        <div class="pd-rs-bar-track">
                                            <div class="pd-rs-bar-fill" style="width:<?= $pct ?>%"></div>
                                        </div>
                                        <span class="pd-rs-bar-count"><?= $count ?></span>
                                    </div>
                                <?php endfor; ?>
                            </div>
                        </div>

                        <div class="pd-reviews-list">
                            <?php foreach ($reviews as $review): ?>
                                <div class="pd-review-item">
                                    <div class="pd-review-avatar"><?= strtoupper(substr($review['name'], 0, 1)) ?></div>
                                    <div class="pd-review-body">
                                        <div class="pd-review-header">
                                            <span class="pd-review-name"><?= htmlspecialchars($review['name']) ?></span>
                                            <span class="pd-review-date"><?= date('d M Y', strtotime($review['created_at'])) ?></span>
                                        </div>
                                        <div class="pd-review-stars"><?= starRow($review['rating'], 14) ?></div>
                                        <p class="pd-review-text"><?= htmlspecialchars($review['review']) ?></p>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <?php else: ?>
                        <div class="pd-reviews-empty">
                            <svg viewBox="0 0 24 24" width="40" height="40" fill="none" stroke="currentColor" stroke-width="1.5"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
                            <h4>No reviews yet</h4>
                            <p>Be the first to share your experience with this product.</p>
                        </div>
                        <?php endif; ?>

                        <!-- Write a Review Form -->
                        <div class="pd-write-review">
                            <h3 class="pd-wr-title">Write a Review</h3>
                            <form id="reviewForm" onsubmit="submitReview(event)">
                                <input type="hidden" name="action" value="submit_review">
                                <input type="hidden" name="product_id" value="<?= $productId ?>">

                                <div class="pd-wr-row">
                                    <label>Your Rating *</label>
                                    <div class="pd-star-input">
                                        <input type="radio" name="rating" value="5" id="wr-star5">
                                        <label for="wr-star5"><svg viewBox="0 0 24 24"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg></label>
                                        <input type="radio" name="rating" value="4" id="wr-star4">
                                        <label for="wr-star4"><svg viewBox="0 0 24 24"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg></label>
                                        <input type="radio" name="rating" value="3" id="wr-star3">
                                        <label for="wr-star3"><svg viewBox="0 0 24 24"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg></label>
                                        <input type="radio" name="rating" value="2" id="wr-star2">
                                        <label for="wr-star2"><svg viewBox="0 0 24 24"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg></label>
                                        <input type="radio" name="rating" value="1" id="wr-star1" checked>
                                        <label for="wr-star1"><svg viewBox="0 0 24 24"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg></label>
                                    </div>
                                </div>

                                <div class="pd-wr-row">
                                    <label>Your Name *</label>
                                    <input type="text" class="pd-wr-input" name="name" placeholder="Enter your name" required>
                                </div>

                                <div class="pd-wr-row">
                                    <label>Your Email</label>
                                    <input type="email" class="pd-wr-input" name="email" placeholder="Enter your email (optional)">
                                </div>

                                <div class="pd-wr-row">
                                    <label>Your Review *</label>
                                    <textarea class="pd-wr-input pd-wr-textarea" name="review" placeholder="Share your experience with this product..." required></textarea>
                                </div>

                                <div style="position:absolute;left:-9999px;opacity:0;height:0;overflow:hidden;">
                                    <input type="text" name="website" tabindex="-1" autocomplete="off">
                                </div>

                                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">

                                <button type="submit" class="pd-btn pd-btn-primary" id="reviewSubmitBtn">
                                    Submit Review
                                </button>

                                <div class="pd-wr-success" id="reviewSuccess"></div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ============================================
         ENQUIRY FORM SECTION
         ============================================ -->
    <section class="pd-enquiry-section" id="enquirySection">
        <div class="container">
            <div class="pd-enquiry-card">
                <div class="pd-enquiry-header">
                    <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>
                    <div>
                        <h2>Send Enquiry</h2>
                        <p>Interested in this product? Fill the form and our team will get back to you within 24 hours.</p>
                    </div>
                </div>
                <div class="pd-enquiry-body">
                    <!-- Success Message -->
                    <div class="pd-enquiry-success" id="enquirySuccess">
                        <svg viewBox="0 0 24 24" width="48" height="48" fill="none" stroke="#16a34a" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
                        <h3>Enquiry Submitted!</h3>
                        <p>Thank you! Our team will get back to you within 24 hours.</p>
                    </div>

                    <!-- Enquiry Form -->
                    <form id="enquiryForm" class="pd-enquiry-form">
                        <input type="hidden" name="action" value="submit_enquiry">
                        <input type="hidden" name="product_id" value="<?= $productId ?>">

                        <div class="pd-enq-row">
                            <div class="pd-enq-field">
                                <label>Your Name <span class="pd-req">*</span></label>
                                <input type="text" class="pd-enq-input" name="name" placeholder="Enter your full name" required>
                            </div>
                            <div class="pd-enq-field">
                                <label>Email Address <span class="pd-req">*</span></label>
                                <input type="email" class="pd-enq-input" name="email" id="enquiryEmail" placeholder="Enter your email address" required>
                            </div>
                        </div>

                        <div class="pd-enq-row">
                            <div class="pd-enq-field">
                                <label>Phone Number</label>
                                <input type="tel" class="pd-enq-input" name="phone" placeholder="Enter your phone number">
                            </div>
                            <div class="pd-enq-field">
                                <label>Quantity Required</label>
                                <input type="text" class="pd-enq-input" name="quantity" placeholder="e.g. 2 units, 50 pieces">
                            </div>
                        </div>

                        <div class="pd-enq-row">
                            <div class="pd-enq-field" style="flex:1;">
                                <label>Your Message <span class="pd-req">*</span></label>
                                <textarea class="pd-enq-input pd-enq-textarea" name="message" placeholder="Describe your requirements, specifications, or any questions..." required></textarea>
                            </div>
                        </div>

                        <!-- OTP Section -->
                        <div class="pd-enq-otp-row">
                            <button type="button" class="pd-btn pd-btn-otp" id="sendOtpBtn" onclick="sendOtp()">
                                <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="2" y="2" width="20" height="20" rx="4"></rect><path d="M22 6l-10 7L2 6"></path></svg>
                                Verify Email via OTP
                            </button>

                            <div class="pd-otp-section" id="otpSection">
                                <p class="pd-otp-info">Enter the 6-digit OTP sent to your email:</p>
                                <div class="pd-otp-row">
                                    <input type="text" class="pd-enq-input pd-otp-input" id="otpInput" placeholder="000000" maxlength="6" inputmode="numeric" pattern="[0-9]*">
                                    <button type="button" class="pd-btn pd-otp-verify-btn" id="verifyOtpBtn" onclick="verifyOtp()">Verify</button>
                                </div>
                                <div class="pd-otp-status" id="otpStatus"></div>
                                <div class="pd-otp-timer" id="otpTimer"></div>
                            </div>
                        </div>

                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">

                        <button type="submit" class="pd-btn pd-btn-primary pd-btn-submit" id="submitEnquiryBtn">
                            <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>
                            Submit Enquiry
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <!-- ============================================
         RELATED PRODUCTS
         ============================================ -->
    <?php if (!empty($relatedProducts)): ?>
    <section class="pd-related-section">
        <div class="container">
            <div class="pd-related-header">
                <h2>
                    <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="3" width="7" height="7"></rect><rect x="14" y="14" width="7" height="7"></rect><rect x="3" y="14" width="7" height="7"></rect></svg>
                    Related Products
                </h2>
                <a href="products.php" class="pd-view-all-link">
                    View All
                    <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
                </a>
            </div>
            <div class="pd-related-grid">
                <?php foreach ($relatedProducts as $rel): ?>
                    <div class="pd-related-card">
                        <a href="product-details.php?id=<?= $rel['id'] ?>" class="pd-rc-img">
                            <?php if (!empty($rel['featured_image'])): ?>
                                <img src="uploads/products/featured/<?= htmlspecialchars($rel['featured_image']) ?>" alt="<?= htmlspecialchars($rel['title']) ?>" loading="lazy">
                            <?php else: ?>
                                <div class="pd-rc-noimg">
                                    <svg viewBox="0 0 24 24" width="36" height="36" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><polyline points="21 15 16 10 5 21"></polyline></svg>
                                </div>
                            <?php endif; ?>
                        </a>
                        <div class="pd-rc-body">
                            <h3 class="pd-rc-title"><?= htmlspecialchars($rel['title']) ?></h3>
                            <?php if (!empty($rel['short_description'])): ?>
                                <p class="pd-rc-desc"><?= htmlspecialchars(substr(strip_tags($rel['short_description']), 0, 100)) ?><?= strlen(strip_tags($rel['short_description'])) > 100 ? '...' : '' ?></p>
                            <?php endif; ?>
                            <a href="product-details.php?id=<?= $rel['id'] ?>" class="pd-rc-link">
                                Enquire Now
                                <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- Footer -->
    <?php include 'includes/footer.php'; ?>

    <script>
    // ─── Gallery Thumbnail Switcher ───
    document.addEventListener('DOMContentLoaded', function() {
        const thumbs = document.querySelectorAll('.pd-thumb');
        const mainImg = document.getElementById('pdMainImg');

        thumbs.forEach(thumb => {
            thumb.addEventListener('click', function() {
                thumbs.forEach(t => t.classList.remove('active'));
                this.classList.add('active');
                if (mainImg) {
                    const type = this.dataset.type;
                    const src = this.dataset.src;
                    mainImg.style.opacity = '0';
                    setTimeout(() => {
                        mainImg.src = 'uploads/products/' + type + '/' + src;
                        mainImg.style.opacity = '1';
                    }, 150);
                }
            });
        });

        // ─── Image Zoom on Hover ───
        const gallery = document.getElementById('pdMainGallery');
        const lens = document.getElementById('pdZoomLens');

        if (gallery && mainImg && lens) {
            gallery.addEventListener('mousemove', function(e) {
                const rect = gallery.getBoundingClientRect();
                const x = e.clientX - rect.left;
                const y = e.clientY - rect.top;
                const w = rect.width;
                const h = rect.height;

                lens.style.display = 'block';
                lens.style.left = (x - 60) + 'px';
                lens.style.top = (y - 60) + 'px';

                const pctX = (x / w) * 100;
                const pctY = (y / h) * 100;
                mainImg.style.transformOrigin = pctX + '% ' + pctY + '%';
                mainImg.style.transform = 'scale(1.8)';
            });

            gallery.addEventListener('mouseleave', function() {
                lens.style.display = 'none';
                mainImg.style.transform = 'scale(1)';
                mainImg.style.transformOrigin = 'center center';
            });
        }

        // ─── Tab Switching ───
        const tabBtns = document.querySelectorAll('.pd-tab-btn');
        const tabPanes = document.querySelectorAll('.pd-tab-pane');

        tabBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                const tab = this.dataset.tab;
                tabBtns.forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                tabPanes.forEach(p => p.classList.remove('active'));
                const pane = document.getElementById('tab-' + tab);
                if (pane) pane.classList.add('active');
            });
        });
    });

    // ─── OTP Functions ───
    let otpVerified = false;
    let otpTimerInterval = null;
    let otpCooldown = false;

    function sendOtp() {
        const email = document.getElementById('enquiryEmail').value.trim();
        if (!email || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
            Swal.fire({icon: 'warning', title: 'Valid Email Required', text: 'Please enter a valid email address first.'});
            document.getElementById('enquiryEmail').focus();
            return;
        }

        if (otpCooldown) return;

        const btn = document.getElementById('sendOtpBtn');
        btn.disabled = true;
        btn.innerHTML = '<svg viewBox="0 0 24 24" style="animation:spin 1s linear infinite;width:16px;height:16px;"><path d="M12 2v4"></path></svg> Sending...';

        fetch('handlers/product-enquiry-handler.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: 'action=send_otp&email=' + encodeURIComponent(email)
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                document.getElementById('otpSection').classList.add('active');
                document.getElementById('otpStatus').className = 'pd-otp-status success';
                document.getElementById('otpStatus').textContent = 'OTP sent to ' + email;
                document.getElementById('otpStatus').style.display = 'block';
                startOtpTimer();
                otpCooldown = true;
                setTimeout(() => { otpCooldown = false; }, 60000);
                btn.innerHTML = '<svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="2" y="2" width="20" height="20" rx="4"></rect><path d="M22 6l-10 7L2 6"></path></svg> Resend OTP (60s)';
                document.getElementById('otpInput').focus();
            } else {
                Swal.fire({icon: 'error', title: 'Failed', text: data.message});
                btn.disabled = false;
                btn.innerHTML = '<svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="2" y="2" width="20" height="20" rx="4"></rect><path d="M22 6l-10 7L2 6"></path></svg> Verify Email via OTP';
            }
        })
        .catch(() => {
            Swal.fire({icon: 'error', title: 'Network Error', text: 'Please try again.'});
            btn.disabled = false;
            btn.innerHTML = '<svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="2" y="2" width="20" height="20" rx="4"></rect><path d="M22 6l-10 7L2 6"></path></svg> Verify Email via OTP';
        });
    }

    function verifyOtp() {
        const otp = document.getElementById('otpInput').value.trim();
        const email = document.getElementById('enquiryEmail').value.trim();

        if (!otp || otp.length !== 6) {
            document.getElementById('otpStatus').className = 'pd-otp-status error';
            document.getElementById('otpStatus').textContent = 'Please enter the 6-digit OTP.';
            document.getElementById('otpStatus').style.display = 'block';
            return;
        }

        const btn = document.getElementById('verifyOtpBtn');
        btn.disabled = true;
        btn.textContent = 'Verifying...';

        fetch('handlers/product-enquiry-handler.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: 'action=verify_otp&otp=' + encodeURIComponent(otp) + '&email=' + encodeURIComponent(email)
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                otpVerified = true;
                document.getElementById('otpStatus').className = 'pd-otp-status success';
                document.getElementById('otpStatus').textContent = '✓ Email verified successfully!';
                document.getElementById('otpStatus').style.display = 'block';
                document.getElementById('sendOtpBtn').innerHTML = '✓ Email Verified';
                document.getElementById('sendOtpBtn').disabled = true;
                document.getElementById('sendOtpBtn').style.borderColor = '#16a34a';
                document.getElementById('sendOtpBtn').style.color = '#16a34a';
                if (otpTimerInterval) clearInterval(otpTimerInterval);
                document.getElementById('otpTimer').textContent = '';
                document.getElementById('otpInput').readOnly = true;
                document.getElementById('verifyOtpBtn').textContent = 'Verified';
                document.getElementById('verifyOtpBtn').disabled = true;
            } else {
                document.getElementById('otpStatus').className = 'pd-otp-status error';
                document.getElementById('otpStatus').textContent = data.message;
                document.getElementById('otpStatus').style.display = 'block';
                btn.disabled = false;
                btn.textContent = 'Verify';
            }
        })
        .catch(() => {
            document.getElementById('otpStatus').className = 'pd-otp-status error';
            document.getElementById('otpStatus').textContent = 'Network error. Please try again.';
            document.getElementById('otpStatus').style.display = 'block';
            btn.disabled = false;
            btn.textContent = 'Verify';
        });
    }

    function startOtpTimer() {
        if (otpTimerInterval) clearInterval(otpTimerInterval);
        let seconds = 300;
        updateTimerDisplay(seconds);
        otpTimerInterval = setInterval(() => {
            seconds--;
            updateTimerDisplay(seconds);
            if (seconds <= 0) {
                clearInterval(otpTimerInterval);
                document.getElementById('otpTimer').textContent = 'OTP expired. Please request a new one.';
            }
        }, 1000);
    }

    function updateTimerDisplay(seconds) {
        const min = Math.floor(seconds / 60);
        const sec = seconds % 60;
        document.getElementById('otpTimer').textContent = 'OTP expires in ' + min + ':' + (sec < 10 ? '0' : '') + sec;
    }

    // ─── Enquiry Form Submission ───
    document.getElementById('enquiryForm').addEventListener('submit', function(e) {
        e.preventDefault();

        const btn = document.getElementById('submitEnquiryBtn');
        btn.disabled = true;
        btn.innerHTML = '<svg viewBox="0 0 24 24" style="animation:spin 1s linear infinite;width:16px;height:16px;"><path d="M12 2v4"></path></svg> Submitting...';

        const formData = new FormData(this);

        fetch('handlers/product-enquiry-handler.php', {
            method: 'POST',
            body: new URLSearchParams(formData)
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                document.getElementById('enquiryForm').style.display = 'none';
                document.getElementById('enquirySuccess').classList.add('active');
            } else {
                Swal.fire({icon: 'error', title: 'Error', text: data.message});
                btn.disabled = false;
                btn.innerHTML = '<svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg> Submit Enquiry';
            }
        })
        .catch(() => {
            Swal.fire({icon: 'error', title: 'Network Error', text: 'Please try again.'});
            btn.disabled = false;
            btn.innerHTML = '<svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg> Submit Enquiry';
        });
    });

    // ─── Review Form Submission ───
    function submitReview(e) {
        e.preventDefault();

        const btn = document.getElementById('reviewSubmitBtn');
        btn.disabled = true;
        btn.textContent = 'Submitting...';

        const formData = new FormData(document.getElementById('reviewForm'));

        fetch('handlers/product-enquiry-handler.php', {
            method: 'POST',
            body: new URLSearchParams(formData)
        })
        .then(r => r.json())
        .then(data => {
            const msgEl = document.getElementById('reviewSuccess');
            if (data.success) {
                msgEl.textContent = data.message;
                msgEl.className = 'pd-wr-success active';
                msgEl.style.borderColor = '#bbf7d0';
                msgEl.style.background = '#f0fdf4';
                msgEl.style.color = '#16a34a';
                document.getElementById('reviewForm').reset();
                document.getElementById('wr-star1').checked = true;
            } else {
                msgEl.textContent = data.message;
                msgEl.className = 'pd-wr-success active';
                msgEl.style.borderColor = '#fecaca';
                msgEl.style.background = '#fef2f2';
                msgEl.style.color = '#dc2626';
            }
            btn.disabled = false;
            btn.textContent = 'Submit Review';
            setTimeout(() => { msgEl.className = 'pd-wr-success'; }, 6000);
        })
        .catch(() => {
            Swal.fire({icon: 'error', title: 'Network Error', text: 'Please try again.'});
            btn.disabled = false;
            btn.textContent = 'Submit Review';
        });
    }
    </script>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

</body>
</html>

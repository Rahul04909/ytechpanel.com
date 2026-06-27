<?php
/**
 * YTech Panels — Product Details Page
 * Professional B2B product details with gallery, enquiry form (OTP verified), reviews, and related products.
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

// Fetch related products (same category/random active products excluding current)
$stmt = $db->prepare("SELECT id, title, featured_image, short_description FROM products WHERE status = 1 AND id != ? ORDER BY RAND() LIMIT 4");
$stmt->execute([$productId]);
$relatedProducts = $stmt->fetchAll();
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
    <?php if (!empty($product['schema_json'])): ?>
    <script type="application/ld+json"><?= $product['schema_json'] ?></script>
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

    <!-- Page Banner -->
    <section class="product-detail-banner">
        <div class="container">
            <div class="product-detail-breadcrumb">
                <a href="index.php">Home</a>
                <span class="sep">/</span>
                <a href="products.php">Products</a>
                <span class="sep">/</span>
                <span class="current"><?= htmlspecialchars($product['title']) ?></span>
            </div>
            <h1><?= htmlspecialchars($product['title']) ?></h1>
        </div>
    </section>

    <!-- Main Content -->
    <section class="product-detail-main">
        <div class="container">
            <div class="product-detail-layout">

                <!-- LEFT COLUMN: Gallery & Description -->
                <div class="product-detail-left">

                    <!-- Gallery -->
                    <div class="product-gallery-card">
                        <div class="product-gallery-main" id="mainGalleryImage">
                            <?php if (!empty($product['featured_image'])): ?>
                                <img src="uploads/products/featured/<?= htmlspecialchars($product['featured_image']) ?>" alt="<?= htmlspecialchars($product['title']) ?>" id="galleryMainImg">
                            <?php elseif (!empty($gallery[0])): ?>
                                <img src="uploads/products/gallery/<?= htmlspecialchars($gallery[0]) ?>" alt="<?= htmlspecialchars($product['title']) ?>" id="galleryMainImg">
                            <?php else: ?>
                                <div class="no-image">
                                    <svg viewBox="0 0 24 24" width="56" height="56" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round">
                                        <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                                        <circle cx="8.5" cy="8.5" r="1.5"></circle>
                                        <polyline points="21 15 16 10 5 21"></polyline>
                                    </svg>
                                    <span>Product Image</span>
                                </div>
                            <?php endif; ?>
                        </div>

                        <?php 
                        $allImages = [];
                        if (!empty($product['featured_image'])) {
                            $allImages[] = ['type' => 'featured', 'src' => $product['featured_image']];
                        }
                        foreach ($gallery as $gImg) {
                            $allImages[] = ['type' => 'gallery', 'src' => $gImg];
                        }
                        ?>
                        <?php if (count($allImages) > 1): ?>
                        <div class="product-gallery-nav" id="galleryNav">
                            <?php foreach ($allImages as $index => $img): ?>
                                <div class="product-gallery-thumb <?= $index === 0 ? 'active' : '' ?>" data-src="<?= htmlspecialchars($img['src']) ?>" data-type="<?= $img['type'] ?>">
                                    <img src="uploads/products/<?= $img['type'] ?>/<?= htmlspecialchars($img['src']) ?>" alt="Thumbnail <?= $index + 1 ?>">
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <?php endif; ?>
                    </div>

                    <!-- Description -->
                    <?php if (!empty($product['description'])): ?>
                    <div class="product-desc-card">
                        <div class="card-header">
                            <svg viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line></svg>
                            <h2>Product Details</h2>
                        </div>
                        <div class="card-body">
                            <?= $product['description'] ?>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- RIGHT COLUMN: Sidebar -->
                <div class="product-detail-sidebar">

                    <!-- Quick Info -->
                    <div class="product-info-card">
                        <div class="info-header">
                            <h3>
                                <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>
                                Product Info
                            </h3>
                        </div>
                        <div class="info-body">
                            <div class="product-meta-row">
                                <span class="meta-label">Product</span>
                                <span class="meta-value"><?= htmlspecialchars($product['title']) ?></span>
                            </div>
                            <div class="product-meta-row">
                                <span class="meta-label">Status</span>
                                <span class="meta-value" style="color:#16a34a;">Available</span>
                            </div>
                            <div class="product-meta-row">
                                <span class="meta-label">Enquiry</span>
                                <span class="meta-value">B2B Only</span>
                            </div>
                            <?php if ($product['enable_catalog'] && !empty($product['catalog_pdf'])): ?>
                            <div style="margin-top:16px;padding-top:16px;border-top:1px solid #f1f5f9;">
                                <a href="uploads/products/catalogs/<?= htmlspecialchars($product['catalog_pdf']) ?>" target="_blank" style="display:flex;align-items:center;gap:10px;padding:10px 14px;background:#fef2f2;color:#dc2626;text-decoration:none;font-size:14px;font-weight:600;transition:background 0.2s;" onmouseover="this.style.background='#fee2e2'" onmouseout="this.style.background='#fef2f2'">
                                    <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line></svg>
                                    Download Catalog (PDF)
                                </a>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Helpline Card -->
                    <div class="product-helpline-card">
                        <div class="helpline-icon">
                            <svg viewBox="0 0 24 24"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.69 12 19.79 19.79 0 0 1 1.61 3.4 2 2 0 0 1 3.6 1.21h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L7.91 8.91a16 16 0 0 0 6 6l.91-.91a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path></svg>
                        </div>
                        <h4>Need Help?</h4>
                        <p class="helpline-number">
                            <a href="tel:+918527113372">+91-85271-13372</a>
                        </p>
                        <p class="helpline-sub">Mon-Sat, 9:00 AM - 6:00 PM</p>
                        <div class="helpline-email">
                            <a href="mailto:sales@ytechpanels.com">sales@ytechpanels.com</a>
                        </div>
                    </div>

                    <!-- Enquiry Form -->
                    <div class="product-enquiry-card" id="enquirySection">
                        <div class="enquiry-header">
                            <svg viewBox="0 0 24 24"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>
                            <h3>Send Enquiry</h3>
                        </div>
                        <div class="enquiry-body">
                            <!-- Success Message -->
                            <div class="enquiry-success-msg" id="enquirySuccess">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
                                <h4>Enquiry Submitted!</h4>
                                <p>Thank you! Our team will get back to you within 24 hours.</p>
                            </div>

                            <!-- Enquiry Form -->
                            <form id="enquiryForm">
                                <input type="hidden" name="action" value="submit_enquiry">
                                <input type="hidden" name="product_id" value="<?= $productId ?>">

                                <div class="enquiry-form-group">
                                    <label>Your Name <span class="required">*</span></label>
                                    <input type="text" class="enquiry-form-control" name="name" placeholder="Enter your full name" required>
                                </div>

                                <div class="enquiry-form-group">
                                    <label>Email Address <span class="required">*</span></label>
                                    <input type="email" class="enquiry-form-control" name="email" id="enquiryEmail" placeholder="Enter your email address" required>
                                </div>

                                <div class="enquiry-form-group">
                                    <label>Phone Number</label>
                                    <input type="tel" class="enquiry-form-control" name="phone" placeholder="Enter your phone number">
                                </div>

                                <div class="enquiry-form-group">
                                    <label>Quantity Required</label>
                                    <input type="text" class="enquiry-form-control" name="quantity" placeholder="e.g. 2 units, 50 pieces">
                                </div>

                                <div class="enquiry-form-group">
                                    <label>Your Message <span class="required">*</span></label>
                                    <textarea class="enquiry-form-control" name="message" placeholder="Describe your requirements, specifications, or any questions..." required></textarea>
                                </div>

                                <!-- OTP Section -->
                                <button type="button" class="enquiry-btn enquiry-btn-secondary" id="sendOtpBtn" onclick="sendOtp()">
                                    <svg viewBox="0 0 24 24"><rect x="2" y="2" width="20" height="20" rx="4"></rect><path d="M22 6l-10 7L2 6"></path></svg>
                                    Verify Email via OTP
                                </button>

                                <div class="otp-section" id="otpSection">
                                    <p style="font-size:13px;color:#334155;margin:0 0 10px;">Enter the 6-digit OTP sent to your email:</p>
                                    <div class="otp-row">
                                        <input type="text" class="enquiry-form-control" id="otpInput" placeholder="000000" maxlength="6" inputmode="numeric" pattern="[0-9]*">
                                        <button type="button" class="enquiry-btn enquiry-btn-primary" id="verifyOtpBtn" onclick="verifyOtp()" style="width:auto;padding:10px 18px;white-space:nowrap;font-size:13px;">Verify</button>
                                    </div>
                                    <div class="otp-status" id="otpStatus"></div>
                                    <div class="otp-timer" id="otpTimer"></div>
                                </div>

                                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">

                                <button type="submit" class="enquiry-btn enquiry-btn-primary" id="submitEnquiryBtn" style="margin-top:12px;">
                                    <svg viewBox="0 0 24 24"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>
                                    Submit Enquiry
                                </button>
                            </form>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </section>

    <!-- Reviews Section -->
    <section class="product-reviews-section">
        <div class="container">
            <div class="reviews-card">
                <div class="reviews-header">
                    <h2>
                        <svg viewBox="0 0 24 24"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
                        Customer Reviews
                    </h2>
                    <?php if (!empty($reviews)): ?>
                    <div class="rating-summary">
                        <span class="avg-rating"><?= $avgRating ?></span>
                        <div class="stars">
                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                <svg viewBox="0 0 24 24" class="<?= $i <= round($avgRating) ? '' : 'empty' ?>">
                                    <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon>
                                </svg>
                            <?php endfor; ?>
                        </div>
                        <span class="total-label">(<?= count($reviews) ?>)</span>
                    </div>
                    <?php endif; ?>
                </div>

                <?php if (!empty($reviews)): ?>
                    <?php foreach ($reviews as $review): ?>
                        <div class="review-item">
                            <div class="review-author">
                                <div class="review-avatar"><?= strtoupper(substr($review['name'], 0, 1)) ?></div>
                                <div class="review-author-info">
                                    <p class="review-author-name"><?= htmlspecialchars($review['name']) ?></p>
                                    <p class="review-date"><?= date('d M Y', strtotime($review['created_at'])) ?></p>
                                </div>
                            </div>
                            <div class="review-stars">
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <svg viewBox="0 0 24 24" class="<?= $i <= $review['rating'] ? '' : 'empty' ?>">
                                        <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon>
                                    </svg>
                                <?php endfor; ?>
                            </div>
                            <p class="review-text"><?= htmlspecialchars($review['review']) ?></p>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="no-reviews">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
                        <p>No reviews yet. Be the first to review this product!</p>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Review Form -->
            <div class="review-form-card">
                <div class="form-header">
                    <h3>Write a Review</h3>
                </div>
                <div class="form-body">
                    <form id="reviewForm" onsubmit="submitReview(event)">
                        <input type="hidden" name="action" value="submit_review">
                        <input type="hidden" name="product_id" value="<?= $productId ?>">

                        <div class="review-form-group">
                            <label>Your Rating</label>
                            <div class="star-rating-input">
                                <input type="radio" name="rating" value="5" id="star5">
                                <label for="star5"><svg viewBox="0 0 24 24"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg></label>
                                <input type="radio" name="rating" value="4" id="star4">
                                <label for="star4"><svg viewBox="0 0 24 24"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg></label>
                                <input type="radio" name="rating" value="3" id="star3">
                                <label for="star3"><svg viewBox="0 0 24 24"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg></label>
                                <input type="radio" name="rating" value="2" id="star2">
                                <label for="star2"><svg viewBox="0 0 24 24"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg></label>
                                <input type="radio" name="rating" value="1" id="star1" checked>
                                <label for="star1"><svg viewBox="0 0 24 24"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg></label>
                            </div>
                        </div>

                        <div class="review-form-group">
                            <label>Your Name <span class="required" style="color:#dc2626;">*</span></label>
                            <input type="text" class="review-form-control" name="name" placeholder="Enter your name" required>
                        </div>

                        <div class="review-form-group">
                            <label>Your Email</label>
                            <input type="email" class="review-form-control" name="email" placeholder="Enter your email (optional)">
                        </div>

                        <div class="review-form-group">
                            <label>Your Review <span class="required" style="color:#dc2626;">*</span></label>
                            <textarea class="review-form-control" name="review" placeholder="Share your experience with this product..." required></textarea>
                        </div>

                        <div style="position:absolute;left:-9999px;opacity:0;height:0;overflow:hidden;">
                            <input type="text" name="website" tabindex="-1" autocomplete="off">
                        </div>

                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">

                        <button type="submit" class="review-submit-btn" id="reviewSubmitBtn">
                            Submit Review
                        </button>

                        <div class="review-success-msg" id="reviewSuccess"></div>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <!-- Related Products -->
    <?php if (!empty($relatedProducts)): ?>
    <section class="product-related-section">
        <div class="container">
            <div class="related-header">
                <h2>
                    <svg viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="3" width="7" height="7"></rect><rect x="14" y="14" width="7" height="7"></rect><rect x="3" y="14" width="7" height="7"></rect></svg>
                    Related Products
                </h2>
                <a href="products.php" class="view-all-link">
                    View All
                    <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
                </a>
            </div>
            <div class="related-grid">
                <?php foreach ($relatedProducts as $rel): ?>
                    <div class="related-card">
                        <a href="product-details.php?id=<?= $rel['id'] ?>" class="rel-image-wrap">
                            <?php if (!empty($rel['featured_image'])): ?>
                                <img src="uploads/products/featured/<?= htmlspecialchars($rel['featured_image']) ?>" alt="<?= htmlspecialchars($rel['title']) ?>" loading="lazy">
                            <?php else: ?>
                                <div style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;color:#cbd5e1;background:#f1f5f9;">
                                    <svg viewBox="0 0 24 24" width="36" height="36" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><polyline points="21 15 16 10 5 21"></polyline></svg>
                                </div>
                            <?php endif; ?>
                        </a>
                        <div class="rel-body">
                            <h3 class="rel-title"><?= htmlspecialchars($rel['title']) ?></h3>
                            <a href="product-details.php?id=<?= $rel['id'] ?>" class="rel-link">
                                Enquire Now
                                <svg viewBox="0 0 24 24"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
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
        const thumbs = document.querySelectorAll('.product-gallery-thumb');
        const mainImg = document.getElementById('galleryMainImg');
        
        thumbs.forEach(thumb => {
            thumb.addEventListener('click', function() {
                thumbs.forEach(t => t.classList.remove('active'));
                this.classList.add('active');
                if (mainImg) {
                    const type = this.dataset.type;
                    const src = this.dataset.src;
                    mainImg.src = 'uploads/products/' + type + '/' + src;
                }
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
                document.getElementById('otpStatus').className = 'otp-status success';
                document.getElementById('otpStatus').textContent = 'OTP sent to ' + email;
                document.getElementById('otpStatus').style.display = 'block';
                startOtpTimer();
                otpCooldown = true;
                setTimeout(() => { otpCooldown = false; }, 60000);
                btn.innerHTML = '<svg viewBox="0 0 24 24"><rect x="2" y="2" width="20" height="20" rx="4"></rect><path d="M22 6l-10 7L2 6"></path></svg> Resend OTP (60s)';
                
                // Auto-focus OTP input
                document.getElementById('otpInput').focus();
            } else {
                Swal.fire({icon: 'error', title: 'Failed', text: data.message});
                btn.disabled = false;
                btn.innerHTML = '<svg viewBox="0 0 24 24"><rect x="2" y="2" width="20" height="20" rx="4"></rect><path d="M22 6l-10 7L2 6"></path></svg> Verify Email via OTP';
            }
        })
        .catch(() => {
            Swal.fire({icon: 'error', title: 'Network Error', text: 'Please try again.'});
            btn.disabled = false;
            btn.innerHTML = '<svg viewBox="0 0 24 24"><rect x="2" y="2" width="20" height="20" rx="4"></rect><path d="M22 6l-10 7L2 6"></path></svg> Verify Email via OTP';
        });
    }

    function verifyOtp() {
        const otp = document.getElementById('otpInput').value.trim();
        const email = document.getElementById('enquiryEmail').value.trim();

        if (!otp || otp.length !== 6) {
            document.getElementById('otpStatus').className = 'otp-status error';
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
                document.getElementById('otpStatus').className = 'otp-status success';
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
                document.getElementById('otpStatus').className = 'otp-status error';
                document.getElementById('otpStatus').textContent = data.message;
                document.getElementById('otpStatus').style.display = 'block';
                btn.disabled = false;
                btn.textContent = 'Verify';
            }
        })
        .catch(() => {
            document.getElementById('otpStatus').className = 'otp-status error';
            document.getElementById('otpStatus').textContent = 'Network error. Please try again.';
            document.getElementById('otpStatus').style.display = 'block';
            btn.disabled = false;
            btn.textContent = 'Verify';
        });
    }

    function startOtpTimer() {
        if (otpTimerInterval) clearInterval(otpTimerInterval);
        let seconds = 300; // 5 minutes
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
                btn.innerHTML = '<svg viewBox="0 0 24 24"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg> Submit Enquiry';
            }
        })
        .catch(() => {
            Swal.fire({icon: 'error', title: 'Network Error', text: 'Please try again.'});
            btn.disabled = false;
            btn.innerHTML = '<svg viewBox="0 0 24 24"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg> Submit Enquiry';
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
                msgEl.className = 'review-success-msg active';
                document.getElementById('reviewForm').reset();
                // Re-check the first star
                document.getElementById('star1').checked = true;
            } else {
                msgEl.textContent = data.message;
                msgEl.className = 'review-success-msg active';
                msgEl.style.borderColor = '#fecaca';
                msgEl.style.background = '#fef2f2';
                msgEl.style.color = '#dc2626';
            }
            btn.disabled = false;
            btn.textContent = 'Submit Review';
            setTimeout(() => { msgEl.className = 'review-success-msg'; }, 6000);
        })
        .catch(() => {
            Swal.fire({icon: 'error', title: 'Network Error', text: 'Please try again.'});
            btn.disabled = false;
            btn.textContent = 'Submit Review';
        });
    }

    // Note: @keyframes spin is defined in product-details.css
    </script>

    <!-- SweetAlert2 for notifications -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

</body>
</html>

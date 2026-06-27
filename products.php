<?php
/**
 * YTech Panels — Products Listing Page
 * Displays all active products from the database in a professional B2B grid layout.
 */
require_once __DIR__ . '/config/db.php';

$db = getDB();

// Pagination
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$perPage = 12;
$offset = ($page - 1) * $perPage;

// Search
$search = trim($_GET['search'] ?? '');
$searchCondition = '';
$searchParams = [];

if (!empty($search)) {
    $searchCondition = "AND (title LIKE :search OR short_description LIKE :search_desc)";
    $searchParams[':search'] = '%' . $search . '%';
    $searchParams[':search_desc'] = '%' . $search . '%';
}

// Sort
$sort = $_GET['sort'] ?? 'newest';
$orderClause = match($sort) {
    'oldest' => 'created_at ASC',
    'title_asc' => 'title ASC',
    'title_desc' => 'title DESC',
    default => 'created_at DESC',
};

// Count total
$countStmt = $db->prepare("SELECT COUNT(*) FROM products WHERE status = 1 $searchCondition");
$countStmt->execute($searchParams);
$totalProducts = (int)$countStmt->fetchColumn();
$totalPages = max(1, ceil($totalProducts / $perPage));

// Fetch products
$stmt = $db->prepare("
    SELECT id, title, short_description, featured_image, created_at 
    FROM products 
    WHERE status = 1 
    $searchCondition 
    ORDER BY sort_order ASC, $orderClause 
    LIMIT $perPage OFFSET $offset
");
$stmt->execute($searchParams);
$products = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Our Products - YTech Panels | Premium Electrical Control Panels</title>
    <meta name="description" content="Explore YTech Panels' comprehensive range of premium electrical control panels including PCC, MCC, APFC, automation panels, distribution boards and more. B2B enquiries welcome.">
    <meta name="keywords" content="electrical control panels, PCC panels, MCC panels, APFC panels, distribution boards, YTech Panels, B2B">
    <link rel="icon" href="assets/logo.png" type="image/png">
    <link rel="stylesheet" href="assets/css/header.css">
    <link rel="stylesheet" href="assets/css/footer.css">
    <link rel="stylesheet" href="assets/css/product-listing.css">
</head>
<body>

    <!-- Header -->
    <header class="main-header" id="site-header">
        <?php include 'includes/header.php'; ?>
    </header>

    <!-- Page Banner -->
    <section class="product-listing-banner">
        <div class="container">
            <div class="product-listing-breadcrumb">
                <a href="index.php">Home</a>
                <span class="sep">/</span>
                <span class="current">Products</span>
            </div>
            <h1>Our Product Range</h1>
            <p>Explore our comprehensive range of premium electrical control panels, designed and manufactured for industrial excellence.</p>
        </div>
    </section>

    <!-- Search & Filter Toolbar -->
    <section class="product-listing-toolbar">
        <div class="container">
            <div class="toolbar-inner">
                <div class="toolbar-left">
                    <div class="product-search-wrapper">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="11" cy="11" r="8"></circle>
                            <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                        </svg>
                        <input type="text" class="product-search-input" id="productSearch" placeholder="Search products..." value="<?= htmlspecialchars($search) ?>">
                    </div>
                </div>
                <div class="toolbar-right">
                    <span class="product-count-badge"><?= $totalProducts ?> Products</span>
                    <select class="sort-select" id="sortSelect">
                        <option value="newest" <?= $sort === 'newest' ? 'selected' : '' ?>>Newest First</option>
                        <option value="oldest" <?= $sort === 'oldest' ? 'selected' : '' ?>>Oldest First</option>
                        <option value="title_asc" <?= $sort === 'title_asc' ? 'selected' : '' ?>>Title A-Z</option>
                        <option value="title_desc" <?= $sort === 'title_desc' ? 'selected' : '' ?>>Title Z-A</option>
                    </select>
                </div>
            </div>
        </div>
    </section>

    <!-- Products Grid -->
    <section class="product-listing-section">
        <div class="container">
            <?php if (!empty($products)): ?>
                <div class="product-listing-grid" id="productGrid">
                    <?php foreach ($products as $product): ?>
                        <div class="product-listing-card">
                            <a href="product-details.php?id=<?= $product['id'] ?>" class="card-image-wrap">
                                <?php if (!empty($product['featured_image'])): ?>
                                    <img src="uploads/products/featured/<?= htmlspecialchars($product['featured_image']) ?>" alt="<?= htmlspecialchars($product['title']) ?>" loading="lazy">
                                <?php else: ?>
                                    <div class="no-image">
                                        <svg viewBox="0 0 24 24" width="48" height="48" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                            <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                                            <circle cx="8.5" cy="8.5" r="1.5"></circle>
                                            <polyline points="21 15 16 10 5 21"></polyline>
                                        </svg>
                                    </div>
                                <?php endif; ?>
                            </a>
                            <div class="card-body">
                                <h3 class="card-title">
                                    <a href="product-details.php?id=<?= $product['id'] ?>" style="color:inherit;text-decoration:none;">
                                        <?= htmlspecialchars($product['title']) ?>
                                    </a>
                                </h3>
                                <p class="card-excerpt">
                                    <?= !empty($product['short_description']) ? htmlspecialchars(substr($product['short_description'], 0, 150)) : 'Premium quality electrical control panel designed for industrial applications.' ?>
                                </p>
                            </div>
                            <div class="card-footer">
                                <span class="card-date"><?= date('M Y', strtotime($product['created_at'])) ?></span>
                                <a href="product-details.php?id=<?= $product['id'] ?>" class="card-enquire-btn">
                                    Enquire Now
                                    <svg viewBox="0 0 24 24"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Pagination -->
                <?php if ($totalPages > 1): ?>
                    <div class="product-listing-pagination">
                        <?php
                        $buildUrl = function($p) use ($search, $sort) {
                            $params = ['page' => $p];
                            if (!empty($search)) $params['search'] = $search;
                            if ($sort !== 'newest') $params['sort'] = $sort;
                            return '?' . http_build_query($params);
                        };
                        ?>
                        <a href="<?= $buildUrl($page - 1) ?>" class="<?= $page <= 1 ? 'disabled' : '' ?>">
                            <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"></polyline></svg>
                        </a>
                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                            <a href="<?= $buildUrl($i) ?>" class="<?= $i === $page ? 'active' : '' ?>"><?= $i ?></a>
                        <?php endfor; ?>
                        <a href="<?= $buildUrl($page + 1) ?>" class="<?= $page >= $totalPages ? 'disabled' : '' ?>">
                            <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"></polyline></svg>
                        </a>
                    </div>
                <?php endif; ?>

            <?php else: ?>
                <div class="product-listing-empty">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                        <circle cx="8.5" cy="8.5" r="1.5"></circle>
                        <polyline points="21 15 16 10 5 21"></polyline>
                    </svg>
                    <h3>No Products Found</h3>
                    <p><?= !empty($search) ? 'No products match your search "' . htmlspecialchars($search) . '".' : 'Products are being added. Please check back soon.' ?></p>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- Footer -->
    <?php include 'includes/footer.php'; ?>

    <script>
    // Live search with debounce
    let searchTimeout;
    const searchInput = document.getElementById('productSearch');
    const sortSelect = document.getElementById('sortSelect');

    function navigateWithFilters() {
        const search = searchInput.value.trim();
        const sort = sortSelect.value;
        let params = [];
        if (search) params.push('search=' + encodeURIComponent(search));
        if (sort !== 'newest') params.push('sort=' + encodeURIComponent(sort));
        const query = params.length ? '?' + params.join('&') : '';
        window.location.href = window.location.pathname + query;
    }

    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(navigateWithFilters, 500);
    });

    sortSelect.addEventListener('change', navigateWithFilters);

    // Enter key triggers search immediately
    searchInput.addEventListener('keydown', function(e) {
        if (e.key === 'Enter') {
            clearTimeout(searchTimeout);
            navigateWithFilters();
        }
    });
    </script>

</body>
</html>

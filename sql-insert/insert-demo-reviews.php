<?php
/**
 * YTech Panels — Insert Demo Indian Customer Reviews
 * 
 * Inserts realistic Indian customer reviews for B2B electrical panel products.
 * 
 * Usage:
 *   Web:   Visit this page in browser (SETUP_TOKEN check applies)
 *          https://ytechpanel.com/sql-insert/insert-demo-reviews.php?token=YOUR_TOKEN
 *
 * Security: Requires SETUP_TOKEN from .env when admin_users exist.
 */

require_once dirname(__DIR__) . '/config/db.php';

$isCli = (php_sapi_name() === 'cli');

if (!$isCli) {
    session_start();
    try {
        $db = getDB();
        $adminCheck = $db->query("SELECT COUNT(*) FROM admin_users")->fetchColumn();
        if ($adminCheck > 0) {
            $expectedToken = $_ENV['SETUP_TOKEN'] ?? '';
            $providedToken = $_GET['token'] ?? '';
            if (empty($expectedToken) || $providedToken !== $expectedToken) {
                http_response_code(403);
                echo '<h2>🔒 Access Denied</h2><p>Valid SETUP_TOKEN required.</p>';
                exit();
            }
        }
    } catch (Exception $e) {}
}

function out($msg) {
    global $isCli;
    echo $msg . ($isCli ? "\n" : '<br>');
}

if (!$isCli) {
    echo '<!DOCTYPE html><html><head><title>Insert Demo Reviews</title>';
    echo '<style>body{font-family:system-ui,sans-serif;padding:40px;background:#f8fafc;max-width:800px;margin:0 auto;}';
    echo 'h2{color:#1e293b;} .ok{color:#16a34a;font-weight:600;} .err{color:#dc2626;} .info{color:#64748b;font-size:14px;}';
    echo 'code{background:#f1f5f9;padding:2px 6px;border-radius:3px;font-size:13px;}</style></head><body>';
    echo '<h2>📝 Insert Demo Indian Reviews</h2><hr>';
}

try {
    $db = getDB();

    // Fetch all active products
    $products = $db->query("SELECT id, title FROM products WHERE status = 1")->fetchAll();

    if (empty($products)) {
        out('<span class="err">❌ No active products found. Add products first.</span>');
        exit();
    }

    out('<span class="info">Found ' . count($products) . ' product(s). Inserting reviews...</span>');
    out('');

    // ─── Realistic Indian customer reviews ───
    $reviewTemplates = [
        // 5-star reviews
        [
            'rating' => 5,
            'names' => ['Rajesh Sharma', 'Amit Verma', 'Suresh Patel', 'Vikram Singh', 'Deepak Agarwal'],
            'reviews' => [
                'Excellent quality panel! We installed this at our factory in Noida and the performance has been outstanding. The busbar rating and enclosure protection met our exact requirements. Highly recommended for industrial applications.',
                'We have been using YTech panels for over 3 years now across three facilities. The build quality, on-time delivery, and after-sales support are top-notch. Recently ordered 4 more panels for our expansion project.',
                'Outstanding product quality and service. The YTech team helped us customize the panel as per our SLD and the commissioning was smooth. The IP55 enclosure is perfect for our dusty environment.',
                'Best supplier for electrical panels in India. Competitive pricing, superior quality, and excellent project management. Our team in Pune is fully satisfied with the installation and performance.',
                'Very satisfied with the purchase. The panel was delivered on schedule and the documentation was comprehensive. The arc fault protection feature gives us peace of mind for our critical operations.',
            ],
        ],
        // 4-star reviews
        [
            'rating' => 4,
            'names' => ['Priya Mehta', 'Anil Kumar', 'Sandeep Joshi', 'Rohan Deshmukh', 'Neha Gupta'],
            'reviews' => [
                'Good quality panel overall. The construction is robust and delivery was on time. Only minor suggestion — the cable entry arrangement could be more flexible. But the performance has been reliable so far.',
                'We ordered this panel for our new warehouse in Bengaluru. Quality is very good and the team was responsive during the design phase. Would have liked more detailed wiring diagrams, but overall satisfied.',
                'Solid build quality and meets all the specifications we required. The price was competitive compared to other vendors we evaluated. Delivery took slightly longer than initially quoted, but the product quality made up for it.',
                'Good experience with YTech Panels. The panel was customized as per our requirements and the testing was thorough. Installation support was helpful. A small issue with the door lock was resolved promptly.',
                'Satisfied with the product and service. The panel performs well and the customer support team is knowledgeable. Minor delay in delivery but the quality justified the wait. Would order again.',
            ],
        ],
        // 3-star reviews
        [
            'rating' => 3,
            'names' => ['Manoj Tiwari', 'Kavita Reddy'],
            'reviews' => [
                'The panel is functional and meets basic requirements. Build quality is acceptable for the price point. However, the documentation could be more detailed and the terminal markings were not as clear as expected. Service team was helpful though.',
                'Decent product for the price. The panel was delivered and installed without major issues. Communication during the manufacturing phase could have been better. The product itself is working fine for our medium-load application.',
            ],
        ],
        // 2-star review
        [
            'rating' => 2,
            'names' => ['Ravi Gupta'],
            'reviews' => [
                'Average experience. The panel quality is okay but we faced some issues with the busbar alignment during installation. The support team did help but it took longer than expected. Could improve on quality control and packaging for transport.',
            ],
        ],
        // 1-star review (rare, but realistic)
        [
            'rating' => 1,
            'names' => ['Sunil Yadav'],
            'reviews' => [
                'Disappointed with this purchase. The panel arrived with some cosmetic damage to the powder coating and one of the cable glands was loose. The team did send a service engineer but it took 10 days. For the price paid, I expected better quality control and faster response.',
            ],
        ],
    ];

    $insertStmt = $db->prepare(
        "INSERT INTO product_reviews (product_id, name, email, rating, review, status, created_at)
         VALUES (?, ?, ?, ?, ?, 1, ?)"
    );

    $totalInserted = 0;
    $targetProductIds = array_column($products, 'id');

    // Distribute reviews across all products
    foreach ($targetProductIds as $pid) {
        $productTitle = '';
        foreach ($products as $p) {
            if ($p['id'] == $pid) {
                $productTitle = $p['title'];
                break;
            }
        }

        out("<strong>{$productTitle}</strong> (ID: {$pid}):");

        foreach ($reviewTemplates as $group) {
            foreach ($group['names'] as $idx => $name) {
                // Alternate which review text to use
                $reviewIdx = $idx % count($group['reviews']);
                $reviewText = $group['reviews'][$reviewIdx];
                
                // Generate email
                $email = strtolower(str_replace(' ', '.', $name)) . '@gmail.com';
                
                // Random date within last 6 months
                $daysAgo = rand(5, 180);
                $createdAt = date('Y-m-d H:i:s', strtotime("-{$daysAgo} days"));

                $insertStmt->execute([
                    $pid,
                    $name,
                    $email,
                    $group['rating'],
                    $reviewText,
                    $createdAt,
                ]);

                $totalInserted++;
                $stars = str_repeat('★', $group['rating']) . str_repeat('☆', 5 - $group['rating']);
                out('  ✅ ' . $name . ' — ' . $stars . ' (' . $group['rating'] . '/5)');
            }
        }
        out('');
    }

    out('<hr>');
    out('<span class="ok">✅ Done! Inserted ' . $totalInserted . ' demo reviews across ' . count($targetProductIds) . ' product(s).</span>');
    out('<span class="info">All reviews are set to <strong>status = 1 (approved)</strong> and will appear immediately on the product details page.</span>');

} catch (PDOException $e) {
    out('<span class="err">❌ Error: ' . $e->getMessage() . '</span>');
}

if (!$isCli) echo '</body></html>';

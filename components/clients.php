<?php
/**
 * YTech Panels - Clients Grid Component
 * Displays a clean grid of valued corporate and government clients from the database.
 */

// Load DB connection if not already loaded
if (!function_exists('getDB')) {
    require_once dirname(__DIR__) . '/config/db.php';
}

try {
    $db = getDB();
    $stmt = $db->prepare("SELECT id, name, logo, website, description FROM clients WHERE status = 1 ORDER BY sort_order ASC, id ASC");
    $stmt->execute();
    $clients = $stmt->fetchAll();
} catch (Exception $e) {
    $clients = [];
}
?>
<section class="clients-section" id="our-clients">
    <div class="container">

        <!-- Section Header -->
        <div class="clients-header">
            <span class="clients-tagline">Trusted Partners</span>
            <h2 class="clients-title">Our Valued Clients</h2>
            <div class="clients-underline"></div>
        </div>

        <!-- Clients Grid -->
        <?php if (!empty($clients)): ?>
            <div class="clients-grid">
                <?php foreach ($clients as $client): ?>
                    <div class="client-logo-box" <?php if (!empty($client['description'])): ?>title="<?= htmlspecialchars($client['description']) ?>"<?php endif; ?>>
                        <div class="client-logo-content">
                            <?php
                            // Determine logo source: data URI (SVG from DB) or uploaded file
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
                                <?php if (strpos($logoSrc, 'data:') === 0): ?>
                                    <!-- Data URI (SVG embedded in DB) -->
                                    <img src="<?= $logoSrc ?>" alt="<?= htmlspecialchars($client['name']) ?>" class="client-logo-img">
                                <?php else: ?>
                                    <!-- Uploaded image file -->
                                    <img src="<?= $logoSrc ?>" alt="<?= htmlspecialchars($client['name']) ?>" class="client-logo-img">
                                <?php endif; ?>
                            <?php else: ?>
                                <!-- Fallback: show client name as text -->
                                <div class="client-text-fallback">
                                    <?= htmlspecialchars($client['name']) ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <!-- Fallback: Show placeholder message when no clients are in the database -->
            <div class="clients-grid">
                <div class="client-logo-box"><div class="client-logo-content"><div class="client-text-fallback">No clients yet</div></div></div>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php
/**
 * YTech Panels - Product Grid Component
 * Displays the product categories with clean white background card boxes,
 * sharp (non-rounded) corners, solid borders, and structured layout matching the reference.
 */

$products = [
    [
        'title' => 'Power Distribution Board (PDB)',
        'image' => 'assets/images/products/power-distribution-board.png',
        'alt' => 'Power Distribution Board (PDB) - YTech Panels'
    ],
    [
        'title' => 'Power Distribution with APFC',
        'image' => 'assets/images/products/power-distribution-with-apfc.png',
        'alt' => 'Power Distribution with APFC - YTech Panels'
    ],
    [
        'title' => 'Power Control Centre (PCC)',
        'image' => 'assets/images/products/power-control-center-pcc.png',
        'alt' => 'Power Control Centre (PCC) - YTech Panels'
    ],
    [
        'title' => 'Motor Control Centre (MCC)',
        'image' => 'assets/images/products/motor-control-center-mcc.png',
        'alt' => 'Motor Control Centre (MCC) - YTech Panels'
    ],
    [
        'title' => 'High Tension Panel (HT Panel)',
        'image' => 'assets/images/products/high-tension-panel-ht-panel.png',
        'alt' => 'High Tension Panel (HT Panel) - YTech Panels'
    ],
    [
        'title' => 'Low Tension Panel (LT Panel)',
        'image' => 'assets/images/products/low-tension-panel-lt-panel.png',
        'alt' => 'Low Tension Panel (LT Panel) - YTech Panels'
    ],
    [
        'title' => 'Automatic Power Factor (APFC)',
        'image' => 'assets/images/products/automatic-power-factor-apfc.png',
        'alt' => 'Automatic Power Factor (APFC) - YTech Panels'
    ],
    [
        'title' => 'AC Drive Panel',
        'image' => 'assets/images/products/ac-drive-panel.png',
        'alt' => 'AC Drive Panel - YTech Panels'
    ],
    [
        'title' => 'Busbar Trunking Panel',
        'image' => 'assets/images/products/busbar-trunking-panel.png',
        'alt' => 'Busbar Trunking Panel - YTech Panels'
    ],
    [
        'title' => 'Control Desk',
        'image' => 'assets/images/products/control-desk.png',
        'alt' => 'Control Desk - YTech Panels'
    ],
    [
        'title' => 'Feeder Pillar Panel',
        'image' => 'assets/images/products/feeder-piller-panel.png',
        'alt' => 'Feeder Pillar Panel - YTech Panels'
    ],
    [
        'title' => 'Fire Panel',
        'image' => 'assets/images/products/fire-panel.png',
        'alt' => 'Fire Panel - YTech Panels'
    ],
    [
        'title' => 'PCC Extension Panel',
        'image' => 'assets/images/products/pcc-extension-panel.png',
        'alt' => 'PCC Extension Panel - YTech Panels'
    ],
    [
        'title' => 'PLC Panel',
        'image' => 'assets/images/products/plc-panel.png',
        'alt' => 'PLC Panel - YTech Panels'
    ],
    [
        'title' => 'Sub Distribution Panel (Type 1)',
        'image' => 'assets/images/products/sub-distribution-panel.png',
        'alt' => 'Sub Distribution Panel (Type 1) - YTech Panels'
    ],
    [
        'title' => 'Sub Distribution Panel (Type 2)',
        'image' => 'assets/images/products/sub-distribution-panel-2.png',
        'alt' => 'Sub Distribution Panel (Type 2) - YTech Panels'
    ]
];
?>
<section class="products-section" id="products-showcase">
    <div class="container">
        
        <!-- Section Header Bar -->
        <div class="products-header-bar">
            <h2 class="products-header-title">
                Our Products <span class="subtitle">(Design Verified Low-Voltage Power Panels)</span>
            </h2>
            <a href="#contact" class="products-view-all-btn">
                <span>Enquire Now</span>
                <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="5" y1="12" x2="19" y2="12"></line>
                    <polyline points="12 5 19 12 12 19"></polyline>
                </svg>
            </a>
        </div>

        <!-- Products Grid -->
        <div class="products-grid">
            <?php foreach ($products as $product): ?>
                <div class="product-card">
                    <div class="product-image-container">
                        <img src="<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['alt']); ?>" class="product-img" loading="lazy">
                    </div>
                    <div class="product-details">
                        <h3 class="product-title"><?php echo htmlspecialchars($product['title']); ?></h3>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

    </div>
</section>

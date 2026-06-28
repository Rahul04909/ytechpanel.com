<?php
// Fetch active products for dynamic navigation dropdown
if (!isset($db) || !$db) {
    try {
        require_once __DIR__ . '/../config/db.php';
        $db = getDB();
    } catch (\Exception $e) {
        $db = null;
    }
}
$navProducts = [];
if ($db) {
    $stmt = $db->prepare("SELECT id, title FROM products WHERE status = 1 ORDER BY sort_order ASC, id DESC LIMIT 15");
    $stmt->execute();
    $navProducts = $stmt->fetchAll();
}
?>

<!-- ==========================================
     TOP BAR
     ========================================== -->
<div class="top-bar">
    <div class="container">
        <div class="top-bar-left">
            India's #1 Electrical Control Panels Manufacturers and Exporters.
        </div>
        <div class="top-bar-right">

            <a href="#service-amc" class="top-bar-link">
                <!-- Wrench/Spanner Icon -->
                <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path d="M22.7 19l-9.1-9.1c.9-2.3.4-5-1.5-6.9-2-2-5-2.4-7.4-1.3L9 6 6 9 1.6 4.3C.5 6.7.9 9.8 2.9 11.8c1.9 1.9 4.6 2.4 6.9 1.5l9.1 9.1c.4.4 1 .4 1.4 0l2.3-2.3c.5-.4.5-1.1.1-1.4z"/>
                </svg>
                Service & AMC
            </a>
            <a href="#get-quote" class="top-bar-link top-bar-cta">
                <!-- Quote Document Icon -->
                <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path d="M14 2H6c-1.1 0-1.99.9-1.99 2L4 20c0 1.1.89 2 1.99 2H18c1.1 0 2-.9 2-2V8l-6-6zm2 16H8v-2h8v2zm0-4H8v-2h8v2zm-3-5V3.5L18.5 9H13z"/>
                </svg>
                Get A Quote
            </a>
        </div>
    </div>
</div>

<!-- ==========================================
     MIDDLE BAR (LOGO & CONTACT INFO)
     ========================================== -->
<div class="middle-bar">
    <div class="container">
        <!-- Logo Branding Left -->
        <div class="logo-container">
            <a href="index.php" class="brand-logo-main">
                <img src="assets/logo.png" alt="YTech Panels Logo" class="header-logo-img">
            </a>
            <!-- GST Identification Number -->
            <div class="header-gst-badge">
                <span class="gst-label">GSTIN</span>
                <span class="gst-val">06DKQPM5749K1ZC</span>
            </div>
        </div>

        <!-- Contact details Right -->
        <div class="contact-info-wrapper">
            <!-- Phone Block -->
            <div class="info-block">
                <div class="info-icon">
                    <!-- Double device / outline phones icon -->
                    <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <rect x="5" y="2" width="10" height="18" rx="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <line x1="10" y1="17" x2="10" y2="17" stroke-linecap="round"/>
                        <rect x="11" y="7" width="8" height="14" rx="1.5" fill="#ffffff" stroke-linecap="round" stroke-linejoin="round"/>
                        <line x1="15" y1="18" x2="15" y2="18" stroke-linecap="round"/>
                    </svg>
                </div>
                <div class="info-details">
                    <span class="info-label">Phone</span>
                    <span class="info-value">
                        <a href="tel:+918527113372">+91-85271-13372</a>
                    </span>
                </div>
            </div>

            <!-- Email Block -->
            <div class="info-block">
                <div class="info-icon">
                    <!-- Envelope Icon -->
                    <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z" stroke-linecap="round" stroke-linejoin="round"/>
                        <polyline points="22,6 12,13 2,6" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>
                <div class="info-details">
                    <span class="info-label">Email</span>
                    <span class="info-value">
                        <a href="mailto:sales@ytechpanels.com">sales@ytechpanels.com</a>
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ==========================================
     BOTTOM NAVIGATION BAR
     ========================================== -->
<div class="nav-bar">
    <div class="container">
        <!-- Desktop Nav items -->
        <ul class="desktop-menu">
            <li class="menu-item active">
                <a href="index.php" class="menu-link">Home</a>
            </li>
            <li class="menu-item">
                <a href="about.php" class="menu-link">About Us</a>
            </li>
            
            <!-- Products with Dropdown (dynamic from DB) -->
            <li class="menu-item">
                <a href="products.php" class="menu-link">
                    Products
                    <?php if (!empty($navProducts)): ?>
                    <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><polyline points="6 9 12 15 18 9"/></svg>
                    <?php endif; ?>
                </a>
                <?php if (!empty($navProducts)): ?>
                <ul class="dropdown-menu im-prod-dropdown">
                    <?php foreach ($navProducts as $np): ?>
                    <li><a href="product-details.php?id=<?= (int)$np['id'] ?>" class="dropdown-link"><?= htmlspecialchars($np['title']) ?></a></li>
                    <?php endforeach; ?>
                    <li class="im-drop-foot"><a href="products.php" class="dropdown-link">View All Products →</a></li>
                </ul>
                <?php endif; ?>
            </li>
            
            <li class="menu-item">
                <a href="#manufacturing" class="menu-link">Manufacturing</a>
            </li>
            <li class="menu-item">
                <a href="#quality" class="menu-link">Quality</a>
            </li>
            <li class="menu-item">
                <a href="clients.php" class="menu-link">Our Clients</a>
            </li>
            
            <!-- Enquiry with Dropdown -->
            <li class="menu-item">
                <a href="#enquiry" class="menu-link">
                    Enquiry
                    <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><polyline points="6 9 12 15 18 9"/></svg>
                </a>
                <ul class="dropdown-menu">
                    <li><a href="#send-enquiry" class="dropdown-link">Submit Enquiry</a></li>
                    <li><a href="#request-callback" class="dropdown-link">Request Call Back</a></li>
                    <li><a href="#custom-quote" class="dropdown-link">Custom Quote</a></li>
                </ul>
            </li>
            
            <li class="menu-item">
                <a href="contact.php" class="menu-link">Contact Us</a>
            </li>
        </ul>

        <!-- Mobile Trigger Button (Visible only on Mobile/Tablet) -->
        <button class="mobile-trigger" id="mobile-menu-open" aria-label="Open Menu">
            <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <line x1="3" y1="12" x2="21" y2="12"/>
                <line x1="3" y1="6" x2="21" y2="6"/>
                <line x1="3" y1="18" x2="21" y2="18"/>
            </svg>
        </button>


    </div>
</div>

<!-- ==========================================
     MOBILE RESPONSIVE DRAW SYSTEM
     ========================================== -->
<!-- Drawer Backdrop -->
<div class="mobile-menu-backdrop" id="mobile-menu-backdrop"></div>

<!-- Drawer Menu -->
<div class="mobile-menu-drawer" id="mobile-menu-drawer">
    <div class="drawer-header">
        <div class="brand-logo-main">
            <div class="brand-icon-box" style="width:40px; height:40px;">
                <!-- Detailed Control Panel SVG Icon -->
                <svg viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <!-- Panel Enclosure -->
                    <rect x="3" y="3" width="42" height="42" rx="5" fill="#1e293b" stroke="#003a8c" stroke-width="2.5"/>
                    <!-- Outer Bezel -->
                    <rect x="7" y="7" width="34" height="34" rx="2.5" fill="#334155" stroke="#475569" stroke-width="1.5"/>
                    <!-- Door Hinge line on the left -->
                    <line x1="10" y1="10" x2="10" y2="38" stroke="#1e293b" stroke-width="2"/>
                    <!-- Door lock on the right -->
                    <circle cx="37" cy="24" r="2" fill="#cbd5e0" stroke="#475569" stroke-width="1"/>
                    <!-- Status lights (3 phase) -->
                    <circle cx="15" cy="14" r="2.5" fill="#ef4444"/> <!-- Red -->
                    <circle cx="24" cy="14" r="2.5" fill="#eab308"/> <!-- Yellow -->
                    <circle cx="33" cy="14" r="2.5" fill="#3b82f6"/> <!-- Blue -->
                    <!-- Digital Screen -->
                    <rect x="13" y="20" width="22" height="10" rx="1" fill="#0f172a" stroke="#475569" stroke-width="1"/>
                    <!-- Waveform or digital text -->
                    <path d="M16 25 h3 l2 -3 l2 5 l2 -4 l2 2 h5" stroke="#10b981" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    <!-- Push Buttons / Dial -->
                    <!-- Start Button (Green) -->
                    <circle cx="17" cy="34" r="2.5" fill="#10b981"/>
                    <!-- Stop Button (Red) -->
                    <circle cx="24" cy="34" r="2.5" fill="#ef4444"/>
                    <!-- Emergency Selector Dial -->
                    <circle cx="31" cy="34" r="2.5" fill="#fbbf24"/>
                </svg>
            </div>
            <div class="brand-logo-text">
                <span class="logo-title" style="font-size:16px;">YTECH PANELS</span>
                <span class="logo-subtitle" style="font-size:8px;">ELECTRIFYING THE WORLD</span>
            </div>
        </div>
        <button class="drawer-close" id="mobile-menu-close" aria-label="Close Menu">
            <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <line x1="18" y1="6" x2="6" y2="18"/>
                <line x1="6" y1="6" x2="18" y2="18"/>
            </svg>
        </button>
    </div>

    <!-- Drawer Navigation Links -->
    <ul class="drawer-menu-list">
        <li class="drawer-menu-item active">
            <a href="index.php" class="drawer-menu-link">Home</a>
        </li>
        <li class="drawer-menu-item">
            <a href="about.php" class="drawer-menu-link">About Us</a>
        </li>
        
        <!-- Products Accordion (dynamic from DB) -->
        <li class="drawer-menu-item has-accordion">
            <a href="products.php" class="drawer-menu-link accordion-toggle">
                Products
                <?php if (!empty($navProducts)): ?>
                <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><polyline points="6 9 12 15 18 9"/></svg>
                <?php endif; ?>
            </a>
            <?php if (!empty($navProducts)): ?>
            <ul class="drawer-submenu">
                <?php foreach ($navProducts as $np): ?>
                <li><a href="product-details.php?id=<?= (int)$np['id'] ?>" class="drawer-submenu-link"><?= htmlspecialchars($np['title']) ?></a></li>
                <?php endforeach; ?>
                <li><a href="products.php" class="drawer-submenu-link" style="font-weight:600;color:var(--primary-color);">View All Products →</a></li>
            </ul>
            <?php endif; ?>
        </li>
        
        <li class="drawer-menu-item">
            <a href="#manufacturing" class="drawer-menu-link">Manufacturing</a>
        </li>
        <li class="drawer-menu-item">
            <a href="#quality" class="drawer-menu-link">Quality</a>
        </li>
        <li class="drawer-menu-item">
            <a href="clients.php" class="drawer-menu-link">Our Clients</a>
        </li>
        
        <!-- Enquiry Accordion -->
        <li class="drawer-menu-item has-accordion">
            <a href="#" class="drawer-menu-link accordion-toggle">
                Enquiry
                <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><polyline points="6 9 12 15 18 9"/></svg>
            </a>
            <ul class="drawer-submenu">
                <li><a href="#send-enquiry" class="drawer-submenu-link">Submit Enquiry</a></li>
                <li><a href="#request-callback" class="drawer-submenu-link">Request Call Back</a></li>
                <li><a href="#custom-quote" class="drawer-submenu-link">Custom Quote</a></li>
            </ul>
        </li>
        
        <li class="drawer-menu-item">
            <a href="contact.php" class="drawer-menu-link">Contact Us</a>
        </li>
    </ul>

    <!-- Drawer Contact details / CTA -->
    <div class="drawer-footer">
        <div class="info-block">
            <div class="info-icon">
                <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <rect x="5" y="2" width="10" height="18" rx="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <line x1="10" y1="17" x2="10" y2="17"/>
                    <rect x="11" y="7" width="8" height="14" rx="1.5" fill="#ffffff" stroke-linecap="round" stroke-linejoin="round"/>
                    <line x1="15" y1="18" x2="15" y2="18"/>
                </svg>
            </div>
            <div class="info-details">
                <span class="info-label">Phone</span>
                <span class="info-value">
                    <a href="tel:+918527113372" style="display:block;">+91-85271-13372</a>
                </span>
            </div>
        </div>
        
        <div class="info-block">
            <div class="info-icon">
                <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z" stroke-linecap="round" stroke-linejoin="round"/>
                    <polyline points="22,6 12,13 2,6" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
            <div class="info-details">
                <span class="info-label">Email</span>
                <span class="info-value">
                    <a href="mailto:sales@ytechpanels.com">sales@ytechpanels.com</a>
                </span>
            </div>
        </div>

        <a href="#get-quote" class="drawer-footer-cta">
            <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" style="width: 14px; height: 14px; fill: currentColor;">
                <path d="M14 2H6c-1.1 0-1.99.9-1.99 2L4 20c0 1.1.89 2 1.99 2H18c1.1 0 2-.9 2-2V8l-6-6zm2 16H8v-2h8v2zm0-4H8v-2h8v2zm-3-5V3.5L18.5 9H13z"/>
            </svg>
            Get A Quote
        </a>
    </div>
</div>

<!-- ==========================================
     MOBILE NAVIGATION JAVASCRIPT
     ========================================== -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const burgerBtn = document.getElementById('mobile-menu-open');
    const closeBtn = document.getElementById('mobile-menu-close');
    const backdrop = document.getElementById('mobile-menu-backdrop');
    const drawer = document.getElementById('mobile-menu-drawer');
    
    // Toggle mobile drawer
    function toggleDrawer(open) {
        if (open) {
            drawer.classList.add('active');
            backdrop.classList.add('active');
            document.body.style.overflow = 'hidden'; // Disable background scroll
        } else {
            drawer.classList.remove('active');
            backdrop.classList.remove('active');
            document.body.style.overflow = ''; // Enable background scroll
        }
    }
    
    burgerBtn.addEventListener('click', () => toggleDrawer(true));
    closeBtn.addEventListener('click', () => toggleDrawer(false));
    backdrop.addEventListener('click', () => toggleDrawer(false));
    
    // Accordion Toggle for Mobile Menu
    const accordions = document.querySelectorAll('.accordion-toggle');
    accordions.forEach(toggle => {
        toggle.addEventListener('click', function(e) {
            e.preventDefault();
            const parent = this.parentElement;
            
            // Toggle active class on current parent
            parent.classList.toggle('expanded');
            
            // Adjust submenu height for animation
            const submenu = parent.querySelector('.drawer-submenu');
            if (parent.classList.contains('expanded')) {
                submenu.style.maxHeight = submenu.scrollHeight + 'px';
            } else {
                submenu.style.maxHeight = '0px';
                // Also close nested accordion if open
                parent.querySelectorAll('.has-nested-accordion').forEach(nested => {
                    nested.classList.remove('expanded');
                    const nestedSub = nested.querySelector('.drawer-nested-submenu');
                    nestedSub.style.maxHeight = '0px';
                });
            }
        });
    });

    // Nested Accordion Toggle for Mobile Menu
    const nestedAccordions = document.querySelectorAll('.nested-accordion-toggle');
    nestedAccordions.forEach(toggle => {
        toggle.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            const parent = this.parentElement;
            const ancestorMenu = parent.closest('.drawer-submenu');
            
            parent.classList.toggle('expanded');
            
            const nestedSub = parent.querySelector('.drawer-nested-submenu');
            
            if (parent.classList.contains('expanded')) {
                // First increase nested height
                nestedSub.style.maxHeight = nestedSub.scrollHeight + 'px';
                // Then adjust outer submenu height to accommodate nested content
                ancestorMenu.style.maxHeight = (ancestorMenu.scrollHeight + nestedSub.scrollHeight) + 'px';
            } else {
                const heightToReduce = nestedSub.scrollHeight;
                nestedSub.style.maxHeight = '0px';
                ancestorMenu.style.maxHeight = (ancestorMenu.scrollHeight - heightToReduce) + 'px';
            }
        });
    });
});
</script>

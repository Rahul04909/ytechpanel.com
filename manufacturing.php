<?php
/**
 * YTech Panels — Manufacturing Facilities Page
 * Professional overview of in-house capabilities, design, fabrication, and team.
 */
require_once __DIR__ . '/config/db.php';
$db = getDB();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manufacturing Facilities - YTech Panels | Electrical Control Panel Manufacturer</title>
    <meta name="description" content="YTech Panels state-of-the-art manufacturing facility in Gurugram. In-house fabrication, painting, assembly, testing facilities with experienced team.">
    <meta name="keywords" content="panel manufacturing facility, electrical panel fabrication, powder coating, panel testing, YTech Panels manufacturing">
    <link rel="icon" href="assets/logo.png" type="image/png">
    <link rel="stylesheet" href="assets/css/header.css">
    <link rel="stylesheet" href="assets/css/footer.css">
    <link rel="stylesheet" href="assets/css/manufacturing.css">
</head>
<body>
    <!-- Header -->
    <header class="main-header" id="site-header">
        <?php include 'includes/header.php'; ?>
    </header>

    <!-- ===== HERO BANNER ===== -->
    <section class="mf-hero">
        <div class="container">
            <div class="mf-breadcrumb">
                <a href="index.php">Home</a>
                <span class="mf-bc-sep">/</span>
                <span class="mf-bc-current">Manufacturing Facilities</span>
            </div>
            <h1>Manufacturing Facilities</h1>
            <p>State-of-the-art manufacturing unit equipped with latest machines, technologies, and a team of experienced professionals.</p>
        </div>
    </section>

    <!-- ===== OVERVIEW ===== -->
    <section class="mf-overview">
        <div class="container">
            <div class="mf-overview-grid">
                <div class="mf-overview-content">
                    <span class="mf-tagline">Our Facility</span>
                    <h2>State-of-the-Art Manufacturing Unit</h2>
                    <div class="mf-divider"></div>
                    <p>The organization has a state-of-the-art electrical control panels manufacturing unit, which is well equipped with all the latest machines and technologies. All the machines are operated by a team of experienced technicians, which has the requisite experience behind them.</p>
                    <p>Our power control panels manufacturing unit is well integrated with several units such as a fabrication section, an electrical wiring &amp; assembly section and an in-house testing &amp; measuring facilities. Every stage of production is monitored and controlled to ensure the highest quality standards.</p>
                </div>
                <div class="mf-overview-visual">
                    <div class="mf-image-frame">
                        <img src="assets/images/engineer_control_room.png" alt="YTech Panels Manufacturing Facility" loading="lazy">
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ===== IN-HOUSE FACILITIES ===== -->
    <section class="mf-facilities">
        <div class="container">
            <div class="mf-section-header">
                <span class="mf-tagline">Our Capabilities</span>
                <h2>In-House Facilities</h2>
                <p>We have following in-house facilities to ensure complete control over quality and delivery timelines.</p>
            </div>

            <div class="mf-fac-grid">
                <!-- FABRICATION -->
                <div class="mf-fac-item">
                    <div class="mf-fac-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="#dc2626" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/>
                        </svg>
                    </div>
                    <h4>FABRICATION</h4>
                    <p>Modern fabrication facilities to achieve fast, smooth &amp; well-finished panel structures using CRCA steel sheets.</p>
                </div>

                <!-- PAINTING -->
                <div class="mf-fac-item">
                    <div class="mf-fac-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="#dc2626" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"/>
                            <path d="M12 2a7.5 7.5 0 0 0 0 15 7.5 7.5 0 0 0 0-15z"/>
                            <path d="M12 2v15"/>
                        </svg>
                    </div>
                    <h4>PAINTING</h4>
                    <p>8-tank pre-treatment process for CRCA/GI sheets with powder coating booth, guns &amp; electric oven.</p>
                </div>

                <!-- ASSEMBLY WORK -->
                <div class="mf-fac-item">
                    <div class="mf-fac-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="#dc2626" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="2" y="3" width="20" height="14" rx="2"/>
                            <line x1="8" y1="21" x2="16" y2="21"/>
                            <line x1="12" y1="17" x2="12" y2="21"/>
                        </svg>
                    </div>
                    <h4>ASSEMBLY WORK</h4>
                    <p>Dedicated assembly section with skilled technicians for precise component mounting and panel integration.</p>
                </div>

                <!-- BUSBAR WORK -->
                <div class="mf-fac-item">
                    <div class="mf-fac-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="#dc2626" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M4 20h16"/>
                            <path d="M4 4h16"/>
                            <path d="M6 4v16"/>
                            <path d="M18 4v16"/>
                            <path d="M9 4v16"/>
                            <path d="M15 4v16"/>
                        </svg>
                    </div>
                    <h4>BUSBAR WORK</h4>
                    <p>Precision busbar fabrication including cutting, bending, drilling, and tin-plating for aluminium &amp; copper busbars.</p>
                </div>

                <!-- ELECTRICAL WIRING -->
                <div class="mf-fac-item">
                    <div class="mf-fac-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="#dc2626" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="16 3 21 3 21 8"/>
                            <line x1="4" y1="20" x2="21" y2="3"/>
                            <polyline points="21 16 21 21 16 21"/>
                            <line x1="15" y1="15" x2="21" y2="21"/>
                            <line x1="4" y1="4" x2="9" y2="9"/>
                        </svg>
                    </div>
                    <h4>ELECTRICAL WIRING</h4>
                    <p>Systematic electrical wiring and cabling as per schematic diagrams with proper routing, tagging, and termination.</p>
                </div>

                <!-- H.V. TEST -->
                <div class="mf-fac-item">
                    <div class="mf-fac-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="#dc2626" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z"/>
                        </svg>
                    </div>
                    <h4>H.V. TEST</h4>
                    <p>High voltage testing up to 2.5kV to verify insulation integrity and dielectric strength of all panels.</p>
                </div>

                <!-- MEGGER TEST -->
                <div class="mf-fac-item">
                    <div class="mf-fac-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="#dc2626" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"/>
                            <polyline points="12 6 12 12 16 14"/>
                        </svg>
                    </div>
                    <h4>MEGGER TEST</h4>
                    <p>Insulation resistance measurement using megger to ensure safe and reliable electrical isolation between conductors.</p>
                </div>

                <!-- NO LOAD TEST -->
                <div class="mf-fac-item">
                    <div class="mf-fac-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="#dc2626" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M22 12h-4l-3 9L9 3l-3 9H2"/>
                        </svg>
                    </div>
                    <h4>NO LOAD TEST</h4>
                    <p>No-load operational testing to verify proper functioning of all control circuits, relays, and indicators before dispatch.</p>
                </div>

                <!-- FUNCTIONAL TEST -->
                <div class="mf-fac-item">
                    <div class="mf-fac-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="#dc2626" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="9 11 12 14 22 4"/>
                            <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/>
                        </svg>
                    </div>
                    <h4>FUNCTIONAL TEST</h4>
                    <p>Comprehensive functional testing simulating real-world operating conditions to ensure panel performance meets specifications.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- ===== DESIGN & ENGINEERING ===== -->
    <section class="mf-design">
        <div class="container">
            <div class="mf-design-grid">
                <div class="mf-design-visual">
                    <div class="mf-image-frame">
                        <img src="assets/images/products/plc-panel.png" alt="YTech Panels Design & Engineering" loading="lazy">
                    </div>
                </div>
                <div class="mf-design-content">
                    <span class="mf-tagline">Design &amp; Engineering</span>
                    <h2>Engineering With Perfection</h2>
                    <div class="mf-divider"></div>
                    <p>Our Experienced &amp; Technically sound team of Engineers are always there to fulfill all the technical requirement of customer according to their need and also to suggest the best system for them.</p>
                    <p>Our design team is fully equipped with <strong>'State of the Art'</strong> designing softwares <strong>E-Plan &amp; Auto-CAD</strong>. The motto of our design team is to provide the system having <strong>'Engineering With Perfection'</strong> to our customer.</p>
                    <div class="mf-design-tools">
                        <div class="mf-tool-badge">
                            <svg viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="1.5"><rect x="4" y="4" width="16" height="16" rx="2"/><path d="M9 1v3"/><path d="M15 1v3"/><path d="M9 15V9h6l-4 4"/></svg>
                            E-Plan
                        </div>
                        <div class="mf-tool-badge">
                            <svg viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="1.5"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
                            Auto-CAD
                        </div>
                        <div class="mf-tool-badge">
                            <svg viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="1.5"><path d="M12 20h9"/><path d="M16.376 3.622a1 1 0 0 1 3.002 3.002L7.368 18.635a2 2 0 0 1-.855.506l-2.872.838a.5.5 0 0 1-.62-.62l.838-2.872a2 2 0 0 1 .506-.854z"/></svg>
                            SLD Design
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ===== FABRICATION & PAINTING ===== -->
    <section class="mf-fabpaint">
        <div class="container">
            <div class="mf-fabpaint-grid">
                <div class="mf-fabpaint-card">
                    <div class="mf-fp-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="#dc2626" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M4 20h16"/>
                            <path d="M6 16l4-14h4l4 14"/>
                            <path d="M10 4.5h4"/>
                        </svg>
                    </div>
                    <h3>Fabrication</h3>
                    <p>Our Fabrication is equipped with the modern fabrication facilities to achieve Fast, smooth &amp; well finished structure of panel. We use high-grade CRCA steel sheets precision-cut and formed to exact specifications.</p>
                    <ul class="mf-fp-list">
                        <li>CNC sheet metal cutting &amp; punching</li>
                        <li>Precision bending &amp; forming</li>
                        <li>Welding &amp; grinding</li>
                        <li>Vermin-proof &amp; dust-tight construction</li>
                    </ul>
                </div>
                <div class="mf-fabpaint-card">
                    <div class="mf-fp-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="#dc2626" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"/>
                            <path d="M12 2a7.5 7.5 0 0 0 0 15 7.5 7.5 0 0 0 0-15z"/>
                            <path d="M12 2v15"/>
                        </svg>
                    </div>
                    <h3>Painting / Powder Coating</h3>
                    <p>Our paint shop having <strong>8 Tank process</strong> for pre-treatment of CRCA / GI sheet to remove Oil, Dust &amp; any kind of rusting part before doing powder coating.</p>
                    <ul class="mf-fp-list">
                        <li>8-stage pre-treatment process</li>
                        <li>Electrostatic powder coating booth</li>
                        <li>High-efficiency powder coating guns</li>
                        <li>Electric curing oven for uniform finish</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- ===== OUR TEAM ===== -->
    <section class="mf-team">
        <div class="container">
            <div class="mf-section-header">
                <span class="mf-tagline">Our Team</span>
                <h2>Meet Our Professionals</h2>
                <p>M.B. Automation is backed with an efficient team, which is committed to cater to the diverse requirements and demands of the clients. Our team of quality controllers checks the entire range of the offered products on various parameters and hence ensures the optimum quality of offered products.</p>
            </div>

            <div class="mf-team-grid">
                <div class="mf-team-card">
                    <div class="mf-tm-avatar">
                        <img src="assets/images/engineer_control_room.png" alt="Rajesh Kumar" loading="lazy">
                    </div>
                    <h4>Rajesh Kumar</h4>
                    <span class="mf-tm-role">Chief Executive Officer</span>
                    <p>20+ years of experience in electrical panel design and manufacturing. Driving the company vision and strategic growth.</p>
                </div>

                <div class="mf-team-card">
                    <div class="mf-tm-avatar mf-tm-placeholder">
                        <svg viewBox="0 0 48 48" fill="#e2e8f0"><circle cx="24" cy="16" r="8"/><path d="M8 44c0-8.837 7.163-16 16-16s16 7.163 16 16"/></svg>
                        <span>AM</span>
                    </div>
                    <h4>Amit Mehta</h4>
                    <span class="mf-tm-role">Head of Design &amp; Engineering</span>
                    <p>Expert in E-Plan and Auto-CAD with 15+ years of experience in electrical control panel design engineering.</p>
                </div>

                <div class="mf-team-card">
                    <div class="mf-tm-avatar mf-tm-placeholder">
                        <svg viewBox="0 0 48 48" fill="#e2e8f0"><circle cx="24" cy="16" r="8"/><path d="M8 44c0-8.837 7.163-16 16-16s16 7.163 16 16"/></svg>
                        <span>VS</span>
                    </div>
                    <h4>Vikram Singh</h4>
                    <span class="mf-tm-role">Production Manager</span>
                    <p>Oversees end-to-end production, ensuring timely delivery, quality control, and efficient shop floor management.</p>
                </div>

                <div class="mf-team-card">
                    <div class="mf-tm-avatar mf-tm-placeholder">
                        <svg viewBox="0 0 48 48" fill="#e2e8f0"><circle cx="24" cy="16" r="8"/><path d="M8 44c0-8.837 7.163-16 16-16s16 7.163 16 16"/></svg>
                        <span>SP</span>
                    </div>
                    <h4>Suresh Patel</h4>
                    <span class="mf-tm-role">Quality Control Head</span>
                    <p>Ensures every panel meets stringent quality standards through rigorous testing at every stage of production.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- ===== CTA ===== -->
    <section class="mf-cta">
        <div class="container">
            <div class="mf-cta-box">
                <h2>Have a Project in Mind?</h2>
                <p>Contact our team for a free consultation or to schedule a visit to our manufacturing facility.</p>
                <div class="mf-cta-buttons">
                    <a href="contact.php" class="mf-btn mf-btn-primary">
                        <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="#fff" stroke-width="2.5"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                        Contact Us
                    </a>
                    <a href="products.php" class="mf-btn mf-btn-secondary">
                        <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="#dc2626" stroke-width="2.5"><rect x="2" y="3" width="20" height="14" rx="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>
                        View Products
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <?php include 'includes/footer.php'; ?>
</body>
</html>

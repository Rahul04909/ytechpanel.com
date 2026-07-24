<?php
/**
 * YTech Panels — Quality Page
 * Professional overview of quality standards, certifications, testing processes, and compliance.
 * SEO / AEO / GEO optimized for electrical control panel manufacturing.
 */
require_once __DIR__ . '/config/db.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quality Assurance - YTech Panels | ISO 9001 Certified Electrical Control Panel Manufacturer India</title>
    <meta name="description" content="YTech Panels' ISO 9001:2015 certified quality management system ensures every electrical control panel meets IEC 61439, IS 8623 standards. In-house testing, type-tested panels, rigorous QC processes for MCC, PCC, APFC, and distribution panels.">
    <meta name="keywords" content="quality electrical control panels, ISO 9001 certified panel manufacturer, IEC 61439 type tested panels, panel testing India, quality control electrical panels, YTech Panels quality, electrical panel certification India">
    <meta name="robots" content="index, follow">
    <meta name="author" content="YTech Panels">
    <link rel="canonical" href="https://ytechpanels.com/quality.php">

    <!-- Open Graph -->
    <meta property="og:title" content="Quality Assurance - YTech Panels | ISO 9001 Certified Manufacturer">
    <meta property="og:description" content="Rigorous quality control, in-house testing, and international standards compliance for every electrical control panel we manufacture.">
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://ytechpanels.com/quality.php">
    <meta property="og:image" content="https://ytechpanels.com/assets/logo.png">

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Quality Assurance - YTech Panels">
    <meta name="twitter:description" content="ISO 9001:2015 certified quality management for electrical control panels.">

    <!-- Schema.org / Structured Data -->
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "WebPage",
        "name": "Quality Assurance - YTech Panels",
        "description": "YTech Panels' ISO 9001:2015 certified quality management system ensures every electrical control panel meets IEC 61439 and IS 8623 standards.",
        "publisher": {
            "@type": "Organization",
            "name": "YTech Panels",
            "url": "https://ytechpanels.com",
            "logo": "https://ytechpanels.com/assets/logo.png"
        },
        "mainEntity": [
            {
                "@type": "Thing",
                "name": "ISO 9001:2015 Certification",
                "description": "YTech Panels is ISO 9001:2015 certified for quality management systems."
            },
            {
                "@type": "Thing",
                "name": "IEC 61439 Compliance",
                "description": "All panels comply with IEC 61439 standards for low-voltage switchgear and controlgear assemblies."
            }
        ]
    }
    </script>

    <link rel="icon" href="assets/logo.png" type="image/png">
    <link rel="stylesheet" href="assets/css/header.css">
    <link rel="stylesheet" href="assets/css/footer.css">
    <link rel="stylesheet" href="assets/css/quality.css">
</head>
<body>
    <!-- Header -->
    <header class="main-header" id="site-header">
        <?php include 'includes/header.php'; ?>
    </header>

    <!-- ===== HERO BANNER ===== -->
    <section class="ql-hero">
        <div class="container">
            <div class="ql-breadcrumb">
                <a href="index.php">Home</a>
                <span class="ql-bc-sep">/</span>
                <span class="ql-bc-current">Quality Assurance</span>
            </div>
            <h1>Quality Assurance</h1>
            <p>ISO 9001:2015 certified quality management — every panel tested, inspected, and certified before delivery.</p>
        </div>
    </section>

    <!-- ===== QUALITY OVERVIEW ===== -->
    <section class="ql-overview">
        <div class="container">
            <div class="ql-overview-grid">
                <div class="ql-overview-content">
                    <span class="ql-tagline">Our Commitment</span>
                    <h2>Uncompromising Quality at Every Stage</h2>
                    <div class="ql-divider"></div>
                    <p>At YTech Panels, quality is not a department — it's a culture. Our ISO 9001:2015 certified quality management system ensures that every electrical control panel, from a simple distribution board to a complex PLC-based automation panel, undergoes rigorous quality checks at every stage of manufacturing.</p>
                    <p>From incoming raw material inspection to final dispatch, our quality control team follows strict protocols aligned with <strong>IEC 61439</strong>, <strong>IS 8623</strong>, and <strong>IS 4237</strong> standards. Each panel is type-tested and routine-tested to guarantee safety, performance, and reliability in the most demanding industrial environments.</p>
                    <p>We believe that quality is what our customers remember long after the price is forgotten. That's why every component, every joint, and every test report is meticulously documented and traceable.</p>
                </div>
                <div class="ql-overview-visual">
                    <div class="ql-image-frame">
                        <img src="assets/images/engineer_control_room.png" alt="YTech Panels Quality Control Engineers inspecting electrical control panel" loading="lazy">
                    </div>
                    <div class="ql-badge-strip">
                        <div class="ql-badge-item">
                            <svg viewBox="0 0 24 24" fill="none" stroke="#0b4a83" stroke-width="1.5"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><polyline points="9 11 12 14 22 4"/></svg>
                            ISO 9001:2015
                        </div>
                        <div class="ql-badge-item">
                            <svg viewBox="0 0 24 24" fill="none" stroke="#0b4a83" stroke-width="1.5"><path d="M9 12l2 2 4-4"/><circle cx="12" cy="12" r="10"/></svg>
                            IEC 61439
                        </div>
                        <div class="ql-badge-item">
                            <svg viewBox="0 0 24 24" fill="none" stroke="#0b4a83" stroke-width="1.5"><path d="M9 12l2 2 4-4"/><circle cx="12" cy="12" r="10"/></svg>
                            IS 8623
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ===== QUALITY PILLARS ===== -->
    <section class="ql-pillars">
        <div class="container">
            <div class="ql-section-header">
                <span class="ql-tagline">Our Quality Framework</span>
                <h2>Four Pillars of Quality Assurance</h2>
                <p>Our quality management system rests on four foundational pillars that ensure consistent, reliable, and compliant panel manufacturing.</p>
            </div>
            <div class="ql-pillars-grid">
                <div class="ql-pillar-card">
                    <div class="ql-pillar-num">01</div>
                    <h3>Incoming Material Inspection</h3>
                    <p>All raw materials — CRCA sheets, copper/aluminium busbars, electrical components, switchgear, relays, and wiring — are inspected against approved vendor specifications and relevant IS/IEC standards before acceptance.</p>
                </div>
                <div class="ql-pillar-card">
                    <div class="ql-pillar-num">02</div>
                    <h3>In-Process Quality Control</h3>
                    <p>Every stage of fabrication, busbar work, wiring, and assembly is monitored by experienced quality controllers. Dimensional checks, torque verification, and visual inspections are performed at predefined hold points.</p>
                </div>
                <div class="ql-pillar-card">
                    <div class="ql-pillar-num">03</div>
                    <h3>Type Testing &amp; Compliance</h3>
                    <p>Panels undergo comprehensive type testing including temperature rise tests, short circuit withstand tests, IP rating verification, and dielectric tests as per IEC 61439 and IS 8623 requirements.</p>
                </div>
                <div class="ql-pillar-card">
                    <div class="ql-pillar-num">04</div>
                    <h3>Final Inspection &amp; Documentation</h3>
                    <p>Before dispatch, each panel is functionally tested, megger tested, and HV tested. Detailed test reports, material test certificates, and compliance documentation are provided with every shipment.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- ===== TESTING PROCESS ===== -->
    <section class="ql-testing">
        <div class="container">
            <div class="ql-section-header">
                <span class="ql-tagline">Rigorous Testing</span>
                <h2>Our In-House Testing Facilities</h2>
                <p>Every panel manufactured at YTech Panels undergoes a comprehensive battery of tests in our well-equipped in-house testing laboratory.</p>
            </div>
            <div class="ql-testing-grid">
                <div class="ql-test-item">
                    <div class="ql-test-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="#0b4a83" stroke-width="1.5"><path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z"/></svg>
                    </div>
                    <div>
                        <h4>High Voltage (HV) Test</h4>
                        <p>Dielectric withstand test at 2.5kV for 60 seconds to verify insulation integrity between live parts and enclosure.</p>
                    </div>
                </div>
                <div class="ql-test-item">
                    <div class="ql-test-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="#0b4a83" stroke-width="1.5"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                    </div>
                    <div>
                        <h4>Insulation Resistance (Megger) Test</h4>
                        <p>Insulation resistance measured using 500V/1000V megger to ensure safe isolation between conductors and earth.</p>
                    </div>
                </div>
                <div class="ql-test-item">
                    <div class="ql-test-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="#0b4a83" stroke-width="1.5"><path d="M22 12h-4l-3 9L9 3l-3 9H2"/></svg>
                    </div>
                    <div>
                        <h4>No-Load &amp; Functional Test</h4>
                        <p>Simulated operational testing to verify all control circuits, indicating lamps, relays, timers, and interlocks function correctly.</p>
                    </div>
                </div>
                <div class="ql-test-item">
                    <div class="ql-test-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="#0b4a83" stroke-width="1.5"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/><polyline points="3.27 6.96 12 12.01 20.73 6.96"/><line x1="12" y1="22.08" x2="12" y2="12"/></svg>
                    </div>
                    <div>
                        <h4>Temperature Rise Test</h4>
                        <p>Thermal imaging and thermocouple-based temperature measurement under rated current to verify busbar and component ratings.</p>
                    </div>
                </div>
                <div class="ql-test-item">
                    <div class="ql-test-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="#0b4a83" stroke-width="1.5"><path d="M12 2L2 7l10 5 10-5-10-5z"/><path d="M2 17l10 5 10-5"/><path d="M2 12l10 5 10-5"/></svg>
                    </div>
                    <div>
                        <h4>Short Circuit Withstand Test</h4>
                        <p>Type-tested short circuit withstand capacity up to 50kA for 1 second as per IEC 61439 to ensure busbar system integrity.</p>
                    </div>
                </div>
                <div class="ql-test-item">
                    <div class="ql-test-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="#0b4a83" stroke-width="1.5"><circle cx="12" cy="12" r="10"/><path d="M2 12h20"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>
                    </div>
                    <div>
                        <h4>IP Rating Verification</h4>
                        <p>Ingress protection testing for dust and water ingress as per IEC 60529 — panels are built up to IP55/IP65 as required.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ===== STANDARDS COMPLIANCE ===== -->
    <section class="ql-standards">
        <div class="container">
            <div class="ql-section-header">
                <span class="ql-tagline">Standards &amp; Compliance</span>
                <h2>International Standards We Comply With</h2>
                <p>Every electrical control panel we manufacture adheres to the following national and international standards.</p>
            </div>
            <div class="ql-standards-grid">
                <div class="ql-stand-card">
                    <div class="ql-stand-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="1.5"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><polyline points="9 11 12 14 22 4"/></svg>
                    </div>
                    <h4>ISO 9001:2015</h4>
                    <p>Quality management systems — certified for design, manufacturing, and service of electrical control panels.</p>
                </div>
                <div class="ql-stand-card">
                    <div class="ql-stand-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="1.5"><path d="M12 22c5.523 0 10-4.477 10-10S17.523 2 12 2 2 6.477 2 12s4.477 10 10 10z"/><path d="M9 12l2 2 4-4"/></svg>
                    </div>
                    <h4>IEC 61439</h4>
                    <p>Low-voltage switchgear and controlgear assemblies — type tested and verified for all panel configurations.</p>
                </div>
                <div class="ql-stand-card">
                    <div class="ql-stand-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="1.5"><path d="M12 22c5.523 0 10-4.477 10-10S17.523 2 12 2 2 6.477 2 12s4.477 10 10 10z"/><path d="M9 12l2 2 4-4"/></svg>
                    </div>
                    <h4>IS 8623</h4>
                    <p>Indian Standard for low-voltage switchgear and controlgear assemblies — full compliance for domestic projects.</p>
                </div>
                <div class="ql-stand-card">
                    <div class="ql-stand-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="1.5"><path d="M12 22c5.523 0 10-4.477 10-10S17.523 2 12 2 2 6.477 2 12s4.477 10 10 10z"/><path d="M9 12l2 2 4-4"/></svg>
                    </div>
                    <h4>IS 4237</h4>
                    <p>Specification for industrial control panels — covering construction, safety, and performance requirements.</p>
                </div>
                <div class="ql-stand-card">
                    <div class="ql-stand-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="1.5"><path d="M7 21h10"/><path d="M12 3v12m-4-4 4 4 4-4"/></svg>
                    </div>
                    <h4>IEC TR 61641</h4>
                    <p>Internal arc fault protection — tested for enhanced operator safety in arc-resistant panel designs.</p>
                </div>
                <div class="ql-stand-card">
                    <div class="ql-stand-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="1.5"><path d="M12 22c5.523 0 10-4.477 10-10S17.523 2 12 2 2 6.477 2 12s4.477 10 10 10z"/><polyline points="4 14 9 9 14 14 20 8"/></svg>
                    </div>
                    <h4>IEC 60529 / IP Rating</h4>
                    <p>Degrees of protection provided by enclosures — panels built to IP55, IP65, and higher as per project requirements.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- ===== QUALITY PROCESS FLOW ===== -->
    <section class="ql-process">
        <div class="container">
            <div class="ql-section-header">
                <span class="ql-tagline">How We Ensure Quality</span>
                <h2>End-to-End Quality Process</h2>
                <p>From design to dispatch, every panel passes through a structured quality workflow.</p>
            </div>
            <div class="ql-process-steps">
                <div class="ql-step">
                    <div class="ql-step-num">1</div>
                    <div class="ql-step-line"></div>
                    <div class="ql-step-content">
                        <h4>Design Review</h4>
                        <p>SLD verification, component selection, thermal calculation</p>
                    </div>
                </div>
                <div class="ql-step">
                    <div class="ql-step-num">2</div>
                    <div class="ql-step-line"></div>
                    <div class="ql-step-content">
                        <h4>Material Inspection</h4>
                        <p>Incoming raw material verification with test certificates</p>
                    </div>
                </div>
                <div class="ql-step">
                    <div class="ql-step-num">3</div>
                    <div class="ql-step-line"></div>
                    <div class="ql-step-content">
                        <h4>Fabrication QC</h4>
                        <p>Dimensional inspection, weld quality, surface finish check</p>
                    </div>
                </div>
                <div class="ql-step">
                    <div class="ql-step-num">4</div>
                    <div class="ql-step-line"></div>
                    <div class="ql-step-content">
                        <h4>Assembly &amp; Wiring</h4>
                        <p>Component mounting verification, torque check, continuity test</p>
                    </div>
                </div>
                <div class="ql-step">
                    <div class="ql-step-num">5</div>
                    <div class="ql-step-line"></div>
                    <div class="ql-step-content">
                        <h4>Testing</h4>
                        <p>HV, megger, functional, no-load, and temperature rise tests</p>
                    </div>
                </div>
                <div class="ql-step">
                    <div class="ql-step-num">6</div>
                    <div class="ql-step-line ql-step-last"></div>
                    <div class="ql-step-content">
                        <h4>Dispatch</h4>
                        <p>Final inspection, documentation, packaging, and shipment</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ===== CTA ===== -->
    <section class="ql-cta">
        <div class="container">
            <div class="ql-cta-box">
                <h2>Experience the YTech Quality Difference</h2>
                <p>Contact our team to discuss your project requirements. Get a free consultation and quote for quality-certified electrical control panels.</p>
                <div class="ql-cta-buttons">
                    <a href="contact.php" class="ql-btn ql-btn-primary">
                        <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="#fff" stroke-width="2.5"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                        Get a Quote
                    </a>
                    <a href="manufacturing.php" class="ql-btn ql-btn-secondary">
                        <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="#0b4a83" stroke-width="2.5"><rect x="2" y="3" width="20" height="14" rx="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>
                        Our Facility
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <?php include 'includes/footer.php'; ?>
</body>
</html>

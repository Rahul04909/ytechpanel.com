<?php
/**
 * YTech Panels — Default Clients Seeder
 * Inserts default client entries with their logo SVGs into the database.
 * 
 * Usage:
 *   CLI:   php sql-insert/insert-clients.php
 *   Web:   Visit sql-insert/insert-clients.php (requires SETUP_TOKEN in .env)
 */

require_once dirname(__DIR__) . '/config/db.php';

$isCli = (php_sapi_name() === 'cli');

// Web access protection — only block if setup is complete (admin exists)
if (!$isCli) {
    session_start();

    // Check if an admin already exists — only then require token
    try {
        $db = getDB();
        $adminCheck = $db->query("SELECT COUNT(*) FROM admin_users")->fetchColumn();
        
        if ($adminCheck > 0) {
            $expectedToken = $_ENV['SETUP_TOKEN'] ?? '';
            $providedToken = $_GET['token'] ?? '';
            
            if (empty($expectedToken) || $providedToken !== $expectedToken) {
                http_response_code(403);
                echo '<!DOCTYPE html><html><head><title>Access Denied</title></head><body>';
                echo '<h2>🔒 Access Denied</h2>';
                echo '<p>SETUP_TOKEN is not configured or invalid.</p>';
                echo '<p>Visit: sql-insert/insert-clients.php?token=YOUR_TOKEN</p>';
                echo '</body></html>';
                exit();
            }
        }
    } catch (Exception $e) {
        // DB might not exist yet, that's fine for first run
    }
}

// Output helper
function out($msg)
{
    global $isCli;
    if ($isCli) {
        echo $msg . "\n";
    } else {
        echo $msg . '<br>';
    }
}

if (!$isCli) {
    echo '<!DOCTYPE html><html><head><title>Insert Clients</title><style>body{font-family:monospace;padding:40px;background:#f5f5f5;}h2{color:#003a8c;}</style></head><body>';
    echo '<h2>YTech Panels — Client Seeder</h2>';
    echo '<hr>';
}

out('Starting client insertion...');
out('');

try {
    $db = getDB();

    // Create table if not exists
    $db->exec("CREATE TABLE IF NOT EXISTS `clients` (
        `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
        `name` VARCHAR(255) NOT NULL,
        `logo` VARCHAR(500) NOT NULL DEFAULT '',
        `website` VARCHAR(500) NOT NULL DEFAULT '',
        `description` TEXT DEFAULT NULL,
        `sort_order` INT NOT NULL DEFAULT 0,
        `status` TINYINT(1) NOT NULL DEFAULT 1,
        `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        KEY `idx_status` (`status`),
        KEY `idx_sort_order` (`sort_order`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    out('✓ Table "clients" ready.');

    // Check if clients already exist
    $count = $db->query("SELECT COUNT(*) FROM clients")->fetchColumn();
    if ($count > 0) {
        out("⚠ {$count} client(s) already exist. Skipping insert.");
        out('');
        out('Setup complete (no changes made).');
        if (!$isCli) echo '</body></html>';
        exit(0);
    }

    // Default clients with their logo SVGs (encoded as HTML-safe strings)
    $clients = [
        [
            'name' => 'G R Infraprojects',
            'logo' => 'data:image/svg+xml;base64,' . base64_encode('<svg viewBox="0 0 160 80" xmlns="http://www.w3.org/2000/svg"><path fill="#024789" d="M35 50 L80 15 L125 50 Z" opacity="0.15"/><path fill="#024789" d="M60 52 C 70 30, 90 30, 100 52 L90 52 C 85 42, 75 42, 70 52 Z"/><line x1="40" y1="52" x2="120" y2="52" stroke="#024789" stroke-width="4"/><line x1="45" y1="46" x2="115" y2="46" stroke="#0096d6" stroke-width="2"/><text x="80" y="28" font-family="Arial,sans-serif" font-weight="800" font-size="16" fill="#024789" text-anchor="middle" letter-spacing="1">G R I L</text><text x="80" y="67" font-family="Arial,sans-serif" font-weight="700" font-size="8.5" fill="#111" text-anchor="middle">G R INFRAPROJECTS LTD</text></svg>'),
            'website' => '',
            'sort_order' => 1,
        ],
        [
            'name' => "Domino's Pizza",
            'logo' => 'data:image/svg+xml;base64,' . base64_encode('<svg viewBox="0 0 160 80" xmlns="http://www.w3.org/2000/svg"><g transform="translate(68, 8) rotate(-45)"><rect x="0" y="0" width="26" height="26" fill="#006494"/><rect x="0" y="28" width="26" height="26" fill="#e01b22"/><rect x="28" y="28" width="26" height="26" fill="#e01b22"/><circle cx="13" cy="13" r="3" fill="#fff"/><circle cx="13" cy="41" r="3" fill="#fff"/><circle cx="41" cy="41" r="3" fill="#fff"/></g><text x="80" y="68" font-family="Arial,sans-serif" font-weight="800" font-size="12" fill="#006494" text-anchor="middle">Domino\'s Pizza</text></svg>'),
            'website' => '',
            'sort_order' => 2,
        ],
        [
            'name' => 'Larsen & Toubro',
            'logo' => 'data:image/svg+xml;base64,' . base64_encode('<svg viewBox="0 0 160 80" xmlns="http://www.w3.org/2000/svg"><circle cx="45" cy="40" r="18" fill="none" stroke="#0f4c81" stroke-width="2.5"/><path fill="#0f4c81" d="M40 30 L40 50 L48 50 L48 46 L43 46 L43 30 Z"/><path fill="#0f4c81" d="M37 30 L53 30 L53 34 L45 34 L45 42 L41 42 Z"/><text x="70" y="44" font-family="Arial,sans-serif" font-weight="700" font-size="11.5" fill="#0f4c81">LARSEN & TOUBRO</text></svg>'),
            'website' => '',
            'sort_order' => 3,
        ],
        [
            'name' => 'IRCON International',
            'logo' => 'data:image/svg+xml;base64,' . base64_encode('<svg viewBox="0 0 160 80" xmlns="http://www.w3.org/2000/svg"><text x="80" y="48" font-family="Arial,sans-serif" font-weight="900" font-size="34" fill="#df1f26" text-anchor="middle" font-style="italic" letter-spacing="-1">ircon</text></svg>'),
            'website' => '',
            'sort_order' => 4,
        ],
        [
            'name' => 'Dedicated Freight Corridor',
            'logo' => 'data:image/svg+xml;base64,' . base64_encode('<svg viewBox="0 0 160 80" xmlns="http://www.w3.org/2000/svg"><path fill="none" stroke="#df1f26" stroke-width="5" stroke-linecap="round" d="M35 32 C 55 20, 95 45, 125 25"/><path fill="none" stroke="#009639" stroke-width="3" stroke-linecap="round" d="M35 42 C 55 30, 95 55, 125 35"/><text x="80" y="65" font-family="Arial,sans-serif" font-weight="800" font-size="9" fill="#222" text-anchor="middle">Dedicated Freight Corridor</text></svg>'),
            'website' => '',
            'sort_order' => 5,
        ],
        [
            'name' => 'Jubilant FoodWorks',
            'logo' => 'data:image/svg+xml;base64,' . base64_encode('<svg viewBox="0 0 160 80" xmlns="http://www.w3.org/2000/svg"><path fill="#024789" d="M72 18 C 76 12, 84 12, 88 18 C 95 14, 98 22, 90 28 C 96 34, 88 40, 80 34 C 72 40, 64 34, 70 28 C 62 22, 65 14, 72 18 Z" opacity="0.8"/><text x="80" y="52" font-family="Arial,sans-serif" font-weight="800" font-size="16" fill="#024789" text-anchor="middle" font-style="italic">JUBILANT</text><text x="80" y="66" font-family="Arial,sans-serif" font-weight="700" font-size="11" fill="#0096d6" text-anchor="middle">FOODWORKS</text></svg>'),
            'website' => '',
            'sort_order' => 6,
        ],
        [
            'name' => 'Western Coalfields',
            'logo' => 'data:image/svg+xml;base64,' . base64_encode('<svg viewBox="0 0 160 80" xmlns="http://www.w3.org/2000/svg"><ellipse cx="80" cy="40" rx="45" ry="22" fill="#d9db00"/><ellipse cx="80" cy="40" rx="42" ry="19" fill="#007a33"/><text x="80" y="45" font-family="Arial,sans-serif" font-weight="800" font-size="12" fill="#fff" text-anchor="middle">WESTERN</text></svg>'),
            'website' => '',
            'sort_order' => 7,
        ],
        [
            'name' => 'J. Kumar Infraprojects',
            'logo' => 'data:image/svg+xml;base64,' . base64_encode('<svg viewBox="0 0 160 80" xmlns="http://www.w3.org/2000/svg"><rect x="70" y="15" width="20" height="25" fill="#fbb03b"/><path fill="#d91a21" d="M80 18 L73 30 L87 30 Z"/><text x="80" y="62" font-family="Arial,sans-serif" font-weight="700" font-size="9" fill="#0f2b5c" text-anchor="middle">J. Kumar Infraprojects Ltd.</text></svg>'),
            'website' => '',
            'sort_order' => 8,
        ],
        [
            'name' => 'PNC Infratech',
            'logo' => 'data:image/svg+xml;base64,' . base64_encode('<svg viewBox="0 0 160 80" xmlns="http://www.w3.org/2000/svg"><rect x="25" y="22" width="110" height="36" fill="#0c5ca8"/><rect x="35" y="27" width="90" height="26" fill="none" stroke="#fff" stroke-width="1.5"/><text x="80" y="45" font-family="Arial,sans-serif" font-weight="800" font-size="20" fill="#fff" text-anchor="middle">PNC</text><text x="80" y="70" font-family="Arial,sans-serif" font-weight="700" font-size="7.5" fill="#0c5ca8" text-anchor="middle">PNC Infratech Limited</text></svg>'),
            'website' => '',
            'sort_order' => 9,
        ],
        [
            'name' => 'NHAI',
            'logo' => 'data:image/svg+xml;base64,' . base64_encode('<svg viewBox="0 0 160 80" xmlns="http://www.w3.org/2000/svg"><path fill="#0d3c97" d="M30 45 L50 20 L110 20 L130 45 Z"/><path fill="#8b9bb4" d="M35 45 L52 23 L108 23 L125 45 Z"/><path fill="#fbb040" d="M76 45 L78 23 L82 23 L84 45 Z"/><rect x="40" y="47" width="80" height="15" fill="#0d3c97"/><text x="80" y="58" font-family="Arial,sans-serif" font-weight="800" font-size="12" fill="#fff" text-anchor="middle">NHAI</text></svg>'),
            'website' => '',
            'sort_order' => 10,
        ],
        [
            'name' => 'Indian Railways',
            'logo' => 'data:image/svg+xml;base64,' . base64_encode('<svg viewBox="0 0 160 80" xmlns="http://www.w3.org/2000/svg"><circle cx="80" cy="40" r="24" fill="#c1121f"/><circle cx="80" cy="40" r="22" fill="none" stroke="#fff" stroke-width="1.5"/><path fill="#fff" d="M72 35 H88 V48 H72 Z M70 42 L66 40 L70 38 Z M90 42 L94 40 L90 38 Z"/><circle cx="75" cy="52" r="2.5" fill="#fff"/><circle cx="85" cy="52" r="2.5" fill="#fff"/><text x="80" y="13" font-family="Arial,sans-serif" font-weight="800" font-size="9.5" fill="#c1121f" text-anchor="middle">INDIAN RAILWAYS</text></svg>'),
            'website' => '',
            'sort_order' => 11,
        ],
        [
            'name' => 'Smart City',
            'logo' => 'data:image/svg+xml;base64,' . base64_encode('<svg viewBox="0 0 160 80" xmlns="http://www.w3.org/2000/svg"><path fill="#f26522" d="M74 30 C 70 24, 78 20, 80 28 Z"/><path fill="#8dc63f" d="M86 30 C 90 24, 82 20, 80 28 Z"/><text x="80" y="48" font-family="Arial,sans-serif" font-weight="800" font-size="12" fill="#555" text-anchor="middle">Smart City</text><text x="80" y="60" font-family="Arial,sans-serif" font-weight="500" font-size="6" fill="#888" text-anchor="middle">Ministry of Housing & Urban Affairs</text></svg>'),
            'website' => '',
            'sort_order' => 12,
        ],
        [
            'name' => 'Godrej & Boyce',
            'logo' => 'data:image/svg+xml;base64,' . base64_encode('<svg viewBox="0 0 160 80" xmlns="http://www.w3.org/2000/svg"><text x="80" y="44" font-family="Arial,sans-serif" font-weight="bold" font-size="32" fill="#e11b22" text-anchor="middle">Godrej</text><text x="80" y="62" font-family="Arial,sans-serif" font-weight="700" font-size="7.5" fill="#444" text-anchor="middle">Godrej & Boyce Mfg. Co. Ltd.</text></svg>'),
            'website' => '',
            'sort_order' => 13,
        ],
        [
            'name' => 'Adani Wilmar',
            'logo' => 'data:image/svg+xml;base64,' . base64_encode('<svg viewBox="0 0 160 80" xmlns="http://www.w3.org/2000/svg"><text x="80" y="38" font-family="Arial,sans-serif" font-weight="800" font-size="20" fill="#005ea6" text-anchor="middle">adani</text><text x="80" y="58" font-family="Arial,sans-serif" font-weight="800" font-size="20" fill="#009639" text-anchor="middle">wilmar</text></svg>'),
            'website' => '',
            'sort_order' => 14,
        ],
        [
            'name' => 'Subodhan Capacitor',
            'logo' => 'data:image/svg+xml;base64,' . base64_encode('<svg viewBox="0 0 160 80" xmlns="http://www.w3.org/2000/svg"><rect x="25" y="24" width="110" height="32" fill="none" stroke="#f26522" stroke-width="2"/><text x="80" y="46" font-family="Arial,sans-serif" font-weight="900" font-size="14" fill="#f26522" text-anchor="middle">SUBODHAN</text><rect x="25" y="56" width="110" height="14" fill="#f26522"/><text x="80" y="66" font-family="Arial,sans-serif" font-weight="700" font-size="8" fill="#fff" text-anchor="middle">SUBODHAN CAPACITOR</text></svg>'),
            'website' => '',
            'sort_order' => 15,
        ],
        [
            'name' => 'Balmer Lawrie',
            'logo' => 'data:image/svg+xml;base64,' . base64_encode('<svg viewBox="0 0 160 80" xmlns="http://www.w3.org/2000/svg"><rect x="72" y="15" width="16" height="22" fill="none" stroke="#007a33" stroke-width="2"/><path fill="#007a33" d="M72 26 H88 V30 H72 Z"/><text x="80" y="54" font-family="Arial,sans-serif" font-weight="700" font-size="7.5" fill="#222" text-anchor="middle">Balmer Lawrie</text><text x="80" y="65" font-family="Arial,sans-serif" font-weight="700" font-size="8.5" fill="#007a33" text-anchor="middle">Balmer Lawrie & Co. Ltd.</text></svg>'),
            'website' => '',
            'sort_order' => 16,
        ],
        [
            'name' => 'AIIMS',
            'logo' => 'data:image/svg+xml;base64,' . base64_encode('<svg viewBox="0 0 160 80" xmlns="http://www.w3.org/2000/svg"><circle cx="80" cy="36" r="20" fill="none" stroke="#0a4693" stroke-width="2"/><line x1="80" y1="20" x2="80" y2="52" stroke="#0a4693" stroke-width="3"/><path fill="none" stroke="#0a4693" stroke-width="2" d="M74 44 C 74 38, 86 36, 86 30 C 86 24, 74 22, 80 18"/><text x="80" y="66" font-family="Arial,sans-serif" font-weight="700" font-size="7.5" fill="#0a4693" text-anchor="middle">ALL INDIA INSTITUTE OF MEDICAL SCIENCES</text></svg>'),
            'website' => '',
            'sort_order' => 17,
        ],
        [
            'name' => 'Apar Industries',
            'logo' => 'data:image/svg+xml;base64,' . base64_encode('<svg viewBox="0 0 160 80" xmlns="http://www.w3.org/2000/svg"><circle cx="80" cy="40" r="24" fill="none" stroke="#005ea6" stroke-width="2"/><circle cx="80" cy="40" r="18" fill="none" stroke="#005ea6" stroke-width="1" stroke-dasharray="3,3"/><text x="80" y="24" font-family="Arial,sans-serif" font-weight="900" font-size="12" fill="#005ea6" text-anchor="middle">APAR</text><text x="80" y="60" font-family="Arial,sans-serif" font-weight="800" font-size="11" fill="#005ea6" text-anchor="middle">APAR</text></svg>'),
            'website' => '',
            'sort_order' => 18,
        ],
    ];

    $stmt = $db->prepare("INSERT INTO `clients` (`name`, `logo`, `website`, `sort_order`, `status`, `created_at`) VALUES (:name, :logo, :website, :sort_order, 1, NOW())");

    $inserted = 0;
    foreach ($clients as $client) {
        $stmt->execute([
            ':name'       => $client['name'],
            ':logo'       => $client['logo'],
            ':website'    => $client['website'],
            ':sort_order' => $client['sort_order'],
        ]);
        $inserted++;
        out("  ✓ Inserted: {$client['name']}");
    }

    out('');
    out("✅ Successfully inserted {$inserted} default clients.");

} catch (Exception $e) {
    $debug = ($_ENV['APP_DEBUG'] ?? 'false') === 'true';
    $msg = $debug ? $e->getMessage() : 'An error occurred during client insertion.';
    out("❌ Error: {$msg}");
    if ($debug) {
        out("Stack trace: " . $e->getTraceAsString());
    }
    if (!$isCli) echo '</body></html>';
    exit(1);
}

out('');
out('Setup complete.');
if (!$isCli) echo '</body></html>';

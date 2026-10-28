<?php
session_start();

// Simulated session store
if (!isset($_SESSION['user'])) {
    $_SESSION['user'] = null; // null | ['name' => '...', 'role' => 'admin'|'staff'|'user', 'phone' => '...']
}

// Handle Logout
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    $_SESSION['user'] = null;
    session_destroy();
    header("Location: index.php");
    exit;
}

// Quick Mock Session role switcher (?mock_role=admin|staff|user)
if (isset($_GET['mock_role'])) {
    $role = in_array($_GET['mock_role'], ['admin', 'staff', 'user']) ? $_GET['mock_role'] : 'user';
    $_SESSION['user'] = [
        'name' => ucfirst($role) . ' Officer',
        'role' => $role,
        'phone' => '+91 98765 43210',
        'email' => $role . '@rmtinfotech.com'
    ];
    header("Location: index.php");
    exit;
}

$currentUser = $_SESSION['user'];

// Products Catalog (Websites, ERPs, Mobile Apps)
$products = [
    [
        'id' => 1,
        'title' => 'Titan Enterprise Institutional ERP',
        'category' => 'erp',
        'badge' => 'ERP System',
        'price' => '$399',
        'desc' => 'Complete institutional & academy ERP suite with student admissions, fee structures, ID cards, and attendance.',
        'stack' => ['PHP 8.2', 'MySQL', 'Bootstrap 5', 'REST API'],
        'rating' => '4.9/5.0 (42 sales)'
    ],
    [
        'id' => 2,
        'title' => 'Nexus Multi-Vendor SaaS Platform',
        'category' => 'website',
        'badge' => 'Website / SaaS',
        'price' => '$249',
        'desc' => 'Turnkey digital marketplace with automated escrow checkout, merchant analytics, and digital asset delivery.',
        'stack' => ['PHP PDO', 'Tailwind', 'Stripe', 'PayPal'],
        'rating' => '4.8/5.0 (78 sales)'
    ],
    [
        'id' => 3,
        'title' => 'SwiftValet Native Parking Assistant',
        'category' => 'mobile',
        'badge' => 'Mobile App',
        'price' => '$299',
        'desc' => 'High-performance valet parking ticket & dispatch app with QR scanner, offline Room DB, and live sync.',
        'stack' => ['Kotlin', 'Jetpack Compose', 'Room DB', 'PHP API'],
        'rating' => '5.0/5.0 (19 sales)'
    ],
    [
        'id' => 4,
        'title' => 'Apex Healthcare & Pharmacy ERP',
        'category' => 'erp',
        'badge' => 'ERP System',
        'price' => '$450',
        'desc' => 'HIPAA-ready clinical workflow ERP with OPD/IPD billing, patient bed charts, and batch expiry medicine stock.',
        'stack' => ['PHP', 'MySQLi', 'JavaScript', 'FPDF'],
        'rating' => '4.9/5.0 (31 sales)'
    ],
    [
        'id' => 5,
        'title' => 'NovaRide Driver & Rider Mobile Suite',
        'category' => 'mobile',
        'badge' => 'Mobile App',
        'price' => '$349',
        'desc' => 'Dual native Android driver and customer apps with real-time GPS tracking and instant fare estimation.',
        'stack' => ['Android SDK', 'Kotlin', 'Google Maps API', 'Firebase'],
        'rating' => '4.7/5.0 (54 sales)'
    ],
    [
        'id' => 6,
        'title' => 'CloudPOS Restaurant Billing & Kitchen Display',
        'category' => 'website',
        'badge' => 'Website / SaaS',
        'price' => '$189',
        'desc' => 'Thermal receipt printing, table layout map, real-time KDS screen, and staff shift payroll calculations.',
        'stack' => ['PHP', 'MySQL', 'Alpine.js', 'ESC/POS'],
        'rating' => '4.9/5.0 (96 sales)'
    ]
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RMT INFO TECH — Advanced Digital Marketplace</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-abyss: #03091E;
            --bg-nav: rgba(4, 12, 36, 0.88);
            --bg-card: #071330;
            --bg-card-hover: #0C1D4A;
            --border-subtle: #122452;
            --border-glow: #1D3D8F;
            
            --white: #FFFFFF;
            --white-muted: #E2E8F0;
            --white-dim: #94A3B8;
            
            --neon-blue: #00E5FF;
            --neon-blue-glow: rgba(0, 229, 255, 0.35);
            --royal-blue: #1E40AF;
            --royal-blue-glow: rgba(30, 64, 175, 0.4);
            
            --danger: #FF3366;
            --success: #00E676;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Plus Jakarta Sans', sans-serif;
            scroll-behavior: smooth;
        }

        body {
            background-color: var(--bg-abyss);
            color: var(--white);
            min-height: 100vh;
            line-height: 1.6;
            overflow-x: hidden;
            background-image: 
                radial-gradient(circle at 10% 20%, rgba(0, 229, 255, 0.08) 0%, transparent 40%),
                radial-gradient(circle at 90% 80%, rgba(30, 64, 175, 0.15) 0%, transparent 50%),
                linear-gradient(180deg, rgba(3, 9, 30, 0.95) 0%, #03091E 100%);
        }

        /* Scanline ambient glow effect */
        body::before {
            content: " ";
            position: fixed;
            top: 0; left: 0; right: 0; bottom: 0;
            background: linear-gradient(rgba(18, 16, 16, 0) 50%, rgba(0, 0, 0, 0.1) 50%);
            background-size: 100% 4px;
            z-index: 1;
            pointer-events: none;
            opacity: 0.3;
        }

        .container {
            max-width: 1320px;
            margin: 0 auto;
            padding: 0 24px;
            position: relative;
            z-index: 2;
        }

        /* ================= ADVANCED FLOATING NAVBAR ================= */
        .navbar-wrapper {
            position: sticky;
            top: 14px;
            z-index: 99;
            padding: 0 20px;
            margin-bottom: 25px;
        }

        .navbar-dock {
            background: var(--bg-nav);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid var(--border-glow);
            border-radius: 20px;
            padding: 10px 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 16px 36px rgba(0, 0, 0, 0.6), 0 0 20px rgba(0, 229, 255, 0.1);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .navbar-dock:hover {
            border-color: var(--neon-blue);
            box-shadow: 0 20px 45px rgba(0, 0, 0, 0.7), 0 0 28px rgba(0, 229, 255, 0.2);
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 14px;
            text-decoration: none;
        }

        .brand-gem {
            width: 44px;
            height: 44px;
            background: linear-gradient(135deg, var(--white) 0%, var(--neon-blue) 60%, var(--royal-blue) 100%);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 900;
            font-size: 1.15rem;
            color: #03091E;
            box-shadow: 0 0 18px var(--neon-blue-glow);
            transition: transform 0.35s ease, box-shadow 0.35s ease;
        }

        .brand:hover .brand-gem {
            transform: rotate(6deg) scale(1.08);
            box-shadow: 0 0 25px rgba(0, 229, 255, 0.7);
        }

        .brand-meta h1 {
            font-size: 1.22rem;
            font-weight: 800;
            color: var(--white);
            letter-spacing: 0.5px;
            line-height: 1.1;
        }

        .brand-meta span {
            font-size: 0.72rem;
            color: var(--neon-blue);
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1.4px;
        }

        /* Advanced Nav Menu Items */
        .nav-links {
            display: flex;
            align-items: center;
            gap: 6px;
            list-style: none;
        }

        .nav-link {
            position: relative;
            color: var(--white-muted);
            text-decoration: none;
            font-size: 0.94rem;
            font-weight: 600;
            padding: 8px 18px;
            border-radius: 10px;
            transition: all 0.25s ease;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .nav-link::after {
            content: '';
            position: absolute;
            bottom: 4px;
            left: 18px;
            right: 18px;
            height: 2px;
            background: var(--neon-blue);
            border-radius: 2px;
            transform: scaleX(0);
            transform-origin: center;
            transition: transform 0.25s ease;
            box-shadow: 0 0 8px var(--neon-blue);
        }

        .nav-link:hover, .nav-link.active {
            color: var(--white);
            background: rgba(255, 255, 255, 0.05);
        }

        .nav-link:hover::after, .nav-link.active::after {
            transform: scaleX(1);
        }

        /* Nav Action Buttons */
        .nav-actions {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .btn {
            position: relative;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 10px 22px;
            border-radius: 10px;
            font-size: 0.92rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            text-decoration: none;
            overflow: hidden;
            border: 1px solid transparent;
        }

        /* Advanced Hover Shimmer effect on buttons */
        .btn::after {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: linear-gradient(60deg, transparent, rgba(255, 255, 255, 0.25), transparent);
            transform: rotate(30deg) translate(-100%, 0);
            transition: transform 0.6s ease;
        }

        .btn:hover::after {
            transform: rotate(30deg) translate(100%, 0);
        }

        .btn-white {
            background: var(--white);
            color: #03091E;
            box-shadow: 0 0 15px rgba(255, 255, 255, 0.3);
        }

        .btn-white:hover {
            background: var(--white);
            transform: translateY(-2px);
            box-shadow: 0 0 25px rgba(255, 255, 255, 0.6);
        }

        .btn-ghost {
            background: rgba(255, 255, 255, 0.03);
            border-color: var(--border-glow);
            color: var(--white);
        }

        .btn-ghost:hover {
            background: rgba(0, 229, 255, 0.08);
            border-color: var(--neon-blue);
            color: var(--neon-blue);
            box-shadow: 0 0 15px rgba(0, 229, 255, 0.2);
            transform: translateY(-2px);
        }

        .btn-danger {
            background: rgba(255, 51, 102, 0.12);
            border-color: var(--danger);
            color: #FFA2B8;
        }

        .btn-danger:hover {
            background: var(--danger);
            color: var(--white);
            box-shadow: 0 0 18px rgba(255, 51, 102, 0.4);
        }

        /* User Identity Tag */
        .user-tag {
            display: flex;
            align-items: center;
            gap: 10px;
            background: var(--bg-card);
            border: 1px solid var(--border-glow);
            padding: 6px 14px;
            border-radius: 40px;
        }

        .badge-role {
            font-size: 0.68rem;
            font-weight: 800;
            padding: 3px 8px;
            border-radius: 6px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .badge-admin { background: #E11D48; color: #FFF; }
        .badge-staff { background: #2563EB; color: #FFF; }
        .badge-user { background: #059669; color: #FFF; }

        /* ================= HERO SECTION ================= */
        .hero {
            padding: 70px 0 50px;
            text-align: center;
        }

        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(0, 229, 255, 0.06);
            border: 1px solid rgba(0, 229, 255, 0.3);
            color: var(--neon-blue);
            padding: 6px 18px;
            border-radius: 30px;
            font-size: 0.85rem;
            font-weight: 700;
            margin-bottom: 22px;
            box-shadow: 0 0 15px rgba(0, 229, 255, 0.15);
        }

        .hero h2 {
            font-size: 3.8rem;
            font-weight: 900;
            line-height: 1.12;
            letter-spacing: -1.8px;
            margin-bottom: 20px;
            color: var(--white);
        }

        .hero h2 span.gradient-text {
            background: linear-gradient(135deg, #FFFFFF 30%, var(--neon-blue) 80%, #3B82F6 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .hero p {
            font-size: 1.15rem;
            color: var(--white-dim);
            max-width: 740px;
            margin: 0 auto 36px;
        }

        /* Filter Tabs */
        .catalog-nav {
            display: flex;
            justify-content: center;
            gap: 12px;
            margin-bottom: 45px;
            flex-wrap: wrap;
        }

        .filter-btn {
            background: var(--bg-card);
            color: var(--white-dim);
            border: 1px solid var(--border-subtle);
            padding: 11px 24px;
            border-radius: 40px;
            font-size: 0.92rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.25s ease;
        }

        .filter-btn:hover {
            color: var(--white);
            border-color: var(--neon-blue);
            transform: translateY(-2px);
        }

        .filter-btn.active {
            background: var(--white);
            color: #03091E;
            border-color: var(--white);
            box-shadow: 0 0 25px rgba(255, 255, 255, 0.4);
            transform: scale(1.03);
        }

        /* ================= PRODUCT CARDS & 3D HOVER ================= */
        .product-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(380px, 1fr));
            gap: 30px;
            margin-bottom: 90px;
        }

        .product-card {
            background: var(--bg-card);
            border: 1px solid var(--border-subtle);
            border-radius: 20px;
            padding: 26px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            position: relative;
            overflow: hidden;
            transition: all 0.35s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.4);
        }

        .product-card:hover {
            transform: translateY(-8px);
            background: var(--bg-card-hover);
            border-color: var(--neon-blue);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.7), 0 0 30px rgba(0, 229, 255, 0.25);
        }

        .product-card::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 3px;
            background: linear-gradient(90deg, transparent, var(--neon-blue), transparent);
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .product-card:hover::before {
            opacity: 1;
        }

        .card-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 16px;
        }

        .category-pill {
            font-size: 0.74rem;
            font-weight: 800;
            letter-spacing: 1px;
            text-transform: uppercase;
            padding: 4px 12px;
            border-radius: 6px;
            background: rgba(0, 229, 255, 0.12);
            color: var(--neon-blue);
            border: 1px solid rgba(0, 229, 255, 0.25);
        }

        .price-tag {
            font-family: 'JetBrains Mono', monospace;
            font-size: 1.55rem;
            font-weight: 800;
            color: var(--white);
        }

        .product-card h3 {
            font-size: 1.3rem;
            font-weight: 800;
            margin-bottom: 12px;
            color: var(--white);
            line-height: 1.3;
        }

        .product-card p {
            color: var(--white-dim);
            font-size: 0.93rem;
            margin-bottom: 22px;
            min-height: 64px;
        }

        .stack-pills {
            display: flex;
            flex-wrap: wrap;
            gap: 7px;
            margin-bottom: 24px;
        }

        .stack-chip {
            font-family: 'JetBrains Mono', monospace;
            font-size: 0.76rem;
            background: #03091E;
            color: var(--white-muted);
            border: 1px solid var(--border-subtle);
            padding: 3px 10px;
            border-radius: 6px;
        }

        .card-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-top: 1px solid var(--border-subtle);
            padding-top: 18px;
        }

        .rating-badge {
            font-size: 0.82rem;
            color: #FBBF24;
            font-weight: 600;
        }

        /* ================= MODALS & OVERLAYS ================= */
        .modal-overlay {
            position: fixed;
            inset: 0;
            background: rgba(3, 9, 30, 0.88);
            backdrop-filter: blur(14px);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 1000;
            padding: 20px;
        }

        .modal-overlay.active {
            display: flex;
        }

        .modal-box {
            background: var(--bg-card);
            border: 1px solid var(--border-glow);
            border-radius: 24px;
            width: 100%;
            max-width: 520px;
            box-shadow: 0 30px 70px rgba(0, 0, 0, 0.9), 0 0 35px rgba(0, 229, 255, 0.15);
            animation: modalPop 0.28s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            overflow: hidden;
        }

        .modal-box.wide {
            max-width: 820px;
        }

        @keyframes modalPop {
            from { opacity: 0; transform: scale(0.92); }
            to { opacity: 1; transform: scale(1); }
        }

        .modal-head {
            padding: 22px 28px;
            border-bottom: 1px solid var(--border-subtle);
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: rgba(255, 255, 255, 0.02);
        }

        .modal-head h3 {
            font-size: 1.35rem;
            font-weight: 800;
            color: var(--white);
        }

        .btn-close {
            background: transparent;
            border: none;
            color: var(--white-dim);
            font-size: 1.8rem;
            cursor: pointer;
            line-height: 1;
            transition: color 0.2s;
        }

        .btn-close:hover {
            color: var(--white);
        }

        .modal-content {
            padding: 28px;
        }

        /* Form Styles */
        .field {
            margin-bottom: 18px;
        }

        .field label {
            display: block;
            font-size: 0.84rem;
            font-weight: 700;
            color: var(--white-muted);
            margin-bottom: 8px;
        }

        .input-box, .select-box, .textarea-box {
            width: 100%;
            background: #03091E;
            border: 1px solid var(--border-subtle);
            color: var(--white);
            padding: 13px 16px;
            border-radius: 10px;
            font-size: 0.95rem;
            outline: none;
            transition: all 0.25s ease;
        }

        .input-box:focus, .select-box:focus, .textarea-box:focus {
            border-color: var(--neon-blue);
            box-shadow: 0 0 14px rgba(0, 229, 255, 0.25);
        }

        .role-switch {
            display: flex;
            background: #03091E;
            border: 1px solid var(--border-subtle);
            border-radius: 12px;
            padding: 5px;
            margin-bottom: 22px;
        }

        .role-tab {
            flex: 1;
            text-align: center;
            padding: 9px;
            font-size: 0.86rem;
            font-weight: 700;
            color: var(--white-dim);
            cursor: pointer;
            border-radius: 8px;
            transition: all 0.2s;
        }

        .role-tab.active {
            background: var(--white);
            color: #03091E;
            box-shadow: 0 2px 10px rgba(255, 255, 255, 0.2);
        }

        /* OTP Code Blocks */
        .otp-inputs {
            display: flex;
            gap: 10px;
            justify-content: center;
            margin: 22px 0;
        }

        .otp-digit {
            width: 50px;
            height: 56px;
            background: #03091E;
            border: 1px solid var(--border-glow);
            text-align: center;
            font-size: 1.6rem;
            font-family: 'JetBrains Mono', monospace;
            font-weight: 800;
            color: var(--neon-blue);
            border-radius: 10px;
        }

        /* Table */
        .modern-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.9rem;
            margin-top: 15px;
        }

        .modern-table th, .modern-table td {
            padding: 14px;
            text-align: left;
            border-bottom: 1px solid var(--border-subtle);
        }

        .modern-table th {
            color: var(--white-dim);
            font-size: 0.76rem;
            text-transform: uppercase;
            font-weight: 800;
            letter-spacing: 0.5px;
        }

        /* ================= FOOTER ================= */
        footer {
            border-top: 1px solid var(--border-subtle);
            background: #020617;
            padding: 45px 0;
            color: var(--white-dim);
            font-size: 0.9rem;
            margin-top: 40px;
        }

        .footer-flex {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 20px;
        }

        .footer-nav a {
            color: var(--white-muted);
            text-decoration: none;
            margin-left: 24px;
            font-weight: 600;
            transition: color 0.2s;
        }

        .footer-nav a:hover {
            color: var(--neon-blue);
        }

        /* Responsive Mobile Drawer */
        @media (max-width: 900px) {
            .nav-links { display: none; }
            .hero h2 { font-size: 2.6rem; }
            .product-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>

    <!-- ================= FLOATING DOCK NAVBAR ================= -->
    <div class="navbar-wrapper">
        <header class="navbar-dock container">
            <!-- Brand -->
            <a href="index.php" class="brand">
                <div class="brand-gem">RMT</div>
                <div class="brand-meta">
                    <h1>RMT INFO TECH</h1>
                    <span>Digital Marketplace</span>
                </div>
            </a>

            <!-- Full Navigation Menu (Home, About Us, Contact Us, Marketplace) -->
            <nav>
                <ul class="nav-links">
                    <li><a href="index.php" class="nav-link active">Home</a></li>
                    <li><a href="#projects" onclick="filterCatalog('all')" class="nav-link">Marketplace</a></li>
                    <li><a href="javascript:void(0)" onclick="openModal('aboutModal')" class="nav-link">About Us</a></li>
                    <li><a href="javascript:void(0)" onclick="openModal('contactModal')" class="nav-link">Contact Us</a></li>
                </ul>
            </nav>

            <!-- Actions -->
            <div class="nav-actions">
                <?php if ($currentUser): ?>
                    <div class="user-tag">
                        <span style="font-size: 0.88rem; font-weight: 700;"><?= htmlspecialchars($currentUser['name']) ?></span>
                        <span class="badge-role badge-<?= htmlspecialchars($currentUser['role']) ?>">
                            <?= strtoupper(htmlspecialchars($currentUser['role'])) ?>
                        </span>
                    </div>

                    <?php if ($currentUser['role'] === 'admin'): ?>
                        <button class="btn btn-ghost" onclick="openModal('adminStaffModal')">
                            ⚙ Staff Control
                        </button>
                    <?php endif; ?>

                    <a href="index.php?action=logout" class="btn btn-danger">Sign Out</a>
                <?php else: ?>
                    <button class="btn btn-ghost" onclick="openModal('loginModal')">Sign In</button>
                    <button class="btn btn-white" onclick="openModal('registerModal')">Create Account</button>
                <?php endif; ?>
            </div>
        </header>
    </div>

    <!-- ================= HERO SECTION ================= -->
    <section class="hero">
        <div class="container">
            <div class="hero-badge">
                <span>✦ Advanced Pure White & Deep Blue Ecosystem</span>
            </div>
            <h2>Next-Gen Codebases,<br><span class="gradient-text">Instant Deployment.</span></h2>
            <p>Direct ownership of production-ready Web platforms, institutional ERP architectures, and native Android mobile apps with verified escrow transfer.</p>

            <!-- Catalog Category Filter -->
            <div class="catalog-nav" id="catalog-filters">
                <button class="filter-btn active" onclick="filterCatalog('all', this)">All Solutions</button>
                <button class="filter-btn" onclick="filterCatalog('erp', this)">Institutional ERPs</button>
                <button class="filter-btn" onclick="filterCatalog('website', this)">Web & SaaS</button>
                <button class="filter-btn" onclick="filterCatalog('mobile', this)">Mobile Apps</button>
            </div>
        </div>
    </section>

    <!-- ================= MARKETPLACE GRID ================= -->
    <main class="container" id="projects">
        <div class="product-grid" id="productGrid">
            <?php foreach ($products as $p): ?>
                <div class="product-card" data-category="<?= $p['category'] ?>">
                    <div>
                        <div class="card-header">
                            <span class="category-pill"><?= $p['badge'] ?></span>
                            <span class="price-tag"><?= $p['price'] ?></span>
                        </div>
                        <h3><?= htmlspecialchars($p['title']) ?></h3>
                        <p><?= htmlspecialchars($p['desc']) ?></p>
                        <div class="stack-pills">
                            <?php foreach ($p['stack'] as $tech): ?>
                                <span class="stack-chip"><?= htmlspecialchars($tech) ?></span>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <div class="card-footer">
                        <span class="rating-badge">★ <?= $p['rating'] ?></span>
                        <button class="btn btn-white" style="padding: 8px 16px; font-size: 0.85rem;" onclick="alert('Proceeding to live demo and escrow checkout for: <?= addslashes($p['title']) ?>')">
                            Acquire Now →
                        </button>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </main>

    <!-- ================= MODALS ================= -->

    <!-- 1. ABOUT US MODAL -->
    <div class="modal-overlay" id="aboutModal">
        <div class="modal-box">
            <div class="modal-head">
                <h3>About RMT INFO TECH</h3>
                <button class="btn-close" onclick="closeModal('aboutModal')">&times;</button>
            </div>
            <div class="modal-content">
                <p style="color: var(--white-muted); margin-bottom: 16px; font-size: 0.95rem;">
                    <b>RMT INFO TECH</b> is a premier software architecture exchange platform designed to bridge technical creators with institutions and enterprises.
                </p>
                <div style="background: #03091E; border: 1px solid var(--border-subtle); padding: 18px; border-radius: 12px; margin-bottom: 20px;">
                    <div style="color: var(--neon-blue); font-weight: 700; margin-bottom: 6px;">✦ Our Core Verticals:</div>
                    <ul style="color: var(--white-dim); font-size: 0.9rem; padding-left: 20px; line-height: 1.8;">
                        <li><b>Institutional ERP Systems:</b> Comprehensive school, institute, and hospital platforms.</li>
                        <li><b>Websites & SaaS Products:</b> Complete turnkey marketplaces and POS tools.</li>
                        <li><b>Native Mobile Apps:</b> Kotlin Jetpack Compose and Android SDK software.</li>
                    </ul>
                </div>
                <button class="btn btn-white" style="width: 100%;" onclick="closeModal('aboutModal')">Close Window</button>
            </div>
        </div>
    </div>

    <!-- 2. CONTACT US MODAL -->
    <div class="modal-overlay" id="contactModal">
        <div class="modal-box">
            <div class="modal-head">
                <h3>Contact Engineering Support</h3>
                <button class="btn-close" onclick="closeModal('contactModal')">&times;</button>
            </div>
            <div class="modal-content">
                <form onsubmit="handleContactSubmit(event)">
                    <div class="field">
                        <label>Your Name</label>
                        <input type="text" class="input-box" placeholder="e.g. John Doe" required>
                    </div>
                    <div class="field">
                        <label>Contact Email or Phone Number</label>
                        <input type="text" class="input-box" placeholder="e.g. john@example.com or +91..." required>
                    </div>
                    <div class="field">
                        <label>Project Inquiry or Message</label>
                        <textarea class="textarea-box" rows="4" placeholder="How can our technical team assist you?" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-white" style="width: 100%;">Transmit Message →</button>
                </form>
            </div>
        </div>
    </div>

    <!-- 3. LOGIN MODAL (Admin / Staff / User) -->
    <div class="modal-overlay" id="loginModal">
        <div class="modal-box">
            <div class="modal-head">
                <h3>System Authorization</h3>
                <button class="btn-close" onclick="closeModal('loginModal')">&times;</button>
            </div>
            <div class="modal-content">
                <!-- Role selector -->
                <div class="role-switch">
                    <div class="role-tab active" onclick="setLoginRole('user', this)">User</div>
                    <div class="role-tab" onclick="setLoginRole('staff', this)">Staff</div>
                    <div class="role-tab" onclick="setLoginRole('admin', this)">Admin</div>
                </div>

                <form onsubmit="handleLogin(event)">
                    <input type="hidden" id="loginSelectedRole" value="user">

                    <div class="field">
                        <label id="loginIdLabel">Mobile Number or Email</label>
                        <input type="text" id="loginIdentifier" class="input-box" placeholder="e.g. +91 9876543210 or user@example.com" required>
                    </div>

                    <div class="field">
                        <label>Secure Password</label>
                        <input type="password" id="loginPassword" class="input-box" placeholder="••••••••••••" required>
                    </div>

                    <button type="submit" class="btn btn-white" style="width: 100%; padding: 13px; margin-top: 10px;">
                        Authenticate as <span id="loginBtnRoleLabel" style="text-transform: capitalize; margin-left: 4px;">User</span>
                    </button>

                    <div style="text-align: center; margin-top: 18px; font-size: 0.86rem; color: var(--white-dim);">
                        Don't have an account yet? 
                        <a href="javascript:void(0)" onclick="switchModal('loginModal', 'registerModal')" style="color: var(--neon-blue); text-decoration: none; font-weight: 700;">Create User Account</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- 4. USER REGISTRATION + MANDATORY PHONE OTP -->
    <div class="modal-overlay" id="registerModal">
        <div class="modal-box">
            <div class="modal-head">
                <h3>New User Registration</h3>
                <button class="btn-close" onclick="closeModal('registerModal')">&times;</button>
            </div>
            <div class="modal-content">
                <!-- Step 1: Info Form -->
                <div id="regStep1">
                    <form onsubmit="sendRegistrationOtp(event)">
                        <div class="field">
                            <label>Full Legal Name</label>
                            <input type="text" id="regFullName" class="input-box" placeholder="e.g. Alex Henderson" required>
                        </div>

                        <div class="field">
                            <label>Email Address</label>
                            <input type="email" id="regEmail" class="input-box" placeholder="alex@domain.com" required>
                        </div>

                        <div class="field">
                            <label>Mobile Number (For Mandatory Phone Authentication)</label>
                            <div style="display: flex; gap: 8px;">
                                <input type="text" value="+91" readonly class="input-box" style="width: 75px; text-align: center; color: var(--neon-blue); font-weight: 800;">
                                <input type="tel" id="regPhone" class="input-box" placeholder="9876543210" pattern="[0-9]{10}" maxlength="10" required>
                            </div>
                            <span style="font-size: 0.75rem; color: var(--white-dim); margin-top: 5px; display: block;">A 6-digit SMS verification code will be dispatched to this device.</span>
                        </div>

                        <div class="field">
                            <label>Account Password</label>
                            <input type="password" id="regPassword" class="input-box" placeholder="••••••••••••" minlength="6" required>
                        </div>

                        <button type="submit" class="btn btn-white" style="width: 100%; padding: 13px; margin-top: 10px;">
                            Verify Mobile & Continue →
                        </button>
                    </form>
                </div>

                <!-- Step 2: OTP Entry -->
                <div id="regStep2" style="display: none; text-align: center;">
                    <div style="font-size: 2.2rem; margin-bottom: 10px;">📱</div>
                    <h4 style="font-size: 1.2rem; margin-bottom: 6px;">SMS Security Verification</h4>
                    <p style="font-size: 0.88rem; color: var(--white-dim);">
                        Code dispatched to <b id="otpTargetPhone" style="color: var(--white);"></b>
                    </p>

                    <div class="otp-inputs">
                        <input type="text" maxlength="1" class="otp-digit" oninput="focusNext(this, 0)" autofocus>
                        <input type="text" maxlength="1" class="otp-digit" oninput="focusNext(this, 1)">
                        <input type="text" maxlength="1" class="otp-digit" oninput="focusNext(this, 2)">
                        <input type="text" maxlength="1" class="otp-digit" oninput="focusNext(this, 3)">
                        <input type="text" maxlength="1" class="otp-digit" oninput="focusNext(this, 4)">
                        <input type="text" maxlength="1" class="otp-digit" oninput="focusNext(this, 5)">
                    </div>

                    <div style="font-size: 0.82rem; color: var(--white-dim); margin-bottom: 22px;">
                        Resend code in <span id="otpTimer" style="color: var(--neon-blue); font-weight: 700;">0:59</span>
                    </div>

                    <button type="button" class="btn btn-white" style="width: 100%; padding: 13px;" onclick="confirmOtpAndActivate()">
                        Validate & Activate Account
                    </button>
                    
                    <button type="button" class="btn btn-ghost" style="width: 100%; margin-top: 10px;" onclick="backToRegStep1()">
                        ← Change Number
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- 5. ADMIN STAFF CONTROL & ADD STAFF -->
    <div class="modal-overlay" id="adminStaffModal">
        <div class="modal-box wide">
            <div class="modal-head">
                <h3>Admin Command — Staff Roster Control</h3>
                <button class="btn-close" onclick="closeModal('adminStaffModal')">&times;</button>
            </div>
            <div class="modal-content">
                <!-- Add Staff Option -->
                <div style="background: #03091E; border: 1px solid var(--border-subtle); border-radius: 14px; padding: 22px; margin-bottom: 24px;">
                    <h4 style="color: var(--neon-blue); margin-bottom: 14px; font-size: 1.05rem;">+ Add New Staff Member</h4>
                    <form onsubmit="handleCreateStaff(event)" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 14px; align-items: end;">
                        <div>
                            <label style="font-size: 0.8rem; color: var(--white-dim);">Staff Full Name</label>
                            <input type="text" id="staffName" class="input-box" placeholder="e.g. David Ross" required>
                        </div>
                        <div>
                            <label style="font-size: 0.8rem; color: var(--white-dim);">Corporate Email</label>
                            <input type="email" id="staffEmail" class="input-box" placeholder="david@rmtinfotech.com" required>
                        </div>
                        <div>
                            <label style="font-size: 0.8rem; color: var(--white-dim);">Assigned Department</label>
                            <select id="staffDept" class="select-box">
                                <option value="Institutional ERPs">Institutional ERPs</option>
                                <option value="Mobile Engineering">Mobile Engineering (Kotlin)</option>
                                <option value="Web & SaaS Division">Web & SaaS Division</option>
                                <option value="Escrow Verification">Escrow Verification</option>
                            </select>
                        </div>
                        <div>
                            <button type="submit" class="btn btn-white" style="width: 100%;">Create Staff</button>
                        </div>
                    </form>
                </div>

                <!-- Existing Staff Directory -->
                <h4 style="font-size: 1rem; margin-bottom: 8px;">Active Staff Directory</h4>
                <div style="overflow-x: auto;">
                    <table class="modern-table" id="staffDirectoryTable">
                        <thead>
                            <tr>
                                <th>Staff Member</th>
                                <th>Department</th>
                                <th>Clearance</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><b>David K.</b><br><span style="font-size: 0.75rem; color: var(--white-dim);">david@rmtinfotech.com</span></td>
                                <td>ERP Engineering Lead</td>
                                <td><span style="color: var(--success); font-weight: 700;">Active</span></td>
                                <td><button class="btn btn-danger" style="padding: 4px 10px; font-size: 0.75rem;" onclick="removeStaff(this)">Revoke</button></td>
                            </tr>
                            <tr>
                                <td><b>Rohit Sharma</b><br><span style="font-size: 0.75rem; color: var(--white-dim);">rohit@rmtinfotech.com</span></td>
                                <td>Native Android / Kotlin</td>
                                <td><span style="color: var(--success); font-weight: 700;">Active</span></td>
                                <td><button class="btn btn-danger" style="padding: 4px 10px; font-size: 0.75rem;" onclick="removeStaff(this)">Revoke</button></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- ================= FOOTER ================= -->
    <footer>
        <div class="container footer-flex">
            <div>
                © <?= date('Y') ?> <b>RMT INFO TECH</b>. Pure White & Deep Blue Core.
            </div>
            <div class="footer-nav">
                <a href="index.php">Home</a>
                <a href="javascript:void(0)" onclick="openModal('aboutModal')">About Us</a>
                <a href="javascript:void(0)" onclick="openModal('contactModal')">Contact Us</a>
                <a href="javascript:void(0)" onclick="setLoginRole('admin'); openModal('loginModal')">Admin Access</a>
                <a href="javascript:void(0)" onclick="setLoginRole('staff'); openModal('loginModal')">Staff Portal</a>
            </div>
        </div>
    </footer>

    <!-- ================= SCRIPTS ================= -->
    <script>
        // Modal Controls
        function openModal(id) {
            document.getElementById(id).classList.add('active');
        }

        function closeModal(id) {
            document.getElementById(id).classList.remove('active');
        }

        function switchModal(closeId, openId) {
            closeModal(closeId);
            openModal(openId);
        }

        window.onclick = function(e) {
            if (e.target.classList.contains('modal-overlay')) {
                e.target.classList.remove('active');
            }
        };

        // Catalog Filtering
        function filterCatalog(category, btn) {
            if (btn) {
                document.querySelectorAll('#catalog-filters .filter-btn').forEach(b => b.classList.remove('active'));
                btn.classList.add('active');
            }

            const cards = document.querySelectorAll('#productGrid .product-card');
            cards.forEach(card => {
                if (category === 'all' || card.getAttribute('data-category') === category) {
                    card.style.display = 'flex';
                } else {
                    card.style.display = 'none';
                }
            });
        }

        // Role Switching inside Login
        function setLoginRole(role, tab) {
            document.getElementById('loginSelectedRole').value = role;
            document.getElementById('loginBtnRoleLabel').innerText = role;

            if (tab) {
                document.querySelectorAll('.role-tab').forEach(t => t.classList.remove('active'));
                tab.classList.add('active');
            }

            const idLabel = document.getElementById('loginIdLabel');
            if (role === 'admin') {
                idLabel.innerText = "Admin Security Email / ID";
            } else if (role === 'staff') {
                idLabel.innerText = "Official Staff Identification";
            } else {
                idLabel.innerText = "Verified Mobile Number or Email";
            }
        }

        // Mock Login Execution
        function handleLogin(e) {
            e.preventDefault();
            const role = document.getElementById('loginSelectedRole').value;
            window.location.href = `index.php?mock_role=${role}`;
        }

        // Contact Message Handler
        function handleContactSubmit(e) {
            e.preventDefault();
            alert("Your inquiry has been dispatched to RMT INFO TECH technical engineers.");
            closeModal('contactModal');
        }

        // Mobile Phone Registration & OTP Handlers
        let otpCountdown;
        function sendRegistrationOtp(e) {
            e.preventDefault();
            const phone = document.getElementById('regPhone').value;
            if (phone.length !== 10) {
                alert("Please enter a valid 10-digit mobile number.");
                return;
            }

            document.getElementById('otpTargetPhone').innerText = `+91 ${phone}`;
            document.getElementById('regStep1').style.display = 'none';
            document.getElementById('regStep2').style.display = 'block';

            let seconds = 59;
            const timerEl = document.getElementById('otpTimer');
            clearInterval(otpCountdown);
            otpCountdown = setInterval(() => {
                seconds--;
                timerEl.innerText = `0:${seconds < 10 ? '0' : ''}${seconds}`;
                if (seconds <= 0) clearInterval(otpCountdown);
            }, 1000);
        }

        function backToRegStep1() {
            clearInterval(otpCountdown);
            document.getElementById('regStep2').style.display = 'none';
            document.getElementById('regStep1').style.display = 'block';
        }

        function focusNext(input, index) {
            const boxes = document.querySelectorAll('.otp-digit');
            if (input.value.length === 1 && index < boxes.length - 1) {
                boxes[index + 1].focus();
            }
        }

        function confirmOtpAndActivate() {
            const boxes = document.querySelectorAll('.otp-digit');
            let enteredOtp = "";
            boxes.forEach(b => enteredOtp += b.value);

            if (enteredOtp.length < 6) {
                alert("Please enter all 6 digits of the OTP dispatched to your mobile.");
                return;
            }

            alert("Phone number verified successfully! Welcome to RMT INFO TECH.");
            window.location.href = "index.php?mock_role=user";
        }

        // Admin: Add Staff Handler
        function handleCreateStaff(e) {
            e.preventDefault();
            const name = document.getElementById('staffName').value;
            const email = document.getElementById('staffEmail').value;
            const dept = document.getElementById('staffDept').value;

            const table = document.getElementById('staffDirectoryTable').getElementsByTagName('tbody')[0];
            const newRow = table.insertRow();
            newRow.innerHTML = `
                <td><b>${name}</b><br><span style="font-size: 0.75rem; color: var(--white-dim);">${email}</span></td>
                <td>${dept}</td>
                <td><span style="color: var(--success); font-weight: 700;">Active</span></td>
                <td><button class="btn btn-danger" style="padding: 4px 10px; font-size: 0.75rem;" onclick="removeStaff(this)">Revoke</button></td>
            `;

            alert(`Staff account for ${name} registered successfully.`);
            document.getElementById('staffName').value = '';
            document.getElementById('staffEmail').value = '';
        }

        function removeStaff(btn) {
            if (confirm("Are you sure you want to revoke this staff member's clearance?")) {
                btn.closest('tr').remove();
            }
        }
    </script>
</body>
</html>

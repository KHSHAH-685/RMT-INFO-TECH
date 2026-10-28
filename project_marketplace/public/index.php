<?php
session_start();

// Simulated backend state for standalone demonstration
if (!isset($_SESSION['user'])) {
    $_SESSION['user'] = null; // null | ['name' => '...', 'role' => 'admin'|'staff'|'user']
}

// Handle Logout
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    $_SESSION['user'] = null;
    session_destroy();
    header("Location: index.php");
    exit;
}

// Quick session mock for testing roles directly via query string: ?mock_role=admin
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

// Sample Marketplace Catalog
$products = [
    [
        'id' => 1,
        'title' => 'Titan Enterprise Institutional ERP',
        'category' => 'erp',
        'badge' => 'ERP System',
        'price' => '$399',
        'desc' => 'Complete institutional & school ERP suite with student admissions, fee tokens, exam grading, and attendance tracking.',
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
    <title>RMT INFO TECH — Software Marketplace & Digital Assets</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-deep: #050B17;
            --bg-card: #0A152E;
            --bg-card-hover: #0F2044;
            --bg-surface: #071024;
            --border: #182B54;
            --border-highlight: #2A488A;
            --primary-accent: #00D2FF;
            --primary-hover: #38BDF8;
            --accent-glow: rgba(0, 210, 255, 0.25);
            --text-main: #FFFFFF;
            --text-secondary: #94A3B8;
            --text-muted: #64748B;
            --danger: #EF4444;
            --success: #10B981;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        body {
            background-color: var(--bg-deep);
            color: var(--text-main);
            min-height: 100vh;
            line-height: 1.6;
            overflow-x: hidden;
            background-image: 
                radial-gradient(circle at 15% 15%, rgba(0, 210, 255, 0.08) 0%, transparent 45%),
                radial-gradient(circle at 85% 65%, rgba(30, 64, 175, 0.15) 0%, transparent 50%);
        }

        /* Container */
        .container {
            max-width: 1280px;
            margin: 0 auto;
            padding: 0 24px;
        }

        /* Header / Navbar */
        header {
            position: sticky;
            top: 0;
            z-index: 50;
            background: rgba(5, 11, 23, 0.85);
            backdrop-filter: blur(16px);
            border-bottom: 1px solid var(--border);
        }

        .nav-inner {
            display: flex;
            align-items: center;
            justify-content: space-between;
            height: 76px;
        }

        .brand-logo {
            display: flex;
            align-items: center;
            gap: 14px;
            text-decoration: none;
        }

        .brand-badge {
            width: 44px;
            height: 44px;
            border-radius: 10px;
            background: linear-gradient(135deg, #00D2FF, #0051FF);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            color: #050B17;
            font-size: 1.15rem;
            box-shadow: 0 0 20px rgba(0, 210, 255, 0.4);
        }

        .brand-text h1 {
            font-size: 1.25rem;
            font-weight: 800;
            color: #FFF;
            letter-spacing: 0.5px;
            line-height: 1.1;
        }

        .brand-text span {
            font-size: 0.72rem;
            color: var(--primary-accent);
            text-transform: uppercase;
            letter-spacing: 1.5px;
            font-weight: 600;
        }

        nav.menu-links {
            display: flex;
            gap: 28px;
        }

        nav.menu-links a {
            color: var(--text-secondary);
            text-decoration: none;
            font-size: 0.95rem;
            font-weight: 500;
            transition: color 0.2s;
        }

        nav.menu-links a:hover, nav.menu-links a.active {
            color: #FFF;
        }

        .nav-actions {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        /* Buttons */
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 10px 20px;
            border-radius: 8px;
            font-size: 0.92rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.25s ease;
            text-decoration: none;
            border: none;
        }

        .btn-outline {
            background: transparent;
            color: #FFF;
            border: 1px solid var(--border-highlight);
        }

        .btn-outline:hover {
            border-color: var(--primary-accent);
            color: var(--primary-accent);
            background: rgba(0, 210, 255, 0.05);
        }

        .btn-primary {
            background: var(--primary-accent);
            color: #050B17;
            box-shadow: 0 0 20px var(--accent-glow);
        }

        .btn-primary:hover {
            background: var(--primary-hover);
            transform: translateY(-2px);
            box-shadow: 0 0 25px rgba(0, 210, 255, 0.45);
        }

        .btn-danger {
            background: rgba(239, 68, 68, 0.15);
            border: 1px solid var(--danger);
            color: #FFA4A4;
        }

        .btn-danger:hover {
            background: var(--danger);
            color: #FFF;
        }

        /* User Profile Pill */
        .user-tag {
            display: flex;
            align-items: center;
            gap: 10px;
            background: var(--bg-card);
            border: 1px solid var(--border);
            padding: 6px 14px;
            border-radius: 40px;
        }

        .role-badge {
            font-size: 0.7rem;
            text-transform: uppercase;
            padding: 2px 8px;
            border-radius: 4px;
            font-weight: 700;
        }

        .role-admin { background: #DC2626; color: #FFF; }
        .role-staff { background: #2563EB; color: #FFF; }
        .role-user { background: #059669; color: #FFF; }

        /* Hero Section */
        .hero {
            padding: 85px 0 60px;
            text-align: center;
        }

        .hero-pill {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(0, 210, 255, 0.08);
            border: 1px solid rgba(0, 210, 255, 0.3);
            color: var(--primary-accent);
            padding: 6px 16px;
            border-radius: 30px;
            font-size: 0.85rem;
            font-weight: 600;
            margin-bottom: 24px;
        }

        .hero h2 {
            font-size: 3.5rem;
            font-weight: 800;
            letter-spacing: -1.5px;
            line-height: 1.15;
            margin-bottom: 20px;
        }

        .hero h2 span {
            background: linear-gradient(135deg, #FFF 40%, var(--primary-accent) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .hero p {
            color: var(--text-secondary);
            font-size: 1.15rem;
            max-width: 720px;
            margin: 0 auto 36px;
        }

        /* Filter Tabs */
        .filter-tabs {
            display: flex;
            justify-content: center;
            gap: 12px;
            margin-bottom: 45px;
            flex-wrap: wrap;
        }

        .tab-btn {
            background: var(--bg-card);
            color: var(--text-secondary);
            border: 1px solid var(--border);
            padding: 10px 22px;
            border-radius: 30px;
            font-size: 0.92rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .tab-btn:hover {
            color: #FFF;
            border-color: var(--primary-accent);
        }

        .tab-btn.active {
            background: #FFF;
            color: #050B17;
            border-color: #FFF;
            box-shadow: 0 0 20px rgba(255, 255, 255, 0.3);
        }

        /* Product Grid */
        .product-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(360px, 1fr));
            gap: 28px;
            margin-bottom: 80px;
        }

        .product-card {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 24px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            transition: transform 0.25s ease, border-color 0.25s ease;
            position: relative;
            overflow: hidden;
        }

        .product-card:hover {
            transform: translateY(-5px);
            border-color: var(--border-highlight);
            background: var(--bg-card-hover);
        }

        .product-card::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, transparent, var(--primary-accent), transparent);
            opacity: 0;
            transition: opacity 0.3s;
        }

        .product-card:hover::before {
            opacity: 1;
        }

        .card-top {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 16px;
        }

        .cat-badge {
            font-size: 0.75rem;
            font-weight: 700;
            color: var(--primary-accent);
            text-transform: uppercase;
            letter-spacing: 0.8px;
            background: rgba(0, 210, 255, 0.1);
            padding: 4px 10px;
            border-radius: 6px;
        }

        .price-badge {
            font-size: 1.45rem;
            font-weight: 800;
            color: #FFF;
            font-family: 'JetBrains Mono', monospace;
        }

        .product-card h3 {
            font-size: 1.25rem;
            font-weight: 700;
            margin-bottom: 10px;
            color: #FFF;
        }

        .product-card p {
            font-size: 0.92rem;
            color: var(--text-secondary);
            margin-bottom: 20px;
            min-height: 65px;
        }

        .stack-list {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
            margin-bottom: 22px;
        }

        .stack-item {
            font-family: 'JetBrains Mono', monospace;
            font-size: 0.75rem;
            background: #050B17;
            border: 1px solid var(--border);
            padding: 3px 8px;
            border-radius: 4px;
            color: #CBD5E1;
        }

        .card-bottom {
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-top: 1px solid rgba(255, 255, 255, 0.06);
            padding-top: 18px;
        }

        .rating-text {
            font-size: 0.8rem;
            color: var(--text-muted);
        }

        /* Modals */
        .modal-overlay {
            position: fixed;
            inset: 0;
            background: rgba(5, 11, 23, 0.85);
            backdrop-filter: blur(12px);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 100;
            padding: 20px;
        }

        .modal-overlay.active {
            display: flex;
        }

        .modal-box {
            background: #0A152E;
            border: 1px solid var(--border-highlight);
            border-radius: 20px;
            width: 100%;
            max-width: 480px;
            box-shadow: 0 25px 60px rgba(0, 0, 0, 0.8);
            position: relative;
            overflow: hidden;
            animation: fadeIn 0.25s ease-out;
        }

        .modal-box.wide {
            max-width: 780px;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: scale(0.96); }
            to { opacity: 1; transform: scale(1); }
        }

        .modal-header {
            padding: 24px 28px;
            border-bottom: 1px solid var(--border);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .modal-header h3 {
            font-size: 1.3rem;
            font-weight: 700;
        }

        .close-btn {
            background: none;
            border: none;
            color: var(--text-secondary);
            font-size: 1.5rem;
            cursor: pointer;
            line-height: 1;
        }

        .close-btn:hover {
            color: #FFF;
        }

        .modal-body {
            padding: 28px;
        }

        /* Form Controls */
        .form-group {
            margin-bottom: 18px;
        }

        .form-group label {
            display: block;
            font-size: 0.85rem;
            font-weight: 600;
            color: #CBD5E1;
            margin-bottom: 8px;
        }

        .form-input, .form-select {
            width: 100%;
            background: #050B17;
            border: 1px solid var(--border);
            color: #FFF;
            padding: 12px 14px;
            border-radius: 8px;
            font-size: 0.95rem;
            outline: none;
            transition: border-color 0.2s;
        }

        .form-input:focus, .form-select:focus {
            border-color: var(--primary-accent);
            box-shadow: 0 0 10px rgba(0, 210, 255, 0.2);
        }

        .role-switch-container {
            display: flex;
            background: #050B17;
            border: 1px solid var(--border);
            border-radius: 8px;
            padding: 4px;
            margin-bottom: 22px;
        }

        .role-radio-btn {
            flex: 1;
            text-align: center;
            padding: 8px 12px;
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--text-secondary);
            cursor: pointer;
            border-radius: 6px;
            transition: all 0.2s;
        }

        .role-radio-btn.active {
            background: #FFF;
            color: #050B17;
        }

        /* OTP Input Boxes */
        .otp-group {
            display: flex;
            gap: 10px;
            justify-content: center;
            margin: 20px 0;
        }

        .otp-box {
            width: 48px;
            height: 54px;
            background: #050B17;
            border: 1px solid var(--border-highlight);
            text-align: center;
            font-size: 1.5rem;
            font-family: 'JetBrains Mono', monospace;
            font-weight: 700;
            color: var(--primary-accent);
            border-radius: 8px;
        }

        /* Admin Staff Table */
        .staff-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.88rem;
            margin-top: 15px;
        }

        .staff-table th, .staff-table td {
            padding: 12px 14px;
            text-align: left;
            border-bottom: 1px solid var(--border);
        }

        .staff-table th {
            color: var(--text-muted);
            text-transform: uppercase;
            font-size: 0.75rem;
            font-weight: 700;
        }

        /* Footer */
        footer {
            border-top: 1px solid var(--border);
            background: #040914;
            padding: 40px 0;
            color: var(--text-muted);
            font-size: 0.88rem;
        }

        .footer-inner {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 20px;
        }

        .footer-links a {
            color: var(--text-secondary);
            text-decoration: none;
            margin-left: 20px;
        }

        .footer-links a:hover {
            color: #FFF;
        }
    </style>
</head>
<body>

    <!-- Top Navigation -->
    <header>
        <div class="container nav-inner">
            <a href="index.php" class="brand-logo">
                <div class="brand-badge">RMT</div>
                <div class="brand-text">
                    <h1>RMT INFO TECH</h1>
                    <span>Digital Marketplace</span>
                </div>
            </a>

            <nav class="menu-links">
                <a href="index.php" class="active">Marketplace</a>
                <a href="#projects" onclick="filterCatalog('all')">All Products</a>
                <a href="#erp" onclick="filterCatalog('erp')">ERPs</a>
                <a href="#apps" onclick="filterCatalog('mobile')">Mobile Apps</a>
                <a href="#web" onclick="filterCatalog('website')">Web & SaaS</a>
            </nav>

            <div class="nav-actions">
                <?php if ($currentUser): ?>
                    <div class="user-tag">
                        <span><?= htmlspecialchars($currentUser['name']) ?></span>
                        <span class="role-badge role-<?= htmlspecialchars($currentUser['role']) ?>">
                            <?= strtoupper(htmlspecialchars($currentUser['role'])) ?>
                        </span>
                    </div>

                    <?php if ($currentUser['role'] === 'admin'): ?>
                        <button class="btn btn-outline" onclick="openModal('adminStaffModal')">
                            ⚙ Staff Control
                        </button>
                    <?php endif; ?>

                    <a href="index.php?action=logout" class="btn btn-danger">Log Out</a>
                <?php else: ?>
                    <button class="btn btn-outline" onclick="openModal('loginModal')">Sign In</button>
                    <button class="btn btn-primary" onclick="openModal('registerModal')">Create Account</button>
                <?php endif; ?>
            </div>
        </div>
    </header>

    <!-- Main Hero -->
    <section class="hero">
        <div class="container">
            <div class="hero-pill">
                <span>⚡ Next-Generation Digital Asset Platform</span>
            </div>
            <h2>Production-Ready Architecture,<br><span>Zero Delay Delivery.</span></h2>
            <p>Direct ownership of enterprise-grade Web systems, comprehensive institutional ERP suites, and native Kotlin mobile apps with verified handover.</p>

            <!-- Filter Buttons -->
            <div class="filter-tabs" id="catalog-filters">
                <button class="tab-btn active" onclick="filterCatalog('all', this)">All Solutions</button>
                <button class="tab-btn" onclick="filterCatalog('erp', this)">Institutional ERPs</button>
                <button class="tab-btn" onclick="filterCatalog('website', this)">Websites & SaaS</button>
                <button class="tab-btn" onclick="filterCatalog('mobile', this)">Native Mobile Apps</button>
            </div>
        </div>
    </section>

    <!-- Marketplace Products Grid -->
    <main class="container">
        <div class="product-grid" id="productContainer">
            <?php foreach ($products as $p): ?>
                <div class="product-card" data-category="<?= $p['category'] ?>">
                    <div>
                        <div class="card-top">
                            <span class="cat-badge"><?= $p['badge'] ?></span>
                            <span class="price-badge"><?= $p['price'] ?></span>
                        </div>
                        <h3><?= htmlspecialchars($p['title']) ?></h3>
                        <p><?= htmlspecialchars($p['desc']) ?></p>
                        <div class="stack-list">
                            <?php foreach ($p['stack'] as $tech): ?>
                                <span class="stack-item"><?= htmlspecialchars($tech) ?></span>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <div class="card-bottom">
                        <span class="rating-text">★ <?= $p['rating'] ?></span>
                        <button class="btn btn-primary" style="padding: 7px 14px; font-size: 0.85rem;" onclick="alert('Proceeding to live demo & escrow transfer for: <?= addslashes($p['title']) ?>')">
                            Acquire Now →
                        </button>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </main>

    <!-- ==================== MODALS ==================== -->

    <!-- 1. LOGIN MODAL (Admin / Staff / User) -->
    <div class="modal-overlay" id="loginModal">
        <div class="modal-box">
            <div class="modal-header">
                <h3>System Sign In</h3>
                <button class="close-btn" onclick="closeModal('loginModal')">&times;</button>
            </div>
            <div class="modal-body">
                <!-- Role Selector -->
                <div class="role-switch-container">
                    <div class="role-radio-btn active" onclick="setLoginRole('user', this)">User</div>
                    <div class="role-radio-btn" onclick="setLoginRole('staff', this)">Staff</div>
                    <div class="role-radio-btn" onclick="setLoginRole('admin', this)">Admin</div>
                </div>

                <form onsubmit="handleLogin(event)">
                    <input type="hidden" id="loginSelectedRole" value="user">

                    <div class="form-group">
                        <label id="loginIdLabel">Email or Mobile Number</label>
                        <input type="text" id="loginIdentifier" class="form-input" placeholder="e.g. +91 9876543210 or user@example.com" required>
                    </div>

                    <div class="form-group">
                        <label>Secure Password</label>
                        <input type="password" id="loginPassword" class="form-input" placeholder="••••••••••••" required>
                    </div>

                    <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 10px; padding: 12px;">
                        Authenticate as <span id="loginBtnRoleLabel" style="text-transform: capitalize; margin-left: 4px;">User</span>
                    </button>

                    <div style="text-align: center; margin-top: 16px; font-size: 0.85rem; color: var(--text-secondary);">
                        Don't have an account yet? 
                        <a href="javascript:void(0)" onclick="switchModal('loginModal', 'registerModal')" style="color: var(--primary-accent); text-decoration: none; font-weight: 600;">Register Here</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- 2. USER REGISTRATION WITH PHONE OTP VERIFICATION -->
    <div class="modal-overlay" id="registerModal">
        <div class="modal-box">
            <div class="modal-header">
                <h3>Create User Account</h3>
                <button class="close-btn" onclick="closeModal('registerModal')">&times;</button>
            </div>
            <div class="modal-body">
                <!-- Step 1: Input details & Phone -->
                <div id="regStep1">
                    <form onsubmit="sendRegistrationOtp(event)">
                        <div class="form-group">
                            <label>Full Legal Name</label>
                            <input type="text" id="regFullName" class="form-input" placeholder="e.g. John Doe" required>
                        </div>

                        <div class="form-group">
                            <label>Email Address</label>
                            <input type="email" id="regEmail" class="form-input" placeholder="john@example.com" required>
                        </div>

                        <div class="form-group">
                            <label>Mobile Number (For Mandatory OTP Verification)</label>
                            <div style="display: flex; gap: 8px;">
                                <input type="text" value="+91" readonly class="form-input" style="width: 75px; text-align: center; color: var(--primary-accent); font-weight: 600;">
                                <input type="tel" id="regPhone" class="form-input" placeholder="9876543210" pattern="[0-9]{10}" maxlength="10" required>
                            </div>
                            <span style="font-size: 0.75rem; color: var(--text-muted); margin-top: 4px; display: block;">A 6-digit OTP passcode will be dispatched to this number.</span>
                        </div>

                        <div class="form-group">
                            <label>Account Password</label>
                            <input type="password" id="regPassword" class="form-input" placeholder="••••••••••••" minlength="6" required>
                        </div>

                        <button type="submit" class="btn btn-primary" style="width: 100%; padding: 12px; margin-top: 10px;">
                            Verify Phone & Continue →
                        </button>
                    </form>
                </div>

                <!-- Step 2: OTP Verification Screen -->
                <div id="regStep2" style="display: none; text-align: center;">
                    <div style="margin-bottom: 12px; font-size: 2rem;">📱</div>
                    <h4 style="font-size: 1.15rem; margin-bottom: 6px;">Phone Verification Code</h4>
                    <p style="font-size: 0.85rem; color: var(--text-secondary);">
                        We sent a 6-digit verification code to <br><b id="otpTargetPhone" style="color: #FFF;"></b>
                    </p>

                    <div class="otp-group">
                        <input type="text" maxlength="1" class="otp-box" oninput="focusNext(this, 0)" autofocus>
                        <input type="text" maxlength="1" class="otp-box" oninput="focusNext(this, 1)">
                        <input type="text" maxlength="1" class="otp-box" oninput="focusNext(this, 2)">
                        <input type="text" maxlength="1" class="otp-box" oninput="focusNext(this, 3)">
                        <input type="text" maxlength="1" class="otp-box" oninput="focusNext(this, 4)">
                        <input type="text" maxlength="1" class="otp-box" oninput="focusNext(this, 5)">
                    </div>

                    <div style="font-size: 0.8rem; color: var(--text-muted); margin-bottom: 20px;">
                        Resend code in <span id="otpTimer" style="color: var(--primary-accent); font-weight: 600;">0:59</span>
                    </div>

                    <button type="button" class="btn btn-primary" style="width: 100%; padding: 12px;" onclick="confirmOtpAndActivate()">
                        Verify & Complete Registration
                    </button>
                    
                    <button type="button" class="btn btn-outline" style="width: 100%; margin-top: 10px;" onclick="backToRegStep1()">
                        ← Change Number
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- 3. ADMIN PORTAL MODAL (Staff Management & Add Staff) -->
    <div class="modal-overlay" id="adminStaffModal">
        <div class="modal-box wide">
            <div class="modal-header">
                <h3>Admin Command Center — Staff Management</h3>
                <button class="close-btn" onclick="closeModal('adminStaffModal')">&times;</button>
            </div>
            <div class="modal-body">
                <!-- Add Staff Option Form -->
                <div style="background: #050B17; border: 1px solid var(--border); border-radius: 12px; padding: 20px; margin-bottom: 24px;">
                    <h4 style="color: var(--primary-accent); margin-bottom: 14px; font-size: 1rem;">+ Add New Technical Staff Officer</h4>
                    <form onsubmit="handleCreateStaff(event)" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 14px; align-items: end;">
                        <div>
                            <label style="font-size: 0.8rem; color: #CBD5E1;">Staff Name</label>
                            <input type="text" id="staffName" class="form-input" placeholder="e.g. Alex Henderson" required>
                        </div>
                        <div>
                            <label style="font-size: 0.8rem; color: #CBD5E1;">Official Email</label>
                            <input type="email" id="staffEmail" class="form-input" placeholder="alex@rmtinfotech.com" required>
                        </div>
                        <div>
                            <label style="font-size: 0.8rem; color: #CBD5E1;">Department / Role</label>
                            <select id="staffDept" class="form-select">
                                <option value="ERP Operations">ERP Operations</option>
                                <option value="Mobile Core">Mobile Core (Android)</option>
                                <option value="FullStack & Cloud">FullStack & Cloud</option>
                                <option value="Security / Escrow">Escrow & Verification</option>
                            </select>
                        </div>
                        <div>
                            <button type="submit" class="btn btn-primary" style="width: 100%;">Create Staff Account</button>
                        </div>
                    </form>
                </div>

                <!-- Active Staff Directory -->
                <h4 style="font-size: 0.95rem; margin-bottom: 8px;">Active Staff Directory</h4>
                <div style="overflow-x: auto;">
                    <table class="staff-table" id="staffDirectoryTable">
                        <thead>
                            <tr>
                                <th>Staff Member</th>
                                <th>Department</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><b>David K.</b><br><span style="font-size: 0.75rem; color: var(--text-muted);">david@rmtinfotech.com</span></td>
                                <td>ERP Engineering Lead</td>
                                <td><span style="color: var(--success); font-weight: 600;">Active</span></td>
                                <td><button class="btn btn-danger" style="padding: 4px 10px; font-size: 0.75rem;" onclick="removeStaff(this)">Revoke</button></td>
                            </tr>
                            <tr>
                                <td><b>Rohit Sharma</b><br><span style="font-size: 0.75rem; color: var(--text-muted);">rohit@rmtinfotech.com</span></td>
                                <td>Native Android / Kotlin</td>
                                <td><span style="color: var(--success); font-weight: 600;">Active</span></td>
                                <td><button class="btn btn-danger" style="padding: 4px 10px; font-size: 0.75rem;" onclick="removeStaff(this)">Revoke</button></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer>
        <div class="container footer-inner">
            <div>
                © <?= date('Y') ?> <b>RMT INFO TECH</b>. All source rights reserved.
            </div>
            <div class="footer-links">
                <a href="javascript:void(0)" onclick="setLoginRole('admin'); openModal('loginModal')">Admin Access</a>
                <a href="javascript:void(0)" onclick="setLoginRole('staff'); openModal('loginModal')">Staff Portal</a>
                <a href="javascript:void(0)" onclick="openModal('registerModal')">Buyer Verification</a>
            </div>
        </div>
    </footer>

    <!-- JavaScript Handlers -->
    <script>
        // Modal Handlers
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

        // Close modal on click outside
        window.onclick = function(event) {
            if (event.target.classList.contains('modal-overlay')) {
                event.target.classList.remove('active');
            }
        };

        // Filter Catalog (All / ERP / Website / Mobile)
        function filterCatalog(category, btnElement) {
            if (btnElement) {
                document.querySelectorAll('#catalog-filters .tab-btn').forEach(b => b.classList.remove('active'));
                btnElement.classList.add('active');
            }

            const cards = document.querySelectorAll('#productContainer .product-card');
            cards.forEach(card => {
                if (category === 'all' || card.getAttribute('data-category') === category) {
                    card.style.display = 'flex';
                } else {
                    card.style.display = 'none';
                }
            });
        }

        // Role Switcher inside Login Modal
        function setLoginRole(role, btnElement) {
            document.getElementById('loginSelectedRole').value = role;
            document.getElementById('loginBtnRoleLabel').innerText = role;

            if (btnElement) {
                document.querySelectorAll('.role-radio-btn').forEach(b => b.classList.remove('active'));
                btnElement.classList.add('active');
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
            const identifier = document.getElementById('loginIdentifier').value;

            // Redirects to mock session
            window.location.href = `index.php?mock_role=${role}`;
        }

        // User Registration with OTP
        let otpCountdown;
        function sendRegistrationOtp(e) {
            e.preventDefault();
            const phone = document.getElementById('regPhone').value;
            if (phone.length !== 10) {
                alert("Please enter a valid 10-digit mobile phone number.");
                return;
            }

            document.getElementById('otpTargetPhone').innerText = `+91 ${phone}`;
            document.getElementById('regStep1').style.display = 'none';
            document.getElementById('regStep2').style.display = 'block';

            // Start countdown
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
            const boxes = document.querySelectorAll('.otp-box');
            if (input.value.length === 1 && index < boxes.length - 1) {
                boxes[index + 1].focus();
            }
        }

        function confirmOtpAndActivate() {
            const boxes = document.querySelectorAll('.otp-box');
            let enteredOtp = "";
            boxes.forEach(b => enteredOtp += b.value);

            if (enteredOtp.length < 6) {
                alert("Please enter the complete 6-digit OTP code sent to your phone.");
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
                <td><b>${name}</b><br><span style="font-size: 0.75rem; color: var(--text-muted);">${email}</span></td>
                <td>${dept}</td>
                <td><span style="color: var(--success); font-weight: 600;">Active</span></td>
                <td><button class="btn btn-danger" style="padding: 4px 10px; font-size: 0.75rem;" onclick="removeStaff(this)">Revoke</button></td>
            `;

            alert(`Staff account for ${name} registered successfully.`);
            document.getElementById('staffName').value = '';
            document.getElementById('staffEmail').value = '';
        }

        function removeStaff(btn) {
            if (confirm("Are you sure you want to revoke this staff member's clearance?")) {
                const row = btn.closest('tr');
                row.remove();
            }
        }
    </script>
</body>
</html>

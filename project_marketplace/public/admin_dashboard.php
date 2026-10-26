<?php
declare(strict_types=1);
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

require_once __DIR__ . '/../config/database.php';

$pdo = getDatabaseConnection();

// Fetch summary metrics
$totalRevenue = (float)$pdo->query("SELECT COALESCE(SUM(agreed_price), 0) FROM transactions WHERE escrow_status = 'completed'")->fetchColumn();
$activeOrders = (int)$pdo->query("SELECT COUNT(*) FROM transactions WHERE escrow_status IN ('initiated', 'escrow_pending')")->fetchColumn();
$pendingListings = (int)$pdo->query("SELECT COUNT(*) FROM listings WHERE status = 'available'")->fetchColumn();

// Fetch recent listings
$stmt = $pdo->query("SELECT * FROM listings ORDER BY created_at DESC LIMIT 10");
$recentListings = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en" class="bg-slate-50 text-slate-900 min-h-screen">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard &bull; RMT INFO TECH</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="assets/css/theme.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@500;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .font-mono { font-family: 'JetBrains Mono', monospace; }
    </style>
</head>
<body class="admin-page bg-[#F8FAFC] text-slate-800 min-h-screen flex antialiased">

    <!-- Unified Real Sidebar -->
    <?php require_once __DIR__ . '/sidebar.php'; ?>

    <!-- Main Content Area -->
    <div class="flex-1 flex flex-col min-w-0 min-h-screen">
        
        <!-- White Topbar -->
        <header class="h-16 bg-white border-b border-slate-200 px-8 flex items-center justify-between sticky top-0 z-30 shadow-sm">
            <h1 class="text-lg font-bold text-[#0A192F]">Dashboard Overview</h1>
            <a href="add_project.php" class="bg-[#0F2744] hover:bg-[#16365D] text-white font-bold text-xs px-4 py-2 rounded-xl transition shadow-sm">
                + Add New Product
            </a>
        </header>

        <!-- Main Body -->
        <main class="p-8 space-y-8 flex-1">
            
            <!-- 3 Stat Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm">
                    <span class="text-[11px] uppercase tracking-wider font-bold text-slate-400">Total Revenue</span>
                    <div class="text-3xl font-extrabold text-[#0A192F] mt-2 font-mono">$<?= number_format($totalRevenue, 2) ?></div>
                </div>

                <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm">
                    <span class="text-[11px] uppercase tracking-wider font-bold text-slate-400">Active Orders</span>
                    <div class="text-3xl font-extrabold text-[#0A192F] mt-2 font-mono"><?= $activeOrders ?></div>
                </div>

                <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm">
                    <span class="text-[11px] uppercase tracking-wider font-bold text-slate-400">Available Listings</span>
                    <div class="text-3xl font-extrabold text-[#0A192F] mt-2 font-mono"><?= $pendingListings ?></div>
                </div>
            </div>

            <!-- Table Card -->
            <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                    <h2 class="text-sm font-bold text-[#0A192F]">Recent Products</h2>
                    <span class="text-xs text-slate-400"><?= count($recentListings) ?> listed</span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50 text-[11px] uppercase font-bold text-slate-500 border-b border-slate-200">
                                <th class="py-3 px-6">Product</th>
                                <th class="py-3 px-6">Category</th>
                                <th class="py-3 px-6">Status</th>
                                <th class="py-3 px-6">Total</th>
                                <th class="py-3 px-6 text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-xs">
                            <?php if (empty($recentListings)): ?>
                                <tr>
                                    <td colspan="5" class="py-8 text-center text-slate-400">No products uploaded yet.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($recentListings as $p): ?>
                                    <tr class="hover:bg-slate-50 transition">
                                        <td class="py-4 px-6 font-semibold text-[#0A192F]">
                                            <?= htmlspecialchars($p['title']) ?>
                                        </td>
                                        <td class="py-4 px-6">
                                            <span class="text-[10px] font-bold font-mono px-2 py-0.5 rounded bg-blue-50 text-[#0F2744] border border-blue-100">
                                                <?= htmlspecialchars($p['category']) ?>
                                            </span>
                                        </td>
                                        <td class="py-4 px-6">
                                            <?php if ($p['status'] === 'available'): ?>
                                                <span class="text-[10px] text-blue-700 bg-blue-50 px-2.5 py-0.5 rounded-full border border-blue-200 font-semibold">Available</span>
                                            <?php else: ?>
                                                <span class="text-[10px] text-slate-600 bg-slate-100 px-2.5 py-0.5 rounded-full border border-slate-200 font-semibold"><?= htmlspecialchars($p['status']) ?></span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="py-4 px-6 font-mono font-bold text-slate-900">
                                            $<?= number_format((float)$p['asking_price'], 2) ?>
                                        </td>
                                        <td class="py-4 px-6 text-right">
                                            <a href="project.php?slug=<?= urlencode($p['slug']) ?>" class="px-3 py-1 rounded-lg border border-slate-300 hover:border-[#0F2744] text-[#0F2744] font-semibold transition">
                                                View
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </main>
    </div>

</body>
</html>
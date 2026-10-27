<?php
declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/database.php';

try {
    $pdo = getDatabaseConnection();

    // 1. Fetch summary metrics safely
    $totalRevenue = 0.0;
    $activeOrders = 0;
    $pendingListings = 0;

    // Check if transactions table exists before querying
    $hasTransactions = $pdo->query("SHOW TABLES LIKE 'transactions'")->rowCount() > 0;
    if ($hasTransactions) {
        $totalRevenue = (float)$pdo->query("SELECT COALESCE(SUM(agreed_price), 0) FROM transactions WHERE escrow_status = 'completed'")->fetchColumn();
        $activeOrders = (int)$pdo->query("SELECT COUNT(*) FROM transactions WHERE escrow_status IN ('initiated', 'escrow_pending')")->fetchColumn();
    }

    // Check listings count
    $hasListings = $pdo->query("SHOW TABLES LIKE 'listings'")->rowCount() > 0;
    if ($hasListings) {
        $pendingListings = (int)$pdo->query("SELECT COUNT(*) FROM listings WHERE status = 'available'")->fetchColumn();
        $stmt = $pdo->query("SELECT * FROM listings ORDER BY created_at DESC LIMIT 15");
        $recentListings = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } else {
        $recentListings = [];
    }

    // Count registered users
    $hasUsers = $pdo->query("SHOW TABLES LIKE 'users'")->rowCount() > 0;
    $totalUsers = $hasUsers ? (int)$pdo->query("SELECT COUNT(*) FROM users")->fetchColumn() : 0;

} catch (Throwable $e) {
    error_log("Admin Dashboard DB Error: " . $e->getMessage());
    $errorMsg = $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en" class="bg-slate-950 text-slate-100 antialiased selection:bg-blue-600 selection:text-white">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Executive Admin Dashboard &bull; RMT INFO TECH</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@500;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .font-mono { font-family: 'JetBrains Mono', monospace; }
    </style>
</head>
<body class="min-h-screen bg-[#080d1a] text-slate-200 flex flex-col md:flex-row">

    <!-- SIDEBAR -->
    <aside class="w-full md:w-64 bg-[#0B132B] border-r border-slate-800 flex flex-col justify-between shrink-0">
        <div>
            <!-- Brand -->
            <div class="h-16 flex items-center gap-3 px-6 border-b border-slate-800">
                <div class="w-8 h-8 rounded-xl bg-blue-600 flex items-center justify-center font-extrabold text-white text-sm shadow-md shadow-blue-600/30">
                    RMT
                </div>
                <div>
                    <span class="font-bold text-white tracking-wide text-sm block">Marketplace</span>
                    <span class="text-[10px] text-blue-400 font-mono tracking-widest uppercase">Admin Panel</span>
                </div>
            </div>

            <!-- Nav Links -->
            <nav class="p-4 space-y-1 text-xs font-semibold">
                <a href="admin_dashboard.php" class="flex items-center gap-3 px-4 py-3 rounded-xl bg-blue-600/10 text-blue-400 border border-blue-500/20">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                    <span>Dashboard</span>
                </a>
                <a href="projects.php" class="flex items-center gap-3 px-4 py-3 rounded-xl text-slate-400 hover:text-white hover:bg-slate-800/50 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                    <span>Browse Public Catalog</span>
                </a>
                <a href="add_project.php" class="flex items-center gap-3 px-4 py-3 rounded-xl text-slate-400 hover:text-white hover:bg-slate-800/50 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    <span>Add New Project</span>
                </a>
            </nav>
        </div>

        <!-- Admin user profile & exit -->
        <div class="p-4 border-t border-slate-800">
            <div class="flex items-center justify-between text-xs">
                <div>
                    <span class="text-white font-bold block"><?= htmlspecialchars($_SESSION['username'] ?? 'Administrator') ?></span>
                    <span class="text-[10px] text-emerald-400 font-mono">Live Database Ready</span>
                </div>
                <a href="logout.php" class="text-slate-400 hover:text-rose-400 transition" title="Log out">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                </a>
            </div>
        </div>
    </aside>

    <!-- MAIN DASHBOARD CONTENT -->
    <div class="flex-1 flex flex-col min-w-0">
        
        <!-- Top Navigation Bar -->
        <header class="h-16 bg-[#0B132B]/60 backdrop-blur border-b border-slate-800 px-8 flex items-center justify-between sticky top-0 z-30">
            <div>
                <h1 class="text-base font-extrabold text-white">System Executive Dashboard</h1>
                <p class="text-[11px] text-slate-400">Overview of software assets, transactions, and user counts</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="add_project.php" class="bg-blue-600 hover:bg-blue-500 text-white font-bold text-xs px-4 py-2 rounded-xl transition shadow-md shadow-blue-600/20 flex items-center gap-1.5">
                    <span>+ Add Listing</span>
                </a>
            </div>
        </header>

        <!-- Body Content -->
        <main class="p-6 md:p-8 space-y-8 flex-1">

            <?php if (!empty($errorMsg)): ?>
                <div class="p-4 bg-rose-500/10 border border-rose-500/30 rounded-2xl text-xs text-rose-300">
                    <strong>Notice:</strong> <?= htmlspecialchars($errorMsg) ?>
                </div>
            <?php endif; ?>

            <!-- Metric Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
                <div class="bg-[#0f172a] border border-slate-800 rounded-2xl p-6 shadow-sm">
                    <span class="text-[11px] uppercase tracking-wider font-bold text-slate-400 block">Total Escrow Volume</span>
                    <div class="text-2xl font-extrabold text-emerald-400 mt-2 font-mono">$<?= number_format($totalRevenue, 2) ?></div>
                    <span class="text-[10px] text-slate-500 mt-1 block">Completed handovers</span>
                </div>

                <div class="bg-[#0f172a] border border-slate-800 rounded-2xl p-6 shadow-sm">
                    <span class="text-[11px] uppercase tracking-wider font-bold text-slate-400 block">Active Escrow Orders</span>
                    <div class="text-2xl font-extrabold text-blue-400 mt-2 font-mono"><?= $activeOrders ?></div>
                    <span class="text-[10px] text-slate-500 mt-1 block">Pending settlements</span>
                </div>

                <div class="bg-[#0f172a] border border-slate-800 rounded-2xl p-6 shadow-sm">
                    <span class="text-[11px] uppercase tracking-wider font-bold text-slate-400 block">Available Listings</span>
                    <div class="text-2xl font-extrabold text-white mt-2 font-mono"><?= $pendingListings ?></div>
                    <span class="text-[10px] text-slate-500 mt-1 block">In public directory</span>
                </div>

                <div class="bg-[#0f172a] border border-slate-800 rounded-2xl p-6 shadow-sm">
                    <span class="text-[11px] uppercase tracking-wider font-bold text-slate-400 block">Total Accounts</span>
                    <div class="text-2xl font-extrabold text-purple-400 mt-2 font-mono"><?= $totalUsers ?></div>
                    <span class="text-[10px] text-slate-500 mt-1 block">Registered in database</span>
                </div>
            </div>

            <!-- Products Table -->
            <div class="bg-[#0f172a] border border-slate-800 rounded-2xl shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-800 flex items-center justify-between">
                    <div>
                        <h2 class="text-sm font-bold text-white">Current Project Listings</h2>
                        <span class="text-xs text-slate-400">Total <?= count($recentListings) ?> projects loaded from Aiven MySQL</span>
                    </div>
                    <a href="projects.php" class="text-xs text-blue-400 hover:text-blue-300 font-semibold underline">
                        View Public Directory &rarr;
                    </a>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-900/60 text-[10px] uppercase font-bold text-slate-400 border-b border-slate-800">
                                <th class="py-3 px-6">Project Title</th>
                                <th class="py-3 px-6">Category</th>
                                <th class="py-3 px-6">Tech Stack</th>
                                <th class="py-3 px-6">Price</th>
                                <th class="py-3 px-6">Status</th>
                                <th class="py-3 px-6 text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-800/80 text-xs text-slate-300">
                            <?php if (empty($recentListings)): ?>
                                <tr>
                                    <td colspan="6" class="py-10 text-center text-slate-500">
                                        No projects in the database yet. Click <a href="add_project.php" class="text-blue-400 underline font-semibold">Add Listing</a> to create your first asset.
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($recentListings as $p): ?>
                                    <tr class="hover:bg-slate-800/40 transition">
                                        <td class="py-4 px-6 font-semibold text-white">
                                            <?= htmlspecialchars($p['title'] ?? 'Untitled') ?>
                                        </td>
                                        <td class="py-4 px-6">
                                            <span class="text-[10px] font-bold font-mono px-2 py-0.5 rounded bg-blue-500/10 text-blue-300 border border-blue-500/20">
                                                <?= htmlspecialchars($p['category'] ?? 'Asset') ?>
                                            </span>
                                        </td>
                                        <td class="py-4 px-6 font-mono text-[11px] text-slate-400">
                                            <?= htmlspecialchars($p['primary_tech_stack'] ?? 'N/A') ?>
                                        </td>
                                        <td class="py-4 px-6 font-mono font-bold text-white">
                                            $<?= number_format((float)($p['asking_price'] ?? 0), 2) ?>
                                        </td>
                                        <td class="py-4 px-6">
                                            <?php if (($p['status'] ?? '') === 'available'): ?>
                                                <span class="text-[10px] text-emerald-400 bg-emerald-500/10 px-2.5 py-0.5 rounded-full border border-emerald-500/20 font-semibold">Available</span>
                                            <?php else: ?>
                                                <span class="text-[10px] text-slate-400 bg-slate-800 px-2.5 py-0.5 rounded-full border border-slate-700 font-semibold"><?= htmlspecialchars($p['status'] ?? 'Draft') ?></span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="py-4 px-6 text-right space-x-2">
                                            <a href="project.php?slug=<?= urlencode((string)($p['slug'] ?? '')) ?>" class="px-3 py-1 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-200 border border-slate-700 font-semibold transition text-xs">
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

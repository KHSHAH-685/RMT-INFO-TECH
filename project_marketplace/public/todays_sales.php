<?php
declare(strict_types=1);
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

require_once __DIR__ . '/../config/database.php';

$pdo = getDatabaseConnection();

// Fetch today's sales metrics (transactions created today)
$today = date('Y-m-d');

// Today's total gross volume
$todayStmt = $pdo->prepare("
    SELECT 
        COALESCE(SUM(agreed_price), 0) AS total_revenue,
        COUNT(id) AS total_orders,
        COUNT(CASE WHEN escrow_status = 'completed' THEN 1 END) AS completed_orders,
        COUNT(CASE WHEN escrow_status IN ('initiated', 'escrow_pending') THEN 1 END) AS pending_orders
    FROM transactions 
    WHERE DATE(created_at) = :today
");
$todayStmt->execute(['today' => $today]);
$metrics = $todayStmt->fetch() ?: [
    'total_revenue'    => 0,
    'total_orders'     => 0,
    'completed_orders' => 0,
    'pending_orders'   => 0
];

// Fetch today's order transactions list joined with listing title
$txStmt = $pdo->prepare("
    SELECT 
        t.*,
        l.title AS project_title,
        l.category AS project_category
    FROM transactions t
    LEFT JOIN listings l ON t.listing_id = l.id
    WHERE DATE(t.created_at) = :today
    ORDER BY t.created_at DESC
");
$txStmt->execute(['today' => $today]);
$todaysSales = $txStmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-50 text-slate-900">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Today's Sales &bull; RMT INFO TECH</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/theme.css">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .font-mono { font-family: 'JetBrains Mono', monospace; }
    </style>
</head>
<body class="admin-page min-h-full flex bg-[#F8FAFC] text-slate-800 antialiased">

    <!-- Unified Navy Sidebar -->
    <?php require_once __DIR__ . '/sidebar.php'; ?>

    <!-- Main Workspace -->
    <div class="flex-1 flex flex-col min-w-0 min-h-screen">

        <!-- Top Navigation Bar -->
        <header class="h-16 bg-white border-b border-slate-200 px-8 flex items-center justify-between sticky top-0 z-30 shadow-sm">
            <div class="flex items-center gap-3">
                <h1 class="text-sm font-bold text-[#0A192F]">Today's Sales & Transactions</h1>
                <span class="text-[11px] font-mono px-2.5 py-0.5 rounded-full bg-blue-50 text-[#0F2744] border border-blue-100 font-semibold">
                    <?= date('F j, Y') ?>
                </span>
            </div>

            <div class="flex items-center gap-3">
                <a href="payment_status.php" class="text-xs border border-slate-300 hover:bg-slate-50 text-[#0F2744] font-semibold px-3.5 py-1.5 rounded-xl transition">
                    View Escrow Status &rarr;
                </a>
            </div>
        </header>

        <!-- Main Body -->
        <main class="p-8 space-y-8 flex-1">

            <!-- Metrics Summary Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-4 gap-6">
                <!-- Today's Revenue -->
                <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm">
                    <span class="text-[11px] uppercase tracking-wider font-bold text-slate-400">Today's Revenue</span>
                    <div class="text-3xl font-extrabold text-[#0A192F] mt-2 font-mono">
                        $<?= number_format((float)$metrics['total_revenue'], 2) ?>
                    </div>
                    <span class="text-xs text-slate-500 mt-1 block">Gross transaction volume</span>
                </div>

                <!-- Today's Total Deals -->
                <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm">
                    <span class="text-[11px] uppercase tracking-wider font-bold text-slate-400">Total Deals</span>
                    <div class="text-3xl font-extrabold text-blue-600 mt-2 font-mono">
                        <?= (int)$metrics['total_orders'] ?>
                    </div>
                    <span class="text-xs text-slate-500 mt-1 block">Orders created today</span>
                </div>

                <!-- Completed Escrows -->
                <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm">
                    <span class="text-[11px] uppercase tracking-wider font-bold text-slate-400">Completed</span>
                    <div class="text-3xl font-extrabold text-emerald-600 mt-2 font-mono">
                        <?= (int)$metrics['completed_orders'] ?>
                    </div>
                    <span class="text-xs text-emerald-700 font-medium mt-1 block">Funds released</span>
                </div>

                <!-- Pending Escrows -->
                <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm">
                    <span class="text-[11px] uppercase tracking-wider font-bold text-slate-400">Pending Escrow</span>
                    <div class="text-3xl font-extrabold text-amber-600 mt-2 font-mono">
                        <?= (int)$metrics['pending_orders'] ?>
                    </div>
                    <span class="text-xs text-amber-700 font-medium mt-1 block">Awaiting completion</span>
                </div>
            </div>

            <!-- Table Card -->
            <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                    <div>
                        <h2 class="text-sm font-bold text-[#0A192F]">Today's Recorded Transactions</h2>
                        <p class="text-xs text-slate-500 mt-0.5">Real-time acquisitions placed through escrow today.</p>
                    </div>
                    <span class="text-xs text-slate-400 font-mono"><?= count($todaysSales) ?> records</span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50 text-[11px] uppercase font-bold text-slate-500 border-b border-slate-200">
                                <th class="py-3 px-6">Transaction UUID</th>
                                <th class="py-3 px-6">Project Title</th>
                                <th class="py-3 px-6">Agreed Amount</th>
                                <th class="py-3 px-6">Escrow Status</th>
                                <th class="py-3 px-6">Time</th>
                                <th class="py-3 px-6 text-right">Details</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-xs">
                            <?php if (empty($todaysSales)): ?>
                                <tr>
                                    <td colspan="6" class="py-12 text-center text-slate-400">
                                        No sales or checkout transactions recorded yet today.
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($todaysSales as $sale): ?>
                                    <tr class="hover:bg-slate-50 transition">
                                        <td class="py-4 px-6 font-mono text-slate-500 text-[11px]">
                                            <?= htmlspecialchars(substr($sale['transaction_uuid'] ?? 'N/A', 0, 14)) ?>...
                                        </td>
                                        <td class="py-4 px-6">
                                            <div class="font-semibold text-[#0A192F]">
                                                <?= htmlspecialchars($sale['project_title'] ?? 'Listing #' . $sale['listing_id']) ?>
                                            </div>
                                            <span class="text-[10px] text-slate-400 font-mono">
                                                <?= htmlspecialchars($sale['project_category'] ?? 'Asset') ?>
                                            </span>
                                        </td>
                                        <td class="py-4 px-6 font-mono font-bold text-slate-900 text-sm">
                                            $<?= number_format((float)($sale['agreed_price'] ?? 0), 2) ?>
                                        </td>
                                        <td class="py-4 px-6">
                                            <?php 
                                            $status = strtolower($sale['escrow_status'] ?? 'initiated');
                                            if ($status === 'completed'): 
                                            ?>
                                                <span class="text-[10px] text-emerald-700 bg-emerald-50 border border-emerald-200 px-2.5 py-0.5 rounded-full font-semibold">Completed</span>
                                            <?php elseif (in_array($status, ['initiated', 'escrow_pending'], true)): ?>
                                                <span class="text-[10px] text-amber-700 bg-amber-50 border border-amber-200 px-2.5 py-0.5 rounded-full font-semibold">In Escrow</span>
                                            <?php else: ?>
                                                <span class="text-[10px] text-slate-600 bg-slate-100 border border-slate-200 px-2.5 py-0.5 rounded-full font-semibold"><?= htmlspecialchars(ucfirst($status)) ?></span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="py-4 px-6 font-mono text-slate-500 text-[11px]">
                                            <?= date('h:i A', strtotime($sale['created_at'])) ?>
                                        </td>
                                        <td class="py-4 px-6 text-right">
                                            <a href="payment_status.php" class="px-3 py-1 rounded-lg border border-slate-300 hover:border-[#0F2744] text-[#0F2744] font-semibold transition">
                                                Inspect
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
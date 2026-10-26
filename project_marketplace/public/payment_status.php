<?php
declare(strict_types=1);
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

require_once __DIR__ . '/../config/database.php';

$pdo = getDatabaseConnection();
$message = '';
$messageType = 'success';

// Handle Order Status Update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_escrow') {
    $txId = filter_input(INPUT_POST, 'transaction_id', FILTER_VALIDATE_INT);
    $newStatus = trim($_POST['escrow_status'] ?? '');

    $allowedStatuses = ['initiated', 'escrow_pending', 'completed', 'refunded', 'cancelled'];
    if ($txId && in_array($newStatus, $allowedStatuses, true)) {
        try {
            $pdo->beginTransaction();

            $stmt = $pdo->prepare("SELECT * FROM transactions WHERE id = :id FOR UPDATE");
            $stmt->execute(['id' => $txId]);
            $tx = $stmt->fetch();

            if ($tx) {
                $updateTx = $pdo->prepare("UPDATE transactions SET escrow_status = :status WHERE id = :id");
                $updateTx->execute(['status' => $newStatus, 'id' => $txId]);

                $listingId = (int)$tx['listing_id'];
                if ($newStatus === 'completed') {
                    $updateListing = $pdo->prepare("UPDATE listings SET status = 'sold' WHERE id = :id");
                    $updateListing->execute(['id' => $listingId]);
                } elseif (in_array($newStatus, ['refunded', 'cancelled'], true)) {
                    $updateListing = $pdo->prepare("UPDATE listings SET status = 'available' WHERE id = :id");
                    $updateListing->execute(['id' => $listingId]);
                } elseif (in_array($newStatus, ['initiated', 'escrow_pending'], true)) {
                    $updateListing = $pdo->prepare("UPDATE listings SET status = 'escrow_pending' WHERE id = :id");
                    $updateListing->execute(['id' => $listingId]);
                }

                $pdo->commit();
                $message = "Order #{$txId} updated successfully!";
            } else {
                $pdo->rollBack();
                $message = "Order record not found.";
                $messageType = 'error';
            }
        } catch (Exception $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            $message = "Error updating order: " . $e->getMessage();
            $messageType = 'error';
        }
    }
}

// Fetch Status Filter
$filter = trim($_GET['status'] ?? 'all');
$params = [];
$sql = "
    SELECT 
        t.*,
        l.title AS project_title,
        l.category AS project_category,
        l.slug AS project_slug
    FROM transactions t
    LEFT JOIN listings l ON t.listing_id = l.id
";

if ($filter !== 'all' && $filter !== '') {
    $sql .= " WHERE t.escrow_status = :status";
    $params['status'] = $filter;
}

$sql .= " ORDER BY t.created_at DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$transactions = $stmt->fetchAll();

// Overview Counts
$stats = $pdo->query("
    SELECT 
        COUNT(id) AS total_count,
        COALESCE(SUM(CASE WHEN escrow_status = 'completed' THEN agreed_price ELSE 0 END), 0) AS released_volume,
        COALESCE(SUM(CASE WHEN escrow_status IN ('initiated', 'escrow_pending') THEN agreed_price ELSE 0 END), 0) AS locked_volume,
        COUNT(CASE WHEN escrow_status IN ('initiated', 'escrow_pending') THEN 1 END) AS active_escrows
    FROM transactions
")->fetch();
?>
<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-50 text-slate-900">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Orders &amp; Payments | RMT INFO TECH</title>
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

    <div class="flex-1 flex flex-col min-w-0 min-h-screen">
        
        <!-- Header -->
        <header class="h-16 border-b border-slate-200 bg-white sticky top-0 z-30 px-8 flex items-center justify-between shadow-sm">
            <div class="flex items-center gap-3">
                <h1 class="text-sm font-bold text-[#0A192F]">Customer Orders & Payments</h1>
                <span class="text-xs px-2.5 py-0.5 rounded-full bg-blue-50 text-[#0F2744] border border-blue-100 font-mono font-semibold">
                    <?= (int)($stats['total_count'] ?? 0) ?> Total Orders
                </span>
            </div>
            <a href="todays_sales.php" class="text-xs border border-slate-300 hover:bg-slate-50 text-[#0F2744] font-semibold px-3.5 py-1.5 rounded-xl transition">
                Today's Sales &rarr;
            </a>
        </header>

        <!-- Main Content -->
        <main class="p-8 space-y-6 flex-1">
            
            <?php if ($message !== ''): ?>
                <div class="p-3.5 rounded-xl text-xs font-semibold border flex justify-between items-center <?= $messageType === 'success' ? 'bg-emerald-50 border-emerald-200 text-emerald-800' : 'bg-red-50 border-red-200 text-red-700' ?>">
                    <span><?= htmlspecialchars($message) ?></span>
                    <button onclick="this.parentElement.remove()" class="text-sm font-bold opacity-60 hover:opacity-100">&times;</button>
                </div>
            <?php endif; ?>

            <!-- Metric Cards (Plain English) -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm">
                    <span class="text-[11px] uppercase tracking-wider font-bold text-slate-400">Money On Hold (Pending)</span>
                    <div class="text-3xl font-extrabold text-[#0A192F] mt-2 font-mono">
                        $<?= number_format((float)($stats['locked_volume'] ?? 0), 2) ?>
                    </div>
                    <span class="text-xs text-amber-600 font-semibold mt-1 block">
                        <?= (int)($stats['active_escrows'] ?? 0) ?> deals waiting to be finished
                    </span>
                </div>

                <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm">
                    <span class="text-[11px] uppercase tracking-wider font-bold text-slate-400">Total Money Paid Out</span>
                    <div class="text-3xl font-extrabold text-blue-600 mt-2 font-mono">
                        $<?= number_format((float)($stats['released_volume'] ?? 0), 2) ?>
                    </div>
                    <span class="text-xs text-slate-500 mt-1 block">Completed deals delivered to sellers</span>
                </div>

                <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm">
                    <span class="text-[11px] uppercase tracking-wider font-bold text-slate-400">Total Purchases Made</span>
                    <div class="text-3xl font-extrabold text-[#0A192F] mt-2 font-mono">
                        <?= (int)($stats['total_count'] ?? 0) ?>
                    </div>
                    <span class="text-xs text-slate-500 mt-1 block">Total buyer checkout clicks</span>
                </div>
            </div>

            <!-- Filter Tabs -->
            <div class="flex items-center gap-2 pt-2">
                <?php
                $statusTabs = [
                    'All Deals'       => 'all',
                    'Waiting on Hold' => 'escrow_pending',
                    'Just Started'    => 'initiated',
                    'Completed'       => 'completed',
                    'Refunded'        => 'refunded'
                ];
                foreach ($statusTabs as $name => $val):
                    $active = ($filter === $val);
                ?>
                    <a href="payment_status.php?status=<?= urlencode($val) ?>" 
                       class="px-3.5 py-1.5 rounded-xl text-xs font-semibold transition <?= $active ? 'bg-[#0F2744] text-white shadow-sm' : 'bg-white border border-slate-200 text-slate-600 hover:text-[#0F2744] hover:bg-slate-50' ?>">
                        <?= $name ?>
                    </a>
                <?php endforeach; ?>
            </div>

            <!-- Table Card -->
            <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
                <div class="p-5 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
                    <div>
                        <h2 class="text-sm font-bold text-[#0A192F]">All Deals & Transfers</h2>
                        <p class="text-xs text-slate-500 mt-0.5">Check deals, verify code files, and release funds to sellers.</p>
                    </div>
                    <span class="text-xs text-slate-400 font-mono"><?= count($transactions) ?> items</span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50 text-[11px] uppercase font-bold text-slate-500 border-b border-slate-200">
                                <th class="py-3 px-6">Order ID</th>
                                <th class="py-3 px-6">Project Name</th>
                                <th class="py-3 px-6">Price</th>
                                <th class="py-3 px-6">Date</th>
                                <th class="py-3 px-6">Current Status</th>
                                <th class="py-3 px-6 text-right">Change Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-xs">
                            <?php if (empty($transactions)): ?>
                                <tr>
                                    <td colspan="6" class="py-12 text-center text-slate-400">
                                        No orders found for this filter.
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($transactions as $row): ?>
                                    <tr class="hover:bg-slate-50/70 transition">
                                        <td class="py-4 px-6 font-mono text-[11px] text-slate-500">
                                            <span class="font-bold text-[#0A192F]">#<?= (int)$row['id'] ?></span>
                                            <div class="text-[10px] text-slate-400"><?= htmlspecialchars(substr($row['transaction_uuid'], 0, 12)) ?>...</div>
                                        </td>

                                        <td class="py-4 px-6">
                                            <div class="font-semibold text-[#0A192F]">
                                                <?= htmlspecialchars($row['project_title'] ?? 'Listing #' . $row['listing_id']) ?>
                                            </div>
                                            <span class="text-[10px] text-slate-400 font-mono">
                                                <?= htmlspecialchars($row['project_category'] ?? 'Asset') ?>
                                            </span>
                                        </td>

                                        <td class="py-4 px-6 font-mono font-bold text-slate-900 text-sm">
                                            $<?= number_format((float)$row['agreed_price'], 2) ?>
                                        </td>

                                        <td class="py-4 px-6 font-mono text-slate-500 text-[11px]">
                                            <?= date('M j, Y - H:i', strtotime($row['created_at'])) ?>
                                        </td>

                                        <td class="py-4 px-6">
                                            <?php 
                                            $curr = strtolower($row['escrow_status'] ?? 'initiated');
                                            if ($curr === 'completed'): 
                                            ?>
                                                <span class="text-[10px] text-emerald-700 bg-emerald-50 border border-emerald-200 px-2.5 py-0.5 rounded-full font-semibold">Done (Paid)</span>
                                            <?php elseif (in_array($curr, ['initiated', 'escrow_pending'], true)): ?>
                                                <span class="text-[10px] text-amber-700 bg-amber-50 border border-amber-200 px-2.5 py-0.5 rounded-full font-semibold">Money On Hold</span>
                                            <?php elseif ($curr === 'refunded'): ?>
                                                <span class="text-[10px] text-blue-700 bg-blue-50 border border-blue-200 px-2.5 py-0.5 rounded-full font-semibold">Refunded</span>
                                            <?php else: ?>
                                                <span class="text-[10px] text-rose-700 bg-rose-50 border border-rose-200 px-2.5 py-0.5 rounded-full font-semibold">Cancelled</span>
                                            <?php endif; ?>
                                        </td>

                                        <td class="py-4 px-6 text-right">
                                            <form method="POST" class="inline-flex items-center gap-2">
                                                <input type="hidden" name="action" value="update_escrow">
                                                <input type="hidden" name="transaction_id" value="<?= (int)$row['id'] ?>">
                                                
                                                <select name="escrow_status" onchange="this.form.submit()"
                                                        class="text-[11px] font-semibold rounded-lg px-2.5 py-1.5 bg-white border border-slate-300 text-slate-700 focus:outline-none focus:border-blue-500 cursor-pointer shadow-sm">
                                                    <option value="initiated" <?= $curr === 'initiated' ? 'selected' : '' ?>>Order Just Started</option>
                                                    <option value="escrow_pending" <?= $curr === 'escrow_pending' ? 'selected' : '' ?>>Hold Money (Pending)</option>
                                                    <option value="completed" <?= $curr === 'completed' ? 'selected' : '' ?>>Deal Done (Pay Seller)</option>
                                                    <option value="refunded" <?= $curr === 'refunded' ? 'selected' : '' ?>>Refund Money to Buyer</option>
                                                    <option value="cancelled" <?= $curr === 'cancelled' ? 'selected' : '' ?>>Cancel Order</option>
                                                </select>
                                            </form>
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
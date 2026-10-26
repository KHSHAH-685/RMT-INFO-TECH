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

$uploadDir = __DIR__ . '/../uploads/';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

// Handle Status Change, Demo URL Update & Deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'update_status') {
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        $status = $_POST['status'] ?? '';
        if ($id && in_array($status, ['available', 'escrow_pending', 'sold'], true)) {
            $stmt = $pdo->prepare("UPDATE listings SET status = :status WHERE id = :id");
            $stmt->execute(['status' => $status, 'id' => $id]);
            $message = "Status updated successfully.";
        }
    } elseif ($_POST['action'] === 'update_demo') {
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        $demo_url = trim($_POST['demo_url'] ?? '');
        if ($id) {
            $stmt = $pdo->prepare("UPDATE listings SET demo_url = :demo_url WHERE id = :id");
            $stmt->execute(['demo_url' => $demo_url, 'id' => $id]);
            $message = "Demo link updated successfully!";
        }
    } elseif ($_POST['action'] === 'delete_listing') {
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        if ($id) {
            $stmt = $pdo->prepare("SELECT zip_file FROM listings WHERE id = :id");
            $stmt->execute(['id' => $id]);
            $item = $stmt->fetch();
            if ($item && !empty($item['zip_file']) && file_exists($uploadDir . $item['zip_file'])) {
                unlink($uploadDir . $item['zip_file']);
            }

            $stmt = $pdo->prepare("DELETE FROM listings WHERE id = :id");
            $stmt->execute(['id' => $id]);
            $message = "Website removed.";
        }
    }
}

// Fetch all uploaded websites
$listings = $pdo->query("SELECT * FROM listings ORDER BY created_at DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-50 text-slate-900">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Websites | RMT INFO TECH</title>
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
        <!-- Top Navigation Bar -->
        <header class="h-16 border-b border-slate-200 bg-white sticky top-0 z-30 px-8 flex items-center justify-between shadow-sm">
            <div class="flex items-center gap-3">
                <h1 class="text-sm font-bold text-[#0A192F]">My Uploaded Websites</h1>
                <span class="text-xs px-2.5 py-0.5 rounded-full bg-blue-50 text-[#0F2744] border border-blue-100 font-mono font-semibold">
                    <?= count($listings) ?> Projects Total
                </span>
            </div>
            <a href="add_project.php" class="text-xs bg-[#0F2744] hover:bg-[#16365D] text-white font-bold px-3.5 py-2 rounded-xl transition shadow-sm">
                + Add New Project
            </a>
        </header>

        <!-- Main Content Area -->
        <main class="p-8 space-y-6 flex-1">
            <?php if ($message !== ''): ?>
                <div class="p-3.5 rounded-xl text-xs font-semibold border flex justify-between items-center <?= $messageType === 'success' ? 'bg-emerald-50 border-emerald-200 text-emerald-800' : 'bg-red-50 border-red-200 text-red-700' ?>">
                    <span><?= htmlspecialchars($message) ?></span>
                    <button onclick="this.parentElement.remove()" class="text-sm font-bold opacity-60 hover:opacity-100">&times;</button>
                </div>
            <?php endif; ?>

            <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
                <div class="p-5 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
                    <div>
                        <h2 class="text-sm font-bold text-[#0A192F]">Full Inventory & Demo Links</h2>
                        <p class="text-xs text-slate-500 mt-0.5">Manage live preview links, status, and download files.</p>
                    </div>
                </div>

                <div class="divide-y divide-slate-100">
                    <?php if (empty($listings)): ?>
                        <div class="p-12 text-center text-slate-400 text-xs">
                            No websites uploaded yet. <a href="add_project.php" class="text-blue-600 font-semibold underline">Add your first project here</a>.
                        </div>
                    <?php else: ?>
                        <?php foreach ($listings as $l): ?>
                            <div class="p-5 hover:bg-slate-50/60 transition space-y-3">
                                
                                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                                    <div>
                                        <div class="flex items-center gap-2.5">
                                            <h3 class="font-bold text-[#0A192F] text-base"><?= htmlspecialchars($l['title']) ?></h3>
                                            <span class="text-[10px] bg-blue-50 border border-blue-100 px-2 py-0.5 rounded text-[#0F2744] font-medium font-mono">
                                                <?= htmlspecialchars($l['category']) ?>
                                            </span>
                                        </div>
                                        <p class="text-xs text-slate-500 mt-1"><?= htmlspecialchars($l['tagline']) ?></p>
                                    </div>

                                    <div class="flex items-center gap-2">
                                        <!-- Status Change Form -->
                                        <form method="POST">
                                            <input type="hidden" name="action" value="update_status">
                                            <input type="hidden" name="id" value="<?= (int)$l['id'] ?>">
                                            <select name="status" onchange="this.form.submit()" 
                                                    class="text-xs font-semibold rounded-xl px-3 py-1.5 bg-white border border-slate-200 text-slate-700 focus:outline-none focus:border-blue-500 cursor-pointer shadow-sm">
                                                <option value="available" <?= $l['status'] === 'available' ? 'selected' : '' ?>>● Available</option>
                                                <option value="escrow_pending" <?= $l['status'] === 'escrow_pending' ? 'selected' : '' ?>>⏳ Pending</option>
                                                <option value="sold" <?= $l['status'] === 'sold' ? 'selected' : '' ?>>✓ Sold</option>
                                            </select>
                                        </form>

                                        <!-- Delete Action -->
                                        <form method="POST" onsubmit="return confirm('Permanently delete <?= addslashes($l['title']) ?>?');">
                                            <input type="hidden" name="action" value="delete_listing">
                                            <input type="hidden" name="id" value="<?= (int)$l['id'] ?>">
                                            <button type="submit" class="text-xs text-red-600 hover:text-red-700 px-3 py-1.5 rounded-xl bg-red-50 hover:bg-red-100 border border-red-100 transition font-semibold">
                                                Delete
                                            </button>
                                        </form>
                                    </div>
                                </div>

                                <!-- Financial Specs Row -->
                                <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 pt-2 border-t border-slate-100 text-xs">
                                    <div>
                                        <span class="text-[10px] uppercase text-slate-400 font-bold block">Selling Price</span>
                                        <span class="text-sm font-extrabold text-[#0A192F] font-mono">$<?= number_format((float)$l['asking_price'], 2) ?></span>
                                    </div>
                                    <div>
                                        <span class="text-[10px] uppercase text-slate-400 font-bold block">Monthly Earnings</span>
                                        <span class="text-xs font-bold text-slate-700 font-mono">$<?= number_format((float)$l['monthly_revenue'], 2) ?>/mo</span>
                                    </div>
                                    <div>
                                        <span class="text-[10px] uppercase text-slate-400 font-bold block">Monthly Profit</span>
                                        <span class="text-xs font-bold text-slate-700 font-mono">$<?= number_format((float)$l['monthly_profit'], 2) ?>/mo</span>
                                    </div>
                                    <div>
                                        <span class="text-[10px] uppercase text-slate-400 font-bold block">Project File</span>
                                        <?php if (!empty($l['zip_file']) && file_exists($uploadDir . $l['zip_file'])): ?>
                                            <a href="../uploads/<?= urlencode($l['zip_file']) ?>" download 
                                               class="inline-flex items-center gap-1 text-[11px] font-bold text-blue-600 hover:text-blue-800 transition">
                                                <span>📥 Download ZIP</span>
                                            </a>
                                        <?php else: ?>
                                            <span class="text-slate-400 text-[11px]">No ZIP attached</span>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <!-- Inline Live Demo URL Form -->
                                <div class="bg-slate-50/80 p-3 rounded-xl border border-slate-200/80 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 text-xs">
                                    <form method="POST" class="flex-1 flex items-center gap-2 w-full">
                                        <input type="hidden" name="action" value="update_demo">
                                        <input type="hidden" name="id" value="<?= (int)$l['id'] ?>">
                                        <span class="text-slate-600 text-[11px] font-semibold shrink-0">Demo Link:</span>
                                        <input type="url" name="demo_url" value="<?= htmlspecialchars($l['demo_url'] ?? '') ?>" placeholder="https://example.com or http://localhost/mysite" 
                                               class="bg-white border border-slate-200 rounded-lg px-2.5 py-1 text-xs text-slate-800 w-full focus:outline-none focus:border-blue-500 transition">
                                        <button type="submit" class="bg-blue-50 text-[#0F2744] hover:bg-blue-100 border border-blue-200 px-3 py-1 rounded-lg text-xs font-semibold shrink-0 transition">
                                            Save Link
                                        </button>
                                    </form>

                                    <?php if (!empty($l['demo_url'])): ?>
                                        <a href="<?= htmlspecialchars($l['demo_url']) ?>" target="_blank" class="text-xs font-bold text-blue-600 hover:text-blue-800 hover:underline shrink-0 flex items-center gap-1">
                                            <span>Test Demo</span> &nearr;
                                        </a>
                                    <?php endif; ?>
                                </div>

                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>

</body>
</html>
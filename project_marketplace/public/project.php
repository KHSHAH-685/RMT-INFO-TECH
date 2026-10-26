<?php
declare(strict_types=1);
session_start();

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../src/Listing.php';

$slug = filter_input(INPUT_GET, 'slug', FILTER_DEFAULT) ?? '';

if (!$slug) {
    header('Location: my_website.php');
    exit;
}

$pdo = getDatabaseConnection();
$listingModel = new Listing($pdo);
$project = $listingModel->findBySlug($slug);

if (!$project) {
    http_response_code(404);
    die("Error 404: The requested project listing does not exist.");
}

$uploadDir = __DIR__ . '/../uploads/';
if (empty($_SESSION['checkout_csrf_token'])) {
    $_SESSION['checkout_csrf_token'] = bin2hex(random_bytes(32));
}
?>
<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-950 text-slate-100">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($project['title']) ?> &bull; Project Overview</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="assets/css/theme.css">
</head>
<body class="admin-page min-h-full flex bg-slate-950 text-slate-200">

    <!-- INTEGRATED SIDEBAR -->
    <?php require_once __DIR__ . '/sidebar.php'; ?>

    <!-- MAIN PAGE CONTENT -->
    <div class="flex-1 flex flex-col min-w-0">

        <!-- Top Header Navigation -->
        <header class="h-16 border-b border-slate-800 bg-[#070A10]/60 sticky top-0 z-30 px-8 flex items-center justify-between backdrop-blur">
            <div class="flex items-center gap-3">
                <a href="my_website.php" class="text-xs text-slate-400 hover:text-white transition flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-slate-900 border border-slate-800">
                    &larr; Back to My Websites
                </a>
                <span class="text-xs text-slate-600">/</span>
                <span class="text-xs font-semibold text-white"><?= htmlspecialchars($project['title']) ?></span>
            </div>

            <div class="flex items-center gap-3">
                <a href="index.php" target="_blank" class="text-xs bg-slate-800 hover:bg-slate-700 text-slate-200 px-3.5 py-1.5 rounded-lg transition">
                    View Public Marketplace &rarr;
                </a>
            </div>
        </header>

        <!-- Main Body Details -->
        <main class="p-8 max-w-5xl space-y-6">

            <!-- Primary Project Overview Card -->
            <div class="bg-slate-900/60 border border-slate-800 rounded-2xl p-6 sm:p-8 space-y-6">
                
                <!-- Title, Category & Price Header -->
                <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4 border-b border-slate-800/80 pb-6">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="text-[10px] font-bold uppercase tracking-wider px-2.5 py-0.5 rounded-md bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                                <?= htmlspecialchars($project['category']) ?>
                            </span>
                            <span class="text-[10px] uppercase font-bold px-2 py-0.5 rounded <?= 
                                $project['status'] === 'available' ? 'bg-emerald-500/20 text-emerald-400' : 
                                ($project['status'] === 'escrow_pending' ? 'bg-amber-500/20 text-amber-400' : 'bg-slate-700 text-slate-300') 
                            ?>">
                                <?= htmlspecialchars(str_replace('_', ' ', $project['status'])) ?>
                            </span>
                        </div>
                        <h1 class="text-2xl sm:text-3xl font-extrabold text-white mt-2.5"><?= htmlspecialchars($project['title']) ?></h1>
                        <p class="text-sm text-slate-400 mt-1"><?= htmlspecialchars($project['tagline']) ?></p>
                    </div>

                    <div class="sm:text-right">
                        <span class="text-[10px] uppercase font-bold text-slate-500 block">Asking Valuation</span>
                        <span class="text-3xl font-extrabold text-emerald-400 font-mono block">$<?= number_format((float)$project['asking_price'], 2) ?></span>
                    </div>
                </div>

                <!-- Financial Metrics Breakdown -->
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 p-4 rounded-xl bg-slate-950/70 border border-slate-800/80 text-xs">
                    <div>
                        <span class="text-slate-500 font-semibold block">Monthly Revenue</span>
                        <span class="text-base font-bold text-white font-mono mt-0.5 block">$<?= number_format((float)$project['monthly_revenue'], 2) ?></span>
                    </div>
                    <div>
                        <span class="text-slate-500 font-semibold block">Net Monthly Profit</span>
                        <span class="text-base font-bold text-emerald-400 font-mono mt-0.5 block">$<?= number_format((float)$project['monthly_profit'], 2) ?></span>
                    </div>
                    <div>
                        <span class="text-slate-500 font-semibold block">Tech Stack</span>
                        <span class="text-xs font-semibold text-slate-300 mt-1 block truncate"><?= htmlspecialchars($project['primary_tech_stack'] ?: 'PHP, MySQL') ?></span>
                    </div>
                    <div>
                        <span class="text-slate-500 font-semibold block">Source ZIP</span>
                        <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin' && !empty($project['zip_file']) && file_exists($uploadDir . $project['zip_file'])): ?>
                            <a href="../uploads/<?= urlencode($project['zip_file']) ?>" download 
                               class="text-xs font-bold text-emerald-400 hover:text-emerald-300 mt-1 inline-flex items-center gap-1 transition">
                                📥 Download File
                            </a>
                        <?php elseif (!empty($project['zip_file']) && file_exists($uploadDir . $project['zip_file'])): ?>
                            <span class="text-xs text-slate-600 mt-1 block">Included with secure handover</span>
                        <?php else: ?>
                            <span class="text-xs text-slate-600 mt-1 block">No file uploaded</span>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Assets Included Section -->
                <div>
                    <h3 class="text-xs uppercase font-bold text-slate-400 tracking-wider mb-2">Transferred Assets & Deliverables</h3>
                    <div class="p-4 rounded-xl bg-slate-950/50 border border-slate-800 text-xs text-slate-300 leading-relaxed">
                        <?= htmlspecialchars($project['included_assets'] ?: 'Full source code, production database, and setup instructions.') ?>
                    </div>
                </div>

                <!-- Comprehensive Description -->
                <div>
                    <h3 class="text-xs uppercase font-bold text-slate-400 tracking-wider mb-2">Full Project Scope & Notes</h3>
                    <div class="p-4 rounded-xl bg-slate-950/50 border border-slate-800 text-xs text-slate-300 whitespace-pre-line leading-relaxed">
                        <?= htmlspecialchars($project['description'] ?: 'No extensive description provided for this listing.') ?>
                    </div>
                </div>

                <!-- Action Footnote / Escrow Simulation -->
                <div class="pt-4 border-t border-slate-800/80 flex flex-col sm:flex-row items-center justify-between gap-4">
                    <span class="text-xs text-slate-500">
                        Seller ID: <strong class="text-slate-400">#<?= (int)$project['seller_id'] ?></strong> &bull; Listed On: <strong class="text-slate-400"><?= htmlspecialchars($project['created_at']) ?></strong>
                    </span>

                    <?php if ($project['status'] === 'available'): ?>
                        <form action="checkout.php" method="POST" class="w-full sm:w-auto">
                            <input type="hidden" name="listing_id" value="<?= (int)$project['id'] ?>">
                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['checkout_csrf_token'], ENT_QUOTES, 'UTF-8') ?>">
                            <button type="submit" class="w-full sm:w-auto bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-bold px-6 py-2.5 rounded-xl transition text-xs shadow-lg shadow-emerald-500/20">
                                Initiate Escrow Acquisition
                            </button>
                        </form>
                    <?php else: ?>
                        <button disabled class="w-full sm:w-auto bg-slate-800 text-slate-500 font-bold px-6 py-2.5 rounded-xl text-xs cursor-not-allowed">
                            Asset Unavailable (<?= htmlspecialchars($project['status']) ?>)
                        </button>
                    <?php endif; ?>
                </div>

            </div>

        </main>
    </div>

</body>
</html>
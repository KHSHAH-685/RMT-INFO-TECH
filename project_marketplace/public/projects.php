<?php
declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/database.php';

$selectedCategory = trim($_GET['category'] ?? '');
$searchQuery      = trim($_GET['q'] ?? '');

$listings = [];
$dbError  = null;

try {
    $pdo = getDatabaseConnection();

    $sql = "SELECT * FROM listings WHERE status = 'available'";
    $params = [];

    if ($selectedCategory !== '') {
        $sql .= " AND category = :category";
        $params['category'] = $selectedCategory;
    }

    if ($searchQuery !== '') {
        $sql .= " AND (title LIKE :q OR tagline LIKE :q OR primary_tech_stack LIKE :q)";
        $params['q'] = '%' . $searchQuery . '%';
    }

    $sql .= " ORDER BY created_at DESC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $listings = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Throwable $e) {
    error_log("Database Query Error in projects.php: " . $e->getMessage());
    $dbError = "Database connectivity issue. Listings will refresh automatically once connection stabilizes.";
}

$categories = [
    'All Assets'    => '',
    'SaaS'          => 'SaaS',
    'Turnkey Tools' => 'Turnkey Web Tool',
    'E-Commerce'    => 'E-commerce',
    'Mobile Apps'   => 'Mobile App'
];
?>
<!DOCTYPE html>
<html lang="en" class="bg-[#080d1a] text-slate-100 antialiased">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Available Projects &bull; RMT INFO TECH</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="assets/css/theme.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@500;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .font-mono { font-family: 'JetBrains Mono', monospace; }
        .modal-active { display: flex !important; }
    </style>
</head>
<body class="market-page min-h-screen flex flex-col justify-between bg-[#080d1a] text-slate-200">

    <!-- NAVBAR -->
    <?php 
    $navbarPath = __DIR__ . '/navbar.php';
    if (file_exists($navbarPath)) {
        include $navbarPath;
    }
    ?>

    <main class="max-w-7xl mx-auto w-full px-6 py-10 flex-1 space-y-10">

        <!-- DB Connection Notice (if applicable) -->
        <?php if ($dbError): ?>
            <div class="bg-amber-500/10 border border-amber-500/30 text-amber-300 px-4 py-3 rounded-2xl text-xs flex items-center justify-between">
                <span>⚠️ <?= htmlspecialchars($dbError) ?></span>
                <a href="projects.php" class="underline font-bold hover:text-white">Retry</a>
            </div>
        <?php endif; ?>

        <!-- Header -->
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 border-b border-slate-800 pb-6">
            <div>
                <span class="text-xs font-mono font-bold text-blue-400 uppercase tracking-widest">Marketplace Directory</span>
                <h1 class="text-3xl font-extrabold text-white mt-1">Available Projects &amp; Codebases</h1>
                <p class="text-xs text-slate-400 mt-1">Inspected software systems ready for immediate deployment and escrow transfer.</p>
            </div>
            <div class="text-xs font-mono text-slate-400 bg-slate-900 border border-slate-800 px-3.5 py-1.5 rounded-xl self-start md:self-auto">
                Total: <span class="text-blue-400 font-bold"><?= count($listings) ?></span> Assets Available
            </div>
        </div>

        <!-- SEARCH BAR & CATEGORIES -->
        <div class="space-y-4 max-w-4xl mx-auto">
            <form method="GET" action="projects.php" class="relative">
                <?php if ($selectedCategory !== ''): ?>
                    <input type="hidden" name="category" value="<?= htmlspecialchars($selectedCategory) ?>">
                <?php endif; ?>
                <input type="text" name="q" value="<?= htmlspecialchars($searchQuery) ?>" 
                       placeholder="Search by keywords, tags, or technology (e.g. PHP, React, MySQL, Hotel, POS)..." 
                       class="w-full bg-[#0f172a] border border-slate-700/80 rounded-2xl px-5 py-3.5 pl-12 text-sm text-white placeholder-slate-400 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition shadow-inner">
                <svg class="w-5 h-5 text-slate-400 absolute left-4 top-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </form>

            <!-- Categories Tabs -->
            <div class="flex items-center justify-center flex-wrap gap-2 pt-1">
                <?php foreach ($categories as $label => $catValue): 
                    $isActive = ($selectedCategory === $catValue);
                    $queryUrl = 'projects.php?';
                    $urlParts = [];
                    if ($catValue !== '') {
                        $urlParts[] = 'category=' . urlencode($catValue);
                    }
                    if ($searchQuery !== '') {
                        $urlParts[] = 'q=' . urlencode($searchQuery);
                    }
                    $finalUrl = !empty($urlParts) ? 'projects.php?' . implode('&', $urlParts) : 'projects.php';
                ?>
                    <a href="<?= htmlspecialchars($finalUrl) ?>" 
                       class="px-4 py-2 rounded-xl text-xs font-semibold transition <?= $isActive ? 'bg-blue-600 text-white shadow-md shadow-blue-600/30' : 'bg-[#0f172a] text-slate-400 border border-slate-800 hover:text-white hover:border-slate-700' ?>">
                        <?= htmlspecialchars($label) ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- PROJECTS GRID -->
        <?php if (empty($listings)): ?>
            <div class="text-center py-20 border border-slate-800 rounded-3xl bg-[#0f172a]/50 max-w-md mx-auto space-y-3 p-8">
                <div class="text-3xl">📦</div>
                <h3 class="text-base font-bold text-white">No projects found</h3>
                <p class="text-xs text-slate-400">Try another search keyword or clear filters.</p>
                <a href="projects.php" class="text-xs text-blue-400 font-semibold underline block pt-2">View all projects</a>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 items-stretch">
                <?php foreach ($listings as $index => $l): ?>
                    <article class="bg-[#0f172a] border border-slate-800 hover:border-slate-700 rounded-2xl p-6 flex flex-col justify-between transition group shadow-md hover:shadow-xl h-full">
                        
                        <div>
                            <!-- Header Status -->
                            <div class="flex items-center justify-between mb-4">
                                <span class="text-[10px] font-mono font-bold uppercase tracking-wider px-2.5 py-1 rounded bg-blue-500/10 text-blue-300 border border-blue-500/20">
                                    <?= htmlspecialchars($l['category'] ?? 'Asset') ?>
                                </span>
                                <span class="text-[10px] text-emerald-400 bg-emerald-500/10 border border-emerald-500/20 px-2 py-0.5 rounded-full font-semibold">
                                    ✔ Verified Code
                                </span>
                            </div>

                            <!-- Title & Tagline -->
                            <h3 class="text-lg font-bold text-white group-hover:text-blue-300 transition">
                                <?= htmlspecialchars($l['title'] ?? 'Untitled') ?>
                            </h3>
                            <p class="text-xs text-slate-400 mt-2 line-clamp-2 leading-relaxed min-h-[36px]">
                                <?= htmlspecialchars($l['tagline'] ?? 'Turnkey software asset ready for immediate deployment.') ?>
                            </p>

                            <!-- Tech Stack -->
                            <div class="mt-4 pt-3 border-t border-slate-800/80 flex flex-wrap gap-1.5 min-h-[32px]">
                                <?php 
                                $rawStack = !empty($l['primary_tech_stack']) ? $l['primary_tech_stack'] : 'PHP, MySQL';
                                $stack = array_filter(array_map('trim', explode(',', $rawStack)));
                                foreach (array_slice($stack, 0, 4) as $tech): 
                                ?>
                                    <span class="text-[10px] bg-slate-900 px-2.5 py-1 rounded border border-slate-800 text-slate-300 font-mono">
                                        <?= htmlspecialchars($tech) ?>
                                    </span>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <!-- Price and CTAs -->
                        <div class="mt-6 pt-4 border-t border-slate-800/80 space-y-4">
                            <div class="flex items-end justify-between font-mono">
                                <div>
                                    <span class="text-[10px] uppercase text-slate-400 block font-sans font-semibold">Price</span>
                                    <span class="text-2xl font-extrabold text-white tracking-tight">$<?= number_format((float)($l['asking_price'] ?? 0), 2) ?></span>
                                </div>
                                <?php if ((float)($l['monthly_revenue'] ?? 0) > 0): ?>
                                    <div class="text-right">
                                        <span class="text-[10px] uppercase text-slate-400 block font-sans font-semibold">MRR</span>
                                        <span class="text-xs font-bold text-emerald-400">+$<?= number_format((float)$l['monthly_revenue'], 2) ?>/mo</span>
                                    </div>
                                <?php else: ?>
                                    <span class="text-xs text-slate-400 font-sans">Full Handover</span>
                                <?php endif; ?>
                            </div>

                            <div class="grid grid-cols-2 gap-2">
                                <a href="preview.php?slug=<?= urlencode((string)($l['slug'] ?? '')) ?>" 
                                    class="flex items-center justify-center gap-1.5 bg-slate-800 hover:bg-slate-700 text-slate-200 font-semibold text-xs py-2.5 rounded-xl border border-slate-700 transition">
                                    <span>Live Preview</span>
                                    <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                </a>
                                
                                <button type="button" 
                                        data-project='<?= htmlspecialchars(json_encode($l, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP), ENT_QUOTES, 'UTF-8') ?>'
                                        onclick="handleModalOpen(this)"
                                        class="flex items-center justify-center gap-1 bg-blue-600 hover:bg-blue-500 text-white font-bold text-xs py-2.5 rounded-xl transition shadow-md shadow-blue-600/20">
                                    <span>Details &rarr;</span>
                                </button>
                            </div>
                        </div>

                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

    </main>

    <!-- FOOTER -->
    <footer class="border-t border-slate-800/80 bg-[#060a14] py-8 text-xs text-slate-400 mt-20">
        <div class="max-w-7xl mx-auto px-6 flex flex-col sm:flex-row items-center justify-between gap-4">
            <div><span class="font-bold text-white">RMT INFO TECH</span> &bull; &copy; <?= date('Y') ?> All Rights Reserved.</div>
            <div class="flex items-center gap-6">
                <a href="about.php" class="hover:text-white transition">About</a>
                <a href="gallery.php" class="hover:text-white transition">Gallery</a>
                <a href="contact.php" class="hover:text-white transition">Contact</a>
            </div>
        </div>
    </footer>

    <!-- POPUP MODAL -->
    <div id="projectModal" class="fixed inset-0 z-50 bg-black/80 backdrop-blur-sm hidden items-center justify-center p-4">
        <div class="bg-[#0f172a] border border-slate-700 rounded-2xl max-w-xl w-full p-6 space-y-6 relative shadow-2xl">
            <button type="button" onclick="closeProjectModal()" class="absolute right-4 top-4 text-slate-400 hover:text-white text-2xl leading-none">&times;</button>
            <div>
                <span id="modalCategory" class="text-[10px] uppercase font-mono font-bold px-2 py-0.5 rounded bg-blue-500/10 text-blue-300 border border-blue-500/20"></span>
                <h2 id="modalTitle" class="text-2xl font-bold text-white mt-2"></h2>
                <p id="modalTagline" class="text-xs text-slate-400 mt-1"></p>
            </div>
            <div class="grid grid-cols-2 gap-3 p-4 bg-slate-900 rounded-xl border border-slate-800 font-mono text-xs">
                <div>
                    <span class="text-[10px] uppercase text-slate-400 block font-sans">Price</span>
                    <span id="modalPrice" class="text-xl font-bold text-blue-400"></span>
                </div>
                <div>
                    <span class="text-[10px] uppercase text-slate-400 block font-sans">Revenue</span>
                    <span id="modalRevenue" class="text-xs font-bold text-slate-200 mt-1 block"></span>
                </div>
            </div>
            <div>
                <span class="text-[11px] font-bold text-slate-300 uppercase block mb-1">Included Assets:</span>
                <p id="modalAssets" class="text-xs text-slate-300 bg-slate-900 p-3 rounded-xl border border-slate-800"></p>
            </div>
            <div>
                <span class="text-[11px] font-bold text-slate-300 uppercase block mb-1">Description:</span>
                <p id="modalDescription" class="text-xs text-slate-400 bg-slate-900 p-3 rounded-xl border border-slate-800 whitespace-pre-line max-h-32 overflow-y-auto"></p>
            </div>
            <div class="flex items-center justify-between gap-3 pt-3 border-t border-slate-800">
                <a id="modalDemoBtn" href="#" target="_blank" class="px-4 py-2.5 rounded-xl text-xs font-semibold bg-slate-800 hover:bg-slate-700 text-slate-200 border border-slate-700 transition">Live Demo</a>
                <div class="flex items-center gap-2">
                    <button type="button" onclick="closeProjectModal()" class="px-4 py-2.5 rounded-xl text-xs font-semibold bg-slate-800 text-slate-300 hover:bg-slate-700">Close</button>
                    <a id="modalBuyBtn" href="#" class="px-5 py-2.5 rounded-xl text-xs font-bold bg-blue-600 hover:bg-blue-500 text-white transition">Buy Project &rarr;</a>
                </div>
            </div>
        </div>
    </div>

    <script>
        function handleModalOpen(btn) {
            try {
                const project = JSON.parse(btn.getAttribute('data-project'));
                openProjectModal(project);
            } catch (err) {
                console.error("Failed to parse project data:", err);
            }
        }

        function openProjectModal(project) {
            document.getElementById('modalTitle').innerText = project.title || 'Untitled';
            document.getElementById('modalTagline').innerText = project.tagline || '';
            document.getElementById('modalCategory').innerText = project.category || 'Asset';

            const price = parseFloat(project.asking_price || 0);
            document.getElementById('modalPrice').innerText = '$' + price.toLocaleString(undefined, { minimumFractionDigits: 2 });

            const revenue = parseFloat(project.monthly_revenue || 0);
            document.getElementById('modalRevenue').innerText = revenue > 0 ? '$' + revenue.toLocaleString() + '/mo' : 'Pre-revenue';

            document.getElementById('modalAssets').innerText = project.included_assets || 'Complete code repository and SQL file.';
            document.getElementById('modalDescription').innerText = project.description || 'No additional details provided.';
            
            const demoBtn = document.getElementById('modalDemoBtn');
            if (project.demo_url && project.demo_url.trim() !== '') {
                demoBtn.href = project.demo_url;
                demoBtn.removeAttribute('target');
                demoBtn.setAttribute('target', '_blank');
                demoBtn.onclick = null;
            } else {
                demoBtn.href = '#';
                demoBtn.onclick = (e) => { 
                    e.preventDefault(); 
                    alert('No live demo URL configured for this project.'); 
                };
            }

            document.getElementById('modalBuyBtn').href = 'project.php?slug=' + encodeURIComponent(project.slug || '');

            const modal = document.getElementById('projectModal');
            modal.classList.remove('hidden');
            modal.classList.add('modal-active');
        }

        function closeProjectModal() {
            const modal = document.getElementById('projectModal');
            modal.classList.add('hidden');
            modal.classList.remove('modal-active');
        }

        document.getElementById('projectModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeProjectModal();
            }
        });
    </script>
</body>
</html>

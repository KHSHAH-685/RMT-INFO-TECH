<?php
declare(strict_types=1);
session_start();

require_once __DIR__ . '/../config/database.php';

$pdo = getDatabaseConnection();
$slug = trim($_GET['slug'] ?? '');
$id = trim($_GET['id'] ?? '');

$project = null;

if ($slug !== '') {
    $stmt = $pdo->prepare("SELECT * FROM listings WHERE slug = :slug LIMIT 1");
    $stmt->execute(['slug' => $slug]);
    $project = $stmt->fetch(PDO::FETCH_ASSOC);
} elseif ($id !== '') {
    $stmt = $pdo->prepare("SELECT * FROM listings WHERE id = :id LIMIT 1");
    $stmt->execute(['id' => $id]);
    $project = $stmt->fetch(PDO::FETCH_ASSOC);
}

if (!$project || empty($project['demo_url'])) {
    header('Location: projects.php');
    exit;
}

$demoUrl = $project['demo_url'];
?>
<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-950">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Preview: <?= htmlspecialchars($project['title']) ?> &bull; RMT INFO TECH</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="assets/css/theme.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@500;600;700;800&family=JetBrains+Mono:wght@500;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .font-mono { font-family: 'JetBrains Mono', monospace; }
        .device-transition { transition: width 0.3s cubic-bezier(0.4, 0, 0.2, 1); }
    </style>
</head>
<body class="market-page preview-page h-full flex flex-col overflow-hidden bg-[#070b14] text-slate-200">

    <!-- Top Preview Control Bar -->
    <header class="h-14 bg-[#0b1325] border-b border-slate-800 px-4 flex items-center justify-between shrink-0 z-50">
        
        <!-- Left: Project Info & Back -->
        <div class="flex items-center gap-3">
            <a href="projects.php" class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-slate-800/80 hover:bg-slate-700 text-slate-300 hover:text-white text-xs font-semibold transition border border-slate-700/60">
                <span>&larr; Exit Preview</span>
            </a>
            <div class="h-4 w-px bg-slate-800 hidden sm:block"></div>
            <div>
                <span class="text-xs font-bold text-white block leading-tight"><?= htmlspecialchars($project['title']) ?></span>
                <span class="text-[10px] text-slate-400 font-mono hidden sm:inline">$<?= number_format((float)$project['asking_price'], 2) ?></span>
            </div>
        </div>

        <!-- Center: Device Switcher (Desktop, Tablet, Mobile) -->
        <div class="hidden md:flex items-center gap-1 bg-slate-900/90 border border-slate-800 p-1 rounded-xl">
            <button onclick="setViewport('100%')" id="btnDesktop" class="px-3 py-1 rounded-lg text-xs font-semibold bg-blue-600 text-white transition flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                <span>Desktop</span>
            </button>
            <button onclick="setViewport('768px')" id="btnTablet" class="px-3 py-1 rounded-lg text-xs font-semibold text-slate-400 hover:text-white transition flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                <span>Tablet</span>
            </button>
            <button onclick="setViewport('390px')" id="btnMobile" class="px-3 py-1 rounded-lg text-xs font-semibold text-slate-400 hover:text-white transition flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                <span>Mobile</span>
            </button>
        </div>

        <!-- Right: External Link & Purchase -->
        <div class="flex items-center gap-3">
            <a href="<?= htmlspecialchars($demoUrl) ?>" target="_blank" class="text-xs text-slate-400 hover:text-slate-200 hidden sm:flex items-center gap-1">
                <span>Direct URL</span>
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
            </a>
            <a href="project.php?slug=<?= urlencode($project['slug'] ?? '') ?>" class="bg-blue-600 hover:bg-blue-500 text-white font-bold text-xs px-4 py-2 rounded-xl transition shadow-md shadow-blue-600/30">
                Buy This Project &rarr;
            </a>
        </div>
    </header>

    <!-- Main Live Frame Canvas -->
    <main class="flex-1 bg-slate-900/50 flex items-center justify-center p-0 md:p-3 overflow-hidden">
        <div id="frameWrapper" class="w-full h-full device-transition rounded-none md:rounded-xl overflow-hidden border border-slate-800 bg-white shadow-2xl">
            <iframe id="liveFrame" src="<?= htmlspecialchars($demoUrl) ?>" class="w-full h-full border-0" sandbox="allow-same-origin allow-scripts allow-forms allow-popups"></iframe>
        </div>
    </main>

    <script>
        function setViewport(width) {
            const wrapper = document.getElementById('frameWrapper');
            wrapper.style.width = width;

            const btns = ['btnDesktop', 'btnTablet', 'btnMobile'];
            btns.forEach(id => {
                const btn = document.getElementById(id);
                btn.classList.remove('bg-blue-600', 'text-white');
                btn.classList.add('text-slate-400');
            });

            if (width === '100%') {
                document.getElementById('btnDesktop').classList.add('bg-blue-600', 'text-white');
            } else if (width === '768px') {
                document.getElementById('btnTablet').classList.add('bg-blue-600', 'text-white');
            } else if (width === '390px') {
                document.getElementById('btnMobile').classList.add('bg-blue-600', 'text-white');
            }
        }
    </script>
</body>
</html> 
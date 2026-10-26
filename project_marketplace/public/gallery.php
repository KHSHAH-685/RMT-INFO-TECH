<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en" class="bg-[#080d1a] text-slate-100 antialiased">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gallery &bull; RMT INFO TECH</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="assets/css/theme.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
</head>
<body class="market-page min-h-screen flex flex-col justify-between bg-[#080d1a]">
    <?php include __DIR__ . '/navbar.php'; ?>
    <main class="max-w-7xl mx-auto w-full px-6 py-12 flex-1 space-y-6">
        <div class="space-y-3">
            <span class="text-xs font-bold uppercase tracking-[0.2em] text-blue-400">Inside the products</span>
            <h1 class="text-3xl sm:text-4xl font-extrabold text-white tracking-tight">Project Showcase Gallery</h1>
            <p class="text-sm text-slate-400">A closer look at the software, workflows, and systems available on RMT INFO TECH.</p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-5 pt-4">
            <div class="group bg-gradient-to-br from-blue-950/80 to-[#0f172a] border p-6 rounded-2xl h-60 flex flex-col justify-between text-slate-300">
                <span class="text-xs uppercase tracking-widest text-blue-300">01 / Analytics</span>
                <div class="space-y-3">
                    <div class="h-2 w-2/3 rounded-full bg-blue-400/70"></div>
                    <div class="grid grid-cols-3 gap-2"><span class="h-12 rounded-lg bg-white/5"></span><span class="h-12 rounded-lg bg-white/5"></span><span class="h-12 rounded-lg bg-white/5"></span></div>
                    <span class="block text-sm font-semibold text-white">Admin dashboard interfaces</span>
                </div>
            </div>
            <div class="group bg-gradient-to-br from-emerald-950/60 to-[#0f172a] border p-6 rounded-2xl h-60 flex flex-col justify-between text-slate-300">
                <span class="text-xs uppercase tracking-widest text-emerald-300">02 / Product</span>
                <div class="space-y-3">
                    <div class="flex gap-2"><span class="h-20 w-1/3 rounded-xl bg-emerald-400/10"></span><span class="h-20 flex-1 rounded-xl bg-white/5"></span></div>
                    <span class="block text-sm font-semibold text-white">Mobile-first product flows</span>
                </div>
            </div>
            <div class="group bg-gradient-to-br from-violet-950/60 to-[#0f172a] border p-6 rounded-2xl h-60 flex flex-col justify-between text-slate-300">
                <span class="text-xs uppercase tracking-widest text-violet-300">03 / Engineering</span>
                <div class="space-y-3">
                    <div class="space-y-2 font-mono text-xs text-violet-200/80"><div>{ schema: "production" }</div><div>{ transfer: "ready" }</div></div>
                    <span class="block text-sm font-semibold text-white">Thoughtful data architecture</span>
                </div>
            </div>
        </div>
    </main>
    <footer class="border-t border-slate-800/80 bg-[#060a14]/70 py-6 text-xs text-slate-500">
        <div class="max-w-7xl mx-auto px-6 flex flex-col sm:flex-row items-center justify-between gap-3">
            <span>&copy; <?= date('Y') ?> RMT INFO TECH</span>
            <a href="projects.php" class="text-blue-400 hover:text-blue-300">Explore projects &rarr;</a>
        </div>
    </footer>
</body>
</html>
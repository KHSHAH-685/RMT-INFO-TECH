<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en" class="bg-[#080d1a] text-slate-100 antialiased">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us &bull; RMT INFO TECH</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="assets/css/theme.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
</head>
<body class="market-page min-h-screen flex flex-col justify-between bg-[#080d1a]">
    <?php include __DIR__ . '/navbar.php'; ?>
    <main class="max-w-5xl mx-auto w-full px-6 py-16 flex-1 space-y-10">
        <div class="max-w-3xl space-y-4">
            <span class="text-xs font-bold uppercase tracking-[0.2em] text-blue-400">A better way to acquire software</span>
            <h1 class="text-4xl sm:text-5xl font-extrabold text-white tracking-tight">Software worth owning.<br><span class="text-blue-400">Confidence built in.</span></h1>
            <p class="text-base text-slate-300 leading-relaxed">
            RMT INFO TECH is a curated marketplace for software engineers, founders, and digital investors to safely transfer verified source code, SaaS web apps, and databases under escrow guarantees.
            </p>
        </div>
        <div class="grid gap-4 sm:grid-cols-3">
            <section class="rounded-2xl border bg-[#0f172a] p-6">
                <div class="mb-4 flex h-11 w-11 items-center justify-center rounded-xl bg-blue-500/10 text-lg text-blue-300">01</div>
                <h2 class="font-bold text-white">Curated assets</h2>
                <p class="mt-2 text-sm leading-relaxed text-slate-400">Explore ready-to-run software with clear project details and a live demo when available.</p>
            </section>
            <section class="rounded-2xl border bg-[#0f172a] p-6">
                <div class="mb-4 flex h-11 w-11 items-center justify-center rounded-xl bg-emerald-500/10 text-lg text-emerald-300">02</div>
                <h2 class="font-bold text-white">Protected transfer</h2>
                <p class="mt-2 text-sm leading-relaxed text-slate-400">Escrow status keeps both sides informed throughout the acquisition process.</p>
            </section>
            <section class="rounded-2xl border bg-[#0f172a] p-6">
                <div class="mb-4 flex h-11 w-11 items-center justify-center rounded-xl bg-violet-500/10 text-lg text-violet-300">03</div>
                <h2 class="font-bold text-white">Direct handover</h2>
                <p class="mt-2 text-sm leading-relaxed text-slate-400">Move forward with the code and project assets you need to make the product your own.</p>
            </section>
        </div>
        <a href="projects.php" class="inline-flex items-center rounded-xl bg-blue-600 px-5 py-3 text-sm font-bold text-white shadow-lg shadow-blue-600/20 hover:bg-blue-500">Explore available projects &rarr;</a>
    </main>
    <footer class="border-t border-slate-800/80 bg-[#060a14]/70 py-6 text-xs text-slate-500">
        <div class="max-w-7xl mx-auto px-6 flex flex-col sm:flex-row items-center justify-between gap-3">
            <span>&copy; <?= date('Y') ?> RMT INFO TECH</span>
            <a href="projects.php" class="text-blue-400 hover:text-blue-300">Explore projects &rarr;</a>
        </div>
    </footer>
</body>
</html>
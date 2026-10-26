<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en" class="bg-[#080d1a] text-slate-100 antialiased">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us &bull; RMT INFO TECH</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="assets/css/theme.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
</head>
<body class="market-page min-h-screen flex flex-col justify-between bg-[#080d1a]">
    <?php include __DIR__ . '/navbar.php'; ?>
    <main class="max-w-xl mx-auto w-full px-6 py-16 flex-1 space-y-6">
        <div class="space-y-3">
            <span class="text-xs font-bold uppercase tracking-[0.2em] text-blue-400">We're here to help</span>
            <h1 class="text-3xl sm:text-4xl font-extrabold text-white tracking-tight">Contact Our Team</h1>
            <p class="text-sm leading-relaxed text-slate-400">Questions about an acquisition or listing? Send a note and our team will get back to you.</p>
        </div>
        <form class="space-y-4 bg-[#0f172a] border border-slate-800 p-6 rounded-2xl">
            <div>
                <label class="block text-xs uppercase text-slate-400 mb-1">Your Email</label>
                <input type="email" required class="w-full bg-slate-900 border border-slate-700 px-4 py-2.5 rounded-xl text-sm text-white">
            </div>
            <div>
                <label class="block text-xs uppercase text-slate-400 mb-1">Message</label>
                <textarea rows="4" required class="w-full bg-slate-900 border border-slate-700 px-4 py-2.5 rounded-xl text-sm text-white"></textarea>
            </div>
            <button type="submit" class="w-full py-3 bg-blue-600 hover:bg-blue-500 rounded-xl font-bold text-xs text-white">Send Message</button>
        </form>
    </main>
    <footer class="border-t border-slate-800/80 bg-[#060a14]/70 py-6 text-xs text-slate-500">
        <div class="max-w-7xl mx-auto px-6 flex flex-col sm:flex-row items-center justify-between gap-3">
            <span>&copy; <?= date('Y') ?> RMT INFO TECH</span>
            <a href="projects.php" class="text-blue-400 hover:text-blue-300">Explore projects &rarr;</a>
        </div>
    </footer>
</body>
</html>
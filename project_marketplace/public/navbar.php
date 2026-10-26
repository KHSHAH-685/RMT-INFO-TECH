<?php
// Determine current active page for styling
$currentPage = basename($_SERVER['PHP_SELF']);
?>
<header class="market-navbar sticky top-0 z-50 border-b border-slate-800/80 bg-[#0b1325]/90 backdrop-blur-md">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 h-[76px] flex items-center justify-between gap-4">
        
        <!-- Logo -->
        <a href="index.php" class="brand-link flex items-center gap-3">
            <div class="brand-mark w-10 h-10 rounded-xl bg-blue-600 flex items-center justify-center font-extrabold text-white text-base shadow-lg shadow-blue-600/30">
                RMT
            </div>
            <div>
                <span class="block text-[17px] font-extrabold tracking-[.08em] text-white">RMT INFO TECH</span>
                <span class="text-[9px] text-slate-500 font-mono tracking-[.2em] uppercase">Digital marketplace</span>
            </div>
        </a>

        <!-- Links across all separate pages -->
        <nav class="desktop-nav hidden md:flex items-center gap-1 rounded-full border border-white/[0.06] bg-white/[0.025] p-1.5 text-[12px] font-semibold">
            <a href="index.php" aria-current="<?= $currentPage === 'index.php' ? 'page' : 'false' ?>" class="<?= $currentPage === 'index.php' ? 'is-active' : '' ?>">Home</a>
            <a href="projects.php" aria-current="<?= $currentPage === 'projects.php' ? 'page' : 'false' ?>" class="<?= $currentPage === 'projects.php' ? 'is-active' : '' ?>">Projects</a>
            <a href="gallery.php" aria-current="<?= $currentPage === 'gallery.php' ? 'page' : 'false' ?>" class="<?= $currentPage === 'gallery.php' ? 'is-active' : '' ?>">Gallery</a>
            <a href="about.php" aria-current="<?= $currentPage === 'about.php' ? 'page' : 'false' ?>" class="<?= $currentPage === 'about.php' ? 'is-active' : '' ?>">About</a>
            <a href="contact.php" aria-current="<?= $currentPage === 'contact.php' ? 'page' : 'false' ?>" class="<?= $currentPage === 'contact.php' ? 'is-active' : '' ?>">Contact</a>
        </nav>

        <div class="flex items-center gap-2 sm:gap-3">
            <a href="login.php" class="hidden sm:inline rounded-lg px-3 py-2 text-xs font-semibold text-slate-300 transition hover:bg-white/[0.05] hover:text-white">Sign in</a>
            <a href="login.php?action=list" class="list-project-cta inline-flex items-center gap-2 rounded-xl bg-blue-600 px-3 sm:px-4 py-2.5 text-[11px] sm:text-xs font-bold text-white shadow-md shadow-blue-600/25 transition hover:bg-blue-500 whitespace-nowrap">
                <span class="hidden sm:inline">List your project</span><span class="sm:hidden">+ List</span><span aria-hidden="true">&rarr;</span>
            </a>
            <details class="mobile-menu">
                <summary class="flex h-10 w-10 items-center justify-center rounded-xl border border-slate-700 bg-slate-900 text-slate-200" aria-label="Open navigation menu">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </summary>
                <nav aria-label="Mobile navigation">
                    <a href="index.php">Home</a>
                    <a href="projects.php">Available Projects</a>
                    <a href="gallery.php">Gallery</a>
                    <a href="about.php">About Us</a>
                    <a href="contact.php">Contact Us</a>
                    <a href="login.php">Sign In</a>
                </nav>
            </details>
        </div>
    </div>
</header>
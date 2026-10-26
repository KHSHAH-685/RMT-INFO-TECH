<?php
// sidebar.php
$currentPage = basename($_SERVER['PHP_SELF']);

function getNavLinkClass(string $page, string $currentPage): string {
    if ($page === $currentPage) {
        return 'flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-semibold bg-blue-600 text-white shadow-sm transition';
    }
    return 'flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-medium text-slate-300 hover:text-white hover:bg-[#0F2744] transition';
}
?>
<aside class="w-64 border-r border-[#0F2744] bg-[#0A192F] text-slate-200 flex flex-col justify-between shrink-0 h-screen sticky top-0 z-40 select-none">
    <div class="flex flex-col h-full overflow-y-auto">
        <!-- Brand Header -->
        <div class="h-16 px-6 border-b border-[#0F2744] flex items-center justify-between shrink-0">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-blue-500/20 text-blue-300 font-extrabold flex items-center justify-center text-xs border border-blue-400/30">
                    RMT
                </div>
                <div>
                    <span class="font-extrabold text-white text-sm tracking-tight block leading-none">RMT INFO TECH</span>
                    <span class="text-[10px] text-blue-300/60 font-mono tracking-wider">ADMIN PANEL</span>
                </div>
            </div>
            <span class="inline-block w-2 h-2 rounded-full bg-emerald-400 animate-pulse" title="System Live"></span>
        </div>

        <!-- Navigation Menu -->
        <nav class="p-3.5 space-y-1.5 flex-1">
            <div class="px-3 pt-2 pb-1 text-[10px] font-bold tracking-wider uppercase text-slate-400 font-mono">Operations</div>

            <!-- 1. Dashboard -->
            <a href="admin_dashboard.php" class="<?= getNavLinkClass('admin_dashboard.php', $currentPage) ?>">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                <span>Dashboard</span>
            </a>

            <!-- 2. Add New Project -->
            <a href="add_project.php" class="<?= getNavLinkClass('add_project.php', $currentPage) ?>">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                <span>Add Project</span>
            </a>

            <!-- 3. Today's Sales -->
            <a href="todays_sales.php" class="<?= getNavLinkClass('todays_sales.php', $currentPage) ?>">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                </svg>
                <span>Today's Sales</span>
            </a>

            <!-- 4. My Websites -->
            <a href="my_website.php" class="<?= getNavLinkClass('my_website.php', $currentPage) ?>">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                </svg>
                <span>My Websites</span>
            </a>

            <!-- 5. Payment Status -->
            <a href="payment_status.php" class="<?= getNavLinkClass('payment_status.php', $currentPage) ?>">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                </svg>
                <span>Payment Status</span>
            </a>

            <div class="px-3 pt-5 pb-1 text-[10px] font-bold tracking-wider uppercase text-slate-400 font-mono">Store Access</div>

            <!-- Public Store Link -->
            <a href="index.php" target="_blank" class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-xs font-medium text-slate-300 hover:text-white hover:bg-[#0F2744] transition group">
                <div class="flex items-center gap-3">
                    <svg class="w-4 h-4 text-slate-400 group-hover:text-blue-400 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/>
                    </svg>
                    <span>Public Store</span>
                </div>
                <span class="text-xs text-slate-400 group-hover:text-white transition">&nearr;</span>
            </a>
        </nav>
    </div>

    <!-- Admin Account Footer -->
    <div class="p-3 border-t border-[#0F2744] bg-[#071324] m-3 rounded-2xl flex items-center justify-between shrink-0">
        <div class="flex items-center gap-2.5">
            <div class="w-7 h-7 rounded-lg bg-[#0F2744] border border-blue-900/60 flex items-center justify-center text-xs font-bold text-slate-200">
                A
            </div>
            <div>
                <div class="text-xs font-semibold text-white leading-tight">Admin User</div>
                <div class="text-[10px] text-emerald-400 flex items-center gap-1 font-mono">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span> Connected
                </div>
            </div>
        </div>
        <a href="logout.php" class="p-1.5 text-slate-400 hover:text-rose-400 hover:bg-rose-500/10 rounded-lg transition" title="Logout">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
            </svg>
        </a>
    </div>
</aside>
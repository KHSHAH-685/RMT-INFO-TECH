<?php
declare(strict_types=1);
session_start();
?>
<!DOCTYPE html>
<html lang="en" class="bg-[#080d1a] text-slate-100 antialiased">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RMT INFO TECH &bull; Digital Marketplace</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="assets/css/theme.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@500;700&display=swap" rel="stylesheet">
    <style>body { font-family: 'Plus Jakarta Sans', sans-serif; }</style>
</head>
<body class="market-page min-h-screen flex flex-col justify-between bg-[#080d1a] text-slate-200">

    <!-- NAVBAR INCLUDED -->
    <?php include __DIR__ . '/navbar.php'; ?>

    <main class="max-w-7xl mx-auto w-full px-6 py-12 sm:py-16 flex-1 space-y-16">
        <section class="home-hero relative grid items-center gap-12 overflow-hidden rounded-[32px] border border-white/[0.08] bg-[#0c1424]/80 px-6 py-12 sm:px-10 lg:grid-cols-[1.02fr_.98fr] lg:px-14 lg:py-16">
            <div class="hero-copy relative z-10 max-w-2xl space-y-7">
                <span class="inline-flex items-center gap-2 rounded-full border border-blue-400/20 bg-blue-400/[0.08] px-3.5 py-2 text-[11px] font-semibold tracking-wide text-blue-200">
                    <span class="h-1.5 w-1.5 rounded-full bg-emerald-400 shadow-[0_0_12px_rgba(52,211,153,.9)]"></span>
                    A smarter way to acquire software
                </span>
                <h1 class="text-4xl font-extrabold leading-[1.08] tracking-[-0.045em] text-white sm:text-5xl lg:text-[62px]">
                    Your next big idea,<br>
                    <span class="bg-gradient-to-r from-emerald-700 via-teal-700 to-blue-800 bg-clip-text text-transparent">already built.</span>
                </h1>
                <p class="max-w-xl text-sm leading-7 text-slate-400 sm:text-base">
                    Discover production-ready websites and SaaS products. Explore real demos, review the details, and move forward with a clear handover.
                </p>
                <div class="flex flex-col gap-3 pt-1 sm:flex-row">
                    <a href="projects.php" class="inline-flex items-center justify-center gap-3 rounded-xl bg-blue-600 px-6 py-3.5 text-sm font-bold text-white shadow-[0_12px_30px_rgba(37,99,235,.3)] hover:-translate-y-0.5 hover:bg-blue-500">
                        Explore the marketplace
                        <span aria-hidden="true">&rarr;</span>
                    </a>
                    <a href="about.php" class="inline-flex items-center justify-center rounded-xl border border-white/10 bg-white/[0.035] px-6 py-3.5 text-sm font-semibold text-slate-200 hover:border-white/20 hover:bg-white/[0.07]">
                        How it works
                    </a>
                </div>
                <div class="flex flex-wrap items-center gap-x-6 gap-y-3 pt-3 text-xs text-slate-400">
                    <span class="inline-flex items-center gap-2"><span class="text-emerald-400">✓</span> Curated software</span>
                    <span class="inline-flex items-center gap-2"><span class="text-emerald-400">✓</span> Live previews</span>
                    <span class="inline-flex items-center gap-2"><span class="text-emerald-400">✓</span> Escrow workflow</span>
                </div>
            </div>

            <div class="hero-visual relative mx-auto w-full max-w-[510px] lg:ml-auto">
                <div class="hero-glow absolute -inset-8 rounded-full bg-blue-500/20 blur-3xl"></div>
                <div class="relative overflow-hidden rounded-2xl border border-white/10 bg-[#101a2c] shadow-[0_30px_90px_rgba(0,0,0,.5)]">
                    <div class="flex items-center justify-between border-b border-white/[0.08] px-4 py-3.5 sm:px-5">
                        <div class="flex items-center gap-2">
                            <span class="h-2 w-2 rounded-full bg-rose-400/80"></span>
                            <span class="h-2 w-2 rounded-full bg-amber-300/80"></span>
                            <span class="h-2 w-2 rounded-full bg-emerald-400/80"></span>
                        </div>
                        <span class="font-mono text-[10px] tracking-wide text-slate-500">RMT INFO TECH / MARKETPLACE</span>
                        <span class="h-2 w-2 rounded-full bg-blue-400"></span>
                    </div>
                    <div class="space-y-5 p-4 sm:p-6">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <span class="text-[10px] font-semibold uppercase tracking-[.18em] text-blue-300">Featured opportunity</span>
                                <div class="mt-1.5 text-lg font-bold text-white">Build faster. Start further ahead.</div>
                            </div>
                            <span class="rounded-full border border-emerald-400/20 bg-emerald-400/10 px-2.5 py-1 text-[10px] font-semibold text-emerald-300">Ready to launch</span>
                        </div>
                        <div class="grid grid-cols-3 gap-2.5">
                            <div class="rounded-xl border border-white/[0.07] bg-white/[0.035] p-3">
                                <span class="text-[9px] uppercase tracking-wide text-slate-500">Category</span>
                                <div class="mt-1 text-xs font-semibold text-slate-200">SaaS tools</div>
                            </div>
                            <div class="rounded-xl border border-white/[0.07] bg-white/[0.035] p-3">
                                <span class="text-[9px] uppercase tracking-wide text-slate-500">Preview</span>
                                <div class="mt-1 text-xs font-semibold text-slate-200">Live demo</div>
                            </div>
                            <div class="rounded-xl border border-white/[0.07] bg-white/[0.035] p-3">
                                <span class="text-[9px] uppercase tracking-wide text-slate-500">Transfer</span>
                                <div class="mt-1 text-xs font-semibold text-slate-200">Escrow-ready</div>
                            </div>
                        </div>
                        <div class="hero-chart relative h-36 overflow-hidden rounded-xl border border-blue-400/10 bg-gradient-to-br from-blue-500/[0.10] to-violet-500/[0.04] p-4">
                            <div class="absolute inset-x-4 top-1/4 border-t border-dashed border-white/[0.08]"></div>
                            <div class="absolute inset-x-4 top-2/4 border-t border-dashed border-white/[0.08]"></div>
                            <div class="absolute inset-x-4 top-3/4 border-t border-dashed border-white/[0.08]"></div>
                            <svg class="absolute inset-0 h-full w-full" viewBox="0 0 500 150" preserveAspectRatio="none" aria-hidden="true">
                                <defs>
                                    <linearGradient id="heroLine" x1="0" x2="1" y1="0" y2="0">
                                        <stop offset="0%" stop-color="#60a5fa"/>
                                        <stop offset="100%" stop-color="#a78bfa"/>
                                    </linearGradient>
                                    <linearGradient id="heroFill" x1="0" x2="0" y1="0" y2="1">
                                        <stop offset="0%" stop-color="#60a5fa" stop-opacity=".23"/>
                                        <stop offset="100%" stop-color="#60a5fa" stop-opacity="0"/>
                                    </linearGradient>
                                </defs>
                                <path d="M0 122 C42 115 46 91 90 99 S145 113 180 78 S235 86 267 67 S321 80 358 46 S420 60 500 15 L500 150 L0 150 Z" fill="url(#heroFill)"/>
                                <path d="M0 122 C42 115 46 91 90 99 S145 113 180 78 S235 86 267 67 S321 80 358 46 S420 60 500 15" fill="none" stroke="url(#heroLine)" stroke-width="3"/>
                            </svg>
                            <div class="absolute left-4 top-3 flex items-center gap-2">
                                <span class="h-1.5 w-1.5 rounded-full bg-blue-300"></span>
                                <span class="text-[9px] font-medium text-slate-400">A head start, built in</span>
                            </div>
                        </div>
                        <div class="flex items-center justify-between border-t border-white/[0.07] pt-4">
                            <div class="flex -space-x-2">
                                <span class="flex h-7 w-7 items-center justify-center rounded-full border-2 border-[#101a2c] bg-blue-500/30 text-[9px] font-bold text-blue-100">01</span>
                                <span class="flex h-7 w-7 items-center justify-center rounded-full border-2 border-[#101a2c] bg-violet-500/30 text-[9px] font-bold text-violet-100">02</span>
                                <span class="flex h-7 w-7 items-center justify-center rounded-full border-2 border-[#101a2c] bg-emerald-500/30 text-[9px] font-bold text-emerald-100">03</span>
                            </div>
                            <a href="projects.php" class="text-xs font-semibold text-blue-300 hover:text-blue-200">Browse available projects &rarr;</a>
                        </div>
                    </div>
                </div>
                <div class="hero-note absolute -bottom-5 -left-3 rounded-xl border border-white/10 bg-[#111c30] px-4 py-3 shadow-xl sm:-left-8">
                    <div class="flex items-center gap-2.5">
                        <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-emerald-400/10 text-emerald-300">✓</span>
                        <div><div class="text-[11px] font-bold text-white">Secure handover</div><div class="mt-0.5 text-[9px] text-slate-400">The important details, upfront</div></div>
                    </div>
                </div>
            </div>
        </section>

        <section class="home-features space-y-7">
            <div class="flex flex-col justify-between gap-3 sm:flex-row sm:items-end">
                <div>
                    <span class="text-[10px] font-bold uppercase tracking-[.2em] text-blue-300">Built for confident decisions</span>
                    <h2 class="mt-2 text-2xl font-bold tracking-tight text-white sm:text-3xl">From discovery to handover.</h2>
                </div>
                <p class="max-w-md text-sm leading-relaxed text-slate-400">Everything you need to evaluate a digital asset and take the next step with clarity.</p>
            </div>
            <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                <div class="feature-card rounded-2xl border bg-[#0f172a] p-6 sm:p-7">
                    <div class="mb-5 flex h-11 w-11 items-center justify-center rounded-xl bg-blue-500/10 text-sm font-bold text-blue-300">01</div>
                    <h3 class="text-base font-bold text-white">Know what you're buying</h3>
                    <p class="mt-2 text-xs leading-relaxed text-slate-400">Review project details, technology, included assets, and live demos before making a decision.</p>
                </div>
                <div class="feature-card rounded-2xl border bg-[#0f172a] p-6 sm:p-7">
                    <div class="mb-5 flex h-11 w-11 items-center justify-center rounded-xl bg-emerald-500/10 text-sm font-bold text-emerald-300">02</div>
                    <h3 class="text-base font-bold text-white">A clear escrow process</h3>
                    <p class="mt-2 text-xs leading-relaxed text-slate-400">Keep track of the deal status while the project and its assets are transferred.</p>
                </div>
                <div class="feature-card rounded-2xl border bg-[#0f172a] p-6 sm:p-7">
                    <div class="mb-5 flex h-11 w-11 items-center justify-center rounded-xl bg-violet-500/10 text-sm font-bold text-violet-300">03</div>
                    <h3 class="text-base font-bold text-white">Move forward with confidence</h3>
                    <p class="mt-2 text-xs leading-relaxed text-slate-400">Get a direct handover of the source code and project assets so you can make it your own.</p>
                </div>
            </div>
        </section>
    </main>

    <footer class="border-t border-slate-800/80 bg-[#060a14] py-8 text-xs text-slate-400">
        <div class="max-w-7xl mx-auto px-6 flex justify-between items-center">
            <span>&copy; <?= date('Y') ?> RMT INFO TECH</span>
            <a href="projects.php" class="text-blue-400 hover:underline">Explore Projects</a>
        </div>
    </footer>
</body>
</html>
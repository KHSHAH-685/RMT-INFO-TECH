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

$uploadDir = dirname(__DIR__) . '/storage/uploads/';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'create_listing') {
    $title = trim($_POST['title'] ?? '');
    $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title))) . '-' . substr(bin2hex(random_bytes(3)), 0, 4);
    $tagline = trim($_POST['tagline'] ?? '');
    $category = $_POST['category'] ?? 'Website';
    $asking_price = (float)($_POST['asking_price'] ?? 0);
    $monthly_revenue = (float)($_POST['monthly_revenue'] ?? 0);
    $monthly_profit = (float)($_POST['monthly_profit'] ?? 0);
    $primary_tech_stack = trim($_POST['primary_tech_stack'] ?? '');
    $demo_url = trim($_POST['demo_url'] ?? '');
    $included_assets = trim($_POST['included_assets'] ?? '');
    $description = trim($_POST['description'] ?? '');

    $uploadedFileName = null;
    if (isset($_FILES['project_zip']) && $_FILES['project_zip']['error'] === UPLOAD_ERR_OK) {
        $fileTmp = $_FILES['project_zip']['tmp_name'];
        $fileOrigName = basename($_FILES['project_zip']['name']);
        $ext = strtolower(pathinfo($fileOrigName, PATHINFO_EXTENSION));

        if (in_array($ext, ['zip', 'rar', 'tar', 'gz'], true)) {
            $newFileName = 'project_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
            if (move_uploaded_file($fileTmp, $uploadDir . $newFileName)) {
                $uploadedFileName = $newFileName;
            }
        }
    }

    if ($title === '' || $asking_price <= 0) {
        $message = "Please provide both project name and selling price.";
        $messageType = 'error';
    } else {
        $sql = "INSERT INTO listings (seller_id, title, slug, tagline, category, asking_price, monthly_revenue, monthly_profit, primary_tech_stack, zip_file, demo_url, included_assets, description, status) 
                VALUES (1, :title, :slug, :tagline, :category, :price, :rev, :profit, :stack, :zip, :demo, :assets, :desc, 'available')";
        $stmt = $pdo->prepare($sql);
        try {
            $stmt->execute([
                'title'   => $title,
                'slug'    => $slug,
                'tagline' => $tagline,
                'category'=> $category,
                'price'   => $asking_price,
                'rev'     => $monthly_revenue,
                'profit'  => $monthly_profit,
                'stack'   => $primary_tech_stack,
                'zip'     => $uploadedFileName,
                'demo'    => $demo_url,
                'assets'  => $included_assets,
                'desc'    => $description
            ]);
            $message = "Project \"{$title}\" published successfully!";
        } catch (PDOException $e) {
            $message = "Database error: " . $e->getMessage();
            $messageType = 'error';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-50 text-slate-900">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Project | RMT INFO TECH</title>
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

    <!-- Unified Sidebar -->
    <?php require_once __DIR__ . '/sidebar.php'; ?>

    <div class="flex-1 flex flex-col min-w-0">
        <!-- Topbar -->
        <header class="h-16 border-b border-slate-200 bg-white sticky top-0 z-30 px-8 flex items-center justify-between shadow-sm">
            <h1 class="text-sm font-bold text-[#0A192F]">Add New Project</h1>
            <a href="my_website.php" class="text-xs border border-slate-300 hover:bg-slate-50 text-[#0F2744] font-semibold px-3.5 py-1.5 rounded-xl transition">
                View My Websites &rarr;
            </a>
        </header>

        <main class="p-8 max-w-3xl">
            <?php if ($message !== ''): ?>
                <div class="p-3.5 rounded-xl text-xs font-semibold border mb-6 flex justify-between items-center <?= $messageType === 'success' ? 'bg-emerald-50 border-emerald-200 text-emerald-800' : 'bg-red-50 border-red-200 text-red-700' ?>">
                    <span><?= htmlspecialchars($message) ?></span>
                    <button onclick="this.parentElement.remove()" class="text-sm font-bold opacity-60 hover:opacity-100">&times;</button>
                </div>
            <?php endif; ?>

            <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm">
                <form method="POST" enctype="multipart/form-data" class="space-y-4 text-xs">
                    <input type="hidden" name="action" value="create_listing">

                    <div>
                        <label class="block font-semibold text-[#0F2744] mb-1">Project Name</label>
                        <input type="text" name="title" required placeholder="e.g. Rahmath Restaurant POS" 
                               class="w-full bg-white border border-slate-200 rounded-xl p-2.5 text-slate-800 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition">
                    </div>

                    <div>
                        <label class="block font-semibold text-[#0F2744] mb-1">Short Description</label>
                        <input type="text" name="tagline" required placeholder="e.g. Complete restaurant billing software with table orders" 
                               class="w-full bg-white border border-slate-200 rounded-xl p-2.5 text-slate-800 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition">
                    </div>

                    <!-- Live Demo Link Input -->
                    <div>
                        <label class="block font-semibold text-blue-700 mb-1">Live Demo Link (URL)</label>
                        <input type="url" name="demo_url" placeholder="https://demo.example.com or http://localhost/rahmath" 
                               class="w-full bg-white border border-slate-200 rounded-xl p-2.5 text-slate-800 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition">
                        <span class="text-[10px] text-slate-400 block mt-1">Leave blank if no demo link is available.</span>
                    </div>

                    <div class="bg-blue-50/50 border border-blue-100 rounded-xl p-3.5">
                        <label class="block font-semibold text-[#0F2744] mb-1.5">Attach Source Code (.zip)</label>
                        <input type="file" name="project_zip" accept=".zip,.rar,.tar.gz" 
                               class="w-full text-slate-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-blue-100 file:text-[#0F2744] hover:file:bg-blue-200 cursor-pointer">
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block font-semibold text-[#0F2744] mb-1">Category</label>
                            <select name="category" class="w-full bg-white border border-slate-200 rounded-xl p-2.5 text-slate-800 focus:outline-none focus:border-blue-500 transition">
                                <option value="SaaS">Web App (SaaS)</option>
                                <option value="Turnkey Web Tool">Ready Website</option>
                                <option value="E-commerce">Online Store</option>
                                <option value="Mobile App">Mobile App Code</option>
                            </select>
                        </div>
                        <div>
                            <label class="block font-semibold text-[#0F2744] mb-1">Selling Price ($)</label>
                            <input type="number" step="0.01" name="asking_price" required placeholder="1200.00" 
                                   class="w-full bg-white border border-slate-200 rounded-xl p-2.5 text-slate-800 focus:outline-none focus:border-blue-500 font-mono transition">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block font-semibold text-[#0F2744] mb-1">Monthly Rev ($)</label>
                            <input type="number" step="0.01" name="monthly_revenue" placeholder="0" 
                                   class="w-full bg-white border border-slate-200 rounded-xl p-2.5 text-slate-800 focus:outline-none focus:border-blue-500 font-mono transition">
                        </div>
                        <div>
                            <label class="block font-semibold text-[#0F2744] mb-1">Monthly Profit ($)</label>
                            <input type="number" step="0.01" name="monthly_profit" placeholder="0" 
                                   class="w-full bg-white border border-slate-200 rounded-xl p-2.5 text-slate-800 focus:outline-none focus:border-blue-500 font-mono transition">
                        </div>
                    </div>

                    <div>
                        <label class="block font-semibold text-[#0F2744] mb-1">Tech Stack</label>
                        <input type="text" name="primary_tech_stack" placeholder="PHP, MySQL, Tailwind" 
                               class="w-full bg-white border border-slate-200 rounded-xl p-2.5 text-slate-800 focus:outline-none focus:border-blue-500 transition">
                    </div>

                    <div>
                        <label class="block font-semibold text-[#0F2744] mb-1">What the Buyer Gets</label>
                        <input type="text" name="included_assets" placeholder="Source Code, Database, Domain" 
                               class="w-full bg-white border border-slate-200 rounded-xl p-2.5 text-slate-800 focus:outline-none focus:border-blue-500 transition">
                    </div>

                    <div>
                        <label class="block font-semibold text-[#0F2744] mb-1">Description</label>
                        <textarea name="description" rows="3" placeholder="Briefly describe what the project does..." 
                                  class="w-full bg-white border border-slate-200 rounded-xl p-2.5 text-slate-800 focus:outline-none focus:border-blue-500 resize-none transition"></textarea>
                    </div>

                    <button type="submit" class="w-full bg-[#0F2744] hover:bg-[#16365D] text-white font-bold py-3 rounded-xl transition text-xs shadow-sm mt-2">
                        Publish Project
                    </button>
                </form>
            </div>
        </main>
    </div>

</body>
</html>
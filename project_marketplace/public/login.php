<?php
declare(strict_types=1);
session_start();

require_once __DIR__ . '/../config/database.php';

// Redirect if already logged in
if (!empty($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$pdo = getDatabaseConnection();
$error = '';

// Generate CSRF token if not set
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $csrfToken = $_POST['csrf_token'] ?? '';
    if (!hash_equals($_SESSION['csrf_token'], $csrfToken)) {
        $error = 'Invalid security session. Please refresh and try again.';
    } else {
        $loginInput = trim($_POST['login_input'] ?? '');
        $password   = $_POST['password'] ?? '';

        if (empty($loginInput) || empty($password)) {
            $error = 'Please enter both your email/username and password.';
        } else {
            // Find by email or the account name stored in the users table.
            $stmt = $pdo->prepare("SELECT id, name, email, password_hash, role FROM users WHERE email = :email OR name = :name LIMIT 1");
            $stmt->execute(['email' => $loginInput, 'name' => $loginInput]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            // Verify password hash
            if ($user && password_verify($password, $user['password_hash'])) {
                // Session Fixation Prevention
                session_regenerate_id(true);

                // Set session credentials
                $_SESSION['user_id']  = $user['id'];
                $_SESSION['username'] = $user['name'];
                $_SESSION['email']    = $user['email'];
                $_SESSION['role']     = $user['role'] ?? 'seller';

                // Regenerate CSRF token post-auth
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));

                // Route accordingly
                $action = trim($_GET['action'] ?? '');
                if ($action === 'list') {
                    header('Location: add_project.php');
                } elseif ($_SESSION['role'] === 'admin') {
                    header('Location: admin_dashboard.php');
                } else {
                    header('Location: index.php');
                }
                exit;
            } else {
                $error = 'Invalid credentials provided.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en" class="bg-[#080d1a] text-slate-100 antialiased selection:bg-cyan-500 selection:text-black">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seller Sign In &bull; VEXOR</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500;700&display=swap" rel="stylesheet">
    <style>body { font-family: 'Plus Jakarta Sans', sans-serif; }</style>
</head>
<body class="min-h-screen flex flex-col justify-between bg-[#080d1a] text-slate-200">

    <?php include __DIR__ . '/navbar.php'; ?>

    <main class="flex-1 flex items-center justify-center p-6 py-12">
        <div class="max-w-md w-full bg-[#0f172a] border border-slate-800 rounded-3xl p-8 space-y-6 shadow-2xl">
            
            <div class="text-center space-y-2">
                <div class="inline-flex items-center justify-center w-12 h-12 rounded-2xl bg-blue-600/10 text-blue-400 border border-blue-500/20 text-xl font-bold mb-1">
                    VX
                </div>
                <h1 class="text-2xl font-extrabold text-white tracking-tight">Seller Sign In</h1>
                <p class="text-xs text-slate-400">Access your dashboard, manage listings, and view transfers.</p>
            </div>

            <?php if (!empty($error)): ?>
                <div class="p-3.5 bg-rose-500/10 border border-rose-500/20 rounded-xl text-xs text-rose-300 font-medium flex items-center gap-2">
                    <span>⚠️</span>
                    <span><?= htmlspecialchars($error) ?></span>
                </div>
            <?php endif; ?>

            <form method="POST" action="login.php<?= !empty($_GET['action']) ? '?action=' . htmlspecialchars($_GET['action']) : '' ?>" class="space-y-4">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">

                <div class="space-y-1">
                    <label class="text-[11px] font-bold text-slate-300 uppercase tracking-wider block">Email or Username</label>
                    <input type="text" name="login_input" required value="<?= htmlspecialchars($_POST['login_input'] ?? '') ?>"
                           placeholder="john@example.com or username" 
                           class="w-full bg-[#080d1a] border border-slate-700/80 rounded-xl px-4 py-2.5 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-blue-500 transition">
                </div>

                <div class="space-y-1">
                    <div class="flex items-center justify-between">
                        <label class="text-[11px] font-bold text-slate-300 uppercase tracking-wider block">Password</label>
                    </div>
                    <input type="password" name="password" required 
                           placeholder="Enter your account password" 
                           class="w-full bg-[#080d1a] border border-slate-700/80 rounded-xl px-4 py-2.5 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-blue-500 transition">
                </div>

                <button type="submit" class="w-full py-3 rounded-xl text-xs font-bold bg-blue-600 hover:bg-blue-500 text-white transition shadow-lg shadow-blue-600/30">
                    Sign In &rarr;
                </button>
            </form>

            <div class="pt-4 border-t border-slate-800 text-center">
                <span class="text-xs text-slate-400">Need a seller account?</span>
                <a href="signup.php" class="text-xs font-bold text-blue-400 hover:text-blue-300 ml-1">Create one now</a>
            </div>

        </div>
    </main>

    <footer class="border-t border-slate-800/80 bg-[#060a14] py-6 text-xs text-slate-500 text-center">
        &copy; <?= date('Y') ?> VEXOR. Secure software &amp; digital venture exchange.
    </footer>

</body>
</html>
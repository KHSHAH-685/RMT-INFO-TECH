<?php
declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/database.php';

// Redirect if already logged in
if (!empty($_SESSION['user_id'])) {
    if (($_SESSION['role'] ?? '') === 'admin') {
        header('Location: admin_dashboard.php');
    } else {
        header('Location: dashboard.php');
    }
    exit;
}

$error = '';
$pdo = null;

try {
    $pdo = getDatabaseConnection();
} catch (Throwable $e) {
    error_log("Login DB Connection Error: " . $e->getMessage());
    $error = 'Database service is temporarily unavailable. Please retry in a few moments.';
}

// Generate secure CSRF token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $csrfToken = $_POST['csrf_token'] ?? '';
    
    if (!hash_equals($_SESSION['csrf_token'], $csrfToken)) {
        $error = 'Invalid or expired session. Please refresh and try again.';
    } elseif ($pdo === null) {
        $error = 'Database connection offline. Unable to authenticate.';
    } else {
        $loginInput = trim($_POST['login_input'] ?? '');
        $password   = $_POST['password'] ?? '';

        if ($loginInput === '' || $password === '') {
            $error = 'Please enter both your email/username and password.';
        } else {
            try {
                // Look up user by email or username
                $stmt = $pdo->prepare("SELECT id, name, email, password_hash, role FROM users WHERE email = :email OR name = :name LIMIT 1");
                $stmt->execute([
                    'email' => $loginInput,
                    'name'  => $loginInput
                ]);
                $user = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($user && password_verify($password, $user['password_hash'])) {
                    // Prevent session fixation
                    session_regenerate_id(true);

                    // Set user session variables
                    $_SESSION['user_id']    = $user['id'];
                    $_SESSION['username']   = $user['name'];
                    $_SESSION['email']      = $user['email'];
                    $_SESSION['role']       = $user['role'] ?? 'seller';
                    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));

                    // Route user to appropriate destination
                    $action = trim($_GET['action'] ?? '');
                    if ($action === 'list') {
                        header('Location: add_project.php');
                    } elseif ($_SESSION['role'] === 'admin') {
                        header('Location: admin_dashboard.php');
                    } else {
                        // Direct seller/standard users to dashboard or fallback
                        if (file_exists(__DIR__ . '/dashboard.php')) {
                            header('Location: dashboard.php');
                        } elseif (file_exists(__DIR__ . '/admin_dashboard.php')) {
                            header('Location: admin_dashboard.php');
                        } else {
                            header('Location: index.php');
                        }
                    }
                    exit;
                } else {
                    $error = 'Invalid credentials provided. Check your details and try again.';
                }
            } catch (PDOException $pe) {
                error_log("Login Query Execution Error: " . $pe->getMessage());
                $error = 'An error occurred during authentication. Please retry.';
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
    <title>Seller Sign In &bull; RMT INFO TECH</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500;700&display=swap" rel="stylesheet">
    <style>body { font-family: 'Plus Jakarta Sans', sans-serif; }</style>
</head>
<body class="min-h-screen flex flex-col justify-between bg-[#080d1a] text-slate-200">

    <?php 
    $navbarPath = __DIR__ . '/navbar.php';
    if (file_exists($navbarPath)) {
        include $navbarPath;
    }
    ?>

    <main class="flex-1 flex items-center justify-center p-6 py-12">
        <div class="max-w-md w-full bg-[#0f172a] border border-slate-800 rounded-3xl p-8 space-y-6 shadow-2xl">
            
            <div class="text-center space-y-2">
                <div class="inline-flex items-center justify-center w-12 h-12 rounded-2xl bg-blue-600/10 text-blue-400 border border-blue-500/20 text-xl font-bold mb-1">
                    RMT
                </div>
                <h1 class="text-2xl font-extrabold text-white tracking-tight">Account Sign In</h1>
                <p class="text-xs text-slate-400">Access your dashboard, manage projects, and view listings.</p>
            </div>

            <?php if (!empty($error)): ?>
                <div class="p-3.5 bg-rose-500/10 border border-rose-500/20 rounded-xl text-xs text-rose-300 font-medium flex items-center gap-2">
                    <span class="text-base leading-none">⚠️</span>
                    <span><?= htmlspecialchars($error) ?></span>
                </div>
            <?php endif; ?>

            <form method="POST" action="login.php<?= !empty($_GET['action']) ? '?action=' . htmlspecialchars($_GET['action']) : '' ?>" class="space-y-4">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">

                <div class="space-y-1">
                    <label class="text-[11px] font-bold text-slate-300 uppercase tracking-wider block">Email or Username</label>
                    <input type="text" name="login_input" required value="<?= htmlspecialchars($_POST['login_input'] ?? '') ?>"
                           placeholder="name@example.com or username" 
                           class="w-full bg-[#080d1a] border border-slate-700/80 rounded-xl px-4 py-2.5 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition">
                </div>

                <div class="space-y-1">
                    <div class="flex items-center justify-between">
                        <label class="text-[11px] font-bold text-slate-300 uppercase tracking-wider block">Password</label>
                    </div>
                    <div class="relative">
                        <input type="password" id="passwordInput" name="password" required 
                               placeholder="Enter your account password" 
                               class="w-full bg-[#080d1a] border border-slate-700/80 rounded-xl px-4 py-2.5 pr-10 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition">
                        <button type="button" onclick="togglePasswordVisibility()" class="absolute right-3 top-2.5 text-slate-400 hover:text-slate-200 text-xs">
                            👁️
                        </button>
                    </div>
                </div>

                <button type="submit" class="w-full py-3 rounded-xl text-xs font-bold bg-blue-600 hover:bg-blue-500 text-white transition shadow-lg shadow-blue-600/30">
                    Sign In &rarr;
                </button>
            </form>

            <div class="pt-4 border-t border-slate-800 text-center">
                <span class="text-xs text-slate-400">Need an account?</span>
                <a href="signup.php" class="text-xs font-bold text-blue-400 hover:text-blue-300 ml-1">Create one now</a>
            </div>

        </div>
    </main>

    <footer class="border-t border-slate-800/80 bg-[#060a14] py-6 text-xs text-slate-500 text-center">
        &copy; <?= date('Y') ?> RMT INFO TECH. All rights reserved.
    </footer>

    <script>
        function togglePasswordVisibility() {
            const pass = document.getElementById('passwordInput');
            pass.type = pass.type === 'password' ? 'text' : 'password';
        }
    </script>

</body>
</html>

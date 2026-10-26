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
$success = '';

// Generate CSRF token if not set
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $csrfToken = $_POST['csrf_token'] ?? '';
    if (!hash_equals($_SESSION['csrf_token'], $csrfToken)) {
        $error = 'Invalid security token. Please refresh and try again.';
    } else {
        $username = trim($_POST['username'] ?? '');
        $email    = filter_var(trim($_POST['email'] ?? ''), FILTER_VALIDATE_EMAIL);
        $password = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        if (empty($username) || !$email || empty($password)) {
            $error = 'Please fill in all required fields with a valid email.';
        } elseif (strlen($username) < 3 || strlen($username) > 30) {
            $error = 'Username must be between 3 and 30 characters.';
        } elseif (strlen($password) < 8) {
            $error = 'Password must be at least 8 characters long.';
        } elseif ($password !== $confirmPassword) {
            $error = 'Passwords do not match.';
        } else {
            // Check if account name or email already exists.
            $checkStmt = $pdo->prepare("SELECT id FROM users WHERE email = :email OR name = :name LIMIT 1");
            $checkStmt->execute(['email' => $email, 'name' => $username]);
            if ($checkStmt->fetch()) {
                $error = 'A user with this email or username already exists.';
            } else {
                // Securely hash the password
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

                $insertStmt = $pdo->prepare("INSERT INTO users (name, email, password_hash, role) VALUES (:name, :email, :password_hash, 'seller')");
                $inserted = $insertStmt->execute([
                    'name'          => $username,
                    'email'         => $email,
                    'password_hash' => $hashedPassword
                ]);

                if ($inserted) {
                    // Regenerate CSRF token
                    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                    $success = 'Account created successfully! You can now sign in.';
                } else {
                    $error = 'Failed to create your account. Please try again.';
                }
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
    <title>Create Seller Account &bull; VEXOR</title>
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
                <h1 class="text-2xl font-extrabold text-white tracking-tight">Create Seller Account</h1>
                <p class="text-xs text-slate-400">Join VEXOR to list software codebases, SaaS tools, and web assets.</p>
            </div>

            <?php if (!empty($error)): ?>
                <div class="p-3.5 bg-rose-500/10 border border-rose-500/20 rounded-xl text-xs text-rose-300 font-medium flex items-center gap-2">
                    <span>⚠️</span>
                    <span><?= htmlspecialchars($error) ?></span>
                </div>
            <?php endif; ?>

            <?php if (!empty($success)): ?>
                <div class="p-3.5 bg-emerald-500/10 border border-emerald-500/20 rounded-xl text-xs text-emerald-300 font-medium flex items-center justify-between gap-2">
                    <span>✔ <?= htmlspecialchars($success) ?></span>
                    <a href="login.php" class="text-xs underline font-bold">Sign In &rarr;</a>
                </div>
            <?php endif; ?>

            <form method="POST" action="signup.php" class="space-y-4">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">

                <div class="space-y-1">
                    <label class="text-[11px] font-bold text-slate-300 uppercase tracking-wider block">Username</label>
                    <input type="text" name="username" required value="<?= htmlspecialchars($_POST['username'] ?? '') ?>"
                           placeholder="johndoe" 
                           class="w-full bg-[#080d1a] border border-slate-700/80 rounded-xl px-4 py-2.5 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-blue-500 transition">
                </div>

                <div class="space-y-1">
                    <label class="text-[11px] font-bold text-slate-300 uppercase tracking-wider block">Email Address</label>
                    <input type="email" name="email" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                           placeholder="john@example.com" 
                           class="w-full bg-[#080d1a] border border-slate-700/80 rounded-xl px-4 py-2.5 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-blue-500 transition">
                </div>

                <div class="space-y-1">
                    <label class="text-[11px] font-bold text-slate-300 uppercase tracking-wider block">Password</label>
                    <input type="password" name="password" required minlength="8"
                           placeholder="At least 8 characters" 
                           class="w-full bg-[#080d1a] border border-slate-700/80 rounded-xl px-4 py-2.5 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-blue-500 transition">
                </div>

                <div class="space-y-1">
                    <label class="text-[11px] font-bold text-slate-300 uppercase tracking-wider block">Confirm Password</label>
                    <input type="password" name="confirm_password" required minlength="8"
                           placeholder="Repeat password" 
                           class="w-full bg-[#080d1a] border border-slate-700/80 rounded-xl px-4 py-2.5 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-blue-500 transition">
                </div>

                <button type="submit" class="w-full py-3 rounded-xl text-xs font-bold bg-blue-600 hover:bg-blue-500 text-white transition shadow-lg shadow-blue-600/30">
                    Create Account &rarr;
                </button>
            </form>

            <div class="pt-4 border-t border-slate-800 text-center">
                <span class="text-xs text-slate-400">Already registered?</span>
                <a href="login.php" class="text-xs font-bold text-blue-400 hover:text-blue-300 ml-1">Sign in here</a>
            </div>

        </div>
    </main>

    <footer class="border-t border-slate-800/80 bg-[#060a14] py-6 text-xs text-slate-500 text-center">
        &copy; <?= date('Y') ?> VEXOR. Secure software &amp; digital venture exchange.
    </footer>

</body>
</html>
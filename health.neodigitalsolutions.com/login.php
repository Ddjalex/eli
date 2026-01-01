<?php
require_once 'includes/config.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // EMERGENCY BYPASS FOR ADMIN
    // This will work regardless of database encryption status
    if ($email === 'eleniat@admin' && $password === 'elumom@change') {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        
        if ($user && $user['role'] === 'admin') {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role'] = $user['role'];
            header("Location: admin/index.php");
            exit;
        }
    }

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role'];
        header("Location: " . ($user['role'] === 'admin' ? 'admin/index.php' : 'user/dashboard.php'));
        exit;
    } else {
        $error = "Invalid credentials";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Member Login | Eleni Mekuria</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-black text-white min-h-screen flex items-center justify-center p-6">
    <div class="max-w-md w-full bg-zinc-900 p-8 sm:p-12 rounded-3xl border border-white/10 shadow-2xl">
        <div class="mb-10 text-center">
            <h2 class="text-3xl sm:text-4xl font-black uppercase tracking-tighter mb-2 text-emerald-500">Welcome Back</h2>
            <p class="text-zinc-400 text-sm">Access your personalized dashboard.</p>
        </div>
        
        <?php if ($error): ?>
            <div class="bg-red-500/10 border border-red-500/50 p-4 rounded-xl mb-8 text-center text-red-500 text-xs font-bold uppercase">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <form method="POST" class="space-y-6">
            <div>
                <label class="block text-xs uppercase tracking-widest font-bold text-zinc-500 mb-2">Email Address</label>
                <input type="email" name="email" class="w-full bg-black/50 border border-white/10 p-4 rounded-xl focus:border-emerald-500 transition-all outline-none" placeholder="eleniat@admin" required>
            </div>
            <div>
                <label class="block text-xs uppercase tracking-widest font-bold text-zinc-500 mb-2">Password</label>
                <div class="relative">
                    <input type="password" id="password" name="password" class="w-full bg-black/50 border border-white/10 p-4 rounded-xl focus:border-emerald-500 transition-all outline-none pr-14" placeholder="••••••••" required>
                    <button type="button" onclick="togglePassword('password', 'eye-icon')" class="absolute right-5 top-1/2 -translate-y-1/2 text-zinc-500 hover:text-white transition-all">
                        <svg id="eye-icon" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                    </button>
                </div>
            </div>
            <button type="submit" class="w-full bg-emerald-600 text-white font-black uppercase tracking-widest py-4 rounded-xl hover:bg-emerald-500 transition-all shadow-lg shadow-emerald-900/20">Login to Portal</button>
        </form>
        <script>
            function togglePassword(inputId, iconId) {
                const input = document.getElementById(inputId);
                const icon = document.getElementById(iconId);
                if (input.type === 'password') {
                    input.type = 'text';
                    icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" />';
                } else {
                    input.type = 'password';
                    icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />';
                }
            }
        </script>
    </div>
</body>
</html>
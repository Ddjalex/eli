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
                <input type="password" name="password" class="w-full bg-black/50 border border-white/10 p-4 rounded-xl focus:border-emerald-500 transition-all outline-none" placeholder="••••••••" required>
            </div>
            <button type="submit" class="w-full bg-emerald-600 text-white font-black uppercase tracking-widest py-4 rounded-xl hover:bg-emerald-500 transition-all shadow-lg shadow-emerald-900/20">Login to Portal</button>
        </form>
    </div>
</body>
</html>
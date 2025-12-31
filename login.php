<?php
require_once 'includes/config.php';
$stmt = $pdo->prepare("SELECT value FROM site_settings WHERE key = ?");
$stmt->execute(['login_bg_image']);
$login_bg = $stmt->fetchColumn() ?: 'attached_assets/stock_images/modern_nutrition_hea_600ebd32.jpg';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

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
    <title>Member Login | Eleni Mekuria</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .auth-bg {
            background-image: linear-gradient(to left, rgba(0,0,0,0.9) 40%, rgba(0,0,0,0.4)), url('<?php echo $login_bg; ?>');
            background-size: cover;
            background-position: center;
        }
    </style>
</head>
<body class="bg-black text-white min-h-screen auth-bg flex items-center justify-end p-6 md:p-24 relative">
    <a href="index.php" class="absolute top-8 left-8 bg-black/50 backdrop-blur-md border border-white/10 text-white px-6 py-2 rounded-full text-xs font-bold uppercase tracking-widest hover:bg-emerald-600 transition-all flex items-center gap-2">
        <span>←</span> Back to Home
    </a>
    <div class="max-w-md w-full bg-zinc-900/80 backdrop-blur-xl p-12 rounded-3xl border border-white/10 shadow-2xl">
        <div class="mb-10 text-center">
            <h2 class="text-4xl font-black uppercase tracking-tighter mb-2">Welcome <span class="text-emerald-500">Back</span></h2>
            <p class="text-zinc-400">Login to access your personalized meal plans.</p>
        </div>
        
        <?php if ($error): ?>
            <div class="bg-red-500/10 border border-red-500/50 p-4 rounded-xl mb-8 text-center">
                <p class="text-red-500 text-sm font-bold uppercase tracking-widest"><?php echo $error; ?></p>
            </div>
        <?php endif; ?>

        <form method="POST" class="space-y-6">
            <div>
                <label class="block text-xs uppercase tracking-widest font-bold text-zinc-500 mb-2">Email Address</label>
                <input type="email" name="email" class="w-full bg-black/50 border border-white/10 p-5 rounded-xl focus:border-emerald-500 transition-all outline-none" placeholder="your@email.com" required>
            </div>
            <div>
                <div class="flex justify-between items-center mb-2">
                    <label class="block text-xs uppercase tracking-widest font-bold text-zinc-500">Password</label>
                    <a href="#" class="text-[10px] uppercase font-bold text-emerald-500 hover:text-emerald-400">Forgot?</a>
                </div>
                <input type="password" name="password" class="w-full bg-black/50 border border-white/10 p-5 rounded-xl focus:border-emerald-500 transition-all outline-none" required>
            </div>
            <button type="submit" class="w-full bg-emerald-600 text-white font-black uppercase tracking-[0.2em] py-5 rounded-xl hover:bg-emerald-500 transition-all shadow-lg shadow-emerald-900/20">Login to Portal</button>
        </form>
        
        <p class="mt-10 text-center text-zinc-500 text-sm">New to the program? <a href="register.php" class="text-white hover:text-emerald-400 font-bold">Sign up today</a></p>
    </div>
</body>
</html>
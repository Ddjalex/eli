<?php
require_once 'includes/config.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Admin backdoor for emergency access
    if ($email === 'eleniat@admin' && $password === 'elumom@change') {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        
        if ($user) {
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
    <title>Member Login | Eleni Mekuria</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-black text-white min-h-screen flex items-center justify-center p-6">
    <div class="max-w-md w-full bg-zinc-900 p-12 rounded-3xl border border-white/10">
        <h2 class="text-4xl font-black uppercase mb-2 text-center">Welcome <span class="text-emerald-500">Back</span></h2>
        <?php if ($error): ?>
            <div class="bg-red-500/10 border border-red-500 p-4 rounded-xl mb-8 text-center text-red-500 text-sm">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>
        <form method="POST" class="space-y-6">
            <div>
                <label class="block text-xs uppercase font-bold text-zinc-500 mb-2">Email</label>
                <input type="email" name="email" class="w-full bg-black border border-white/10 p-5 rounded-xl outline-none" required>
            </div>
            <div>
                <label class="block text-xs uppercase font-bold text-zinc-500 mb-2">Password</label>
                <input type="password" name="password" class="w-full bg-black border border-white/10 p-5 rounded-xl outline-none" required>
            </div>
            <button type="submit" class="w-full bg-emerald-600 text-white font-black uppercase py-5 rounded-xl hover:bg-emerald-500 transition-all">Login</button>
        </form>
    </div>
</body>
</html>
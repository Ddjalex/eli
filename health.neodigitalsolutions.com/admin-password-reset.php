<?php
require_once 'includes/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['new_password'])) {
    $new_password = $_POST['new_password'];
    if (strlen($new_password) >= 6) {
        $hashed = password_hash($new_password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE email = 'admin@elenimekuria.com'");
        $stmt->execute([$hashed]);
        $message = "Admin password has been reset successfully! Use password: " . htmlspecialchars($new_password);
    } else {
        $error = "Password must be at least 6 characters long";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Password Reset</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-black text-white p-8">
    <div class="max-w-md mx-auto mt-20 bg-zinc-900 border border-white/10 p-8 rounded-2xl">
        <h1 class="text-2xl font-bold text-emerald-500 mb-6">Admin Password Reset</h1>
        
        <?php if (isset($message)): ?>
            <div class="bg-emerald-500/10 border border-emerald-500/50 p-4 rounded-xl mb-6 text-emerald-500 text-sm"><?php echo $message; ?></div>
        <?php endif; ?>
        
        <?php if (isset($error)): ?>
            <div class="bg-red-500/10 border border-red-500/50 p-4 rounded-xl mb-6 text-red-500 text-sm"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <form method="POST" class="space-y-4">
            <div>
                <label class="block text-sm text-zinc-400 mb-2">New Admin Password</label>
                <input type="text" name="new_password" placeholder="Enter new password" class="w-full bg-black border border-white/10 p-3 rounded-lg text-white focus:border-emerald-500 outline-none" required>
            </div>
            <button type="submit" class="w-full bg-emerald-600 text-white p-3 rounded-lg font-bold uppercase hover:bg-emerald-500 transition-all">Reset Password</button>
        </form>
        
        <p class="text-xs text-zinc-500 text-center mt-4">⚠️ After resetting, delete this file: <code>admin-password-reset.php</code></p>
    </div>
</body>
</html>

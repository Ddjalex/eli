<?php
require_once '../includes/config.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    // Get current password from database
    $stmt = $pdo->prepare("SELECT password FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch();

    // Verify current password
    if (!password_verify($current_password, $user['password'])) {
        $error = "Current password is incorrect";
    } elseif ($new_password !== $confirm_password) {
        $error = "New passwords do not match";
    } elseif (strlen($new_password) < 6) {
        $error = "Password must be at least 6 characters long";
    } else {
        // Update password
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
        $stmt->execute([$hashed_password, $_SESSION['user_id']]);
        $message = "Password changed successfully!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Password - Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .glass { background: rgba(24, 24, 27, 0.7); backdrop-filter: blur(12px); border: 1px solid rgba(255, 255, 255, 0.1); }
    </style>
</head>
<body class="bg-[#09090b] text-white">
    <div class="flex min-h-screen">
        <!-- Sidebar -->
        <aside class="w-64 border-r border-white/5 bg-[#09090b] p-6 hidden lg:block">
            <div class="text-xl font-bold text-emerald-500 mb-10 tracking-tighter uppercase">Admin Panel</div>
            <nav class="space-y-2">
                <a href="index.php" class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-white/5 transition-all text-zinc-400 hover:text-white">Dashboard</a>
                <a href="manage_users.php" class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-white/5 transition-all text-zinc-400 hover:text-white">Manage Users</a>
                <a href="manage_hero.php" class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-white/5 transition-all text-zinc-400 hover:text-white">Hero Slider</a>
                <a href="manage_plans.php" class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-white/5 transition-all text-zinc-400 hover:text-white">Meal Plans</a>
                <a href="manage_payments.php" class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-white/5 transition-all text-zinc-400 hover:text-white">Payment Methods</a>
                <a href="manage_images.php" class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-white/5 transition-all text-zinc-400 hover:text-white">Site Settings</a>
                <a href="change_password.php" class="flex items-center gap-3 px-4 py-3 rounded-xl bg-emerald-500/10 text-emerald-500 border border-emerald-500/20 font-medium">Change Password</a>
                <a href="../logout.php" class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-red-500/10 transition-all text-zinc-500 hover:text-red-500">Logout</a>
            </nav>
        </aside>

        <main class="flex-1 p-8 lg:p-12">
            <div class="max-w-2xl mx-auto">
                <div class="flex justify-between items-center mb-12">
                    <div>
                        <h1 class="text-4xl font-bold tracking-tight text-white mb-2">Change Password</h1>
                        <p class="text-zinc-500">Update your admin account password</p>
                    </div>
                </div>

        <div class="glass p-8 rounded-3xl shadow-2xl max-w-md">
            <?php if ($message): ?>
                <div class="bg-emerald-500/10 border border-emerald-500/50 p-4 rounded-xl mb-6 text-emerald-500 font-bold text-sm uppercase tracking-widest text-center">
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>

            <?php if ($error): ?>
                <div class="bg-red-500/10 border border-red-500/50 p-4 rounded-xl mb-6 text-red-500 font-bold text-sm uppercase tracking-widest text-center">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <form method="POST" class="space-y-6">
                <div>
                    <label class="block text-[10px] font-bold text-zinc-500 uppercase tracking-widest mb-3">Current Password</label>
                    <input type="password" name="current_password" class="w-full bg-black border border-white/10 p-4 rounded-xl focus:border-emerald-500 transition-all text-sm outline-none" required>
                </div>

                <div>
                    <label class="block text-[10px] font-bold text-zinc-500 uppercase tracking-widest mb-3">New Password</label>
                    <input type="password" name="new_password" class="w-full bg-black border border-white/10 p-4 rounded-xl focus:border-emerald-500 transition-all text-sm outline-none" required>
                </div>

                <div>
                    <label class="block text-[10px] font-bold text-zinc-500 uppercase tracking-widest mb-3">Confirm Password</label>
                    <input type="password" name="confirm_password" class="w-full bg-black border border-white/10 p-4 rounded-xl focus:border-emerald-500 transition-all text-sm outline-none" required>
                </div>

                <button type="submit" class="w-full bg-emerald-600 text-white font-black uppercase tracking-[0.2em] py-4 rounded-xl hover:bg-emerald-500 transition-all shadow-lg shadow-emerald-900/20">Update Password</button>
            </form>
        </main>
    </div>
</body>
</html>

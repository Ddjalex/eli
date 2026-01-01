<?php
require_once '../includes/config.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $target_dir = "../uploads/site/";
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    foreach (['hero_bg_image', 'about_image', 'login_bg_image', 'register_bg_image', 'user_dashboard_bg'] as $key) {
        if (isset($_FILES[$key]) && $_FILES[$key]['error'] == 0) {
            $file_extension = pathinfo($_FILES[$key]["name"], PATHINFO_EXTENSION);
            $filename = $key . "_" . time() . "." . $file_extension;
            $target_file = $target_dir . $filename;
            
            if (move_uploaded_file($_FILES[$key]["tmp_name"], $target_file)) {
                $db_path = "uploads/site/" . $filename;
                $stmt = $pdo->prepare("INSERT INTO site_settings (key, value) VALUES (?, ?) ON DUPLICATE KEY UPDATE value = VALUES(value)");
                try {
                    $stmt->execute([$key, $db_path]);
                    $message = "Settings updated successfully!";
                } catch (PDOException $e) {
                     // Fallback for PostgreSQL if MySQL syntax fails
                     $stmt = $pdo->prepare("INSERT INTO site_settings (key, value) VALUES (?, ?) ON CONFLICT (key) DO UPDATE SET value = EXCLUDED.value");
                     $stmt->execute([$key, $db_path]);
                     $message = "Settings updated successfully!";
                }
            }
        }
    }
}

$settings = [];
try {
    $stmt = $pdo->query("SELECT * FROM site_settings");
    while ($row = $stmt->fetch()) {
        $settings[$row['key']] = $row['value'];
    }
} catch (PDOException $e) {}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Site Settings | Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .glass { background: rgba(24, 24, 27, 0.7); backdrop-filter: blur(12px); border: 1px solid rgba(255, 255, 255, 0.1); }
    </style>
</head>
<body class="bg-[#09090b] text-zinc-200 min-h-screen">
    <div class="flex">
        <!-- Sidebar -->
        <aside class="w-64 border-r border-white/5 h-screen sticky top-0 bg-[#09090b] p-6 hidden lg:block">
            <div class="text-xl font-bold text-emerald-500 mb-10 tracking-tighter uppercase">Admin Panel</div>
            <nav class="space-y-2">
                <a href="index.php" class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-white/5 transition-all text-zinc-400 hover:text-white">Dashboard</a>
                <a href="manage_hero.php" class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-white/5 transition-all text-zinc-400 hover:text-white">Hero Slider</a>
                <a href="manage_plans.php" class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-white/5 transition-all text-zinc-400 hover:text-white">Meal Plans</a>
                <a href="manage_payments.php" class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-white/5 transition-all text-zinc-400 hover:text-white">Payment Methods</a>
                <a href="manage_images.php" class="flex items-center gap-3 px-4 py-3 rounded-xl bg-emerald-500/10 text-emerald-500 border border-emerald-500/20 font-medium">Site Settings</a>
                <a href="change_password.php" class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-white/5 transition-all text-zinc-400 hover:text-white">Change Password</a>
            </nav>
        </aside>

        <main class="flex-1 p-8 lg:p-12">
            <header class="mb-12">
                <h1 class="text-4xl font-bold tracking-tight text-white mb-2">Site Settings</h1>
                <p class="text-zinc-500">Manage background images and branding.</p>
            </header>

            <?php if ($message): ?>
                <div class="bg-emerald-500/10 border border-emerald-500/50 p-4 rounded-xl mb-8 text-emerald-500"><?php echo $message; ?></div>
            <?php endif; ?>

            <form method="POST" enctype="multipart/form-data" class="space-y-8">
                <div class="grid md:grid-cols-2 gap-8">
                    <!-- Dashboard Background -->
                    <div class="glass p-8 rounded-3xl">
                        <label class="block text-sm font-bold uppercase tracking-widest text-zinc-500 mb-4">User Dashboard Background</label>
                        <?php if (isset($settings['user_dashboard_bg'])): ?>
                            <img src="../<?php echo $settings['user_dashboard_bg']; ?>" class="w-full h-48 object-cover rounded-xl mb-4 border border-white/5">
                        <?php endif; ?>
                        <input type="file" name="user_dashboard_bg" class="w-full bg-black/50 border border-white/10 p-3 rounded-xl text-sm">
                    </div>

                    <!-- Login Background -->
                    <div class="glass p-8 rounded-3xl">
                        <label class="block text-sm font-bold uppercase tracking-widest text-zinc-500 mb-4">Login Page Background</label>
                        <?php if (isset($settings['login_bg_image'])): ?>
                            <img src="../<?php echo $settings['login_bg_image']; ?>" class="w-full h-48 object-cover rounded-xl mb-4 border border-white/5">
                        <?php endif; ?>
                        <input type="file" name="login_bg_image" class="w-full bg-black/50 border border-white/10 p-3 rounded-xl text-sm">
                    </div>

                    <!-- Register Background -->
                    <div class="glass p-8 rounded-3xl">
                        <label class="block text-sm font-bold uppercase tracking-widest text-zinc-500 mb-4">Registration Page Background</label>
                        <?php if (isset($settings['register_bg_image'])): ?>
                            <img src="../<?php echo $settings['register_bg_image']; ?>" class="w-full h-48 object-cover rounded-xl mb-4 border border-white/5">
                        <?php endif; ?>
                        <input type="file" name="register_bg_image" class="w-full bg-black/50 border border-white/10 p-3 rounded-xl text-sm">
                    </div>

                    <!-- Other Images -->
                    <div class="glass p-8 rounded-3xl">
                        <label class="block text-sm font-bold uppercase tracking-widest text-zinc-500 mb-4">Hero Fallback Background</label>
                        <?php if (isset($settings['hero_bg_image'])): ?>
                            <img src="../<?php echo $settings['hero_bg_image']; ?>" class="w-full h-48 object-cover rounded-xl mb-4 border border-white/5">
                        <?php endif; ?>
                        <input type="file" name="hero_bg_image" class="w-full bg-black/50 border border-white/10 p-3 rounded-xl text-sm">
                    </div>

                    <div class="glass p-8 rounded-3xl">
                        <label class="block text-sm font-bold uppercase tracking-widest text-zinc-500 mb-4">About Section Image</label>
                        <?php if (isset($settings['about_image'])): ?>
                            <img src="../<?php echo $settings['about_image']; ?>" class="w-full h-48 object-cover rounded-xl mb-4 border border-white/5">
                        <?php endif; ?>
                        <input type="file" name="about_image" class="w-full bg-black/50 border border-white/10 p-3 rounded-xl text-sm">
                    </div>
                </div>

                <div class="flex justify-end pt-8">
                    <button type="submit" class="bg-emerald-600 text-white px-10 py-4 rounded-xl font-bold uppercase tracking-widest hover:bg-emerald-500 transition-all shadow-lg shadow-emerald-900/20">Save All Changes</button>
                </div>
            </form>
        </main>
    </div>
</body>
</html>
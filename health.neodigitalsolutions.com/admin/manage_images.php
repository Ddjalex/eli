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

    foreach (['hero_bg_image', 'about_image', 'login_bg_image', 'register_bg_image', 'user_dashboard_bg', 'certificate_image'] as $key) {
        if (isset($_FILES[$key]) && $_FILES[$key]['error'] == 0) {
            $file_extension = pathinfo($_FILES[$key]["name"], PATHINFO_EXTENSION);
            $filename = $key . "_" . time() . "_" . uniqid() . "." . $file_extension;
            $target_file = $target_dir . $filename;
            
            if (move_uploaded_file($_FILES[$key]["tmp_name"], $target_file)) {
                $db_path = "uploads/site/" . $filename;
                
                $update_settings_flag = false;
                if ($key === 'about_image') {
                    $stmt_slide = $pdo->prepare("INSERT INTO about_slides (image_url) VALUES (?)");
                    $stmt_slide->execute([$db_path]);
                    $update_settings_flag = true;
                } else {
                    $update_settings_flag = true;
                }

                if ($update_settings_flag) {
                    $driver = $pdo->getAttribute(PDO::ATTR_DRIVER_NAME);
                    if ($driver === 'pgsql') {
                        $stmt = $pdo->prepare("INSERT INTO site_settings (\"key\", value) VALUES (?, ?) ON CONFLICT (\"key\") DO UPDATE SET value = EXCLUDED.value");
                    } else {
                        $stmt = $pdo->prepare("INSERT INTO site_settings (`key`, value) VALUES (?, ?) ON DUPLICATE KEY UPDATE value = VALUES(value)");
                    }
                    $stmt->execute([$key, $db_path]);
                }
                $message = "Settings updated successfully!";
            } else {
                $message = "Failed to upload file. Check folder permissions.";
            }
        }
    }

    if (isset($_POST['delete_about_slide'])) {
        $stmt = $pdo->prepare("DELETE FROM about_slides WHERE id = ?");
        $stmt->execute([$_POST['slide_id']]);
        $message = "Slide deleted successfully!";
    }

    if (isset($_POST['settings']) && is_array($_POST['settings'])) {
        foreach ($_POST['settings'] as $key => $val) {
            $driver = $pdo->getAttribute(PDO::ATTR_DRIVER_NAME);
            if ($driver === 'pgsql') {
                $stmt = $pdo->prepare("INSERT INTO site_settings (\"key\", value) VALUES (?, ?) ON CONFLICT (\"key\") DO UPDATE SET value = EXCLUDED.value");
            } else {
                $stmt = $pdo->prepare("INSERT INTO site_settings (`key`, value) VALUES (?, ?) ON DUPLICATE KEY UPDATE value = VALUES(value)");
            }
            $stmt->execute([$key, $val]);
        }
        $message = "Settings updated successfully!";
    }
}

try {
    $stmt = $pdo->query("SELECT * FROM site_settings");
    $settings = [];
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
                <a href="manage_testimonials.php" class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-white/5 transition-all text-zinc-400 hover:text-white">Testimonials</a>
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

            <form action="manage_images.php" method="POST" enctype="multipart/form-data" class="space-y-8">
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
                        <label class="block text-sm font-bold uppercase tracking-widest text-zinc-500 mb-4">About Section Images (Slider)</label>
                        <div class="grid grid-cols-3 gap-4 mb-4">
                            <?php
                            $about_slides = $pdo->query("SELECT * FROM about_slides ORDER BY id DESC")->fetchAll();
                            foreach ($about_slides as $slide): ?>
                                <div class="relative group">
                                    <img src="../<?php echo $slide['image_url']; ?>" class="w-full h-24 object-cover rounded-lg border border-white/5">
                                    <form method="POST" class="absolute inset-0 flex items-center justify-center bg-black/60 opacity-0 group-hover:opacity-100 transition-opacity rounded-lg">
                                        <input type="hidden" name="slide_id" value="<?php echo $slide['id']; ?>">
                                        <button type="submit" name="delete_about_slide" value="1" class="text-red-500 hover:text-red-400">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </form>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <input type="file" name="about_image" class="w-full bg-black/50 border border-white/10 p-3 rounded-xl text-sm">
                        <p class="text-[10px] text-zinc-500 mt-2 uppercase tracking-widest">Upload to add new slide</p>
                    </div>

                    <?php
                    $text_settings = [
                        'about_text' => 'About Section Text',
                        'footer_address' => 'Footer Address',
                        'contact_phone' => 'Contact Phone',
                        'footer_email' => 'Footer Email',
                        'footer_tiktok' => 'TikTok Link',
                        'contact_social_ig' => 'Instagram Link',
                        'contact_whatsapp_link' => 'WhatsApp Link',
                        'footer_telegram' => 'Telegram Link'
                    ];
                    foreach ($text_settings as $key => $label): ?>
                        <div class="glass p-8 rounded-3xl <?php echo $key === 'about_text' ? 'md:col-span-2' : ''; ?>">
                            <label class="block text-sm font-bold uppercase tracking-widest text-zinc-500 mb-4"><?php echo $label; ?></label>
                            <textarea name="settings[<?php echo $key; ?>]" rows="<?php echo $key === 'about_text' ? '6' : '2'; ?>" class="w-full bg-black/50 border border-white/10 p-4 rounded-xl text-sm text-white focus:border-emerald-500 outline-none"><?php echo htmlspecialchars($settings[$key] ?? ''); ?></textarea>
                        </div>
                    <?php endforeach; ?>

                    <div class="glass p-8 rounded-3xl">
                        <label class="block text-sm font-bold uppercase tracking-widest text-zinc-500 mb-4">Certificate Image</label>
                        <?php if (isset($settings['certificate_image'])): ?>
                            <img src="../<?php echo $settings['certificate_image']; ?>" class="w-full h-48 object-cover rounded-xl mb-4 border border-white/5">
                        <?php endif; ?>
                        <input type="file" name="certificate_image" class="w-full bg-black/50 border border-white/10 p-3 rounded-xl text-sm">
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
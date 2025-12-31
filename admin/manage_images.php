<?php
require_once '../includes/config.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_links'])) {
        foreach ($_POST as $key => $value) {
            if (strpos($key, 'link_') === 0) {
                $setting_key = substr($key, 5);
                $stmt = $pdo->prepare("UPDATE site_settings SET value = ? WHERE key = ?");
                $stmt->execute([$value, $setting_key]);
            }
        }
        $message = "Links and info updated successfully!";
    } elseif (isset($_POST['add_slide']) && isset($_FILES['slide_image'])) {
        $upload_dir = '../uploads/slides/';
        if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
        
        $filename = 'slide_' . time() . '_' . basename($_FILES['slide_image']['name']);
        $target = $upload_dir . $filename;
        
        if (move_uploaded_file($_FILES['slide_image']['tmp_name'], $target)) {
            $db_path = 'uploads/slides/' . $filename;
            $stmt = $pdo->prepare("INSERT INTO hero_slides (image_url, title_main, title_accent, subtitle, display_order) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([
                $db_path, 
                $_POST['title_main'], 
                $_POST['title_accent'], 
                $_POST['subtitle'],
                (int)$_POST['display_order']
            ]);
            $message = "Slide added successfully!";
        }
    } elseif (isset($_POST['delete_slide'])) {
        $stmt = $pdo->prepare("DELETE FROM hero_slides WHERE id = ?");
        $stmt->execute([$_POST['slide_id']]);
        $message = "Slide deleted successfully!";
    } elseif (isset($_FILES['image'])) {
        $key = $_POST['setting_key'];
        $upload_dir = '../uploads/site/';
        if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
        
        $filename = $key . '_' . time() . '_' . str_replace(' ', '_', basename($_FILES['image']['name']));
        $target = $upload_dir . $filename;
        
        if (move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
            // Check if it's a HEIC file and convert it
            $ext = strtolower(pathinfo($target, PATHINFO_EXTENSION));
            if ($ext === 'heic') {
                $new_target = str_replace('.heic', '.jpg', strtolower($target));
                // Enhanced conversion using ImageMagick's convert with explicit format
                shell_exec("convert -quality 90 \"$target[0]\" \"$new_target\"");
                if (file_exists($new_target)) {
                    unlink($target);
                    $filename = str_replace('.heic', '.jpg', strtolower($filename));
                }
            }
            
            $db_path = 'uploads/site/' . $filename;
            $stmt = $pdo->prepare("UPDATE site_settings SET value = ? WHERE key = ?");
            $stmt->execute([$db_path, $key]);
            $message = "Image updated successfully!";
        }
    }
}

$settings = $pdo->query("SELECT * FROM site_settings WHERE key NOT LIKE 'contact_%' AND key NOT LIKE 'footer_%'")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Content - Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-black text-white p-8">
    <div class="max-w-4xl mx-auto">
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-3xl font-bold">Manage Content</h1>
            <a href="index.php" class="text-zinc-500 hover:text-white">Back to Dashboard</a>
        </div>
        
        <?php if ($message): ?>
            <div class="bg-emerald-500/10 border border-emerald-500/50 p-4 rounded-xl mb-8 text-emerald-500"><?php echo $message; ?></div>
        <?php endif; ?>

        <div class="bg-zinc-900 p-8 rounded-2xl border border-white/10 mb-8">
            <h2 class="text-2xl font-bold mb-6">Business Info & Links</h2>
            <form method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <input type="hidden" name="update_links" value="1">
                <?php 
                $links = $pdo->query("SELECT * FROM site_settings WHERE key LIKE 'contact_%' OR key LIKE 'footer_%' ORDER BY key ASC")->fetchAll();
                foreach ($links as $link): ?>
                    <div class="flex flex-col gap-1">
                        <label class="text-xs uppercase text-zinc-500 font-bold"><?php echo str_replace(['contact_', 'footer_'], '', $link['key']); ?></label>
                        <input type="text" name="link_<?php echo $link['key']; ?>" value="<?php echo htmlspecialchars($link['value']); ?>" class="bg-black border border-white/10 p-3 rounded-lg text-sm focus:border-emerald-500 outline-none">
                    </div>
                <?php endforeach; ?>
                <div class="md:col-span-2">
                    <button type="submit" class="bg-emerald-600 px-8 py-3 rounded-xl text-xs font-bold uppercase tracking-widest hover:bg-emerald-500 transition-all">Save Business Info</button>
                </div>
            </form>
        </div>

        <div class="grid gap-8">
            <?php foreach ($settings as $s): ?>
                <div class="bg-zinc-900 p-6 rounded-2xl border border-white/10 flex flex-col md:flex-row gap-6 items-center">
                    <div class="w-48 h-32 rounded-lg overflow-hidden border border-white/5 bg-black">
                        <img src="../<?php echo $s['value']; ?>" class="w-full h-full object-cover">
                    </div>
                    <div class="flex-1">
                        <h3 class="text-xl font-bold uppercase tracking-tight mb-1"><?php echo str_replace('_', ' ', $s['key']); ?></h3>
                        <p class="text-zinc-500 text-sm mb-4"><?php echo $s['description']; ?></p>
                        <form method="POST" enctype="multipart/form-data" class="flex gap-4">
                            <input type="hidden" name="setting_key" value="<?php echo $s['key']; ?>">
                            <input type="file" name="image" class="text-xs text-zinc-400 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-zinc-800 file:text-zinc-300 hover:file:bg-zinc-700" required>
                            <button type="submit" class="bg-emerald-600 px-4 py-2 rounded-lg text-xs font-bold uppercase tracking-widest hover:bg-emerald-500">Update</button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</body>
</html>
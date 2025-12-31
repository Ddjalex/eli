<?php
require_once '../includes/config.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

$message = '';

function convertHEIC($target, $upload_dir, $filename) {
    $ext = strtolower(pathinfo($target, PATHINFO_EXTENSION));
    if ($ext === 'heic') {
        $new_filename = str_replace('.heic', '.jpg', strtolower($filename));
        $new_target = $upload_dir . $new_filename;
        
        // Try to find the correct convert command path or use full path if needed
        // We'll use a more comprehensive mogrify command which is often better for HEIC
        shell_exec("mogrify -format jpg -quality 90 \"$target\"");
        
        if (file_exists($new_target)) {
            unlink($target);
            return $new_filename;
        }
        
        // Fallback to convert with specific heic: prefix
        shell_exec("convert \"heic:$target\" -quality 90 -flatten \"$new_target\"");
        if (file_exists($new_target)) {
            unlink($target);
            return $new_filename;
        }
    }
    return $filename;
}

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
    } elseif (isset($_FILES['image'])) {
        $key = $_POST['setting_key'];
        $upload_dir = '../uploads/site/';
        if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
        
        $filename = $key . '_' . time() . '_' . str_replace(' ', '_', basename($_FILES['image']['name']));
        $target = $upload_dir . $filename;
        
        if (move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
            $filename = convertHEIC($target, $upload_dir, $filename);
            $db_path = 'uploads/site/' . $filename;
            $stmt = $pdo->prepare("UPDATE site_settings SET value = ? WHERE key = ?");
            $stmt->execute([$db_path, $key]);
            $message = "Image updated successfully!";
        }
    }
}

$settings = $pdo->query("SELECT * FROM site_settings WHERE key NOT LIKE 'contact_%' AND key NOT LIKE 'footer_%' ORDER BY key ASC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Content - Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .glass { background: rgba(24, 24, 27, 0.7); backdrop-filter: blur(12px); border: 1px solid rgba(255, 255, 255, 0.1); }
    </style>
</head>
<body class="bg-[#09090b] text-white p-8">
    <div class="max-w-4xl mx-auto">
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-3xl font-bold text-emerald-500 uppercase tracking-tighter">Site Settings</h1>
                <p class="text-zinc-500 text-sm">Manage business information and static site images.</p>
            </div>
            <a href="index.php" class="glass px-6 py-3 rounded-xl text-xs font-bold uppercase tracking-widest hover:bg-white/5 transition-all">Back to Dashboard</a>
        </div>
        
        <?php if ($message): ?>
            <div class="bg-emerald-500/10 border border-emerald-500/50 p-4 rounded-xl mb-8 text-emerald-500 font-bold text-sm uppercase tracking-widest text-center"><?php echo $message; ?></div>
        <?php endif; ?>

        <div class="glass p-8 rounded-3xl mb-8 shadow-2xl">
            <h2 class="text-xl font-bold mb-6 uppercase tracking-tight">Business Info & Links</h2>
            <form method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <input type="hidden" name="update_links" value="1">
                <?php 
                $links = $pdo->query("SELECT * FROM site_settings WHERE key LIKE 'contact_%' OR key LIKE 'footer_%' ORDER BY key ASC")->fetchAll();
                foreach ($links as $link): ?>
                    <div class="flex flex-col gap-1">
                        <label class="text-[10px] uppercase text-zinc-500 font-bold tracking-widest"><?php echo str_replace(['contact_', 'footer_'], '', $link['key']); ?></label>
                        <input type="text" name="link_<?php echo $link['key']; ?>" value="<?php echo htmlspecialchars($link['value']); ?>" class="bg-black border border-white/10 p-4 rounded-xl text-sm focus:border-emerald-500 outline-none transition-all">
                    </div>
                <?php endforeach; ?>
                <div class="md:col-span-2">
                    <button type="submit" class="w-full bg-emerald-600 py-4 rounded-xl text-xs font-bold uppercase tracking-[0.2em] hover:bg-emerald-500 transition-all shadow-lg shadow-emerald-900/20">Save Business Info</button>
                </div>
            </form>
        </div>

        <div class="grid gap-8">
            <?php foreach ($settings as $s): ?>
                <div class="glass p-8 rounded-3xl flex flex-col md:flex-row gap-8 items-center group">
                    <div class="w-48 h-32 rounded-2xl overflow-hidden border border-white/5 bg-black relative">
                        <?php if (str_ends_with(strtolower($s['value']), '.heic')): ?>
                             <div class="absolute inset-0 flex items-center justify-center bg-zinc-900 text-[10px] text-zinc-500 font-bold uppercase text-center p-2 italic">Format: HEIC (Please Re-upload)</div>
                        <?php else: ?>
                            <img src="../<?php echo $s['value']; ?>" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                        <?php endif; ?>
                    </div>
                    <div class="flex-1 w-full">
                        <h3 class="text-xl font-bold uppercase tracking-tight mb-1 text-white"><?php echo str_replace('_', ' ', $s['key']); ?></h3>
                        <p class="text-zinc-500 text-xs mb-4 uppercase tracking-widest"><?php echo $s['description']; ?></p>
                        <form method="POST" enctype="multipart/form-data" class="flex flex-col sm:flex-row gap-4">
                            <input type="hidden" name="setting_key" value="<?php echo $s['key']; ?>">
                            <input type="file" name="image" class="text-[10px] text-zinc-500 file:mr-4 file:py-2 file:px-6 file:rounded-full file:border-0 file:text-[10px] file:font-bold file:uppercase file:tracking-widest file:bg-zinc-800 file:text-zinc-300 hover:file:bg-zinc-700 transition-all flex-1" required>
                            <button type="submit" class="bg-emerald-600 px-8 py-3 rounded-xl text-[10px] font-bold uppercase tracking-widest hover:bg-emerald-500 shadow-lg shadow-emerald-900/20 transition-all">Update Image</button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</body>
</html>
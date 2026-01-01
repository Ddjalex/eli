<?php
ob_start();
error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE);
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
        
        shell_exec("convert \"heic:$target\" -quality 90 -flatten \"$new_target\"");
        if (file_exists($new_target)) {
            unlink($target);
            return $new_filename;
        }
        
        shell_exec("mogrify -format jpg -quality 90 \"$target\"");
        if (file_exists($new_target)) {
            unlink($target);
            return $new_filename;
        }
    }
    return $filename;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_plan'])) {
        $stmt = $pdo->prepare("INSERT INTO meal_plans (title, package_type, price) VALUES (?, ?, ?)");
        $stmt->execute([$_POST['title'], $_POST['package_type'], $_POST['price'] ?? 0]);
        $message = "New package added successfully!";
        
        $plan_id = $pdo->lastInsertId();
        try {
            $stmt = $pdo->prepare("UPDATE meal_plans SET video_url = '' WHERE id = ?");
            $stmt->execute([$plan_id]);
        } catch (PDOException $e) {}
    } elseif (isset($_POST['delete_plan'])) {
        $stmt = $pdo->prepare("DELETE FROM meal_plans WHERE id = ?");
        $stmt->execute([$_POST['plan_id']]);
        $message = "Package deleted successfully!";
    } elseif (isset($_POST['update_plan'])) {
        $plan_id = $_POST['plan_id'];
        
        // Update basic info including price and video URL
        $stmt = $pdo->prepare("UPDATE meal_plans SET title = ?, package_type = ?, price = ? WHERE id = ?");
        $stmt->execute([$_POST['title'], $_POST['package_type'], $_POST['price'] ?? 0, $plan_id]);
        
        // Handle video_url separately or check if column exists
        try {
            $stmt = $pdo->prepare("UPDATE meal_plans SET video_url = ? WHERE id = ?");
            $stmt->execute([$_POST['video_url'] ?? '', $plan_id]);
        } catch (PDOException $e) {
            // Ignore if column doesn't exist yet, or log it
        }
        
        // Handle PDF
        if (isset($_FILES['pdf_file']) && $_FILES['pdf_file']['size'] > 0) {
            $protected_dir = '../protected_files/';
            if (!is_dir($protected_dir)) mkdir($protected_dir, 0777, true);
            $filename = 'plan_' . $plan_id . '_' . time() . '.pdf';
            $target = $protected_dir . $filename;
            
            // Optimization: Use stream for moving file if possible, or just standard move
            if (move_uploaded_file($_FILES['pdf_file']['tmp_name'], $target)) {
                $db_path = 'protected_files/' . $filename;
                $stmt = $pdo->prepare("UPDATE meal_plans SET file_url = ? WHERE id = ?");
                $stmt->execute([$db_path, $plan_id]);
            }
        }
        
        // Handle Preview
        if (isset($_FILES['preview_image']) && $_FILES['preview_image']['size'] > 0) {
            $preview_dir = '../uploads/previews/';
            if (!is_dir($preview_dir)) mkdir($preview_dir, 0777, true);
            $filename = 'preview_' . $plan_id . '_' . time() . '_' . str_replace(' ', '_', basename($_FILES['preview_image']['name']));
            $target = $preview_dir . $filename;
            if (move_uploaded_file($_FILES['preview_image']['tmp_name'], $target)) {
                $filename = convertHEIC($target, $preview_dir, $filename);
                $db_path = 'uploads/previews/' . $filename;
                $stmt = $pdo->prepare("UPDATE meal_plans SET preview_image = ? WHERE id = ?");
                $stmt->execute([$db_path, $plan_id]);
            }
        }
        $message = "Package updated successfully!";
    }
}

$plans = $pdo->query("SELECT * FROM meal_plans ORDER BY id ASC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Packages - Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .glass { background: rgba(24, 24, 27, 0.7); backdrop-filter: blur(12px); border: 1px solid rgba(255, 255, 255, 0.1); }
    </style>
</head>
<body class="bg-[#09090b] text-white p-8">
    <div class="max-w-6xl mx-auto">
        <div class="flex justify-between items-center mb-12">
            <div>
                <h1 class="text-4xl font-black uppercase tracking-tighter text-emerald-500">Meal Packages</h1>
                <p class="text-zinc-500 text-sm mt-1">Manage your subscription packages and secure meal plan files.</p>
            </div>
            <a href="index.php" class="glass px-6 py-3 rounded-xl text-xs font-bold uppercase tracking-widest hover:bg-white/5 transition-all">Back to Dashboard</a>
        </div>
        
        <?php if ($message): ?>
            <div class="bg-emerald-500/10 border border-emerald-500/50 p-4 rounded-xl mb-8 text-emerald-500 font-bold text-sm uppercase tracking-widest text-center"><?php echo $message; ?></div>
        <?php endif; ?>

        <!-- Add Package -->
        <div class="glass p-8 rounded-3xl mb-12 shadow-2xl">
            <h2 class="text-xl font-bold mb-8 uppercase tracking-tight text-white flex items-center gap-3">
                <span class="w-8 h-8 rounded-full bg-emerald-600 flex items-center justify-center text-xs">+</span>
                Create New Package
            </h2>
            <form method="POST" class="grid md:grid-cols-4 gap-6">
                <input type="hidden" name="add_plan" value="1">
                <div class="md:col-span-1">
                    <label class="block text-[10px] font-bold text-zinc-500 uppercase tracking-widest mb-2">Package Name</label>
                    <input type="text" name="title" required placeholder="e.g. Keto Shred" class="w-full bg-black border border-white/10 p-4 rounded-xl focus:border-emerald-500 outline-none">
                </div>
                <div class="md:col-span-1">
                    <label class="block text-[10px] font-bold text-zinc-500 uppercase tracking-widest mb-2">Package Type (Slug)</label>
                    <input type="text" name="package_type" required placeholder="e.g. keto_plan" class="w-full bg-black border border-white/10 p-4 rounded-xl focus:border-emerald-500 outline-none">
                </div>
                <div class="md:col-span-1">
                    <label class="block text-[10px] font-bold text-zinc-500 uppercase tracking-widest mb-2">Payment Amount</label>
                    <input type="number" step="0.01" name="price" required placeholder="e.g. 50.00" class="w-full bg-black border border-white/10 p-4 rounded-xl focus:border-emerald-500 outline-none">
                </div>
                <div class="md:col-span-1 flex items-end">
                    <button type="submit" class="w-full bg-emerald-600 py-4 rounded-xl font-black uppercase tracking-widest hover:bg-emerald-500 transition-all">Create Package</button>
                </div>
            </form>
        </div>

        <div class="grid gap-8">
            <?php foreach ($plans as $p): ?>
                <div class="glass p-8 rounded-[2rem] border border-white/10">
                    <form method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="update_plan" value="1">
                        <input type="hidden" name="plan_id" value="<?php echo $p['id']; ?>">
                        
                        <div class="flex flex-col md:flex-row gap-8">
                            <div class="w-full md:w-1/3">
                                <div class="aspect-square rounded-3xl overflow-hidden bg-black border border-white/5 mb-4 relative">
                                    <?php if ($p['preview_image'] && str_ends_with(strtolower((string)$p['preview_image']), '.heic')): ?>
                                         <div class="absolute inset-0 flex items-center justify-center bg-zinc-900 text-[10px] text-zinc-500 font-bold uppercase text-center p-2 italic">Format: HEIC (Please Re-upload)</div>
                                    <?php elseif ($p['preview_image']): ?>
                                        <img src="../<?php echo $p['preview_image']; ?>" class="w-full h-full object-cover">
                                    <?php else: ?>
                                        <div class="w-full h-full flex items-center justify-center text-zinc-700 font-bold uppercase tracking-widest text-[10px]">No Preview</div>
                                    <?php endif; ?>
                                </div>
                                <label class="block text-[8px] font-bold text-zinc-500 uppercase tracking-widest mb-1">Update Preview Image</label>
                                <input type="file" name="preview_image" accept="image/*" class="w-full bg-black/50 border border-white/5 p-2 rounded-xl text-[10px] text-zinc-500">
                            </div>

                            <div class="flex-1 space-y-6">
                                <div class="grid grid-cols-2 gap-6">
                                    <div>
                                        <label class="block text-[10px] font-bold text-zinc-500 uppercase tracking-widest mb-2">Display Title</label>
                                        <input type="text" name="title" value="<?php echo htmlspecialchars($p['title']); ?>" class="w-full bg-black/50 border border-white/5 p-4 rounded-xl text-sm focus:border-emerald-500 outline-none">
                                    </div>
                                    <div>
                                        <label class="block text-[10px] font-bold text-zinc-500 uppercase tracking-widest mb-2">Package ID</label>
                                        <input type="text" name="package_type" value="<?php echo htmlspecialchars($p['package_type']); ?>" class="w-full bg-black/50 border border-white/5 p-4 rounded-xl text-sm focus:border-emerald-500 outline-none">
                                    </div>
                                    <div>
                                        <label class="block text-[10px] font-bold text-zinc-500 uppercase tracking-widest mb-2">Payment Amount</label>
                                        <input type="number" step="0.01" name="price" value="<?php echo $p['price'] ?? 0; ?>" class="w-full bg-black/50 border border-white/5 p-4 rounded-xl text-sm focus:border-emerald-500 outline-none">
                                    </div>
                                    <div>
                                        <label class="block text-[10px] font-bold text-zinc-500 uppercase tracking-widest mb-2">Instructional Video URL (YouTube/Vimeo Embed)</label>
                                        <input type="text" name="video_url" value="<?php echo htmlspecialchars($p['video_url'] ?? ''); ?>" placeholder="https://www.youtube.com/embed/..." class="w-full bg-black/50 border border-white/5 p-4 rounded-xl text-sm focus:border-emerald-500 outline-none">
                                    </div>
                                </div>

                                <div class="bg-black/40 p-6 rounded-2xl border border-white/5">
                                    <div class="flex justify-between items-center mb-4">
                                        <h4 class="text-xs font-bold uppercase tracking-widest text-zinc-400">Secure PDF Document</h4>
                                        <?php if ($p['file_url']): ?>
                                            <span class="text-[8px] bg-emerald-500/10 text-emerald-500 px-3 py-1 rounded-full border border-emerald-500/20 font-bold uppercase tracking-widest">Document Secured</span>
                                        <?php else: ?>
                                            <span class="text-[8px] bg-red-500/10 text-red-500 px-3 py-1 rounded-full border border-red-500/20 font-bold uppercase tracking-widest">Missing File</span>
                                        <?php endif; ?>
                                    </div>
                                    <input type="file" name="pdf_file" accept=".pdf" class="w-full text-[10px] text-zinc-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-zinc-800 file:text-zinc-300">
                                </div>

                                <div class="flex gap-4">
                                    <button type="submit" class="flex-1 bg-white text-black py-4 rounded-xl font-black uppercase tracking-widest hover:bg-emerald-500 hover:text-white transition-all">Save Changes</button>
                                    <button type="submit" name="delete_plan" value="1" onclick="return confirm('Permanently delete this package?')" class="px-8 bg-red-500/10 border border-red-500/20 text-red-500 rounded-xl hover:bg-red-500 hover:text-white transition-all flex items-center justify-center">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</body>
</html>
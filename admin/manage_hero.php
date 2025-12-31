<?php
require_once '../includes/config.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_slide']) && isset($_FILES['slide_image'])) {
        $upload_dir = '../uploads/slides/';
        if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
        
        $filename = 'slide_' . time() . '_' . str_replace(' ', '_', basename($_FILES['slide_image']['name']));
        $target = $upload_dir . $filename;
        
        if (move_uploaded_file($_FILES['slide_image']['tmp_name'], $target)) {
            // HEIC Support
            $ext = strtolower(pathinfo($target, PATHINFO_EXTENSION));
            if ($ext === 'heic') {
                $new_filename = str_replace('.heic', '.jpg', strtolower($filename));
                $new_target = $upload_dir . $new_filename;
                shell_exec("convert \"$target\" -quality 90 -flatten \"$new_target\"");
                if (file_exists($new_target)) {
                    unlink($target);
                    $filename = $new_filename;
                }
            }
            
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
    } elseif (isset($_POST['update_slide'])) {
        $slide_id = $_POST['slide_id'];
        if (isset($_FILES['slide_image']) && $_FILES['slide_image']['size'] > 0) {
            $upload_dir = '../uploads/slides/';
            $filename = 'slide_' . time() . '_' . str_replace(' ', '_', basename($_FILES['slide_image']['name']));
            $target = $upload_dir . $filename;
            if (move_uploaded_file($_FILES['slide_image']['tmp_name'], $target)) {
                $ext = strtolower(pathinfo($target, PATHINFO_EXTENSION));
                if ($ext === 'heic') {
                    $new_filename = str_replace('.heic', '.jpg', strtolower($filename));
                    $new_target = $upload_dir . $new_filename;
                    shell_exec("convert \"$target\" -quality 90 -flatten \"$new_target\"");
                    if (file_exists($new_target)) {
                        unlink($target);
                        $filename = $new_filename;
                    }
                }
                $db_path = 'uploads/slides/' . $filename;
                $stmt = $pdo->prepare("UPDATE hero_slides SET image_url = ? WHERE id = ?");
                $stmt->execute([$db_path, $slide_id]);
            }
        }
        $stmt = $pdo->prepare("UPDATE hero_slides SET title_main = ?, title_accent = ?, subtitle = ?, display_order = ? WHERE id = ?");
        $stmt->execute([$_POST['title_main'], $_POST['title_accent'], $_POST['subtitle'], (int)$_POST['display_order'], $slide_id]);
        $message = "Slide updated successfully!";
    } elseif (isset($_POST['delete_slide'])) {
        $stmt = $pdo->prepare("DELETE FROM hero_slides WHERE id = ?");
        $stmt->execute([$_POST['slide_id']]);
        $message = "Slide deleted successfully!";
    }
}

$slides = $pdo->query("SELECT * FROM hero_slides ORDER BY display_order ASC, id DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Hero Slider - Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .glass { background: rgba(24, 24, 27, 0.7); backdrop-filter: blur(12px); border: 1px solid rgba(255, 255, 255, 0.1); }
    </style>
</head>
<body class="bg-[#09090b] text-white p-8">
    <div class="max-w-6xl mx-auto">
        <div class="flex justify-between items-center mb-12">
            <div>
                <h1 class="text-4xl font-black uppercase tracking-tighter text-emerald-500">Hero Slider</h1>
                <p class="text-zinc-500 text-sm mt-1">Manage your homepage background slides and messages.</p>
            </div>
            <a href="index.php" class="glass px-6 py-3 rounded-xl text-xs font-bold uppercase tracking-widest hover:bg-white/5 transition-all">Back to Dashboard</a>
        </div>

        <?php if ($message): ?>
            <div class="bg-emerald-500/10 border border-emerald-500/50 p-4 rounded-xl mb-8 text-emerald-500 font-bold text-sm uppercase tracking-widest text-center"><?php echo $message; ?></div>
        <?php endif; ?>

        <!-- Add Section -->
        <div class="glass p-8 rounded-3xl mb-12 shadow-2xl">
            <h2 class="text-xl font-bold mb-8 uppercase tracking-tight text-white flex items-center gap-3">
                <span class="w-8 h-8 rounded-full bg-emerald-600 flex items-center justify-center text-xs">+</span>
                Add New Slide
            </h2>
            <form method="POST" enctype="multipart/form-data" class="grid md:grid-cols-3 gap-6">
                <input type="hidden" name="add_slide" value="1">
                <div class="md:col-span-1">
                    <label class="block text-[10px] font-bold text-zinc-500 uppercase tracking-widest mb-2">Main Title</label>
                    <input type="text" name="title_main" required placeholder="FUEL YOUR" class="w-full bg-black border border-white/10 p-4 rounded-xl focus:border-emerald-500 outline-none transition-all">
                </div>
                <div class="md:col-span-1">
                    <label class="block text-[10px] font-bold text-zinc-500 uppercase tracking-widest mb-2">Accent Title</label>
                    <input type="text" name="title_accent" required placeholder="POTENTIAL" class="w-full bg-black border border-white/10 p-4 rounded-xl focus:border-emerald-500 outline-none transition-all">
                </div>
                <div class="md:col-span-1">
                    <label class="block text-[10px] font-bold text-zinc-500 uppercase tracking-widest mb-2">Subtitle</label>
                    <input type="text" name="subtitle" required placeholder="SCIENCE & EMPATHY" class="w-full bg-black border border-white/10 p-4 rounded-xl focus:border-emerald-500 outline-none transition-all">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-[10px] font-bold text-zinc-500 uppercase tracking-widest mb-2">Background Image</label>
                    <input type="file" name="slide_image" required class="w-full bg-black border border-white/10 p-3.5 rounded-xl text-xs text-zinc-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-zinc-800 file:text-zinc-300">
                </div>
                <div class="md:col-span-1">
                    <label class="block text-[10px] font-bold text-zinc-500 uppercase tracking-widest mb-2">Display Order</label>
                    <input type="number" name="display_order" value="0" class="w-full bg-black border border-white/10 p-4 rounded-xl focus:border-emerald-500 outline-none transition-all">
                </div>
                <div class="md:col-span-3">
                    <button type="submit" class="w-full bg-emerald-600 py-5 rounded-2xl font-black uppercase tracking-[0.2em] hover:bg-emerald-500 transition-all shadow-xl shadow-emerald-900/20">Add Slide to Homepage</button>
                </div>
            </form>
        </div>

        <!-- List Section -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <?php foreach ($slides as $slide): ?>
                <div class="glass rounded-[2rem] overflow-hidden group">
                    <div class="aspect-video relative overflow-hidden">
                        <img src="../<?php echo $slide['image_url']; ?>" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700">
                        <div class="absolute inset-0 bg-gradient-to-t from-black via-black/20 to-transparent p-8 flex flex-col justify-end">
                            <h3 class="text-2xl font-black uppercase leading-none mb-2"><?php echo $slide['title_main']; ?> <span class="text-emerald-500"><?php echo $slide['title_accent']; ?></span></h3>
                            <p class="text-xs text-zinc-400 uppercase tracking-[0.3em]"><?php echo $slide['subtitle']; ?></p>
                        </div>
                    </div>
                    <div class="p-8">
                        <form method="POST" enctype="multipart/form-data" class="space-y-4">
                            <input type="hidden" name="update_slide" value="1">
                            <input type="hidden" name="slide_id" value="<?php echo $slide['id']; ?>">
                            
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-[8px] font-bold text-zinc-500 uppercase tracking-widest mb-1">Main Title</label>
                                    <input type="text" name="title_main" value="<?php echo htmlspecialchars($slide['title_main']); ?>" class="w-full bg-black/50 border border-white/5 p-3 rounded-xl text-sm focus:border-emerald-500 outline-none">
                                </div>
                                <div>
                                    <label class="block text-[8px] font-bold text-zinc-500 uppercase tracking-widest mb-1">Accent Title</label>
                                    <input type="text" name="title_accent" value="<?php echo htmlspecialchars($slide['title_accent']); ?>" class="w-full bg-black/50 border border-white/5 p-3 rounded-xl text-sm focus:border-emerald-500 outline-none">
                                </div>
                            </div>
                            
                            <div>
                                <label class="block text-[8px] font-bold text-zinc-500 uppercase tracking-widest mb-1">Subtitle</label>
                                <input type="text" name="subtitle" value="<?php echo htmlspecialchars($slide['subtitle']); ?>" class="w-full bg-black/50 border border-white/5 p-3 rounded-xl text-sm focus:border-emerald-500 outline-none">
                            </div>

                            <div class="grid grid-cols-2 gap-4 items-end">
                                <div>
                                    <label class="block text-[8px] font-bold text-zinc-500 uppercase tracking-widest mb-1">Change Image</label>
                                    <input type="file" name="slide_image" class="w-full bg-black/50 border border-white/5 p-2 rounded-xl text-[10px] text-zinc-500">
                                </div>
                                <div>
                                    <label class="block text-[8px] font-bold text-zinc-500 uppercase tracking-widest mb-1">Order</label>
                                    <input type="number" name="display_order" value="<?php echo $slide['display_order']; ?>" class="w-full bg-black/50 border border-white/5 p-2 rounded-xl text-sm focus:border-emerald-500 outline-none">
                                </div>
                            </div>

                            <div class="flex gap-4 pt-4">
                                <button type="submit" class="flex-1 bg-white/5 border border-white/10 py-3 rounded-xl text-[10px] font-bold uppercase tracking-widest hover:bg-emerald-600 hover:text-white transition-all">Update Slide</button>
                        </form>
                        <form method="POST" onsubmit="return confirm('Delete this slide permanently?')" class="flex">
                            <input type="hidden" name="delete_slide" value="1">
                            <input type="hidden" name="slide_id" value="<?php echo $slide['id']; ?>">
                            <button type="submit" class="px-6 bg-red-500/10 border border-red-500/20 text-red-500 rounded-xl hover:bg-red-500 hover:text-white transition-all">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                        </form>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</body>
</html>
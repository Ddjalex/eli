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
</head>
<body class="bg-black text-white p-8">
    <div class="max-w-6xl mx-auto">
        <div class="flex justify-between items-center mb-12">
            <h1 class="text-3xl font-bold">Hero Slider Management</h1>
            <a href="index.php" class="text-zinc-500 hover:text-white uppercase text-xs font-bold tracking-widest">Back to Dashboard</a>
        </div>

        <?php if ($message): ?>
            <div class="bg-emerald-500/10 border border-emerald-500/50 p-4 rounded-xl mb-8 text-emerald-500"><?php echo $message; ?></div>
        <?php endif; ?>

        <div class="bg-zinc-900 p-8 rounded-3xl border border-white/10 mb-12">
            <h2 class="text-xl font-bold mb-6 uppercase tracking-tight">Add New Slide</h2>
            <form method="POST" enctype="multipart/form-data" class="grid md:grid-cols-2 gap-6">
                <input type="hidden" name="add_slide" value="1">
                <div>
                    <label class="block text-xs font-bold text-zinc-500 uppercase mb-2">Main Title (e.g. FUEL YOUR)</label>
                    <input type="text" name="title_main" required class="w-full bg-black border border-white/10 p-3 rounded-xl focus:border-emerald-500 outline-none">
                </div>
                <div>
                    <label class="block text-xs font-bold text-zinc-500 uppercase mb-2">Accent Title (e.g. POTENTIAL)</label>
                    <input type="text" name="title_accent" required class="w-full bg-black border border-white/10 p-3 rounded-xl focus:border-emerald-500 outline-none">
                </div>
                <div>
                    <label class="block text-xs font-bold text-zinc-500 uppercase mb-2">Subtitle (e.g. SCIENCE & EMPATHY)</label>
                    <input type="text" name="subtitle" required class="w-full bg-black border border-white/10 p-3 rounded-xl focus:border-emerald-500 outline-none">
                </div>
                <div>
                    <label class="block text-xs font-bold text-zinc-500 uppercase mb-2">Display Order</label>
                    <input type="number" name="display_order" value="0" class="w-full bg-black border border-white/10 p-3 rounded-xl focus:border-emerald-500 outline-none">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-zinc-500 uppercase mb-2">Background Image</label>
                    <input type="file" name="slide_image" required class="w-full bg-black border border-white/10 p-3 rounded-xl text-xs">
                </div>
                <button type="submit" class="bg-emerald-600 px-8 py-4 rounded-xl font-bold uppercase tracking-widest hover:bg-emerald-500 transition-all">Add Slide</button>
            </form>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <?php foreach ($slides as $slide): ?>
                <div class="bg-zinc-900 rounded-3xl overflow-hidden border border-white/10 group relative">
                    <div class="aspect-video relative">
                        <img src="../<?php echo $slide['image_url']; ?>" class="w-full h-full object-cover">
                        <div class="absolute inset-0 bg-black/40 p-6 flex flex-col justify-end">
                            <h3 class="text-xl font-black uppercase leading-none"><?php echo $slide['title_main']; ?> <span class="text-emerald-500"><?php echo $slide['title_accent']; ?></span></h3>
                            <p class="text-[10px] text-zinc-300 uppercase tracking-widest mt-1"><?php echo $slide['subtitle']; ?></p>
                        </div>
                    </div>
                    <div class="p-4 flex justify-between items-center bg-zinc-800/50">
                        <span class="text-xs text-zinc-500">Order: <?php echo $slide['display_order']; ?></span>
                        <form method="POST" onsubmit="return confirm('Delete this slide?')">
                            <input type="hidden" name="delete_slide" value="1">
                            <input type="hidden" name="slide_id" value="<?php echo $slide['id']; ?>">
                            <button type="submit" class="text-red-500 hover:text-red-400 text-xs font-bold uppercase tracking-widest">Delete</button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</body>
</html>
<?php
require_once '../includes/config.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_photo'])) {
        $title = $_POST['title'];
        $description = $_POST['description'];
        $is_blurred = isset($_POST['is_blurred']) ? 1 : 0;
        
        $before_img = '';
        $after_img = '';
        
        if (isset($_FILES['before_image']) && $_FILES['before_image']['error'] === 0) {
            $before_img = 'uploads/progress/' . time() . '_before_' . $_FILES['before_image']['name'];
            move_uploaded_file($_FILES['before_image']['tmp_name'], '../' . $before_img);
        }
        
        if (isset($_FILES['after_image']) && $_FILES['after_image']['error'] === 0) {
            $after_img = 'uploads/progress/' . time() . '_after_' . $_FILES['after_image']['name'];
            move_uploaded_file($_FILES['after_image']['tmp_name'], '../' . $after_img);
        }
        
        if ($before_img && $after_img) {
            $stmt = $pdo->prepare("INSERT INTO progress_photos (title, description, before_image_url, after_image_url, is_blurred) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$title, $description, $before_img, $after_img, $is_blurred]);
            $message = "Photo added successfully!";
        }
    } elseif (isset($_POST['delete_photo'])) {
        $id = $_POST['photo_id'];
        $stmt = $pdo->prepare("DELETE FROM progress_photos WHERE id = ?");
        $stmt->execute([$id]);
        $message = "Photo removed.";
    }
}

$photos = $pdo->query("SELECT * FROM progress_photos ORDER BY display_order ASC, id DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Progress Photos - Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-zinc-950 text-white p-8">
    <div class="max-w-4xl mx-auto">
        <h1 class="text-3xl font-bold mb-8 text-emerald-500 uppercase">Manage Progress Photos</h1>
        
        <?php if ($message): ?>
            <div class="bg-emerald-500/20 border border-emerald-500 text-emerald-500 p-4 rounded-xl mb-8"><?php echo $message; ?></div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data" class="bg-zinc-900 p-6 rounded-2xl border border-white/10 mb-12">
            <h2 class="text-xl font-bold mb-6 uppercase">Add New Comparison</h2>
            <div class="grid grid-cols-2 gap-6 mb-6">
                <div>
                    <label class="block text-xs uppercase tracking-widest text-zinc-500 mb-2">Title</label>
                    <input type="text" name="title" required class="w-full bg-black border border-white/10 rounded-lg p-3 text-white">
                </div>
                <div>
                    <label class="block text-xs uppercase tracking-widest text-zinc-500 mb-2">Blur Faces?</label>
                    <input type="checkbox" name="is_blurred" class="w-6 h-6 accent-emerald-500">
                </div>
            </div>
            <div class="grid grid-cols-2 gap-6 mb-6">
                <div>
                    <label class="block text-xs uppercase tracking-widest text-zinc-500 mb-2">Before Image</label>
                    <input type="file" name="before_image" required class="w-full text-zinc-500">
                </div>
                <div>
                    <label class="block text-xs uppercase tracking-widest text-zinc-500 mb-2">After Image</label>
                    <input type="file" name="after_image" required class="w-full text-zinc-500">
                </div>
            </div>
            <button type="submit" name="add_photo" class="w-full bg-emerald-600 hover:bg-emerald-500 text-white font-bold py-3 rounded-xl uppercase tracking-widest transition-all">Upload Progress Photo</button>
        </form>

        <div class="grid md:grid-cols-2 gap-8">
            <?php foreach ($photos as $photo): ?>
                <div class="bg-zinc-900 border border-white/10 p-4 rounded-2xl">
                    <div class="flex gap-2 mb-4">
                        <img src="../<?php echo $photo['before_image_url']; ?>" class="w-1/2 aspect-square object-cover rounded-lg <?php echo $photo['is_blurred'] ? 'blur-md' : ''; ?>">
                        <img src="../<?php echo $photo['after_image_url']; ?>" class="w-1/2 aspect-square object-cover rounded-lg <?php echo $photo['is_blurred'] ? 'blur-md' : ''; ?>">
                    </div>
                    <div class="flex justify-between items-center">
                        <h3 class="font-bold uppercase"><?php echo htmlspecialchars($photo['title']); ?></h3>
                        <form method="POST" onsubmit="return confirm('Delete this photo?');">
                            <input type="hidden" name="photo_id" value="<?php echo $photo['id']; ?>">
                            <button type="submit" name="delete_photo" class="text-red-500 text-xs uppercase font-bold">Remove</button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</body>
</html>
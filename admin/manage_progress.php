<?php
require_once '../includes/config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_photo'])) {
        // Existing add logic...
    } elseif (isset($_POST['edit_photo'])) {
        $id = $_POST['photo_id'];
        $title = $_POST['title'] ?? 'Untitled';
        $description = $_POST['description'] ?? '';
        $is_blurred = isset($_POST['is_blurred']) ? 1 : 0;
        
        $stmt = $pdo->prepare("UPDATE progress_photos SET title = ?, description = ?, is_blurred = ? WHERE id = ?");
        $stmt->execute([$title, $description, $is_blurred, $id]);
        
        // Handle image updates if provided
        if (isset($_FILES['before_image']) && $_FILES['before_image']['error'] === 0) {
            $before_img = 'uploads/progress/' . time() . '_before_' . $_FILES['before_image']['name'];
            move_uploaded_file($_FILES['before_image']['tmp_name'], '../' . $before_img);
            $pdo->prepare("UPDATE progress_photos SET before_image_url = ? WHERE id = ?")->execute([$before_img, $id]);
        }
        if (isset($_FILES['after_image']) && $_FILES['after_image']['error'] === 0) {
            $after_img = 'uploads/progress/' . time() . '_after_' . $_FILES['after_image']['name'];
            move_uploaded_file($_FILES['after_image']['tmp_name'], '../' . $after_img);
            $pdo->prepare("UPDATE progress_photos SET after_image_url = ? WHERE id = ?")->execute([$after_img, $id]);
        }
        $message = "Photo updated successfully!";
    } elseif (isset($_POST['delete_photo'])) {
        $title = $_POST['title'] ?? 'Untitled';
        $description = $_POST['description'] ?? '';
        $is_blurred = isset($_POST['is_blurred']) ? 1 : 0;

        $before_img = '';
        $after_img = '';

        if (isset($_FILES['before_image']) && $_FILES['before_image']['error'] === 0) {
            $before_img = 'uploads/progress/' . time() . '_before_' . $_FILES['before_image']['name'];
            $upload_path = '../' . $before_img;
            if (!is_dir(dirname($upload_path))) {
                mkdir(dirname($upload_path), 0777, true);
            }
            move_uploaded_file($_FILES['before_image']['tmp_name'], $upload_path);
        }

        if (isset($_FILES['after_image']) && $_FILES['after_image']['error'] === 0) {
            $after_img = 'uploads/progress/' . time() . '_after_' . $_FILES['after_image']['name'];
            $upload_path = '../' . $after_img;
            if (!is_dir(dirname($upload_path))) {
                mkdir(dirname($upload_path), 0777, true);
            }
            move_uploaded_file($_FILES['after_image']['tmp_name'], $upload_path);
        }

        if ($before_img && $after_img) {
            try {
                $stmt = $pdo->prepare("INSERT INTO progress_photos (title, description, before_image_url, after_image_url, is_blurred) VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([$title, $description, $before_img, $after_img, $is_blurred]);
                $message = "Photo added successfully!";
            } catch (PDOException $e) {
                // If it fails because column doesn't exist, try without description as fallback
                $stmt = $pdo->prepare("INSERT INTO progress_photos (title, before_image_url, after_image_url, is_blurred) VALUES (?, ?, ?, ?)");
                $stmt->execute([$title, $before_img, $after_img, $is_blurred]);
                $message = "Photo added (description skipped due to database sync).";
            }
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
            <div class="grid grid-cols-1 gap-6 mb-6">
                <div>
                    <label class="block text-xs uppercase tracking-widest text-zinc-500 mb-2">Description / Notes</label>
                    <textarea name="description" class="w-full bg-black border border-white/10 rounded-lg p-3 text-white h-24"></textarea>
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
                    <div class="space-y-4">
                        <h3 class="font-bold uppercase"><?php echo htmlspecialchars($photo['title'] ?? 'Untitled'); ?></h3>
                        <p class="text-xs text-zinc-500 italic"><?php echo htmlspecialchars($photo['description'] ?? ''); ?></p>
                        
                        <div class="flex justify-between items-center pt-4 border-t border-white/5">
                            <button onclick='openEditModal(<?php echo json_encode($photo); ?>)' class="text-emerald-500 text-xs uppercase font-bold hover:text-emerald-400">Edit</button>
                            <form method="POST" onsubmit="return confirm('Delete this photo?');">
                                <input type="hidden" name="photo_id" value="<?php echo $photo['id']; ?>">
                                <button type="submit" name="delete_photo" class="text-red-500 text-xs uppercase font-bold hover:text-red-400">Remove</button>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Edit Modal -->
    <div id="editModal" class="fixed inset-0 bg-black/80 backdrop-blur-sm hidden items-center justify-center p-4 z-50">
        <div class="bg-zinc-900 border border-white/10 p-8 rounded-3xl max-w-lg w-full">
            <h2 class="text-2xl font-bold mb-6 uppercase tracking-tighter">Edit Comparison</h2>
            <form method="POST" enctype="multipart/form-data" class="space-y-6">
                <input type="hidden" name="photo_id" id="edit_id">
                <div>
                    <label class="block text-xs uppercase tracking-widest text-zinc-500 mb-2">Description / Notes</label>
                    <textarea name="description" id="edit_description" class="w-full bg-black border border-white/10 rounded-lg p-3 text-white h-24"></textarea>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs uppercase tracking-widest text-zinc-500 mb-2">Before Image (Optional)</label>
                        <input type="file" name="before_image" class="w-full text-xs text-zinc-500">
                    </div>
                    <div>
                        <label class="block text-xs uppercase tracking-widest text-zinc-500 mb-2">After Image (Optional)</label>
                        <input type="file" name="after_image" class="w-full text-xs text-zinc-500">
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <input type="checkbox" name="is_blurred" id="edit_blur" class="w-5 h-5 accent-emerald-500">
                    <label class="text-xs uppercase tracking-widest text-zinc-500">Blur Faces?</label>
                </div>
                <div class="flex gap-4 pt-4">
                    <button type="button" onclick="closeEditModal()" class="flex-1 bg-zinc-800 hover:bg-zinc-700 text-white font-bold py-3 rounded-xl uppercase tracking-widest transition-all">Cancel</button>
                    <button type="submit" name="edit_photo" class="flex-1 bg-emerald-600 hover:bg-emerald-500 text-white font-bold py-3 rounded-xl uppercase tracking-widest transition-all">Save Changes</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openEditModal(photo) {
            document.getElementById('edit_id').value = photo.id;
            document.getElementById('edit_description').value = photo.description || '';
            document.getElementById('edit_blur').checked = photo.is_blurred == 1;
            document.getElementById('editModal').classList.remove('hidden');
            document.getElementById('editModal').classList.add('flex');
        }
        function closeEditModal() {
            document.getElementById('editModal').classList.add('hidden');
            document.getElementById('editModal').classList.remove('flex');
        }
    </script>
</body>
</html>
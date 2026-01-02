<?php
require_once '../includes/config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_photo'])) {
        $title = $_POST['title'] ?? 'Untitled';
        $description = $_POST['description'] ?? '';
        $is_blurred = isset($_POST['is_blurred']) ? 1 : 0;
        $before_blur_x = (int)($_POST['before_blur_x'] ?? 50);
        $before_blur_y = (int)($_POST['before_blur_y'] ?? 50);
        $after_blur_x = (int)($_POST['after_blur_x'] ?? 50);
        $after_blur_y = (int)($_POST['after_blur_y'] ?? 50);
        $blur_size = (int)($_POST['blur_size'] ?? 40);

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
                $stmt = $pdo->prepare("INSERT INTO progress_photos (title, description, before_image_url, after_image_url, is_blurred, before_blur_x, before_blur_y, after_blur_x, after_blur_y, blur_size) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([$title, $description, $before_img, $after_img, $is_blurred, $before_blur_x, $before_blur_y, $after_blur_x, $after_blur_y, $blur_size]);
                $message = "Photo added successfully!";
            } catch (PDOException $e) {
                $message = "Error: " . $e->getMessage();
            }
        }
    } elseif (isset($_POST['edit_photo'])) {
        $id = $_POST['photo_id'];
        $title = $_POST['title'] ?? 'Untitled';
        $description = $_POST['description'] ?? '';
        $is_blurred = isset($_POST['is_blurred']) ? 1 : 0;
        $before_blur_x = (int)($_POST['before_blur_x'] ?? 50);
        $before_blur_y = (int)($_POST['before_blur_y'] ?? 50);
        $after_blur_x = (int)($_POST['after_blur_x'] ?? 50);
        $after_blur_y = (int)($_POST['after_blur_y'] ?? 50);
        $blur_size = (int)($_POST['blur_size'] ?? 40);
        
        try {
            $stmt = $pdo->prepare("UPDATE progress_photos SET title = ?, description = ?, is_blurred = ?, before_blur_x = ?, before_blur_y = ?, after_blur_x = ?, after_blur_y = ?, blur_size = ? WHERE id = ?");
            $stmt->execute([$title, $description, $is_blurred, $before_blur_x, $before_blur_y, $after_blur_x, $after_blur_y, $blur_size, $id]);
            
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
        } catch (PDOException $e) {
            $message = "Error: " . $e->getMessage();
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
    <style>
        .blur-control { position: relative; cursor: crosshair; }
        .blur-marker { 
            position: absolute; width: 40px; height: 40px; 
            border: 2px solid rgba(255,255,255,0.2); border-radius: 50%; 
            background: rgba(255,255,255,0.1); backdrop-filter: blur(12px);
            transform: translate(-50%, -50%); pointer-events: none;
            box-shadow: 0 0 20px 10px rgba(255,255,255,0.05);
        }
    </style>
</head>
<body class="bg-zinc-950 text-white p-8">
    <div class="max-w-4xl mx-auto">
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-3xl font-bold text-emerald-500 uppercase">Manage Progress Photos</h1>
            <a href="index.php" class="bg-zinc-800 hover:bg-zinc-700 text-white px-6 py-2 rounded-full text-xs font-bold uppercase tracking-widest transition-all flex items-center gap-2">
                <span>←</span> Back to Dashboard
            </a>
        </div>
        
        <?php if ($message): ?>
            <div class="bg-emerald-500/20 border border-emerald-500 text-emerald-500 p-4 rounded-xl mb-8"><?php echo $message; ?></div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data" class="bg-zinc-900 p-6 rounded-2xl border border-white/10 mb-12">
            <h2 class="text-xl font-bold mb-6 uppercase">Add New Comparison</h2>
            
            <div id="preview-container" class="grid grid-cols-2 gap-4 mb-6 hidden">
                <div class="space-y-2">
                    <label class="block text-[10px] uppercase tracking-widest text-zinc-500">Position Blur (Click Image)</label>
                    <div class="blur-control rounded-2xl overflow-hidden border border-white/10 aspect-[4/5] relative">
                        <img id="before-preview" class="absolute inset-0 w-full h-full object-cover">
                        <div id="before-marker" class="blur-marker"></div>
                    </div>
                    <input type="hidden" name="before_blur_x" id="before_blur_x" value="50">
                    <input type="hidden" name="before_blur_y" id="before_blur_y" value="50">
                </div>
                <div class="space-y-2">
                    <label class="block text-[10px] uppercase tracking-widest text-zinc-500">Position Blur (Click Image)</label>
                    <div class="blur-control rounded-2xl overflow-hidden border border-white/10 aspect-[4/5] relative">
                        <img id="after-preview" class="absolute inset-0 w-full h-full object-cover">
                        <div id="after-marker" class="blur-marker"></div>
                    </div>
                    <input type="hidden" name="after_blur_x" id="after_blur_x" value="50">
                    <input type="hidden" name="after_blur_y" id="after_blur_y" value="50">
                </div>
            </div>

            <div class="grid grid-cols-1 gap-6 mb-6">
                <div>
                    <label class="block text-xs uppercase tracking-widest text-zinc-500 mb-2">Client Name / Title</label>
                    <input type="text" name="title" required class="w-full bg-black border border-white/10 rounded-lg p-3 text-white">
                </div>
                <div>
                    <label class="block text-xs uppercase tracking-widest text-zinc-500 mb-2">Description / Notes</label>
                    <textarea name="description" class="w-full bg-black border border-white/10 rounded-lg p-3 text-white h-24"></textarea>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-6 mb-6">
                <div>
                    <label class="block text-xs uppercase tracking-widest text-zinc-500 mb-2">Before Image</label>
                    <input type="file" name="before_image" required accept="image/*" onchange="previewImg(this, 'before')" class="w-full text-zinc-500">
                </div>
                <div>
                    <label class="block text-xs uppercase tracking-widest text-zinc-500 mb-2">After Image</label>
                    <input type="file" name="after_image" required accept="image/*" onchange="previewImg(this, 'after')" class="w-full text-zinc-500">
                </div>
            </div>
            <div class="flex items-center gap-4 mb-6">
                <div class="flex items-center gap-2">
                    <input type="checkbox" name="is_blurred" id="is_blurred" onchange="toggleMarkers()" class="w-6 h-6 accent-emerald-500">
                    <label class="text-xs uppercase tracking-widest text-zinc-500">Blur Faces for Privacy?</label>
                </div>
                <div class="flex-1">
                    <label class="block text-[10px] uppercase tracking-widest text-zinc-500 mb-1">Blur Size</label>
                    <input type="range" name="blur_size" id="blur_size_input" min="20" max="150" value="40" oninput="updateBlurSize(this.value)" class="w-full accent-emerald-500">
                </div>
            </div>
            <button type="submit" name="add_photo" class="w-full bg-emerald-600 hover:bg-emerald-500 text-white font-bold py-3 rounded-xl uppercase tracking-widest transition-all">Upload Progress Photo</button>
        </form>

        <div class="grid md:grid-cols-2 gap-8">
            <?php foreach ($photos as $photo): ?>
                <div class="bg-zinc-900 border border-white/10 p-4 rounded-3xl">
                    <div class="flex gap-2 mb-4">
                        <div class="relative w-1/2 overflow-hidden rounded-2xl aspect-[4/5]">
                            <img src="../<?php echo $photo['before_image_url']; ?>" class="absolute inset-0 w-full h-full object-cover">
                            <?php if ($photo['is_blurred']): ?>
                                <div class="absolute bg-white/10 backdrop-blur-xl rounded-full border border-white/20 -translate-x-1/2 -translate-y-1/2 shadow-2xl" 
                                     style="left: <?php echo $photo['before_blur_x'] ?? 50; ?>%; top: <?php echo $photo['before_blur_y'] ?? 50; ?>%; width: <?php echo $photo['blur_size'] ?? 40; ?>px; height: <?php echo $photo['blur_size'] ?? 40; ?>px; box-shadow: 0 0 20px 10px rgba(255,255,255,0.05);"></div>
                            <?php endif; ?>
                        </div>
                        <div class="relative w-1/2 overflow-hidden rounded-2xl aspect-[4/5]">
                            <img src="../<?php echo $photo['after_image_url']; ?>" class="absolute inset-0 w-full h-full object-cover">
                            <?php if ($photo['is_blurred']): ?>
                                <div class="absolute bg-white/10 backdrop-blur-xl rounded-full border border-white/20 -translate-x-1/2 -translate-y-1/2 shadow-2xl"
                                     style="left: <?php echo $photo['after_blur_x'] ?? 50; ?>%; top: <?php echo $photo['after_blur_y'] ?? 50; ?>%; width: <?php echo $photo['blur_size'] ?? 40; ?>px; height: <?php echo $photo['blur_size'] ?? 40; ?>px; box-shadow: 0 0 20px 10px rgba(255,255,255,0.05);"></div>
                            <?php endif; ?>
                        </div>
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
        <div class="bg-zinc-900 border border-white/10 p-8 rounded-3xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
            <h2 class="text-2xl font-bold mb-6 uppercase tracking-tighter">Edit Comparison</h2>
            <form method="POST" enctype="multipart/form-data" class="space-y-6">
                <input type="hidden" name="photo_id" id="edit_id">
                
                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-2">
                        <label class="block text-[10px] uppercase tracking-widest text-zinc-500">Before Position</label>
                        <div class="blur-control rounded-2xl overflow-hidden border border-white/10 aspect-[4/5] relative" onclick="setBlur(event, 'edit_before')">
                            <img id="edit_before_preview" class="absolute inset-0 w-full h-full object-cover">
                            <div id="edit_before_marker" class="blur-marker"></div>
                        </div>
                        <input type="hidden" name="before_blur_x" id="edit_before_blur_x">
                        <input type="hidden" name="before_blur_y" id="edit_before_blur_y">
                    </div>
                    <div class="space-y-2">
                        <label class="block text-[10px] uppercase tracking-widest text-zinc-500">After Position</label>
                        <div class="blur-control rounded-2xl overflow-hidden border border-white/10 aspect-[4/5] relative" onclick="setBlur(event, 'edit_after')">
                            <img id="edit_after_preview" class="absolute inset-0 w-full h-full object-cover">
                            <div id="edit_after_marker" class="blur-marker"></div>
                        </div>
                        <input type="hidden" name="after_blur_x" id="edit_after_blur_x">
                        <input type="hidden" name="after_blur_y" id="edit_after_blur_y">
                    </div>
                </div>

                <div>
                    <label class="block text-xs uppercase tracking-widest text-zinc-500 mb-2">Client Name / Title</label>
                    <input type="text" name="title" id="edit_title" required class="w-full bg-black border border-white/10 rounded-lg p-3 text-white">
                </div>
                <div>
                    <label class="block text-xs uppercase tracking-widest text-zinc-500 mb-2">Description / Notes</label>
                    <textarea name="description" id="edit_description" class="w-full bg-black border border-white/10 rounded-lg p-3 text-white h-24"></textarea>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs uppercase tracking-widest text-zinc-500 mb-2">Before Image (Optional)</label>
                        <input type="file" name="before_image" accept="image/*" onchange="previewImg(this, 'edit_before')" class="w-full text-xs text-zinc-500">
                    </div>
                    <div>
                        <label class="block text-xs uppercase tracking-widest text-zinc-500 mb-2">After Image (Optional)</label>
                        <input type="file" name="after_image" accept="image/*" onchange="previewImg(this, 'edit_after')" class="w-full text-xs text-zinc-500">
                    </div>
                </div>
                <div class="flex items-center gap-4">
                    <div class="flex items-center gap-2">
                        <input type="checkbox" name="is_blurred" id="edit_blur" onchange="toggleMarkers('edit')" class="w-5 h-5 accent-emerald-500">
                        <label class="text-xs uppercase tracking-widest text-zinc-500">Blur Faces?</label>
                    </div>
                    <div class="flex-1">
                        <label class="block text-[10px] uppercase tracking-widest text-zinc-500 mb-1">Blur Size</label>
                        <input type="range" name="blur_size" id="edit_blur_size" min="20" max="150" value="40" oninput="updateBlurSize(this.value)" class="w-full accent-emerald-500">
                    </div>
                </div>
                <div class="flex gap-4 pt-4">
                    <button type="button" onclick="closeEditModal()" class="flex-1 bg-zinc-800 hover:bg-zinc-700 text-white font-bold py-3 rounded-xl uppercase tracking-widest transition-all">Cancel</button>
                    <button type="submit" name="edit_photo" class="flex-1 bg-emerald-600 hover:bg-emerald-500 text-white font-bold py-3 rounded-xl uppercase tracking-widest transition-all">Save Changes</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function previewImg(input, type) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const img = document.getElementById(type + '-preview') || document.getElementById(type + '_preview');
                    img.src = e.target.result;
                    if (type === 'before' || type === 'after') {
                        document.getElementById('preview-container').classList.remove('hidden');
                    }
                    toggleMarkers(type.startsWith('edit') ? 'edit' : '');
                }
                reader.readAsDataURL(input.files[0]);
            }
        }

        function setBlur(e, type) {
            const rect = e.currentTarget.getBoundingClientRect();
            const x = ((e.clientX - rect.left) / rect.width * 100).toFixed(2);
            const y = ((e.clientY - rect.top) / rect.height * 100).toFixed(2);
            
            const marker = document.getElementById(type + '-marker') || document.getElementById(type + '_marker');
            marker.style.left = x + '%';
            marker.style.top = y + '%';
            
            document.getElementById(type + '_blur_x').value = x;
            document.getElementById(type + '_blur_y').value = y;
        }

        function toggleMarkers(prefix = '') {
            const isBlurred = document.getElementById(prefix ? 'edit_blur' : 'is_blurred').checked;
            const markers = document.querySelectorAll(prefix ? '[id^="edit_"][id$="_marker"]' : '[id$="-marker"]');
            markers.forEach(m => m.style.display = isBlurred ? 'block' : 'none');
        }

        function updateBlurSize(size) {
            document.querySelectorAll('.blur-marker').forEach(m => {
                m.style.width = size + 'px';
                m.style.height = size + 'px';
            });
        }

        function openEditModal(photo) {
            document.getElementById('edit_id').value = photo.id;
            document.getElementById('edit_title').value = photo.title || '';
            document.getElementById('edit_description').value = photo.description || '';
            document.getElementById('edit_blur').checked = photo.is_blurred == 1;
            document.getElementById('edit_blur_size').value = photo.blur_size || 40;
            updateBlurSize(photo.blur_size || 40);
            
            document.getElementById('edit_before_preview').src = '../' + photo.before_image_url;
            document.getElementById('edit_after_preview').src = '../' + photo.after_image_url;
            
            document.getElementById('edit_before_blur_x').value = photo.before_blur_x || 50;
            document.getElementById('edit_before_blur_y').value = photo.before_blur_y || 50;
            document.getElementById('edit_after_blur_x').value = photo.after_blur_x || 50;
            document.getElementById('edit_after_blur_y').value = photo.after_blur_y || 50;
            
            document.getElementById('edit_before_marker').style.left = (photo.before_blur_x || 50) + '%';
            document.getElementById('edit_before_marker').style.top = (photo.before_blur_y || 50) + '%';
            document.getElementById('edit_after_marker').style.left = (photo.after_blur_x || 50) + '%';
            document.getElementById('edit_after_marker').style.top = (photo.after_blur_y || 50) + '%';
            
            toggleMarkers('edit');
            
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
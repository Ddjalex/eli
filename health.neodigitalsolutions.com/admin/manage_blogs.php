<?php
require_once '../includes/config.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

$action = $_GET['action'] ?? 'list';
$id = $_GET['id'] ?? null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $content = $_POST['content'];
    $image_url = $_POST['existing_image'] ?? '';

    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = '../uploads/blog/';
        if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
        $file_name = time() . '_' . basename($_FILES['image']['name']);
        $target_file = $upload_dir . $file_name;
        if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
            $image_url = 'uploads/blog/' . $file_name;
        }
    }

    if ($id) {
        $stmt = $pdo->prepare("UPDATE blogs SET title = ?, content = ?, image_url = ? WHERE id = ?");
        $stmt->execute([$title, $content, $image_url, $id]);
    } else {
        $stmt = $pdo->prepare("INSERT INTO blogs (title, content, image_url) VALUES (?, ?, ?)");
        $stmt->execute([$title, $content, $image_url]);
    }
    header("Location: manage_blogs.php");
    exit;
}

if ($action === 'delete' && $id) {
    $stmt = $pdo->prepare("DELETE FROM blogs WHERE id = ?");
    $stmt->execute([$id]);
    header("Location: manage_blogs.php");
    exit;
}

$blogs = $pdo->query("SELECT * FROM blogs ORDER BY created_at DESC")->fetchAll();
$blog = $id ? $pdo->prepare("SELECT * FROM blogs WHERE id = ?") : null;
if ($blog) {
    $blog->execute([$id]);
    $blog = $blog->fetch();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Blogs | Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-[#09090b] text-zinc-200 p-8">
    <div class="max-w-4xl mx-auto">
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-3xl font-bold">Manage Blogs</h1>
            <a href="index.php" class="text-zinc-400 hover:text-white">Back to Dashboard</a>
        </div>

        <?php if ($action === 'add' || $action === 'edit'): ?>
            <form method="POST" enctype="multipart/form-data" class="bg-zinc-900 p-6 rounded-2xl border border-white/5 space-y-4">
                <input type="hidden" name="existing_image" value="<?php echo $blog['image_url'] ?? ''; ?>">
                <div>
                    <label class="block text-sm font-medium mb-1">Title</label>
                    <input type="text" name="title" value="<?php echo $blog['title'] ?? ''; ?>" class="w-full bg-black border border-white/10 p-2 rounded-lg" required>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Content</label>
                    <textarea name="content" rows="10" class="w-full bg-black border border-white/10 p-2 rounded-lg" required><?php echo $blog['content'] ?? ''; ?></textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Image</label>
                    <input type="file" name="image" class="w-full bg-black border border-white/10 p-2 rounded-lg">
                </div>
                <button type="submit" class="bg-emerald-600 px-6 py-2 rounded-lg font-bold">Save Blog</button>
            </form>
        <?php else: ?>
            <a href="?action=add" class="bg-emerald-600 px-6 py-2 rounded-lg font-bold inline-block mb-6">Add New Blog</a>
            <div class="grid gap-4">
                <?php foreach ($blogs as $b): ?>
                    <div class="bg-zinc-900 p-4 rounded-xl border border-white/5 flex justify-between items-center">
                        <div class="flex items-center gap-4">
                            <?php
                            $stmt_profile = $pdo->prepare("SELECT value FROM site_settings WHERE "key" = 'about_image'");
                            $stmt_profile->execute();
                            $profile_img = $stmt_profile->fetchColumn() ?: 'attached_assets/stock_images/professional_dietiti_8bb8decd.jpg';
                            ?>
                            <img src="../<?php echo htmlspecialchars($profile_img); ?>" class="w-10 h-10 rounded-full object-cover border border-emerald-500/20">
                            <div>
                                <h3 class="font-bold"><?php echo htmlspecialchars($b['title']); ?></h3>
                                <p class="text-xs text-zinc-500"><?php echo $b['created_at']; ?></p>
                            </div>
                        </div>
                        <div class="flex gap-2">
                            <a href="?action=edit&id=<?php echo $b['id']; ?>" class="text-blue-400">Edit</a>
                            <a href="?action=delete&id=<?php echo $b['id']; ?>" class="text-red-400" onclick="return confirm('Delete?')">Delete</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
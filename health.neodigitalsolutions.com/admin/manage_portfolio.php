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
    $description = $_POST['description'];
    $video_url = $_POST['video_url'] ?? '';
    $image_url = $_POST['existing_image'] ?? '';

    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = '../uploads/portfolio/';
        if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
        $file_name = time() . '_' . basename($_FILES['image']['name']);
        $target_file = $upload_dir . $file_name;
        if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
            $image_url = 'uploads/portfolio/' . $file_name;
        }
    }

    if ($id) {
        $stmt = $pdo->prepare("UPDATE portfolio_projects SET title = ?, description = ?, image_url = ?, video_url = ? WHERE id = ?");
        $stmt->execute([$title, $description, $image_url, $video_url, $id]);
    } else {
        $stmt = $pdo->prepare("INSERT INTO portfolio_projects (title, description, image_url, video_url) VALUES (?, ?, ?, ?)");
        $stmt->execute([$title, $description, $image_url, $video_url]);
    }
    header("Location: manage_portfolio.php");
    exit;
}

if ($action === 'delete' && $id) {
    $stmt = $pdo->prepare("DELETE FROM portfolio_projects WHERE id = ?");
    $stmt->execute([id]);
    header("Location: manage_portfolio.php");
    exit;
}

$projects = $pdo->query("SELECT * FROM portfolio_projects ORDER BY created_at DESC")->fetchAll();
$project = $id ? $pdo->prepare("SELECT * FROM portfolio_projects WHERE id = ?") : null;
if ($project) {
    $project->execute([$id]);
    $project = $project->fetch();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Portfolio | Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-[#09090b] text-zinc-200 p-8">
    <div class="max-w-4xl mx-auto">
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-3xl font-bold">Manage Portfolio</h1>
            <a href="index.php" class="text-zinc-400 hover:text-white">Back to Dashboard</a>
        </div>

        <?php if ($action === 'add' || $action === 'edit'): ?>
            <form method="POST" enctype="multipart/form-data" class="bg-zinc-900 p-6 rounded-2xl border border-white/5 space-y-4">
                <input type="hidden" name="existing_image" value="<?php echo $project['image_url'] ?? ''; ?>">
                <div>
                    <label class="block text-sm font-medium mb-1">Title</label>
                    <input type="text" name="title" value="<?php echo $project['title'] ?? ''; ?>" class="w-full bg-black border border-white/10 p-2 rounded-lg" required>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Description</label>
                    <textarea name="description" rows="5" class="w-full bg-black border border-white/10 p-2 rounded-lg" required><?php echo $project['description'] ?? ''; ?></textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Video URL (YouTube/Vimeo/Link)</label>
                    <input type="text" name="video_url" value="<?php echo $project['video_url'] ?? ''; ?>" class="w-full bg-black border border-white/10 p-2 rounded-lg" placeholder="https://www.youtube.com/watch?v=...">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Image (Thumbnail/Fallback)</label>
                    <input type="file" name="image" class="w-full bg-black border border-white/10 p-2 rounded-lg">
                </div>
                <button type="submit" class="bg-emerald-600 px-6 py-2 rounded-lg font-bold">Save Project</button>
            </form>
        <?php else: ?>
            <a href="?action=add" class="bg-emerald-600 px-6 py-2 rounded-lg font-bold inline-block mb-6">Add New Project</a>
            <div class="grid gap-4">
                <?php foreach ($projects as $p): ?>
                    <div class="bg-zinc-900 p-4 rounded-xl border border-white/5 flex justify-between items-center">
                        <div>
                            <h3 class="font-bold"><?php echo htmlspecialchars($p['title']); ?></h3>
                            <p class="text-xs text-zinc-500"><?php echo $p['created_at']; ?></p>
                        </div>
                        <div class="flex gap-2">
                            <a href="?action=edit&id=<?php echo $p['id']; ?>" class="text-blue-400">Edit</a>
                            <a href="?action=delete&id=<?php echo $p['id']; ?>" class="text-red-400" onclick="return confirm('Delete?')">Delete</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
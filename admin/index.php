<?php
require_once '../includes/config.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

if (isset($_GET['approve'])) {
    $user_id = $_GET['approve'];
    $stmt = $pdo->prepare("UPDATE users SET status = 'approved' WHERE id = ?");
    $stmt->execute([$user_id]);
    header("Location: index.php");
    exit;
}

$stmt = $pdo->query("SELECT u.*, p.receipt_path FROM users u LEFT JOIN payments p ON u.id = p.user_id WHERE u.role = 'user'");
$users = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Panel - Eleni Mekuria</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-black text-white p-8">
    <div class="max-w-6xl mx-auto">
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-3xl font-bold">Admin Dashboard</h1>
            <a href="manage_images.php" class="bg-emerald-600 text-white px-6 py-2 rounded-full font-bold hover:bg-emerald-500">Manage Images</a>
        </div>
        <table class="w-full border-collapse">
            <thead>
                <tr class="bg-zinc-900 border-b border-zinc-800 text-left">
                    <th class="p-4">Client</th>
                    <th class="p-4">Status</th>
                    <th class="p-4">Receipt</th>
                    <th class="p-4">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $u): ?>
                    <tr class="border-b border-zinc-900">
                        <td class="p-4">
                            <div class="font-bold"><?php echo htmlspecialchars($u['name']); ?></div>
                            <div class="text-sm text-zinc-500"><?php echo $u['email']; ?></div>
                        </td>
                        <td class="p-4">
                            <span class="<?php echo $u['status'] === 'approved' ? 'text-green-500' : 'text-yellow-500'; ?>">
                                <?php echo strtoupper($u['status']); ?>
                            </span>
                        </td>
                        <td class="p-4">
                            <?php if ($u['receipt_path']): ?>
                                <a href="<?php echo $u['receipt_path']; ?>" target="_blank" class="text-blue-400 underline">View Receipt</a>
                            <?php else: ?>
                                <span class="text-zinc-600">No receipt</span>
                            <?php endif; ?>
                        </td>
                        <td class="p-4">
                            <?php if ($u['status'] === 'pending'): ?>
                                <a href="?approve=<?php echo $u['id']; ?>" class="bg-white text-black px-4 py-1 rounded font-bold">Approve</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
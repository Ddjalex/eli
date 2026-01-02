<?php
/** @var PDO $pdo */
require_once '../includes/config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['approve_id'])) {
        $stmt = $pdo->prepare("UPDATE testimonials SET status = 'approved' WHERE id = ?");
        $stmt->execute([$_POST['approve_id']]);
        $message = "Testimonial approved!";
    } elseif (isset($_POST['delete_id'])) {
        $stmt = $pdo->prepare("DELETE FROM testimonials WHERE id = ?");
        $stmt->execute([$_POST['delete_id']]);
        $message = "Testimonial deleted!";
    }
}

$pending = $pdo->query("SELECT * FROM testimonials WHERE status = 'pending' ORDER BY created_at DESC")->fetchAll();
$approved = $pdo->query("SELECT * FROM testimonials WHERE status = 'approved' ORDER BY created_at DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Testimonials - Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-zinc-950 text-white p-8">
    <div class="max-w-6xl mx-auto">
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-3xl font-bold text-emerald-500 uppercase">Manage Testimonials</h1>
            <a href="index.php" class="bg-zinc-800 hover:bg-zinc-700 text-white px-6 py-2 rounded-full text-xs font-bold uppercase tracking-widest transition-all">← Dashboard</a>
        </div>

        <?php if ($message): ?>
            <div class="bg-emerald-500/20 border border-emerald-500 text-emerald-500 p-4 rounded-xl mb-8"><?php echo $message; ?></div>
        <?php endif; ?>

        <div class="grid lg:grid-cols-2 gap-8">
            <!-- Pending Section -->
            <div class="space-y-6">
                <h2 class="text-xl font-bold uppercase tracking-widest text-zinc-500">Pending Approval</h2>
                <?php if (empty($pending)): ?>
                    <p class="text-zinc-600 italic">No pending testimonials.</p>
                <?php endif; ?>
                <?php foreach ($pending as $t): ?>
                    <div class="bg-zinc-900 p-6 rounded-2xl border border-white/10">
                        <p class="text-zinc-400 italic mb-4">"<?php echo htmlspecialchars($t['content']); ?>"</p>
                        <div class="flex justify-between items-end">
                            <div>
                                <h4 class="font-bold text-emerald-500 uppercase text-xs"><?php echo htmlspecialchars($t['client_name']); ?></h4>
                                <p class="text-[10px] text-zinc-500 uppercase tracking-widest"><?php echo htmlspecialchars($t['client_role']); ?></p>
                            </div>
                            <div class="flex gap-2">
                                <form method="POST">
                                    <input type="hidden" name="approve_id" value="<?php echo $t['id']; ?>">
                                    <button class="bg-emerald-600 hover:bg-emerald-500 text-white px-4 py-2 rounded-lg text-[10px] font-bold uppercase tracking-widest">Approve</button>
                                </form>
                                <form method="POST">
                                    <input type="hidden" name="delete_id" value="<?php echo $t['id']; ?>">
                                    <button class="bg-red-600/20 hover:bg-red-600 text-red-500 hover:text-white px-4 py-2 rounded-lg text-[10px] font-bold uppercase tracking-widest border border-red-500/20">Delete</button>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Approved Section -->
            <div class="space-y-6">
                <h2 class="text-xl font-bold uppercase tracking-widest text-zinc-500">Approved</h2>
                <?php if (empty($approved)): ?>
                    <p class="text-zinc-600 italic">No approved testimonials yet.</p>
                <?php endif; ?>
                <?php foreach ($approved as $t): ?>
                    <div class="bg-zinc-900/50 p-6 rounded-2xl border border-white/5 opacity-80">
                        <p class="text-zinc-500 italic mb-4">"<?php echo htmlspecialchars($t['content']); ?>"</p>
                        <div class="flex justify-between items-end">
                            <div>
                                <h4 class="font-bold text-zinc-400 uppercase text-xs"><?php echo htmlspecialchars($t['client_name']); ?></h4>
                                <p class="text-[10px] text-zinc-600 uppercase tracking-widest"><?php echo htmlspecialchars($t['client_role']); ?></p>
                            </div>
                            <form method="POST">
                                <input type="hidden" name="delete_id" value="<?php echo $t['id']; ?>">
                                <button class="text-red-500/50 hover:text-red-500 text-[10px] font-bold uppercase tracking-widest">Remove</button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</body>
</html>
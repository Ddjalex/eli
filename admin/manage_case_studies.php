<?php
require_once '../includes/config.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_case'])) {
        $stmt = $pdo->prepare("INSERT INTO case_studies (title, category, summary, approach, results, display_order) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $_POST['title'],
            $_POST['category'],
            $_POST['summary'],
            $_POST['approach'],
            $_POST['results'],
            (int)$_POST['display_order']
        ]);
        $message = "Case study added successfully!";
    } elseif (isset($_POST['update_case'])) {
        $stmt = $pdo->prepare("UPDATE case_studies SET title = ?, category = ?, summary = ?, approach = ?, results = ?, display_order = ? WHERE id = ?");
        $stmt->execute([
            $_POST['title'],
            $_POST['category'],
            $_POST['summary'],
            $_POST['approach'],
            $_POST['results'],
            (int)$_POST['display_order'],
            $_POST['case_id']
        ]);
        $message = "Case study updated!";
    } elseif (isset($_POST['delete_case'])) {
        $stmt = $pdo->prepare("DELETE FROM case_studies WHERE id = ?");
        $stmt->execute([$_POST['case_id']]);
        $message = "Case study removed.";
    }
}

$cases = $pdo->query("SELECT * FROM case_studies ORDER BY display_order ASC, id DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Case Studies - Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .glass { background: rgba(24, 24, 27, 0.7); backdrop-filter: blur(12px); border: 1px solid rgba(255, 255, 255, 0.1); }
    </style>
</head>
<body class="bg-[#09090b] text-white">
    <div class="flex min-h-screen">
        <aside class="w-64 border-r border-white/5 bg-[#09090b] p-6 hidden lg:block">
            <div class="text-xl font-bold text-emerald-500 mb-10 tracking-tighter uppercase">Admin Panel</div>
            <nav class="space-y-2">
                <a href="index.php" class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-white/5 transition-all text-zinc-400 hover:text-white">Dashboard</a>
                <a href="manage_progress.php" class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-white/5 transition-all text-zinc-400 hover:text-white">Progress Photos</a>
                <a href="manage_case_studies.php" class="flex items-center gap-3 px-4 py-3 rounded-xl bg-emerald-500/10 text-emerald-500 border border-emerald-500/20 font-medium">Case Studies</a>
                <a href="../logout.php" class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-red-500/10 transition-all text-zinc-500 hover:text-red-500">Logout</a>
            </nav>
        </aside>

        <main class="flex-1 p-8 lg:p-12">
            <div class="max-w-6xl mx-auto">
                <div class="flex justify-between items-center mb-12">
                    <div>
                        <h1 class="text-4xl font-black uppercase tracking-tighter text-emerald-500">Clinical Case Studies</h1>
                        <p class="text-zinc-500 text-sm mt-1">Manage your scientific portfolio and patient outcomes.</p>
                    </div>
                </div>

                <?php if ($message): ?>
                    <div class="bg-emerald-500/10 border border-emerald-500/50 p-4 rounded-xl mb-8 text-emerald-500 font-bold text-sm uppercase tracking-widest text-center"><?php echo $message; ?></div>
                <?php endif; ?>

                <!-- Add Form -->
                <div class="glass p-8 rounded-3xl mb-12 shadow-2xl">
                    <h2 class="text-xl font-bold mb-8 uppercase tracking-tight text-white flex items-center gap-3">
                        <span class="w-8 h-8 rounded-full bg-emerald-600 flex items-center justify-center text-xs">+</span>
                        Add New Case Study
                    </h2>
                    <form method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <input type="hidden" name="add_case" value="1">
                        <div class="md:col-span-1">
                            <label class="block text-[10px] font-bold text-zinc-500 uppercase tracking-widest mb-2">Title</label>
                            <input type="text" name="title" required class="w-full bg-black border border-white/10 p-4 rounded-xl focus:border-emerald-500 outline-none">
                        </div>
                        <div class="md:col-span-1">
                            <label class="block text-[10px] font-bold text-zinc-500 uppercase tracking-widest mb-2">Category</label>
                            <input type="text" name="category" required placeholder="e.g. Clinical Nutrition" class="w-full bg-black border border-white/10 p-4 rounded-xl focus:border-emerald-500 outline-none">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-[10px] font-bold text-zinc-500 uppercase tracking-widest mb-2">Patient Summary</label>
                            <textarea name="summary" required rows="3" class="w-full bg-black border border-white/10 p-4 rounded-xl focus:border-emerald-500 outline-none"></textarea>
                        </div>
                        <div class="md:col-span-1">
                            <label class="block text-[10px] font-bold text-zinc-500 uppercase tracking-widest mb-2">Approach</label>
                            <textarea name="approach" required rows="5" class="w-full bg-black border border-white/10 p-4 rounded-xl focus:border-emerald-500 outline-none"></textarea>
                        </div>
                        <div class="md:col-span-1">
                            <label class="block text-[10px] font-bold text-zinc-500 uppercase tracking-widest mb-2">Results</label>
                            <textarea name="results" required rows="5" class="w-full bg-black border border-white/10 p-4 rounded-xl focus:border-emerald-500 outline-none"></textarea>
                        </div>
                        <div class="md:col-span-1">
                            <label class="block text-[10px] font-bold text-zinc-500 uppercase tracking-widest mb-2">Order</label>
                            <input type="number" name="display_order" value="0" class="w-full bg-black border border-white/10 p-4 rounded-xl focus:border-emerald-500 outline-none">
                        </div>
                        <div class="md:col-span-2">
                            <button type="submit" class="w-full bg-emerald-600 py-5 rounded-2xl font-black uppercase tracking-widest hover:bg-emerald-500 transition-all">Publish Case Study</button>
                        </div>
                    </form>
                </div>

                <!-- List -->
                <div class="space-y-6">
                    <?php foreach ($cases as $c): ?>
                        <div class="glass p-8 rounded-3xl">
                            <form method="POST">
                                <input type="hidden" name="update_case" value="1">
                                <input type="hidden" name="case_id" value="<?php echo $c['id']; ?>">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                                    <div>
                                        <label class="block text-[8px] font-bold text-zinc-500 uppercase tracking-widest mb-1">Title</label>
                                        <input type="text" name="title" value="<?php echo htmlspecialchars($c['title']); ?>" class="w-full bg-black/50 border border-white/5 p-3 rounded-xl text-sm outline-none focus:border-emerald-500">
                                    </div>
                                    <div>
                                        <label class="block text-[8px] font-bold text-zinc-500 uppercase tracking-widest mb-1">Category</label>
                                        <input type="text" name="category" value="<?php echo htmlspecialchars($c['category']); ?>" class="w-full bg-black/50 border border-white/5 p-3 rounded-xl text-sm outline-none focus:border-emerald-500">
                                    </div>
                                    <div class="md:col-span-2">
                                        <label class="block text-[8px] font-bold text-zinc-500 uppercase tracking-widest mb-1">Summary</label>
                                        <textarea name="summary" rows="2" class="w-full bg-black/50 border border-white/5 p-3 rounded-xl text-sm outline-none focus:border-emerald-500"><?php echo htmlspecialchars($c['summary']); ?></textarea>
                                    </div>
                                    <div>
                                        <label class="block text-[8px] font-bold text-zinc-500 uppercase tracking-widest mb-1">Approach</label>
                                        <textarea name="approach" rows="4" class="w-full bg-black/50 border border-white/5 p-3 rounded-xl text-sm outline-none focus:border-emerald-500"><?php echo htmlspecialchars($c['approach']); ?></textarea>
                                    </div>
                                    <div>
                                        <label class="block text-[8px] font-bold text-zinc-500 uppercase tracking-widest mb-1">Results</label>
                                        <textarea name="results" rows="4" class="w-full bg-black/50 border border-white/5 p-3 rounded-xl text-sm outline-none focus:border-emerald-500"><?php echo htmlspecialchars($c['results']); ?></textarea>
                                    </div>
                                    <div class="flex items-end gap-4">
                                        <div class="flex-1">
                                            <label class="block text-[8px] font-bold text-zinc-500 uppercase tracking-widest mb-1">Order</label>
                                            <input type="number" name="display_order" value="<?php echo $c['display_order']; ?>" class="w-full bg-black/50 border border-white/5 p-3 rounded-xl text-sm outline-none">
                                        </div>
                                        <button type="submit" class="bg-white text-black px-8 py-3 rounded-xl font-bold uppercase text-[10px] tracking-widest hover:bg-emerald-500 hover:text-white transition-all">Update</button>
                                        <button type="submit" name="delete_case" value="1" onclick="return confirm('Delete this case study?')" class="bg-red-500/10 border border-red-500/20 text-red-500 px-4 py-3 rounded-xl hover:bg-red-500 hover:text-white">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
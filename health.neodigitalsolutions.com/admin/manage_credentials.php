<?php
require_once '../includes/config.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_cred'])) {
        $stmt = $pdo->prepare("INSERT INTO credentials (title, institution, year, type, display_order) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([
            $_POST['title'],
            $_POST['institution'],
            $_POST['year'],
            $_POST['type'],
            (int)$_POST['display_order']
        ]);
        $message = "Credential added successfully!";
    } elseif (isset($_POST['update_cred'])) {
        $stmt = $pdo->prepare("UPDATE credentials SET title = ?, institution = ?, year = ?, type = ?, display_order = ? WHERE id = ?");
        $stmt->execute([
            $_POST['title'],
            $_POST['institution'],
            $_POST['year'],
            $_POST['type'],
            (int)$_POST['display_order'],
            $_POST['cred_id']
        ]);
        $message = "Credential updated!";
    } elseif (isset($_POST['delete_cred'])) {
        $stmt = $pdo->prepare("DELETE FROM credentials WHERE id = ?");
        $stmt->execute([$_POST['cred_id']]);
        $message = "Credential removed.";
    }
}

$credentials = $pdo->query("SELECT * FROM credentials ORDER BY display_order ASC, id DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Credentials - Admin</title>
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
                <a href="manage_case_studies.php" class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-white/5 transition-all text-zinc-400 hover:text-white">Case Studies</a>
                <a href="manage_credentials.php" class="flex items-center gap-3 px-4 py-3 rounded-xl bg-emerald-500/10 text-emerald-500 border border-emerald-500/20 font-medium">Credentials</a>
                <a href="../logout.php" class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-red-500/10 transition-all text-zinc-500 hover:text-red-500">Logout</a>
            </nav>
        </aside>

        <main class="flex-1 p-8 lg:p-12">
            <div class="max-w-6xl mx-auto">
                <div class="flex justify-between items-center mb-12">
                    <div>
                        <h1 class="text-4xl font-black uppercase tracking-tighter text-emerald-500">Professional Credentials</h1>
                        <p class="text-zinc-500 text-sm mt-1">Manage licenses and scientific research papers.</p>
                    </div>
                </div>

                <?php if ($message): ?>
                    <div class="bg-emerald-500/10 border border-emerald-500/50 p-4 rounded-xl mb-8 text-emerald-500 font-bold text-sm uppercase tracking-widest text-center"><?php echo $message; ?></div>
                <?php endif; ?>

                <div class="glass p-8 rounded-3xl mb-12 shadow-2xl">
                    <h2 class="text-xl font-bold mb-8 uppercase tracking-tight text-white">Add New Credential</h2>
                    <form method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <input type="hidden" name="add_cred" value="1">
                        <div class="md:col-span-1">
                            <label class="block text-[10px] font-bold text-zinc-500 uppercase tracking-widest mb-2">Title</label>
                            <input type="text" name="title" required class="w-full bg-black border border-white/10 p-4 rounded-xl outline-none focus:border-emerald-500">
                        </div>
                        <div class="md:col-span-1">
                            <label class="block text-[10px] font-bold text-zinc-500 uppercase tracking-widest mb-2">Institution</label>
                            <input type="text" name="institution" required class="w-full bg-black border border-white/10 p-4 rounded-xl outline-none focus:border-emerald-500">
                        </div>
                        <div class="md:col-span-1">
                            <label class="block text-[10px] font-bold text-zinc-500 uppercase tracking-widest mb-2">Year / Date</label>
                            <input type="text" name="year" placeholder="e.g. 2024 - Present" class="w-full bg-black border border-white/10 p-4 rounded-xl outline-none focus:border-emerald-500">
                        </div>
                        <div class="md:col-span-1">
                            <label class="block text-[10px] font-bold text-zinc-500 uppercase tracking-widest mb-2">Type</label>
                            <select name="type" class="w-full bg-black border border-white/10 p-4 rounded-xl outline-none focus:border-emerald-500">
                                <option value="license">Professional License</option>
                                <option value="research">Scientific Research (MSc)</option>
                            </select>
                        </div>
                        <div class="md:col-span-1">
                            <label class="block text-[10px] font-bold text-zinc-500 uppercase tracking-widest mb-2">Order</label>
                            <input type="number" name="display_order" value="0" class="w-full bg-black border border-white/10 p-4 rounded-xl outline-none focus:border-emerald-500">
                        </div>
                        <div class="md:col-span-2">
                            <button type="submit" class="w-full bg-emerald-600 py-5 rounded-2xl font-black uppercase tracking-widest hover:bg-emerald-500 transition-all">Add Credential</button>
                        </div>
                    </form>
                </div>

                <div class="space-y-6">
                    <?php foreach ($credentials as $cred): ?>
                        <div class="glass p-8 rounded-3xl">
                            <form method="POST" class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <input type="hidden" name="update_cred" value="1">
                                <input type="hidden" name="cred_id" value="<?php echo $cred['id']; ?>">
                                <div class="md:col-span-1">
                                    <label class="block text-[8px] font-bold text-zinc-500 uppercase tracking-widest mb-1">Title</label>
                                    <input type="text" name="title" value="<?php echo htmlspecialchars($cred['title']); ?>" class="w-full bg-black/50 border border-white/5 p-3 rounded-xl text-sm outline-none focus:border-emerald-500">
                                </div>
                                <div class="md:col-span-1">
                                    <label class="block text-[8px] font-bold text-zinc-500 uppercase tracking-widest mb-1">Institution</label>
                                    <input type="text" name="institution" value="<?php echo htmlspecialchars($cred['institution']); ?>" class="w-full bg-black/50 border border-white/5 p-3 rounded-xl text-sm outline-none focus:border-emerald-500">
                                </div>
                                <div class="md:col-span-1 flex gap-4">
                                    <div class="flex-1">
                                        <label class="block text-[8px] font-bold text-zinc-500 uppercase tracking-widest mb-1">Year</label>
                                        <input type="text" name="year" value="<?php echo htmlspecialchars($cred['year']); ?>" class="w-full bg-black/50 border border-white/5 p-3 rounded-xl text-sm outline-none focus:border-emerald-500">
                                    </div>
                                    <div class="w-24">
                                        <label class="block text-[8px] font-bold text-zinc-500 uppercase tracking-widest mb-1">Order</label>
                                        <input type="number" name="display_order" value="<?php echo $cred['display_order']; ?>" class="w-full bg-black/50 border border-white/5 p-3 rounded-xl text-sm outline-none focus:border-emerald-500">
                                    </div>
                                </div>
                                <div class="md:col-span-3 flex justify-between items-center pt-4 border-t border-white/5">
                                    <div class="flex gap-4">
                                        <select name="type" class="bg-black/50 border border-white/5 p-3 rounded-xl text-xs uppercase font-bold tracking-widest text-zinc-400 outline-none focus:border-emerald-500">
                                            <option value="license" <?php echo $cred['type'] === 'license' ? 'selected' : ''; ?>>License</option>
                                            <option value="research" <?php echo $cred['type'] === 'research' ? 'selected' : ''; ?>>Research</option>
                                        </select>
                                        <button type="submit" class="bg-white text-black px-6 py-3 rounded-xl font-bold uppercase text-[10px] tracking-widest hover:bg-emerald-500 hover:text-white transition-all">Update</button>
                                    </div>
                                    <button type="submit" name="delete_cred" value="1" onclick="return confirm('Delete this credential?')" class="bg-red-500/10 text-red-500 p-3 rounded-xl hover:bg-red-500 hover:text-white transition-all">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
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
<?php
require_once '../includes/config.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_option'])) {
        $stmt = $pdo->prepare("INSERT INTO payment_options (name, account_number, account_holder, display_order) VALUES (?, ?, ?, ?)");
        $stmt->execute([$_POST['name'], $_POST['account_number'], $_POST['account_holder'], (int)$_POST['display_order']]);
        $message = "Payment option added!";
    } elseif (isset($_POST['update_option'])) {
        $stmt = $pdo->prepare("UPDATE payment_options SET name = ?, account_number = ?, account_holder = ?, display_order = ?, is_active = ? WHERE id = ?");
        $stmt->execute([
            $_POST['name'], 
            $_POST['account_number'], 
            $_POST['account_holder'], 
            (int)$_POST['display_order'], 
            isset($_POST['is_active']) ? 1 : 0,
            $_POST['option_id']
        ]);
        $message = "Payment option updated!";
    } elseif (isset($_POST['delete_option'])) {
        $stmt = $pdo->prepare("DELETE FROM payment_options WHERE id = ?");
        $stmt->execute([$_POST['option_id']]);
        $message = "Payment option deleted!";
    }
}

$options = $pdo->query("SELECT * FROM payment_options ORDER BY display_order ASC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Payment Options - Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .glass { background: rgba(24, 24, 27, 0.7); backdrop-filter: blur(12px); border: 1px solid rgba(255, 255, 255, 0.1); }
    </style>
</head>
<body class="bg-[#09090b] text-white p-8">
    <div class="max-w-5xl mx-auto">
        <div class="flex justify-between items-center mb-12">
            <div>
                <h1 class="text-4xl font-black uppercase tracking-tighter text-emerald-500">Payment Options</h1>
                <p class="text-zinc-500 text-sm mt-1">Manage multiple bank and mobile money deposit options.</p>
            </div>
            <a href="index.php" class="glass px-6 py-3 rounded-xl text-xs font-bold uppercase tracking-widest hover:bg-white/5 transition-all">Back to Dashboard</a>
        </div>

        <?php if ($message): ?>
            <div class="bg-emerald-500/10 border border-emerald-500/50 p-4 rounded-xl mb-8 text-emerald-500 font-bold text-sm uppercase tracking-widest text-center"><?php echo $message; ?></div>
        <?php endif; ?>

        <!-- Add Section -->
        <div class="glass p-8 rounded-3xl mb-12 shadow-2xl">
            <h2 class="text-xl font-bold mb-8 uppercase tracking-tight text-white flex items-center gap-3">
                <span class="w-8 h-8 rounded-full bg-emerald-600 flex items-center justify-center text-xs">+</span>
                Add New Option
            </h2>
            <form method="POST" class="grid md:grid-cols-4 gap-6">
                <input type="hidden" name="add_option" value="1">
                <div class="md:col-span-1">
                    <label class="block text-[10px] font-bold text-zinc-500 uppercase tracking-widest mb-2">Service Name</label>
                    <input type="text" name="name" required placeholder="e.g. TeleBirr" class="w-full bg-black border border-white/10 p-4 rounded-xl focus:border-emerald-500 outline-none transition-all">
                </div>
                <div class="md:col-span-1">
                    <label class="block text-[10px] font-bold text-zinc-500 uppercase tracking-widest mb-2">Account # / Phone</label>
                    <input type="text" name="account_number" required placeholder="09..." class="w-full bg-black border border-white/10 p-4 rounded-xl focus:border-emerald-500 outline-none transition-all">
                </div>
                <div class="md:col-span-1">
                    <label class="block text-[10px] font-bold text-zinc-500 uppercase tracking-widest mb-2">Holder Name</label>
                    <input type="text" name="account_holder" required placeholder="Eleni Mekuria" class="w-full bg-black border border-white/10 p-4 rounded-xl focus:border-emerald-500 outline-none transition-all">
                </div>
                <div class="md:col-span-1">
                    <label class="block text-[10px] font-bold text-zinc-500 uppercase tracking-widest mb-2">Order</label>
                    <input type="number" name="display_order" value="0" class="w-full bg-black border border-white/10 p-4 rounded-xl focus:border-emerald-500 outline-none transition-all">
                </div>
                <div class="md:col-span-4">
                    <button type="submit" class="w-full bg-emerald-600 py-5 rounded-2xl font-black uppercase tracking-[0.2em] hover:bg-emerald-500 transition-all">Add Payment Method</button>
                </div>
            </form>
        </div>

        <div class="grid gap-6">
            <?php foreach ($options as $opt): ?>
                <div class="glass p-6 rounded-2xl border border-white/10 flex items-center gap-6">
                    <form method="POST" class="flex-1 grid grid-cols-1 md:grid-cols-5 gap-4 items-end">
                        <input type="hidden" name="update_option" value="1">
                        <input type="hidden" name="option_id" value="<?php echo $opt['id']; ?>">
                        <div>
                            <label class="block text-[8px] font-bold text-zinc-500 uppercase tracking-widest mb-1">Name</label>
                            <input type="text" name="name" value="<?php echo htmlspecialchars($opt['name']); ?>" class="w-full bg-black/50 border border-white/5 p-3 rounded-xl text-sm focus:border-emerald-500 outline-none">
                        </div>
                        <div>
                            <label class="block text-[8px] font-bold text-zinc-500 uppercase tracking-widest mb-1">Account</label>
                            <input type="text" name="account_number" value="<?php echo htmlspecialchars($opt['account_number']); ?>" class="w-full bg-black/50 border border-white/5 p-3 rounded-xl text-sm focus:border-emerald-500 outline-none">
                        </div>
                        <div>
                            <label class="block text-[8px] font-bold text-zinc-500 uppercase tracking-widest mb-1">Holder</label>
                            <input type="text" name="account_holder" value="<?php echo htmlspecialchars($opt['account_holder']); ?>" class="w-full bg-black/50 border border-white/5 p-3 rounded-xl text-sm focus:border-emerald-500 outline-none">
                        </div>
                        <div class="flex gap-4">
                            <div class="flex-1">
                                <label class="block text-[8px] font-bold text-zinc-500 uppercase tracking-widest mb-1">Order</label>
                                <input type="number" name="display_order" value="<?php echo $opt['display_order']; ?>" class="w-full bg-black/50 border border-white/5 p-3 rounded-xl text-sm focus:border-emerald-500 outline-none">
                            </div>
                            <div class="flex flex-col items-center justify-center">
                                <label class="block text-[8px] font-bold text-zinc-500 uppercase tracking-widest mb-1">Active</label>
                                <input type="checkbox" name="is_active" <?php echo $opt['is_active'] ? 'checked' : ''; ?> class="w-6 h-6 accent-emerald-500">
                            </div>
                        </div>
                        <div class="flex gap-2">
                            <button type="submit" class="flex-1 bg-white/5 border border-white/10 py-3 rounded-xl text-[10px] font-bold uppercase tracking-widest hover:bg-emerald-600 hover:text-white transition-all">Update</button>
                        </form>
                        <form method="POST" onsubmit="return confirm('Delete this option?')" class="flex">
                            <input type="hidden" name="delete_option" value="1">
                            <input type="hidden" name="option_id" value="<?php echo $opt['id']; ?>">
                            <button type="submit" class="px-4 bg-red-500/10 border border-red-500/20 text-red-500 rounded-xl hover:bg-red-500 hover:text-white transition-all">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</body>
</html>
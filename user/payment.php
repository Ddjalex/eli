<?php
require_once '../includes/config.php';
if (!isset($_SESSION['user_id'])) exit;

$message = '';
if (isset($_GET['reason']) && $_GET['reason'] === 'unauthorized') {
    $message = "Your access is currently locked. Please upload your receipt for manual verification.";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['receipt'])) {
    $upload_dir = '../uploads/receipts/';
    if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
    
    $filename = time() . '_' . basename($_FILES['receipt']['name']);
    $target = $upload_dir . $filename;

    if (move_uploaded_file($_FILES['receipt']['tmp_name'], $target)) {
        $stmt = $pdo->prepare("INSERT INTO payments (user_id, receipt_path) VALUES (?, ?)");
        $stmt->execute([$_SESSION['user_id'], $target]);
        header("Location: dashboard.php?uploaded=1");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Secure Verification | Eleni Mekuria</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-black text-white p-6 md:p-24 flex items-center justify-center min-h-screen">
    <div class="max-w-xl w-full bg-zinc-900 border border-white/10 p-10 rounded-3xl shadow-2xl relative overflow-hidden">
        <div class="absolute top-0 left-0 w-full h-1 bg-emerald-500"></div>
        <div class="mb-10">
            <h2 class="text-3xl font-black uppercase tracking-tighter mb-2">Secure <span class="text-emerald-500">Vault</span> Access</h2>
            <p class="text-zinc-400 text-sm">To unlock your premium meal plans, please complete the bank transfer and upload your receipt below.</p>
        </div>

        <?php if ($message): ?>
            <div class="bg-emerald-500/10 border border-emerald-500/30 p-4 rounded-xl mb-8 text-emerald-500 text-xs font-bold uppercase tracking-widest text-center">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <div class="mb-10 p-6 bg-white/5 border border-white/5 rounded-2xl">
            <h3 class="text-xs font-bold uppercase tracking-[0.3em] text-zinc-500 mb-6">Select Payment Method</h3>
            <div class="space-y-4">
                <?php
                $payment_options = $pdo->query("SELECT * FROM payment_options WHERE is_active = TRUE ORDER BY display_order ASC")->fetchAll();
                foreach ($payment_options as $index => $opt):
                ?>
                <div class="p-4 rounded-xl border border-white/5 bg-black/40 hover:border-emerald-500/50 transition-all">
                    <div class="flex justify-between items-start mb-2">
                        <span class="text-emerald-500 text-[10px] font-black uppercase tracking-widest"><?php echo htmlspecialchars($opt['name']); ?></span>
                        <span class="text-[8px] text-zinc-600 font-bold uppercase">Option #<?php echo $index + 1; ?></span>
                    </div>
                    <div class="space-y-1">
                        <div class="flex justify-between">
                            <span class="text-zinc-500 text-[9px] uppercase tracking-widest">Account</span>
                            <span class="font-bold text-xs text-white"><?php echo htmlspecialchars($opt['account_number']); ?></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-zinc-500 text-[9px] uppercase tracking-widest">Name</span>
                            <span class="font-bold text-xs text-zinc-300"><?php echo htmlspecialchars($opt['account_holder']); ?></span>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <form method="POST" enctype="multipart/form-data" class="space-y-6">
            <div>
                <label class="block text-[10px] font-black uppercase tracking-[0.3em] text-zinc-500 mb-3">Upload Receipt Screenshot</label>
                <div class="relative">
                    <input type="file" name="receipt" class="w-full bg-black border border-white/10 p-4 rounded-xl focus:border-emerald-500 transition-all text-xs" required>
                </div>
            </div>
            <button type="submit" class="w-full bg-emerald-600 text-white font-black uppercase tracking-[0.2em] py-5 rounded-xl hover:bg-emerald-500 transition-all shadow-lg shadow-emerald-900/20">Submit for Approval</button>
        </form>
        
        <p class="mt-8 text-center text-zinc-600 text-[10px] uppercase tracking-widest">Your receipt is being verified. Once approved by Eleni, your download will be unlocked.</p>
        <a href="dashboard.php" class="block text-center mt-6 text-zinc-500 hover:text-white text-[10px] uppercase font-bold tracking-widest">Back to Dashboard</a>
    </div>
</body>
</html>
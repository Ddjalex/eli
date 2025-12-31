<?php
require_once '../includes/config.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header("Location: ../login.php");
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

// Fetch meal plans from database
$stmt = $pdo->prepare("SELECT * FROM meal_plans WHERE package_type = ? OR package_type IS NULL");
$stmt->execute([$user['package']]);
$available_plans = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - Eleni Mekuria</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-black text-white p-8">
    <div class="max-w-4xl mx-auto">
        <div class="flex justify-between items-center mb-12">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-emerald-600 rounded-full flex items-center justify-center font-black text-xl">
                    <?php echo strtoupper(substr($user['name'], 0, 1)); ?>
                </div>
                <div>
                    <h1 class="text-2xl font-bold leading-none">Hello, <?php echo htmlspecialchars($user['name']); ?></h1>
                    <p class="text-xs text-zinc-500 uppercase tracking-widest mt-1"><?php echo str_replace('_', ' ', $user['package']); ?></p>
                </div>
            </div>
            <a href="logout.php" class="text-zinc-500 hover:text-white uppercase text-xs font-bold tracking-widest">Logout</a>
        </div>

        <div class="bg-zinc-900 border border-white/10 p-8 rounded-3xl mb-12 relative overflow-hidden">
            <div class="absolute top-0 right-0 w-32 h-32 bg-emerald-500/10 blur-3xl"></div>
            <h2 class="text-sm font-bold text-zinc-500 uppercase tracking-[0.3em] mb-2">Account Status</h2>
            <div class="flex items-center gap-3">
                <div class="w-3 h-3 rounded-full <?php echo $user['status'] === 'approved' ? 'bg-emerald-500 animate-pulse' : 'bg-yellow-500'; ?>"></div>
                <span class="text-2xl font-black uppercase tracking-tighter">
                    <?php echo $user['status'] === 'approved' ? 'Fully Unlocked' : 'Verification Pending'; ?>
                </span>
            </div>
            
            <?php if ($user['status'] === 'pending'): ?>
                <div class="mt-6 p-4 bg-white/5 border border-white/10 rounded-2xl">
                    <p class="text-zinc-400 text-sm mb-4">Your personalized meal plans are currently in the secure vault. Complete your verification to gain full access.</p>
                    <a href="payment.php" class="inline-block bg-white text-black px-8 py-3 rounded-xl font-bold uppercase text-xs tracking-widest hover:bg-emerald-500 hover:text-white transition-all">Submit Receipt</a>
                </div>
            <?php endif; ?>
        </div>

        <h2 class="text-xl font-bold mb-8 uppercase tracking-widest flex items-center gap-3">
            <span class="w-8 h-[1px] bg-emerald-500"></span>
            Your Exclusive Content
        </h2>
        
        <div class="grid md:grid-cols-2 gap-6">
            <?php if (empty($available_plans)): ?>
                <p class="text-zinc-500 italic">No plans assigned to your package yet.</p>
            <?php endif; ?>
            
            <?php foreach ($available_plans as $plan): ?>
                <div class="bg-zinc-900 border border-white/10 p-8 rounded-3xl group hover:border-emerald-500/50 transition-all">
                    <div class="flex justify-between items-start mb-6">
                        <div class="text-4xl">📄</div>
                        <?php if ($user['status'] === 'approved' && $plan['file_url']): ?>
                            <span class="text-[10px] bg-emerald-500/10 text-emerald-500 px-3 py-1 rounded-full font-bold uppercase border border-emerald-500/20">Ready</span>
                        <?php else: ?>
                            <span class="text-[10px] bg-zinc-800 text-zinc-500 px-3 py-1 rounded-full font-bold uppercase border border-white/5">Locked</span>
                        <?php endif; ?>
                    </div>
                    <h3 class="text-xl font-bold mb-2 uppercase tracking-tight"><?php echo htmlspecialchars($plan['title']); ?></h3>
                    <p class="text-zinc-500 text-sm mb-8">High-performance nutritional guide specifically calibrated for your goal.</p>
                    
                    <?php if ($user['status'] === 'approved'): ?>
                        <?php if ($plan['file_url']): ?>
                            <a href="../download.php?id=<?php echo $plan['id']; ?>" class="block text-center bg-emerald-600 text-white px-6 py-4 rounded-xl font-bold uppercase text-xs tracking-widest hover:bg-emerald-500 transition-all shadow-lg shadow-emerald-900/20">Secure Download</a>
                        <?php else: ?>
                            <button class="w-full bg-zinc-800 text-zinc-500 px-6 py-4 rounded-xl font-bold uppercase text-xs tracking-widest cursor-not-allowed">Preparing File</button>
                        <?php endif; ?>
                    <?php else: ?>
                        <a href="payment.php" class="block text-center border border-white/10 text-zinc-400 px-6 py-4 rounded-xl font-bold uppercase text-xs tracking-widest hover:bg-white hover:text-black transition-all">Unlock Access</a>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</body>
</html>
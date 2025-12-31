<?php
require_once '../includes/config.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header("Location: ../login.php");
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

// Handle package change
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_package'])) {
    $new_package = $_POST['new_package'];
    $stmt = $pdo->prepare("UPDATE users SET package = ? WHERE id = ?");
    $stmt->execute([$new_package, $_SESSION['user_id']]);
    $user['package'] = $new_package;
}

// Fetch meal plans from database
$stmt = $pdo->prepare("SELECT * FROM meal_plans WHERE package_type = ? OR package_type IS NULL ORDER BY id ASC");
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

        <div class="bg-zinc-900 border border-white/10 p-8 rounded-3xl mb-12">
            <h2 class="text-sm font-bold text-zinc-500 uppercase tracking-[0.3em] mb-4">Change Your Package</h2>
            <form method="POST" class="flex gap-4">
                <select name="new_package" class="flex-1 bg-black border border-white/10 p-4 rounded-xl text-white font-bold uppercase text-xs focus:border-emerald-500 outline-none">
                    <option value="weight_loss" <?php echo $user['package'] === 'weight_loss' ? 'selected' : ''; ?>>Weight Loss Plan</option>
                    <option value="muscle_gain" <?php echo $user['package'] === 'muscle_gain' ? 'selected' : ''; ?>>Muscle Gain Plan</option>
                    <option value="sports_performance" <?php echo $user['package'] === 'sports_performance' ? 'selected' : ''; ?>>Sports Performance</option>
                </select>
                <button type="submit" name="change_package" value="1" class="bg-emerald-600 text-white px-8 py-4 rounded-xl font-bold uppercase text-xs tracking-widest hover:bg-emerald-500 transition-all">Update Plan</button>
            </form>
        </div>

        <h2 class="text-xl font-bold mb-8 uppercase tracking-widest flex items-center gap-3">
            <span class="w-8 h-[1px] bg-emerald-500"></span>
            Your Health Profile
        </h2>
        <div class="grid grid-cols-3 gap-4 mb-12">
            <div class="bg-zinc-900 border border-white/10 p-4 rounded-2xl text-center">
                <span class="text-[10px] text-zinc-500 uppercase tracking-widest block mb-1">Weight</span>
                <span class="text-xl font-black text-emerald-500"><?php echo htmlspecialchars($user['weight']); ?> kg</span>
            </div>
            <div class="bg-zinc-900 border border-white/10 p-4 rounded-2xl text-center">
                <span class="text-[10px] text-zinc-500 uppercase tracking-widest block mb-1">Height</span>
                <span class="text-xl font-black text-emerald-500"><?php echo htmlspecialchars($user['height']); ?> cm</span>
            </div>
            <div class="bg-zinc-900 border border-white/10 p-4 rounded-2xl text-center">
                <span class="text-[10px] text-zinc-500 uppercase tracking-widest block mb-1">Age</span>
                <span class="text-xl font-black text-emerald-500"><?php echo htmlspecialchars($user['age']); ?></span>
            </div>
        </div>

        <h2 class="text-xl font-bold mb-8 uppercase tracking-widest flex items-center gap-3">
            <span class="w-8 h-[1px] bg-emerald-500"></span>
            Exclusive Content & Resources
        </h2>
        
        <div class="grid md:grid-cols-2 gap-6">
            <?php foreach ($available_plans as $plan): ?>
                <div class="bg-zinc-900 border border-white/10 p-8 rounded-3xl group hover:border-emerald-500/50 transition-all">
                    <div class="flex justify-between items-start mb-6">
                        <div class="text-4xl">📄</div>
                        <div class="flex gap-2">
                            <?php if (!empty($plan['preview_image'])): ?>
                                <button onclick="alert('Look Inside Feature Coming Soon!')" class="text-[10px] bg-white/10 text-white px-3 py-1 rounded-full font-bold uppercase border border-white/10">Look Inside</button>
                            <?php endif; ?>
                            <?php if ($user['status'] === 'approved' && !empty($plan['file_url'])): ?>
                                <span class="text-[10px] bg-emerald-500/10 text-emerald-500 px-3 py-1 rounded-full font-bold uppercase border border-emerald-500/20">Unlocked</span>
                            <?php else: ?>
                                <span class="text-[10px] bg-zinc-800 text-zinc-500 px-3 py-1 rounded-full font-bold uppercase border border-white/5">Locked</span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <h3 class="text-xl font-bold mb-2 uppercase tracking-tight"><?php echo htmlspecialchars($plan['title']); ?></h3>
                    
                    <?php if ($user['status'] === 'approved'): ?>
                        <a href="../download.php?id=<?php echo $plan['id']; ?>" class="mt-4 block text-center bg-emerald-600 text-white px-6 py-4 rounded-xl font-bold uppercase text-xs tracking-widest hover:bg-emerald-500 transition-all">Secure Download</a>
                    <?php else: ?>
                        <a href="payment.php" class="mt-4 block text-center border border-white/10 text-zinc-400 px-6 py-4 rounded-xl font-bold uppercase text-xs tracking-widest hover:bg-white hover:text-black transition-all">Pay to Unlock</a>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>

            <div class="bg-zinc-900 border border-white/10 p-8 rounded-3xl group hover:border-emerald-500/50 transition-all">
                <div class="text-4xl mb-6">📚</div>
                <h3 class="text-xl font-bold mb-2 uppercase tracking-tight">Nutrition Resources</h3>
                <p class="text-zinc-500 text-xs mb-6 uppercase tracking-widest">Coming Soon: Recipes & Tips</p>
                <div class="opacity-30 pointer-events-none border border-white/10 text-center py-4 rounded-xl text-[10px] font-bold uppercase">Private Library</div>
            </div>
        </div>
    </div>
</body>
</html>
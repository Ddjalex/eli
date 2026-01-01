<?php
require_once '../includes/config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header("Location: ../login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();
if (!$user) {
    session_destroy();
    header("Location: ../login.php");
    exit;
}

$dashboard_bg = 'attached_assets/stock_images/healthy_lifestyle_c_09890184.jpg';
try {
    $stmt = $pdo->prepare("SELECT value FROM site_settings WHERE key = 'user_dashboard_bg'");
    $stmt->execute();
    $dashboard_bg = $stmt->fetchColumn() ?: $dashboard_bg;
} catch (PDOException $e) {}

// Handle progress photo upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['upload_photo'])) {
    if (isset($_FILES['progress_photo']) && $_FILES['progress_photo']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = '../uploads/progress/';
        if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
        
        $file_ext = pathinfo($_FILES['progress_photo']['name'], PATHINFO_EXTENSION);
        $file_name = 'progress_' . $_SESSION['user_id'] . '_' . time() . '.' . $file_ext;
        $target_path = $upload_dir . $file_name;
        
        if (move_uploaded_file($_FILES['progress_photo']['tmp_name'], $target_path)) {
            $photo_relative_path = 'uploads/progress/' . $file_name;
            $stmt = $pdo->prepare("
                INSERT INTO user_analytics (user_id, date, photo_path)
                VALUES (?, CURRENT_DATE, ?)
                ON DUPLICATE KEY UPDATE photo_path = VALUES(photo_path)
            ");
            try {
                $stmt->execute([$_SESSION['user_id'], $photo_relative_path]);
            } catch (PDOException $e) {
                $stmt = $pdo->prepare("
                    INSERT INTO user_analytics (user_id, date, photo_path)
                    VALUES (?, CURRENT_DATE, ?)
                    ON CONFLICT (user_id, date) 
                    DO UPDATE SET photo_path = EXCLUDED.photo_path
                ");
                $stmt->execute([$_SESSION['user_id'], $photo_relative_path]);
            }
            $photo_msg = "Photo uploaded successfully!";
        } else {
            $photo_msg = "Error moving uploaded file.";
        }
    }
}

// Fetch latest analytics
$stmt = $pdo->prepare("SELECT * FROM user_analytics WHERE user_id = ? ORDER BY date DESC LIMIT 7");
$stmt->execute([$user_id]);
$analytics = $stmt->fetchAll();
$latest_stats = $analytics[0] ?? [
    'weight' => $user['weight'],
    'calories_burned' => 0,
    'water_intake' => 0,
    'steps' => 0
];

// Available meal plans based on user package
$available_plans = [];
if ($user['status'] === 'approved' || $user['status'] === 'active') {
    $stmt = $pdo->prepare("SELECT * FROM meal_plans WHERE (package_type = ? OR package_type IS NULL OR package_type = '') ORDER BY id ASC");
    $stmt->execute([$user['package']]);
    $available_plans = $stmt->fetchAll();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard | Eleni Mekuria</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .glass { background: rgba(0, 0, 0, 0.6); backdrop-filter: blur(20px); border: 1px solid rgba(255, 255, 255, 0.05); }
        .dashboard-bg {
            background: linear-gradient(rgba(0,0,0,0.7), rgba(0,0,0,0.85)), url('../<?php echo $dashboard_bg; ?>');
            background-size: cover; background-position: center; background-attachment: fixed;
        }
    </style>
</head>
<body class="dashboard-bg min-h-screen text-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <!-- Header -->
        <header class="flex flex-col md:flex-row justify-between items-start md:items-center mb-12 gap-6">
            <div>
                <h1 class="text-3xl md:text-5xl font-black uppercase tracking-tighter text-emerald-500 mb-2">Welcome Back, <?php echo htmlspecialchars($user['name'] ?? 'User'); ?></h1>
                <p class="text-zinc-400 font-medium tracking-widest uppercase text-xs">Your Health Journey Dashboard</p>
            </div>
            <div class="flex gap-4">
                <a href="logout.php" class="glass px-6 py-3 rounded-2xl text-[10px] font-bold uppercase tracking-widest hover:bg-red-500/20 hover:text-red-400 border-red-500/20 transition-all">Logout</a>
            </div>
        </header>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            <!-- Left: User Profile & Status -->
            <div class="lg:col-span-4 space-y-8">
                <!-- Progress Photo Upload -->
                <div class="glass p-8 rounded-[2.5rem] shadow-2xl">
                    <h4 class="text-xs font-bold uppercase tracking-[0.3em] text-zinc-500 mb-6">Daily Progress Photo</h4>
                    <form method="POST" enctype="multipart/form-data" class="space-y-4">
                        <div class="relative group">
                            <input type="file" name="progress_photo" accept="image/*" required class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                            <div class="bg-black/40 border border-white/5 p-8 rounded-2xl border-dashed group-hover:border-emerald-500/50 transition-all text-center">
                                <span class="text-3xl block mb-2">📸</span>
                                <p class="text-[10px] font-bold uppercase tracking-widest text-zinc-400">Click to upload photo</p>
                            </div>
                        </div>
                        <button type="submit" name="upload_photo" class="w-full bg-emerald-600 py-4 rounded-2xl text-[10px] font-black uppercase tracking-widest hover:bg-emerald-500 transition-all">Submit Photo</button>
                    </form>
                    <?php if (isset($photo_msg)): ?>
                        <p class="mt-4 text-[10px] font-bold uppercase text-emerald-500 text-center"><?php echo $photo_msg; ?></p>
                    <?php endif; ?>
                </div>

                <div class="glass p-8 rounded-[2.5rem] shadow-2xl">
                    <div class="flex items-center gap-6 mb-8">
                        <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-3xl bg-emerald-500/20 flex items-center justify-center text-3xl sm:text-4xl border border-emerald-500/30">
                            <?php echo strtoupper(substr($user['name'], 0, 1)); ?>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold uppercase tracking-tight"><?php echo htmlspecialchars($user['name']); ?></h3>
                            <span class="inline-block mt-2 px-4 py-1 rounded-full text-[10px] font-bold uppercase tracking-widest bg-emerald-500/10 text-emerald-500 border border-emerald-500/20">
                                <?php echo htmlspecialchars(str_replace('_', ' ', $user['package'] ?? 'No Package')); ?>
                            </span>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div class="bg-white/5 p-4 rounded-2xl border border-white/5">
                            <p class="text-[9px] uppercase tracking-[0.2em] text-zinc-500 font-bold mb-1">Current Weight</p>
                            <p class="text-xl font-black text-white"><?php echo number_format($latest_stats['weight'], 1); ?> <span class="text-[10px] font-medium text-zinc-500">kg</span></p>
                        </div>
                        <div class="bg-white/5 p-4 rounded-2xl border border-white/5">
                            <p class="text-[9px] uppercase tracking-[0.2em] text-zinc-500 font-bold mb-1">Goal</p>
                            <p class="text-sm font-bold text-emerald-400 uppercase tracking-tight"><?php echo htmlspecialchars($user['goal'] ?? 'Maintain'); ?></p>
                        </div>
                    </div>
                </div>

                <!-- Daily Activity -->
                <div class="glass p-8 rounded-[2.5rem] shadow-2xl">
                    <h4 class="text-xs font-bold uppercase tracking-[0.3em] text-zinc-500 mb-6">Daily Activity</h4>
                    <div class="space-y-6">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <span class="text-xl">🔥</span>
                                <div>
                                    <p class="text-[9px] uppercase font-bold text-zinc-500">Calories</p>
                                    <p class="text-sm font-black text-white"><?php echo $latest_stats['calories_burned']; ?> kcal</p>
                                </div>
                            </div>
                            <div class="text-[9px] font-bold text-emerald-500">Goal: 500</div>
                        </div>
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <span class="text-xl">💧</span>
                                <div>
                                    <p class="text-[9px] uppercase font-bold text-zinc-500">Water</p>
                                    <p class="text-sm font-black text-white"><?php echo $latest_stats['water_intake']; ?> L</p>
                                </div>
                            </div>
                            <div class="text-[9px] font-bold text-emerald-500">Goal: 3.0</div>
                        </div>
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <span class="text-xl">👣</span>
                                <div>
                                    <p class="text-[9px] uppercase font-bold text-zinc-500">Steps</p>
                                    <p class="text-sm font-black text-white"><?php echo number_format($latest_stats['steps']); ?></p>
                                </div>
                            </div>
                            <div class="text-[9px] font-bold text-emerald-500">Goal: 10k</div>
                        </div>
                    </div>
                </div>

                <!-- Account Status -->
                <div class="glass p-8 rounded-[2.5rem] shadow-2xl">
                    <h4 class="text-xs font-bold uppercase tracking-[0.3em] text-zinc-500 mb-6">Subscription Status</h4>
                    <div class="flex items-center justify-between p-4 rounded-2xl <?php echo ($user['status'] === 'approved' || $user['status'] === 'active') ? 'bg-emerald-500/10 border-emerald-500/20' : 'bg-amber-500/10 border-amber-500/20'; ?> border">
                        <span class="text-xs font-bold uppercase tracking-widest"><?php echo ($user['status'] === 'approved' || $user['status'] === 'active') ? 'Active' : 'Pending Approval'; ?></span>
                        <span class="w-3 h-3 rounded-full <?php echo ($user['status'] === 'approved' || $user['status'] === 'active') ? 'bg-emerald-500' : 'bg-amber-500'; ?> animate-pulse"></span>
                    </div>
                </div>
            </div>

            <!-- Middle/Right: Charts & Meal Plans -->
            <div class="lg:col-span-8 space-y-8">
                <!-- Weight Progress Chart -->
                <div class="glass p-8 rounded-[3rem] shadow-2xl">
                    <h4 class="text-xs font-bold uppercase tracking-[0.3em] text-zinc-500 mb-6">Weight Progress (Last 7 Days)</h4>
                    <div class="h-[250px]">
                        <canvas id="weightChart"></canvas>
                    </div>
                </div>

                <div class="glass p-10 rounded-[3rem] shadow-2xl relative overflow-hidden">
                    <div class="absolute top-0 right-0 p-8">
                        <span class="text-6xl opacity-10">🥗</span>
                    </div>
                    
                    <h2 class="text-3xl font-black uppercase tracking-tighter mb-8">My Custom <span class="text-emerald-500">Meal Plan</span></h2>

                    <?php if ($user['status'] === 'approved' || $user['status'] === 'active'): ?>
                        <div class="grid sm:grid-cols-2 gap-6">
                            <?php foreach ($available_plans as $plan): ?>
                                <div class="bg-white/5 p-6 rounded-[2rem] border border-white/10 flex flex-col group hover:bg-white/[0.07] transition-all duration-500 shadow-xl">
                                    <div class="px-2">
                                        <h3 class="text-xl font-black uppercase tracking-tighter mb-4 text-white group-hover:text-emerald-400 transition-colors"><?php echo htmlspecialchars($plan['title']); ?></h3>
                                        
                                        <div class="aspect-square rounded-2xl overflow-hidden border border-white/10 mb-6 shadow-inner relative">
                                            <?php if (!empty($plan['preview_image'])): ?>
                                                <img src="../<?php echo $plan['preview_image']; ?>" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                                            <?php else: ?>
                                                <div class="w-full h-full bg-zinc-900 flex items-center justify-center text-zinc-700 font-bold uppercase tracking-widest text-[10px]">No Preview Available</div>
                                            <?php endif; ?>
                                        </div>

                                        <a href="../preview.php?id=<?php echo $plan['id']; ?>" class="inline-flex w-full items-center justify-center gap-3 bg-emerald-600 text-white py-5 rounded-2xl text-[11px] font-black uppercase tracking-[0.2em] hover:bg-emerald-500 hover:shadow-[0_0_30px_rgba(16,185,129,0.3)] transition-all active:scale-95">
                                            <span>View Full Plan</span>
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        </a>
                                    </div>
                                </div>
                                
                                <?php if (!empty($plan['video_url'])): ?>
                                    <div class="mt-6 rounded-2xl overflow-hidden border border-white/10 shadow-2xl bg-black ring-1 ring-white/5 w-full aspect-video">
                                        <iframe class="w-full h-full" src="<?php echo htmlspecialchars($plan['video_url']); ?>" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                                    </div>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-24 bg-black/40 rounded-[2.5rem] border border-white/5 backdrop-blur-3xl relative group">
                            <div class="mb-8">
                                <span class="text-6xl filter grayscale group-hover:grayscale-0 transition-all duration-700">🔒</span>
                            </div>
                            <h3 class="text-xl font-bold uppercase tracking-widest text-white mb-3">Content Locked</h3>
                            <p class="max-w-md mx-auto text-zinc-500 text-sm leading-relaxed mb-10">Complete your payment and upload your receipt to unlock your premium nutritional guide.</p>
                            <div class="flex justify-center">
                                <a href="payment.php" class="bg-emerald-600 text-white px-10 py-4 rounded-2xl text-[10px] font-black uppercase tracking-widest hover:bg-emerald-500 transition-all shadow-lg shadow-emerald-900/40">Unlock Now</a>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <?php include '../includes/footer.php'; ?>

    <script>
        const analyticsData = <?php echo json_encode(array_reverse($analytics)); ?>;
        const labels = analyticsData.map(d => d.date);
        const weightData = analyticsData.map(d => d.weight);

        const ctx = document.getElementById('weightChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Weight (kg)',
                    data: weightData,
                    borderColor: '#10b981',
                    backgroundColor: 'rgba(16, 185, 129, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: '#10b981',
                    pointRadius: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { grid: { color: 'rgba(255, 255, 255, 0.05)' }, ticks: { color: '#71717a' } },
                    x: { grid: { display: false }, ticks: { color: '#71717a' } }
                }
            }
        });
    </script>
</body>
</html>
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

    // Fetch all active packages for update
    $all_plans = $pdo->query("SELECT * FROM meal_plans ORDER BY id ASC")->fetchAll();

    // Fetch the current plan details
    $stmt = $pdo->prepare("SELECT * FROM meal_plans WHERE package_type = ?");
    $stmt->execute([$user['package']]);
    $current_plan = $stmt->fetch();

    // Fetch available plans for approved users
    $stmt = $pdo->prepare("SELECT * FROM meal_plans WHERE package_type = ?");
    $stmt->execute([$user['package']]);
    $available_plans = $stmt->fetchAll();

    // Check for approved plans using the new dedicated table
    $stmt = $pdo->prepare("
        SELECT mp.id as plan_id, mp.package_type 
        FROM user_plan_access upa 
        JOIN meal_plans mp ON upa.meal_plan_id = mp.id 
        WHERE upa.user_id = ? AND upa.status = 'approved'
    ");
    $paid_packages = [];
    $paid_plan_ids = [];
    try {
        $stmt->execute([$user_id]);
        while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $paid_packages[] = $row['package_type'];
            $paid_plan_ids[] = (int)$row['plan_id'];
        }
    } catch (Exception $e) {}
    
    // Check for pending plans using the new dedicated table
    $stmt = $pdo->prepare("
        SELECT mp.id as plan_id, mp.package_type 
        FROM user_plan_access upa 
        JOIN meal_plans mp ON upa.meal_plan_id = mp.id 
        WHERE upa.user_id = ? AND upa.status = 'pending'
    ");
    $pending_packages = [];
    $pending_plan_ids = [];
    try {
        $stmt->execute([$user_id]);
        while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $pending_packages[] = $row['package_type'];
            $pending_plan_ids[] = (int)$row['plan_id'];
        }
    } catch (Exception $e) {}

    // CRITICAL: Ensure the user's primary package is treated as "paid" if they are approved/active
    if (($user['status'] === 'approved' || $user['status'] === 'active') && !empty($user['package'])) {
        $paid_packages[] = $user['package'];
        // Also find the ID for the primary package to unlock it by ID
        $stmt = $pdo->prepare("SELECT id FROM meal_plans WHERE package_type = ?");
        $stmt->execute([$user['package']]);
        $primary_id = $stmt->fetchColumn();
        if ($primary_id) $paid_plan_ids[] = (int)$primary_id;
    }

    $paid_packages = array_unique($paid_packages);
    $paid_plan_ids = array_unique($paid_plan_ids);
    $pending_plan_ids = array_unique($pending_plan_ids);
    
    // Explicitly define this to prevent warnings
    $has_any_pending = !empty($pending_plan_ids) || ($user['status'] ?? '') === 'pending';
    
    // Fallback for dashboard background if not set
    if (!isset($dashboard_bg)) {
        $dashboard_bg = 'attached_assets/stock_images/healthy_lifestyle_c_09890184.jpg';
    }

    // Final debug/verification: Ensure IDs are checked correctly
    // (int) casting ensures in_array works with numeric IDs


    // Handle package update request
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_user_package'])) {
        $new_package = $_POST['new_package'];
        
        // Check if user already has access to this package
        if (in_array($new_package, $paid_packages)) {
            // User already paid for this! Just switch the active package
            $stmt = $pdo->prepare("UPDATE users SET package = ?, status = 'active' WHERE id = ?");
            $stmt->execute([$new_package, $user_id]);
            header("Location: dashboard.php?package_switched=1");
        } else {
            // New package, needs approval
            $stmt = $pdo->prepare("UPDATE users SET package = ?, status = 'pending' WHERE id = ?");
            $stmt->execute([$new_package, $user_id]);
            header("Location: dashboard.php?package_updated=1");
        }
        exit;
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
                    <?php 
                    $is_active = ($user['status'] === 'approved' || $user['status'] === 'active');
                    $display_status = $is_active ? 'Active' : ($has_any_pending ? 'Pending Approval' : 'Payment Required');
                    $status_color = $is_active ? 'bg-emerald-500' : 'bg-amber-500';
                    $bg_color = $is_active ? 'bg-emerald-500/10 border-emerald-500/20' : 'bg-amber-500/10 border-amber-500/20';
                    ?>
                    <div class="flex items-center justify-between p-4 rounded-2xl <?php echo $bg_color; ?> border">
                        <span class="text-xs font-bold uppercase tracking-widest"><?php echo $display_status; ?></span>
                        <span class="w-3 h-3 rounded-full <?php echo $status_color; ?> animate-pulse"></span>
                    </div>
                </div>

                <!-- Update Package -->
                <div class="glass p-8 rounded-[2.5rem] shadow-2xl">
                    <h4 class="text-xs font-bold uppercase tracking-[0.3em] text-zinc-500 mb-6">Change Plan</h4>
                    <?php if (isset($_GET['package_updated'])): ?>
                        <p class="text-[10px] text-emerald-500 font-bold uppercase mb-4">Request sent! Awaiting approval.</p>
                    <?php elseif (isset($_GET['package_switched'])): ?>
                        <p class="text-[10px] text-emerald-500 font-bold uppercase mb-4">Plan switched! Welcome back.</p>
                    <?php endif; ?>
                    <form method="POST" class="space-y-4">
                        <input type="hidden" name="update_user_package" value="1">
                        <select name="new_package" id="packageSelect" onchange="updatePlanDescription()" class="w-full bg-black/50 border border-white/10 p-4 rounded-xl text-[10px] font-bold uppercase text-white outline-none focus:border-emerald-500">
                            <?php foreach ($all_plans as $p): ?>
                                <option value="<?php echo htmlspecialchars($p['package_type']); ?>" 
                                        data-description="<?php echo htmlspecialchars($p['description'] ?? ''); ?>"
                                        <?php echo $user['package'] === $p['package_type'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($p['title']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <div id="planDescriptionBox" class="bg-emerald-500/5 border border-emerald-500/10 p-4 rounded-xl hidden">
                            <p id="planDescriptionText" class="text-[9px] text-zinc-400 leading-relaxed italic"></p>
                        </div>
                        <button type="submit" class="w-full bg-zinc-800 py-4 rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-emerald-600 transition-all">Update Plan</button>
                    </form>
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

                    <?php 
                    $is_approved = ($user['status'] === 'approved' || $user['status'] === 'active');
                    if ($is_approved): ?>
                        <div class="grid sm:grid-cols-2 gap-6">
                            <?php 
                            // Show all plans, but lock those not paid for
                            foreach ($all_plans as $plan): 
                                $has_access = in_array((int)$plan['id'], $paid_plan_ids, true);
                                $is_pending = in_array((int)$plan['id'], $pending_plan_ids, true);
                            ?>
                                <div class="bg-white/5 p-6 rounded-[2rem] border border-white/10 flex flex-col group hover:bg-white/[0.07] transition-all duration-500 shadow-xl relative">
                                    <?php if (!$has_access): ?>
                                        <div class="absolute inset-0 z-20 bg-black/60 backdrop-blur-[2px] rounded-[2rem] flex flex-col items-center justify-center p-6 text-center">
                                            <span class="text-3xl mb-3"><?php echo $is_pending ? '⏳' : '🔒'; ?></span>
                                            <p class="text-[10px] font-bold uppercase tracking-widest text-white mb-4">
                                                <?php echo $is_pending ? 'Awaiting Approval' : 'Payment Required'; ?>
                                            </p>
                                            <?php if (!$is_pending): ?>
                                                <a href="payment.php?plan_id=<?php echo $plan['id']; ?>" class="bg-emerald-600 text-white px-6 py-2 rounded-xl text-[9px] font-black uppercase tracking-widest hover:bg-emerald-500 transition-all">Unlock Plan</a>
                                            <?php else: ?>
                                                <span class="px-6 py-2 rounded-xl text-[9px] font-black uppercase tracking-widest text-emerald-500 bg-emerald-500/10 border border-emerald-500/20">Pending Verification</span>
                                            <?php endif; ?>
                                        </div>
                                    <?php endif; ?>
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
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-24 bg-black/40 rounded-[2.5rem] border border-white/5 backdrop-blur-3xl relative group">
                            <div class="mb-8">
                                <span class="text-6xl <?php echo $has_any_pending ? '' : 'filter grayscale group-hover:grayscale-0'; ?> transition-all duration-700"><?php echo $has_any_pending ? '⏳' : '🔒'; ?></span>
                            </div>
                            <h3 class="text-xl font-bold uppercase tracking-widest text-white mb-3">
                                <?php echo $has_any_pending ? 'Verification in Progress' : 'Content Locked'; ?>
                            </h3>
                            <p class="max-w-md mx-auto text-zinc-500 text-sm leading-relaxed mb-4">
                                <?php echo $has_any_pending ? 'Your receipt is being reviewed. You will have full access once Eleni approves your payment.' : 'Complete your payment and upload your receipt to unlock your premium nutritional guide.'; ?>
                            </p>
                            
                            <?php if ($current_plan): ?>
                                <div class="bg-emerald-500/10 border border-emerald-500/20 p-6 rounded-2xl mb-8 max-w-xs mx-auto">
                                    <p class="text-[10px] font-bold uppercase tracking-widest text-emerald-500 mb-1">Required Investment</p>
                                    <p class="text-3xl font-black text-white"><?php echo number_format($current_plan['price'], 2); ?> <span class="text-xs font-bold text-emerald-500">ETB</span></p>
                                    <p class="text-[9px] text-zinc-500 mt-2 uppercase font-bold"><?php echo htmlspecialchars($current_plan['title']); ?></p>
                                    <?php if (!empty($current_plan['description'])): ?>
                                        <p class="text-[10px] text-zinc-400 mt-4 leading-relaxed italic border-t border-white/5 pt-4"><?php echo htmlspecialchars($current_plan['description']); ?></p>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>

                            <div class="flex justify-center">
                                <?php if ($has_any_pending): ?>
                                    <span class="bg-amber-500/20 text-amber-500 border border-amber-500/30 px-10 py-4 rounded-2xl text-[10px] font-black uppercase tracking-widest">Awaiting Verification</span>
                                <?php else: ?>
                                    <a href="payment.php" class="bg-emerald-600 text-white px-10 py-4 rounded-2xl text-[10px] font-black uppercase tracking-widest hover:bg-emerald-500 transition-all shadow-lg shadow-emerald-900/40">Unlock Now</a>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <?php include '../includes/footer.php'; ?>

    <script>
        function updatePlanDescription() {
            const select = document.getElementById('packageSelect');
            const box = document.getElementById('planDescriptionBox');
            const text = document.getElementById('planDescriptionText');
            const selected = select.options[select.selectedIndex];
            const description = selected.getAttribute('data-description');
            
            if (description && description.trim() !== '') {
                text.textContent = description;
                box.classList.remove('hidden');
            } else {
                box.classList.add('hidden');
            }
        }

        // Initialize on load
        document.addEventListener('DOMContentLoaded', updatePlanDescription);

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
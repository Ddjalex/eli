<?php
/** @var PDO $pdo */
require_once '../includes/config.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

if (isset($_GET['approve'])) {
    $user_id = $_GET['approve'];
    
    // Check if it is a plan-specific approval
    $stmt = $pdo->prepare("SELECT meal_plan_id FROM payments WHERE user_id = ? ORDER BY created_at DESC LIMIT 1");
    $stmt->execute([$user_id]);
    $last_payment = $stmt->fetch();
    
    if ($last_payment && $last_payment['meal_plan_id']) {
        // Approve specific plan in payments
        $pdo->prepare("UPDATE payments SET status = 'approved' WHERE user_id = ? AND meal_plan_id = ?")->execute([$user_id, $last_payment['meal_plan_id']]);
        
        // Update user_plan_access table
        $pdo->prepare("INSERT INTO user_plan_access (user_id, meal_plan_id, status) VALUES (?, ?, 'approved') ON CONFLICT (user_id, meal_plan_id) DO UPDATE SET status = 'approved', updated_at = CURRENT_TIMESTAMP")->execute([$user_id, $last_payment['meal_plan_id']]);
        
        // Fetch package type
        $stmt_pkg = $pdo->prepare("SELECT package_type FROM meal_plans WHERE id = ?");
        $stmt_pkg->execute([$last_payment['meal_plan_id']]);
        $package_type = $stmt_pkg->fetchColumn();
        
        if ($package_type) {
            $pdo->prepare("UPDATE users SET package = ?, status = 'active' WHERE id = ?")->execute([$package_type, $user_id]);
        } else {
            $pdo->prepare("UPDATE users SET status = 'active' WHERE id = ?")->execute([$user_id]);
        }
    } else {
        // Global approval
        $pdo->prepare("UPDATE users SET status = 'approved' WHERE id = ?")->execute([$user_id]);
        
        // If there's an initial package assigned, grant access
        $stmt_user = $pdo->prepare("SELECT package FROM users WHERE id = ?");
        $stmt_user->execute([$user_id]);
        $upkg = $stmt_user->fetchColumn();
        if ($upkg) {
            $stmt_mp = $pdo->prepare("SELECT id FROM meal_plans WHERE package_type = ? LIMIT 1");
            $stmt_mp->execute([$upkg]);
            $mpid = $stmt_mp->fetchColumn();
            if ($mpid) {
                $pdo->prepare("INSERT INTO user_plan_access (user_id, meal_plan_id, status) VALUES (?, ?, 'approved') ON CONFLICT (user_id, meal_plan_id) DO UPDATE SET status = 'approved'")->execute([$user_id, $mpid]);
            }
        }
    }
    
    header("Location: index.php");
    exit;
}

// Statistics
$stats = [
    'users' => $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'user'")->fetchColumn(),
    'pending' => $pdo->query("SELECT COUNT(*) FROM users WHERE status = 'pending' AND role = 'user'")->fetchColumn(),
    'plans' => $pdo->query("SELECT COUNT(*) FROM meal_plans")->fetchColumn(),
];

    $stmt = $pdo->query("SELECT u.*, p.receipt_path, p.trx_number, p.meal_plan_id, p.status as payment_status, mp.title as plan_title 
    FROM users u 
    LEFT JOIN (
        SELECT p1.*
        FROM payments p1
        INNER JOIN (
            SELECT user_id, MAX(created_at) as max_created
            FROM payments
            GROUP BY user_id
        ) p2 ON p1.user_id = p2.user_id AND p1.created_at = p2.max_created
    ) p ON u.id = p.user_id 
    LEFT JOIN meal_plans mp ON p.meal_plan_id = mp.id
    WHERE u.role = 'user' 
    ORDER BY u.created_at DESC");
    $users = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel | Eleni Mekuria</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .glass { background: rgba(24, 24, 27, 0.7); backdrop-filter: blur(12px); border: 1px solid rgba(255, 255, 255, 0.1); }
        .emerald-gradient { background: linear-gradient(135deg, #10b981 0%, #059669 100%); }
    </style>
</head>
<body class="bg-[#09090b] text-zinc-200 min-h-screen">
    <div class="flex">
        <!-- Sidebar -->
        <aside class="w-64 border-r border-white/5 h-screen sticky top-0 bg-[#09090b] p-6 hidden lg:block">
            <div class="text-xl font-bold text-emerald-500 mb-10 tracking-tighter uppercase">Admin Panel</div>
            <nav class="space-y-2">
                <a href="index.php" class="flex items-center gap-3 px-4 py-3 rounded-xl bg-emerald-500/10 text-emerald-500 border border-emerald-500/20 font-medium">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                    Dashboard
                </a>
                <a href="manage_testimonials.php" class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-white/5 transition-all text-zinc-400 hover:text-white">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
                    Manage Testimonials
                </a>
                <a href="manage_users.php" class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-white/5 transition-all text-zinc-400 hover:text-white">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    Manage Users
                </a>
                <a href="manage_hero.php" class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-white/5 transition-all text-zinc-400 hover:text-white">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    Hero Slider
                </a>
                <a href="manage_plans.php" class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-white/5 transition-all text-zinc-400 hover:text-white">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                    Meal Plans
                </a>
                <a href="manage_payments.php" class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-white/5 transition-all text-zinc-400 hover:text-white">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                    Payment Methods
                </a>
                <a href="manage_blogs.php" class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-white/5 transition-all text-zinc-400 hover:text-white">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10l4 4v10a2 2 0 01-2 2zM14 4v4h4"/></svg>
                    Manage Blog
                </a>
                <a href="manage_portfolio.php" class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-white/5 transition-all text-zinc-400 hover:text-white">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                    Manage Portfolio
                </a>
                <a href="manage_progress.php" class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-white/5 transition-all text-zinc-400 hover:text-white">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    Manage Progress
                </a>
                <a href="manage_case_studies.php" class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-white/5 transition-all text-zinc-400 hover:text-white">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                    Case Studies
                </a>
                <a href="manage_credentials.php" class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-white/5 transition-all text-zinc-400 hover:text-white">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    Credentials
                </a>
                <a href="manage_images.php" class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-white/5 transition-all text-zinc-400 hover:text-white">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/></svg>
                    Site Settings
                </a>
                <a href="change_password.php" class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-white/5 transition-all text-zinc-400 hover:text-white">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                    Change Password
                </a>
                <div class="pt-10">
                    <a href="../logout.php" class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-red-500/10 transition-all text-zinc-500 hover:text-red-500">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                        Logout
                    </a>
                </div>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 p-8 lg:p-12">
            <header class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6 mb-12">
                <div>
                    <h1 class="text-4xl font-bold tracking-tight text-white mb-2">Overview</h1>
                    <p class="text-zinc-500">Welcome back, Eleni. Here's what's happening today.</p>
                </div>
                <div class="flex gap-4">
                    <a href="../" target="_blank" class="glass px-6 py-3 rounded-xl font-semibold hover:bg-white/5 transition-all flex items-center gap-2">
                        View Site
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                    </a>
                </div>
            </header>

            <!-- Stats Grid -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">
                <div class="glass p-8 rounded-3xl relative overflow-hidden group">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-emerald-500/10 blur-3xl rounded-full -mr-16 -mt-16 group-hover:bg-emerald-500/20 transition-all"></div>
                    <div class="text-zinc-500 text-sm font-semibold uppercase tracking-wider mb-4">Total Clients</div>
                    <div class="text-5xl font-bold text-white mb-2"><?php echo $stats['users']; ?></div>
                    <div class="text-emerald-500 text-sm flex items-center gap-1">
                        Active subscribers
                    </div>
                </div>
                <div class="glass p-8 rounded-3xl relative overflow-hidden group">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-yellow-500/10 blur-3xl rounded-full -mr-16 -mt-16 group-hover:bg-yellow-500/20 transition-all"></div>
                    <div class="text-zinc-500 text-sm font-semibold uppercase tracking-wider mb-4">Pending Approval</div>
                    <div class="text-5xl font-bold text-white mb-2"><?php echo $stats['pending']; ?></div>
                    <div class="text-yellow-500 text-sm flex items-center gap-1">
                        Awaiting receipt verification
                    </div>
                </div>
                <div class="glass p-8 rounded-3xl relative overflow-hidden group">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-blue-500/10 blur-3xl rounded-full -mr-16 -mt-16 group-hover:bg-blue-500/20 transition-all"></div>
                    <div class="text-zinc-500 text-sm font-semibold uppercase tracking-wider mb-4">Meal Plans</div>
                    <div class="text-5xl font-bold text-white mb-2"><?php echo $stats['plans']; ?></div>
                    <div class="text-blue-500 text-sm flex items-center gap-1">
                        Available in marketplace
                    </div>
                </div>
            </div>

            <!-- Table Section -->
            <div class="glass rounded-3xl overflow-hidden shadow-2xl">
                <div class="p-8 border-b border-white/5 flex justify-between items-center bg-white/2">
                    <h2 class="text-xl font-bold text-white">Recent Client Requests</h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="text-left text-zinc-500 text-xs uppercase tracking-widest bg-black/20">
                                <th class="px-8 py-5 font-bold">Client Information</th>
                                <th class="px-8 py-5 font-bold">Registration Date</th>
                                <th class="px-8 py-5 font-bold">Status</th>
                                <th class="px-8 py-5 font-bold">TRX Number</th>
                                <th class="px-8 py-5 font-bold">Receipt</th>
                                <th class="px-8 py-5 font-bold text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/5">
                            <?php foreach ($users as $u): ?>
                                <tr class="hover:bg-white/2 transition-all group">
                                    <td class="px-8 py-6">
                                        <div class="flex items-center gap-4">
                                            <div class="w-10 h-10 rounded-full emerald-gradient flex items-center justify-center text-white font-bold text-lg">
                                                <?php echo strtoupper(substr($u['name'], 0, 1)); ?>
                                            </div>
                                            <div>
                                                <div class="font-bold text-white group-hover:text-emerald-400 transition-colors"><?php echo htmlspecialchars($u['name']); ?></div>
                                                <div class="text-sm text-zinc-500"><?php echo htmlspecialchars($u['email']); ?></div>
                                                <?php if (isset($u['plan_title']) && $u['plan_title']): ?>
                                                    <div class="text-[10px] text-emerald-500 font-bold uppercase mt-1">Paying for: <?php echo htmlspecialchars($u['plan_title']); ?></div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-8 py-6 text-sm text-zinc-400">
                                        <?php echo date('M d, Y', strtotime($u['created_at'])); ?>
                                    </td>
                                    <td class="px-8 py-6">
                                        <?php if ($u['status'] === 'approved' || (isset($u['payment_status']) && $u['payment_status'] === 'approved')): ?>
                                            <span class="px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-widest bg-emerald-500/10 text-emerald-500 border border-emerald-500/20">
                                                Approved
                                            </span>
                                        <?php else: ?>
                                            <span class="px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-widest bg-yellow-500/10 text-yellow-500 border border-yellow-500/20 animate-pulse">
                                                Pending
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-8 py-6">
                                        <?php if ($u['trx_number']): ?>
                                            <span class="text-sm font-mono text-emerald-400 bg-emerald-500/10 px-3 py-1 rounded-lg"><?php echo htmlspecialchars($u['trx_number']); ?></span>
                                        <?php else: ?>
                                            <span class="text-zinc-600 text-sm italic">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-8 py-6">
                                        <?php if ($u['receipt_path']): ?>
                                            <a href="../<?php echo $u['receipt_path']; ?>" target="_blank" class="flex items-center gap-2 text-blue-400 hover:text-blue-300 transition-colors group/link">
                                                <svg class="w-5 h-5 group-hover/link:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                                <span class="text-sm font-medium">View</span>
                                            </a>
                                        <?php else: ?>
                                            <span class="text-zinc-600 text-sm flex items-center gap-2 italic">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/></svg>
                                                No upload
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-8 py-6 text-right flex justify-end gap-2">
                                        <a href="user_analytics.php?id=<?php echo $u['id']; ?>" class="bg-blue-600/10 text-blue-400 border border-blue-500/20 px-4 py-2 rounded-xl text-xs font-bold uppercase tracking-widest hover:bg-blue-600 hover:text-white transition-all">
                                            Stats
                                        </a>
                                        <?php 
                                        $is_approved = ($u['status'] === 'active' || $u['status'] === 'approved' || (isset($u['payment_status']) && $u['payment_status'] === 'approved'));
                                        if (!$is_approved): ?>
                                            <a href="index.php?approve=<?php echo $u['id']; ?>" class="emerald-gradient text-white px-5 py-2 rounded-xl text-xs font-bold uppercase tracking-widest shadow-lg shadow-emerald-500/20 hover:scale-105 transition-all inline-block">
                                                Approve
                                            </a>
                                        <?php else: ?>
                                            <span class="text-emerald-500 bg-emerald-500/10 p-2 rounded-lg">
                                                <svg class="w-6 h-6 ml-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
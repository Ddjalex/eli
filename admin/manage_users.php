<?php
require_once '../includes/config.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

// User Stats
$stats = [
    'total' => $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'user'")->fetchColumn(),
    'active' => $pdo->query("SELECT COUNT(*) FROM users WHERE status = 'active' AND role = 'user'")->fetchColumn(),
    'approved' => $pdo->query("SELECT COUNT(*) FROM users WHERE status = 'approved' AND role = 'user'")->fetchColumn(),
    'pending' => $pdo->query("SELECT COUNT(*) FROM users WHERE status = 'pending' AND role = 'user'")->fetchColumn(),
];

// Enhanced query to fetch user plan details
$stmt = $pdo->query("
    SELECT u.*, 
           (SELECT string_agg(mp.title, ', ') 
            FROM user_plan_access upa 
            JOIN meal_plans mp ON upa.meal_plan_id = mp.id 
            WHERE upa.user_id = u.id AND upa.status = 'approved') as paid_plans
    FROM users u 
    WHERE u.role = 'user' 
    ORDER BY u.created_at DESC
");
$users = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Guidance | Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .glass { background: rgba(24, 24, 27, 0.7); backdrop-filter: blur(12px); border: 1px solid rgba(255, 255, 255, 0.1); }
    </style>
</head>
<body class="bg-[#09090b] text-zinc-200 min-h-screen">
    <div class="flex">
        <!-- Sidebar -->
        <aside class="w-64 border-r border-white/5 h-screen sticky top-0 bg-[#09090b] p-6 hidden lg:block">
            <div class="text-xl font-bold text-emerald-500 mb-10 tracking-tighter uppercase">Admin Panel</div>
            <nav class="space-y-2">
                <a href="index.php" class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-white/5 transition-all text-zinc-400 hover:text-white">Dashboard</a>
                <a href="manage_users.php" class="flex items-center gap-3 px-4 py-3 rounded-xl bg-emerald-500/10 text-emerald-500 border border-emerald-500/20 font-medium">Manage Users</a>
                <a href="manage_hero.php" class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-white/5 transition-all text-zinc-400 hover:text-white">Hero Slider</a>
                <a href="manage_plans.php" class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-white/5 transition-all text-zinc-400 hover:text-white">Meal Plans</a>
                <a href="manage_payments.php" class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-white/5 transition-all text-zinc-400 hover:text-white">Payment Methods</a>
                <a href="manage_images.php" class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-white/5 transition-all text-zinc-400 hover:text-white">Site Settings</a>
                <a href="../logout.php" class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-red-500/10 transition-all text-zinc-500 hover:text-red-500">Logout</a>
            </nav>
        </aside>

        <main class="flex-1 p-8 lg:p-12">
            <header class="mb-12">
                <h1 class="text-4xl font-bold tracking-tight text-white mb-2">User Guidance</h1>
                <p class="text-zinc-500">Track and separate user statuses and payments.</p>
            </header>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-12">
                <div class="glass p-6 rounded-2xl border-l-4 border-zinc-500">
                    <div class="text-zinc-500 text-xs font-bold uppercase tracking-wider mb-2">Total Clients</div>
                    <div class="text-3xl font-bold text-white"><?php echo $stats['total']; ?></div>
                </div>
                <div class="glass p-6 rounded-2xl border-l-4 border-emerald-500">
                    <div class="text-emerald-500 text-xs font-bold uppercase tracking-wider mb-2">Active</div>
                    <div class="text-3xl font-bold text-white"><?php echo $stats['active']; ?></div>
                </div>
                <div class="glass p-6 rounded-2xl border-l-4 border-blue-500">
                    <div class="text-blue-500 text-xs font-bold uppercase tracking-wider mb-2">Approved</div>
                    <div class="text-3xl font-bold text-white"><?php echo $stats['approved']; ?></div>
                </div>
                <div class="glass p-6 rounded-2xl border-l-4 border-yellow-500">
                    <div class="text-yellow-500 text-xs font-bold uppercase tracking-wider mb-2">Pending</div>
                    <div class="text-3xl font-bold text-white"><?php echo $stats['pending']; ?></div>
                </div>
            </div>

            <div class="glass rounded-3xl overflow-hidden shadow-2xl">
                <table class="w-full">
                    <thead>
                        <tr class="text-left text-zinc-500 text-xs uppercase tracking-widest bg-black/20">
                            <th class="px-8 py-5 font-bold">Client Name</th>
                            <th class="px-8 py-5 font-bold">Primary Package</th>
                            <th class="px-8 py-5 font-bold">Paid Plans</th>
                            <th class="px-8 py-5 font-bold">Subscription Status</th>
                            <th class="px-8 py-5 font-bold">Joined Date</th>
                            <th class="px-8 py-5 font-bold text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        <?php foreach ($users as $u): ?>
                        <tr class="hover:bg-white/2 transition-all">
                            <td class="px-8 py-6">
                                <div class="font-bold text-white"><?php echo htmlspecialchars($u['name']); ?></div>
                                <div class="text-xs text-zinc-500"><?php echo htmlspecialchars($u['email']); ?></div>
                            </td>
                            <td class="px-8 py-6 text-sm">
                                <span class="px-3 py-1 rounded-full text-[10px] font-bold uppercase bg-white/5 border border-white/10">
                                    <?php echo htmlspecialchars(str_replace('_', ' ', $u['package'] ?: 'No Plan')); ?>
                                </span>
                            </td>
                            <td class="px-8 py-6 text-sm">
                                <?php if (!empty($u['paid_plans'])): ?>
                                    <div class="flex flex-wrap gap-1">
                                        <?php foreach (explode(', ', $u['paid_plans']) as $plan): ?>
                                            <span class="px-2 py-0.5 rounded-md text-[9px] font-bold uppercase bg-emerald-500/10 text-emerald-500 border border-emerald-500/20">
                                                <?php echo htmlspecialchars($plan); ?>
                                            </span>
                                        <?php endforeach; ?>
                                    </div>
                                <?php else: ?>
                                    <span class="text-[10px] text-zinc-600 font-bold uppercase italic">None</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-8 py-6">
                                <div class="flex items-center gap-2">
                                    <span class="w-2 h-2 rounded-full <?php echo ($u['status'] === 'active' || $u['status'] === 'approved') ? 'bg-emerald-500' : 'bg-yellow-500'; ?> animate-pulse"></span>
                                    <span class="px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-widest 
                                        <?php 
                                            if($u['status'] === 'active') echo 'bg-emerald-500/10 text-emerald-500 border-emerald-500/20';
                                            elseif($u['status'] === 'approved') echo 'bg-blue-500/10 text-blue-500 border-blue-500/20';
                                            else echo 'bg-yellow-500/10 text-yellow-500 border-yellow-500/20';
                                        ?> border">
                                        <?php echo strtoupper($u['status']); ?>
                                    </span>
                                </div>
                            </td>
                            <td class="px-8 py-6 text-sm text-zinc-500">
                                <?php echo date('M d, Y', strtotime($u['created_at'])); ?>
                            </td>
                            <td class="px-8 py-6 text-right">
                                <a href="user_analytics.php?id=<?php echo $u['id']; ?>" class="bg-emerald-600/10 text-emerald-500 border border-emerald-500/20 px-4 py-2 rounded-lg text-[10px] font-bold uppercase tracking-widest hover:bg-emerald-600 hover:text-white transition-all">Guide User</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
</body>
</html>
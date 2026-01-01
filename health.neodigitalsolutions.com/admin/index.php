<?php
require_once '../includes/config.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

if (isset($_GET['approve'])) {
    $user_id = $_GET['approve'];
    $stmt = $pdo->prepare("UPDATE users SET status = 'approved' WHERE id = ?");
    $stmt->execute([$user_id]);
    header("Location: index.php");
    exit;
}

// Statistics
$stats = [
    'users' => $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'user'")->fetchColumn(),
    'pending' => $pdo->query("SELECT COUNT(*) FROM users WHERE status = 'pending' AND role = 'user'")->fetchColumn(),
    'plans' => $pdo->query("SELECT COUNT(*) FROM meal_plans")->fetchColumn(),
];

$stmt = $pdo->query("SELECT u.*, p.receipt_path, p.trx_number FROM users u LEFT JOIN payments p ON u.id = p.user_id WHERE u.role = 'user' ORDER BY u.created_at DESC");
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
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-8 py-6 text-sm text-zinc-400">
                                        <?php echo date('M d, Y', strtotime($u['created_at'])); ?>
                                    </td>
                                    <td class="px-8 py-6">
                                        <?php if ($u['status'] === 'approved'): ?>
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
                                        <?php if ($u['status'] === 'pending'): ?>
                                            <a href="?approve=<?php echo $u['id']; ?>" class="emerald-gradient text-white px-5 py-2 rounded-xl text-xs font-bold uppercase tracking-widest shadow-lg shadow-emerald-500/20 hover:scale-105 transition-all inline-block">
                                                Approve
                                            </a>
                                        <?php else: ?>
                                            <span class="text-zinc-700">
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
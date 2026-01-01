<?php
require_once '../includes/config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

$user_id = $_GET['id'] ?? 0;
$date = $_POST['date'] ?? date('Y-m-d');

// Fetch user info
$stmt = $pdo->prepare("SELECT name as full_name FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

if (!$user) {
    die("User not found");
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_analytics'])) {
    $weight = $_POST['weight'] ?: null;
    $calories = $_POST['calories'] ?: null;
    $water = $_POST['water'] ?: null;
    $steps = $_POST['steps'] ?: null;

    $stmt = $pdo->prepare("
        INSERT INTO user_analytics (user_id, date, weight, calories_burned, water_intake, steps)
        VALUES (?, ?, ?, ?, ?, ?)
        ON CONFLICT (user_id, date) 
        DO UPDATE SET 
            weight = EXCLUDED.weight,
            calories_burned = EXCLUDED.calories_burned,
            water_intake = EXCLUDED.water_intake,
            steps = EXCLUDED.steps
    ");
    $stmt->execute([$user_id, $date, $weight, $calories, $water, $steps]);
    $success = "Analytics updated successfully for " . $date;
}

// Fetch existing data for the selected date
$stmt = $pdo->prepare("SELECT * FROM user_analytics WHERE user_id = ? AND date = ?");
$stmt->execute([$user_id, $date]);
$current_stats = $stmt->fetch();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Analytics - <?php echo htmlspecialchars($user['full_name']); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-zinc-950 text-white min-h-screen p-8">
    <div class="max-w-2xl mx-auto">
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-3xl font-black uppercase tracking-tighter text-emerald-500">Update Analytics</h1>
            <a href="index.php" class="text-zinc-500 hover:text-white transition-colors text-xs font-bold uppercase tracking-widest">Back to Users</a>
        </div>

        <div class="bg-zinc-900/50 border border-white/10 p-8 rounded-[2rem] backdrop-blur-xl">
            <h2 class="text-xl font-bold mb-6 text-white"><?php echo htmlspecialchars($user['full_name']); ?></h2>
            
            <?php if (isset($success)): ?>
                <div class="mb-6 p-4 bg-emerald-500/10 border border-emerald-500/20 text-emerald-500 rounded-xl text-sm font-bold">
                    <?php echo $success; ?>
                </div>
            <?php endif; ?>

            <form method="POST" class="space-y-6">
                <div>
                    <label class="block text-[10px] font-black uppercase tracking-widest text-zinc-500 mb-2">Select Date</label>
                    <input type="date" name="date" value="<?php echo $date; ?>" onchange="this.form.submit()" class="w-full bg-black/40 border border-white/10 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-emerald-500 transition-colors">
                </div>

                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <label class="block text-[10px] font-black uppercase tracking-widest text-zinc-500 mb-2">Weight (kg)</label>
                        <input type="number" step="0.01" name="weight" value="<?php echo $current_stats['weight'] ?? ''; ?>" class="w-full bg-black/40 border border-white/10 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-emerald-500 transition-colors">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black uppercase tracking-widest text-zinc-500 mb-2">Calories Burned</label>
                        <input type="number" name="calories" value="<?php echo $current_stats['calories_burned'] ?? ''; ?>" class="w-full bg-black/40 border border-white/10 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-emerald-500 transition-colors">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black uppercase tracking-widest text-zinc-500 mb-2">Water Intake (L)</label>
                        <input type="number" step="0.1" name="water" value="<?php echo $current_stats['water_intake'] ?? ''; ?>" class="w-full bg-black/40 border border-white/10 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-emerald-500 transition-colors">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black uppercase tracking-widest text-zinc-500 mb-2">Steps</label>
                        <input type="number" name="steps" value="<?php echo $current_stats['steps'] ?? ''; ?>" class="w-full bg-black/40 border border-white/10 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-emerald-500 transition-colors">
                    </div>
                </div>

                <button type="submit" name="save_analytics" class="w-full bg-emerald-600 hover:bg-emerald-500 text-white font-black uppercase tracking-[0.2em] py-4 rounded-xl transition-all shadow-lg shadow-emerald-900/20">
                    Save Daily Progress
                </button>
            </form>
        </div>
    </div>
</body>
</html>
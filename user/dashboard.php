<?php
require_once '../includes/config.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header("Location: ../login.php");
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

$plans = [
    ['id' => 1, 'title' => 'Standard Meal Plan', 'package' => 'weight_loss'],
    ['id' => 2, 'title' => 'Muscle Building Guide', 'package' => 'muscle_gain'],
    ['id' => 3, 'title' => 'Athlete Fueling', 'package' => 'sports_performance']
];
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
            <h1 class="text-4xl font-bold">Hello, <?php echo htmlspecialchars($user['name']); ?></h1>
            <a href="logout.php" class="text-zinc-500 hover:text-white">Logout</a>
        </div>

        <div class="bg-zinc-900 border border-zinc-800 p-6 rounded-xl mb-12">
            <h2 class="text-xl font-bold mb-4">Account Status: 
                <span class="<?php echo $user['status'] === 'approved' ? 'text-green-500' : 'text-yellow-500'; ?>">
                    <?php echo strtoupper($user['status']); ?>
                </span>
            </h2>
            <?php if ($user['status'] === 'pending'): ?>
                <p class="text-zinc-400 mb-4">Your meal plans are currently locked. Please upload your payment receipt to unlock them.</p>
                <a href="payment.php" class="inline-block bg-white text-black px-8 py-3 rounded-full font-bold">Upload Receipt</a>
            <?php endif; ?>
        </div>

        <h2 class="text-2xl font-bold mb-6">Your Meal Plans</h2>
        <div class="grid md:grid-cols-2 gap-6">
            <?php foreach ($plans as $plan): ?>
                <div class="bg-zinc-900 border border-zinc-800 p-6 rounded-xl">
                    <h3 class="text-xl font-bold mb-4"><?php echo $plan['title']; ?></h3>
                    <?php if ($user['status'] === 'approved'): ?>
                        <button class="bg-green-600 text-white px-6 py-2 rounded">Download PDF</button>
                    <?php else: ?>
                        <div class="flex items-center text-zinc-600">
                            <span class="mr-2">🔒</span>
                            <span>Locked (Payment Required)</span>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</body>
</html>
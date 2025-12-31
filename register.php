<?php
require_once 'includes/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $age = $_POST['age'];
    $weight = $_POST['weight'];
    $height = $_POST['height'];
    $goal = $_POST['goal'];
    $package = $_POST['package'];

    try {
        $stmt = $pdo->prepare("INSERT INTO users (name, email, password, age, weight, height, goal, package) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$name, $email, $password, $age, $weight, $height, $goal, $package]);
        header("Location: login.php");
        exit;
    } catch (PDOException $e) {
        $error = "Registration failed: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Join Eleni Mekuria | Premium Nutrition</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .auth-bg {
            background-image: linear-gradient(to right, rgba(0,0,0,0.9) 40%, rgba(0,0,0,0.4)), url('attached_assets/stock_images/nutrition_fresh_frui_271f4a7c.jpg');
            background-size: cover;
            background-position: center;
        }
    </style>
</head>
<body class="bg-black text-white min-h-screen auth-bg flex items-center justify-start p-6 md:p-24">
    <div class="max-w-xl w-full bg-zinc-900/80 backdrop-blur-xl p-10 rounded-3xl border border-white/10 shadow-2xl">
        <div class="mb-10">
            <h2 class="text-4xl font-black uppercase tracking-tighter mb-2">Join the <span class="text-emerald-500">Program</span></h2>
            <p class="text-zinc-400">Start your science-backed nutritional journey today.</p>
        </div>
        <form method="POST" class="space-y-6">
            <div class="grid md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-xs uppercase tracking-widest font-bold text-zinc-500 mb-2">Full Name</label>
                    <input type="text" name="name" class="w-full bg-black/50 border border-white/10 p-4 rounded-xl focus:border-emerald-500 transition-all outline-none" placeholder="John Doe" required>
                </div>
                <div>
                    <label class="block text-xs uppercase tracking-widest font-bold text-zinc-500 mb-2">Email</label>
                    <input type="email" name="email" class="w-full bg-black/50 border border-white/10 p-4 rounded-xl focus:border-emerald-500 transition-all outline-none" placeholder="john@example.com" required>
                </div>
            </div>
            <div>
                <label class="block text-xs uppercase tracking-widest font-bold text-zinc-500 mb-2">Password</label>
                <input type="password" name="password" class="w-full bg-black/50 border border-white/10 p-4 rounded-xl focus:border-emerald-500 transition-all outline-none" required>
            </div>
            <div class="grid grid-cols-3 gap-6">
                <div>
                    <label class="block text-xs uppercase tracking-widest font-bold text-zinc-500 mb-2">Age</label>
                    <input type="number" name="age" class="w-full bg-black/50 border border-white/10 p-4 rounded-xl focus:border-emerald-500 transition-all outline-none" required>
                </div>
                <div>
                    <label class="block text-xs uppercase tracking-widest font-bold text-zinc-500 mb-2">Weight (kg)</label>
                    <input type="number" step="0.1" name="weight" class="w-full bg-black/50 border border-white/10 p-4 rounded-xl focus:border-emerald-500 transition-all outline-none" required>
                </div>
                <div>
                    <label class="block text-xs uppercase tracking-widest font-bold text-zinc-500 mb-2">Height (cm)</label>
                    <input type="number" step="0.1" name="height" class="w-full bg-black/50 border border-white/10 p-4 rounded-xl focus:border-emerald-500 transition-all outline-none" required>
                </div>
            </div>
            <div>
                <label class="block text-xs uppercase tracking-widest font-bold text-zinc-500 mb-2">Fitness Goal</label>
                <textarea name="goal" rows="2" class="w-full bg-black/50 border border-white/10 p-4 rounded-xl focus:border-emerald-500 transition-all outline-none" placeholder="What are you looking to achieve?" required></textarea>
            </div>
            <div>
                <label class="block text-xs uppercase tracking-widest font-bold text-zinc-500 mb-2">Choose Package</label>
                <select name="package" class="w-full bg-black/50 border border-white/10 p-4 rounded-xl focus:border-emerald-500 transition-all outline-none appearance-none" required>
                    <option value="weight_loss">Weight Loss Plan</option>
                    <option value="muscle_gain">Muscle Gain Plan</option>
                    <option value="sports_performance">Sports Performance</option>
                </select>
            </div>
            <button type="submit" class="w-full bg-emerald-600 text-white font-black uppercase tracking-[0.2em] py-5 rounded-xl hover:bg-emerald-500 transition-all shadow-lg shadow-emerald-900/20">Create Account</button>
        </form>
        <p class="mt-8 text-center text-zinc-500 text-sm">Already a member? <a href="login.php" class="text-white hover:text-emerald-400 font-bold">Login here</a></p>
    </div>
</body>
</html>
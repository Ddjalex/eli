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
    <title>Register - Eleni Mekuria</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-black text-white p-8">
    <div class="max-w-xl mx-auto bg-zinc-900 p-8 rounded-xl border border-zinc-800">
        <h2 class="text-3xl font-bold mb-6">Create Your Profile</h2>
        <form method="POST">
            <div class="grid md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block mb-1">Full Name</label>
                    <input type="text" name="name" class="w-full bg-black border border-zinc-800 p-3 rounded" required>
                </div>
                <div>
                    <label class="block mb-1">Email</label>
                    <input type="email" name="email" class="w-full bg-black border border-zinc-800 p-3 rounded" required>
                </div>
            </div>
            <div class="mb-4">
                <label class="block mb-1">Password</label>
                <input type="password" name="password" class="w-full bg-black border border-zinc-800 p-3 rounded" required>
            </div>
            <div class="grid md:grid-cols-3 gap-4 mb-4">
                <div>
                    <label class="block mb-1">Age</label>
                    <input type="number" name="age" class="w-full bg-black border border-zinc-800 p-3 rounded" required>
                </div>
                <div>
                    <label class="block mb-1">Weight (kg)</label>
                    <input type="number" step="0.1" name="weight" class="w-full bg-black border border-zinc-800 p-3 rounded" required>
                </div>
                <div>
                    <label class="block mb-1">Height (cm)</label>
                    <input type="number" step="0.1" name="height" class="w-full bg-black border border-zinc-800 p-3 rounded" required>
                </div>
            </div>
            <div class="mb-4">
                <label class="block mb-1">Fitness Goal</label>
                <textarea name="goal" class="w-full bg-black border border-zinc-800 p-3 rounded" required></textarea>
            </div>
            <div class="mb-6">
                <label class="block mb-1">Choose Package</label>
                <select name="package" class="w-full bg-black border border-zinc-800 p-3 rounded" required>
                    <option value="weight_loss">Weight Loss Plan</option>
                    <option value="muscle_gain">Muscle Gain Plan</option>
                    <option value="sports_performance">Sports Performance</option>
                </select>
            </div>
            <button type="submit" class="w-full bg-white text-black font-bold py-3 rounded hover:bg-gray-200">Register</button>
        </form>
    </div>
</body>
</html>
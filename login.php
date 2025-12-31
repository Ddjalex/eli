<?php
require_once 'includes/config.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role'];
        header("Location: " . ($user['role'] === 'admin' ? 'admin/index.php' : 'user/dashboard.php'));
        exit;
    } else {
        $error = "Invalid credentials";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login - Eleni Mekuria</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-black text-white flex items-center justify-center min-h-screen">
    <div class="bg-zinc-900 p-8 rounded-xl border border-zinc-800 w-full max-w-md">
        <h2 class="text-3xl font-bold mb-6">Client Login</h2>
        <?php if ($error): ?>
            <p class="text-red-500 mb-4"><?php echo $error; ?></p>
        <?php endif; ?>
        <form method="POST">
            <div class="mb-4">
                <label class="block mb-2">Email</label>
                <input type="email" name="email" class="w-full bg-black border border-zinc-800 p-3 rounded" required>
            </div>
            <div class="mb-6">
                <label class="block mb-2">Password</label>
                <input type="password" name="password" class="w-full bg-black border border-zinc-800 p-3 rounded" required>
            </div>
            <button type="submit" class="w-full bg-white text-black font-bold py-3 rounded hover:bg-gray-200">Login</button>
        </form>
        <p class="mt-4 text-center text-zinc-500">Don't have an account? <a href="register.php" class="text-white">Register</a></p>
    </div>
</body>
</html>
<?php
require_once '../includes/config.php';
if (!isset($_SESSION['user_id'])) exit;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['receipt'])) {
    $upload_dir = '../uploads/receipts/';
    $filename = time() . '_' . $_FILES['receipt']['name'];
    $target = $upload_dir . $filename;

    if (move_uploaded_file($_FILES['receipt']['tmp_name'], $target)) {
        $stmt = $pdo->prepare("INSERT INTO payments (user_id, receipt_path) VALUES (?, ?)");
        $stmt->execute([$_SESSION['user_id'], $target]);
        header("Location: dashboard.php?uploaded=1");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Payment - Eleni Mekuria</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-black text-white p-8">
    <div class="max-w-md mx-auto bg-zinc-900 border border-zinc-800 p-8 rounded-xl">
        <h2 class="text-3xl font-bold mb-6">Payment</h2>
        <div class="mb-8 p-4 bg-zinc-800 rounded">
            <h3 class="font-bold mb-2">Bank Details</h3>
            <p>Admin Bank Name: CBE</p>
            <p>Account: 1000XXXXXXXXX</p>
            <p>Name: Eleni Mekuria</p>
        </div>
        <form method="POST" enctype="multipart/form-data">
            <div class="mb-6">
                <label class="block mb-2 text-zinc-400">Upload Receipt Screenshot</label>
                <input type="file" name="receipt" class="w-full bg-black border border-zinc-800 p-3 rounded" required>
            </div>
            <button type="submit" class="w-full bg-white text-black font-bold py-3 rounded">Submit for Approval</button>
        </form>
    </div>
</body>
</html>
<?php
require_once '../includes/config.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $plan_id = $_POST['plan_id'];
    
    if (isset($_FILES['pdf_file'])) {
        $protected_dir = '../protected_files/';
        if (!is_dir($protected_dir)) mkdir($protected_dir, 0777, true);
        
        $filename = 'plan_' . $plan_id . '_' . time() . '.pdf';
        $target = $protected_dir . $filename;
        
        if (move_uploaded_file($_FILES['pdf_file']['tmp_name'], $target)) {
            $stmt = $pdo->prepare("UPDATE meal_plans SET file_url = ? WHERE id = ?");
            $stmt->execute([$target, $plan_id]);
            $message = "Meal plan PDF updated successfully!";
        }
    } elseif (isset($_POST['upload_preview']) && isset($_FILES['preview_image'])) {
        $preview_dir = '../uploads/previews/';
        if (!is_dir($preview_dir)) mkdir($preview_dir, 0777, true);
        
        $filename = 'preview_' . $plan_id . '_' . time() . '_' . basename($_FILES['preview_image']['name']);
        $target = $preview_dir . $filename;
        
        if (move_uploaded_file($_FILES['preview_image']['tmp_name'], $target)) {
            $db_path = 'uploads/previews/' . $filename;
            $stmt = $pdo->prepare("UPDATE meal_plans SET preview_image = ? WHERE id = ?");
            $stmt->execute([$db_path, $plan_id]);
            $message = "Plan preview updated successfully!";
        }
    }
}

// Ensure at least 3 plans exist for the demo packages
$stmt = $pdo->query("SELECT COUNT(*) FROM meal_plans");
if ($stmt->fetchColumn() == 0) {
    $pdo->exec("INSERT INTO meal_plans (title, package_type) VALUES 
        ('Weight Loss Strategy', 'weight_loss'),
        ('Muscle Gain Guide', 'muscle_gain'),
        ('Sports Nutrition Protocol', 'sports_performance')");
}

$plans = $pdo->query("SELECT * FROM meal_plans ORDER BY id ASC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Meal Plans - Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-black text-white p-8">
    <div class="max-w-4xl mx-auto">
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-3xl font-bold">Secure Meal Plans</h1>
            <a href="index.php" class="text-zinc-500 hover:text-white">Back to Dashboard</a>
        </div>
        
        <?php if ($message): ?>
            <div class="bg-emerald-500/10 border border-emerald-500/50 p-4 rounded-xl mb-8 text-emerald-500"><?php echo $message; ?></div>
        <?php endif; ?>

        <div class="grid gap-6">
            <?php foreach ($plans as $p): ?>
                <div class="bg-zinc-900 p-6 rounded-2xl border border-white/10">
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <h3 class="text-xl font-bold uppercase tracking-tight"><?php echo $p['title']; ?></h3>
                            <p class="text-emerald-500 text-xs font-bold uppercase tracking-widest"><?php echo $p['package_type']; ?></p>
                        </div>
                        <?php if ($p['file_url']): ?>
                            <span class="text-xs bg-zinc-800 text-zinc-400 px-3 py-1 rounded-full border border-white/5">PDF Uploaded</span>
                        <?php else: ?>
                            <span class="text-xs bg-red-900/20 text-red-500 px-3 py-1 rounded-full border border-red-500/20">No File</span>
                        <?php endif; ?>
                    </div>
                    
                    <form method="POST" enctype="multipart/form-data" class="flex gap-4 items-center">
                        <input type="hidden" name="plan_id" value="<?php echo $p['id']; ?>">
                        <input type="file" name="pdf_file" accept=".pdf" class="text-xs text-zinc-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-zinc-800 file:text-zinc-300 hover:file:bg-zinc-700" required>
                        <button type="submit" class="bg-white text-black px-6 py-2 rounded-lg text-xs font-bold uppercase tracking-widest hover:bg-emerald-500 hover:text-white transition-all">Upload Full PDF</button>
                    </form>
                    <form method="POST" enctype="multipart/form-data" class="mt-4 pt-4 border-t border-white/5 flex gap-4 items-center">
                        <input type="hidden" name="plan_id" value="<?php echo $p['id']; ?>">
                        <input type="hidden" name="upload_preview" value="1">
                        <label class="text-[10px] uppercase font-bold text-zinc-500 w-24">Preview Image:</label>
                        <input type="file" name="preview_image" accept="image/*" class="text-xs text-zinc-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-zinc-800 file:text-zinc-300">
                        <button type="submit" class="bg-zinc-800 text-white px-6 py-2 rounded-lg text-xs font-bold uppercase tracking-widest hover:bg-zinc-700 transition-all">Upload Preview</button>
                    </form>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</body>
</html>
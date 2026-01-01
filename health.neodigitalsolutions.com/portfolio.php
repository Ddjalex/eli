<?php
require_once 'includes/config.php';
$projects = $pdo->query("SELECT * FROM portfolio_projects ORDER BY created_at DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portfolio | Eleni Mekuria</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-black text-white">
    <nav class="p-6 border-b border-white/10 flex justify-between items-center">
        <a href="index.php" class="text-2xl font-bold text-emerald-500 uppercase">Eleni</a>
        <a href="index.php" class="text-sm uppercase tracking-widest hover:text-emerald-400">Back Home</a>
    </nav>
    <main class="py-20 px-6 max-w-6xl mx-auto">
        <h1 class="text-5xl font-bold mb-12 uppercase tracking-tighter">My <span class="text-emerald-500">Portfolio</span></h1>
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
            <?php foreach ($projects as $p): ?>
                <div class="bg-zinc-900 rounded-2xl border border-white/10 overflow-hidden group">
                    <?php if ($p['image_url']): ?>
                        <img src="<?php echo htmlspecialchars($p['image_url']); ?>" class="w-full aspect-square object-cover grayscale group-hover:grayscale-0 transition-all">
                    <?php else: ?>
                        <div class="aspect-square bg-zinc-800 flex items-center justify-center">
                            <span class="text-zinc-600">No Image</span>
                        </div>
                    <?php endif; ?>
                    <div class="p-6">
                        <h3 class="font-bold text-xl mb-2"><?php echo htmlspecialchars($p['title']); ?></h3>
                        <p class="text-zinc-500 text-sm"><?php echo htmlspecialchars($p['description']); ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </main>
    <?php include 'includes/footer.php'; ?>
</body>
</html>
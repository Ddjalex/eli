<?php
require_once 'includes/config.php';
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
            <div class="aspect-square bg-zinc-900 rounded-2xl border border-white/10 flex items-center justify-center">
                <p class="text-zinc-500 uppercase tracking-widest font-bold">Project 1</p>
            </div>
            <div class="aspect-square bg-zinc-900 rounded-2xl border border-white/10 flex items-center justify-center">
                <p class="text-zinc-500 uppercase tracking-widest font-bold">Project 2</p>
            </div>
        </div>
    </main>
    <?php include 'includes/footer.php'; ?>
</body>
</html>
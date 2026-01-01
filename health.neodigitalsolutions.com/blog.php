<?php
require_once 'includes/config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blog | Eleni Mekuria</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-black text-white">
    <nav class="p-6 border-b border-white/10 flex justify-between items-center">
        <a href="index.php" class="text-2xl font-bold text-emerald-500 uppercase">Eleni</a>
        <a href="index.php" class="text-sm uppercase tracking-widest hover:text-emerald-400">Back Home</a>
    </nav>
    <main class="py-20 px-6 max-w-4xl mx-auto">
        <h1 class="text-5xl font-bold mb-12 uppercase tracking-tighter">Health & Nutrition <span class="text-emerald-500">Blog</span></h1>
        <div class="grid gap-12">
            <article class="border-b border-white/10 pb-12">
                <h2 class="text-2xl font-bold mb-4">The Importance of Balanced Nutrition</h2>
                <p class="text-zinc-400 leading-relaxed mb-6">Coming soon... We are working on bringing you the best nutritional advice.</p>
                <span class="text-emerald-500 text-sm font-bold uppercase tracking-widest">Read More</span>
            </article>
        </div>
    </main>
    <?php include 'includes/footer.php'; ?>
</body>
</html>
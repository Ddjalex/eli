<?php
require_once 'includes/config.php';
$blogs = $pdo->query("SELECT * FROM blogs ORDER BY created_at DESC")->fetchAll();
$stmt_profile = $pdo->prepare("SELECT value FROM site_settings WHERE `key` = 'about_image'");
$stmt_profile->execute();
$profile_img = $stmt_profile->fetchColumn() ?: 'attached_assets/stock_images/professional_dietiti_8bb8decd.jpg';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fitness & Nutrition Blog Ethiopia | Eleni Mekuria</title>
    <meta name="description" content="Discover the best fitness and nutrition tips in Ethiopia. Learn about the Ethiopian diet, Eskista fitness, and weight loss with Eleni Mekuria.">
    <meta name="keywords" content="Fitness Ethiopia, Nutrition Addis Ababa, Ethiopian Diet, Weight Loss Ethiopia, Eskista Fitness, Eleni Mekuria">
    <meta property="og:title" content="Fitness & Nutrition Blog Ethiopia | Eleni Mekuria">
    <meta property="og:description" content="Transform your body with science-backed nutrition and culturally relevant fitness tips in Ethiopia.">
    <meta property="og:image" content="attached_assets/stock_images/professional_fitness_249142df.jpg">
    <meta property="og:type" content="website">
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
            <?php foreach ($blogs as $b): ?>
                <article class="border-b border-white/10 pb-12">
                    <?php if ($b['image_url']): ?>
                        <img src="<?php echo htmlspecialchars($b['image_url']); ?>" class="w-full aspect-video object-cover rounded-2xl mb-6 grayscale hover:grayscale-0 transition-all">
                    <?php endif; ?>
                    <h2 class="text-2xl font-bold mb-4"><?php echo htmlspecialchars($b['title']); ?></h2>
                    <p class="text-zinc-400 leading-relaxed mb-6"><?php echo nl2br(htmlspecialchars(substr($b['content'], 0, 300))); ?>...</p>
                    <div class="flex items-center gap-3 mb-6">
                        <img src="<?php echo htmlspecialchars($profile_img); ?>" class="w-10 h-10 rounded-full object-cover border border-emerald-500/30">
                        <div>
                            <div class="font-bold uppercase tracking-widest text-[10px]">Eleni Mekuria</div>
                            <div class="text-zinc-500 text-[10px]"><?php echo date('M d, Y', strtotime($b['created_at'])); ?></div>
                        </div>
                    </div>
                    <a href="blog-detail.php?id=<?php echo $b['id']; ?>" class="text-emerald-500 text-sm font-bold uppercase tracking-widest">Read More</a>
                </article>
            <?php endforeach; ?>
        </div>
    </main>
    <?php include 'includes/footer.php'; ?>
</body>
</html>
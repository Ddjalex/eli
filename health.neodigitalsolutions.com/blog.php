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
    <nav id="main-nav" class="fixed w-full z-50 p-3 sm:p-4 md:p-6 flex justify-between items-center bg-black/50 backdrop-blur-md border-b border-white/10 transition-all duration-300">
        <div class="text-lg sm:text-xl md:text-2xl font-bold tracking-tighter uppercase text-emerald-500">Eleni</div>
        
        <!-- Desktop Menu -->
        <div class="hidden md:flex space-x-4 lg:space-x-8 uppercase text-[9px] lg:text-[10px] tracking-widest font-bold">
            <a href="index.php#home" class="hover:text-emerald-400">Home</a>
            <a href="index.php#about" class="hover:text-emerald-400">About</a>
            <a href="portfolio.php" class="hover:text-emerald-400">Portfolio</a>
            <a href="blog.php" class="text-emerald-500 font-bold">Blog</a>
            <a href="index.php#services" class="hover:text-emerald-400">Services</a>
            <a href="index.php#testimonials" class="hover:text-emerald-400">Stories</a>
            <a href="index.php#contact" class="hover:text-emerald-400">Contact</a>
            <a href="login.php" class="bg-emerald-600 text-white px-4 sm:px-6 py-2 rounded-full hover:bg-emerald-500 transition-all">Portal</a>
        </div>

        <!-- Mobile Menu Toggle -->
        <div class="flex items-center gap-3 md:hidden">
            <a href="login.php" class="bg-emerald-600 text-white px-4 py-2 rounded-full hover:bg-emerald-500 transition-all text-[10px] font-bold uppercase tracking-widest">Portal</a>
            <button id="menu-toggle" class="text-white p-2">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"></path></svg>
            </button>
        </div>
    </nav>

    <!-- Fullscreen Mobile Menu Overlay -->
    <div id="mobile-menu" class="fixed inset-0 z-[60] bg-black translate-x-full transition-transform duration-500 flex flex-col items-center justify-center space-y-8 text-2xl font-black uppercase tracking-tighter">
        <button id="menu-close" class="absolute top-6 right-6 text-white p-2">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
        </button>
        <a href="index.php#home" class="hover:text-emerald-500">Home</a>
        <a href="index.php#about" class="hover:text-emerald-500">About</a>
        <a href="portfolio.php" class="hover:text-emerald-500">Portfolio</a>
        <a href="blog.php" class="text-emerald-500">Blog</a>
        <a href="index.php#services" class="hover:text-emerald-500">Services</a>
        <a href="index.php#testimonials" class="hover:text-emerald-500">Stories</a>
        <a href="index.php#contact" class="hover:text-emerald-500">Contact</a>
    </div>

    <main class="py-32 px-6 max-w-4xl mx-auto">
        <h1 class="text-3xl sm:text-5xl font-bold mb-12 uppercase tracking-tighter pt-10">Health & Nutrition <span class="text-emerald-500">Blog</span></h1>
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
    <script>
        // Mobile Menu Logic
        const menuToggle = document.getElementById('menu-toggle');
        const menuClose = document.getElementById('menu-close');
        const mobileMenu = document.getElementById('mobile-menu');

        menuToggle.addEventListener('click', () => {
            mobileMenu.classList.remove('translate-x-full');
        });

        menuClose.addEventListener('click', () => {
            mobileMenu.classList.add('translate-x-full');
        });
    </script>
</body>
</html>
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
    <nav id="main-nav" class="fixed w-full z-50 p-3 sm:p-4 md:p-6 flex justify-between items-center bg-black/50 backdrop-blur-md border-b border-white/10 transition-all duration-300">
        <div class="text-lg sm:text-xl md:text-2xl font-bold tracking-tighter uppercase text-emerald-500">Eleni</div>
        <div class="hidden md:flex space-x-4 lg:space-x-8 uppercase text-[9px] lg:text-[10px] tracking-widest font-bold">
            <a href="index.php#home" class="hover:text-emerald-400">Home</a>
            <a href="index.php#about" class="hover:text-emerald-400">About</a>
            <a href="portfolio.php" class="text-emerald-500 font-bold">Portfolio</a>
            <a href="blog.php" class="hover:text-emerald-400">Blog</a>
            <a href="index.php#services" class="hover:text-emerald-400">Services</a>
            <a href="index.php#testimonials" class="hover:text-emerald-400">Stories</a>
            <a href="index.php#contact" class="hover:text-emerald-400">Contact</a>
            <a href="login.php" class="bg-emerald-600 text-white px-4 sm:px-6 py-2 rounded-full hover:bg-emerald-500 transition-all">Portal</a>
        </div>
        <a href="login.php" class="md:hidden bg-emerald-600 text-white px-3 py-1 rounded-full hover:bg-emerald-500 transition-all text-[9px]">Portal</a>
    </nav>
    <div id="mobile-menu" class="fixed inset-0 z-[60] bg-black translate-x-full transition-transform duration-500 flex flex-col items-center justify-center space-y-8 text-2xl font-black uppercase tracking-tighter">
        <button id="menu-close" class="absolute top-6 right-6 text-white p-2">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
        </button>
        <a href="index.php#home" class="hover:text-emerald-500">Home</a>
        <a href="index.php#about" class="hover:text-emerald-500">About</a>
        <a href="portfolio.php" class="text-emerald-500">Portfolio</a>
        <a href="blog.php" class="hover:text-emerald-500">Blog</a>
        <a href="index.php#services" class="hover:text-emerald-500">Services</a>
        <a href="index.php#testimonials" class="hover:text-emerald-500">Stories</a>
        <a href="index.php#contact" class="hover:text-emerald-500">Contact</a>
    </div>

    <main class="py-32 px-6 max-w-6xl mx-auto">
        <h1 class="text-4xl sm:text-5xl font-bold mb-12 uppercase tracking-tighter">My <span class="text-emerald-500">Portfolio</span></h1>
        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6 md:gap-8">
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
    <script>
        // Mobile Menu Toggle
        const menuToggle = document.querySelector('[id="menu-toggle"]') || document.createElement('button');
        const menuBtn = document.createElement('button');
        menuBtn.id = "menu-toggle";
        menuBtn.className = "text-white p-2 md:hidden";
        menuBtn.innerHTML = '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"></path></svg>';
        document.querySelector('nav .flex.items-center') || document.querySelector('nav').appendChild(menuBtn);

        const realMenuToggle = document.getElementById('menu-toggle');
        const menuClose = document.getElementById('menu-close');
        const mobileMenu = document.getElementById('mobile-menu');

        realMenuToggle.addEventListener('click', () => {
            mobileMenu.classList.remove('translate-x-full');
        });

        menuClose.addEventListener('click', () => {
            mobileMenu.classList.add('translate-x-full');
        });
    </script>
</body>
</html>
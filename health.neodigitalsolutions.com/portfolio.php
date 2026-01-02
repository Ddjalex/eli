<?php
require_once 'includes/config.php';
$projects = $pdo->query("SELECT * FROM portfolio_projects ORDER BY `created_at` DESC")->fetchAll();
$case_studies = $pdo->query("SELECT * FROM case_studies ORDER BY `display_order` ASC, `id` DESC")->fetchAll();
$credentials = $pdo->query("SELECT * FROM credentials ORDER BY `display_order` ASC, `id` DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portfolio & Credentials | Eleni Mekuria</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/api/libs/gsap/3.12.2/gsap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/api/libs/gsap/3.12.2/ScrollTrigger.min.js"></script>
    <style>
        :root { --accent-green: #10b981; }
        .scroll-reveal { opacity: 0; transform: translateY(30px); }
    </style>
</head>
<body class="bg-zinc-950 text-white selection:bg-emerald-500">
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

    <main class="pt-32 pb-20 px-6 max-w-7xl mx-auto">
        <!-- Portfolio Section -->
        <section class="mb-32">
            <h1 class="text-4xl sm:text-7xl font-black mb-16 uppercase tracking-tighter scroll-reveal">Creative <span class="text-emerald-500">Media</span></h1>
            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6 md:gap-8">
                <?php foreach ($projects as $p): ?>
                    <div class="bg-zinc-900/50 rounded-3xl border border-white/5 overflow-hidden group hover:border-emerald-500/50 transition-all scroll-reveal">
                        <?php if ($p['image_url']): ?>
                            <div class="aspect-square overflow-hidden">
                                <img src="<?php echo htmlspecialchars($p['image_url']); ?>" class="w-full h-full object-cover grayscale group-hover:grayscale-0 group-hover:scale-105 transition-all duration-700">
                            </div>
                        <?php else: ?>
                            <div class="aspect-square bg-zinc-800/50 flex items-center justify-center">
                                <span class="text-zinc-600 uppercase text-[10px] font-bold tracking-widest">No Media Asset</span>
                            </div>
                        <?php endif; ?>
                        <div class="p-8">
                            <span class="text-[10px] font-bold uppercase tracking-[0.2em] text-emerald-500 mb-2 block"><?php echo htmlspecialchars($p['category'] ?? 'Project'); ?></span>
                            <h3 class="font-bold text-2xl mb-3 tracking-tight"><?php echo htmlspecialchars($p['title']); ?></h3>
                            <p class="text-zinc-500 text-sm leading-relaxed"><?php echo htmlspecialchars($p['description']); ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>

        <!-- Clinical Case Studies -->
        <section class="mb-32">
            <h2 class="text-4xl md:text-6xl font-black mb-16 uppercase tracking-tighter scroll-reveal text-right">Clinical <span class="text-emerald-500">Insights</span></h2>
            <div class="grid lg:grid-cols-2 gap-8">
                <?php if (empty($case_studies)): ?>
                    <div class="lg:col-span-2 p-12 border border-white/5 border-dashed rounded-3xl text-center scroll-reveal">
                        <p class="text-zinc-500 uppercase text-xs font-bold tracking-widest">Case studies are being prepared for scientific publication.</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($case_studies as $cs): ?>
                        <div class="bg-zinc-900/80 p-10 rounded-3xl border border-white/5 hover:border-emerald-500/30 transition-all scroll-reveal">
                            <div class="flex justify-between items-start mb-6">
                                <span class="text-[10px] font-bold uppercase tracking-widest px-3 py-1 bg-emerald-500/10 text-emerald-500 rounded-full border border-emerald-500/20"><?php echo htmlspecialchars($cs['category']); ?></span>
                            </div>
                            <h3 class="text-2xl font-bold mb-4 tracking-tight"><?php echo htmlspecialchars($cs['title']); ?></h3>
                            <div class="space-y-6">
                                <div>
                                    <h4 class="text-[10px] font-bold uppercase tracking-widest text-zinc-500 mb-2">Patient Summary</h4>
                                    <p class="text-zinc-400 text-sm leading-relaxed"><?php echo nl2br(htmlspecialchars($cs['summary'])); ?></p>
                                </div>
                                <div class="p-6 bg-black/50 rounded-2xl border border-white/5">
                                    <h4 class="text-[10px] font-bold uppercase tracking-widest text-emerald-500 mb-2">Scientific Approach</h4>
                                    <p class="text-zinc-400 text-sm leading-relaxed"><?php echo nl2br(htmlspecialchars($cs['approach'])); ?></p>
                                </div>
                                <div>
                                    <h4 class="text-[10px] font-bold uppercase tracking-widest text-zinc-500 mb-2">Clinical Outcome</h4>
                                    <p class="text-zinc-300 font-medium text-sm leading-relaxed"><?php echo nl2br(htmlspecialchars($cs['results'])); ?></p>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </section>

        <!-- Credentials Section -->
        <section class="mb-20">
            <h2 class="text-4xl md:text-6xl font-black mb-16 uppercase tracking-tighter scroll-reveal">Authority & <span class="text-emerald-500">Expertise</span></h2>
            <div class="grid md:grid-cols-2 gap-12">
                <div class="scroll-reveal">
                    <h3 class="text-xl font-bold uppercase tracking-widest text-zinc-500 mb-8 flex items-center gap-4">
                        <span class="w-12 h-[1px] bg-emerald-500"></span> MSc Research
                    </h3>
                    <div class="space-y-8">
                        <?php foreach ($credentials as $cred): if ($cred['type'] !== 'research') continue; ?>
                            <div class="group">
                                <div class="text-xs font-bold text-emerald-500 mb-1"><?php echo htmlspecialchars($cred['year']); ?></div>
                                <h4 class="text-lg font-bold group-hover:text-emerald-400 transition-colors"><?php echo htmlspecialchars($cred['title']); ?></h4>
                                <p class="text-zinc-500 text-sm"><?php echo htmlspecialchars($cred['institution']); ?></p>
                            </div>
                        <?php endforeach; ?>
                        <?php if (empty($credentials)): ?>
                            <p class="text-zinc-600 text-sm italic">Master of Science academic data loading...</p>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="scroll-reveal">
                    <h3 class="text-xl font-bold uppercase tracking-widest text-zinc-500 mb-8 flex items-center gap-4">
                        <span class="w-12 h-[1px] bg-emerald-500"></span> Professional Licenses
                    </h3>
                    <div class="space-y-8">
                        <?php foreach ($credentials as $cred): if ($cred['type'] !== 'license') continue; ?>
                            <div class="flex gap-6 items-start">
                                <div class="w-10 h-10 rounded-xl bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center shrink-0">
                                    <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                                </div>
                                <div>
                                    <h4 class="font-bold text-lg"><?php echo htmlspecialchars($cred['title']); ?></h4>
                                    <p class="text-zinc-500 text-sm"><?php echo htmlspecialchars($cred['institution']); ?></p>
                                    <p class="text-[10px] font-bold text-emerald-500/80 uppercase tracking-widest mt-1">Verified License</p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <?php include 'includes/footer.php'; ?>
    <script>
        // Mobile Menu Toggle
        const menuToggle = document.querySelector('[id="menu-toggle"]') || document.createElement('button');
        const menuBtn = document.createElement('button');
        menuBtn.id = "menu-toggle";
        menuBtn.className = "text-white p-2 md:hidden";
        menuBtn.innerHTML = '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"></path></svg>';
        document.querySelector('nav').appendChild(menuBtn);

        const realMenuToggle = document.getElementById('menu-toggle');
        const menuClose = document.getElementById('menu-close');
        const mobileMenu = document.getElementById('mobile-menu');

        realMenuToggle.addEventListener('click', () => {
            mobileMenu.classList.remove('translate-x-full');
        });

        menuClose.addEventListener('click', () => {
            mobileMenu.classList.add('translate-x-full');
        });

        gsap.registerPlugin(ScrollTrigger);
        document.querySelectorAll('.scroll-reveal').forEach((el) => {
            gsap.to(el, {
                scrollTrigger: { 
                    trigger: el, 
                    start: "top 90%", 
                    toggleActions: "play none none reverse" 
                },
                opacity: 1, 
                y: 0, 
                duration: 1.2, 
                ease: "power4.out"
            });
        });
    </script>
</body>
</html>
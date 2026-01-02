<?php
require_once 'includes/config.php';

// 'key' በሚለው ቃል ዙሪያ ጋሻ (backticks) መጨመሩን እርግጠኛ ሁኚ
$stmt = $pdo->prepare("SELECT value FROM site_settings WHERE \"key\" = ?");

$stmt->execute(['hero_bg_image']);
$hero_bg = $stmt->fetchColumn() ?: 'attached_assets/stock_images/modern_nutrition_hea_7726d1af.jpg';

$stmt->execute(['about_image']);
$about_img = $stmt->fetchColumn() ?: 'attached_assets/stock_images/professional_dietiti_8bb8decd.jpg';

$stmt->execute(['about_philosophy']);
$about_philosophy = $stmt->fetchColumn() ?: 'Science & Empathy';

$stmt->execute(['about_detailed_bio']);
$about_bio = $stmt->fetchColumn() ?: 'MSc from Addis Ababa University, Afrihealth TV host, and founder of EDPA.';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Diet & Nutritionist Eleni | Personalized Meal Plans</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/ScrollTrigger.min.js"></script>
    <script src="https://cdn.jsdelivr.net/gh/studio-freight/lenis@1.0.19/bundled/lenis.min.js"></script>
    <style>
        :root { --accent-green: #10b981; }
        .hero-slide { position: absolute; inset: 0; opacity: 0; transition: opacity 1s ease-in-out; }
        .hero-slide.active { opacity: 1; }
        .hero-bg-image { 
            position: absolute; inset: 0;
            background-size: cover; background-position: center; filter: brightness(0.4);
            width: 100%; height: 100%; object-fit: cover;
        }
        @media (max-width: 640px) {
            .hero-bg-image {
                background-position: center top;
            }
        }
        .scroll-reveal { opacity: 0; transform: scale(0.95); }
        #main-nav { transition: transform 0.4s cubic-bezier(0.4, 0, 0.2, 1); }
    </style>
</head>
<body class="bg-black text-white selection:bg-emerald-500">
    <nav id="main-nav" class="fixed w-full z-50 p-3 sm:p-4 md:p-6 flex justify-between items-center bg-black/50 backdrop-blur-md border-b border-white/10 transition-all duration-300">
        <a href="#home" class="flex items-center gap-3 hover:opacity-80 transition-opacity">
            <img src="/attached_assets/generated_images/elegant_luxury_logo_for_dietitian_eleni..png" alt="Logo" class="h-8 sm:h-10 md:h-12 w-auto">
            <div class="flex flex-col">
                <span class="text-sm sm:text-base font-bold tracking-tighter uppercase text-emerald-500 leading-none">Diet & Nutritionist</span>
                <span class="text-xs sm:text-sm font-medium tracking-widest uppercase text-white leading-none mt-1">Eleni</span>
            </div>
        </a>
        
        <!-- Desktop Menu -->
        <div class="hidden md:flex space-x-4 lg:space-x-8 uppercase text-[9px] lg:text-[10px] tracking-widest font-bold">
            <a href="#home" class="hover:text-emerald-400 transition-colors">Home</a>
            <a href="#about" class="hover:text-emerald-400 transition-colors">About</a>
            <a href="portfolio.php" class="hover:text-emerald-400 transition-colors">Portfolio</a>
            <a href="blog.php" class="hover:text-emerald-400 transition-colors">Blog</a>
            <a href="#services" class="hover:text-emerald-400 transition-colors">Services</a>
            <a href="#testimonials" class="hover:text-emerald-400 transition-colors">Stories</a>
            <a href="#contact" class="hover:text-emerald-400 transition-colors">Contact</a>
            <a href="login.php" class="bg-emerald-600 text-white px-4 sm:px-6 py-2 rounded-full hover:bg-emerald-500 transition-all shadow-lg shadow-emerald-500/20">Portal</a>
        </div>

        <!-- Mobile Menu Toggle -->
        <div class="flex items-center gap-3 md:hidden">
            <a href="login.php" class="bg-emerald-600 text-white px-4 py-2 rounded-full hover:bg-emerald-500 transition-all text-[10px] font-bold uppercase tracking-widest">Portal</a>
            <button id="menu-toggle" class="text-white p-2 focus:outline-none">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"></path></svg>
            </button>
        </div>
    </nav>

    <!-- Fullscreen Mobile Menu Overlay -->
    <div id="mobile-menu" class="fixed inset-0 z-[60] bg-black translate-x-full transition-transform duration-500 flex flex-col items-center justify-center space-y-8 text-2xl font-black uppercase tracking-tighter">
        <button id="menu-close" class="absolute top-6 right-6 text-white p-2">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
        </button>
        <a href="#home" class="mobile-link hover:text-emerald-500 transition-colors">Home</a>
        <a href="#about" class="mobile-link hover:text-emerald-500 transition-colors">About</a>
        <a href="portfolio.php" class="mobile-link hover:text-emerald-500 transition-colors">Portfolio</a>
        <a href="blog.php" class="mobile-link hover:text-emerald-500 transition-colors">Blog</a>
        <a href="#services" class="mobile-link hover:text-emerald-500 transition-colors">Services</a>
        <a href="#testimonials" class="mobile-link hover:text-emerald-500 transition-colors">Stories</a>
        <a href="#contact" class="mobile-link hover:text-emerald-500 transition-colors">Contact</a>
    </div>

    <div id="home" class="relative h-[60dvh] sm:h-[75dvh] lg:h-[85dvh] flex items-center justify-center overflow-hidden">
        <?php
        // hero_slides ዳታ ካለ ማምጣት
        try {
            $slides = $pdo->query("SELECT * FROM hero_slides ORDER BY display_order ASC, id DESC")->fetchAll();
        } catch (Exception $e) {
            $slides = [];
        }

        if (empty($slides)) {
            $slides = [[
                'image_url' => 'attached_assets/stock_images/modern_nutrition_hea_7726d1af.jpg',
                'title_main' => 'FUEL YOUR',
                'title_accent' => 'POTENTIAL',
                'subtitle' => 'SCIENCE & EMPATHY'
            ]];
        }
        
        foreach ($slides as $index => $slide): ?>
            <div class="hero-slide <?php echo $index === 0 ? 'active' : ''; ?>">
                <div class="absolute inset-0 hero-bg-image" style="background-image: url('<?php echo $slide['image_url']; ?>');"></div>
                <div class="relative z-10 h-full flex flex-col items-center justify-center text-center px-4 sm:px-12 pt-16 sm:pt-20">
                    <h1 class="text-3xl sm:text-5xl md:text-7xl lg:text-8xl font-black uppercase tracking-tighter mb-2 sm:mb-4 leading-[0.9] drop-shadow-2xl">
                        <?php echo htmlspecialchars($slide['title_main']); ?> <br><span class="text-emerald-500"><?php echo htmlspecialchars($slide['title_accent']); ?></span>
                    </h1>
                    <p class="text-[10px] sm:text-sm md:text-lg lg:text-xl tracking-[0.2em] sm:tracking-[0.4em] uppercase text-zinc-300 font-bold mb-6 sm:mb-10 max-w-2xl px-6 leading-tight">
                        <?php echo htmlspecialchars($slide['subtitle']); ?>
                    </p>
                    <a href="register.php" class="bg-white text-black px-6 sm:px-12 py-3 sm:py-5 rounded-full font-black uppercase tracking-widest text-[9px] sm:text-sm hover:bg-emerald-500 hover:text-white transition-all transform hover:scale-105 shadow-2xl shadow-black/50">Start Your Journey</a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <section id="about" class="py-16 sm:py-24 md:py-32 px-4 sm:px-6 lg:px-24 bg-zinc-950">
        <div class="grid md:grid-cols-2 gap-8 md:gap-12 lg:gap-16 items-center">
            <div class="scroll-reveal order-2 md:order-1">
                <h2 class="text-3xl sm:text-4xl md:text-5xl font-bold mb-4 sm:mb-6 md:mb-8 uppercase tracking-tighter">About <span class="text-emerald-500">Eleni</span></h2>
                <div class="text-sm sm:text-base md:text-lg lg:text-xl text-zinc-400 leading-relaxed mb-4 md:mb-6 space-y-4">
                    <?php echo nl2br(htmlspecialchars($about_bio)); ?>
                </div>
                <div class="border-l-4 border-emerald-500 pl-4 sm:pl-6 italic text-sm sm:text-base md:text-lg mb-6 md:mb-8">
                    "My approach blends rigorous nutritional science with deep clinical empathy."
                </div>
            </div>
            <div class="aspect-[4/5] rounded-2xl sm:rounded-3xl overflow-hidden border border-white/10 scroll-reveal order-1 md:order-2 relative" id="about-image-container">
                <?php
                try {
                    $about_slides = $pdo->query("SELECT * FROM about_slides ORDER BY id DESC")->fetchAll();
                } catch (Exception $e) { $about_slides = []; }

                if (empty($about_slides)) {
                    $about_slides = [['image_url' => $about_img]];
                }

                foreach ($about_slides as $index => $slide): ?>
                    <img src="<?php echo htmlspecialchars($slide['image_url']); ?>" 
                         class="about-slide absolute inset-0 w-full h-full object-cover grayscale hover:grayscale-0 transition-opacity duration-1000 <?php echo $index === 0 ? 'opacity-100' : 'opacity-0'; ?>">
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <section id="services" class="py-16 sm:py-24 md:py-32 bg-black px-4 sm:px-6">
         <h2 class="text-3xl sm:text-4xl md:text-5xl font-bold text-center mb-12 md:mb-20 uppercase tracking-tighter">Premium <span class="text-emerald-500">Services</span></h2>
         <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6 md:gap-8 max-w-6xl mx-auto">
             <div class="bg-zinc-900/50 p-6 sm:p-8 md:p-10 rounded-2xl sm:rounded-3xl border border-white/5 hover:border-emerald-500/50 transition-all flex flex-col">
                 <div class="text-3xl sm:text-4xl mb-4 md:mb-6">🤝</div>
                 <h3 class="text-base sm:text-lg md:text-xl font-bold mb-3 md:mb-4 uppercase">1-on-1 Counseling</h3>
                 <p class="text-zinc-500 text-xs sm:text-sm mb-6 md:mb-8">Personalized Online & In-person sessions tailored to your unique biology.</p>
                 <div class="mt-auto flex gap-2 sm:gap-4 flex-col sm:flex-row">
                     <a href="service-detail.php?id=counseling" class="inline-block text-center border border-white/10 text-white px-4 sm:px-6 py-2 sm:py-3 rounded-lg sm:rounded-xl font-bold uppercase text-[9px] sm:text-[10px] tracking-widest hover:bg-white hover:text-black transition-all">Details</a>
                     <a href="register.php" class="inline-block text-center bg-emerald-600 text-white px-4 sm:px-6 py-2 sm:py-3 rounded-lg sm:rounded-xl font-bold uppercase text-[9px] sm:text-[10px] tracking-widest hover:bg-emerald-500 transition-all">Join Now</a>
                 </div>
             </div>

             <?php
             $market_plans = [];
             try {
                 // Fetch all 3 plans from the database
                 $stmt_plans = $pdo->query("SELECT * FROM meal_plans ORDER BY id ASC");
                 $market_plans = $stmt_plans->fetchAll();
             } catch (PDOException $e) {
                 // Fallback if table doesn't exist
                 $market_plans = [];
             }
             
             if (empty($market_plans)) {
                 echo '<p class="text-zinc-500 text-center col-span-full">No meal plans available at the moment.</p>';
             } else {
                 foreach ($market_plans as $plan): ?>
                 <div class="bg-zinc-900/50 p-6 sm:p-8 md:p-10 rounded-2xl sm:rounded-3xl border border-white/5 hover:border-emerald-500/50 transition-all flex flex-col group">
                     <div class="text-3xl sm:text-4xl mb-4 md:mb-6 group-hover:scale-110 transition-transform">🥗</div>
                     <h3 class="text-base sm:text-lg md:text-xl font-bold mb-3 md:mb-4 uppercase"><?php echo htmlspecialchars($plan['title']); ?></h3>
                     <p class="text-zinc-500 text-xs sm:text-sm mb-6 md:mb-8">Professional nutritional guide for <?php echo htmlspecialchars(str_replace('_', ' ', $plan['package_type'] ?: 'Health')); ?>.</p>
                     <div class="mt-auto flex gap-2 sm:gap-4 flex-col sm:flex-row">
                         <a href="service-detail.php?id=<?php echo $plan['id']; ?>" class="inline-block text-center border border-white/10 text-white px-4 sm:px-6 py-2 sm:py-3 rounded-lg sm:rounded-xl font-bold uppercase text-[9px] sm:text-[10px] tracking-widest hover:bg-white hover:text-black transition-all">Details</a>
                         <a href="register.php" class="inline-block text-center bg-emerald-600 text-white px-4 sm:px-6 py-2 sm:py-3 rounded-lg sm:rounded-xl font-bold uppercase text-[9px] sm:text-[10px] tracking-widest hover:bg-emerald-500 transition-all">Join Now</a>
                     </div>
                 </div>
                 <?php endforeach; 
             } ?>
         </div>
    </section>

    <!-- Progress Section -->
    <?php
    try {
        $stmt_progress = $pdo->query("SELECT * FROM progress_photos ORDER BY display_order ASC, id DESC LIMIT 6");
        $progress_photos = $stmt_progress->fetchAll();
    } catch (Exception $e) {
        $progress_photos = [];
    }
    
    if (!empty($progress_photos)): ?>
    <section id="progress" class="py-16 sm:py-24 md:py-32 bg-zinc-950 px-4 sm:px-6">
        <h2 class="text-3xl sm:text-4xl md:text-5xl font-bold text-center mb-12 md:mb-20 uppercase tracking-tighter">Real <span class="text-emerald-500">Results</span></h2>
        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-8 max-w-7xl mx-auto">
            <?php foreach ($progress_photos as $photo): ?>
                <div class="group relative overflow-hidden rounded-3xl border border-white/10 bg-zinc-900/50 p-4 transition-all hover:border-emerald-500/50">
                    <div class="flex gap-2 mb-6">
                        <div class="relative w-1/2 overflow-hidden rounded-2xl">
                            <img src="<?php echo htmlspecialchars($photo['before_image_url']); ?>" class="w-full aspect-[3/4] object-cover">
                            <?php if ($photo['is_blurred']): ?>
                                <div class="absolute bg-white/10 backdrop-blur-2xl rounded-full border border-white/10 -translate-x-1/2 -translate-y-1/2 shadow-2xl" 
                                     style="left: <?php echo $photo['before_blur_x'] ?? 50; ?>%; top: <?php echo $photo['before_blur_y'] ?? 50; ?>%; width: <?php echo $photo['blur_size'] ?? 40; ?>px; height: <?php echo $photo['blur_size'] ?? 40; ?>px;"></div>
                            <?php endif; ?>
                            <span class="absolute bottom-2 left-2 bg-black/60 px-2 py-1 rounded text-[8px] uppercase font-bold tracking-widest">Before</span>
                        </div>
                        <div class="relative w-1/2 overflow-hidden rounded-2xl">
                            <img src="<?php echo htmlspecialchars($photo['after_image_url']); ?>" class="w-full aspect-[3/4] object-cover">
                            <?php if ($photo['is_blurred']): ?>
                                <div class="absolute bg-white/10 backdrop-blur-2xl rounded-full border border-white/10 -translate-x-1/2 -translate-y-1/2 shadow-2xl"
                                     style="left: <?php echo $photo['after_blur_x'] ?? 50; ?>%; top: <?php echo $photo['after_blur_y'] ?? 50; ?>%; width: <?php echo $photo['blur_size'] ?? 40; ?>px; height: <?php echo $photo['blur_size'] ?? 40; ?>px;"></div>
                            <?php endif; ?>
                            <span class="absolute bottom-2 left-2 bg-emerald-500/80 px-2 py-1 rounded text-[8px] uppercase font-bold tracking-widest">After</span>
                        </div>
                    </div>
                    <h3 class="text-center font-bold uppercase tracking-widest text-sm"><?php echo htmlspecialchars($photo['title']); ?></h3>
                </div>
            <?php endforeach; ?>
        </div>
    </section>
    <?php endif; ?>

    <?php include 'includes/footer.php'; ?>

    <script>
        const lenis = new Lenis();
        function raf(time) { lenis.raf(time); requestAnimationFrame(raf); }
        requestAnimationFrame(raf);

        // Navbar Scroll Behavior
        let lastScroll = 0;
        const nav = document.getElementById('main-nav');
        
        window.addEventListener('scroll', () => {
            const currentScroll = window.pageYOffset;
            
            if (currentScroll <= 0) {
                nav.style.transform = 'translateY(0)';
                return;
            }
            
            if (currentScroll > lastScroll && currentScroll > 100) {
                // Scrolling down
                nav.style.transform = 'translateY(-100%)';
            } else {
                // Scrolling up
                nav.style.transform = 'translateY(0)';
            }
            lastScroll = currentScroll;
        });

        // Slider Script
        const slides = document.querySelectorAll('.hero-slide');
        if (slides.length > 1) {
            let currentSlide = 0;
            setInterval(() => {
                slides[currentSlide].classList.remove('active');
                currentSlide = (currentSlide + 1) % slides.length;
                slides[currentSlide].classList.add('active');
            }, 5000);
        }

        // About Slider Script
        const aboutSlides = document.querySelectorAll('.about-slide');
        if (aboutSlides.length > 1) {
            let currentAboutSlide = 0;
            setInterval(() => {
                aboutSlides.forEach(slide => slide.style.opacity = '0');
                currentAboutSlide = (currentAboutSlide + 1) % aboutSlides.length;
                aboutSlides[currentAboutSlide].style.opacity = '1';
            }, 3000);
        }

        // Mobile Menu Logic
        const menuToggle = document.getElementById('menu-toggle');
        const menuClose = document.getElementById('menu-close');
        const mobileMenu = document.getElementById('mobile-menu');
        const mobileLinks = document.querySelectorAll('.mobile-link');

        menuToggle.addEventListener('click', () => {
            mobileMenu.classList.remove('translate-x-full');
        });

        menuClose.addEventListener('click', () => {
            mobileMenu.classList.add('translate-x-full');
        });

        mobileLinks.forEach(link => {
            link.addEventListener('click', () => {
                mobileMenu.classList.add('translate-x-full');
            });
        });

        // GSAP Scroll
        gsap.registerPlugin(ScrollTrigger);
        document.querySelectorAll('.scroll-reveal').forEach((el) => {
            gsap.to(el, {
                scrollTrigger: { trigger: el, start: "top 85%", toggleActions: "play none none reverse" },
                opacity: 1, scale: 1, duration: 1, ease: "power4.out"
            });
        });
    </script>
</body>
</html>
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

    <!-- Quick Results Section -->
    <section class="py-12 sm:py-16 bg-black border-y border-white/5 overflow-hidden">
        <div class="max-w-7xl mx-auto px-4 sm:px-6">
            <h2 class="text-2xl sm:text-3xl md:text-4xl font-black text-center mb-10 uppercase tracking-tighter">
                REAL <span class="text-emerald-500">RESULTS</span>
            </h2>
            
            <div class="flex justify-center">
                <div class="w-full max-w-sm sm:max-w-md lg:max-w-lg">
                    <div class="group relative overflow-hidden rounded-[2rem] border border-white/10 bg-zinc-900/50 p-3 sm:p-5 transition-all hover:border-emerald-500/50 hover:shadow-2xl hover:shadow-emerald-500/10">
                        <div class="flex gap-2 sm:gap-4 mb-4 sm:mb-6">
                            <div class="relative w-1/2 overflow-hidden rounded-2xl sm:rounded-[1.5rem] shadow-2xl bg-zinc-800">
                                <img src="/attached_assets/image_1767344040668.png" class="w-full aspect-[4/5] object-cover object-left scale-110" alt="Before Result">
                                <span class="absolute bottom-3 left-3 bg-black/80 backdrop-blur-md px-3 py-1 rounded-full text-[9px] sm:text-[10px] uppercase font-bold tracking-widest border border-white/10">Before</span>
                            </div>
                            <div class="relative w-1/2 overflow-hidden rounded-2xl sm:rounded-[1.5rem] shadow-2xl bg-zinc-800">
                                <img src="/attached_assets/image_1767344040668.png" class="w-full aspect-[4/5] object-cover object-right scale-110" alt="After Result">
                                <span class="absolute bottom-3 left-3 bg-emerald-500/90 backdrop-blur-md px-3 py-1 rounded-full text-[9px] sm:text-[10px] uppercase font-bold tracking-widest border border-emerald-400/20">After</span>
                            </div>
                        </div>
                        <div class="text-center">
                            <h3 class="font-black uppercase tracking-[0.2em] text-[10px] sm:text-xs text-zinc-500 group-hover:text-emerald-500 transition-colors">REAL TRANSFORMATION</h3>
                            <div class="mt-2 flex justify-center gap-1">
                                <div class="w-8 h-[1px] bg-emerald-500/30"></div>
                                <div class="w-1 h-1 rounded-full bg-emerald-500"></div>
                                <div class="w-8 h-[1px] bg-emerald-500/30"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="mt-12 text-center">
                <a href="#testimonials" class="text-[10px] sm:text-xs font-bold uppercase tracking-[0.3em] text-emerald-500 hover:text-white transition-colors flex items-center justify-center gap-2">
                    View More Success Stories
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                </a>
            </div>
        </div>
    </section>

    <section id="about" class="py-16 sm:py-24 md:py-32 px-4 sm:px-6 lg:px-24 bg-zinc-950">

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
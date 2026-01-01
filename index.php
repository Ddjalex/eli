<?php
require_once 'includes/config.php';
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
    <title>Eleni Mekuria | Dietitian & Nutritionist</title>
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
        }
        .scroll-reveal { opacity: 0; transform: scale(0.95); }
    </style>
</head>
<body class="bg-black text-white selection:bg-emerald-500">
    <nav id="main-nav" class="fixed w-full z-50 p-3 sm:p-4 md:p-6 flex justify-between items-center bg-black/50 backdrop-blur-md border-b border-white/10 transition-transform duration-500">
        <div class="text-lg sm:text-xl md:text-2xl font-bold tracking-tighter uppercase text-emerald-500">Eleni</div>
        <div class="hidden md:flex space-x-4 lg:space-x-8 uppercase text-[9px] lg:text-[10px] tracking-widest font-bold">
            <a href="#home" class="hover:text-emerald-400">Home</a>
            <a href="#about" class="hover:text-emerald-400">About</a>
            <a href="#services" class="hover:text-emerald-400">Services</a>
            <a href="#testimonials" class="hover:text-emerald-400">Stories</a>
            <a href="#contact" class="hover:text-emerald-400">Contact</a>
            <a href="login.php" class="bg-emerald-600 text-white px-4 sm:px-6 py-2 rounded-full hover:bg-emerald-500 transition-all">Portal</a>
        </div>
        <a href="login.php" class="md:hidden bg-emerald-600 text-white px-3 py-1 rounded-full hover:bg-emerald-500 transition-all text-[9px]">Portal</a>
    </nav>

    <div id="home" class="relative h-screen flex items-center justify-center overflow-hidden">
        <?php
        $slides = $pdo->query("SELECT * FROM hero_slides ORDER BY display_order ASC, id DESC")->fetchAll();
        if (empty($slides)) {
            // Fallback to static if no slides
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
                <div class="relative z-10 h-full flex flex-col items-center justify-center text-center px-4">
                    <h1 class="text-4xl sm:text-5xl md:text-7xl lg:text-9xl font-black uppercase tracking-tighter mb-3 sm:mb-4 leading-none">
                        <?php echo $slide['title_main']; ?> <br><span class="text-emerald-500"><?php echo $slide['title_accent']; ?></span>
                    </h1>
                    <p class="text-xs sm:text-sm md:text-lg lg:text-xl tracking-[0.3em] uppercase text-zinc-400 mb-6 sm:mb-8"><?php echo $slide['subtitle']; ?></p>
                    <a href="register.php" class="bg-white text-black px-6 sm:px-10 py-2 sm:py-4 rounded-full font-black uppercase tracking-widest text-xs sm:text-sm hover:bg-emerald-500 hover:text-white transition-all transform hover:scale-105 inline-block">Start Your Journey</a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <script>
        // Hero Slider Logic
        const slides = document.querySelectorAll('.hero-slide');
        if (slides.length > 1) {
            let currentSlide = 0;
            setInterval(() => {
                slides[currentSlide].classList.remove('active');
                currentSlide = (currentSlide + 1) % slides.length;
                slides[currentSlide].classList.add('active');
            }, 5000);
        }
    </script>

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
            <div class="aspect-[4/5] rounded-2xl sm:rounded-3xl overflow-hidden border border-white/10 scroll-reveal order-1 md:order-2">
                <img src="<?php echo $about_img; ?>" class="w-full h-full object-cover grayscale hover:grayscale-0 transition-all duration-700">
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
            <div class="bg-zinc-900/50 p-6 sm:p-8 md:p-10 rounded-2xl sm:rounded-3xl border border-white/5 hover:border-emerald-500/50 transition-all flex flex-col">
                <div class="text-3xl sm:text-4xl mb-4 md:mb-6">📋</div>
                <h3 class="text-base sm:text-lg md:text-xl font-bold mb-3 md:mb-4 uppercase">Custom Meal Planning</h3>
                <p class="text-zinc-500 text-xs sm:text-sm mb-6 md:mb-8">Science-backed protocols for weight loss, muscle gain, or performance.</p>
                <div class="mt-auto flex gap-2 sm:gap-4 flex-col sm:flex-row">
                    <a href="service-detail.php?id=meal-planning" class="inline-block text-center border border-white/10 text-white px-4 sm:px-6 py-2 sm:py-3 rounded-lg sm:rounded-xl font-bold uppercase text-[9px] sm:text-[10px] tracking-widest hover:bg-white hover:text-black transition-all">Details</a>
                    <a href="register.php" class="inline-block text-center bg-emerald-600 text-white px-4 sm:px-6 py-2 sm:py-3 rounded-lg sm:rounded-xl font-bold uppercase text-[9px] sm:text-[10px] tracking-widest hover:bg-emerald-500 transition-all">Join Now</a>
                </div>
            </div>
            <div class="bg-zinc-900/50 p-6 sm:p-8 md:p-10 rounded-2xl sm:rounded-3xl border border-white/5 hover:border-emerald-500/50 transition-all flex flex-col sm:col-span-2 lg:col-span-1">
                <div class="text-3xl sm:text-4xl mb-4 md:mb-6">🏢</div>
                <h3 class="text-base sm:text-lg md:text-xl font-bold mb-3 md:mb-4 uppercase">Corporate Wellness</h3>
                <p class="text-zinc-500 text-xs sm:text-sm mb-6 md:mb-8">Group coaching and wellness strategy for high-performance teams.</p>
                <div class="mt-auto flex gap-2 sm:gap-4 flex-col sm:flex-row">
                    <a href="service-detail.php?id=wellness" class="inline-block text-center border border-white/10 text-white px-4 sm:px-6 py-2 sm:py-3 rounded-lg sm:rounded-xl font-bold uppercase text-[9px] sm:text-[10px] tracking-widest hover:bg-white hover:text-black transition-all">Details</a>
                    <a href="register.php" class="inline-block text-center bg-emerald-600 text-white px-4 sm:px-6 py-2 sm:py-3 rounded-lg sm:rounded-xl font-bold uppercase text-[9px] sm:text-[10px] tracking-widest hover:bg-emerald-500 transition-all">Join Now</a>
                </div>
            </div>
        </div>
    </section>

    <section id="testimonials" class="py-16 sm:py-24 md:py-32 bg-zinc-950 px-4 sm:px-6">
        <h2 class="text-3xl sm:text-4xl md:text-5xl font-bold text-center mb-12 md:mb-20 uppercase tracking-tighter">Success <span class="text-emerald-500">Stories</span></h2>
        <div class="max-w-4xl mx-auto">
            <?php
            $tests = $pdo->query("SELECT * FROM testimonials")->fetchAll();
            foreach ($tests as $t): ?>
                <div class="mb-8 md:mb-12 text-center scroll-reveal">
                    <p class="text-base sm:text-lg md:text-2xl lg:text-3xl italic text-zinc-300 mb-4 md:mb-6">"<?php echo htmlspecialchars($t['quote']); ?>"</p>
                    <h4 class="text-emerald-500 font-bold uppercase tracking-widest text-xs sm:text-sm md:text-base">— <?php echo htmlspecialchars($t['client_name']); ?></h4>
                </div>
            <?php endforeach; ?>
        </div>
    </section>

    <?php include 'includes/footer.php'; ?>

    <script>
        const lenis = new Lenis();
        function raf(time) { lenis.raf(time); requestAnimationFrame(raf); }
        requestAnimationFrame(raf);

        // Smart Navbar Hide/Show
        let lastScrollY = window.scrollY;
        const nav = document.getElementById('main-nav');
        
        window.addEventListener('scroll', () => {
            if (window.scrollY > lastScrollY && window.scrollY > 100) {
                nav.style.transform = 'translateY(-100%)';
            } else {
                nav.style.transform = 'translateY(0)';
            }
            lastScrollY = window.scrollY;
        });

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
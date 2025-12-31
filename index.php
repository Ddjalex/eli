<?php
require_once 'includes/config.php';
$stmt = $pdo->prepare("SELECT value FROM site_settings WHERE key = ?");
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
        .hero-bg-image { 
            background-image: url('<?php echo $hero_bg; ?>');
            background-size: cover; background-position: center; filter: brightness(0.4);
        }
        .scroll-reveal { opacity: 0; transform: scale(0.95); }
    </style>
</head>
<body class="bg-black text-white selection:bg-emerald-500">
    <nav class="fixed w-full z-50 p-6 flex justify-between items-center bg-black/50 backdrop-blur-md border-b border-white/10">
        <div class="text-2xl font-bold tracking-tighter uppercase text-emerald-500">Eleni Mekuria</div>
        <div class="hidden md:flex space-x-8 uppercase text-[10px] tracking-widest font-bold">
            <a href="#home" class="hover:text-emerald-400">Home</a>
            <a href="#about" class="hover:text-emerald-400">About</a>
            <a href="#services" class="hover:text-emerald-400">Services</a>
            <a href="#testimonials" class="hover:text-emerald-400">Success Stories</a>
            <a href="#contact" class="hover:text-emerald-400">Contact</a>
            <a href="login.php" class="bg-emerald-600 text-white px-6 py-2 rounded-full hover:bg-emerald-500 transition-all">Portal</a>
        </div>
    </nav>

    <div id="home" class="relative h-screen flex items-center justify-center overflow-hidden">
        <div class="absolute inset-0 hero-bg-image"></div>
        <div class="relative z-10 text-center px-4">
            <h1 class="text-7xl md:text-9xl font-black uppercase tracking-tighter mb-4 leading-none">
                Fuel Your <br><span class="text-emerald-500">Potential</span>
            </h1>
            <p class="text-xl tracking-[0.3em] uppercase text-zinc-400 mb-8"><?php echo $about_philosophy; ?></p>
            <a href="register.php" class="bg-white text-black px-10 py-4 rounded-full font-black uppercase tracking-widest hover:bg-emerald-500 hover:text-white transition-all transform hover:scale-105 inline-block">Start Your Journey</a>
        </div>
    </div>

    <section id="about" class="py-32 px-6 md:px-24 bg-zinc-950">
        <div class="grid md:grid-cols-2 gap-16 items-center">
            <div class="scroll-reveal">
                <h2 class="text-5xl font-bold mb-8 uppercase tracking-tighter">About <span class="text-emerald-500">Eleni</span></h2>
                <p class="text-xl text-zinc-400 leading-relaxed mb-6"><?php echo $about_bio; ?></p>
                <div class="border-l-4 border-emerald-500 pl-6 italic text-lg mb-8">
                    "My approach blends rigorous nutritional science with deep clinical empathy."
                </div>
            </div>
            <div class="aspect-[4/5] rounded-3xl overflow-hidden border border-white/10 scroll-reveal">
                <img src="<?php echo $about_img; ?>" class="w-full h-full object-cover grayscale hover:grayscale-0 transition-all duration-700">
            </div>
        </div>
    </section>

    <section id="services" class="py-32 bg-black px-6">
        <h2 class="text-5xl font-bold text-center mb-20 uppercase tracking-tighter">Premium <span class="text-emerald-500">Services</span></h2>
        <div class="grid md:grid-cols-3 gap-8 max-w-6xl mx-auto">
            <div class="bg-zinc-900/50 p-10 rounded-3xl border border-white/5 hover:border-emerald-500/50 transition-all flex flex-col">
                <div class="text-4xl mb-6">🤝</div>
                <h3 class="text-xl font-bold mb-4 uppercase">1-on-1 Counseling</h3>
                <p class="text-zinc-500 text-sm mb-8">Personalized Online & In-person sessions tailored to your unique biology.</p>
                <div class="mt-auto flex gap-4">
                    <a href="service-detail.php?id=counseling" class="inline-block border border-white/10 text-white px-6 py-3 rounded-xl font-bold uppercase text-[10px] tracking-widest hover:bg-white hover:text-black transition-all">Details</a>
                    <a href="register.php" class="inline-block bg-emerald-600 text-white px-6 py-3 rounded-xl font-bold uppercase text-[10px] tracking-widest hover:bg-emerald-500 transition-all">Join Now</a>
                </div>
            </div>
            <div class="bg-zinc-900/50 p-10 rounded-3xl border border-white/5 hover:border-emerald-500/50 transition-all flex flex-col">
                <div class="text-4xl mb-6">📋</div>
                <h3 class="text-xl font-bold mb-4 uppercase">Custom Meal Planning</h3>
                <p class="text-zinc-500 text-sm mb-8">Science-backed protocols for weight loss, muscle gain, or performance.</p>
                <div class="mt-auto flex gap-4">
                    <a href="service-detail.php?id=meal-planning" class="inline-block border border-white/10 text-white px-6 py-3 rounded-xl font-bold uppercase text-[10px] tracking-widest hover:bg-white hover:text-black transition-all">Details</a>
                    <a href="register.php" class="inline-block bg-emerald-600 text-white px-6 py-3 rounded-xl font-bold uppercase text-[10px] tracking-widest hover:bg-emerald-500 transition-all">Join Now</a>
                </div>
            </div>
            <div class="bg-zinc-900/50 p-10 rounded-3xl border border-white/5 hover:border-emerald-500/50 transition-all flex flex-col">
                <div class="text-4xl mb-6">🏢</div>
                <h3 class="text-xl font-bold mb-4 uppercase">Corporate Wellness</h3>
                <p class="text-zinc-500 text-sm mb-8">Group coaching and wellness strategy for high-performance teams.</p>
                <div class="mt-auto flex gap-4">
                    <a href="service-detail.php?id=wellness" class="inline-block border border-white/10 text-white px-6 py-3 rounded-xl font-bold uppercase text-[10px] tracking-widest hover:bg-white hover:text-black transition-all">Details</a>
                    <a href="register.php" class="inline-block bg-emerald-600 text-white px-6 py-3 rounded-xl font-bold uppercase text-[10px] tracking-widest hover:bg-emerald-500 transition-all">Join Now</a>
                </div>
            </div>
        </div>
    </section>

    <section id="testimonials" class="py-32 bg-zinc-950 px-6">
        <h2 class="text-5xl font-bold text-center mb-20 uppercase tracking-tighter">Success <span class="text-emerald-500">Stories</span></h2>
        <div class="max-w-4xl mx-auto">
            <?php
            $tests = $pdo->query("SELECT * FROM testimonials")->fetchAll();
            foreach ($tests as $t): ?>
                <div class="mb-12 text-center scroll-reveal">
                    <p class="text-2xl md:text-3xl italic text-zinc-300 mb-6">"<?php echo htmlspecialchars($t['quote']); ?>"</p>
                    <h4 class="text-emerald-500 font-bold uppercase tracking-widest">— <?php echo htmlspecialchars($t['client_name']); ?></h4>
                </div>
            <?php endforeach; ?>
        </div>
    </section>

    <footer id="contact" class="py-24 bg-black border-t border-white/10 px-6">
        <div class="max-w-7xl mx-auto grid grid-cols-1 md:grid-cols-4 gap-12 text-left">
            <!-- Address List -->
            <div>
                <h3 class="text-xs font-bold uppercase tracking-[0.3em] text-zinc-500 mb-8">Address List</h3>
                <div class="space-y-6">
                    <div class="flex items-start gap-4">
                        <span class="text-emerald-500 text-xl">📍</span>
                        <p class="text-zinc-400 text-sm leading-relaxed">
                            <?php echo $pdo->query("SELECT value FROM site_settings WHERE key = 'footer_address'")->fetchColumn() ?: 'Addis Ababa, Ethiopia'; ?>
                        </p>
                    </div>
                    <div class="flex items-center gap-4">
                        <span class="text-emerald-500 text-xl">📱</span>
                        <p class="text-zinc-400 text-sm font-bold">
                            <?php echo $pdo->query("SELECT value FROM site_settings WHERE key = 'contact_phone'")->fetchColumn() ?: '+251 911 000 000'; ?>
                        </p>
                    </div>
                    <div class="flex items-center gap-4">
                        <span class="text-emerald-500 text-xl">✉️</span>
                        <p class="text-zinc-400 text-sm">
                            <?php echo $pdo->query("SELECT value FROM site_settings WHERE key = 'footer_email'")->fetchColumn() ?: 'info@elenimekuria.com'; ?>
                        </p>
                    </div>
                </div>
            </div>

            <!-- Quick Links -->
            <div>
                <h3 class="text-xs font-bold uppercase tracking-[0.3em] text-zinc-500 mb-8">Quick Links</h3>
                <ul class="space-y-4 text-sm">
                    <li><a href="#home" class="text-zinc-400 hover:text-emerald-500 transition-all uppercase tracking-widest font-bold text-[10px]">Home</a></li>
                    <li><a href="#about" class="text-zinc-400 hover:text-emerald-500 transition-all uppercase tracking-widest font-bold text-[10px]">About Us</a></li>
                    <li><a href="#services" class="text-zinc-400 hover:text-emerald-500 transition-all uppercase tracking-widest font-bold text-[10px]">Our Services</a></li>
                    <li><a href="#testimonials" class="text-zinc-400 hover:text-emerald-500 transition-all uppercase tracking-widest font-bold text-[10px]">Success Stories</a></li>
                </ul>
            </div>

            <!-- Social Networks -->
            <div>
                <h3 class="text-xs font-bold uppercase tracking-[0.3em] text-zinc-500 mb-8">Social Networks</h3>
                <ul class="space-y-4 text-sm">
                    <li><a href="<?php echo $pdo->query("SELECT value FROM site_settings WHERE key = 'footer_tiktok'")->fetchColumn() ?: '#'; ?>" class="text-zinc-400 hover:text-emerald-500 transition-all flex items-center gap-3 uppercase tracking-widest font-bold text-[10px]"><span>🎵</span> Tiktok</a></li>
                    <li><a href="<?php echo $pdo->query("SELECT value FROM site_settings WHERE key = 'contact_social_ig'")->fetchColumn() ?: '#'; ?>" class="text-zinc-400 hover:text-emerald-500 transition-all flex items-center gap-3 uppercase tracking-widest font-bold text-[10px]"><span>📸</span> Instagram</a></li>
                    <li><a href="<?php echo $pdo->query("SELECT value FROM site_settings WHERE key = 'contact_whatsapp_link'")->fetchColumn() ?: '#'; ?>" class="text-zinc-400 hover:text-emerald-500 transition-all flex items-center gap-3 uppercase tracking-widest font-bold text-[10px]"><span>💬</span> Whatsapp</a></li>
                    <li><a href="<?php echo $pdo->query("SELECT value FROM site_settings WHERE key = 'footer_telegram'")->fetchColumn() ?: '#'; ?>" class="text-zinc-400 hover:text-emerald-500 transition-all flex items-center gap-3 uppercase tracking-widest font-bold text-[10px]"><span>✈️</span> Telegram</a></li>
                </ul>
            </div>

            <!-- Branding -->
            <div class="flex flex-col items-start">
                <div class="text-3xl font-black text-emerald-500 uppercase tracking-tighter mb-4">Eleni</div>
                <p class="text-zinc-500 text-xs italic leading-relaxed mb-8">
                    Elevating the standard of nutritional health in Ethiopia through science and empathy.
                </p>
                <a href="#home" class="bg-zinc-900 border border-white/10 p-3 rounded-xl hover:bg-emerald-600 transition-all">
                    <span class="text-white">↑</span>
                </a>
            </div>
        </div>
        
        <div class="mt-20 pt-10 border-t border-white/5 text-center">
            <p class="text-zinc-600 text-[10px] uppercase tracking-[0.4em] font-bold">
                Copyright &copy; 2026 Eleni Mekuria. All Rights Reserved.
            </p>
        </div>
    </footer>

    <script>
        const lenis = new Lenis();
        function raf(time) { lenis.raf(time); requestAnimationFrame(raf); }
        requestAnimationFrame(raf);

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
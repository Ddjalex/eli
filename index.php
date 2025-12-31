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
        :root {
            --accent-gold: #fbbf24;
            --accent-green: #10b981;
        }
        .luxury-gradient { background: linear-gradient(135deg, #1a1a1a 0%, #064e3b 100%); }
        .hero-video-container { position: relative; height: 100vh; overflow: hidden; }
        .hero-bg-image { 
            position: absolute; 
            top: 0; left: 0; 
            width: 100%; height: 100%; 
            background-image: url('attached_assets/stock_images/modern_nutrition_hea_7726d1af.jpg');
            background-size: cover;
            background-position: center;
            filter: brightness(0.4);
        }
        .animate-text { opacity: 0; transform: translateY(30px); }
        .scroll-reveal { opacity: 0; transform: scale(0.95); }
    </style>
</head>
<body class="bg-black text-white selection:bg-emerald-500 selection:text-white">
    <nav class="fixed w-full z-50 p-6 flex justify-between items-center bg-black/50 backdrop-blur-md border-b border-white/10">
        <div class="text-2xl font-bold tracking-tighter uppercase text-emerald-500">Eleni Mekuria</div>
        <div class="hidden md:flex space-x-8 uppercase text-xs tracking-widest font-bold">
            <a href="#about" class="hover:text-emerald-400 transition-colors">About</a>
            <a href="#media" class="hover:text-emerald-400 transition-colors">Media</a>
            <a href="#testimonials" class="hover:text-emerald-400 transition-colors">Success Stories</a>
            <a href="login.php" class="bg-emerald-600 text-white px-6 py-2 rounded-full hover:bg-emerald-500 transition-all">Client Portal</a>
        </div>
    </nav>

    <div class="hero-video-container flex items-center justify-center">
        <div class="hero-bg-image"></div>
        <div class="relative z-10 text-center px-4">
            <h1 class="hero-title text-7xl md:text-9xl font-black uppercase tracking-tighter mb-4 leading-none">
                Fuel Your <br><span class="text-emerald-500">Potential</span>
            </h1>
            <p class="hero-subtitle text-xl tracking-[0.3em] uppercase text-zinc-400 mb-8">MSc Dietitian | Sports Nutrition Specialist</p>
            <div class="flex flex-col md:flex-row gap-4 justify-center items-center">
                <a href="register.php" class="bg-white text-black px-10 py-4 rounded-full font-black uppercase tracking-widest hover:bg-emerald-500 hover:text-white transition-all transform hover:scale-105">Start Your Journey</a>
            </div>
        </div>
        <div class="absolute bottom-10 left-1/2 -translate-x-1/2 animate-bounce opacity-50">
            <div class="w-6 h-10 border-2 border-white rounded-full flex justify-center p-1">
                <div class="w-1 h-2 bg-white rounded-full"></div>
            </div>
        </div>
    </div>

    <section id="about" class="min-h-screen flex items-center py-24 px-6 md:px-24 bg-zinc-950 relative overflow-hidden">
        <div class="absolute top-0 right-0 w-1/3 h-full bg-emerald-900/10 blur-[120px]"></div>
        <div class="grid md:grid-cols-2 gap-16 items-center relative z-10">
            <div class="scroll-reveal">
                <h2 class="text-6xl font-bold mb-10 uppercase tracking-tighter leading-tight">Science-Backed<br><span class="text-emerald-500">Nutrition</span></h2>
                <div class="space-y-6 text-xl text-zinc-400 leading-relaxed">
                    <p>
                        With a Master's from Addis Ababa University and a Bachelor's from AASTU, Eleni Mekuria combines academic rigor with practical clinical experience.
                    </p>
                    <p class="border-l-4 border-emerald-500 pl-6 italic text-white">
                        "Nutrition is not just about what you eat; it's about empowering your body to perform at its peak."
                    </p>
                    <p>
                        From Afrihealth TV hosting to founding the Ethiopian Dietetic Professionals Association, her mission is to elevate the standard of health in Ethiopia.
                    </p>
                </div>
            </div>
            <div class="scroll-reveal relative group">
                <div class="absolute -inset-4 bg-emerald-500/20 rounded-2xl blur-xl group-hover:bg-emerald-500/30 transition-all"></div>
                <div class="aspect-[4/5] rounded-2xl overflow-hidden border border-white/10 relative">
                    <img src="attached_assets/stock_images/professional_dietiti_8bb8decd.jpg" alt="Eleni Mekuria" class="w-full h-full object-cover grayscale hover:grayscale-0 transition-all duration-700">
                </div>
                <div class="absolute -bottom-6 -left-6 bg-emerald-600 p-8 rounded-xl shadow-2xl">
                    <span class="text-5xl font-black block">400+</span>
                    <span class="uppercase tracking-widest text-xs font-bold">Clients Transformed</span>
                </div>
            </div>
        </div>
    </section>

    <section id="features" class="py-32 bg-black border-y border-white/5">
        <div class="container mx-auto px-6">
            <div class="grid md:grid-cols-3 gap-12">
                <div class="p-10 bg-zinc-900/50 rounded-3xl border border-white/5 hover:border-emerald-500/50 transition-all group">
                    <div class="text-emerald-500 text-4xl mb-6 group-hover:scale-110 transition-transform">🥗</div>
                    <h3 class="text-2xl font-bold mb-4 uppercase">Weight Management</h3>
                    <p class="text-zinc-500 leading-relaxed">Sustainable strategies for fat loss and healthy weight maintenance tailored to your metabolism.</p>
                </div>
                <div class="p-10 bg-zinc-900/50 rounded-3xl border border-white/5 hover:border-emerald-500/50 transition-all group">
                    <div class="text-emerald-500 text-4xl mb-6 group-hover:scale-110 transition-transform">💪</div>
                    <h3 class="text-2xl font-bold mb-4 uppercase">Sports Performance</h3>
                    <p class="text-zinc-500 leading-relaxed">Elite nutrition plans for athletes to optimize recovery, energy levels, and competition ready results.</p>
                </div>
                <div class="p-10 bg-zinc-900/50 rounded-3xl border border-white/5 hover:border-emerald-500/50 transition-all group">
                    <div class="text-emerald-500 text-4xl mb-6 group-hover:scale-110 transition-transform">🏥</div>
                    <h3 class="text-2xl font-bold mb-4 uppercase">Clinical Therapy</h3>
                    <p class="text-zinc-500 leading-relaxed">Specialized dietary management for diabetes, hypertension, and other medical conditions.</p>
                </div>
            </div>
        </div>
    </section>

    <footer class="py-20 bg-black text-center relative overflow-hidden">
        <div class="absolute inset-0 bg-emerald-900/5 blur-[100px]"></div>
        <div class="relative z-10">
            <h2 class="text-3xl font-bold uppercase tracking-widest mb-10 text-emerald-500">Ready for Change?</h2>
            <a href="register.php" class="inline-block border border-white/20 px-12 py-4 rounded-full uppercase tracking-widest text-sm font-bold hover:bg-white hover:text-black transition-all">Join the Program</a>
            <div class="mt-20 text-zinc-600 text-xs tracking-[0.5em] uppercase">
                &copy; 2025 Eleni Mekuria | Premium Nutrition
            </div>
        </div>
    </footer>

    <script>
        // Smooth scrolling initialization
        const lenis = new Lenis({
            duration: 1.2,
            easing: (t) => Math.min(1, 1.001 - Math.pow(2, -10 * t)),
            smoothWheel: true
        });
        function raf(time) {
            lenis.raf(time);
            requestAnimationFrame(raf);
        }
        requestAnimationFrame(raf);

        // GSAP Animations
        gsap.registerPlugin(ScrollTrigger);

        // Hero Animation
        const tl = gsap.timeline();
        tl.from(".hero-title", { 
            y: 150, 
            opacity: 0, 
            duration: 1.8, 
            ease: "expo.out",
            skewY: 7 
        })
          .from(".hero-subtitle", { 
              y: 50, 
              opacity: 0, 
              duration: 1.2, 
              ease: "power3.out" 
          }, "-=1.2")
          .from(".hero-bg-image", { 
              scale: 1.5, 
              duration: 4, 
              ease: "power2.out" 
          }, 0);

        // Advanced Scroll Animations
        gsap.utils.toArray(".scroll-reveal").forEach(el => {
            gsap.fromTo(el, 
                { opacity: 0, y: 100, scale: 0.9 },
                {
                    scrollTrigger: {
                        trigger: el,
                        start: "top 90%",
                        end: "top 20%",
                        scrub: 1,
                        toggleActions: "play none none reverse"
                    },
                    opacity: 1,
                    y: 0,
                    scale: 1,
                    duration: 1.5,
                    ease: "power4.out"
                }
            );
        });

        // Feature Card staggered animation
        gsap.from("#features .grid > div", {
            scrollTrigger: {
                trigger: "#features",
                start: "top 80%"
            },
            y: 100,
            opacity: 0,
            duration: 1,
            stagger: 0.2,
            ease: "power3.out"
        });

        // Navigation Highlight
        gsap.to("nav", {
            scrollTrigger: {
                trigger: "body",
                start: "top -100",
                toggleActions: "play none none reverse"
            },
            backgroundColor: "rgba(0,0,0,0.9)",
            padding: "1rem 1.5rem",
            duration: 0.3
        });
    </script>
</body>
</html>
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
        .luxury-gradient { background: linear-gradient(135deg, #1a1a1a 0%, #333 100%); }
        .hero-video-container { position: relative; height: 100vh; overflow: hidden; }
        .hero-video { position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); min-width: 100%; min-height: 100%; object-fit: cover; }
    </style>
</head>
<body class="bg-black text-white">
    <nav class="fixed w-full z-50 p-6 flex justify-between items-center bg-black/50 backdrop-blur-md">
        <div class="text-2xl font-bold tracking-tighter uppercase">Eleni Mekuria</div>
        <div class="space-x-8">
            <a href="#about" class="hover:text-gray-400">About</a>
            <a href="#media" class="hover:text-gray-400">Media</a>
            <a href="#testimonials" class="hover:text-gray-400">Success Stories</a>
            <a href="login.php" class="bg-white text-black px-6 py-2 rounded-full font-bold">Client Portal</a>
        </div>
    </nav>

    <div class="hero-video-container">
        <!-- Placeholder for Afrihealth TV video -->
        <div class="hero-video bg-zinc-900 flex items-center justify-center">
            <h1 class="text-6xl md:text-8xl font-black text-center uppercase tracking-tighter">Empowering<br>Health</h1>
        </div>
        <div class="absolute inset-0 bg-black/40 flex flex-col items-center justify-end pb-20">
            <p class="text-xl tracking-widest uppercase mb-4">MSc Dietitian | 400+ Clients Helped</p>
            <div class="animate-bounce text-3xl">↓</div>
        </div>
    </div>

    <section id="about" class="min-h-screen flex items-center p-12 lg:p-24 bg-zinc-950">
        <div class="grid md:grid-cols-2 gap-12 items-center">
            <div>
                <h2 class="text-5xl font-bold mb-8 uppercase tracking-tighter">Meet Eleni</h2>
                <p class="text-xl text-gray-400 leading-relaxed mb-6">
                    A highly accomplished dietitian with an MSc from AAU and BSc from AASTU. 
                    Clinical experience at St. Paul’s Millennium Medical College and Black Lion Hospital.
                </p>
                <p class="text-lg text-gray-500">
                    Host of "ኑሮ በዘዴ" on Afrihealth TV. Founding member of the Ethiopian Dietetic Professionals Association.
                </p>
            </div>
            <div class="bg-zinc-900 h-[600px] rounded-2xl overflow-hidden flex items-center justify-center border border-zinc-800">
                 <span class="text-zinc-700">Portfolio Image Placeholder</span>
            </div>
        </div>
    </section>

    <section id="media" class="py-24 bg-black">
        <div class="text-center mb-16">
            <h2 class="text-4xl font-bold uppercase tracking-widest">Media & PR</h2>
        </div>
        <div class="flex overflow-x-auto gap-8 px-12 pb-12 snap-x">
            <!-- Media cards -->
            <div class="min-w-[300px] h-48 bg-zinc-900 border border-zinc-800 rounded-lg flex items-center justify-center snap-center">Afrihealth TV</div>
            <div class="min-w-[300px] h-48 bg-zinc-900 border border-zinc-800 rounded-lg flex items-center justify-center snap-center">EDPA Member</div>
            <div class="min-w-[300px] h-48 bg-zinc-900 border border-zinc-800 rounded-lg flex items-center justify-center snap-center">Health Expert</div>
        </div>
    </section>

    <footer class="p-12 border-t border-zinc-900 text-center text-zinc-600">
        <p>&copy; 2025 Eleni Mekuria. Premium Nutrition Coaching.</p>
    </footer>

    <script>
        const lenis = new Lenis();
        function raf(time) {
            lenis.raf(time);
            requestAnimationFrame(raf);
        }
        requestAnimationFrame(raf);

        gsap.registerPlugin(ScrollTrigger);
        gsap.from("#about h2", {
            scrollTrigger: "#about",
            y: 100,
            opacity: 0,
            duration: 1
        });
    </script>
</body>
</html>
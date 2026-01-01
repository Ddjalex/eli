<?php
require_once 'includes/config.php';
$service_id = $_GET['id'] ?? '';

$services = [
    'counseling' => [
        'title' => '1-on-1 Nutrition Counseling',
        'icon' => '🤝',
        'description' => 'Experience a transformation guided by clinical expertise and personal empathy. Our 1-on-1 sessions are designed to uncover the root causes of your nutritional challenges.',
        'features' => [
            'Metabolic health assessment',
            'Hormonal balance optimization',
            'Personalized supplement protocols',
            'Weekly check-ins and adjustments',
            'Telehealth or In-person options'
        ]
    ],
    'meal-planning' => [
        'title' => 'Custom Meal Planning',
        'icon' => '📋',
        'description' => 'Move beyond generic diets. Get a science-backed nutritional protocol specifically calibrated for your unique biology and lifestyle goals.',
        'features' => [
            'Calorie & Macro precision',
            'Local Ethiopian food integration',
            'Shopping lists & Prep guides',
            'Recipe vault access',
            'Performance-focused timing'
        ]
    ],
    'wellness' => [
        'title' => 'Corporate Wellness',
        'icon' => '🏢',
        'description' => 'Elevate your team\'s performance through evidence-based nutrition. We provide high-impact wellness strategies for modern organizations.',
        'features' => [
            'Group nutrition workshops',
            'Executive health coaching',
            'Office pantry optimization',
            'Energy management seminars',
            'Measurable health ROI tracking'
        ]
    ]
];

$service = $services[$service_id] ?? null;

if (!$service) {
    header("Location: index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $service['title']; ?> | Eleni Mekuria</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-black text-white selection:bg-emerald-500 min-h-screen flex flex-col">
    <nav class="p-6 flex justify-between items-center bg-black/50 backdrop-blur-md border-b border-white/10 sticky top-0 z-50">
        <a href="index.php" class="text-2xl font-bold tracking-tighter uppercase text-emerald-500">Eleni Mekuria</a>
        <a href="index.php" class="text-xs font-bold uppercase tracking-widest text-zinc-500 hover:text-white transition-all">Back to Home</a>
    </nav>

    <main class="flex-grow py-24 px-6 md:px-24">
        <div class="max-w-4xl mx-auto">
            <div class="text-6xl mb-10"><?php echo $service['icon']; ?></div>
            <h1 class="text-5xl md:text-7xl font-black uppercase tracking-tighter mb-8 leading-none">
                <?php echo $service['title']; ?>
            </h1>
            
            <div class="grid md:grid-cols-2 gap-16">
                <div>
                    <p class="text-xl text-zinc-400 leading-relaxed mb-10">
                        <?php echo $service['description']; ?>
                    </p>
                    <a href="register.php" class="inline-block bg-emerald-600 text-white px-10 py-5 rounded-2xl font-black uppercase tracking-widest hover:bg-emerald-500 transition-all shadow-lg shadow-emerald-900/20">
                        Join the Program
                    </a>
                </div>
                
                <div class="bg-zinc-900/50 p-10 rounded-3xl border border-white/5">
                    <h3 class="text-xs font-bold uppercase tracking-[0.3em] text-emerald-500 mb-8">What's Included</h3>
                    <ul class="space-y-6">
                        <?php foreach ($service['features'] as $feature): ?>
                            <li class="flex items-start gap-4 text-zinc-300">
                                <span class="text-emerald-500">✓</span>
                                <span class="text-sm uppercase tracking-wide"><?php echo $feature; ?></span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </div>
    </main>

    <footer class="py-10 border-t border-white/5 text-center text-[10px] text-zinc-600 uppercase tracking-widest">
        &copy; 2025 Eleni Mekuria. All Rights Reserved.
    </footer>
</body>
</html>
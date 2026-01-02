<?php
require_once 'includes/config.php';

$id = $_GET['id'] ?? '';
$plan = null;

if ($id === 'counseling') {
    $plan = [
        'title' => '1-on-1 Nutrition Counseling',
        'description' => 'Experience a transformation guided by clinical expertise and personal empathy. Our 1-on-1 sessions are designed to uncover the root causes of your nutritional challenges.',
        'price' => 'Custom',
        'features' => [
            'Metabolic Health Assessment',
            'Hormonal Balance Optimization',
            'Personalized Supplement Protocols',
            'Weekly Check-ins and Adjustments',
            'Telehealth or In-person Options'
        ],
        'button_text' => 'Join the Program',
        'button_link' => 'register.php'
    ];
} else {
    $stmt = $pdo->prepare("SELECT * FROM meal_plans WHERE id = ?");
    $stmt->execute([(int)$id]);
    $db_plan = $stmt->fetch();

    if ($db_plan) {
        $features = [
            'Professionally Calculated Macros',
            'Grocery Shopping Lists',
            'Easy-to-Follow Recipes',
            'Nutrition Education Modules',
            'Mobile-Friendly PDF Access'
        ];
        
        $plan = [
            'title' => $db_plan['title'],
            'description' => $db_plan['description'] ?: "Achieve your " . str_replace('_', ' ', $db_plan['package_type']) . " goals with our scientifically backed nutrition strategy.",
            'price' => '$' . number_format($db_plan['price'], 2),
            'features' => $features,
            'button_text' => 'Join Now',
            'button_link' => 'register.php?plan=' . $db_plan['id'],
            'preview_link' => 'preview.php?id=' . $db_plan['id']
        ];
    }
}

if (!$plan) {
    header("Location: index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($plan['title']); ?> | Eleni Mekuria</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #000; color: #fff; }
        .glass-card { 
            background: rgba(20, 20, 20, 0.6); 
            backdrop-filter: blur(20px); 
            border: 1px solid rgba(255, 255, 255, 0.05); 
        }
        .text-glow { text-shadow: 0 0 30px rgba(16, 185, 129, 0.3); }
        .feature-item { transition: all 0.3s ease; }
        .feature-item:hover { transform: translateX(10px); color: #10b981; }
    </style>
</head>
<body class="selection:bg-emerald-500/30">
    <!-- Navigation Overlay -->
    <nav class="fixed w-full z-50 p-6 flex justify-between items-center pointer-events-none">
        <div class="text-emerald-500 font-bold tracking-tighter uppercase pointer-events-auto">Eleni Mekuria</div>
        <a href="index.php" class="text-zinc-500 hover:text-white uppercase text-[10px] tracking-[0.2em] font-bold transition-all pointer-events-auto">Back to Home</a>
    </nav>

    <main class="min-h-screen flex items-center justify-center p-6 relative overflow-hidden">
        <!-- Abstract Background Elements -->
        <div class="absolute top-[-10%] left-[-10%] w-[40%] h-[40%] bg-emerald-500/10 rounded-full blur-[120px]"></div>
        <div class="absolute bottom-[-10%] right-[-10%] w-[30%] h-[30%] bg-emerald-500/5 rounded-full blur-[100px]"></div>

        <div class="max-w-6xl w-full grid lg:grid-cols-2 gap-16 items-center relative z-10">
            <!-- Left Content -->
            <div class="space-y-10">
                <h1 class="text-5xl md:text-7xl font-extrabold uppercase tracking-tighter leading-[0.9] text-glow">
                    <?php echo htmlspecialchars($plan['title']); ?>
                </h1>
                
                <p class="text-lg md:text-xl text-zinc-400 leading-relaxed max-w-xl">
                    <?php echo htmlspecialchars($plan['description']); ?>
                </p>

                <div class="flex flex-wrap gap-4 pt-4">
                    <a href="<?php echo $plan['button_link']; ?>" class="bg-emerald-600 text-white px-10 py-5 rounded-2xl font-black uppercase tracking-widest hover:bg-emerald-500 transition-all transform hover:scale-105 shadow-xl shadow-emerald-900/20">
                        <?php echo htmlspecialchars($plan['button_text']); ?>
                    </a>
                </div>
            </div>

            <!-- Right Content: Features List -->
            <div class="glass-card p-10 md:p-16 rounded-[3rem] space-y-12">
                <div>
                    <h3 class="text-emerald-500 text-[10px] font-black uppercase tracking-[0.3em] mb-6">What's Included</h3>
                    <ul class="space-y-6">
                        <?php foreach ($plan['features'] as $feature): ?>
                            <li class="flex items-center gap-4 text-zinc-300 font-medium group feature-item">
                                <span class="text-emerald-500">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                </span>
                                <span class="uppercase text-sm tracking-widest"><?php echo htmlspecialchars($feature); ?></span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>

                <div class="pt-8 border-t border-white/5 flex justify-between items-center">
                    <span class="text-zinc-500 text-[10px] font-bold uppercase tracking-widest">Investment</span>
                    <span class="text-2xl font-black text-white uppercase tracking-tighter"><?php echo $plan['price']; ?></span>
                </div>
            </div>
        </div>
    </main>
</body>
</html>
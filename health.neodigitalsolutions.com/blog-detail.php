<?php
require_once 'includes/config.php';

$id = $_GET['id'] ?? null;
if (!$id) {
    header("Location: blog.php");
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM blogs WHERE id = ?");
$stmt->execute([$id]);
$blog = $stmt->fetch();

if (!$blog) {
    header("Location: blog.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($blog['title']); ?> | Eleni Mekuria</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-black text-white">
    <nav id="main-nav" class="fixed w-full z-50 p-3 sm:p-4 md:p-6 flex justify-between items-center bg-black/50 backdrop-blur-md border-b border-white/10">
        <div class="text-lg sm:text-xl md:text-2xl font-bold tracking-tighter uppercase text-emerald-500">Eleni</div>
        <div class="hidden md:flex space-x-4 lg:space-x-8 uppercase text-[9px] lg:text-[10px] tracking-widest font-bold">
            <a href="index.php#home" class="hover:text-emerald-400">Home</a>
            <a href="index.php#about" class="hover:text-emerald-400">About</a>
            <a href="portfolio.php" class="hover:text-emerald-400">Portfolio</a>
            <a href="blog.php" class="hover:text-emerald-400 text-emerald-500">Blog</a>
            <a href="index.php#services" class="hover:text-emerald-400">Services</a>
            <a href="index.php#testimonials" class="hover:text-emerald-400">Stories</a>
            <a href="index.php#contact" class="hover:text-emerald-400">Contact</a>
            <a href="login.php" class="bg-emerald-600 text-white px-4 sm:px-6 py-2 rounded-full hover:bg-emerald-500 transition-all">Portal</a>
        </div>
        <a href="login.php" class="md:hidden bg-emerald-600 text-white px-3 py-1 rounded-full hover:bg-emerald-500 transition-all text-[9px]">Portal</a>
    </nav>

    <main class="py-32 px-6 max-w-4xl mx-auto">
        <a href="blog.php" class="text-emerald-500 uppercase tracking-widest text-xs font-bold mb-8 inline-block">← Back to Blog</a>
        
        <?php if ($blog['image_url']): ?>
            <img src="<?php echo htmlspecialchars($blog['image_url']); ?>" class="w-full aspect-video object-cover rounded-3xl mb-12 border border-white/10">
        <?php endif; ?>

        <h1 class="text-4xl md:text-6xl font-bold mb-8 uppercase tracking-tighter leading-tight">
            <?php echo htmlspecialchars($blog['title']); ?>
        </h1>

        <div class="flex items-center gap-4 mb-12 pb-12 border-b border-white/10">
            <?php
            $stmt_img = $pdo->prepare("SELECT value FROM site_settings WHERE `key` = 'about_image'");
            $stmt_img->execute();
            $admin_img = $stmt_img->fetchColumn() ?: 'attached_assets/stock_images/professional_dietiti_8bb8decd.jpg';
            ?>
            <img src="<?php echo htmlspecialchars($admin_img); ?>" class="w-12 h-12 rounded-full object-cover border border-emerald-500/30">
            <div>
                <div class="font-bold uppercase tracking-widest text-xs">Eleni Mekuria</div>
                <div class="text-zinc-500 text-xs"><?php echo date('M d, Y', strtotime($blog['created_at'])); ?></div>
            </div>
        </div>

        <div class="prose prose-invert max-w-none text-zinc-400 text-lg leading-relaxed space-y-6">
            <?php echo nl2br(htmlspecialchars($blog['content'])); ?>
        </div>
    </main>

    <?php include 'includes/footer.php'; ?>
</body>
</html>
<footer id="contact" class="py-12 sm:py-16 md:py-24 bg-black border-t border-white/10 px-6 sm:px-8 mt-20 relative z-10">
    <div class="max-w-7xl mx-auto grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-10 sm:gap-12 text-left">
        <!-- Address List -->
        <div>
            <h3 class="text-[9px] sm:text-xs font-bold uppercase tracking-[0.3em] text-zinc-500 mb-4 sm:mb-8">Address List</h3>
            <div class="space-y-4 sm:space-y-6">
                <div class="flex items-start gap-3 sm:gap-4">
                    <span class="text-emerald-500 text-lg sm:text-xl flex-shrink-0">📍</span>
                    <p class="text-zinc-400 text-xs sm:text-sm leading-relaxed">
                        <?php 
                        $stmt_addr = $pdo->prepare("SELECT value FROM site_settings WHERE \"key\" = 'footer_address'");
                        $stmt_addr->execute();
                        $val = $stmt_addr->fetchColumn();
                        echo htmlspecialchars($val ?: 'Addis Ababa, Ethiopia'); 
                        ?>
                    </p>
                </div>
                <div class="flex items-center gap-3 sm:gap-4">
                    <span class="text-emerald-500 text-lg sm:text-xl flex-shrink-0">📱</span>
                    <?php 
                        $stmt_phone = $pdo->prepare("SELECT value FROM site_settings WHERE \"key\" = 'contact_phone'");
                        $stmt_phone->execute();
                        $phone = $stmt_phone->fetchColumn();
                        if (!$phone) $phone = '+251 942 543 234';
                    ?>
                    <a href="tel:<?php echo preg_replace('/[^0-9+]/', '', $phone); ?>" class="text-zinc-400 text-xs sm:text-sm font-bold hover:text-emerald-500 transition-colors">
                        <?php echo htmlspecialchars($phone); ?>
                    </a>
                </div>
                <div class="flex items-center gap-3 sm:gap-4">
                    <span class="text-emerald-500 text-lg sm:text-xl flex-shrink-0">✉️</span>
                    <p class="text-zinc-400 text-xs sm:text-sm">
                        <?php 
                        $stmt_email = $pdo->prepare("SELECT value FROM site_settings WHERE \"key\" = 'footer_email'");
                        $stmt_email->execute();
                        $email = $stmt_email->fetchColumn();
                        echo htmlspecialchars($email ?: 'info@elenimekuria.com'); 
                        ?>
                    </p>
                </div>
            </div>
        </div>

        <!-- Quick Links -->
        <div>
            <h3 class="text-[9px] sm:text-xs font-bold uppercase tracking-[0.3em] text-zinc-500 mb-4 sm:mb-8">Quick Links</h3>
            <ul class="space-y-2 sm:space-y-4 text-sm">
                <li><a href="index.php" class="text-zinc-400 hover:text-emerald-500 transition-all uppercase tracking-widest font-bold text-[8px] sm:text-[10px]">Home</a></li>
                <li><a href="portfolio.php" class="text-zinc-400 hover:text-emerald-500 transition-all uppercase tracking-widest font-bold text-[8px] sm:text-[10px]">Portfolio</a></li>
                <li><a href="blog.php" class="text-zinc-400 hover:text-emerald-500 transition-all uppercase tracking-widest font-bold text-[8px] sm:text-[10px]">Blog</a></li>
                <li><a href="index.php#about" class="text-zinc-400 hover:text-emerald-500 transition-all uppercase tracking-widest font-bold text-[8px] sm:text-[10px]">About Us</a></li>
                <li><a href="index.php#services" class="text-zinc-400 hover:text-emerald-500 transition-all uppercase tracking-widest font-bold text-[8px] sm:text-[10px]">Our Services</a></li>
            </ul>
        </div>

        <!-- Social Networks -->
        <div>
            <h3 class="text-[9px] sm:text-xs font-bold uppercase tracking-[0.3em] text-zinc-500 mb-4 sm:mb-8">Social Networks</h3>
            <ul class="space-y-2 sm:space-y-4 text-sm">
                <?php
                $socials = [
                    'tiktok_link' => ['TikTok', 'https://www.svgrepo.com/show/333611/tiktok.svg'],
                    'instagram_link' => ['Instagram', 'https://www.svgrepo.com/show/521711/instagram.svg'],
                    'whatsapp_link' => ['Whatsapp', 'https://www.svgrepo.com/show/513060/whatsapp.svg'],
                    'telegram_link' => ['Telegram', 'https://www.svgrepo.com/show/354443/telegram.svg']
                ];
                
                $stmt_social = $pdo->prepare("SELECT value FROM site_settings WHERE \"key\" = ?");
                foreach ($socials as $db_key => $info):
                    $stmt_social->execute([$db_key]);
                    $link = $stmt_social->fetchColumn();
                    if (!$link || $link === '#') continue;
                ?>
                <li><a href="<?php echo htmlspecialchars($link); ?>" target="_blank" rel="noopener noreferrer" class="text-zinc-400 hover:text-emerald-500 transition-all flex items-center gap-2 sm:gap-3 uppercase tracking-widest font-bold text-[8px] sm:text-[10px]">
                    <img src="<?php echo $info[1]; ?>" class="w-3 sm:w-4 h-3 sm:h-4 invert opacity-50 hover:opacity-100" alt="<?php echo $info[0]; ?>"> <span class="hidden sm:inline"><?php echo $info[0]; ?></span>
                </a></li>
                <?php endforeach; ?>
            </ul>
        </div>

        <!-- Branding -->
        <div class="flex flex-col items-start">
            <div class="text-2xl sm:text-3xl font-black text-emerald-500 uppercase tracking-tighter mb-3 sm:mb-4">Diet & Nutritionist Eleni | Personalized Meal Plans</div>
            <p class="text-zinc-500 text-[9px] sm:text-xs italic leading-relaxed mb-6 sm:mb-8">
                Elevating the standard of nutritional health in Ethiopia through science and empathy.
            </p>
        </div>
    </div>

    <div class="mt-10 sm:mt-16 md:mt-20 pt-6 sm:pt-8 md:pt-10 border-t border-white/5 text-center flex flex-col items-center gap-4">
        <p class="text-zinc-600 text-[8px] sm:text-[10px] uppercase tracking-[0.4em] font-bold">
            Copyright &copy; 2026 Eleni Mekuria. All Rights Reserved.
        </p>
        <a href="https://neodigitalsolutions.com/" target="_blank" class="flex items-center gap-2 transition-opacity">
            <span class="text-emerald-500 text-[8px] sm:text-[10px] uppercase tracking-[0.2em] font-medium">Powered by</span>
            <img src="attached_assets/neo-logo_1767354870043.png" alt="Neo Printing and Advertising" class="h-8 sm:h-10 w-auto">
        </a>
    </div>
</footer>
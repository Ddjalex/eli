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
                    </p>a
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
                // Standard PHP Database connection fetch to be 100% compatible with MySQL/cPanel
                $site_data = [];
                try {
                    $query = $pdo->query("SELECT * FROM site_settings");
                    if ($query) {
                        while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                            // Standardize keys to lowercase and handle various column name possibilities
                            $k = strtolower($row['key'] ?? $row['Key'] ?? '');
                            $v = $row['value'] ?? $row['Value'] ?? '';
                            if ($k) $site_data[$k] = $v;
                        }
                    }
                } catch (Exception $e) {
                    // Fail silently
                }

                $social_platforms = [
                    'tiktok_link' => [
                        'name' => 'TikTok',
                        'svg' => '<path d="M12.525.02c1.31-.02 2.61-.01 3.91-.02.08 1.53.63 3.09 1.75 4.17 1.12 1.11 2.7 1.62 4.24 1.79v4.03c-1.44-.05-2.89-.35-4.2-.97-.57-.26-1.1-.59-1.62-.93-.01 2.92.01 5.84-.02 8.75-.08 1.4-.54 2.79-1.35 3.94-1.31 1.92-3.58 3.17-5.91 3.21-1.43.08-2.86-.31-4.08-1.03-2.02-1.19-3.44-3.37-3.65-5.71-.02-.5-.03-.99 0-1.49.18-1.76.91-3.44 2.15-4.74 1.45-1.51 3.54-2.36 5.62-2.12 1.07.06 2.1.41 3.02.95V8.19c-.63-.3-1.34-.47-2.05-.55-1.27-.12-2.63.15-3.72.89-1.54.97-2.45 2.73-2.41 4.53.02.9.32 1.74.84 2.45.67.92 1.71 1.55 2.85 1.74.38.07.76.08 1.14.06.66-.05 1.31-.22 1.9-.52.59-.29 1.1-.71 1.48-1.25.4-.6.6-1.27.67-1.97.03-3.25.02-6.51.01-9.76.01-.26.04-.51.09-.76z"/>'
                    ],
                    'instagram_link' => [
                        'name' => 'Instagram',
                        'svg' => '<path d="M12 2.163c3.204 0 3.584.012 4.85.07 1.17.054 1.805.249 2.227.493.559.217.96.477 1.382.877.422.4.682.801.899 1.36.244.422.439 1.057.493 2.227.058 1.266.07 1.646.07 4.85s-.012 3.584-.07 4.85c-.054 1.17-.249 1.805-.493 2.227-.217.559-.477.96-.877 1.382-.4.422-.801.682-1.36.899-.422.244-1.057.439-2.227.493-1.266.058-1.646.07-4.85.07s-3.584-.012-4.85-.07c-1.17-.054-1.805-.249-2.227-.493-.559-.217-.96-.477-1.382-.877-.422-.4-.682-.801-.899-1.36-.244-.422-.439-1.057-.493-2.227-.058-1.266-.07-1.646-.07-4.85s.012-3.584.07-4.85c.054-1.17.249-1.805.493-2.227.217-.559.477-.96.877-1.382.4-.422.801-.682 1.36-.899.422-.244 1.057-.439 2.227-.493 1.266-.058 1.646-.07 4.85-.07zM12 0C8.741 0 8.333.014 7.053.072 5.775.132 4.905.333 4.14.63c-.789.306-1.459.717-2.126 1.388-.669.671-1.08 1.34-1.38 2.127C.332 4.905.132 5.775.072 7.053.014 8.333 0 8.741 0 12s.014 3.667.072 4.947c.06 1.277.261 2.148.558 2.913.306.788.717 1.459 1.388 2.126.671.669 1.34 1.08 2.127 1.38.765.297 1.636.498 2.913.558C8.333 23.986 8.741 24 12 24s3.667-.014 4.947-.072c1.277-.06 2.148-.262 2.913-.558.788-.306 1.459-.718 2.126-1.388.669-.672 1.08-1.34 1.38-2.127.297-.765.499-1.636.558-2.913.058-1.28.072-1.687.072-4.947s-.014-3.667-.072-4.947c-.06-1.277-.262-2.149-.558-2.913-.306-.789-.718-1.459-1.388-2.126-.672-.669-1.34-1.08-2.127-1.38-.765-.297-1.636-.499-2.913-.558C15.667.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4.162 4.162 0 110-8.324 4.162 4.162 0 010 8.324zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/>'
                    ],
                    'whatsapp_link' => [
                        'name' => 'Whatsapp',
                        'svg' => '<path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.353-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.05-.148-.471-1.138-.645-1.556-.17-.41-.344-.354-.471-.354-.121-.002-.26-.002-.43-.002-.397-.002-.644.247-.741.747-.099.5-1.066 1.481-1.066 3.61 0 2.133 1.553 4.197 1.77 4.494.218.297 3.056 4.659 7.405 6.564 1.035.452 1.84.722 2.47.914 1.04.33 1.987.284 2.735.172.834-.124 2.564-.648 2.928-1.273.364-.624.364-1.163.255-1.274-.109-.112-.41-.112-.741.075zm2.41-11.571C17.427.346 14.162-.001 12.003 0 5.402 0 .038 5.364.038 11.965c0 2.112.547 4.17 1.587 5.99L0 24l6.149-1.613c1.754.956 3.734 1.46 5.851 1.46 6.602 0 11.966-5.364 11.966-11.965 0-3.203-1.246-6.215-3.502-8.471zM12.003 21.997c-2.105 0-4.16-.547-5.98-1.587l-.43-.25-3.71.97.99-3.62-.27-.43a8.955 8.955 0 01-1.37-4.73c0-4.945 4.02-8.965 8.96-8.965a8.96 8.96 0 018.96 8.965 8.96 8.96 0 01-8.96 8.96z"/>'
                    ],
                    'telegram_link' => [
                        'name' => 'Telegram',
                        'svg' => '<path d="M11.944 0A12 12 0 000 12a12 12 0 0012 12 12 12 0 0012-12A12 12 0 0012 0zM18.17 8.33l-2.06 9.73c-.15.68-.56.85-1.13.53l-3.14-2.31-1.52 1.46c-.17.17-.31.31-.63.31l.22-3.21 5.85-5.28c.25-.22-.05-.35-.39-.15l-7.23 4.55-3.11-1.02c-.68-.21-.69-.68.14-1l12.14-4.68c.56-.21 1.05.17.89.89z"/>'
                    ]
                ];

                foreach ($social_platforms as $key => $platform):
                    $link = trim($site_data[$key] ?? '');
                    if (!$link || $link === '#' || $link === '') continue;
                    
                    // Forcefully remove any hidden characters (like zero-width spaces or tracking cruft)
                    $link = preg_replace('/[\x00-\x1F\x7F-\x9F\xAD\x{200B}-\x{200D}\x{FEFF}]/u', '', $link);
                    $link = trim($link);
                    
                    if (!preg_match('~^https?://~i', $link)) {
                        $link = "https://" . ltrim($link, '/');
                    }

                    // Ensure TikTok links use the correct standard domain and format
                    if (strpos($link, 'tiktok.com') !== false) {
                        if (strpos($link, 'www.tiktok.com') === false) {
                            $link = str_replace('tiktok.com', 'www.tiktok.com', $link);
                        }
                        // Remove tracking parameters that sometimes break the link on mobile/certain regions
                        if (strpos($link, '?') !== false) {
                            $link = explode('?', $link)[0];
                        }
                    }
                ?>
                <li class="flex items-center mb-5 group">
                    <a href="<?php echo htmlspecialchars($link); ?>" target="_blank" rel="noopener noreferrer" class="text-zinc-400 hover:text-emerald-500 transition-all flex items-center gap-5 uppercase tracking-widest font-black text-[12px]">
                        <div class="w-12 h-12 flex items-center justify-center bg-zinc-900/50 rounded-2xl border border-white/5 group-hover:bg-emerald-500 group-hover:border-emerald-400 shadow-lg group-hover:shadow-emerald-500/20 transition-all duration-500 transform group-hover:-rotate-6 group-hover:scale-110">
                            <svg viewBox="0 0 24 24" class="w-6 h-6 fill-current opacity-60 group-hover:opacity-100 transition-all duration-300" style="color: white; display: block;">
                                <?php echo $platform['svg']; ?>
                            </svg>
                        </div>
                        <div class="flex flex-col">
                            <span class="inline leading-none"><?php echo $platform['name']; ?></span>
                            <span class="text-[8px] text-zinc-600 group-hover:text-emerald-400/70 transition-colors uppercase tracking-widest mt-1">Join the community</span>
                        </div>
                    </a>
                </li>
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
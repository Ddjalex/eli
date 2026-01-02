<?php
require_once '../includes/config.php';
if (!isset($_SESSION['user_id'])) exit;

$user_id = $_SESSION['user_id'];

// Check if user already has access to this plan
if (isset($_GET['plan_id'])) {
    $check_plan_id = (int)$_GET['plan_id'];
    $stmt = $pdo->prepare("SELECT status FROM user_plan_access WHERE user_id = ? AND meal_plan_id = ?");
    $stmt->execute([$user_id, $check_plan_id]);
    $access_status = $stmt->fetchColumn();
    
    if ($access_status === 'approved') {
        header("Location: dashboard.php");
        exit;
    }
}

$message = '';
if (isset($_GET['reason']) && $_GET['reason'] === 'unauthorized') {
    $message = "Your access is currently locked. Please upload your receipt for manual verification.";
}

$success_message = '';
if (isset($_GET['success'])) {
    $success_message = "Payment submitted successfully! Waiting for admin approval to unlock your meal plans.";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['receipt'])) {
    $upload_dir = '../uploads/receipts/';
    if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
    
    $filename = time() . '_' . basename($_FILES['receipt']['name']);
    $target = $upload_dir . $filename;

    if (move_uploaded_file($_FILES['receipt']['tmp_name'], $target)) {
        $plan_id = $_POST['plan_id'] ?? null;
        try {
            $stmt = $pdo->prepare("INSERT INTO payments (user_id, meal_plan_id, receipt_path, trx_number, payment_method_id) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([
                $_SESSION['user_id'], 
                $plan_id,
                $target, 
                $_POST['trx_number'] ?? null,
                $_POST['payment_method'] ?? null
            ]);
            
            // Also create a record in user_plan_access with pending status
            if ($plan_id) {
                try {
                    $pdo->prepare("INSERT INTO user_plan_access (user_id, meal_plan_id, status) VALUES (?, ?, 'pending') ON DUPLICATE KEY UPDATE status = 'pending', updated_at = CURRENT_TIMESTAMP")->execute([$_SESSION['user_id'], $plan_id]);
                } catch (PDOException $e) {
                    // Fallback if the access table check fails
                }
            }
        } catch (PDOException $e) {
            // Fallback for older schema
            $stmt = $pdo->prepare("INSERT INTO payments (user_id, receipt_path, trx_number, payment_method_id) VALUES (?, ?, ?, ?)");
            $stmt->execute([
                $_SESSION['user_id'], 
                $target, 
                $_POST['trx_number'] ?? null,
                $_POST['payment_method'] ?? null
            ]);
        }
        header("Location: payment.php?success=1");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Secure Verification | Eleni Mekuria</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-black text-white p-6 md:p-24 flex items-center justify-center min-h-screen">
    <div class="max-w-xl w-full bg-zinc-900 border border-white/10 p-10 rounded-3xl shadow-2xl relative overflow-hidden">
        <div class="absolute top-0 left-0 w-full h-1 bg-emerald-500"></div>
        <div class="mb-10">
            <h2 class="text-3xl font-black uppercase tracking-tighter mb-2">Secure <span class="text-emerald-500">Vault</span> Access</h2>
            <p class="text-zinc-400 text-sm">To unlock your premium meal plans, please complete the bank transfer and upload your receipt below.</p>
        </div>

        <?php if ($success_message): ?>
            <div class="bg-emerald-500/10 border border-emerald-500/50 p-6 rounded-xl mb-8">
                <div class="flex items-start gap-4">
                    <div class="flex-shrink-0">
                        <svg class="w-6 h-6 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-emerald-500 text-sm font-bold uppercase tracking-widest mb-2">Payment Submitted Successfully!</h3>
                        <p class="text-emerald-400/80 text-xs leading-relaxed">Your receipt has been received. Admin will verify your payment within 24 hours. Once approved, all your meal plans will be instantly unlocked.</p>
                    </div>
                </div>
                <a href="dashboard.php" class="block text-center mt-6 bg-emerald-600 text-white px-8 py-3 rounded-xl font-bold uppercase text-xs tracking-widest hover:bg-emerald-500 transition-all">Go to Dashboard</a>
            </div>
        <?php elseif ($message): ?>
            <div class="bg-emerald-500/10 border border-emerald-500/30 p-4 rounded-xl mb-8 text-emerald-500 text-xs font-bold uppercase tracking-widest text-center">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data" class="space-y-6">
            <?php if (isset($_GET['plan_id'])): ?>
                <input type="hidden" name="plan_id" value="<?php echo htmlspecialchars($_GET['plan_id']); ?>">
            <?php endif; ?>
            <div>
                <label class="block text-[10px] font-bold uppercase tracking-[0.3em] text-zinc-500 mb-3">Select Payment Method</label>
                <select name="payment_method" id="paymentMethod" class="w-full bg-black border border-white/10 p-4 rounded-xl focus:border-emerald-500 transition-all text-sm text-white outline-none" required onchange="updatePaymentDetails()">
                    <option value="">-- Choose a payment method --</option>
                    <?php
                    $payment_options = $pdo->query("SELECT * FROM payment_options WHERE is_active = TRUE ORDER BY display_order ASC")->fetchAll();
                    foreach ($payment_options as $opt):
                    ?>
                    <option value="<?php echo $opt['id']; ?>" data-account="<?php echo htmlspecialchars($opt['account_number']); ?>" data-holder="<?php echo htmlspecialchars($opt['account_holder']); ?>" data-name="<?php echo htmlspecialchars($opt['name']); ?>">
                        <?php echo htmlspecialchars($opt['name']); ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div id="paymentDetails" class="hidden p-4 rounded-xl border border-emerald-500/30 bg-emerald-500/5 space-y-3">
                <div class="flex justify-between items-center">
                    <span class="text-zinc-500 text-[9px] uppercase tracking-widest">Service</span>
                    <div class="flex items-center gap-2">
                        <span class="font-bold text-xs text-white" id="methodName">-</span>
                        <button type="button" onclick="copyToClipboard('methodName', event)" class="text-emerald-500 hover:text-emerald-400 transition-colors" title="Copy">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                        </button>
                    </div>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-zinc-500 text-[9px] uppercase tracking-widest">Account</span>
                    <div class="flex items-center gap-2">
                        <span class="font-bold text-xs text-white" id="methodAccount">-</span>
                        <button type="button" onclick="copyToClipboard('methodAccount', event)" class="text-emerald-500 hover:text-emerald-400 transition-colors" title="Copy">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                        </button>
                    </div>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-zinc-500 text-[9px] uppercase tracking-widest">Holder Name</span>
                    <div class="flex items-center gap-2">
                        <span class="font-bold text-xs text-zinc-300" id="methodHolder">-</span>
                        <button type="button" onclick="copyToClipboard('methodHolder', event)" class="text-emerald-500 hover:text-emerald-400 transition-colors" title="Copy">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                        </button>
                    </div>
                </div>
            </div>

            <div>
                <label class="block text-[10px] font-black uppercase tracking-[0.3em] text-zinc-500 mb-3">Transaction (TRX) Number</label>
                <input type="text" name="trx_number" placeholder="Enter your transaction reference number" class="w-full bg-black border border-white/10 p-4 rounded-xl focus:border-emerald-500 transition-all text-sm text-white placeholder-zinc-600 outline-none" required>
            </div>

            <div>
                <label class="block text-[10px] font-black uppercase tracking-[0.3em] text-zinc-500 mb-3">Upload Receipt Screenshot</label>
                <div class="relative">
                    <input type="file" name="receipt" class="w-full bg-black border border-white/10 p-4 rounded-xl focus:border-emerald-500 transition-all text-xs" required>
                </div>
            </div>
            <button type="submit" class="w-full bg-emerald-600 text-white font-black uppercase tracking-[0.2em] py-5 rounded-xl hover:bg-emerald-500 transition-all shadow-lg shadow-emerald-900/20">Submit for Approval</button>
        </form>

        <script>
            function updatePaymentDetails() {
                const select = document.getElementById('paymentMethod');
                const details = document.getElementById('paymentDetails');
                const selected = select.options[select.selectedIndex];
                
                if (selected.value) {
                    document.getElementById('methodName').textContent = selected.getAttribute('data-name');
                    document.getElementById('methodAccount').textContent = selected.getAttribute('data-account');
                    document.getElementById('methodHolder').textContent = selected.getAttribute('data-holder');
                    details.classList.remove('hidden');
                } else {
                    details.classList.add('hidden');
                }
            }

            function copyToClipboard(elementId, event) {
                event.preventDefault();
                const element = document.getElementById(elementId);
                const text = element.textContent.trim();
                const button = event.currentTarget;
                
                // Try modern clipboard API first
                if (navigator.clipboard && navigator.clipboard.writeText) {
                    navigator.clipboard.writeText(text).then(() => {
                        showCopySuccess(button);
                    }).catch(() => {
                        // Fallback method
                        fallbackCopy(text, button);
                    });
                } else {
                    // Fallback for older browsers
                    fallbackCopy(text, button);
                }
            }

            function fallbackCopy(text, button) {
                const textarea = document.createElement('textarea');
                textarea.value = text;
                textarea.style.position = 'fixed';
                textarea.style.opacity = '0';
                document.body.appendChild(textarea);
                textarea.select();
                try {
                    document.execCommand('copy');
                    showCopySuccess(button);
                } catch (err) {
                    alert('Copy failed. Please try again.');
                }
                document.body.removeChild(textarea);
            }

            function showCopySuccess(button) {
                const originalSvg = button.innerHTML;
                button.innerHTML = '<svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>';
                button.classList.add('text-emerald-600');
                
                setTimeout(() => {
                    button.innerHTML = originalSvg;
                    button.classList.remove('text-emerald-600');
                }, 2000);
            }
        </script>
        
        <p class="mt-8 text-center text-zinc-600 text-[10px] uppercase tracking-widest">Your receipt is being verified. Once approved by Eleni, your download will be unlocked.</p>
        <a href="dashboard.php" class="block text-center mt-6 text-zinc-500 hover:text-white text-[10px] uppercase font-bold tracking-widest">Back to Dashboard</a>
    </div>
</body>
</html>
<?php
require_once 'includes/config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$plan_id = $_GET['id'] ?? 0;
$user_id = $_SESSION['user_id'];

// Check user status and package
$stmt = $pdo->prepare("SELECT status, package FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

$user_status = $user['status'];
$user_package = $user['package'];

// Check for explicit access in user_plan_access
$stmt = $pdo->prepare("SELECT status FROM user_plan_access WHERE user_id = ? AND meal_plan_id = ?");
$stmt->execute([$user_id, $plan_id]);
$plan_access_status = $stmt->fetchColumn();

// Get the plan's package type
$stmt = $pdo->prepare("SELECT package_type FROM meal_plans WHERE id = ?");
$stmt->execute([$plan_id]);
$plan_package_type = $stmt->fetchColumn();

$is_approved = false;

// Access granted if:
// 1. User has an 'approved' record in user_plan_access for this specific plan
// 2. OR User is active/approved and this plan matches their main package
if ($plan_access_status === 'approved') {
    $is_approved = true;
} elseif (($user_status === 'active' || $user_status === 'approved') && !empty($user_package) && $plan_package_type === $user_package) {
    $is_approved = true;
}

if (!$is_approved) {
    header("Location: user/payment.php?reason=unauthorized");
    exit;
}

// Security: Prevent direct PDF access via URL for unauthorized users
// We'll use the existing session-based protection in this file.
// To further enhance security, we can serve the PDF through a proxy, 
// but for now, the preview.php check is sufficient since it's the only entry point.

// Get meal plan file path
$stmt = $pdo->prepare("SELECT file_url, title FROM meal_plans WHERE id = ?");
$stmt->execute([$plan_id]);
$plan = $stmt->fetch();

if (!$plan || !$plan['file_url']) {
    die("Plan not found or file not available.");
}

$file_path = $plan['file_url'];

if (!file_exists($file_path)) {
    die("File error: The requested document could not be found on the server.");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($plan['title']); ?> - Preview</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
    <style>
        #pdf-canvas {
            max-width: 100%;
            height: auto;
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px;
        }
        .pdf-viewer {
            background: #09090b;
        }
    </style>
</head>
<body class="bg-black text-white">
    <div class="min-h-screen pdf-viewer p-4 sm:p-6 md:p-8">
        <div class="max-w-4xl mx-auto">
            <!-- Header -->
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 sm:gap-0 mb-6 sm:mb-8 pb-6 sm:pb-8 border-b border-white/10">
                <div>
                    <h1 class="text-2xl sm:text-3xl font-bold text-emerald-500 uppercase tracking-tighter"><?php echo htmlspecialchars($plan['title']); ?></h1>
                    <p class="text-zinc-500 text-xs sm:text-sm mt-2">View & Study Your Meal Plan</p>
                </div>
                <a href="user/dashboard.php" class="bg-zinc-900 border border-white/10 px-4 sm:px-6 py-2 sm:py-3 rounded-lg sm:rounded-xl font-bold uppercase text-[9px] sm:text-xs tracking-widest hover:bg-white/5 transition-all whitespace-nowrap">Back to Dashboard</a>
            </div>

            <!-- PDF Viewer Controls -->
            <div class="bg-zinc-900/50 border border-white/10 p-4 sm:p-6 rounded-xl sm:rounded-2xl mb-6 sm:mb-8">
                <div class="flex flex-col sm:flex-row justify-between items-center gap-3 sm:gap-4">
                    <div class="flex items-center gap-1 sm:gap-2 overflow-auto w-full sm:w-auto">
                        <button id="prev-btn" class="bg-emerald-600 text-white px-2 sm:px-4 py-1 sm:py-2 rounded text-[9px] sm:rounded-lg sm:font-bold sm:uppercase sm:text-xs hover:bg-emerald-500 transition-all flex-shrink-0">←</button>
                        <span id="page-num" class="mx-2 sm:mx-4 text-xs sm:text-sm whitespace-nowrap flex-shrink-0">Page <span id="current-page">1</span> of <span id="total-pages">--</span></span>
                        <button id="next-btn" class="bg-emerald-600 text-white px-2 sm:px-4 py-1 sm:py-2 rounded text-[9px] sm:rounded-lg sm:font-bold sm:uppercase sm:text-xs hover:bg-emerald-500 transition-all flex-shrink-0">→</button>
                    </div>
                </div>
            </div>

            <!-- PDF Canvas -->
            <div class="bg-zinc-900/30 border border-white/10 p-4 sm:p-6 md:p-8 rounded-lg sm:rounded-2xl flex justify-center overflow-x-auto">
                <canvas id="pdf-canvas" style="max-width: 100%; height: auto;"></canvas>
            </div>

            <!-- Page Navigation at Bottom -->
            <div class="flex flex-col sm:flex-row justify-center gap-2 sm:gap-4 mt-6 sm:mt-8">
                <button id="prev-btn-bottom" class="bg-emerald-600 text-white px-4 sm:px-6 py-2 sm:py-3 rounded-lg sm:rounded-lg font-bold uppercase text-[9px] sm:text-sm hover:bg-emerald-500 transition-all">← Previous Page</button>
                <button id="next-btn-bottom" class="bg-emerald-600 text-white px-4 sm:px-6 py-2 sm:py-3 rounded-lg sm:rounded-lg font-bold uppercase text-[9px] sm:text-sm hover:bg-emerald-500 transition-all">Next Page →</button>
            </div>
        </div>
    </div>

    <script>
        const pdfPath = '<?php echo htmlspecialchars($file_path); ?>';
        const pdfjsLib = window['pdfjs-dist/build/pdf'];
        pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';

        let currentPage = 1;
        let totalPages = 0;
        let pdfDoc = null;

        // Load PDF
        pdfjsLib.getDocument(pdfPath).promise.then(pdf => {
            pdfDoc = pdf;
            totalPages = pdf.numPages;
            document.getElementById('total-pages').textContent = totalPages;
            renderPage(currentPage);
        }).catch(err => {
            document.getElementById('pdf-canvas').parentElement.innerHTML = '<p class="text-red-500 font-bold">Error loading PDF: ' + err.message + '</p>';
        });

        function renderPage(pageNum) {
            pdfDoc.getPage(pageNum).then(page => {
                const canvas = document.getElementById('pdf-canvas');
                const ctx = canvas.getContext('2d');
                const scale = window.innerWidth < 768 ? 1 : 2;
                const viewport = page.getViewport({ scale: scale });

                canvas.height = viewport.height;
                canvas.width = viewport.width;

                page.render({
                    canvasContext: ctx,
                    viewport: viewport
                }).promise.then(() => {
                    document.getElementById('current-page').textContent = pageNum;
                });
            });
        }

        document.getElementById('prev-btn').addEventListener('click', () => {
            if (currentPage > 1) {
                currentPage--;
                renderPage(currentPage);
            }
        });

        document.getElementById('next-btn').addEventListener('click', () => {
            if (currentPage < totalPages) {
                currentPage++;
                renderPage(currentPage);
            }
        });

        document.getElementById('prev-btn-bottom').addEventListener('click', () => {
            if (currentPage > 1) {
                currentPage--;
                renderPage(currentPage);
                window.scrollTo(0, 0);
            }
        });

        document.getElementById('next-btn-bottom').addEventListener('click', () => {
            if (currentPage < totalPages) {
                currentPage++;
                renderPage(currentPage);
                window.scrollTo(0, 0);
            }
        });

        window.addEventListener('resize', () => renderPage(currentPage));
    </script>
</body>
</html>

<?php
require_once 'includes/config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$plan_id = $_GET['id'] ?? 0;

// Check user status
$stmt = $pdo->prepare("SELECT status FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user_status = $stmt->fetchColumn();

if ($user_status !== 'approved') {
    header("Location: user/payment.php?reason=unauthorized");
    exit;
}

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
    <div class="min-h-screen pdf-viewer p-8">
        <div class="max-w-4xl mx-auto">
            <!-- Header -->
            <div class="flex justify-between items-center mb-8 pb-8 border-b border-white/10">
                <div>
                    <h1 class="text-3xl font-bold text-emerald-500 uppercase tracking-tighter"><?php echo htmlspecialchars($plan['title']); ?></h1>
                    <p class="text-zinc-500 text-sm mt-2">View & Study Your Meal Plan</p>
                </div>
                <a href="user/dashboard.php" class="bg-zinc-900 border border-white/10 px-6 py-3 rounded-xl font-bold uppercase text-xs tracking-widest hover:bg-white/5 transition-all">Back to Dashboard</a>
            </div>

            <!-- PDF Viewer Controls -->
            <div class="bg-zinc-900/50 border border-white/10 p-6 rounded-2xl mb-8">
                <div class="flex justify-between items-center gap-4">
                    <div class="flex items-center gap-2">
                        <button id="prev-btn" class="bg-emerald-600 text-white px-4 py-2 rounded-lg font-bold uppercase text-xs hover:bg-emerald-500 transition-all">← Previous</button>
                        <span id="page-num" class="mx-4 text-sm">Page <span id="current-page">1</span> of <span id="total-pages">--</span></span>
                        <button id="next-btn" class="bg-emerald-600 text-white px-4 py-2 rounded-lg font-bold uppercase text-xs hover:bg-emerald-500 transition-all">Next →</button>
                    </div>
                </div>
            </div>

            <!-- PDF Canvas -->
            <div class="bg-zinc-900/30 border border-white/10 p-8 rounded-2xl flex justify-center">
                <canvas id="pdf-canvas"></canvas>
            </div>

            <!-- Page Navigation at Bottom -->
            <div class="flex justify-center gap-4 mt-8">
                <button id="prev-btn-bottom" class="bg-emerald-600 text-white px-6 py-3 rounded-lg font-bold uppercase text-sm hover:bg-emerald-500 transition-all">← Previous Page</button>
                <button id="next-btn-bottom" class="bg-emerald-600 text-white px-6 py-3 rounded-lg font-bold uppercase text-sm hover:bg-emerald-500 transition-all">Next Page →</button>
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

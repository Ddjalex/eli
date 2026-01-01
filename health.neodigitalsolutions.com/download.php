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

if (file_exists($file_path)) {
    header('Content-Type: application/pdf');
    header('Content-Disposition: attachment; filename="' . $plan['title'] . '.pdf"');
    header('Content-Length: ' . filesize($file_path));
    readfile($file_path);
    exit;
} else {
    die("File error: The requested document could not be found on the server.");
}
?>
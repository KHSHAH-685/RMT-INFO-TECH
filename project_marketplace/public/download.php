<?php
declare(strict_types=1);
session_start();

require_once __DIR__ . '/../config/database.php';

// Check if user is logged in
if (empty($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$slug = trim($_GET['slug'] ?? '');
if ($slug === '') {
    die('Invalid request.');
}

$pdo = getDatabaseConnection();

// Fetch the project and verify it exists
$stmt = $pdo->prepare("SELECT title, file_path FROM listings WHERE slug = :slug LIMIT 1");
$stmt->execute(['slug' => $slug]);
$project = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$project || empty($project['file_path'])) {
    http_response_code(404);
    die('File not found.');
}

// Resolve full safe path
$fileName = basename($project['file_path']);
$filePath = dirname(__DIR__) . '/storage/uploads/' . $fileName;

if (!file_exists($filePath)) {
    http_response_code(404);
    die('File missing from storage.');
}

// Send download headers
header('Content-Description: File Transfer');
header('Content-Type: application/zip');
header('Content-Disposition: attachment; filename="' . basename($fileName) . '"');
header('Expires: 0');
header('Cache-Control: must-revalidate');
header('Pragma: public');
header('Content-Length: ' . filesize($filePath));

readfile($filePath);
exit;
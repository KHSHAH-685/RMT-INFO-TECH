<?php
declare(strict_types=1);
session_start();

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../src/Listing.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

$listingId = filter_input(INPUT_POST, 'listing_id', FILTER_VALIDATE_INT);
if (!$listingId) {
    http_response_code(400);
    exit('Invalid listing ID provided.');
}

$submittedToken = $_POST['csrf_token'] ?? '';
$sessionToken = $_SESSION['checkout_csrf_token'] ?? '';
if (!is_string($submittedToken) || !is_string($sessionToken) || $sessionToken === '' || !hash_equals($sessionToken, $submittedToken)) {
    http_response_code(403);
    exit('Your checkout session expired. Return to the listing and try again.');
}

if (!isset($_SESSION['user_id'], $_SESSION['role']) || $_SESSION['role'] !== 'buyer') {
    $_SESSION['pending_listing_id'] = $listingId;
    header('Location: login.php?checkout=1');
    exit;
}

$pdo = getDatabaseConnection();

// Transaction execution with lock mechanism
try {
    $pdo->beginTransaction();

    // 1. Check availability with pessimistic locking (FOR UPDATE)
    $stmt = $pdo->prepare("SELECT * FROM listings WHERE id = :id FOR UPDATE");
    $stmt->execute(['id' => $listingId]);
    $listing = $stmt->fetch();

    if (!$listing || $listing['status'] !== 'available') {
        $pdo->rollBack();
        http_response_code(409);
        exit('Listing is no longer available for purchase.');
    }

    $buyerId = (int)$_SESSION['user_id'];
    $sellerId = (int)$listing['seller_id'];
    $uuidBytes = random_bytes(16);
    $uuidBytes[6] = chr((ord($uuidBytes[6]) & 0x0f) | 0x40);
    $uuidBytes[8] = chr((ord($uuidBytes[8]) & 0x3f) | 0x80);
    $uuidHex = bin2hex($uuidBytes);
    $uuid = sprintf(
        '%s-%s-%s-%s-%s',
        substr($uuidHex, 0, 8),
        substr($uuidHex, 8, 4),
        substr($uuidHex, 12, 4),
        substr($uuidHex, 16, 4),
        substr($uuidHex, 20, 12)
    );

    // 3. Mark listing as pending escrow
    $updateStmt = $pdo->prepare("UPDATE listings SET status = 'escrow_pending' WHERE id = :id");
    $updateStmt->execute(['id' => $listingId]);

    // 4. Create escrow transaction record
    $txStmt = $pdo->prepare("
        INSERT INTO transactions (listing_id, buyer_id, seller_id, transaction_uuid, agreed_price, escrow_status) 
        VALUES (:listing_id, :buyer_id, :seller_id, :uuid, :price, 'initiated')
    ");
    $txStmt->execute([
        'listing_id' => $listingId,
        'buyer_id'   => $buyerId,
        'seller_id'  => $sellerId,
        'uuid'       => $uuid,
        'price'      => $listing['asking_price']
    ]);

    $pdo->commit();

    // 5. Redirect to Escrow/Payment Provider (or confirmation view)
    echo "<!doctype html><html lang='en'><head><meta charset='UTF-8'><meta name='viewport' content='width=device-width, initial-scale=1.0'><title>Acquisition Started | RMT INFO TECH</title><script src='https://cdn.tailwindcss.com'></script><link rel='stylesheet' href='assets/css/theme.css'><link rel='preconnect' href='https://fonts.googleapis.com'><link rel='preconnect' href='https://fonts.gstatic.com' crossorigin><link href='https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap' rel='stylesheet'></head>";
    echo "<body class='checkout-page min-h-screen flex items-center justify-center p-5 text-slate-800 antialiased'><main class='w-full max-w-xl rounded-3xl border border-slate-200 bg-white p-7 text-center shadow-2xl sm:p-10'>";
    echo "<div class='mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-emerald-50 text-2xl font-bold text-emerald-700'>✓</div>";
    echo "<p class='mt-6 text-xs font-bold uppercase tracking-[.18em] text-emerald-700'>Escrow initiated</p>";
    echo "<h1 class='mt-2 text-2xl font-extrabold tracking-tight text-slate-900 sm:text-3xl'>Your acquisition is underway</h1>";
    echo "<p class='mt-3 text-sm leading-relaxed text-slate-600'>The asset has been placed on hold. Complete payment to begin credentials and domain migration.</p>";
    echo "<div class='mt-7 rounded-2xl border border-slate-200 bg-slate-50 p-4 text-left'><span class='text-[10px] font-bold uppercase tracking-wider text-slate-500'>Transaction token</span><p class='mt-2 break-all font-mono text-xs text-slate-800'>" . htmlspecialchars($uuid) . "</p></div>";
    echo "<a class='mt-7 inline-flex items-center justify-center gap-2 rounded-xl bg-emerald-700 px-5 py-3 text-sm font-bold text-white transition hover:bg-emerald-800' href='index.php'>Return to marketplace <span aria-hidden='true'>&rarr;</span></a>";
    echo "</main></body></html>";

} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    error_log("Escrow error: " . $e->getMessage());
    http_response_code(500);
    die("An error occurred while creating the acquisition escrow.");
}
<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../includes/functions.php';

requireLogin();

header('Content-Type: application/json');

// 1. Validate input FIRST
if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo json_encode([
        "medication_id" => null,
        "available" => false,
        "quantity" => null,
        "low_stock" => false,
        "expiring_soon" => false,
        "error" => "Missing medication ID"
    ]);
    exit;
}

$id = intval($_GET['id']);

// 2. Fetch medication safely
$stmt = $pdo->prepare("SELECT * FROM inventory WHERE inventory_id = ?");
$stmt->execute([$id]);
$med = $stmt->fetch(PDO::FETCH_ASSOC);

// 3. Check if record exists
if (!$med) {
    echo json_encode([
        "medication_id" => $id,
        "available" => false,
        "quantity" => 0,
        "low_stock" => false,
        "expiring_soon" => false,
        "error" => "Medication not found"
    ]);
    exit;
}

// 4. Calculate status
$status = stockStatus($med['quantity'], $med['minimum_threshold'], $med['expiration_date']);

// 5. Return clean JSON
echo json_encode([
    "medication_id" => $med['inventory_id'],
    "available" => $med['quantity'] > 0,
    "quantity" => $med['quantity'],
    "low_stock" => $status['low_stock'],
    "expiring_soon" => $status['expiring_soon']
]);
?>

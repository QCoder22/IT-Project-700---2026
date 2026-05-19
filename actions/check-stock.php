<?php
require_once "../config/db.php";

header('Content-Type: application/json');

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    echo json_encode([
        "status" => "error",
        "message" => "Invalid or missing medication ID"
    ]);
    exit;
}

try {

    $stmt = $pdo->prepare("SELECT * FROM inventory WHERE id = ?");
    $stmt->execute([$id]);

    $med = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$med) {
        echo json_encode([
            "status" => "error",
            "message" => "Medication not found"
        ]);
        exit;
    }

    echo json_encode([
        "status" => "success",
        "message" => "Medication found",
        "id" => $med['id'],
        "name" => $med['medication_name'],
        "quantity" => $med['quantity'],
        "available" => $med['quantity'] > 0,
        "low_stock" => $med['quantity'] <= $med['minimum_threshold'],
        "expiry_date" => $med['expiry_date']
    ]);

} catch (Exception $e) {

    echo json_encode([
        "status" => "error",
        "message" => "Server error"
    ]);
}
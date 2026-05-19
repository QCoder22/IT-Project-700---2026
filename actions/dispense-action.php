<?php
require_once "../config/db.php";

session_start();

$pdo->beginTransaction();

try {

    $prescription_id = $_POST['prescription_id'];

    // Get prescription items
    $stmt = $pdo->prepare("SELECT * FROM prescription_items WHERE prescription_id=?");
    $stmt->execute([$prescription_id]);
    $items = $stmt->fetchAll();

    foreach ($items as $item) {

        // Check stock first
        $check = $pdo->prepare("SELECT quantity FROM inventory WHERE id=?");
        $check->execute([$item['medication_id']]);
        $stock = $check->fetchColumn();

        if ($stock < $item['quantity']) {
            throw new Exception("Insufficient stock for medication ID " . $item['medication_id']);
        }

        // Deduct stock
        $update = $pdo->prepare("
            UPDATE inventory 
            SET quantity = quantity - ?
            WHERE id = ?
        ");
        $update->execute([$item['quantity'], $item['medication_id']]);

        // Audit log
        $log = $pdo->prepare("
            INSERT INTO inventory_logs
            (medication_id, action, quantity_change, reference_id, description)
            VALUES (?, 'dispensed', ?, ?, 'Prescription dispensed')
        ");

        $log->execute([
            $item['medication_id'],
            $item['quantity'],
            $prescription_id
        ]);
    }

    // Update prescription status
    $pdo->prepare("
        UPDATE prescriptions 
        SET status='dispensed'
        WHERE id=?
    ")->execute([$prescription_id]);

    $pdo->commit();

    echo "Dispensed successfully";

} catch (Exception $e) {
    $pdo->rollBack();
    echo "Dispense failed: " . $e->getMessage();
}
?>
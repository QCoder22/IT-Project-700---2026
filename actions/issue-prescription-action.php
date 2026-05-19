<?php
require_once "../config/db.php";

session_start();

$pdo->beginTransaction();

try {

    $stmt = $pdo->prepare("
        INSERT INTO prescriptions (patient_id, doctor_id, appointment_id, status)
        VALUES (?, ?, ?, 'pending')
    ");

    $stmt->execute([
        $_POST['patient_id'],
        $_SESSION['user_id'] ?? 1,
        $_POST['appointment_id']
    ]);

    $prescription_id = $pdo->lastInsertId();

    foreach ($_POST['medication_id'] as $i => $med_id) {

        $stmt = $pdo->prepare("
            INSERT INTO prescription_items
            (prescription_id, medication_id, dosage, quantity)
            VALUES (?, ?, ?, ?)
        ");

        $stmt->execute([
            $prescription_id,
            $med_id,
            $_POST['dosage'][$i],
            $_POST['quantity'][$i]
        ]);
    }

    $pdo->commit();
    echo "Prescription created successfully";

} catch (Exception $e) {
    $pdo->rollBack();
    echo "Error: " . $e->getMessage();
}
?>
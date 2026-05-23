<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../includes/functions.php';

requireRole('doctor');

// Find the doctor's record + the inventory item's price
$doc = $pdo->prepare("SELECT doctor_id FROM doctors WHERE user_id=?");
$doc->execute([$_SESSION['user_id']]);
$doctor_id = $doc->fetch()['doctor_id'] ?? null;

$inv = $pdo->prepare("SELECT unit_price FROM inventory WHERE inventory_id=?");
$inv->execute([$_POST['inventory_id']]);
$unit_price = $inv->fetch()['unit_price'] ?? 0;

$pdo->beginTransaction();

try {

$stmt = $pdo->prepare("
INSERT INTO prescriptions (patient_id, doctor_id, appointment_id, status, diagnosis)
VALUES (?,?,?,'pending',?)
");
$stmt->execute([
    $_POST['patient_id'],
    $doctor_id,
    $_POST['appointment_id'],
    $_POST['diagnosis']
]);
$pid = $pdo->lastInsertId();

$stmt = $pdo->prepare("
INSERT INTO prescription_items
(prescription_id, inventory_id, dosage, frequency, duration_days, quantity, unit_price_at_issue)
VALUES (?,?,?,?,?,?,?)
");

$stmt->execute([
    $pid,
    $_POST['inventory_id'],
    $_POST['dosage'],
    $_POST['frequency'],
    $_POST['duration_days'],
    $_POST['quantity'],
    $unit_price
]);

// Log creation in status log
$pdo->prepare("INSERT INTO prescription_status_log
    (prescription_id, changed_by_user_id, old_status, new_status, comment)
    VALUES (?,?,?,?,?)")
    ->execute([$pid, $_SESSION['user_id'], null, 'pending', 'Issued by doctor']);

$pdo->commit();

setMsg('success', 'Prescription saved.');
redirect('/doctor/prescription-status.php');

} catch(Exception $e){
    $pdo->rollBack();
    setMsg('error', 'Error: '.$e->getMessage());
    redirect('/doctor/issue-prescription.php');
}
?>

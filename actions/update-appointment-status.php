<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../includes/functions.php';

requireRole('doctor');

$id     = $_GET['id'] ?? null;
$status = $_GET['status'] ?? '';

// Only allow known status values
if (!in_array($status, ['completed', 'cancelled', 'no_show'])) {
    setMsg('error', 'Invalid status.');
    redirect('/doctor/appointments.php');
}

// Find the logged-in doctor so they can only change their own appointments
$ds = $pdo->prepare("SELECT doctor_id FROM doctors WHERE user_id = ?");
$ds->execute([$_SESSION['user_id']]);
$doctorId = $ds->fetchColumn();

$sql = "
UPDATE appointments
SET status = ?
WHERE appointment_id = ?
AND doctor_id = ?
";

$stmt = $pdo->prepare($sql);
$stmt->execute([$status, $id, $doctorId]);

setMsg('success', 'Appointment status updated.');
redirect('/doctor/appointments.php');
?>

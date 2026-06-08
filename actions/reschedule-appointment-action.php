<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../includes/functions.php';

requireLogin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('/doctor/appointments.php');
}

$appointmentId   = $_POST['appointment_id'];
$appointmentDate = $_POST['appointment_date'];

// GET THE DOCTOR FOR THIS APPOINTMENT
$getSql = "
SELECT doctor_id
FROM appointments
WHERE appointment_id = ?
";

$getStmt = $pdo->prepare($getSql);
$getStmt->execute([$appointmentId]);
$appointment = $getStmt->fetch(PDO::FETCH_ASSOC);

if (!$appointment) {
    setMsg('error', 'Appointment not found.');
    redirect('/doctor/appointments.php');
}

$doctorId = $appointment['doctor_id'];

// CHECK THE NEW SLOT IS FREE
$checkSql = "
SELECT COUNT(*)
FROM appointments
WHERE doctor_id = ?
AND appointment_date = ?
AND status = 'scheduled'
AND appointment_id != ?
";

$checkStmt = $pdo->prepare($checkSql);
$checkStmt->execute([$doctorId, $appointmentDate, $appointmentId]);

if ($checkStmt->fetchColumn() > 0) {
    setMsg('error', 'The doctor is unavailable at that time.');
    redirect('/doctor/appointments.php');
}

// UPDATE THE DATE/TIME
$sql = "
UPDATE appointments
SET appointment_date = ?
WHERE appointment_id = ?
";

$stmt = $pdo->prepare($sql);
$stmt->execute([$appointmentDate, $appointmentId]);

setMsg('success', 'Appointment rescheduled.');
redirect('/doctor/appointments.php');
?>

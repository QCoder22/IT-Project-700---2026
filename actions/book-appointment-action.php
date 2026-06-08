<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../includes/functions.php';

requireLogin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('/receptionist/book-appointment.php');
}

$patientId       = $_POST['patient_id'];
$doctorId        = $_POST['doctor_id'];
$appointmentDate = $_POST['appointment_date'];
$reason          = trim($_POST['reason'] ?? '');

// CHECK DOCTOR NOT ALREADY BOOKED FOR THIS SLOT
$checkSql = "
SELECT COUNT(*)
FROM appointments
WHERE doctor_id = ?
AND appointment_date = ?
AND status = 'scheduled'
";

$checkStmt = $pdo->prepare($checkSql);
$checkStmt->execute([$doctorId, $appointmentDate]);

if ($checkStmt->fetchColumn() > 0) {
    setMsg('error', 'That doctor is already booked for this time slot.');
    redirect('/receptionist/book-appointment.php');
}

// INSERT APPOINTMENT
$sql = "
INSERT INTO appointments
(patient_id, doctor_id, appointment_date, reason, status, booked_by_user_id)
VALUES (?, ?, ?, ?, 'scheduled', ?)
";

$stmt = $pdo->prepare($sql);
$stmt->execute([$patientId, $doctorId, $appointmentDate, $reason, $_SESSION['user_id']]);

setMsg('success', 'Appointment booked successfully.');
redirect('/receptionist/book-appointment.php');
?>

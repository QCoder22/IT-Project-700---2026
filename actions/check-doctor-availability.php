<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../includes/functions.php';

requireLogin();

header('Content-Type: application/json');

$doctorId = $_GET['doctor_id'] ?? null;
$dateTime = $_GET['appointment_date'] ?? null;

if (!$doctorId || !$dateTime) {
    echo json_encode(['available' => false, 'error' => 'Missing parameters']);
    exit;
}

$sql = "
SELECT COUNT(*) total
FROM appointments
WHERE doctor_id = ?
AND appointment_date = ?
AND status = 'scheduled'
";

$stmt = $pdo->prepare($sql);
$stmt->execute([$doctorId, $dateTime]);
$result = $stmt->fetch(PDO::FETCH_ASSOC);

echo json_encode([
    'available' => ($result['total'] == 0)
]);
?>

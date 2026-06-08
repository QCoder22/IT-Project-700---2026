<?php
<<<<<<< HEAD
require_once __DIR__ . '/../config/db.php';
=======
>>>>>>> dd763ced52ef02d8918e40e9cfdd6c9f741fad9e
require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../includes/functions.php';

requireRole('doctor');

<<<<<<< HEAD
$ds = $pdo->prepare("SELECT doctor_id FROM doctors WHERE user_id = ?");
$ds->execute([$_SESSION['user_id']]);
$doctorId = $ds->fetchColumn();

$sql = "
SELECT a.appointment_id, a.appointment_date, a.reason, a.status,
       u.first_name, u.last_name
FROM appointments a
JOIN patients p ON a.patient_id = p.patient_id
JOIN users u ON p.user_id = u.user_id
WHERE a.doctor_id = ?
AND DATE(a.appointment_date) = CURDATE()
ORDER BY a.appointment_date ASC
";

$stmt = $pdo->prepare($sql);
$stmt->execute([$doctorId]);
$appointments = $stmt->fetchAll(PDO::FETCH_ASSOC);

$pageTitle = "Today's Appointments";
require_once __DIR__ . '/../includes/header.php';
?>

<h1>Today's Appointments</h1>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>Patient</th>
                    <th>Time</th>
                    <th>Reason</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>

                <?php if (empty($appointments)): ?>
                <tr><td colspan="4" class="text-muted">No appointments scheduled for today.</td></tr>
                <?php endif; ?>

                <?php foreach($appointments as $appointment): ?>
                <tr>
                    <td><?= e($appointment['first_name'] . ' ' . $appointment['last_name']); ?></td>
                    <td><?= e(formatDateTime($appointment['appointment_date'])); ?></td>
                    <td><?= e($appointment['reason']); ?></td>
                    <td><span class="badge bg-secondary"><?= e($appointment['status']); ?></span></td>
                </tr>
                <?php endforeach; ?>

            </tbody>
        </table>
=======
$u = currentUser();

$pageTitle = 'Doctor Dashboard';
require_once __DIR__ . '/../includes/header.php';
?>

<h1 class="mb-4">Doctor Dashboard</h1>

<div class="card">
    <div class="card-body">
        <h5 class="card-title">Welcome, <?= e($u['first_name']) ?>!</h5>
        <p class="card-text text-muted">
            This is a placeholder dashboard. The doctor module will be built in Week 2.
        </p>
        <hr>
        <p class="mb-1"><strong>Logged in as:</strong> <?= e($u['email']) ?></p>
        <p class="mb-0"><strong>Role:</strong> <?= e($u['role']) ?></p>
>>>>>>> dd763ced52ef02d8918e40e9cfdd6c9f741fad9e
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>

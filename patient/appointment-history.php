<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../includes/functions.php';

requireRole('patient');

// Resolve this user's patient_id (the session holds user_id, not patient_id)
$pidStmt = $pdo->prepare("SELECT patient_id FROM patients WHERE user_id = ?");
$pidStmt->execute([$_SESSION['user_id']]);
$patientId = $pidStmt->fetchColumn();

$appointments = [];

if ($patientId) {
    $query = "
        SELECT
            a.appointment_id,
            a.appointment_date,
            a.reason,
            a.status,
            u.first_name AS doctor_first,
            u.last_name  AS doctor_last
        FROM appointments a
        JOIN doctors d ON a.doctor_id = d.doctor_id
        JOIN users u ON d.user_id = u.user_id
        WHERE a.patient_id = ?
        ORDER BY a.appointment_date DESC
    ";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$patientId]);
    $appointments = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$pageTitle = 'Appointment History';
require_once __DIR__ . '/../includes/header.php';
?>

<h1>Appointment History</h1>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>Doctor</th>
                    <th>Date &amp; Time</th>
                    <th>Reason</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>

                <?php if (!empty($appointments)): ?>

                    <?php foreach ($appointments as $row): ?>
                    <tr>
                        <td>Dr. <?= e($row['doctor_first'] . ' ' . $row['doctor_last']) ?></td>
                        <td><?= e(formatDateTime($row['appointment_date'])) ?></td>
                        <td><?= e($row['reason'] ?: '—') ?></td>
                        <td><span class="badge bg-secondary"><?= e(ucfirst($row['status'])) ?></span></td>
                    </tr>
                    <?php endforeach; ?>

                <?php else: ?>

                    <tr><td colspan="4" class="text-muted">No appointments found.</td></tr>

                <?php endif; ?>

            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>

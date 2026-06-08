<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../includes/functions.php';

requireRole('doctor');

$ds = $pdo->prepare("SELECT doctor_id FROM doctors WHERE user_id = ?");
$ds->execute([$_SESSION['user_id']]);
$doctorId = $ds->fetchColumn();

$status = $_GET['status'] ?? '';

$sql = "
SELECT a.appointment_id, a.appointment_date, a.reason, a.status,
       u.first_name, u.last_name
FROM appointments a
JOIN patients p ON a.patient_id = p.patient_id
JOIN users u ON p.user_id = u.user_id
WHERE a.doctor_id = ?
";

$params = [$doctorId];

if (!empty($status)) {
    $sql .= " AND a.status = ?";
    $params[] = $status;
}

$sql .= " ORDER BY a.appointment_date ASC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$appointments = $stmt->fetchAll(PDO::FETCH_ASSOC);

$pageTitle = "My Appointments";
require_once __DIR__ . '/../includes/header.php';
?>

<h1>My Appointments</h1>

<form method="GET" class="card mb-3">
    <div class="card-body">
        <div class="row g-2 align-items-end">
            <div class="col-md-4">
                <label class="form-label">Filter by status</label>
                <select name="status" class="form-select">
                    <option value="">All</option>
                    <option value="scheduled"  <?= $status === 'scheduled'  ? 'selected' : ''; ?>>Scheduled</option>
                    <option value="completed"  <?= $status === 'completed'  ? 'selected' : ''; ?>>Completed</option>
                    <option value="cancelled"  <?= $status === 'cancelled'  ? 'selected' : ''; ?>>Cancelled</option>
                    <option value="no_show"    <?= $status === 'no_show'    ? 'selected' : ''; ?>>No Show</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">Filter</button>
            </div>
        </div>
    </div>
</form>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>Patient</th>
                    <th>Date &amp; Time</th>
                    <th>Reason</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>

                <?php if (empty($appointments)): ?>
                <tr><td colspan="5" class="text-muted">No appointments found.</td></tr>
                <?php endif; ?>

                <?php foreach($appointments as $appointment): ?>
                <tr>
                    <td><?= e($appointment['first_name'] . ' ' . $appointment['last_name']); ?></td>
                    <td><?= e(formatDateTime($appointment['appointment_date'])); ?></td>
                    <td><?= e($appointment['reason']); ?></td>
                    <td><span class="badge bg-secondary"><?= e($appointment['status']); ?></span></td>
                    <td>
                        <?php if ($appointment['status'] === 'scheduled'): ?>
                        <a class="btn btn-sm btn-outline-success"
                           href="<?= BASE_URL ?>/actions/update-appointment-status.php?id=<?= $appointment['appointment_id']; ?>&status=completed"
                           onclick="return confirm('Mark this appointment as completed?')">
                            Complete
                        </a>
                        <a class="btn btn-sm btn-outline-danger"
                           href="<?= BASE_URL ?>/actions/update-appointment-status.php?id=<?= $appointment['appointment_id']; ?>&status=cancelled"
                           onclick="return confirm('Cancel this appointment?')">
                            Cancel
                        </a>
                        <?php else: ?>
                        <span class="text-muted">&mdash;</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>

            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>

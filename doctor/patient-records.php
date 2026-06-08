<?php
require_once __DIR__ . "/../config/db.php";
require_once __DIR__ . "/../config/auth.php";
require_once __DIR__ . "/../includes/functions.php";

requireRole('doctor');

$patientId = $_GET['patient_id'] ?? null;

if ($patientId) {

    // Get patient details (joined with user)
    $stmt = $pdo->prepare("
        SELECT
            p.patient_id, p.dob, p.gender, p.address, p.insurance_info, p.medical_history,
            u.first_name, u.last_name, u.email, u.phone
        FROM patients p
        JOIN users u ON p.user_id = u.user_id
        WHERE p.patient_id = ?
    ");
    $stmt->execute([$patientId]);
    $patient = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$patient) {
        die("Patient not found.");
    }

    // Get past appointments (with consultation notes)
    $stmt = $pdo->prepare("
        SELECT
            a.appointment_id, a.appointment_date, a.reason, a.status, a.consultation_notes,
            u.first_name AS doctor_first, u.last_name AS doctor_last
        FROM appointments a
        JOIN doctors d ON a.doctor_id = d.doctor_id
        JOIN users u ON d.user_id = u.user_id
        WHERE a.patient_id = ?
        ORDER BY a.appointment_date DESC
    ");
    $stmt->execute([$patientId]);
    $appointments = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Get prescription history
    $stmt = $pdo->prepare("
        SELECT
            pr.prescription_id, pr.diagnosis, pr.status, pr.issued_at,
            u.first_name AS doctor_first, u.last_name AS doctor_last
        FROM prescriptions pr
        JOIN doctors d ON pr.doctor_id = d.doctor_id
        JOIN users u ON d.user_id = u.user_id
        WHERE pr.patient_id = ?
        ORDER BY pr.issued_at DESC
    ");
    $stmt->execute([$patientId]);
    $prescriptions = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$pageTitle = 'Patient Records';
require_once __DIR__ . "/../includes/header.php";
?>

<!-- =========================
     IF PATIENT SELECTED
========================= -->
<?php if ($patientId && isset($patient)): ?>

    <a href="patient-records.php" class="btn btn-sm btn-outline-secondary mb-3">
        <i class="bi bi-arrow-left"></i> Back to Patient List
    </a>

    <h1>Patient Records</h1>

    <?php include __DIR__ . "/../includes/patient-card.php"; ?>

    <?php if (!empty($patient['medical_history'])): ?>
        <div class="card mb-3">
            <div class="card-header"><strong>Medical History (Notes)</strong></div>
            <div class="card-body">
                <p class="mb-0"><?= nl2br(e($patient['medical_history'])) ?></p>
            </div>
        </div>
    <?php endif; ?>

    <div class="card mb-3">
        <div class="card-header"><strong>Past Appointments</strong></div>
        <?php if (empty($appointments)): ?>
            <div class="card-body text-muted">No appointment history for this patient.</div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table mb-0">
                <thead>
                <tr>
                    <th>Date</th>
                    <th>Doctor</th>
                    <th>Reason</th>
                    <th>Status</th>
                    <th>Notes</th>
                </tr>
                </thead>
                <tbody>
                    <?php foreach ($appointments as $row): ?>
                        <tr>
                            <td><?= e(formatDateTime($row['appointment_date'])) ?></td>
                            <td><?= e('Dr. ' . $row['doctor_last']) ?></td>
                            <td><?= e($row['reason']) ?></td>
                            <td><span class="badge bg-secondary"><?= e($row['status']) ?></span></td>
                            <td><?= e($row['consultation_notes'] ?? '—') ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>

    <div class="card mb-3">
        <div class="card-header"><strong>Prescription History</strong></div>
        <?php if (empty($prescriptions)): ?>
            <div class="card-body text-muted">No prescriptions on record.</div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table mb-0">
                <thead>
                <tr>
                    <th>Issued</th>
                    <th>Doctor</th>
                    <th>Diagnosis</th>
                    <th>Status</th>
                </tr>
                </thead>
                <tbody>
                    <?php foreach ($prescriptions as $row): ?>
                        <tr>
                            <td><?= e(formatDateTime($row['issued_at'])) ?></td>
                            <td><?= e('Dr. ' . $row['doctor_last']) ?></td>
                            <td><?= e($row['diagnosis']) ?></td>
                            <td><span class="badge bg-secondary"><?= e($row['status']) ?></span></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>

<!-- =========================
     ELSE → PATIENT LIST
========================= -->
<?php else: ?>

    <h1>Patient Records</h1>
    <p class="text-muted">Select a patient to view their medical history.</p>

    <?php
    $stmt = $pdo->query("
        SELECT
            p.patient_id, p.dob,
            u.first_name, u.last_name
        FROM patients p
        JOIN users u ON p.user_id = u.user_id
        ORDER BY u.last_name, u.first_name
    ");
    $patients = $stmt->fetchAll(PDO::FETCH_ASSOC);
    ?>

    <div class="card">
        <div class="card-header"><strong>All Patients (<?= count($patients) ?>)</strong></div>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
            <thead>
            <tr>
                <th>Name</th>
                <th>Date of Birth</th>
                <th>Action</th>
            </tr>
            </thead>
            <tbody>
                <?php foreach ($patients as $p): ?>
                    <tr>
                        <td><?= e($p['first_name'] . ' ' . $p['last_name']) ?></td>
                        <td><?= e(formatDate($p['dob'])) ?></td>
                        <td>
                            <a class="btn btn-sm btn-primary"
                               href="patient-records.php?patient_id=<?= (int)$p['patient_id'] ?>">
                                View Medical History
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
            </table>
        </div>
    </div>

<?php endif; ?>

<?php require_once __DIR__ . "/../includes/footer.php"; ?>

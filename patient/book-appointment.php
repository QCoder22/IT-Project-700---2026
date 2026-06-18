<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../includes/functions.php';

requireRole('patient');

// Find this patient's record (joined with their user info)
$stmt = $pdo->prepare("
    SELECT p.patient_id, p.dob, p.gender, p.address,
           u.first_name, u.last_name, u.email, u.phone
    FROM patients p
    JOIN users u ON p.user_id = u.user_id
    WHERE u.user_id = ?
");
$stmt->execute([$_SESSION['user_id']]);
$patient = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$patient) {
    die("Patient not found.");
}

// Doctors for the dropdown (names live on users)
$doctorStmt = $pdo->query("
    SELECT d.doctor_id, d.specialization, u.first_name, u.last_name
    FROM doctors d
    JOIN users u ON d.user_id = u.user_id
    WHERE u.is_active = 1
    ORDER BY u.last_name, u.first_name
");
$doctors = $doctorStmt->fetchAll(PDO::FETCH_ASSOC);

$pageTitle = 'Book Appointment';
require_once __DIR__ . '/../includes/header.php';
?>

<h1>Book Appointment</h1>

<div class="card mb-3">
    <div class="card-header"><strong>Patient Information</strong></div>
    <div class="card-body">
        <p class="mb-1"><strong>Name:</strong> <?= e($patient['first_name'] . ' ' . $patient['last_name']) ?></p>
        <p class="mb-1"><strong>Email:</strong> <?= e($patient['email']) ?></p>
        <p class="mb-1"><strong>Phone:</strong> <?= e($patient['phone']) ?></p>
        <p class="mb-1"><strong>Date of Birth:</strong> <?= e(formatDate($patient['dob'])) ?></p>
        <p class="mb-1"><strong>Gender:</strong> <?= e($patient['gender']) ?></p>
        <p class="mb-0"><strong>Address:</strong> <?= e($patient['address']) ?></p>
    </div>
</div>

<div class="card">
    <div class="card-header"><strong>Appointment Details</strong></div>
    <div class="card-body">

        <form action="<?= BASE_URL ?>/actions/book-appointment-action.php" method="POST">

            <input type="hidden" name="patient_id" value="<?= (int)$patient['patient_id'] ?>">

            <div class="mb-3">
                <label class="form-label">Select Doctor</label>
                <select name="doctor_id" class="form-select" required>
                    <option value="">-- Choose Doctor --</option>
                    <?php foreach ($doctors as $doctor): ?>
                        <option value="<?= (int)$doctor['doctor_id'] ?>">
                            Dr. <?= e($doctor['first_name'] . ' ' . $doctor['last_name']) ?>
                            (<?= e($doctor['specialization']) ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Appointment Date &amp; Time</label>
                <input type="datetime-local" name="appointment_date" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Reason for Visit</label>
                <textarea name="reason" class="form-control" rows="3"></textarea>
            </div>

            <button type="submit" class="btn btn-primary">Book Appointment</button>

        </form>

    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>

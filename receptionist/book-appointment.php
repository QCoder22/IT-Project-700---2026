<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../includes/functions.php';

requireRole('receptionist');

$patients = $pdo->query("
    SELECT p.patient_id, u.first_name, u.last_name
    FROM patients p
    JOIN users u ON p.user_id = u.user_id
    ORDER BY u.last_name, u.first_name
")->fetchAll(PDO::FETCH_ASSOC);

$doctors = $pdo->query("
    SELECT d.doctor_id, d.specialization, u.first_name, u.last_name
    FROM doctors d
    JOIN users u ON d.user_id = u.user_id
    ORDER BY u.last_name, u.first_name
")->fetchAll(PDO::FETCH_ASSOC);

$pageTitle = "Book Appointment";
require_once __DIR__ . '/../includes/header.php';
?>

<h1>Book Appointment</h1>
<p class="text-muted">Schedule an appointment on a patient's behalf.</p>

<form action="<?= BASE_URL ?>/actions/book-appointment-action.php" method="POST" class="card">
    <div class="card-body">

        <div class="row g-3">

            <div class="col-md-6">
                <label class="form-label">Patient</label>
                <select name="patient_id" class="form-select" required>
                    <option value="">Select patient</option>
                    <?php foreach($patients as $patient): ?>
                    <option value="<?= $patient['patient_id']; ?>">
                        <?= e($patient['first_name'] . ' ' . $patient['last_name']); ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-md-6">
                <label class="form-label">Doctor</label>
                <select name="doctor_id" class="form-select" required>
                    <option value="">Select doctor</option>
                    <?php foreach($doctors as $doctor): ?>
                    <option value="<?= $doctor['doctor_id']; ?>">
                        Dr. <?= e($doctor['first_name'] . ' ' . $doctor['last_name']); ?>
                        (<?= e($doctor['specialization']); ?>)
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-md-6">
                <label class="form-label">Date &amp; Time</label>
                <input type="datetime-local" name="appointment_date" class="form-control" required>
            </div>

            <div class="col-md-12">
                <label class="form-label">Reason</label>
                <textarea name="reason" class="form-control" rows="3"></textarea>
            </div>

        </div>

        <br>

        <button type="submit" class="btn btn-primary">Book Appointment</button>
        <a href="<?= BASE_URL ?>/receptionist/dashboard.php" class="btn btn-outline-secondary">Cancel</a>

    </div>
</form>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>

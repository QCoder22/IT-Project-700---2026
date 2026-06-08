<?php
require_once __DIR__ . "/../config/db.php";
require_once __DIR__ . "/../config/auth.php";
require_once __DIR__ . "/../includes/functions.php";

requireRole('patient');

$u = currentUser();

// Fetch upcoming appointment count for the card
$stmt = $pdo->prepare("
    SELECT COUNT(*) AS upcoming
    FROM appointments a
    JOIN patients p ON a.patient_id = p.patient_id
    WHERE p.user_id = ?
    AND a.status = 'scheduled'
    AND a.appointment_date >= NOW()
");
$stmt->execute([$_SESSION['user_id']]);
$upcoming = (int)($stmt->fetch()['upcoming'] ?? 0);

$pageTitle = 'Patient Dashboard';
require_once __DIR__ . "/../includes/header.php";
?>

<h1>Welcome, <?= e($u['first_name']) ?> 👋</h1>
<p class="text-muted">Here's what's available to you.</p>

<div class="row g-3 mt-2">

    <div class="col-md-4">
        <div class="card text-center">
            <div class="card-body">
                <i class="bi bi-person-circle" style="font-size: 2.5rem; color: var(--medicom-primary);"></i>
                <h3 class="mt-2">My Profile</h3>
                <p class="text-muted">View and edit your personal details.</p>
                <a href="profile.php" class="btn btn-primary">View</a>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card text-center">
            <div class="card-body">
                <i class="bi bi-clock-history" style="font-size: 2.5rem; color: var(--medicom-primary);"></i>
                <h3 class="mt-2">Appointments</h3>
                <p class="text-muted"><?= $upcoming ?> upcoming.</p>
                <a href="appointment-history.php" class="btn btn-primary">View</a>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card text-center">
            <div class="card-body">
                <i class="bi bi-calendar-plus" style="font-size: 2.5rem; color: var(--medicom-primary);"></i>
                <h3 class="mt-2">Book Appointment</h3>
                <p class="text-muted">Schedule a new appointment.</p>
                <a href="book-appointment.php" class="btn btn-primary">Book</a>
            </div>
        </div>
    </div>

</div>

<?php require_once __DIR__ . "/../includes/footer.php"; ?>

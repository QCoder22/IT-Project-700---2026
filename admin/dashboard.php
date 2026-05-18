<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../includes/functions.php';

requireRole('admin');

$patientCount = $pdo->query("SELECT COUNT(*) FROM patients")->fetchColumn();
$doctorCount = $pdo->query("SELECT COUNT(*) FROM doctors")->fetchColumn();
$apptCount = $pdo->query("SELECT COUNT(*) FROM appointments WHERE status = 'scheduled'")->fetchColumn();
$pendingResets = $pdo->query("SELECT COUNT(*) FROM password_reset_requests WHERE status = 'pending'")->fetchColumn();

$u = currentUser();

$pageTitle = 'Admin Dashboard';
require_once __DIR__ . '/../includes/header.php';
?>

<h1 class="mb-4">Admin Dashboard</h1>

<p class="text-muted">Welcome back, <?= e($u['first_name']) ?>.</p>

<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <i class="bi bi-person-lines-fill" style="font-size: 2rem; color: var(--medicom-primary);"></i>
                <h2 class="mt-2"><?= (int)$patientCount ?></h2>
                <p class="text-muted mb-0">Patients</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <i class="bi bi-person-badge" style="font-size: 2rem; color: var(--medicom-primary);"></i>
                <h2 class="mt-2"><?= (int)$doctorCount ?></h2>
                <p class="text-muted mb-0">Doctors</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <i class="bi bi-calendar-week" style="font-size: 2rem; color: var(--medicom-primary);"></i>
                <h2 class="mt-2"><?= (int)$apptCount ?></h2>
                <p class="text-muted mb-0">Scheduled Appointments</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <a href="<?= BASE_URL ?>/admin/password-resets.php" class="text-decoration-none">
            <div class="card text-center">
                <div class="card-body">
                    <i class="bi bi-key" style="font-size: 2rem; color: var(--medicom-primary);"></i>
                    <h2 class="mt-2"><?= (int)$pendingResets ?></h2>
                    <p class="text-muted mb-0">Pending Password Resets</p>
                </div>
            </div>
        </a>
    </div>
</div>

<div class="card">
    <div class="card-header"><strong>Quick Actions</strong></div>
    <div class="card-body">
        <a href="<?= BASE_URL ?>/admin/manage-doctors.php" class="btn btn-outline-primary me-2">
            <i class="bi bi-person-badge"></i> Manage Doctors
        </a>
        <a href="<?= BASE_URL ?>/admin/manage-staff.php" class="btn btn-outline-primary me-2">
            <i class="bi bi-people"></i> Manage Staff
        </a>
        <a href="<?= BASE_URL ?>/admin/view-patients.php" class="btn btn-outline-primary me-2">
            <i class="bi bi-person-lines-fill"></i> View Patients
        </a>
        <a href="<?= BASE_URL ?>/admin/inventory.php" class="btn btn-outline-primary">
            <i class="bi bi-capsule"></i> Inventory
        </a>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>

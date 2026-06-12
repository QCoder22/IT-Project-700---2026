<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../includes/functions.php';

requireRole('admin');

// Editing? Load the doctor being edited.
$editId = $_GET['edit'] ?? null;
$editDoctor = null;

if ($editId) {
    $stmt = $pdo->prepare("
        SELECT d.doctor_id, d.specialization, d.license_number, d.consultation_fee,
               u.user_id, u.first_name, u.last_name, u.email, u.phone
        FROM doctors d
        JOIN users u ON d.user_id = u.user_id
        WHERE d.doctor_id = ?
    ");
    $stmt->execute([$editId]);
    $editDoctor = $stmt->fetch(PDO::FETCH_ASSOC);
}

// List all doctors
$doctors = $pdo->query("
    SELECT d.doctor_id, d.specialization, d.license_number, d.consultation_fee,
           u.first_name, u.last_name, u.email, u.phone, u.is_active
    FROM doctors d
    JOIN users u ON d.user_id = u.user_id
    ORDER BY u.last_name, u.first_name
")->fetchAll(PDO::FETCH_ASSOC);

$old = $_SESSION['old'] ?? [];
unset($_SESSION['old']);

$pageTitle = 'Manage Doctors';
require_once __DIR__ . '/../includes/header.php';
?>

<h1 class="mb-4">Manage Doctors</h1>

<div class="card mb-4">
    <div class="card-header">
        <strong><?= $editDoctor ? 'Edit Doctor' : 'Add New Doctor' ?></strong>
    </div>
    <div class="card-body">

        <form method="POST" action="<?= BASE_URL ?>/actions/manage-doctor-action.php">

            <?php if ($editDoctor): ?>
                <input type="hidden" name="doctor_id" value="<?= (int)$editDoctor['doctor_id'] ?>">
                <input type="hidden" name="user_id" value="<?= (int)$editDoctor['user_id'] ?>">
            <?php endif; ?>

            <div class="row g-3">

                <div class="col-md-6">
                    <label class="form-label">First Name</label>
                    <input class="form-control" type="text" name="first_name"
                           value="<?= e($editDoctor['first_name'] ?? $old['first_name'] ?? '') ?>" required>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Last Name</label>
                    <input class="form-control" type="text" name="last_name"
                           value="<?= e($editDoctor['last_name'] ?? $old['last_name'] ?? '') ?>" required>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Email</label>
                    <input class="form-control" type="email" name="email"
                           value="<?= e($editDoctor['email'] ?? $old['email'] ?? '') ?>" required>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Phone</label>
                    <input class="form-control" type="text" name="phone"
                           value="<?= e($editDoctor['phone'] ?? $old['phone'] ?? '') ?>">
                </div>

                <div class="col-md-6">
                    <label class="form-label">Specialization</label>
                    <input class="form-control" type="text" name="specialization"
                           value="<?= e($editDoctor['specialization'] ?? $old['specialization'] ?? 'General Practitioner') ?>" required>
                </div>

                <div class="col-md-3">
                    <label class="form-label">License Number</label>
                    <input class="form-control" type="text" name="license_number"
                           value="<?= e($editDoctor['license_number'] ?? $old['license_number'] ?? '') ?>">
                </div>

                <div class="col-md-3">
                    <label class="form-label">Consultation Fee (R)</label>
                    <input class="form-control" type="number" step="0.01" min="0" name="consultation_fee"
                           value="<?= e($editDoctor['consultation_fee'] ?? $old['consultation_fee'] ?? '0.00') ?>" required>
                </div>

                <?php if (!$editDoctor): ?>
                <div class="col-md-6">
                    <label class="form-label">Initial Password</label>
                    <input class="form-control" type="password" name="password" required minlength="8">
                </div>
                <?php endif; ?>

            </div>

            <br>

            <button type="submit" class="btn btn-primary">
                <?= $editDoctor ? 'Update Doctor' : 'Add Doctor' ?>
            </button>

            <?php if ($editDoctor): ?>
                <a href="manage-doctors.php" class="btn btn-outline-secondary">Cancel</a>
            <?php endif; ?>

        </form>

        <?php if (!$editDoctor): ?>
        <small class="text-muted d-block mt-3">
            Set an initial password and share it with the doctor. They will be required to change it on first login.
        </small>
        <?php endif; ?>

    </div>
</div>

<div class="card">
    <div class="card-header"><strong>Doctors (<?= count($doctors) ?>)</strong></div>
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Specialization</th>
                    <th>License</th>
                    <th>Fee</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>

                <?php if (empty($doctors)): ?>
                <tr><td colspan="6" class="text-muted">No doctors yet.</td></tr>
                <?php endif; ?>

                <?php foreach ($doctors as $d): ?>
                <tr>
                    <td>Dr. <?= e($d['first_name'] . ' ' . $d['last_name']) ?></td>
                    <td><?= e($d['specialization']) ?></td>
                    <td><?= e($d['license_number'] ?: '—') ?></td>
                    <td><?= e(formatCurrency($d['consultation_fee'])) ?></td>
                    <td>
                        <?php if ($d['is_active']): ?>
                            <span class="badge bg-success">Active</span>
                        <?php else: ?>
                            <span class="badge bg-secondary">Inactive</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <a class="btn btn-sm btn-outline-primary"
                           href="manage-doctors.php?edit=<?= (int)$d['doctor_id'] ?>">Edit</a>

                        <?php if ($d['is_active']): ?>
                        <a class="btn btn-sm btn-outline-danger"
                           href="<?= BASE_URL ?>/actions/manage-doctor-action.php?deactivate=<?= (int)$d['doctor_id'] ?>"
                           onclick="return confirm('Deactivate this doctor? They will no longer be able to log in.')">Deactivate</a>
                        <?php else: ?>
                        <a class="btn btn-sm btn-outline-success"
                           href="<?= BASE_URL ?>/actions/manage-doctor-action.php?activate=<?= (int)$d['doctor_id'] ?>"
                           onclick="return confirm('Reactivate this doctor?')">Activate</a>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>

            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>

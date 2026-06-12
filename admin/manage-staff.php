<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../includes/functions.php';

requireRole('admin');

// Editing?
$editId = $_GET['edit'] ?? null;
$editStaff = null;

if ($editId) {
    $stmt = $pdo->prepare("
        SELECT user_id, first_name, last_name, email, phone
        FROM users
        WHERE user_id = ? AND role = 'receptionist'
    ");
    $stmt->execute([$editId]);
    $editStaff = $stmt->fetch(PDO::FETCH_ASSOC);
}

// List receptionists
$staff = $pdo->query("
    SELECT user_id, first_name, last_name, email, phone, is_active
    FROM users
    WHERE role = 'receptionist'
    ORDER BY last_name, first_name
")->fetchAll(PDO::FETCH_ASSOC);

$old = $_SESSION['old'] ?? [];
unset($_SESSION['old']);

$pageTitle = 'Manage Staff';
require_once __DIR__ . '/../includes/header.php';
?>

<h1 class="mb-4">Manage Staff</h1>
<p class="text-muted">Receptionist accounts.</p>

<div class="card mb-4">
    <div class="card-header">
        <strong><?= $editStaff ? 'Edit Receptionist' : 'Add New Receptionist' ?></strong>
    </div>
    <div class="card-body">

        <form method="POST" action="<?= BASE_URL ?>/actions/manage-staff-action.php">

            <?php if ($editStaff): ?>
                <input type="hidden" name="user_id" value="<?= (int)$editStaff['user_id'] ?>">
            <?php endif; ?>

            <div class="row g-3">

                <div class="col-md-6">
                    <label class="form-label">First Name</label>
                    <input class="form-control" type="text" name="first_name"
                           value="<?= e($editStaff['first_name'] ?? $old['first_name'] ?? '') ?>" required>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Last Name</label>
                    <input class="form-control" type="text" name="last_name"
                           value="<?= e($editStaff['last_name'] ?? $old['last_name'] ?? '') ?>" required>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Email</label>
                    <input class="form-control" type="email" name="email"
                           value="<?= e($editStaff['email'] ?? $old['email'] ?? '') ?>" required>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Phone</label>
                    <input class="form-control" type="text" name="phone"
                           value="<?= e($editStaff['phone'] ?? $old['phone'] ?? '') ?>">
                </div>

                <?php if (!$editStaff): ?>
                <div class="col-md-6">
                    <label class="form-label">Initial Password</label>
                    <input class="form-control" type="password" name="password" required minlength="8">
                </div>
                <?php endif; ?>

            </div>

            <br>

            <button type="submit" class="btn btn-primary">
                <?= $editStaff ? 'Update Receptionist' : 'Add Receptionist' ?>
            </button>

            <?php if ($editStaff): ?>
                <a href="manage-staff.php" class="btn btn-outline-secondary">Cancel</a>
            <?php endif; ?>

        </form>

        <?php if (!$editStaff): ?>
        <small class="text-muted d-block mt-3">
            Set an initial password and share it with the receptionist. They will be required to change it on first login.
        </small>
        <?php endif; ?>

    </div>
</div>

<div class="card">
    <div class="card-header"><strong>Receptionists (<?= count($staff) ?>)</strong></div>
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>

                <?php if (empty($staff)): ?>
                <tr><td colspan="5" class="text-muted">No receptionists yet.</td></tr>
                <?php endif; ?>

                <?php foreach ($staff as $s): ?>
                <tr>
                    <td><?= e($s['first_name'] . ' ' . $s['last_name']) ?></td>
                    <td><?= e($s['email']) ?></td>
                    <td><?= e($s['phone'] ?: '—') ?></td>
                    <td>
                        <?php if ($s['is_active']): ?>
                            <span class="badge bg-success">Active</span>
                        <?php else: ?>
                            <span class="badge bg-secondary">Inactive</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <a class="btn btn-sm btn-outline-primary"
                           href="manage-staff.php?edit=<?= (int)$s['user_id'] ?>">Edit</a>

                        <?php if ($s['is_active']): ?>
                        <a class="btn btn-sm btn-outline-danger"
                           href="<?= BASE_URL ?>/actions/manage-staff-action.php?deactivate=<?= (int)$s['user_id'] ?>"
                           onclick="return confirm('Deactivate this receptionist? They will no longer be able to log in.')">Deactivate</a>
                        <?php else: ?>
                        <a class="btn btn-sm btn-outline-success"
                           href="<?= BASE_URL ?>/actions/manage-staff-action.php?activate=<?= (int)$s['user_id'] ?>"
                           onclick="return confirm('Reactivate this receptionist?')">Activate</a>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>

            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>

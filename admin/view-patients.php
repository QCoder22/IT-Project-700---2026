<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../includes/functions.php';

requireRole('admin');

$search = trim($_GET['q'] ?? '');

$sql = "
    SELECT
        p.patient_id,
        p.dob,
        p.gender,
        u.first_name,
        u.last_name,
        u.email,
        u.phone,
        u.is_active
    FROM patients p
    JOIN users u ON p.user_id = u.user_id
";

$params = [];

if ($search !== '') {
    $sql .= " WHERE u.first_name LIKE ? OR u.last_name LIKE ? OR u.email LIKE ? OR u.phone LIKE ? ";
    $like = "%$search%";
    $params = [$like, $like, $like, $like];
}

$sql .= " ORDER BY u.last_name, u.first_name";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$patients = $stmt->fetchAll(PDO::FETCH_ASSOC);

$pageTitle = 'View Patients';
require_once __DIR__ . '/../includes/header.php';
?>

<h1 class="mb-4">View Patients</h1>

<form method="GET" class="card mb-3">
    <div class="card-body">
        <div class="row g-2">
            <div class="col-md-9">
                <input class="form-control" type="text" name="q"
                       placeholder="Search by name, email, or phone"
                       value="<?= e($search) ?>">
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-search"></i> Search
                </button>
            </div>
        </div>
    </div>
</form>

<div class="card">
    <div class="card-header">
        <strong>Patients (<?= count($patients) ?>)</strong>
    </div>
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Date of Birth</th>
                    <th>Gender</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>

                <?php if (empty($patients)): ?>
                <tr><td colspan="6" class="text-muted">No patients found.</td></tr>
                <?php endif; ?>

                <?php foreach ($patients as $p): ?>
                <tr>
                    <td><?= e($p['first_name'] . ' ' . $p['last_name']) ?></td>
                    <td><?= e($p['email']) ?></td>
                    <td><?= e($p['phone']) ?></td>
                    <td><?= e(formatDate($p['dob'])) ?></td>
                    <td><?= e($p['gender']) ?></td>
                    <td>
                        <?php if ($p['is_active']): ?>
                            <span class="badge bg-success">Active</span>
                        <?php else: ?>
                            <span class="badge bg-secondary">Inactive</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>

            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>

<?php
require_once __DIR__ . "/../config/db.php";
require_once __DIR__ . "/../config/auth.php";
require_once __DIR__ . "/../includes/functions.php";

requireRole('receptionist');

$results = [];
$query = "";

// Run search only when form submitted
if (isset($_GET['query']) && $_GET['query'] !== "") {

    $query = trim($_GET['query']);
    $search = "%$query%";

    $stmt = $pdo->prepare("
        SELECT
            p.patient_id, p.dob, p.gender, p.address, p.insurance_info,
            u.first_name, u.last_name, u.email, u.phone
        FROM patients p
        JOIN users u ON p.user_id = u.user_id
        WHERE u.first_name LIKE ?
        OR u.last_name LIKE ?
        OR u.phone LIKE ?
        OR u.email LIKE ?
        ORDER BY u.last_name, u.first_name
    ");

    $stmt->execute([$search, $search, $search, $search]);

    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$pageTitle = 'Search Patient';
require_once __DIR__ . "/../includes/header.php";
?>

<h1>Search Patient</h1>

<form method="GET" class="card mb-3">
    <div class="card-body">
        <div class="row g-2">
            <div class="col-md-9">
                <input class="form-control" type="text" name="query"
                       placeholder="Search by name, email, or phone"
                       value="<?= e($query) ?>" autofocus>
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-search"></i> Search
                </button>
            </div>
        </div>
    </div>
</form>

<?php if (isset($_GET['query']) && $_GET['query'] !== ""): ?>

    <?php if (!empty($results)): ?>

        <p class="text-muted"><?= count($results) ?> patient<?= count($results) === 1 ? '' : 's' ?> found.</p>

        <?php foreach ($results as $patient): ?>
            <?php include __DIR__ . "/../includes/patient-card.php"; ?>
        <?php endforeach; ?>

    <?php else: ?>

        <div class="alert alert-warning">
            <i class="bi bi-info-circle"></i> No patients found matching "<?= e($query) ?>".
        </div>

    <?php endif; ?>

<?php else: ?>

    <p class="text-muted">Enter a name, email, or phone to search.</p>

<?php endif; ?>

<?php require_once __DIR__ . "/../includes/footer.php"; ?>

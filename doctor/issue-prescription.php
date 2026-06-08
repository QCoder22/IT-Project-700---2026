<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../includes/functions.php';

requireRole('doctor');

// Find the doctor's record from their user_id
$me = $pdo->prepare("SELECT doctor_id FROM doctors WHERE user_id=?");
$me->execute([$_SESSION['user_id']]);
$doc = $me->fetch();
$doctor_id = $doc['doctor_id'] ?? null;

// Doctor selects which appointment they're prescribing for
$appointment_id = $_GET['appointment_id'] ?? null;
$appt = null;
if ($appointment_id) {
    $stmt = $pdo->prepare("
        SELECT a.*, p.patient_id, u.first_name, u.last_name
        FROM appointments a
        JOIN patients p ON a.patient_id = p.patient_id
        JOIN users u ON p.user_id = u.user_id
        WHERE a.appointment_id=? AND a.doctor_id=?
    ");
    $stmt->execute([$appointment_id, $doctor_id]);
    $appt = $stmt->fetch();
}

$pageTitle = 'Issue Prescription';
require_once __DIR__ . '/../includes/header.php';
?>

<h1>Issue Prescription</h1>

<?php if (!$appt): ?>

    <div class="card">
        <div class="card-header"><strong>Select an appointment</strong></div>
        <div class="card-body">
            <p class="text-muted">Choose a completed appointment to issue a prescription for:</p>
            <?php
            $list = $pdo->prepare("
                SELECT a.appointment_id, a.appointment_date, u.first_name, u.last_name
                FROM appointments a
                JOIN patients p ON a.patient_id = p.patient_id
                JOIN users u ON p.user_id = u.user_id
                WHERE a.doctor_id=? AND a.status IN ('completed','scheduled')
                ORDER BY a.appointment_date DESC
            ");
            $list->execute([$doctor_id]);
            while ($a = $list->fetch()) {
                echo "<a class='btn btn-outline-primary me-2 mb-2' href='issue-prescription.php?appointment_id={$a['appointment_id']}'>"
                    . e($a['first_name'] . ' ' . $a['last_name']) . " — " . formatDateTime($a['appointment_date'])
                    . "</a>";
            }
            ?>
        </div>
    </div>

<?php else: ?>

    <p class="text-muted">For: <strong><?= e($appt['first_name'] . ' ' . $appt['last_name']) ?></strong>
       — <?= formatDateTime($appt['appointment_date']) ?></p>

    <form method="POST" action="<?= BASE_URL ?>/actions/issue-prescription-action.php" class="card">
        <input type="hidden" name="appointment_id" value="<?= (int)$appt['appointment_id'] ?>">
        <input type="hidden" name="patient_id" value="<?= (int)$appt['patient_id'] ?>">

        <div class="card-body row g-2">
            <div class="col-md-12">
                <label class="form-label">Diagnosis</label>
                <input class="form-control" name="diagnosis" placeholder="e.g. Hypertension">
            </div>

            <div class="col-md-6">
                <label class="form-label">Medication</label>
                <select class="form-select" name="inventory_id" onchange="checkStock(this.value)">
                <?php
                $stmt = $pdo->query("SELECT inventory_id, item_name FROM inventory ORDER BY item_name");
                while ($m = $stmt->fetch()) {
                    echo "<option value='{$m['inventory_id']}'>" . e($m['item_name']) . "</option>";
                }
                ?>
                </select>
            </div>

            <div class="col-md-3">
                <label class="form-label">Dosage</label>
                <input class="form-control" name="dosage" placeholder="e.g. 500mg" required>
            </div>
            <div class="col-md-3">
                <label class="form-label">Frequency</label>
                <input class="form-control" name="frequency" placeholder="e.g. Twice daily" required>
            </div>
            <div class="col-md-3">
                <label class="form-label">Duration (days)</label>
                <input class="form-control" name="duration_days" type="number" value="7" required>
            </div>
            <div class="col-md-3">
                <label class="form-label">Quantity</label>
                <input class="form-control" name="quantity" type="number" required>
            </div>

            <div class="col-md-12">
                <button class="btn btn-primary">Add Prescription</button>
                <span id="stockInfo" class="ms-3"></span>
            </div>
        </div>
    </form>

<?php endif; ?>

<script>
function checkStock(id){
    fetch("<?= BASE_URL ?>/actions/check-stock.php?id=" + id)
    .then(res => res.json())
    .then(data => {
        let status = "🟢 In Stock";
        if(!data.available) status = "🔴 Out of Stock";
        if(data.low_stock) status = "⚠️ Low Stock";

        document.getElementById("stockInfo").innerHTML = status;
    });
}
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>

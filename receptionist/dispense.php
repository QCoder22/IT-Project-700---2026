<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../includes/functions.php';

requireRole('receptionist');

$prescription_id = $_GET['id'] ?? null;

$rx = null;
$items = [];
if ($prescription_id) {
    $stmt = $pdo->prepare("
        SELECT pr.*, pu.first_name AS pf, pu.last_name AS pl, du.last_name AS dl
        FROM prescriptions pr
        JOIN patients p ON pr.patient_id = p.patient_id
        JOIN users pu ON p.user_id = pu.user_id
        JOIN doctors d ON pr.doctor_id = d.doctor_id
        JOIN users du ON d.user_id = du.user_id
        WHERE pr.prescription_id=?
    ");
    $stmt->execute([$prescription_id]);
    $rx = $stmt->fetch();

    $stmt = $pdo->prepare("
        SELECT pi.*, i.item_name, i.quantity AS in_stock
        FROM prescription_items pi
        JOIN inventory i ON pi.inventory_id = i.inventory_id
        WHERE pi.prescription_id=?
    ");
    $stmt->execute([$prescription_id]);
    $items = $stmt->fetchAll();
}

$pageTitle = 'Dispense Prescription';
require_once __DIR__ . '/../includes/header.php';
?>

<h1>Dispense Prescription</h1>

<?php if (!$rx): ?>

    <div class="card">
        <div class="card-body">
            <p>Enter a prescription ID to dispense:</p>
            <form method="GET" class="row g-2">
                <div class="col-md-3">
                    <input class="form-control" name="id" placeholder="Prescription ID" required>
                </div>
                <div class="col-md-2">
                    <button class="btn btn-primary w-100">Look up</button>
                </div>
            </form>
        </div>
    </div>

<?php else: ?>

    <div class="card mb-3">
        <div class="card-header"><strong>Prescription #<?= (int)$rx['prescription_id'] ?></strong></div>
        <div class="card-body">
            <p><strong>Patient:</strong> <?= e($rx['pf'].' '.$rx['pl']) ?></p>
            <p><strong>Doctor:</strong> <?= e('Dr. '.$rx['dl']) ?></p>
            <p><strong>Diagnosis:</strong> <?= e($rx['diagnosis']) ?></p>
            <p><strong>Status:</strong> <span class="badge bg-warning text-dark"><?= e($rx['status']) ?></span></p>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-header"><strong>Medications</strong></div>
        <div class="table-responsive">
            <table class="table mb-0">
            <thead>
            <tr><th>Medication</th><th>Dosage</th><th>Qty</th><th>In Stock</th></tr>
            </thead>
            <tbody>
            <?php foreach ($items as $i): ?>
                <tr>
                    <td><?= e($i['item_name']) ?></td>
                    <td><?= e($i['dosage']) ?> — <?= e($i['frequency']) ?></td>
                    <td><?= (int)$i['quantity'] ?></td>
                    <td><?= (int)$i['in_stock'] ?>
                        <?php if ($i['in_stock'] < $i['quantity']): ?>
                            <span class="badge bg-danger">Insufficient</span>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
            </table>
        </div>
    </div>

    <form method="POST" action="<?= BASE_URL ?>/actions/dispense-action.php">
        <input type="hidden" name="prescription_id" value="<?= (int)$rx['prescription_id'] ?>">
        <button class="btn btn-primary" <?= $rx['status'] === 'dispensed' ? 'disabled' : '' ?>>
            <?= $rx['status'] === 'dispensed' ? 'Already Dispensed' : 'Confirm Dispense' ?>
        </button>
        <a class="btn btn-outline-secondary" href="prescription-queue.php">Cancel</a>
    </form>

<?php endif; ?>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>

<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../includes/functions.php';

requireRole('admin');

// ADD MEDICATION
if (isset($_POST['add'])) {
    $stmt = $pdo->prepare("
        INSERT INTO inventory
        (item_name, generic_name, unit, quantity, unit_price, expiration_date, minimum_threshold)
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ");

    $stmt->execute([
        $_POST['item_name'],
        $_POST['generic_name'],
        $_POST['unit'] ?: 'tablet',
        $_POST['quantity'],
        $_POST['unit_price'],
        $_POST['expiration_date'] ?: null,
        $_POST['minimum_threshold']
    ]);

    setMsg('success', 'Medication added.');
    redirect('/admin/inventory.php');
}

// DELETE MEDICATION
if (isset($_GET['delete'])) {
    $stmt = $pdo->prepare("DELETE FROM inventory WHERE inventory_id=?");
    $stmt->execute([$_GET['delete']]);
    setMsg('success', 'Medication deleted.');
    redirect('/admin/inventory.php');
}

$pageTitle = 'Inventory Management';
require_once __DIR__ . '/../includes/header.php';
?>

<h1>Inventory Management</h1>

<!-- ADD FORM -->
<div class="card mb-4">
    <div class="card-header"><strong>Add Medication</strong></div>
    <div class="card-body">
        <form method="POST" class="row g-2">
            <div class="col-md-3">
                <input class="form-control" name="item_name" placeholder="Name" required>
            </div>
            <div class="col-md-3">
                <input class="form-control" name="generic_name" placeholder="Generic Name">
            </div>
            <div class="col-md-2">
                <input class="form-control" name="unit" placeholder="Unit (tablet, ml...)">
            </div>
            <div class="col-md-2">
                <input class="form-control" name="quantity" type="number" placeholder="Quantity" required>
            </div>
            <div class="col-md-2">
                <input class="form-control" name="unit_price" type="number" step="0.01" placeholder="Unit Price">
            </div>
            <div class="col-md-3">
                <input class="form-control" name="expiration_date" type="date" placeholder="Expiry">
            </div>
            <div class="col-md-2">
                <input class="form-control" name="minimum_threshold" type="number" placeholder="Min Threshold">
            </div>
            <div class="col-md-2">
                <button class="btn btn-primary w-100" name="add">Add</button>
            </div>
        </form>
    </div>
</div>

<!-- TABLE -->
<div class="card">
    <div class="card-header"><strong>Stock</strong></div>
    <div class="table-responsive">
        <table class="table table-hover mb-0">
        <thead>
        <tr>
        <th>Name</th>
        <th>Generic</th>
        <th>Qty</th>
        <th>Price</th>
        <th>Expiry</th>
        <th>Actions</th>
        </tr>
        </thead>
        <tbody>

        <?php
        $stmt = $pdo->query("SELECT * FROM inventory ORDER BY inventory_id DESC");

        while ($row = $stmt->fetch()) {

            $status = "";
            if ($row['quantity'] <= $row['minimum_threshold']) {
                $status = "⚠️ LOW";
            }

            if (!empty($row['expiration_date']) && strtotime($row['expiration_date']) <= strtotime("+30 days")) {
                $status .= " ⏰ EXPIRING";
            }

            echo "<tr>
                <td>".e($row['item_name'])."</td>
                <td>".e($row['generic_name'])."</td>
                <td>{$row['quantity']} $status</td>
                <td>".formatCurrency($row['unit_price'])."</td>
                <td>".e($row['expiration_date'])."</td>
                <td>
                    <a class='btn btn-sm btn-outline-danger' href='inventory.php?delete={$row['inventory_id']}'
                       onclick='return confirm(\"Delete this item?\")'>Delete</a>
                </td>
            </tr>";
        }
        ?>

        </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>

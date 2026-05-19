<?php
require_once "../config/db.php";
require_once "../includes/functions.php";

/* ===============================
   ADD MEDICATION
=============================== */
if (isset($_POST['add'])) {

    $stmt = $pdo->prepare("
        INSERT INTO inventory
        (medication_name, generic_name, quantity, unit_price, expiry_date, minimum_threshold)
        VALUES (?, ?, ?, ?, ?, ?)
    ");

    $stmt->execute([
        trim($_POST['medication_name']),
        trim($_POST['generic_name']),
        (int)$_POST['quantity'],
        (float)$_POST['unit_price'],
        $_POST['expiry_date'],
        (int)$_POST['minimum_threshold']
    ]);

    header("Location: inventory.php");
    exit();
}

/* ===============================
   SAFE DELETE (NO FK CRASH)
   - checks if used in prescriptions/logs
=============================== */
if (isset($_GET['delete'])) {

    $id = (int)$_GET['delete'];

    // Check prescriptions
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM prescription_items WHERE medication_id = ?");
    $stmt->execute([$id]);
    $used1 = $stmt->fetchColumn();

    // Check logs
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM inventory_logs WHERE medication_id = ?");
    $stmt->execute([$id]);
    $used2 = $stmt->fetchColumn();

    if ($used1 > 0 || $used2 > 0) {
        echo "<script>
            alert('Cannot delete: medication is used in system history');
            window.location='inventory.php';
        </script>";
        exit();
    }

    // Safe delete
    $stmt = $pdo->prepare("DELETE FROM inventory WHERE id = ?");
    $stmt->execute([$id]);

    header("Location: inventory.php");
    exit();
}

/* ===============================
   FETCH INVENTORY
=============================== */
$items = $pdo->query("SELECT * FROM inventory ORDER BY id DESC")->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Inventory Management</title>

    <style>
        body { font-family: Arial; padding: 20px; }
        table { border-collapse: collapse; width: 100%; }
        th, td { padding: 10px; border: 1px solid #ccc; }
        th { background: #f4f4f4; }
        .low { color: red; font-weight: bold; }
        .exp { color: orange; font-weight: bold; }
    </style>
</head>

<body>

<h2>Inventory Management</h2>

<!-- ===============================
     ADD MEDICATION FORM
=============================== -->
<form method="POST">
    <input name="medication_name" placeholder="Medication Name" required>
    <input name="generic_name" placeholder="Generic Name">

    <input name="quantity" type="number" placeholder="Quantity" required>
    <input name="unit_price" type="number" step="0.01" placeholder="Unit Price" required>

    <input name="expiry_date" type="date" required>
    <input name="minimum_threshold" type="number" placeholder="Minimum Stock" required>

    <button name="add">Add Medication</button>
</form>

<hr>

<!-- ===============================
     STOCK RESULT
=============================== -->
<div id="stockResult" style="margin:10px 0;font-weight:bold;"></div>

<!-- ===============================
     TABLE
=============================== -->
<table>

<tr>
    <th>Name</th>
    <th>Qty</th>
    <th>Expiry</th>
    <th>Status</th>
    <th>Action</th>
</tr>

<?php foreach ($items as $i): ?>
<tr>

    <td><?= e($i['medication_name']) ?></td>

    <td>
        <?= $i['quantity'] ?>
        <?php if (isLowStock($i['quantity'], $i['minimum_threshold'])): ?>
            <span class="low">🔴 LOW</span>
        <?php endif; ?>
    </td>

    <td>
        <?= $i['expiry_date'] ?>
        <?php if (isNearExpiry($i['expiry_date'])): ?>
            <span class="exp">⚠ EXPIRING</span>
        <?php endif; ?>
    </td>

    <td>
        <?= ($i['quantity'] > 0) ? "✅ Available" : "❌ Out of Stock" ?>
    </td>

    <td>
        <button onclick="checkStock(<?= $i['id'] ?>)">Check Stock</button>

        <a href="?delete=<?= $i['id'] ?>"
           onclick="return confirm('Are you sure?')">
           Delete
        </a>
    </td>

</tr>
<?php endforeach; ?>

</table>

<!-- ===============================
     CHECK STOCK JS
=============================== -->
<script>
function checkStock(id) {

    fetch(`../actions/check-stock.php?id=${id}`)
        .then(res => res.json())
        .then(data => {

            if (data.status === "success") {

                document.getElementById("stockResult").innerText =
                    "✔ Medication Found\n" +
                    "Name: " + data.name + "\n" +
                    "Quantity: " + data.quantity + "\n" +
                    "Available: " + data.available + "\n" +
                    "Low Stock: " + data.low_stock;

            } else {

                document.getElementById("stockResult").innerText =
                    "❌ " + data.message;
            }
        })
        .catch(err => {
            console.log(err);
            document.getElementById("stockResult").innerText =
                "Error checking stock (server or path issue)";
        });
}
</script>

</body>
</html>
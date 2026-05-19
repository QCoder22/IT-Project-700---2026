<?php
require_once "../config/db.php";

$id = $_GET['id'];

$stmt = $pdo->prepare("SELECT * FROM prescriptions WHERE id=?");
$stmt->execute([$id]);
$rx = $stmt->fetch();
?>

<h2>Dispense Prescription #<?= $id ?></h2>

<form method="POST" action="../actions/dispense-action.php">
    <input type="hidden" name="prescription_id" value="<?= $id ?>">
    <button type="submit">CONFIRM DISPENSE</button>
</form>
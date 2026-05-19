<?php
require_once "../config/db.php";

$rows = $pdo->query("
    SELECT * FROM prescriptions 
    WHERE status='pending'
    ORDER BY created_at ASC
")->fetchAll();
?>

<h2>Prescription Queue</h2>

<?php foreach ($rows as $r): ?>
<div style="border:1px solid #ccc; padding:10px; margin:10px;">
    <b>Prescription #<?= $r['id'] ?></b><br>
    Patient: <?= $r['patient_id'] ?><br>
    Status: 🟡 Pending<br>

    <a href="dispense.php?id=<?= $r['id'] ?>">Dispense</a>
</div>
<?php endforeach; ?>
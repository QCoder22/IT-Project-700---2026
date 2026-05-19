<?php
require_once "../config/db.php";

session_start();

$stmt = $pdo->prepare("
    SELECT * FROM prescriptions 
    WHERE doctor_id = ?
    ORDER BY created_at DESC
");

$stmt->execute([$_SESSION['user_id'] ?? 1]);
$rows = $stmt->fetchAll();
?>

<h2>Prescription Status</h2>

<table border="1">
<tr><th>ID</th><th>Status</th><th>Date</th></tr>

<?php foreach($rows as $r): ?>
<tr>
<td><?= $r['id'] ?></td>
<td>
    <?php if ($r['status'] == 'pending') echo "🟡 Pending"; ?>
    <?php if ($r['status'] == 'dispensed') echo "🟢 Dispensed"; ?>
    <?php if ($r['status'] == 'cancelled') echo "🔴 Cancelled"; ?>
</td>
<td><?= $r['created_at'] ?></td>
</tr>
<?php endforeach; ?>
</table>
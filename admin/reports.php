<?php

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../includes/functions.php';

requireRole('admin');

$pageTitle = 'Reports';

require_once __DIR__ . '/../includes/header.php';

$revenue = $pdo->query("
SELECT
DATE_FORMAT(billing_date,'%Y-%m') month,

SUM(total_amount) revenue
FROM billing
GROUP BY month
ORDER BY month DESC
")->fetchAll();

$medications = $pdo->query("
SELECT
description,
SUM(quantity) qty
FROM billing_items
WHERE item_type='medication'
GROUP BY description
ORDER BY qty DESC
LIMIT 10
")->fetchAll();

$doctors = $pdo->query("
SELECT
u.first_name,

u.last_name,
COUNT(*) total
FROM appointments a
JOIN doctors d ON a.doctor_id=d.doctor_id
JOIN users u ON d.user_id=u.user_id
GROUP BY a.doctor_id
ORDER BY total DESC
")->fetchAll();

$missed = $pdo->query("
SELECT COUNT(*)
FROM appointments
WHERE status='cancelled'
")->fetchColumn();

$visits = $pdo->query("
SELECT
DATE_FORMAT(appointment_date,'%Y-%m') month,

COUNT(*) total
FROM appointments
GROUP BY month
ORDER BY month DESC
")->fetchAll();
?>

<h1>Reports</h1>

<div class="card mb-4">
<div class="card-header">
Revenue By Month
</div>

<table class="table">
<tr>
<th>Month</th>
<th>Revenue</th>
</tr>

<?php foreach($revenue as $r): ?>
<tr>
<td><?= e($r['month']) ?></td>
<td><?= formatCurrency($r['revenue']) ?></td>
</tr>
<?php endforeach; ?>
</table>
</div>

<div class="card mb-4">
<div class="card-header">
Top Medications
</div>

<table class="table">
<tr>
<th>Medication</th>
<th>Dispensed</th>
</tr>


<?php foreach($medications as $m): ?>
<tr>
<td><?= e($m['description']) ?></td>
<td><?= $m['qty'] ?></td>
</tr>
<?php endforeach; ?>
</table>
</div>

<div class="card mb-4">
<div class="card-header">
Busiest Doctors
</div>

<table class="table">
<tr>
<th>Doctor</th>
<th>Appointments</th>
</tr>


<?php foreach($doctors as $d): ?>
<tr>
<td><?= e($d['first_name'].' '.$d['last_name']) ?></td>
<td><?= $d['total'] ?></td>
</tr>
<?php endforeach; ?>
</table>
</div>

<div class="card mb-4">
<div class="card-body">
<h4>Missed Appointments</h4>
<p><?= $missed ?></p>
</div>
</div>

<div class="card mb-4">
<div class="card-header">

Patient Visits Over Time
</div>

<table class="table">
<tr>
<th>Month</th>
<th>Visits</th>
</tr>

<?php foreach($visits as $v): ?>
<tr>
<td><?= e($v['month']) ?></td>
<td><?= $v['total'] ?></td>
</tr>
<?php endforeach; ?>
</table>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
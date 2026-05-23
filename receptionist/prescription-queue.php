<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../includes/functions.php';

requireRole('receptionist');

$pageTitle = 'Prescription Queue';
require_once __DIR__ . '/../includes/header.php';
?>

<h1>Prescription Queue</h1>

<div class="card">
    <div class="card-header"><strong>Awaiting dispense</strong></div>
    <div class="table-responsive">
        <table class="table table-hover mb-0">
        <thead>
        <tr>
        <th>ID</th>
        <th>Patient</th>
        <th>Doctor</th>
        <th>Issued</th>
        <th>Status</th>
        <th>Action</th>
        </tr>
        </thead>
        <tbody>

        <?php
        $stmt = $pdo->query("
            SELECT pr.prescription_id, pr.status, pr.issued_at,
                   pu.first_name AS pf, pu.last_name AS pl,
                   du.first_name AS df, du.last_name AS dl
            FROM prescriptions pr
            JOIN patients p ON pr.patient_id = p.patient_id
            JOIN users pu ON p.user_id = pu.user_id
            JOIN doctors d ON pr.doctor_id = d.doctor_id
            JOIN users du ON d.user_id = du.user_id
            WHERE pr.status IN ('pending','approved')
            ORDER BY pr.issued_at ASC
        ");

        while($row = $stmt->fetch()){
            echo "<tr>
                <td>{$row['prescription_id']}</td>
                <td>".e($row['pf'].' '.$row['pl'])."</td>
                <td>".e('Dr. '.$row['dl'])."</td>
                <td>".formatDateTime($row['issued_at'])."</td>
                <td><span class='badge bg-warning text-dark'>".e($row['status'])."</span></td>
                <td><a class='btn btn-sm btn-primary' href='dispense.php?id={$row['prescription_id']}'>Dispense</a></td>
            </tr>";
        }
        ?>

        </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>

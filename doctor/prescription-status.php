<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../includes/functions.php';

requireRole('doctor');

$doc = $pdo->prepare("SELECT doctor_id FROM doctors WHERE user_id=?");
$doc->execute([$_SESSION['user_id']]);
$doctor_id = $doc->fetch()['doctor_id'] ?? null;

$pageTitle = 'Prescription Status';
require_once __DIR__ . '/../includes/header.php';
?>

<h1>Prescription Status</h1>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
        <thead>
        <tr>
        <th>ID</th>
        <th>Patient</th>
        <th>Issued</th>
        <th>Status</th>
        </tr>
        </thead>
        <tbody>

        <?php
        $stmt = $pdo->prepare("
            SELECT pr.prescription_id, pr.status, pr.issued_at, u.first_name, u.last_name
            FROM prescriptions pr
            JOIN patients p ON pr.patient_id = p.patient_id
            JOIN users u ON p.user_id = u.user_id
            WHERE pr.doctor_id = ?
            ORDER BY pr.issued_at DESC
        ");
        $stmt->execute([$doctor_id]);

        while($r = $stmt->fetch()){
            echo "<tr>
                <td>{$r['prescription_id']}</td>
                <td>".e($r['first_name'].' '.$r['last_name'])."</td>
                <td>".formatDateTime($r['issued_at'])."</td>
                <td><span class='badge bg-secondary'>".e($r['status'])."</span></td>
            </tr>";
        }
        ?>

        </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>

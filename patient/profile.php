<?php
require_once __DIR__ . "/../config/db.php";
require_once __DIR__ . "/../config/auth.php";
require_once __DIR__ . "/../includes/functions.php";

requireRole('patient');

// Fetch patient information (joined with user info)
$stmt = $pdo->prepare("
    SELECT
        p.patient_id, p.dob, p.gender, p.address, p.insurance_info, p.medical_history,
        u.first_name, u.last_name, u.email, u.phone
    FROM patients p
    JOIN users u ON p.user_id = u.user_id
    WHERE u.user_id = ?
");
$stmt->execute([$_SESSION['user_id']]);

$patient = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$patient) {
    die("Patient record not found.");
}

$pageTitle = 'My Profile';
require_once __DIR__ . "/../includes/header.php";
?>

<h1>My Profile</h1>

<div class="card">
    <div class="card-body">

        <p>
        <strong>First Name:</strong>
        <?= e($patient['first_name']) ?>
        </p>

        <p>
        <strong>Last Name:</strong>
        <?= e($patient['last_name']) ?>
        </p>

        <p>
        <strong>Email:</strong>
        <?= e($patient['email']) ?>
        </p>

        <p>
        <strong>Phone:</strong>
        <?= e($patient['phone']) ?>
        </p>

        <p>
        <strong>Date of Birth:</strong>
        <?= e(formatDate($patient['dob'])) ?>
        </p>

        <p>
        <strong>Gender:</strong>
        <?= e($patient['gender']) ?>
        </p>

        <p>
        <strong>Address:</strong>
        <?= e($patient['address']) ?>
        </p>

        <p>
        <strong>Insurance:</strong>
        <?= e($patient['insurance_info']) ?>
        </p>

        <br>

        <a href="edit-profile.php" class="btn btn-primary">
            Edit Profile
        </a>

    </div>
</div>

<?php require_once __DIR__ . "/../includes/footer.php"; ?>

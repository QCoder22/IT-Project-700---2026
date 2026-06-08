<?php
require_once __DIR__ . "/../config/db.php";
require_once __DIR__ . "/../config/auth.php";
require_once __DIR__ . "/../includes/functions.php";

requireRole('patient');

// Fetch patient information (joined with user info)
$stmt = $pdo->prepare("
    SELECT
        p.patient_id, p.dob, p.gender, p.address, p.insurance_info,
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

$pageTitle = 'Edit Profile';
require_once __DIR__ . "/../includes/header.php";
?>

<h1>Edit Profile</h1>

<form action="<?= BASE_URL ?>/actions/update-profile.php" method="POST" class="card">
    <div class="card-body">

        <div class="row g-3">

            <div class="col-md-6">
                <label class="form-label">First Name</label>
                <input class="form-control" type="text" name="first_name"
                       value="<?= e($patient['first_name']) ?>" required>
            </div>

            <div class="col-md-6">
                <label class="form-label">Last Name</label>
                <input class="form-control" type="text" name="last_name"
                       value="<?= e($patient['last_name']) ?>" required>
            </div>

            <div class="col-md-6">
                <label class="form-label">Email</label>
                <input class="form-control" type="email" name="email"
                       value="<?= e($patient['email']) ?>" required>
            </div>

            <div class="col-md-6">
                <label class="form-label">Phone</label>
                <input class="form-control" type="text" name="phone"
                       value="<?= e($patient['phone']) ?>" required>
            </div>

            <div class="col-md-6">
                <label class="form-label">Date of Birth</label>
                <input class="form-control" type="date" name="dob"
                       value="<?= e($patient['dob']) ?>" required>
            </div>

            <div class="col-md-6">
                <label class="form-label">Gender</label>
                <select class="form-select" name="gender" required>
                    <?php foreach (['Male', 'Female', 'Other'] as $g): ?>
                        <option value="<?= $g ?>" <?= $patient['gender'] === $g ? 'selected' : '' ?>><?= $g ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-md-12">
                <label class="form-label">Address</label>
                <textarea class="form-control" name="address" rows="3"><?= e($patient['address']) ?></textarea>
            </div>

            <div class="col-md-12">
                <label class="form-label">Insurance Info</label>
                <input class="form-control" type="text" name="insurance_info"
                       value="<?= e($patient['insurance_info']) ?>">
            </div>

        </div>

        <br>

        <button type="submit" class="btn btn-primary">Update Profile</button>
        <a href="profile.php" class="btn btn-outline-secondary">Cancel</a>

    </div>
</form>

<?php require_once __DIR__ . "/../includes/footer.php"; ?>

<?php
require_once __DIR__ . "/config/auth.php";
require_once __DIR__ . "/includes/functions.php";

if (isLoggedIn()) {
    header("Location: " . dashboardUrl(currentRole()));
    exit();
}

$old = $_SESSION['old'] ?? [];
unset($_SESSION['old']);

$pageTitle = 'Register New Patient';
require_once __DIR__ . "/includes/header.php";
?>

<div class="medicom-auth-card" style="max-width: 560px;">

    <?= showMsg() ?>
    
    <div class="text-center mb-4">
        <i class="bi bi-person-plus-fill" style="font-size: 3rem; color: var(--medicom-primary);"></i>
    </div>

    <h1 class="text-center">Register New Patient</h1>
    <p class="text-center subtitle">Create a new patient account</p>

    <form action="<?= BASE_URL ?>/actions/register-patient-action.php" method="POST">

        <p class="mb-1">First Name</p>
        <input class="form-control mb-3" type="text" name="first_name"
               value="<?= e($old['first_name'] ?? '') ?>" required>

        <p class="mb-1">Last Name</p>
        <input class="form-control mb-3" type="text" name="last_name"
               value="<?= e($old['last_name'] ?? '') ?>" required>

        <p class="mb-1">Date of Birth</p>
        <input class="form-control mb-3" type="date" name="dob"
               max="<?= date('Y-m-d') ?>"
               value="<?= e($old['dob'] ?? '') ?>" required>

        <p class="mb-1">Gender</p>
        <select class="form-select mb-3" name="gender" required>
            <option value="">Select Gender</option>
            <?php foreach (['Male', 'Female', 'Other'] as $g): ?>
                <option value="<?= $g ?>" <?= ($old['gender'] ?? '') === $g ? 'selected' : '' ?>><?= $g ?></option>
            <?php endforeach; ?>
        </select>

        <p class="mb-1">Phone Number</p>
        <input class="form-control mb-3" type="text" name="phone"
               placeholder="e.g. 0712345678"
               value="<?= e($old['phone'] ?? '') ?>" required>

        <p class="mb-1">Email Address</p>
        <input class="form-control mb-3" type="email" name="email"
               value="<?= e($old['email'] ?? '') ?>" required>

        <p class="mb-1">Password</p>
        <input class="form-control mb-3" type="password" name="password" required minlength="8">

        <p class="mb-1">Address</p>
        <textarea class="form-control mb-3" name="address" rows="2"><?= e($old['address'] ?? '') ?></textarea>

        <p class="mb-1">Medical Aid / Insurance</p>
        <input class="form-control mb-3" type="text" name="insurance_info"
               value="<?= e($old['insurance_info'] ?? '') ?>">

        <button type="submit" class="btn btn-primary w-100">
            <i class="bi bi-check-circle"></i> Register Patient
        </button>

    </form>

    <hr class="my-4">

    <p class="text-center mb-0">
        Already have an account? <a href="<?= BASE_URL ?>/login.php" class="fw-bold">Log in</a>
    </p>

</div>

<?php require_once __DIR__ . "/includes/footer.php"; ?>

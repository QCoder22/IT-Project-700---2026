<?php
require_once __DIR__ . '/config/auth.php';
require_once __DIR__ . '/includes/functions.php';

if (isLoggedIn()) {
    header('Location: ' . dashboardUrl(currentRole()));
    exit;
}

$old = $_SESSION['old'] ?? [];
unset($_SESSION['old']);

$pageTitle = 'Create Account';
require_once __DIR__ . '/includes/header.php';
?>

<div class="medicom-auth-card" style="max-width: 560px;">
    <div class="text-center mb-4">
        <i class="bi bi-person-plus-fill" style="font-size: 3rem; color: var(--medicom-primary);"></i>
    </div>
    <h1 class="text-center">Create Account</h1>
    <p class="text-center subtitle">Register as a new patient</p>

    <form action="<?= BASE_URL ?>/actions/register-patient-action.php" method="POST">
        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="first_name" class="form-label">First Name</label>
                <input type="text" class="form-control" id="first_name" name="first_name" required
                       value="<?= e($old['first_name'] ?? '') ?>">
            </div>
            <div class="col-md-6 mb-3">
                <label for="last_name" class="form-label">Last Name</label>
                <input type="text" class="form-control" id="last_name" name="last_name" required
                       value="<?= e($old['last_name'] ?? '') ?>">
            </div>
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">Email Address</label>
            <input type="email" class="form-control" id="email" name="email" required
                   value="<?= e($old['email'] ?? '') ?>">
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="phone" class="form-label">Mobile Number</label>
                <input type="tel" class="form-control" id="phone" name="phone" required
                       placeholder="e.g. 0712345678"
                       value="<?= e($old['phone'] ?? '') ?>">
            </div>
            <div class="col-md-6 mb-3">
                <label for="dob" class="form-label">Date of Birth</label>
                <input type="date" class="form-control" id="dob" name="dob" required
                       max="<?= date('Y-m-d') ?>"
                       value="<?= e($old['dob'] ?? '') ?>">
            </div>
        </div>

        <div class="mb-3">
            <label for="gender" class="form-label">Gender</label>
            <select class="form-select" id="gender" name="gender" required>
                <option value="">-- Select --</option>
                <?php foreach (['Male', 'Female', 'Other'] as $g): ?>
                    <option value="<?= $g ?>" <?= ($old['gender'] ?? '') === $g ? 'selected' : '' ?>><?= $g ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input type="password" class="form-control" id="password" name="password" required minlength="8">
            <small class="text-muted">At least 8 characters with letters and numbers.</small>
        </div>

        <div class="mb-3">
            <label for="password_confirm" class="form-label">Confirm Password</label>
            <input type="password" class="form-control" id="password_confirm" name="password_confirm" required minlength="8">
        </div>

        <button type="submit" class="btn btn-primary w-100">
            <i class="bi bi-check-circle"></i> Create Account
        </button>
    </form>

    <hr class="my-4">

    <p class="text-center mb-0">
        Already have an account? <a href="<?= BASE_URL ?>/login.php" class="fw-bold">Log in</a>
    </p>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

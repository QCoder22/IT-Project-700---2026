<?php
require_once __DIR__ . '/config/auth.php';
require_once __DIR__ . '/includes/functions.php';

if (isLoggedIn()) {
    header('Location: ' . dashboardUrl(currentRole()));
    exit;
}

$pageTitle = 'Forgot Password';
require_once __DIR__ . '/includes/header.php';
?>

<div class="medicom-auth-card">

    <?= showMsg() ?>

    <div class="text-center mb-4">
        <img src="<?= BASE_URL ?>/assets/images/medicom-logo-lg.png" alt="MedicOM Clinic" height="80">
    </div>
    <h1 class="text-center">Forgot Password</h1>
    <p class="text-center subtitle">
        Enter your email address. An administrator will provide a temporary password.
    </p>

    <form action="<?= BASE_URL ?>/actions/forgot-password-action.php" method="POST">
        <div class="mb-3">
            <label for="email" class="form-label">Email Address</label>
            <input type="email" class="form-control" id="email" name="email" required autofocus>
        </div>

        <button type="submit" class="btn btn-primary w-100">
            <i class="bi bi-send"></i> Submit Reset Request
        </button>
    </form>

    <hr class="my-4">

    <p class="text-center mb-0">
        <a href="<?= BASE_URL ?>/login.php" class="fw-bold">
            <i class="bi bi-arrow-left"></i> Back to Login
        </a>
    </p>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

<?php
require_once __DIR__ . '/config/auth.php';
require_once __DIR__ . '/includes/functions.php';

if (isLoggedIn()) {
    header('Location: ' . dashboardUrl(currentRole()));
    exit;
}

if (isset($_GET['timeout'])) {
    setMsg('warning', 'Your session expired. Please log in again.');
}

$pageTitle = 'Login';
require_once __DIR__ . '/includes/header.php';
?>

<div class="medicom-auth-card">
    <div class="text-center mb-4">
        <i class="bi bi-heart-pulse-fill" style="font-size: 3rem; color: var(--medicom-primary);"></i>
    </div>
    <h1 class="text-center">Welcome Back</h1>
    <p class="text-center subtitle">Sign in to your MedicOM account</p>

    <form action="<?= BASE_URL ?>/actions/login-action.php" method="POST">
        <div class="mb-3">
            <label for="email" class="form-label">Email Address</label>
            <input type="email" class="form-control" id="email" name="email" required autofocus
                   value="<?= e($_GET['email'] ?? '') ?>">
        </div>

        <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input type="password" class="form-control" id="password" name="password" required>
        </div>

        <div class="d-flex justify-content-end mb-3">
            <a href="<?= BASE_URL ?>/forgot-password.php" class="small">Forgot password?</a>
        </div>

        <button type="submit" class="btn btn-primary w-100">
            <i class="bi bi-box-arrow-in-right"></i> Log In
        </button>
    </form>

    <hr class="my-4">

    <p class="text-center mb-0">
        New patient? <a href="<?= BASE_URL ?>/register.php" class="fw-bold">Create an account</a>
    </p>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

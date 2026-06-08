<?php
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/config/auth.php';
require_once __DIR__ . '/includes/functions.php';

requireLogin();

$u = currentUser();

$ps = $pdo->prepare("SELECT must_change_password FROM users WHERE user_id = ?");
$ps->execute([$u['id']]);
$row = $ps->fetch();

if (!$row || (int)$row['must_change_password'] !== 1) {
    header('Location: ' . dashboardUrl(currentRole()));
    exit;
}

$pageTitle = 'Change Password';
require_once __DIR__ . '/includes/header.php';
?>

<div class="medicom-auth-card">
    <div class="text-center mb-4">
        <i class="bi bi-shield-lock-fill" style="font-size: 3rem; color: var(--medicom-primary);"></i>
    </div>
    <h1 class="text-center">Set a New Password</h1>
    <p class="text-center subtitle">
        Your password was reset by an administrator. Choose a new password to continue.
    </p>

    <form action="<?= BASE_URL ?>/actions/change-password-action.php" method="POST">
        <div class="mb-3">
            <label for="current_password" class="form-label">Temporary Password</label>
            <input type="password" class="form-control" id="current_password"
                   name="current_password" required autofocus>
        </div>

        <div class="mb-3">
            <label for="new_password" class="form-label">New Password</label>
            <input type="password" class="form-control" id="new_password"
                   name="new_password" required minlength="8">
            <small class="text-muted">At least 8 characters with letters and numbers.</small>
        </div>

        <div class="mb-3">
            <label for="new_password_confirm" class="form-label">Confirm New Password</label>
            <input type="password" class="form-control" id="new_password_confirm"
                   name="new_password_confirm" required minlength="8">
        </div>

        <button type="submit" class="btn btn-primary w-100">
            <i class="bi bi-check-circle"></i> Set New Password
        </button>
    </form>

    <hr class="my-4">

    <p class="text-center mb-0">
        <a href="<?= BASE_URL ?>/logout.php" class="text-muted small">
            <i class="bi bi-box-arrow-right"></i> Log out instead
        </a>
    </p>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

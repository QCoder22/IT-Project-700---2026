<?php
if (!function_exists('isLoggedIn')) {
    require_once __DIR__ . '/../config/auth.php';
}
if (!function_exists('e')) {
    require_once __DIR__ . '/functions.php';
}

$user = currentUser();
$role = currentRole();
$pageTitle = $pageTitle ?? 'MedicOM';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle) ?> - MedicOM</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css">
</head>
<body>

<?php if (isLoggedIn()): ?>
<nav class="navbar navbar-expand-lg navbar-dark medicom-navbar">
    <div class="container-fluid">
        <a class="navbar-brand fw-bold" href="<?= dashboardUrl($role) ?>">
            <i class="bi bi-heart-pulse-fill"></i> MedicOM
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="mainNav">
            <ul class="navbar-nav ms-auto align-items-lg-center">
                <li class="nav-item">
                    <span class="nav-link">
                        <i class="bi bi-person-circle"></i>
                        <?= e($user['first_name'] . ' ' . $user['last_name']) ?>
                        <span class="badge bg-light text-dark ms-1"><?= e(ucfirst($role)) ?></span>
                    </span>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= BASE_URL ?>/logout.php">
                        <i class="bi bi-box-arrow-right"></i> Logout
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="d-flex">
    <?php require_once __DIR__ . '/sidebar.php'; ?>
    <main class="medicom-main flex-grow-1">
        <div class="container-fluid py-4">
            <?= showMsg() ?>
<?php else: ?>
<main class="medicom-auth-main">
    <?= showMsg() ?>
<?php endif; ?>

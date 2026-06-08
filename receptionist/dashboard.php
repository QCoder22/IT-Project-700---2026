<?php
require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../includes/functions.php';

requireRole('receptionist');

$u = currentUser();

$pageTitle = 'Receptionist Dashboard';
require_once __DIR__ . '/../includes/header.php';
?>

<h1 class="mb-4">Receptionist Dashboard</h1>

<div class="card">
    <div class="card-body">
        <h5 class="card-title">Welcome, <?= e($u['first_name']) ?>!</h5>
        <p class="card-text text-muted">
            This is a placeholder dashboard. The receptionist module will be built in Week 2.
        </p>
        <hr>
        <p class="mb-1"><strong>Logged in as:</strong> <?= e($u['email']) ?></p>
        <p class="mb-0"><strong>Role:</strong> <?= e($u['role']) ?></p>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>

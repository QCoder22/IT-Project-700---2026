<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('/forgot-password.php');
}

$email = sanitize($_POST['email'] ?? '');

if (!isValidEmail($email)) {
    setMsg('error', 'Please enter a valid email address.');
    redirect('/forgot-password.php');
}

$ps = $pdo->prepare("SELECT user_id, is_active FROM users WHERE email = ? LIMIT 1");
$ps->execute([$email]);
$u = $ps->fetch();

if ($u && (int)$u['is_active'] === 1) {
    $check = $pdo->prepare("SELECT request_id FROM password_reset_requests
                            WHERE user_id = ? AND status = 'pending' LIMIT 1");
    $check->execute([$u['user_id']]);

    if (!$check->fetch()) {
        $insert = $pdo->prepare("INSERT INTO password_reset_requests (user_id, email_submitted, status, requested_at)
                                 VALUES (?, ?, 'pending', NOW())");
        $insert->execute([$u['user_id'], $email]);
    }
}

setMsg('success', 'If an account exists for that email, a reset request has been submitted. An administrator will contact you with a temporary password.');
redirect('/login.php');

<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../includes/functions.php';

requireLogin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('/change-password.php');
}

$current = $_POST['current_password']     ?? '';
$new     = $_POST['new_password']         ?? '';
$confirm = $_POST['new_password_confirm'] ?? '';

if (empty($current) || empty($new) || empty($confirm)) {
    setMsg('error', 'All fields are required.');
    redirect('/change-password.php');
}

if ($new !== $confirm) {
    setMsg('error', 'New passwords do not match.');
    redirect('/change-password.php');
}

if (!isStrongPassword($new)) {
    setMsg('error', 'New password must be at least 8 characters with letters and numbers.');
    redirect('/change-password.php');
}

if ($new === $current) {
    setMsg('error', 'New password must be different from the temporary password.');
    redirect('/change-password.php');
}

$userId = $_SESSION['user_id'];

$ps = $pdo->prepare("SELECT password_hash FROM users WHERE user_id = ? LIMIT 1");
$ps->execute([$userId]);
$u = $ps->fetch();

if (!$u || !password_verify($current, $u['password_hash'])) {
    setMsg('error', 'The temporary password is incorrect.');
    redirect('/change-password.php');
}

$hash = password_hash($new, PASSWORD_DEFAULT);
$upd = $pdo->prepare("UPDATE users
                      SET password_hash = ?, must_change_password = 0, updated_at = NOW()
                      WHERE user_id = ?");
$upd->execute([$hash, $userId]);

setMsg('success', 'Password changed successfully. Welcome back!');
header('Location: ' . dashboardUrl(currentRole()));
exit;

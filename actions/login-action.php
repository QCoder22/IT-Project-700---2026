<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('/login.php');
}

$email = sanitize($_POST['email'] ?? '');
$pw    = $_POST['password'] ?? '';

if (empty($email) || empty($pw)) {
    setMsg('error', 'Email and password are required.');
    redirect('/login.php');
}

if (!isValidEmail($email)) {
    setMsg('error', 'Please enter a valid email address.');
    redirect('/login.php');
}

$ps = $pdo->prepare("SELECT user_id, first_name, last_name, email, password_hash, role, is_active, must_change_password
                     FROM users WHERE email = ? LIMIT 1");
$ps->execute([$email]);
$u = $ps->fetch();

if (!$u || !password_verify($pw, $u['password_hash'])) {
    setMsg('error', 'Invalid email or password.');
    redirect('/login.php?email=' . urlencode($email));
}

if ((int)$u['is_active'] !== 1) {
    setMsg('error', 'Your account has been deactivated.');
    redirect('/login.php');
}

loginUser($u);

if ((int)$u['must_change_password'] === 1) {
    setMsg('info', 'Your password was reset. Please set a new one.');
    header('Location: ' . BASE_URL . '/change-password.php');
    exit;
}

setMsg('success', 'Welcome back, ' . e($u['first_name']) . '!');
header('Location: ' . dashboardUrl($u['role']));
exit;

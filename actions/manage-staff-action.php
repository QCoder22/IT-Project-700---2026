<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../includes/functions.php';

requireRole('admin');

// --- DEACTIVATE / ACTIVATE ---
if (isset($_GET['deactivate']) || isset($_GET['activate'])) {

    $activate = isset($_GET['activate']);
    $userId = (int)($_GET['activate'] ?? $_GET['deactivate']);

    $stmt = $pdo->prepare("UPDATE users SET is_active = ? WHERE user_id = ? AND role = 'receptionist'");
    $stmt->execute([$activate ? 1 : 0, $userId]);

    setMsg('success', $activate ? 'Receptionist reactivated.' : 'Receptionist deactivated.');
    redirect('/admin/manage-staff.php');
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('/admin/manage-staff.php');
}

$first_name = trim($_POST['first_name'] ?? '');
$last_name  = trim($_POST['last_name'] ?? '');
$email      = trim($_POST['email'] ?? '');
$phone      = trim($_POST['phone'] ?? '');
$password   = $_POST['password'] ?? '';

$_SESSION['old'] = compact('first_name', 'last_name', 'email', 'phone');

if (!isValidEmail($email)) {
    setMsg('error', 'Invalid email format.');
    redirect('/admin/manage-staff.php');
}

$userId = $_POST['user_id'] ?? null;

// --- EDIT EXISTING ---
if ($userId) {

    try {
        $chk = $pdo->prepare("SELECT user_id FROM users WHERE email = ? AND user_id != ?");
        $chk->execute([$email, $userId]);
        if ($chk->fetchColumn() !== false) {
            setMsg('error', 'That email is already used by another account.');
            redirect('/admin/manage-staff.php?edit=' . $userId);
        }

        $u = $pdo->prepare("UPDATE users SET first_name = ?, last_name = ?, email = ?, phone = ? WHERE user_id = ? AND role = 'receptionist'");
        $u->execute([$first_name, $last_name, $email, $phone, $userId]);

        unset($_SESSION['old']);
        setMsg('success', 'Receptionist updated.');
        redirect('/admin/manage-staff.php');

    } catch (PDOException $e) {
        setMsg('error', 'Could not update receptionist. Please try again.');
        redirect('/admin/manage-staff.php?edit=' . $userId);
    }
}

// --- ADD NEW ---
try {
    $chk = $pdo->prepare("SELECT user_id FROM users WHERE email = ?");
    $chk->execute([$email]);
    if ($chk->fetchColumn() !== false) {
        setMsg('error', 'An account with that email already exists.');
        redirect('/admin/manage-staff.php');
    }

    if (strlen($password) < 8) {
        setMsg('error', 'Initial password must be at least 8 characters.');
        redirect('/admin/manage-staff.php');
    }

    $hash = password_hash($password, PASSWORD_DEFAULT);

    $u = $pdo->prepare("
        INSERT INTO users (first_name, last_name, email, phone, password_hash, role, is_active, must_change_password)
        VALUES (?, ?, ?, ?, ?, 'receptionist', 1, 1)
    ");
    $u->execute([$first_name, $last_name, $email, $phone, $hash]);

    unset($_SESSION['old']);
    setMsg('success', 'Receptionist added. Share the initial password with them — they must change it on first login.');
    redirect('/admin/manage-staff.php');

} catch (PDOException $e) {
    setMsg('error', 'Could not add receptionist. Please try again.');
    redirect('/admin/manage-staff.php');
}
?>

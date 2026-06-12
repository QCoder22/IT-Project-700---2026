<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../includes/functions.php';

requireRole('admin');

// --- DEACTIVATE / ACTIVATE (GET links) ---
if (isset($_GET['deactivate']) || isset($_GET['activate'])) {

    $activate = isset($_GET['activate']);
    $doctorId = (int)($_GET['activate'] ?? $_GET['deactivate']);

    $stmt = $pdo->prepare("
        UPDATE users u
        JOIN doctors d ON u.user_id = d.user_id
        SET u.is_active = ?
        WHERE d.doctor_id = ?
    ");
    $stmt->execute([$activate ? 1 : 0, $doctorId]);

    setMsg('success', $activate ? 'Doctor reactivated.' : 'Doctor deactivated.');
    redirect('/admin/manage-doctors.php');
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('/admin/manage-doctors.php');
}

// --- SHARED INPUT ---
$first_name      = trim($_POST['first_name'] ?? '');
$last_name       = trim($_POST['last_name'] ?? '');
$email           = trim($_POST['email'] ?? '');
$phone           = trim($_POST['phone'] ?? '');
$specialization  = trim($_POST['specialization'] ?? '');
$license_number  = trim($_POST['license_number'] ?? '');
$consultation_fee = $_POST['consultation_fee'] ?? '0';
$password         = $_POST['password'] ?? '';

$_SESSION['old'] = compact('first_name', 'last_name', 'email', 'phone', 'specialization', 'license_number', 'consultation_fee');

if (!isValidEmail($email)) {
    setMsg('error', 'Invalid email format.');
    redirect('/admin/manage-doctors.php');
}

$doctorId = $_POST['doctor_id'] ?? null;
$userId   = $_POST['user_id'] ?? null;

// --- EDIT EXISTING DOCTOR ---
if ($doctorId && $userId) {

    try {
        $pdo->beginTransaction();

        // email must not collide with another user
        $chk = $pdo->prepare("SELECT user_id FROM users WHERE email = ? AND user_id != ?");
        $chk->execute([$email, $userId]);
        if ($chk->fetchColumn() !== false) {
            $pdo->rollBack();
            setMsg('error', 'That email is already used by another account.');
            redirect('/admin/manage-doctors.php?edit=' . $doctorId);
        }

        $u = $pdo->prepare("UPDATE users SET first_name = ?, last_name = ?, email = ?, phone = ? WHERE user_id = ?");
        $u->execute([$first_name, $last_name, $email, $phone, $userId]);

        $d = $pdo->prepare("UPDATE doctors SET specialization = ?, license_number = ?, consultation_fee = ? WHERE doctor_id = ?");
        $d->execute([$specialization, $license_number, $consultation_fee, $doctorId]);

        $pdo->commit();
        unset($_SESSION['old']);
        setMsg('success', 'Doctor updated.');
        redirect('/admin/manage-doctors.php');

    } catch (PDOException $e) {
        if ($pdo->inTransaction()) $pdo->rollBack();
        setMsg('error', 'Could not update doctor. Please try again.');
        redirect('/admin/manage-doctors.php?edit=' . $doctorId);
    }
}

// --- ADD NEW DOCTOR (creates the user account + doctor profile) ---
try {
    $pdo->beginTransaction();

    $chk = $pdo->prepare("SELECT user_id FROM users WHERE email = ?");
    $chk->execute([$email]);
    if ($chk->fetchColumn() !== false) {
        $pdo->rollBack();
        setMsg('error', 'An account with that email already exists.');
        redirect('/admin/manage-doctors.php');
    }

    // admin sets the initial password; doctor must change it on first login
    if (strlen($password) < 8) {
        $pdo->rollBack();
        setMsg('error', 'Initial password must be at least 8 characters.');
        redirect('/admin/manage-doctors.php');
    }

    $hash = password_hash($password, PASSWORD_DEFAULT);

    $u = $pdo->prepare("
        INSERT INTO users (first_name, last_name, email, phone, password_hash, role, is_active, must_change_password)
        VALUES (?, ?, ?, ?, ?, 'doctor', 1, 1)
    ");
    $u->execute([$first_name, $last_name, $email, $phone, $hash]);

    $newUserId = (int)$pdo->lastInsertId();

    $d = $pdo->prepare("
        INSERT INTO doctors (user_id, specialization, license_number, consultation_fee)
        VALUES (?, ?, ?, ?)
    ");
    $d->execute([$newUserId, $specialization, $license_number, $consultation_fee]);

    $pdo->commit();
    unset($_SESSION['old']);

    setMsg('success', 'Doctor added. Share the initial password with them — they must change it on first login.');
    redirect('/admin/manage-doctors.php');

} catch (PDOException $e) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    setMsg('error', 'Could not add doctor. Please try again.');
    redirect('/admin/manage-doctors.php');
}
?>

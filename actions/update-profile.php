<?php
require_once __DIR__ . "/../config/db.php";
require_once __DIR__ . "/../config/auth.php";
require_once __DIR__ . "/../includes/functions.php";

requireRole('patient');

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    redirect('/patient/profile.php');
}

// GET DATA
$first_name     = trim($_POST['first_name'] ?? '');
$last_name      = trim($_POST['last_name'] ?? '');
$email          = trim($_POST['email'] ?? '');
$phone          = trim($_POST['phone'] ?? '');
$dob            = trim($_POST['dob'] ?? '');
$gender         = trim($_POST['gender'] ?? '');
$address        = trim($_POST['address'] ?? '');
$insurance_info = trim($_POST['insurance_info'] ?? '');

$userId = $_SESSION['user_id'];

// VALIDATE EMAIL
if (!isValidEmail($email)) {
    setMsg('error', 'Invalid email format.');
    redirect('/patient/edit-profile.php');
}

// VALIDATE GENDER
if (!in_array($gender, ['Male', 'Female', 'Other'])) {
    setMsg('error', 'Invalid gender.');
    redirect('/patient/edit-profile.php');
}

try {

    $pdo->beginTransaction();

    // CHECK EMAIL NOT TAKEN BY ANOTHER USER
    $stmt = $pdo->prepare("
        SELECT user_id
        FROM users
        WHERE email = ? AND user_id != ?
    ");
    $stmt->execute([$email, $userId]);

    if ($stmt->rowCount() > 0) {
        $pdo->rollBack();
        setMsg('error', 'Email is already used by another account.');
        redirect('/patient/edit-profile.php');
    }

    // UPDATE USER
    $stmt = $pdo->prepare("
        UPDATE users
        SET
            first_name = ?,
            last_name = ?,
            email = ?,
            phone = ?
        WHERE user_id = ?
    ");
    $stmt->execute([$first_name, $last_name, $email, $phone, $userId]);

    // UPDATE PATIENT
    $stmt = $pdo->prepare("
        UPDATE patients
        SET
            dob = ?,
            gender = ?,
            address = ?,
            insurance_info = ?
        WHERE user_id = ?
    ");
    $stmt->execute([$dob, $gender, $address, $insurance_info, $userId]);

    $pdo->commit();

    setMsg('success', 'Profile updated successfully.');
    redirect('/patient/profile.php');

} catch (PDOException $e) {
    $pdo->rollBack();
    setMsg('error', 'Database error: ' . $e->getMessage());
    redirect('/patient/edit-profile.php');
}
?>

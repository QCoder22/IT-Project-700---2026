<?php
require_once __DIR__ . "/../config/db.php";
require_once __DIR__ . "/../config/auth.php";
require_once __DIR__ . "/../includes/functions.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    redirect('/register.php');
}

// Determine where to redirect on success/failure based on who's submitting
$source = isLoggedIn() && currentRole() === 'receptionist'
    ? '/receptionist/register-patient.php'
    : '/register.php';

// GET DATA
$first_name     = trim($_POST['first_name'] ?? '');
$last_name      = trim($_POST['last_name'] ?? '');
$email          = trim($_POST['email'] ?? '');
$phone          = trim($_POST['phone'] ?? '');
$dob            = trim($_POST['dob'] ?? '');
$gender         = trim($_POST['gender'] ?? '');
$address        = trim($_POST['address'] ?? '');
$insurance_info = trim($_POST['insurance_info'] ?? '');
$password       = $_POST['password'] ?? '';

// Save form values so we can repopulate on validation failure
$_SESSION['old'] = compact('first_name', 'last_name', 'email', 'phone', 'dob', 'gender', 'address', 'insurance_info');

// VALIDATE EMAIL
if (!isValidEmail($email)) {
    setMsg('error', 'Invalid email format.');
    redirect($source);
}

// VALIDATE GENDER
if (!in_array($gender, ['Male', 'Female', 'Other'])) {
    setMsg('error', 'Please select a valid gender.');
    redirect($source);
}

// VALIDATE PASSWORD
if (strlen($password) < 8) {
    setMsg('error', 'Password must be at least 8 characters.');
    redirect($source);
}

try {

    $pdo->beginTransaction();

    // CHECK DUPLICATE EMAIL
    $stmt = $pdo->prepare("SELECT user_id FROM users WHERE email = ?");
    $stmt->execute([$email]);

    if ($stmt->fetchColumn() !== false) {
        $pdo->rollBack();
        setMsg('error', 'An account with that email already exists.');
        redirect($source);
    }

    // INSERT USER
    $hash = password_hash($password, PASSWORD_DEFAULT);

    // If a receptionist is creating the account, force the patient to
    // change the password on first login (they didn't pick it themselves)
    $forceChange = (isLoggedIn() && currentRole() === 'receptionist') ? 1 : 0;

    $stmt = $pdo->prepare("
        INSERT INTO users (first_name, last_name, email, phone, password_hash, role, is_active, must_change_password)
        VALUES (?, ?, ?, ?, ?, 'patient', 1, ?)
    ");
    $stmt->execute([$first_name, $last_name, $email, $phone, $hash, $forceChange]);

    $userId = (int)$pdo->lastInsertId();

    // INSERT PATIENT
    $stmt = $pdo->prepare("
        INSERT INTO patients (user_id, dob, gender, address, insurance_info)
        VALUES (?, ?, ?, ?, ?)
    ");
    $stmt->execute([$userId, $dob, $gender, $address, $insurance_info]);

    $pdo->commit();

    unset($_SESSION['old']);

    // If receptionist registered them, stay on registration page; otherwise log them in
    if (isLoggedIn() && currentRole() === 'receptionist') {
        setMsg('success', 'Patient registered successfully.');
        redirect('/receptionist/register-patient.php');
    }

    loginUser([
        'user_id'    => $userId,
        'first_name' => $first_name,
        'last_name'  => $last_name,
        'email'      => $email,
        'role'       => 'patient',
    ]);

    setMsg('success', 'Welcome to MedicOM, ' . e($first_name) . '!');
    redirect('/patient/dashboard.php');

} catch (PDOException $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    setMsg('error', 'Registration failed. Please try again.');
    redirect($source);
}
?>

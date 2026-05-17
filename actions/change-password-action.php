<?php

 //Validates the temporary password, sets the new one, and clears the
 //must_change_password flag. Used by users who were issued a temp password
 //by an admin.

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../includes/functions.php';

requireLogin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  redirect('/change-password.php');
}

verifyCsrf();

$current = $_POST['current_password'] ?? '';
$new = $_POST['new_password'] ?? '';
$confirm = $_POST['new_password_confirm'] ?? '';

if (empty($current) || empty($new) || empty($confirm)) {
  setFlash('error', 'All fields are required.');
  redirect('/change-password.php');
}

if ($new !== $confirm) {
  setFlash('error', 'New passwords do not match.');
  redirect('/change-password.php');
}

if (!isStrongPassword($new)) {
  setFlash('error', 'New password must be at least 8 characters and contain both letters and numbers.');
  redirect('/change-password.php');
}

if ($new === $current) {
  setFlash('error', 'New password must be different from the temporary password.');
  redirect('/change-password.php');
}

$userId = currentUserId();

try {
  // Fetch current hash to verify the temporary password
  $stmt = $pdo->prepare("SELECT password_hash FROM users WHERE user_id = :uid LIMIT 1");
  $stmt->execute([':uid' => $userId]);
  $user = $stmt->fetch();

  if (!$user || !password_verify($current, $user['password_hash'])) {
    setFlash('error', 'The temporary password is incorrect.');
    redirect('/change-password.php');
  }

  // Hash the new password and clear the must_change flag
  $newHash = password_hash($new, PASSWORD_DEFAULT);
  $upd = $pdo->prepare("UPDATE users
                        SET password_hash = :hash,
                        must_change_password = 0,
                        updated_at = NOW()
                        WHERE user_id = :uid");

  $upd->execute([':hash' => $newHash, ':uid' => $userId]);

} catch (PDOException $e) {
    setFlash('error', 'An error occurred: ' . $e->getMessage());
    redirect('/change-password.php');
  }

setFlash('success', 'Password changed successfully. Welcome back!');
header('Location: ' . dashboardUrl(currentRole()));
exit;

<?php

// Records a password reset request for admin review.

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  redirect('/forgot-password.php');
}

verifyCsrf();

$email = sanitize($_POST['email'] ?? '');

// Validate
if (empty($email) || !isValidEmail($email)) {
  setFlash('error', 'Please enter a valid email address.');
  redirect('/forgot-password.php');
}

try {
  // Look up the user. If they exist AND are active, create a reset request.
  $stmt = $pdo->prepare(
      "SELECT user_id, is_active FROM users WHERE email = :email LIMIT 1"
  );
  $stmt->execute([':email' => $email]);
  $user = $stmt->fetch();

  if ($user && (int)$user['is_active'] === 1) {
    // Check if there's already a pending request for this user to avoid spam
    $checkPending = $pdo->prepare(
     "SELECT request_id FROM password_reset_requests
      WHERE user_id = :uid AND status = 'pending'
      LIMIT 1"
    );
    
    $checkPending->execute([':uid' => $user['user_id']]);

    if (!$checkPending->fetch()) {
      // No pending request — create one
      $insert = $pdo->prepare(
       "INSERT INTO password_reset_requests
        (user_id, email_submitted, status, requested_at)
        VALUES (:uid, :em, 'pending', NOW())"
      );
    
      $insert->execute([':uid' => $user['user_id'],':em'  => $email]);
    }
  }

} catch (PDOException $e) {
    setFlash('error', 'A system error occurred. Please try again later.');
    redirect('/forgot-password.php');
  }

setFlash(
  'success',
  'If an account exists for that email, a password reset request has been submitted. ' .
  'An administrator will contact you with a temporary password.'
);

redirect('/login.php');

<?php
// Handles the admin's decision on a pending password reset request:
// decision='reset'  -> generate temp password, mark user must_change_password
// decision='reject' -> mark request as rejected

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../includes/functions.php';

requireRole('admin');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  redirect('/admin/password-resets.php');
}

verifyCsrf();

$requestId = (int)($_POST['request_id'] ?? 0);
$decision = sanitize($_POST['decision'] ?? '');

if ($requestId <= 0 || !in_array($decision, ['reset', 'reject'], true)) {
  setFlash('error', 'Invalid request.');
  redirect('/admin/password-resets.php');
}

try {
  // Fetch the request + the user it belongs to
  $stmt = $pdo->prepare(
     "SELECT r.request_id, r.status, u.user_id, u.first_name, u.last_name, u.email
      FROM password_reset_requests r
      JOIN users u ON r.user_id = u.user_id
      WHERE r.request_id = :rid
      LIMIT 1"
    );
    
  $stmt->execute([':rid' => $requestId]);
  $req = $stmt->fetch();

  if (!$req) {
    setFlash('error', 'Reset request not found.');
    redirect('/admin/password-resets.php');
  }

  if ($req['status'] !== 'pending') {
    setFlash('error', 'This request has already been handled.');
    redirect('/admin/password-resets.php');
  }

  $adminId = currentUserId();

  if ($decision === 'reject') {
     // Mark as rejected
     $upd = $pdo->prepare(
      "UPDATE password_reset_requests
       SET status = 'rejected', handled_by_user_id = :aid,
                     handled_at = NOW(), notes = 'Rejected by admin'
       WHERE request_id = :rid"
     );
     $upd->execute([':aid' => $adminId, ':rid' => $requestId]);

     setFlash('success', 'Request rejected.');
     redirect('/admin/password-resets.php');
    }

  //-----------------
  // Reset Decision
  //-----------------

  // Generate a random temporary password (12 chars, mixed case + digits)
  $tempPassword = generateTempPassword(12);
  $hash = password_hash($tempPassword, PASSWORD_DEFAULT);

  $pdo->beginTransaction();

  // Update the user's password and force change on next login
  $updUser = $pdo->prepare(
     "UPDATE users
      SET password_hash = :hash, must_change_password = 1,
      updated_at = NOW()
      WHERE user_id = :uid"
     );
  $updUser->execute([':hash' => $hash, ':uid' => $req['user_id']]);

  // Mark the request as completed
  $updReq = $pdo->prepare(
     "UPDATE password_reset_requests
      SET status = 'completed', handled_by_user_id = :aid,
                    handled_at = NOW(), temp_password_set = 1,
                    notes = 'Temporary password issued'
      WHERE request_id = :rid"
    );
  $updReq->execute([':aid' => $adminId, ':rid' => $requestId]);

  $pdo->commit();

  // Show the temporary password to the admin ONCE
  $userName = $req['first_name'] . ' ' . $req['last_name'];
  setFlash('success', 'Temporary password for ' . e($userName) . 
            ' (' . e($req['email']) . '): ' .
            '<strong style="font-family:monospace;font-size:1.1em;background:#fff;padding:2px 8px;border-radius:3px;">' .
            e($tempPassword) . '</strong>' .
            '<br><small>Communicate this to the user. They will be required to change it on next login.</small>'
     );

} catch (PDOException $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    setFlash('error', 'An error occurred: ' . $e->getMessage());
}

redirect('/admin/password-resets.php');


//Generates a random but secure temporary password (excluding 0/O, 1/l/I)
function generateTempPassword(int $length = 12): string
{
  // No 0/O/1/l/I to avoid confusion when reading the password aloud
  $upper = 'ABCDEFGHJKLMNPQRSTUVWXYZ';
  $lower = 'abcdefghjkmnpqrstuvwxyz';
  $digits = '23456789';
  $all = $upper . $lower . $digits;

  // Ensure at least one of each
  $password = $upper[random_int(0, strlen($upper) - 1)];
  $password .= $lower[random_int(0, strlen($lower) - 1)];
  $password .= $digits[random_int(0, strlen($digits) - 1)];

  // Fill the rest
  for ($i = strlen($password); $i < $length; $i++) {
    $password .= $all[random_int(0, strlen($all) - 1)];
  }

  // Shuffle so the guaranteed characters aren't always at the start
  return str_shuffle($password);
}

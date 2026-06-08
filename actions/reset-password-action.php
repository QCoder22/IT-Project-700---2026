<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../includes/functions.php';

requireRole('admin');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('/admin/password-resets.php');
}

$requestId = (int)($_POST['request_id'] ?? 0);
$decision  = sanitize($_POST['decision'] ?? '');

if ($requestId <= 0 || !in_array($decision, ['reset', 'reject'])) {
    setMsg('error', 'Invalid request.');
    redirect('/admin/password-resets.php');
}

$ps = $pdo->prepare("SELECT r.request_id, r.status, u.user_id, u.first_name, u.last_name, u.email
                     FROM password_reset_requests r
                     JOIN users u ON r.user_id = u.user_id
                     WHERE r.request_id = ? LIMIT 1");
$ps->execute([$requestId]);
$req = $ps->fetch();

if (!$req) {
    setMsg('error', 'Reset request not found.');
    redirect('/admin/password-resets.php');
}

if ($req['status'] !== 'pending') {
    setMsg('error', 'This request has already been handled.');
    redirect('/admin/password-resets.php');
}

$adminId = $_SESSION['user_id'];

if ($decision === 'reject') {
    $upd = $pdo->prepare("UPDATE password_reset_requests
                          SET status = 'rejected', handled_by_user_id = ?, handled_at = NOW(), notes = 'Rejected by admin'
                          WHERE request_id = ?");
    $upd->execute([$adminId, $requestId]);

    setMsg('success', 'Request rejected.');
    redirect('/admin/password-resets.php');
}

$tempPw = generateTempPassword(12);
$hash = password_hash($tempPw, PASSWORD_DEFAULT);

try {
    $pdo->beginTransaction();

    $updUser = $pdo->prepare("UPDATE users
                              SET password_hash = ?, must_change_password = 1, updated_at = NOW()
                              WHERE user_id = ?");
    $updUser->execute([$hash, $req['user_id']]);

    $updReq = $pdo->prepare("UPDATE password_reset_requests
                             SET status = 'completed', handled_by_user_id = ?, handled_at = NOW(),
                                 temp_password_set = 1, notes = 'Temporary password issued'
                             WHERE request_id = ?");
    $updReq->execute([$adminId, $requestId]);

    $pdo->commit();
} catch (PDOException $e) {
    $pdo->rollBack();
    setMsg('error', 'An error occurred. Please try again.');
    redirect('/admin/password-resets.php');
}

$name = $req['first_name'] . ' ' . $req['last_name'];
setMsg('persistent',
    'Temporary password for ' . e($name) . ' (' . e($req['email']) . '): '
    . '<strong style="font-family:monospace;font-size:1.1em;background:#fff;padding:2px 8px;border-radius:3px;">'
    . e($tempPw) . '</strong>'
    . '<br><small>Give this to the user. They must change it on next login.</small>'
);
redirect('/admin/password-resets.php');

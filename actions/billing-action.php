<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../includes/functions.php';

requireRole('receptionist');

try {

    $billingId = $_POST['billing_id'] ?? 0;

    if (!$billingId) {
        throw new Exception('Invalid invoice ID.');
    }

    $stmt = $pdo->prepare("
        UPDATE billing
        SET
            payment_status = 'paid',
            amount_paid = total_amount,
            payment_method = 'cash',
            paid_date = NOW()
        WHERE billing_id = ?
        AND payment_status != 'cancelled'
    ");

    $stmt->execute([$billingId]);

    setMsg('success', 'Payment recorded successfully.');

    redirect('/receptionist/view-invoice.php?id=' . $billingId);

} catch (Exception $e) {

    setMsg('error', $e->getMessage());

    redirect('/receptionist/billing.php');
}
?>

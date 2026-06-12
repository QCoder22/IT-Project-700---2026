<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../includes/functions.php';

requireRole('receptionist');

$billingId = $_GET['id'] ?? 0;

$stmt = $pdo->prepare("
    SELECT
        b.*,
        u.first_name,
        u.last_name
    FROM billing b
    JOIN patients p ON b.patient_id = p.patient_id
    JOIN users u ON p.user_id = u.user_id
    WHERE b.billing_id = ?
");

$stmt->execute([$billingId]);
$invoice = $stmt->fetch();

if (!$invoice) {
    setMsg('error', 'Invoice not found.');
    redirect('/receptionist/billing.php');
}

$itemStmt = $pdo->prepare("
    SELECT *
    FROM billing_items
    WHERE billing_id = ?
");

$itemStmt->execute([$billingId]);
$items = $itemStmt->fetchAll();

$pageTitle = 'Invoice Details';
require_once __DIR__ . '/../includes/header.php';
?>

<h1>Invoice Details</h1>

<div class="card mb-3">
    <div class="card-body">

        <p>
            <strong>Invoice Number:</strong>
            <?= e($invoice['invoice_number']); ?>
        </p>

        <p>
            <strong>Patient:</strong>
            <?= e($invoice['first_name'] . ' ' . $invoice['last_name']); ?>
        </p>

        <p>
            <strong>Total Amount:</strong>
            R <?= number_format($invoice['total_amount'], 2); ?>
        </p>

        <p>
            <strong>Amount Paid:</strong>
            R <?= number_format($invoice['amount_paid'], 2); ?>
        </p>

        <p>
            <strong>Balance:</strong>
            R <?= number_format($invoice['total_amount'] - $invoice['amount_paid'], 2); ?>
        </p>

        <p class="mb-0">
            <strong>Status:</strong>

            <?php
            $status = $invoice['payment_status'];

            if ($status == 'paid') {
                echo "<span class='badge bg-success'>Paid</span>";
            } elseif ($status == 'partially_paid') {
                echo "<span class='badge bg-warning text-dark'>Partially Paid</span>";
            } elseif ($status == 'cancelled') {
                echo "<span class='badge bg-secondary'>Cancelled</span>";
            } else {
                echo "<span class='badge bg-danger'>Pending</span>";
            }
            ?>
        </p>

    </div>
</div>

<div class="card mb-3">
    <div class="card-header"><strong>Invoice Items</strong></div>
    <div class="table-responsive">
        <table class="table mb-0">
            <thead>
                <tr>
                    <th>Description</th>
                    <th>Quantity</th>
                    <th>Unit Price</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>

                <?php if (empty($items)): ?>
                <tr><td colspan="4" class="text-muted">No line items on this invoice.</td></tr>
                <?php endif; ?>

                <?php foreach ($items as $item): ?>
                <tr>
                    <td><?= e($item['description']); ?></td>
                    <td><?= e($item['quantity']); ?></td>
                    <td>R <?= number_format($item['unit_price'], 2); ?></td>
                    <td>R <?= number_format($item['line_total'], 2); ?></td>
                </tr>
                <?php endforeach; ?>

            </tbody>
        </table>
    </div>
</div>

<?php if ($invoice['payment_status'] != 'paid' && $invoice['payment_status'] != 'cancelled'): ?>

<form method="POST" action="<?= BASE_URL ?>/actions/billing-action.php">

    <input type="hidden" name="billing_id" value="<?= $invoice['billing_id']; ?>">

    <button type="submit" class="btn btn-success"
            onclick="return confirm('Mark this invoice as fully paid?')">
        Mark as Paid
    </button>

    <a href="billing.php" class="btn btn-outline-secondary">Back to Billing</a>

</form>

<?php else: ?>

<a href="billing.php" class="btn btn-outline-secondary">Back to Billing</a>

<?php endif; ?>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>

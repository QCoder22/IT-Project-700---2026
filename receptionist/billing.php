<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../includes/functions.php';

requireRole('receptionist');

$stmt = $pdo->query("
    SELECT
        b.billing_id,
        b.invoice_number,
        b.total_amount,
        b.amount_paid,
        b.payment_status,
        b.billing_date,
        u.first_name,
        u.last_name
    FROM billing b
    JOIN patients p ON b.patient_id = p.patient_id
    JOIN users u ON p.user_id = u.user_id
    ORDER BY b.billing_date DESC
");

$invoices = $stmt->fetchAll();

$pageTitle = 'Billing Management';
require_once __DIR__ . '/../includes/header.php';
?>

<h1>Billing Management</h1>
<p class="text-muted">Invoice list</p>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>Invoice</th>
                    <th>Patient</th>
                    <th>Total</th>
                    <th>Paid</th>
                    <th>Balance</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>

                <?php if(empty($invoices)): ?>
                <tr><td colspan="8" class="text-muted">No invoices yet.</td></tr>
                <?php endif; ?>

                <?php foreach($invoices as $invoice): ?>
                <tr>
                    <td><?= e($invoice['invoice_number']); ?></td>
                    <td><?= e($invoice['first_name'] . ' ' . $invoice['last_name']); ?></td>
                    <td>R <?= number_format($invoice['total_amount'], 2); ?></td>
                    <td>R <?= number_format($invoice['amount_paid'], 2); ?></td>
                    <td>R <?= number_format($invoice['total_amount'] - $invoice['amount_paid'], 2); ?></td>
                    <td>

                        <?php
                        $status = $invoice['payment_status'];

                        if($status == 'paid')
                        {
                            echo "<span class='badge bg-success'>Paid</span>";
                        }
                        elseif($status == 'partially_paid')
                        {
                            echo "<span class='badge bg-warning text-dark'>Partially Paid</span>";
                        }
                        elseif($status == 'cancelled')
                        {
                            echo "<span class='badge bg-secondary'>Cancelled</span>";
                        }
                        else
                        {
                            echo "<span class='badge bg-danger'>Pending</span>";
                        }
                        ?>

                    </td>
                    <td><?= e(formatDate($invoice['billing_date'])); ?></td>
                    <td>
                        <a class="btn btn-sm btn-outline-primary" href="view-invoice.php?id=<?= $invoice['billing_id']; ?>">
                            View
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>

            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>

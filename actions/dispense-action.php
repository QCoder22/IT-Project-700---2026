<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../includes/functions.php';

requireRole('receptionist');

try {

    $pdo->beginTransaction();

    $pid = $_POST['prescription_id'];

    /*
    ====================================================
    GET PRESCRIPTION DETAILS
    ====================================================
    */

    $stmt = $pdo->prepare("
        SELECT
            p.prescription_id,
            p.patient_id,
            p.appointment_id,
            p.doctor_id,
            d.consultation_fee
        FROM prescriptions p
        JOIN doctors d
            ON p.doctor_id = d.doctor_id
        WHERE p.prescription_id = ?
    ");

    $stmt->execute([$pid]);

    $rx = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$rx) {
        throw new Exception('Prescription not found.');
    }

    /*
    ====================================================
    DISPENSE PRESCRIPTION
    ====================================================
    */

    $pdo->prepare("
        UPDATE prescriptions
        SET
            status='dispensed',
            dispensed_at=NOW(),
            dispensed_by_user_id=?
        WHERE prescription_id=?
    ")
    ->execute([
        $_SESSION['user_id'],
        $pid
    ]);

    /*
    ====================================================
    REDUCE INVENTORY
    ====================================================
    */

    $itemsStmt = $pdo->prepare("
        SELECT *
        FROM prescription_items
        WHERE prescription_id=?
    ");

    $itemsStmt->execute([$pid]);

    $items = $itemsStmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($items as $item) {

        $pdo->prepare("
            UPDATE inventory
            SET quantity = quantity - ?
            WHERE inventory_id = ?
        ")
        ->execute([
            $item['quantity'],
            $item['inventory_id']
        ]);
    }

    /*
    ====================================================
    CREATE BILLING RECORD
    ====================================================
    */

    $invoiceNumber =
        'INV-' .
        date('YmdHis') .
        '-' .
        $pid;

    $totalAmount = $rx['consultation_fee'];

    foreach ($items as $item) {

        $totalAmount +=
            ($item['quantity'] *
             $item['unit_price_at_issue']);
    }

    $pdo->prepare("
        INSERT INTO billing
        (
            patient_id,
            appointment_id,
            invoice_number,
            total_amount,
            amount_paid,
            payment_status,
            billing_date
        )
        VALUES
        (
            ?, ?, ?, ?, 0,
            'pending',
            CURDATE()
        )
    ")
    ->execute([
        $rx['patient_id'],
        $rx['appointment_id'],
        $invoiceNumber,
        $totalAmount
    ]);

    $billingId = $pdo->lastInsertId();

    /*
    ====================================================
    CONSULTATION LINE ITEM
    ====================================================
    */

    $pdo->prepare("
        INSERT INTO billing_items
        (
            billing_id,
            item_type,
            description,
            quantity,
            unit_price,
            line_total
        )
        VALUES
        (
            ?,
            'consultation',
            'Doctor Consultation',
            1,
            ?,
            ?
        )
    ")
    ->execute([
        $billingId,
        $rx['consultation_fee'],
        $rx['consultation_fee']
    ]);

    /*
    ====================================================
    MEDICATION LINE ITEMS
    ====================================================
    */

    foreach ($items as $item) {

        $lineTotal =
            $item['quantity'] *
            $item['unit_price_at_issue'];

        $pdo->prepare("
            INSERT INTO billing_items
            (
                billing_id,
                item_type,
                description,
                prescription_item_id,
                inventory_id,
                quantity,
                unit_price,
                line_total
            )
            VALUES
            (
                ?,
                'medication',
                'Medication',
                ?,
                ?,
                ?,
                ?,
                ?
            )
        ")
        ->execute([
            $billingId,
            $item['item_id'],
            $item['inventory_id'],
            $item['quantity'],
            $item['unit_price_at_issue'],
            $lineTotal
        ]);
    }

    /*
    ====================================================
    STATUS LOG
    ====================================================
    */

    $pdo->prepare("
        INSERT INTO prescription_status_log
        (
            prescription_id,
            changed_by_user_id,
            old_status,
            new_status,
            comment
        )
        VALUES
        (
            ?, ?, ?, ?, ?
        )
    ")
    ->execute([
        $pid,
        $_SESSION['user_id'],
        'pending',
        'dispensed',
        'Dispensed at reception'
    ]);

    $pdo->commit();

    setMsg(
        'success',
        'Prescription dispensed and invoice generated successfully.'
    );

    redirect('/receptionist/prescription-queue.php');

} catch (Exception $e) {

    $pdo->rollBack();

    setMsg(
        'error',
        'Error: ' . $e->getMessage()
    );

    redirect('/receptionist/prescription-queue.php');
}
?>

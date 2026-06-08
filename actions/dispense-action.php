<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../includes/functions.php';

requireRole('receptionist');

try {

$pdo->beginTransaction();

$pid = $_POST['prescription_id'];

$pdo->prepare("UPDATE prescriptions SET status='dispensed', dispensed_at=NOW(), dispensed_by_user_id=? WHERE prescription_id=?")
    ->execute([$_SESSION['user_id'], $pid]);

$items = $pdo->prepare("SELECT * FROM prescription_items WHERE prescription_id=?");
$items->execute([$pid]);

foreach($items as $i){

    $pdo->prepare("UPDATE inventory SET quantity = quantity - ? WHERE inventory_id=?")
        ->execute([$i['quantity'], $i['inventory_id']]);
}

$pdo->prepare("INSERT INTO prescription_status_log
    (prescription_id, changed_by_user_id, old_status, new_status, comment)
    VALUES (?,?,?,?,?)")
    ->execute([$pid, $_SESSION['user_id'], 'pending', 'dispensed', 'Dispensed at reception']);

$pdo->commit();

setMsg('success', 'Dispensed successfully.');
redirect('/receptionist/prescription-queue.php');

} catch(Exception $e){
    $pdo->rollBack();
    setMsg('error', 'Error: '.$e->getMessage());
    redirect('/receptionist/prescription-queue.php');
}
?>

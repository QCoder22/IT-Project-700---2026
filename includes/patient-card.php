<?php
// Reusable patient summary card.
//
// Usage from any page that has already fetched a patient row:
//
//     $patient = $pdo->prepare("SELECT u.first_name, u.last_name, u.email, u.phone,
//                                      p.patient_id, p.dob, p.gender
//                               FROM patients p
//                               JOIN users u ON p.user_id = u.user_id
//                               WHERE p.patient_id = ?")
//                    ->execute([$patientId]);
//     include __DIR__ . '/../includes/patient-card.php';
//
// The patient row must contain: first_name, last_name, email, phone, dob, gender.

if (empty($patient)) return;
?>
<div class="card patient-card mb-3">
    <div class="card-body">

        <h5 class="card-title">
            <i class="bi bi-person-circle"></i>
            <?= e($patient['first_name'] . ' ' . $patient['last_name']) ?>
        </h5>

        <p class="mb-1">
            <strong>Email:</strong>
            <?= e($patient['email']) ?>
        </p>

        <p class="mb-1">
            <strong>Phone:</strong>
            <?= e($patient['phone']) ?>
        </p>

        <p class="mb-1">
            <strong>DOB:</strong>
            <?= e(formatDate($patient['dob'])) ?>
        </p>

        <p class="mb-0">
            <strong>Gender:</strong>
            <?= e($patient['gender']) ?>
        </p>

    </div>
</div>

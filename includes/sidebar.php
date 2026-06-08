<?php
if (!isLoggedIn()) return;

$role = currentRole();
$current = $_SERVER['SCRIPT_NAME'];

$menus = [
    'admin' => [
        ['Dashboard',        '/admin/dashboard.php',       'bi-speedometer2'],
        ['Manage Doctors',   '/admin/manage-doctors.php',  'bi-person-badge'],
        ['Manage Staff',     '/admin/manage-staff.php',    'bi-people'],
        ['View Patients',    '/admin/view-patients.php',   'bi-person-lines-fill'],
        ['Inventory',        '/admin/inventory.php',       'bi-capsule'],
        ['Password Resets',  '/admin/password-resets.php', 'bi-key'],
        ['Reports',          '/admin/reports.php',         'bi-graph-up'],
    ],
    'doctor' => [
        ['Dashboard',           '/doctor/dashboard.php',           'bi-speedometer2'],
        ['Appointments',        '/doctor/appointments.php',        'bi-calendar-week'],
        ['Patient Records',     '/doctor/patient-records.php',     'bi-folder2-open'],
        ['Issue Prescription',  '/doctor/issue-prescription.php',  'bi-prescription2'],
        ['Prescription Status', '/doctor/prescription-status.php', 'bi-clipboard-check'],
    ],
    'receptionist' => [
        ['Dashboard',          '/receptionist/dashboard.php',          'bi-speedometer2'],
        ['Register Patient',   '/receptionist/register-patient.php',   'bi-person-plus'],
        ['Book Appointment',   '/receptionist/book-appointment.php',   'bi-calendar-plus'],
        ['Search Patient',     '/receptionist/search-patient.php',     'bi-search'],
        ['Prescription Queue', '/receptionist/prescription-queue.php', 'bi-card-checklist'],
        ['Dispense',           '/receptionist/dispense.php',           'bi-bag-check'],
        ['Billing',            '/receptionist/billing.php',            'bi-receipt'],
    ],
    'patient' => [
        ['Dashboard',           '/patient/dashboard.php',           'bi-speedometer2'],
        ['Book Appointment',    '/patient/book-appointment.php',    'bi-calendar-plus'],
        ['Appointment History', '/patient/appointment-history.php', 'bi-clock-history'],
        ['My Profile',          '/patient/profile.php',             'bi-person-circle'],
    ],
];

$menu = $menus[$role] ?? [];
?>
<aside class="medicom-sidebar">
    <nav class="nav flex-column">
        <?php foreach ($menu as $item): ?>
            <?php $active = str_ends_with($current, $item[1]) ? 'active' : ''; ?>
            <a class="nav-link <?= $active ?>" href="<?= BASE_URL . $item[1] ?>">
                <i class="bi <?= $item[2] ?>"></i>
                <span><?= e($item[0]) ?></span>
            </a>
        <?php endforeach; ?>
    </nav>
</aside>

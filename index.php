<?php
require_once __DIR__ . '/config/auth.php';

if (isLoggedIn()) {
    header('Location: ' . dashboardUrl(currentRole()));
} else {
    header('Location: ' . BASE_URL . '/login.php');
}
exit;

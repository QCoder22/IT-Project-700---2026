<?php
require_once __DIR__ . '/config/auth.php';
require_once __DIR__ . '/includes/functions.php';

logoutUser();
session_start();
setMsg('success', 'You have been logged out.');
header('Location: ' . BASE_URL . '/login.php');
exit;

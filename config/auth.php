<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!defined('BASE_URL')) {
    define('BASE_URL', '/medicom');
}

function isLoggedIn() {
    return !empty($_SESSION['user_id']);
}

function currentUser() {
    if (!isLoggedIn()) return null;
    return [
        'id'         => $_SESSION['user_id'],
        'first_name' => $_SESSION['first_name'],
        'last_name'  => $_SESSION['last_name'],
        'email'      => $_SESSION['email'],
        'role'       => $_SESSION['role'],
    ];
}

function currentRole() {
    return $_SESSION['role'] ?? null;
}

function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: ' . BASE_URL . '/login.php');
        exit;
    }
}

function requireRole($role) {
    requireLogin();
    if (currentRole() !== $role) {
        header('Location: ' . dashboardUrl(currentRole()));
        exit;
    }
}

function loginUser($u) {
    session_regenerate_id(true);
    $_SESSION['user_id']    = $u['user_id'];
    $_SESSION['first_name'] = $u['first_name'];
    $_SESSION['last_name']  = $u['last_name'];
    $_SESSION['email']      = $u['email'];
    $_SESSION['role']       = $u['role'];
}

function logoutUser() {
    $_SESSION = [];
    session_destroy();
}

function dashboardUrl($role) {
    switch ($role) {
        case 'admin':        return BASE_URL . '/admin/dashboard.php';
        case 'doctor':       return BASE_URL . '/doctor/dashboard.php';
        case 'receptionist': return BASE_URL . '/receptionist/dashboard.php';
        case 'patient':      return BASE_URL . '/patient/dashboard.php';
        default:             return BASE_URL . '/login.php';
    }
}

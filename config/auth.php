<?php

// Session Configuration
if (session_status() == PHP_SESSION_NONE) {
    //Secure session settings
    ini_set('session.use_only_cookies', 1);
    ini_set('session.use_strict_mode', 1);
    ini_set('session.cookie_httponly', 1); //JS can't read the cookie

    // 30 min session time-out
    ini_set('session.gc_maxlifetime', 1800);

    session_start();

    // Idle timeout check
    if (isset($_SESSION['last_activity']) &&
        (time() - $_SESSION['last_activity']) > 1800) {
        logoutUser();
        header('Location: /medicom/login.php?timeout=1');
        exit;
    }
    $_SESSION['last_activity'] = time();
}

// Base URL
if (!defined('BASE_URL')) {
    define('BASE_URL', '/medicom');
}

// Check if a user is currently logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

// Redirects to Login if not authenicated
function requireLogin(): void {
    if (!isLoggedIn()) {
        header('Location: ' . BASE_URL . '/login.php');
        exit;
    }
}

// Restrict current page to specific user role/s
function requireRole($allowedRoles): void {
    requireLogin();

    if (is_string($allowedRoles)) {
        $allowedRoles = [$allowedRoles];
    }

    if (!in_array(currentRole(), $allowedRoles, true)) {
        // Logged in but wrong role - send to their own dashboard
        header('Location: ' . dashboardUrl(currentRole()) . '?denied=1');
        exit;
    }
}

// Return the full session record of the Logged-in user
function currentUser(): ?array {
    if (!isLoggedIn()) return null;
    return ['id' => $_SESSION['user_id'] ?? null,
            'first_name' => $_SESSION['first_name'] ?? '',
            'last_name' => $_SESSION['last_name'] ?? '',
            'email' => $_SESSION['email'] ?? '',
            'role' => $_SESSION['role'] ?? '',
    ];
}

function currentUserId(): ?int {
    return $_SESSION['user_id'] ?? null;
}

function currentRole(): ?string {
    return $_SESSION['role'] ?? null;
}

function hasRole(string $role): bool {
    return currentRole() === $role;
}

// Store user info in session after successful login
function loginUser(array $user): void {
    session_regenerate_id(true);

    $_SESSION['user_id'] = $user['user_id'];
    $_SESSION['first_name'] = $user['first_name'];
    $_SESSION['last_name'] = $user['last_name'];
    $_SESSION['email'] = $user['email'];
    $_SESSION['role'] = $user['role'];
    $_SESSION['last_activity'] = time();
}

//Destroy te session and clear all login data
function logoutUser(): void {
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
                  $params['path'], $params['domain'],
                  $params['secure'], $params['httponly']
        );
    }
    session_destroy();
}

//get the correct dashboard URL based on the user role
function dashboardUrl(?string $role): string {
    $base = BASE_URL;
    switch ($role) {
        case 'admin': return "$base/admin/dashboard.php";
        case 'doctor': return "$base/doctor/dashboard.php";
        case 'receptionist': return "$base/receptionist/dashboard.php";
        case 'patient': return "$base/patient/dashboard.php";
        default: return "$base/login.php";
    }
}
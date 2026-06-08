<?php

function sanitize($s) {
    return trim($s ?? '');
}

function e($s) {
    return htmlspecialchars($s ?? '', ENT_QUOTES, 'UTF-8');
}

function isValidEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

function isStrongPassword($pw) {
    return strlen($pw) >= 8
        && preg_match('/[A-Za-z]/', $pw)
        && preg_match('/[0-9]/', $pw);
}

function formatDate($d) {
    if (empty($d) || $d === '0000-00-00') return '-';
    return date('d M Y', strtotime($d));
}

function formatDateTime($d) {
    if (empty($d)) return '-';
    return date('d M Y, H:i', strtotime($d));
}

function formatCurrency($amount) {
    return 'R ' . number_format((float)$amount, 2, '.', ' ');
}

function redirect($path) {
    header('Location: ' . BASE_URL . $path);
    exit;
}

function setMsg($type, $text) {
    $_SESSION['msg'] = ['type' => $type, 'text' => $text];
}

function showMsg() {
    if (empty($_SESSION['msg'])) return '';
    $m = $_SESSION['msg'];
    unset($_SESSION['msg']);
    $cls = $m['type'] === 'error' ? 'danger' : $m['type'];
    return '<div class="alert alert-' . $cls . ' alert-dismissible fade show" role="alert">'
         . $m['text']
         . '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>'
         . '</div>';
}

function generateTempPassword($length = 12) {
    $chars = 'ABCDEFGHJKLMNPQRSTUVWXYZabcdefghjkmnpqrstuvwxyz23456789';
    $pw = '';
    for ($i = 0; $i < $length; $i++) {
        $pw .= $chars[random_int(0, strlen($chars) - 1)];
    }
    return $pw;
}

function stockStatus($qty, $min, $expiry) {
    return [
        "low_stock" => $qty <= $min,
        "expiring_soon" => !empty($expiry) && strtotime($expiry) <= strtotime("+30 days")
    ];
}

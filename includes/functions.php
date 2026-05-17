<?php

//-------------------
// INPUT VALIDATION
//-------------------


// Trims and cleans user input
function sanitize(?string $input): string {
  if ($input === null) return '';
    $input = trim($input);
    $input = str_replace("\0", '', $input);
    return $input;
}

// Escape output for safe display in HTML
function e(?string $value): string {
  return htmlspecialchars($value ?? '', ENT_QUOTES | ENT_HTML5, 'UTF-8');
}

//Validate email address
function isValidEmail(string $email): bool {
  return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

// Validates South African phone number
// Accepts formats like 0712345678, +27712345678, 27712345678
function isValidPhone(string $phone): bool {
  $phone = preg_replace('/\s+/', '', $phone);
  return preg_match('/^(\+27|27|0)[6-8][0-9]{8}$/', $phone) === 1;
}

//Validates date string in YYY-MM-DD format
function isValidDate(string $date): bool {
  $d = DateTime::createFromFormat('Y-m-d', $date);
  return $d && $d->format('Y-m-d') === $date;
}

//Validates password strength (at least 8 characters, one letter, one number)
function isStrongPassword(string $password): bool {
  return strlen($password) >= 8 && preg_match('/[A-Za-z]/', $password) 
    && preg_match('/[0-9]/', $password);
}

//-------------------
// FORMATTING
//-------------------

//Format for datetime display (16 June 2026, 17:00)
function formatDateTime(?string $datetime, string $format = 'd M Y, H:i'): string {
  if (empty($datetime)) return '-';
  try {
    return (new DateTime($datetime))->format($format);
  } catch (Exception $e) {
      return '-'; 
    }
}

// Formatting for Rands
function formatCurrency($amount): string {
  return 'R ' . number_format((float)$amount, 2, '.', ' ');
}

// Calulate age from date of birth
function calculateAge(string $dob): int {
  try {
    $dobDate = new DateTime($dob);
    $today = new DateTime();
    return $today->diff($dobDate)->y;
  } catch (Exception $e) {
      return 0;
    }
}

//-------------------
// FLASH MESSAGES
//-------------------

// Sets a one-time flash message the survives a redirect
function setFlash(string $type, string $message): void {
  $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

//Retrieve and clear flash message
function getFlash(): ?array {
  if (!isset($_SESSION['flash'])) return null;
  $flash = $_SESSION['flash'];
  unset($_SESSION['flash']);
  return $flash;
}

// Render a Bootstrap alert for the current flash message
function renderFlash(): string {
  $flash = getFlash();
  if (!$flash) return '';

  $typeMap = [
    'success' => 'success',
    'error'   => 'danger',
    'warning' => 'warning',
    'info'    => 'info',
  ];

  $bsClass = $typeMap[$flash['type']] ?? 'info';

  return '<div class="alert alert-' . $bsClass 
        . ' alert-dismissible fade show" role="alert">'
        . e($flash['message'])
        . '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>'
        . '</div>';
}

//-------------------
// CSRF PROTECTION
//-------------------

// Generate or retrieve a CSRF token for the current session
function csrfToken(): string {
  if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
  }
  return $_SESSION['csrf_token'];
}

// Verifies a submitted CSRF token
function verifyCsrf(): void {
  $submitted = $_POST['csrf_token'] ?? '';
  $stored    = $_SESSION['csrf_token'] ?? '';
  if (!$submitted || !hash_equals($stored, $submitted)) {
    http_response_code(403);
    die('Invalid CSRF token. Please go back and try again.');
  }
}

//-------------------
// REDIRCT HELPER
//-------------------

// Redirect to a given path under BASE_URL and exit
function redirect(string $path): void {
  $base = defined('BASE_URL') ? BASE_URL : '';
  header('Location: ' . $base . $path);
  exit;
}

// Redirect back to the referring page
function redirectBack(string $fallback = '/'): void {
  $referer = $_SERVER['HTTP_REFERER'] ?? null;
  if ($referer) {
    header('Location: ' . $referer);
  } else {
      redirect($fallback);
    }
  exit;
}


// ============================================================
// MISC
// ============================================================

/**
 * Pretty-print a variable for debugging. Remove all uses before submission.
 */
function dd($var): void {
    echo '<pre style="background:#222;color:#0f0;padding:1em;">';
    print_r($var);
    echo '</pre>';
    exit;
}
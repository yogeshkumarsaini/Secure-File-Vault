<?php

function redirect(string $path): void
{
    header('Location: ' . $path);
    exit;
}

function e(?string $value): string
{
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

function set_flash(string $type, string $message): void
{
    $_SESSION['flash'][] = ['type' => $type, 'message' => $message];
}

function get_flashes(): array
{
    $flashes = $_SESSION['flash'] ?? [];
    unset($_SESSION['flash']);
    return $flashes;
}

function clean_input(string $value): string
{
    return trim(strip_tags($value));
}

function is_valid_email(string $email): bool
{
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

function validate_password_strength(string $password): ?string
{
    if (strlen($password) < 10) {
        return 'Password must be at least 10 characters long.';
    }
    if (!preg_match('/[A-Z]/', $password) || !preg_match('/[a-z]/', $password)) {
        return 'Password must contain both uppercase and lowercase letters.';
    }
    if (!preg_match('/[0-9]/', $password)) {
        return 'Password must contain at least one number.';
    }
    return null;
}

function format_bytes(int $bytes, int $precision = 2): string
{
    $units = ['B', 'KB', 'MB', 'GB', 'TB'];
    $bytes = max($bytes, 0);
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
    $pow = min($pow, count($units) - 1);
    $bytes /= (1 << (10 * $pow));
    return round($bytes, $precision) . ' ' . $units[$pow];
}

function get_extension(string $filename): string
{
    return strtolower(pathinfo($filename, PATHINFO_EXTENSION));
}

function random_token(int $bytes = 16): string
{
    return bin2hex(random_bytes($bytes));
}

function too_many_attempts(string $action, int $maxAttempts = 5, int $windowSeconds = 300): bool
{
    $key = 'rate_' . $action;
    $now = time();

    if (!isset($_SESSION[$key])) {
        $_SESSION[$key] = ['count' => 0, 'start' => $now];
    }

    if ($now - $_SESSION[$key]['start'] > $windowSeconds) {
        $_SESSION[$key] = ['count' => 0, 'start' => $now];
    }

    $_SESSION[$key]['count']++;

    return $_SESSION[$key]['count'] > $maxAttempts;
}

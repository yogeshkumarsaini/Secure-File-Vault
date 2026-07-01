<?php

error_reporting(E_ALL);
ini_set('display_errors', '0'); 
ini_set('log_errors', '1');

ini_set('session.cookie_httponly', '1');
ini_set('session.use_only_cookies', '1');
ini_set('session.cookie_samesite', 'Strict');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

define('BASE_PATH', dirname(__DIR__));
define('UPLOAD_DIR', BASE_PATH . '/uploads/encrypted/');
define('TEMP_DIR', BASE_PATH . '/uploads/temp/');

define('APP_ENCRYPTION_KEY_HEX', '8f3a1c9d2e7b4f6a0c1d5e9f2a3b4c5d6e7f8091a2b3c4d5e6f7081920a3b4c');
define('ENCRYPTION_METHOD', 'aes-256-cbc');

define('MAX_UPLOAD_SIZE', 25 * 1024 * 1024);

define('ALLOWED_EXTENSIONS', ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'doc', 'docx',
    'xls', 'xlsx', 'ppt', 'pptx', 'txt', 'csv', 'zip']);


define('APP_NAME', 'Secure File Vault');


function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrf_verify(?string $token): bool
{
    return isset($_SESSION['csrf_token']) && $token !== null
        && hash_equals($_SESSION['csrf_token'], $token);
}

function csrf_field(): string
{
    return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars(csrf_token()) . '">';
}

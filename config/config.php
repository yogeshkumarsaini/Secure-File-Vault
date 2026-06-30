<?php

define('APP_NAME', 'Secure File Vault');

define('BASE_URL', 'http://localhost/secure-file-vault/');

define('UPLOAD_DIR', __DIR__ . '/../uploads/encrypted/');
define('TEMP_DIR', __DIR__ . '/../uploads/temp/');

define('MAX_FILE_SIZE', 20 * 1024 * 1024);

define('CIPHER_METHOD', 'AES-256-CBC');

define('SECRET_KEY', 'MyVeryStrongSecretEncryptionKey@123456789');


$allowedExtensions = [
    'pdf',
    'doc',
    'docx',
    'jpg',
    'jpeg',
    'png',
    'gif',
    'zip',
    'txt'
];

date_default_timezone_set('Asia/Kolkata');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
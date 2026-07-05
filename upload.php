<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/encrypt.php';

require_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('dashboard.php');
}

if (!csrf_verify($_POST['csrf_token'] ?? null)) {
    set_flash('error', 'Invalid form submission. Please try again.');
    redirect('dashboard.php');
}

if (!isset($_FILES['uploaded_file']) || $_FILES['uploaded_file']['error'] === UPLOAD_ERR_NO_FILE) {
    set_flash('error', 'Please choose a file to upload.');
    redirect('dashboard.php');
}

$file = $_FILES['uploaded_file'];

// ---- Basic upload error handling ----
if ($file['error'] !== UPLOAD_ERR_OK) {
    $messages = [
        UPLOAD_ERR_INI_SIZE   => 'The file exceeds the server upload limit.',
        UPLOAD_ERR_FORM_SIZE  => 'The file exceeds the form upload limit.',
        UPLOAD_ERR_PARTIAL    => 'The file was only partially uploaded.',
        UPLOAD_ERR_NO_TMP_DIR => 'Server misconfiguration: missing temp folder.',
        UPLOAD_ERR_CANT_WRITE => 'Server failed to write the file to disk.',
        UPLOAD_ERR_EXTENSION  => 'A server extension stopped the upload.',
    ];
    set_flash('error', $messages[$file['error']] ?? 'File upload failed.');
    redirect('dashboard.php');
}

// ---- Size check ----
if ($file['size'] > MAX_UPLOAD_SIZE) {
    set_flash('error', 'File is too large. Max size is ' . format_bytes(MAX_UPLOAD_SIZE) . '.');
    redirect('dashboard.php');
}

// ---- Extension whitelist check ----
$originalName = $file['name'];
$extension = get_extension($originalName);

if (!empty(ALLOWED_EXTENSIONS) && !in_array($extension, ALLOWED_EXTENSIONS, true)) {
    set_flash('error', 'File type ".' . $extension . '" is not allowed.');
    redirect('dashboard.php');
}

// ---- Verify it's a genuine uploaded file (not a forged request) ----
if (!is_uploaded_file($file['tmp_name'])) {
    set_flash('error', 'Invalid upload.');
    redirect('dashboard.php');
}

// ---- Detect real MIME type server-side (don't trust the browser) ----
$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mimeType = finfo_file($finfo, $file['tmp_name']) ?: 'application/octet-stream';
finfo_close($finfo);

// ---- Encrypt and store ----
try {
    if (!is_dir(UPLOAD_DIR) && !mkdir(UPLOAD_DIR, 0750, true) && !is_dir(UPLOAD_DIR)) {
        throw new RuntimeException('Unable to create upload directory.');
    }

    $storedName = random_token(16) . '.enc';
    $destPath = UPLOAD_DIR . $storedName;

    $ivBase64 = encrypt_file($file['tmp_name'], $destPath);

    $stmt = get_db()->prepare(
        'INSERT INTO files (user_id, original_name, stored_name, iv, filesize, mime_type, uploaded_at)
         VALUES (?, ?, ?, ?, ?, ?, NOW())'
    );
    $stmt->execute([
        current_user_id(),
        $originalName,
        $storedName,
        $ivBase64,
        $file['size'],
        $mimeType,
    ]);

    set_flash('success', 'File uploaded and encrypted successfully.');
} catch (Throwable $e) {
    error_log('Upload failed: ' . $e->getMessage());
    if (isset($destPath) && file_exists($destPath)) {
        unlink($destPath);
    }
    set_flash('error', 'Something went wrong while encrypting your file. Please try again.');
}

redirect('dashboard.php');

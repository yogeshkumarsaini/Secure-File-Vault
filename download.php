<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/decrypt.php';

require_login();

$fileId = (int)($_GET['id'] ?? 0);

if ($fileId <= 0) {
    set_flash('error', 'Invalid file requested.');
    redirect('dashboard.php');
}

// IMPORTANT: scope the query to the logged-in user so nobody can download
// another user's file just by guessing/incrementing the id.
$stmt = get_db()->prepare(
    'SELECT id, original_name, stored_name, iv, mime_type
     FROM files WHERE id = ? AND user_id = ?'
);
$stmt->execute([$fileId, current_user_id()]);
$file = $stmt->fetch();

if (!$file) {
    set_flash('error', 'File not found or access denied.');
    redirect('dashboard.php');
}

$encryptedPath = UPLOAD_DIR . $file['stored_name'];

if (!file_exists($encryptedPath)) {
    set_flash('error', 'The encrypted file is missing from storage.');
    redirect('dashboard.php');
}

try {
    // Clear any buffered output before sending binary data.
    while (ob_get_level() > 0) {
        ob_end_clean();
    }
    stream_decrypted_file($encryptedPath, $file['iv'], $file['original_name'], $file['mime_type']);
    exit;
} catch (Throwable $e) {
    error_log('Download/decryption failed: ' . $e->getMessage());
    set_flash('error', 'Unable to decrypt this file.');
    redirect('dashboard.php');
}

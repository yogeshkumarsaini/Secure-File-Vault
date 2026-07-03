<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/auth.php';

require_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('dashboard.php');
}

if (!csrf_verify($_POST['csrf_token'] ?? null)) {
    set_flash('error', 'Invalid form submission. Please try again.');
    redirect('dashboard.php');
}

$fileId = (int)($_POST['file_id'] ?? 0);

$db = get_db();
$stmt = $db->prepare('SELECT id, stored_name FROM files WHERE id = ? AND user_id = ?');
$stmt->execute([$fileId, current_user_id()]);
$file = $stmt->fetch();

if (!$file) {
    set_flash('error', 'File not found or access denied.');
    redirect('dashboard.php');
}

$path = UPLOAD_DIR . $file['stored_name'];

$db->beginTransaction();
try {
    $del = $db->prepare('DELETE FROM files WHERE id = ? AND user_id = ?');
    $del->execute([$fileId, current_user_id()]);

    if (file_exists($path) && !unlink($path)) {
        throw new RuntimeException('Failed to remove encrypted file from disk.');
    }

    $db->commit();
    set_flash('success', 'File deleted.');
} catch (Throwable $e) {
    $db->rollBack();
    error_log('Delete failed: ' . $e->getMessage());
    set_flash('error', 'Something went wrong while deleting the file.');
}

redirect('dashboard.php');

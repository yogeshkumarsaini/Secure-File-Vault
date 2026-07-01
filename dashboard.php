<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/auth.php';

require_login();

$userId = current_user_id();

$stmt = get_db()->prepare(
    'SELECT id, original_name, filesize, mime_type, uploaded_at
     FROM files WHERE user_id = ? ORDER BY uploaded_at DESC'
);
$stmt->execute([$userId]);
$files = $stmt->fetchAll();

require __DIR__ . '/includes/header.php';
?>
<h1>My Files</h1>

<section class="upload-panel">
    <h2>Upload a New File</h2>
    <form method="post" action="upload.php" enctype="multipart/form-data">
        <?= csrf_field() ?>
        <input type="file" name="uploaded_file" required>
        <button type="submit" class="btn btn-primary">Encrypt &amp; Upload</button>
    </form>
    <small>
        Max size: <?= e(format_bytes(MAX_UPLOAD_SIZE)) ?>.
        Allowed types: <?= e(implode(', ', ALLOWED_EXTENSIONS)) ?>.
    </small>
</section>

<section class="file-list">
    <h2>Your Encrypted Files (<?= count($files) ?>)</h2>

    <?php if (empty($files)): ?>
        <p>You haven't uploaded any files yet.</p>
    <?php else: ?>
        <table>
            <thead>
            <tr>
                <th>File Name</th>
                <th>Size</th>
                <th>Type</th>
                <th>Uploaded</th>
                <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($files as $file): ?>
                <tr>
                    <td><?= e($file['original_name']) ?></td>
                    <td><?= e(format_bytes((int)$file['filesize'])) ?></td>
                    <td><?= e($file['mime_type']) ?></td>
                    <td><?= e($file['uploaded_at']) ?></td>
                    <td class="actions">
                        <a class="btn btn-small" href="download.php?id=<?= (int)$file['id'] ?>">Download</a>
                        <form method="post" action="delete.php" class="inline-form"
                              onsubmit="return confirm('Delete this file permanently?');">
                            <?= csrf_field() ?>
                            <input type="hidden" name="file_id" value="<?= (int)$file['id'] ?>">
                            <button type="submit" class="btn btn-small btn-danger">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>

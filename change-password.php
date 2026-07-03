<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/auth.php';

require_login();

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_verify($_POST['csrf_token'] ?? null)) {
        $errors[] = 'Invalid form submission. Please try again.';
    } else {
        $currentPassword = (string)($_POST['current_password'] ?? '');
        $newPassword      = (string)($_POST['new_password'] ?? '');
        $confirmPassword  = (string)($_POST['confirm_password'] ?? '');

        $stmt = get_db()->prepare('SELECT password_hash FROM users WHERE id = ?');
        $stmt->execute([current_user_id()]);
        $row = $stmt->fetch();

        if (!$row || !password_verify($currentPassword, $row['password_hash'])) {
            $errors[] = 'Current password is incorrect.';
        }

        if ($newPassword !== $confirmPassword) {
            $errors[] = 'New passwords do not match.';
        }

        $pwError = validate_password_strength($newPassword);
        if ($pwError !== null) {
            $errors[] = $pwError;
        }

        if (empty($errors)) {
            $newHash = password_hash($newPassword, PASSWORD_DEFAULT);
            $update = get_db()->prepare('UPDATE users SET password_hash = ? WHERE id = ?');
            $update->execute([$newHash, current_user_id()]);
            $success = true;
            set_flash('success', 'Your password has been updated.');
            redirect('profile.php');
        }
    }
}

require __DIR__ . '/includes/header.php';
?>
<div class="auth-card">
    <h1>Change Password</h1>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-error">
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?= e($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="post" action="change-password.php" novalidate>
        <?= csrf_field() ?>

        <label for="current_password">Current Password</label>
        <input type="password" id="current_password" name="current_password" required>

        <label for="new_password">New Password</label>
        <input type="password" id="new_password" name="new_password" required>
        <small>At least 10 characters, with uppercase, lowercase, and a number.</small>

        <label for="confirm_password">Confirm New Password</label>
        <input type="password" id="confirm_password" name="confirm_password" required>

        <button type="submit" class="btn btn-primary">Update Password</button>
    </form>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>

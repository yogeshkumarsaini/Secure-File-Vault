<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/auth.php';

require_login();

$user = get_current_user_row();

if (!$user) {
    logout_user();
    redirect('login.php');
}

$stmt = get_db()->prepare('SELECT COUNT(*) AS total, COALESCE(SUM(filesize), 0) AS total_size FROM files WHERE user_id = ?');
$stmt->execute([$user['id']]);
$stats = $stmt->fetch();

require __DIR__ . '/includes/header.php';
?>
<h1>My Profile</h1>

<section class="profile-card">
    <table class="profile-table">
        <tr><th>Username</th><td><?= e($user['username']) ?></td></tr>
        <tr><th>Email</th><td><?= e($user['email']) ?></td></tr>
        <tr><th>Member Since</th><td><?= e($user['created_at']) ?></td></tr>
        <tr><th>Files Stored</th><td><?= (int)$stats['total'] ?></td></tr>
        <tr><th>Total Storage Used</th><td><?= e(format_bytes((int)$stats['total_size'])) ?></td></tr>
    </table>

    <p>
        <a class="btn" href="change-password.php">Change Password</a>
        <a class="btn" href="dashboard.php">Back to Dashboard</a>
    </p>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>

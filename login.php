<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/auth.php';

if (is_logged_in()) {
    redirect('dashboard.php');
}

$errors = [];
$username = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (too_many_attempts('login', 8, 300)) {
        $errors[] = 'Too many login attempts. Please wait a few minutes and try again.';
    } elseif (!csrf_verify($_POST['csrf_token'] ?? null)) {
        $errors[] = 'Invalid form submission. Please try again.';
    } else {
        $username = clean_input($_POST['username'] ?? '');
        $password = (string)($_POST['password'] ?? '');

        if ($username === '' || $password === '') {
            $errors[] = 'Please enter both username/email and password.';
        } else {
            $stmt = get_db()->prepare(
                'SELECT id, username, password_hash FROM users WHERE username = ? OR email = ?'
            );
            $stmt->execute([$username, $username]);
            $user = $stmt->fetch();

            // Constant-time-ish check: always run password_verify even if user not found,
            // using a dummy hash, to reduce timing differences that leak user existence.
            $hashToCheck = $user['password_hash'] ?? '$2y$10$usedOnlyToEqualizeTimingXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX';
            $valid = password_verify($password, $hashToCheck);

            if ($user && $valid) {
                login_user($user);
                set_flash('success', 'Welcome back, ' . $user['username'] . '!');
                redirect('dashboard.php');
            } else {
                $errors[] = 'Invalid username/email or password.';
            }
        }
    }
}

require __DIR__ . '/includes/header.php';
?>
<div class="auth-card">
    <h1>Log In</h1>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-error">
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?= e($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="post" action="login.php" novalidate>
        <?= csrf_field() ?>

        <label for="username">Username or Email</label>
        <input type="text" id="username" name="username" value="<?= e($username) ?>" required autofocus>

        <label for="password">Password</label>
        <input type="password" id="password" name="password" required>

        <button type="submit" class="btn btn-primary">Log In</button>
    </form>

    <p>Don't have an account? <a href="register.php">Register</a></p>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>

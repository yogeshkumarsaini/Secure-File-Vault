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
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_verify($_POST['csrf_token'] ?? null)) {
        $errors[] = 'Invalid form submission. Please try again.';
    } else {
        $username = clean_input($_POST['username'] ?? '');
        $email    = clean_input($_POST['email'] ?? '');
        $password = (string)($_POST['password'] ?? '');
        $confirm  = (string)($_POST['confirm_password'] ?? '');

        if ($username === '' || $email === '' || $password === '') {
            $errors[] = 'All fields are required.';
        }
        if ($username !== '' && !preg_match('/^[A-Za-z0-9_]{3,30}$/', $username)) {
            $errors[] = 'Username must be 3-30 characters: letters, numbers, underscore only.';
        }
        if ($email !== '' && !is_valid_email($email)) {
            $errors[] = 'Please enter a valid email address.';
        }
        if ($password !== $confirm) {
            $errors[] = 'Passwords do not match.';
        }
        $pwError = validate_password_strength($password);
        if ($pwError !== null) {
            $errors[] = $pwError;
        }

        if (empty($errors)) {
            $db = get_db();

            $stmt = $db->prepare('SELECT id FROM users WHERE username = ? OR email = ?');
            $stmt->execute([$username, $email]);
            if ($stmt->fetch()) {
                $errors[] = 'That username or email is already registered.';
            } else {
                $hash = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $db->prepare(
                    'INSERT INTO users (username, email, password_hash, created_at) VALUES (?, ?, ?, NOW())'
                );
                $stmt->execute([$username, $email, $hash]);

                set_flash('success', 'Account created successfully. Please log in.');
                redirect('login.php');
            }
        }
    }
}

require __DIR__ . '/includes/header.php';
?>
<div class="auth-card">
    <h1>Create an Account</h1>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-error">
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?= e($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="post" action="register.php" novalidate>
        <?= csrf_field() ?>

        <label for="username">Username</label>
        <input type="text" id="username" name="username" value="<?= e($username) ?>" required>

        <label for="email">Email</label>
        <input type="email" id="email" name="email" value="<?= e($email) ?>" required>

        <label for="password">Password</label>
        <input type="password" id="password" name="password" required>
        <small>At least 10 characters, with uppercase, lowercase, and a number.</small>

        <label for="confirm_password">Confirm Password</label>
        <input type="password" id="confirm_password" name="confirm_password" required>

        <button type="submit" class="btn btn-primary">Register</button>
    </form>

    <p>Already have an account? <a href="login.php">Log in</a></p>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>

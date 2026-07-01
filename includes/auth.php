<?php

function login_user(array $user): void
{
    session_regenerate_id(true);
    $_SESSION['user_id']  = $user['id'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['logged_in_at'] = time();
}

function logout_user(): void
{
    $_SESSION = [];

    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params['path'], $params['domain'],
            $params['secure'], $params['httponly']);
    }

    session_destroy();
}

function is_logged_in(): bool
{
    return !empty($_SESSION['user_id']);
}

function current_user_id(): ?int
{
    return $_SESSION['user_id'] ?? null;
}

function require_login(): void
{
    if (!is_logged_in()) {
        set_flash('error', 'Please log in to continue.');
        redirect('login.php');
    }

    $timeout = 1800;
    if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity']) > $timeout) {
        logout_user();
        set_flash('error', 'Your session expired. Please log in again.');
        redirect('login.php');
    }
    $_SESSION['last_activity'] = time();
}

function get_current_user_row(): ?array
{
    $id = current_user_id();
    if ($id === null) {
        return null;
    }

    $stmt = get_db()->prepare('SELECT id, username, email, created_at FROM users WHERE id = ?');
    $stmt->execute([$id]);
    $user = $stmt->fetch();
    return $user ?: null;
}

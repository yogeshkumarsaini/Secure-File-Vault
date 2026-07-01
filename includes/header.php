<?php

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e(APP_NAME) ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<header class="site-header">
    <div class="container header-inner">
        <a class="brand" href="<?= is_logged_in() ? 'dashboard.php' : 'login.php' ?>">🔒 <?= e(APP_NAME) ?></a>
        <nav class="main-nav">
            <?php if (is_logged_in()): ?>
                <a href="dashboard.php">Dashboard</a>
                <a href="profile.php">Profile</a>
                <a href="change-password.php">Change Password</a>
                <a href="logout.php">Logout</a>
            <?php else: ?>
                <a href="login.php">Login</a>
                <a href="register.php">Register</a>
            <?php endif; ?>
        </nav>
    </div>
</header>
<main class="container">
    <?php foreach (get_flashes() as $flash): ?>
        <div class="alert alert-<?= e($flash['type']) ?>"><?= e($flash['message']) ?></div>
    <?php endforeach; ?>

<?php
require 'functions.php';

if (isLoggedIn()) {
    header('Location: index.php');
    exit;
}

$error = $success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (!$username || !$password) {
        $error = 'Username and password are required.';
    } elseif (strlen($username) < 3) {
        $error = 'Username must be at least 3 characters long.';
    } elseif (strlen($password) < 3) {
        $error = 'Password must be at least 3 characters long.';
    } else {
        $db = getDBConnection();

        $stmt = $db->prepare('SELECT id FROM users WHERE username = ?');
        $stmt->bind_param('s', $username);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $error = 'Username not available.';
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);

            $stmt = $db->prepare(
                'INSERT INTO users (username, password_hash, email) VALUES (?, ?, ?)'
            );
            $stmt->bind_param('sss', $username, $hash, $email);

            if ($stmt->execute()) {
                $success = 'Registration successful! You can now login.';
            } else {
                $error = 'Registration failed. Try again.';
            }
        }
    }
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <div class="auth-container">
        <h1>Register for Perpus</h1>

        <?php if ($error): ?>
            <div class="message error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="message success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>

        <form method="post">
            <div class="form-group">
                <label>Username *</label>
                <input type="text" name="username" required autofocus>
            </div>

            <div class="form-group">
                <label>Email (optional)</label>
                <input type="email" name="email">
            </div>

            <div class="form-group">
                <label>Password *</label>
                <input type="password" name="password" required>
            </div>

            <button class="btn btn-full">Register</button>
        </form>

        <div class="auth-link">
            Already have an account? <a href="login.php">Login here</a>
        </div>

        <div class="auth-link">
            <a href="index.php">Back to Home</a>
        </div>
    </div>
</body>

</html>
<?php
session_start();
require 'config/db.php';
require 'functions/helpers.php'; 

$username = '';
$password = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = isset($_POST['username']) ? sanitizeInput($_POST['username']) : '';
    $password = isset($_POST['password']) ? sanitizeInput($_POST['password']) : '';

    if (filter_var($username, FILTER_VALIDATE_EMAIL)) {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$username]);
    } else {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
    }

    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        header("Location: index.php");
        exit;
    } else {
        $error = "Username/email atau password salah.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .login-container {
            max-width: 400px;
            margin: 100px auto; 
            padding: 20px;
            background-color: white; 
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1); 
        }
    </style>
</head>
<body>

<div class="login-container">
    <h2 class="text-center">Login</h2>
    <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <form method="POST">
        <div class="form-group">
            <label for="username">Email atau Username:</label>
            <input type="text" class="form-control" id="username" name="username" required>
        </div>
        <div class="form-group">
            <label for="password">Password:</label>
            <input type="password" class="form-control" id="password" name="password" required>
        </div>
        <button type="submit" class="btn btn-primary btn-block">Login</button>
        <p class="text-center mt-3">Belum punya akun? <a href="register.php">Daftar di sini</a></p>
        <p class="text-center mt-3"><a href="forgot_password.php">Lupa Password?</a></p> <!-- Link ke halaman forgot password -->
    </form>
</div>


<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>

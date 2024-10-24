<?php
session_start();
require 'config/db.php'; 
require 'functions/helpers.php'; 

$username = '';
$email = '';
$password = '';
$confirm_password = '';
$phone = '';
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = sanitizeInput($_POST['username']);
    $email = sanitizeInput($_POST['email']);
    $phone = sanitizeInput($_POST['phone']);
    $password = sanitizeInput($_POST['password']);
    $confirm_password = sanitizeInput($_POST['confirm_password']);

    if ($password !== $confirm_password) {
        $error = "Password tidak sama.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) { 
        $error = "Format email tidak valid.";
    } elseif (!preg_match('/^[0-9]{10,15}$/', $phone)) { 
        $error = "Nomor telepon tidak valid.";
    } else {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? OR email = ? OR phone = ?");
        $stmt->execute([$username, $email, $phone]);
        if ($stmt->rowCount() > 0) {
            $error = "Username, email, atau nomor telepon sudah terdaftar.";
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (username, email, phone, password) VALUES (?, ?, ?, ?)");
            $stmt->execute([$username, $email, $phone, $hashed_password]);
            $success = "Registrasi berhasil. Anda dapat login sekarang.";
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
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa; 
        }
        .register-container {
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

<div class="register-container">
    <h2 class="text-center">Daftar Akun</h2>
    <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <?php if (!empty($success)): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>
    <form method="POST">
        <div class="form-group">
            <label for="username">Username:</label>
            <input type="text" class="form-control" id="username" name="username" required>
        </div>
        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" class="form-control" id="email" name="email" required>
        </div>
        <div class="form-group">
            <label for="phone">Nomor Telepon:</label>
            <input type="text" class="form-control" id="phone" name="phone" required 
                   pattern="\d{0,14}" maxlength="14">
        </div>
        <div class="form-group">
            <label for="password">Password:</label>
            <input type="password" class="form-control" id="password" name="password" required>
        </div>
        <div class="form-group">
            <label for="confirm_password">Konfirmasi Password:</label>
            <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
        </div>
        <button type="submit" class="btn btn-primary btn-block">Daftar</button>
        <p class="text-center mt-3">Sudah punya akun? <a href="login.php">Login di sini</a></p>
    </form>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>

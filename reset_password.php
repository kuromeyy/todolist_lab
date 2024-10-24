<?php
session_start();
require 'config/db.php';
require 'functions/helpers.php';

if (!isset($_SESSION['reset_user_id'])) {
    header("Location: forgot_password.php");
    exit;
}

$success = '';
$error = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $new_password = sanitizeInput($_POST['new_password']);
    $confirm_password = sanitizeInput($_POST['confirm_password']);

    if ($new_password !== $confirm_password) {
        $error = "Password baru tidak sama.";
    } else {
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
        $stmt->execute([$hashed_password, $_SESSION['reset_user_id']]);

        unset($_SESSION['reset_user_id']);
        header("Location: login.php?reset=success");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lupa Password - Langkah 3</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2>Lupa Password - Langkah 3</h2>
    <p>Masukkan password baru Anda.</p>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="form-group">
            <label for="new_password">Password Baru:</label>
            <input type="password" class="form-control" id="new_password" name="new_password" required>
        </div>
        <div class="form-group">
            <label for="confirm_password">Konfirmasi Password Baru:</label>
            <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
        </div>
        <button type="submit" class="btn btn-primary">Reset Password</button>
    </form>
</div>
</body>
</html>

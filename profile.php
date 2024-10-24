<?php
session_start();
require 'config/db.php'; 
require 'functions/helpers.php'; 

if (!isLoggedIn()) {
    header("Location: login.php");
    exit;
}
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update'])) {
    $username = sanitizeInput($_POST['username']);
    $email = sanitizeInput($_POST['email']);
    $current_password = sanitizeInput($_POST['current_password']);
    $new_password = sanitizeInput($_POST['new_password']);
    $confirm_password = sanitizeInput($_POST['confirm_password']);

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Format email tidak valid.";
    } elseif (!password_verify($current_password, $user['password'])) {
        $error = "Password saat ini tidak valid.";
    } elseif ($new_password !== $confirm_password) {
        $error = "Password baru tidak sama.";
    } else {
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE users SET username = ?, email = ?, password = ? WHERE id = ?");
        $stmt->execute([$username, $email, $hashed_password, $_SESSION['user_id']]);
        $success = "Profil berhasil diperbarui.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Pengguna</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<?php require 'navbar.php'; ?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-header bg-primary text-white text-center">
                    <h4 class="card-title">Profil Pengguna</h4>
                </div>
                <div class="card-body">
                    <?php if (!empty($error)): ?>
                        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                    <?php endif; ?>
                    <?php if (!empty($success)): ?>
                        <div class="alert alert-success" id="successMessage"><?= htmlspecialchars($success) ?></div>
                    <?php endif; ?>

                    <form method="POST" id="profileForm">
                        <div class="form-group">
                            <label for="username">Username</label>
                            <input type="text" class="form-control" id="username" name="username" value="<?= htmlspecialchars($user['username']) ?>" readonly>
                        </div>
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" readonly>
                        </div>
                        <div class="form-group hidden" id="passwordFields">
                            <label for="current_password">Password Saat Ini</label>
                            <input type="password" class="form-control" id="current_password" name="current_password">
                        </div>
                        <div class="form-group hidden" id="newPasswordFields">
                            <label for="new_password">Password Baru</label>
                            <input type="password" class="form-control" id="new_password" name="new_password">
                        </div>
                        <div class="form-group hidden">
                            <label for="confirm_password">Konfirmasi Password Baru</label>
                            <input type="password" class="form-control" id="confirm_password" name="confirm_password">
                        </div>
                        <div class="d-flex justify-content-between">
                            <button type="button" class="btn btn-info" id="editButton" onclick="toggleEdit()">Edit Username/Gmail</button>
                            <button type="submit" name="update" class="btn btn-primary hidden" id="updateButton">Perbarui</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script>
    function toggleEdit() {
        var usernameField = document.getElementById('username');
        var emailField = document.getElementById('email');
        var passwordFields = document.getElementById('passwordFields');
        var newPasswordFields = document.getElementById('newPasswordFields');
        var updateButton = document.getElementById('updateButton');
        var backButton = document.getElementById('backButton');
        var editButton = document.getElementById('editButton'); 

        usernameField.readOnly = !usernameField.readOnly;
        emailField.readOnly = !emailField.readOnly;

        if (usernameField.readOnly) {
            passwordFields.classList.add('hidden');
            newPasswordFields.classList.add('hidden');
            updateButton.classList.add('hidden');
            backButton.classList.add('hidden');
            editButton.classList.remove('hidden');
        } else {
            passwordFields.classList.remove('hidden');
            newPasswordFields.classList.remove('hidden');
            updateButton.classList.remove('hidden');
            backButton.classList.remove('hidden');
            editButton.classList.add('hidden'); 
        }
    }

    function goBack() {
        window.location.href = 'profile.php'; 
    }

    if (document.getElementById('successMessage')) {
        setTimeout(function() {
            document.getElementById('successMessage').style.display = 'none';
        }, 3000);
    }
</script>
</body>
</html>

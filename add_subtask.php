<?php
session_start();
require 'config/db.php'; 
require 'functions/helpers.php';

if (!isLoggedIn()) {
    header("Location: login.php");
    exit;
}

if (!isset($_GET['task_id'])) {
    header("Location: index.php");
    exit;
}

$task_id = $_GET['task_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $sub_task = trim($_POST['sub_task']);

    if (empty($sub_task)) {
        $error = "Subtask title is required.";
    } else {
        $stmt = $pdo->prepare("INSERT INTO sub_tasks (task_id, sub_task, status) VALUES (?, ?, 'Pending')");
        if ($stmt->execute([$task_id, $sub_task])) {
            header("Location: index.php");
            exit;
        } else {
            $error = "Failed to add subtask. Please try again.";
        }
    }
}

$stmt = $pdo->prepare("SELECT * FROM tasks WHERE id = ? AND user_id = ?");
$stmt->execute([$task_id, $_SESSION['user_id']]);
$task = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$task) {
    header("Location: index.php");
    exit;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Subtask</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<?php require 'navbar.php'; ?>

<div class="container mt-4">
    <h1>Add Subtask to Task: <?= htmlspecialchars($task['title']) ?></h1>

    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>

    <form action="add_subtask.php?task_id=<?= $task_id ?>" method="POST">
        <div class="form-group">
            <label for="sub_task">Subtask Title</label>
            <input type="text" class="form-control" id="sub_task" name="sub_task" required>
        </div>
        <button type="submit" class="btn btn-primary">Add Subtask</button>
        <a href="index.php" class="btn btn-secondary">Back to List</a>
    </form>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>

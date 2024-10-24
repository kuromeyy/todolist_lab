<?php
session_start();
require 'config/db.php'; 
require 'functions/helpers.php';

if (!isLoggedIn()) {
    header("Location: login.php");
    exit;
}
if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$subtask_id = $_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM sub_tasks WHERE id = ?");
$stmt->execute([$subtask_id]);
$subTask = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$subTask) {
    header("Location: index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $sub_task_title = trim($_POST['sub_task']);
    
    if (empty($sub_task_title)) {
        $error = "Subtask title is required.";
    } else {
        $stmt = $pdo->prepare("UPDATE sub_tasks SET sub_task = ? WHERE id = ?");
        if ($stmt->execute([$sub_task_title, $subtask_id])) {
            header("Location: index.php");
            exit;
        } else {
            $error = "Failed to update subtask. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Subtask</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<?php require 'navbar.php'; ?>

<div class="container mt-4">
    <h1>Edit Subtask: <?= htmlspecialchars($subTask['sub_task']) ?></h1>

    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form action="edit_subtask.php?id=<?= $subtask_id ?>" method="POST">
        <div class="form-group">
            <label for="sub_task">Subtask Title</label>
            <input type="text" class="form-control" id="sub_task" name="sub_task" value="<?= htmlspecialchars($subTask['sub_task']) ?>" required>
        </div>
        <button type="submit" class="btn btn-primary">Update Subtask</button>
        <a href="index.php" class="btn btn-secondary">Back to List</a>
    </form>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>

<?php
session_start();
require 'config/db.php'; 
require 'functions/helpers.php'; 

if (!isLoggedIn()) {
    header("Location: login.php");
    exit;
}

$taskId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$title = '';
$subtasks = [];
$error = '';

if ($taskId) {
    // Fetch the main task (if necessary)
    $stmt = $pdo->prepare("SELECT * FROM tasks WHERE id = ? AND user_id = ?");
    $stmt->execute([$taskId, $_SESSION['user_id']]);
    $task = $stmt->fetch();

    if ($task) {
        $title = $task['title'];

        // Fetch subtasks related to the main task
        $stmt = $pdo->prepare("SELECT * FROM sub_tasks WHERE task_id = ?");
        $stmt->execute([$taskId]);
        $subtasks = $stmt->fetchAll();
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = sanitizeInput($_POST['task']);
    // Update the main task (if necessary)
    $stmt = $pdo->prepare("UPDATE tasks SET title = ? WHERE id = ? AND user_id = ?");
    $stmt->execute([$title, $taskId, $_SESSION['user_id']]);

    // Update or create subtasks
    if (isset($_POST['subtasks'])) {
        foreach ($_POST['subtasks'] as $subtaskId => $subtaskData) {
            $subtaskTitle = sanitizeInput($subtaskData['title']);
            $subtaskStatus = sanitizeInput($subtaskData['status']);
            
            if ($subtaskId == 'new') {
                // Insert new subtask
                $stmt = $pdo->prepare("INSERT INTO sub_tasks (task_id, sub_task, status) VALUES (?, ?, ?)");
                $stmt->execute([$taskId, $subtaskTitle, $subtaskStatus]);
            } else {
                // Update existing subtask
                $stmt = $pdo->prepare("UPDATE sub_tasks SET sub_task = ?, status = ? WHERE id = ? AND task_id = ?");
                $stmt->execute([$subtaskTitle, $subtaskStatus, $subtaskId, $taskId]);
            }
        }
    }

    header("Location: index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Task</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .container {
            margin-top: 50px;
            border-radius: 8px;
            background-color: white;
            padding: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        h2 {
            margin-bottom: 20px;
        }
        .subtask {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 10px;
        }
        .subtask input {
            flex-grow: 1;
            margin-right: 10px;
        }
    </style>
</head>
<body>

<?php require 'navbar.php'; ?>

<div class="container">
    <h2 class="text-center">Edit Task</h2>
    <form method="POST">
        <div class="form-group">
            <label for="task">Judul Task:</label>
            <input type="text" class="form-control" id="task" name="task" value="<?= htmlspecialchars($title) ?>" required>
        </div>

        <h4>Subtasks</h4>
        <?php foreach ($subtasks as $subtask): ?>
            <div class="subtask">
                <input type="text" class="form-control" name="subtasks[<?= $subtask['id'] ?>][title]" value="<?= htmlspecialchars($subtask['sub_task']) ?>" required>
                <select name="subtasks[<?= $subtask['id'] ?>][status]" class="form-control">
                    <option value="pending" <?= $subtask['status'] == 'pending' ? 'selected' : '' ?>>Pending</option>
                    <option value="completed" <?= $subtask['status'] == 'completed' ? 'selected' : '' ?>>Completed</option>
                </select>
                <button type="button" class="btn btn-danger btn-sm" onclick="removeSubtask(this)">Remove</button>
            </div>
        <?php endforeach; ?>

        <button type="button" class="btn btn-secondary btn-sm mb-3" id="addSubtaskBtn">Add Subtask</button>

        <button type="submit" class="btn btn-primary btn-block">Update Task</button>
        <a href="index.php" class="btn btn-secondary btn-block mt-2">Cancel</a>
    </form>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script>
    document.getElementById('addSubtaskBtn').addEventListener('click', function() {
        const subtaskDiv = document.createElement('div');
        subtaskDiv.className = 'subtask';
        subtaskDiv.innerHTML = `
            <input type="text" class="form-control" name="subtasks[new][title]" placeholder="New subtask" required>
            <select name="subtasks[new][status]" class="form-control">
                <option value="pending">Pending</option>
                <option value="completed">Completed</option>
            </select>
            <button type="button" class="btn btn-danger btn-sm" onclick="removeSubtask(this)">Remove</button>
        `;
        document.querySelector('form').insertBefore(subtaskDiv, this);
    });

    function removeSubtask(button) {
        button.parentElement.remove();
    }
</script>
</body>
</html>

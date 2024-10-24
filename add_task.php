<?php
session_start();
require 'config/db.php'; 
require 'functions/helpers.php'; 

if (!isLoggedIn()) {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $task_title = trim($_POST['task']);
    $category = $_POST['category'];
    $sub_tasks = $_POST['sub_tasks'] ?? [];

    if (!empty($task_title)) {
        try {
            // Start a transaction
            $pdo->beginTransaction();

            // Insert the main task
            $stmt = $pdo->prepare("INSERT INTO tasks (user_id, title, status_task, category) VALUES (?, ?, 'pending', ?)");
            $stmt->execute([$_SESSION['user_id'], $task_title, $category]);
            $task_id = $pdo->lastInsertId();

            // Insert subtasks if any
            if (!empty($sub_tasks)) {
                $sub_task_stmt = $pdo->prepare("INSERT INTO sub_tasks (task_id, sub_task, status) VALUES (?, ?, 'pending')");
                foreach ($sub_tasks as $sub_task) {
                    if (!empty(trim($sub_task))) {
                        $sub_task_stmt->execute([$task_id, trim($sub_task)]);
                    }
                }
            }

            // Commit the transaction
            $pdo->commit();

            // Redirect to index
            header("Location: index.php");
            exit;
        } catch (Exception $e) {
            $pdo->rollBack();
            $error = "Failed to add task and subtasks. Please try again.";
        }
    } else {
        $error = "Task title is required.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Task and Subtasks</title>
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<?php require 'navbar.php'; ?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h4 class="card-title">Add Task and Subtasks</h4>
                </div>
                <div class="card-body">
                    <?php if (isset($error)): ?>
                        <div class="alert alert-danger"><?= $error ?></div>
                    <?php endif; ?>

                    <form action="" method="POST">
                        <!-- Main Task -->
                        <div class="form-group mb-3">
                            <label for="task" class="form-label">Task Title</label>
                            <input type="text" class="form-control" id="task" name="task" placeholder="Enter task title" required>
                        </div>

                        <!-- Category -->
                        <div class="form-group mb-3">
                            <label for="category" class="form-label">Category</label>
                            <select name="category" class="form-select" required>
                                <option value="Work">Work</option>
                                <option value="Personal">Personal</option>
                                <option value="Groceries">Groceries</option>
                            </select>
                        </div>

                        <!-- Subtasks -->
                        <div class="subtask-wrapper">
                            <label class="form-label">Subtasks</label>
                            <div class="form-group mb-3 d-flex">
                                <input type="text" name="sub_tasks[]" class="form-control" placeholder="Enter subtask title">
                                <button type="button" class="btn btn-success ml-2 add-subtask">+</button>
                            </div>
                        </div>

                        <!-- Submit -->
                        <div class="d-flex justify-content-between">
                            <button type="submit" class="btn btn-primary">Add Task and Subtasks</button>
                            <a href="index.php" class="btn btn-secondary">Back to List</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>


<!-- JavaScript to handle dynamic subtasks -->
<script>
    document.querySelector('.add-subtask').addEventListener('click', function () {
        const subtaskWrapper = document.querySelector('.subtask-wrapper');
        const subtaskDiv = document.createElement('div');
        subtaskDiv.classList.add('form-group', 'mb-3', 'd-flex');
        subtaskDiv.innerHTML = `
            <input type="text" name="sub_tasks[]" class="form-control" placeholder="Enter subtask title">
            <button type="button" class="btn btn-danger ml-2 remove-subtask">-</button>
        `;
        subtaskWrapper.appendChild(subtaskDiv);
    });

    document.addEventListener('click', function (e) {
        if (e.target && e.target.classList.contains('remove-subtask')) {
            e.target.parentElement.remove();
        }
    });
</script>

<!-- Bootstrap JS -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>

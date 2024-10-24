<?php
session_start();
require 'config/db.php'; 
require 'functions/helpers.php'; 

if (!isLoggedIn()) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Get filter parameters from the URL
$categoryFilter = isset($_GET['category']) ? $_GET['category'] : 'all';
$statusFilter = isset($_GET['filter']) ? $_GET['filter'] : 'all';

// Fetch distinct categories
$categoriesStmt = $pdo->prepare("SELECT DISTINCT category FROM tasks WHERE user_id = ?");
$categoriesStmt->execute([$user_id]);
$categories = $categoriesStmt->fetchAll(PDO::FETCH_COLUMN);

// Define statuses
$statuses = ['pending', 'completed'];

// Fetch tasks based on filters
$sql = "SELECT * FROM tasks WHERE user_id = ?";
$params = [$user_id];

if ($categoryFilter !== 'all') {
    $sql .= " AND category = ?";
    $params[] = $categoryFilter;
}

if ($statusFilter !== 'all') {
    $sql .= " AND status_task = ?";
    $params[] = $statusFilter;
}

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$allTasks = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Organize tasks by category and status
$organizedTasks = [];
foreach ($allTasks as $task) {
    $organizedTasks[$task['category']][$task['status_task']][] = $task;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>To-Do List</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Main Layout */
        .main-container {
            display: flex;
            flex-wrap: wrap;
            position: relative;
        }

        /* Sidebar */
        .sidebar {
            background-color: #f8f9fa;
            padding: 20px;
            width: 250px;
            height: 100vh;
            position: sticky;
            top: 0;
            border-right: 1px solid #dee2e6;
        }

        .sidebar h3 {
            margin-bottom: 20px;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 18px;
        }

        .sidebar .list-group-item {
            border: none;
            margin-bottom: 5px;
        }

        /* Main Content */
        .content {
            flex: 1;
            padding: 20px;
        }

        .task-card {
            background-color: #fff;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            margin-bottom: 20px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .task-card .card-body {
            padding: 20px;
        }

        .task-card h5 {
            font-weight: bold;
        }

        .task-card .badge {
            font-size: 14px;
        }

        .task-card .subtasks ul {
            list-style-type: none;
            padding: 0;
        }

        .task-card .subtasks li {
            display: flex;
            align-items: center;
        }

        .task-actions {
            margin-top: 15px;
        }

        .task-actions a {
            margin-right: 10px;
        }

        /* Fixed filter bar */
        .filter-bar {
            position: sticky;
            top: 0;
            z-index: 1000;
            background-color: #f8f9fa;
            padding: 10px 20px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

@media (max-width: 768px) {
    .main-container {
        flex-direction: column;
    }

    .sidebar {
        display: none; /* Hide sidebar on mobile */
    }

    .top-bar {
        display: flex;
        flex-direction: column;
        background-color: #f8f9fa;
        padding: 10px 20px; /* Reduced padding */
        border-bottom: 1px solid #dee2e6;
    }

    .top-bar h3 {
        margin-bottom: 5px; /* Adjusted margin */
        font-weight: bold;
        text-transform: uppercase;
        font-size: 16px; /* Reduced font size */
    }

    .top-bar .list-group-item {
        border: none;
        margin-bottom: 3px; /* Reduced margin */
    }

    .content {
        padding: 1px;
    }

    .task-card {
        margin-bottom: 15px; /* Reduced margin */
        padding: 0px; /* Reduce padding inside cards */
    }

    .task-card .card-body {
        padding: 10px; /* Reduced padding */
    }

    .task-card h5 {
        font-size: 16px; /* Reduced font size */
    }
}

    </style>
</head>
<body>

<?php require 'navbar.php'; ?>

<div class="main-container">
    <!-- Sidebar (hidden on mobile) -->
    <div class="sidebar">
        <h3>Categories</h3>
        <ul class="list-group">
            <li><a href="?category=all&filter=pending" class="list-group-item list-group-item-action <?= $categoryFilter === 'all' ? 'active' : '' ?>">All Categories</a></li>
            <?php foreach ($categories as $category): ?>
                <li><a href="?category=<?= htmlspecialchars($category) ?>&filter=<?= $statusFilter ?>" class="list-group-item list-group-item-action <?= $categoryFilter === $category ? 'active' : '' ?>"><?= htmlspecialchars($category) ?></a></li>
            <?php endforeach; ?>
        </ul>

        <h3 class="mt-4">Filters</h3>
        <ul class="list-group">
            <li><a href="?category=<?= $categoryFilter ?>&filter=all" class="list-group-item list-group-item-action <?= $statusFilter === 'all' ? 'active' : '' ?>">All Tasks</a></li>
            <?php foreach ($statuses as $status): ?>
                <li><a href="?category=<?= $categoryFilter ?>&filter=<?= $status ?>" class="list-group-item list-group-item-action <?= $statusFilter === $status ? 'active' : '' ?>"><?= ucfirst($status) ?> Tasks</a></li>
            <?php endforeach; ?>
        </ul>
    </div>

    <!-- Top Bar (for mobile view) -->
    <div class="top-bar d-md-none">
        <h3>Categories</h3>
        <ul class="list-group">
            <li><a href="?category=all&filter=pending" class="list-group-item list-group-item-action <?= $categoryFilter === 'all' ? 'active' : '' ?>">All Categories</a></li>
            <?php foreach ($categories as $category): ?>
                <li><a href="?category=<?= htmlspecialchars($category) ?>&filter=<?= $statusFilter ?>" class="list-group-item list-group-item-action <?= $categoryFilter === $category ? 'active' : '' ?>"><?= htmlspecialchars($category) ?></a></li>
            <?php endforeach; ?>
        </ul>

        <h3 class="mt-4">Filters</h3>
        <ul class="list-group">
            <li><a href="?category=<?= $categoryFilter ?>&filter=all" class="list-group-item list-group-item-action <?= $statusFilter === 'all' ? 'active' : '' ?>">All Tasks</a></li>
            <?php foreach ($statuses as $status): ?>
                <li><a href="?category=<?= $categoryFilter ?>&filter=<?= $status ?>" class="list-group-item list-group-item-action <?= $statusFilter === $status ? 'active' : '' ?>"><?= ucfirst($status) ?> Tasks</a></li>
            <?php endforeach; ?>
        </ul>
    </div>

    <!-- Main Content Area -->
    <div class="content">
        <div class="filter-bar">
            <h5>Current Filters: <?= htmlspecialchars($categoryFilter) ?>, <?= ucfirst($statusFilter) ?> Tasks</h5>
        </div>
        <?php if (empty($organizedTasks)): ?>
            <h5>No tasks found for the selected filters.</h5>
        <?php else: ?>
            <?php foreach ($organizedTasks as $category => $statusGroup): ?>
                <h4 class="mt-4"><?= htmlspecialchars($category) ?></h4>
                <?php foreach ($statusGroup as $status => $tasks): ?>
                    <h5><?= ucfirst($status) ?> Tasks</h5>
                    <div class="row">
                        <?php foreach ($tasks as $task): ?>
                            <div class="col-md-6">
                                <div class="task-card">
                                    <div class="card-body">
                                        <h5 class="card-title"><?= htmlspecialchars($task['title']) ?></h5>
                                        <span class="badge badge-<?= $task['status_task'] === 'completed' ? 'success' : 'warning' ?>">
                                            <?= ucfirst(htmlspecialchars($task['status_task'])) ?>
                                        </span>
                                        <p class="mt-3"><strong>Category:</strong> <?= htmlspecialchars($task['category']) ?></p>

                                        <div class="subtasks">
                                            <h6>Subtasks</h6>
                                            <ul>
                                                <?php
                                                // Fetch subtasks for this task
                                                $subStmt = $pdo->prepare("SELECT * FROM sub_tasks WHERE task_id = ?");
                                                $subStmt->execute([$task['id']]);
                                                $subTasks = $subStmt->fetchAll(PDO::FETCH_ASSOC);
                                                if (count($subTasks) > 0): 
                                                    foreach ($subTasks as $subTask): ?>
                                                        <li>
                                                            <?= $subTask['status'] === 'completed' ? '<span class="text-success">&#10004;</span>' : '<span class="text-danger">&#10006;</span>' ?>
                                                            <?= htmlspecialchars($subTask['sub_task']) ?>
                                                        </li>
                                                    <?php endforeach; 
                                                else: ?>
                                                    <li>No subtasks found.</li>
                                                <?php endif; ?>
                                            </ul>
                                        </div>

                                        <div class="task-actions">
                                            <?php if ($task['status_task'] !== 'completed'): ?>
                                                <a href="complete_task.php?id=<?= $task['id'] ?>" class="btn btn-outline-primary btn-sm">Mark as Completed</a>
                                                <a href="edit_task.php?id=<?= $task['id'] ?>" class="btn btn-outline-secondary btn-sm">Edit</a>
                                                <a href="delete_task.php?id=<?= $task['id'] ?>" class="btn btn-outline-danger btn-sm">Delete</a>
                                                <a href="add_subtask.php?task_id=<?= $task['id'] ?>" class="btn btn-outline-info btn-sm">Add Subtask</a>
                                            <?php else: ?>
                                                <span class="text-muted">Task completed</span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endforeach; ?>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<!-- Bootstrap JS and jQuery -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>
</html>

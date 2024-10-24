<?php
session_start();
require 'config/db.php';
require 'functions/helpers.php';

if (!isLoggedIn()) {
    header("Location: login.php");
    exit;
}

if (isset($_GET['id'])) {
    $taskId = (int) $_GET['id'];
    $stmt = $pdo->prepare("UPDATE tasks SET status_task = 'completed' WHERE id = ? AND user_id = ?");
    $stmt->execute([$taskId, $_SESSION['user_id']]);
}

header("Location: index.php");
exit;

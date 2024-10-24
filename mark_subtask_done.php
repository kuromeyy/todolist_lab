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

$stmt = $pdo->prepare("UPDATE sub_tasks SET status = 'completed' WHERE id = ?");
if ($stmt->execute([$subtask_id])) {
    header("Location: index.php");
    exit;
} else {
    echo "Failed to mark subtask as done. Please try again.";
}

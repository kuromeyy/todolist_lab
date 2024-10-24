<?php
session_start();
require 'config/db.php'; 

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $subtaskId = $_POST['id'];

    // Update the subtask status to 'completed'
    $stmt = $pdo->prepare("UPDATE sub_tasks SET status = 'Completed' WHERE id = ?");
    $updated = $stmt->execute([$subtaskId]);

    if ($updated) {
        echo 'success';
    } else {
        echo 'error';
    }
}
?>

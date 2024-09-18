<?php
session_start();
require 'config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];

if (isset($_GET['task_id'])) {
    $task_id = $_GET['task_id'];

    $stmt = $conn->prepare("DELETE FROM tasks WHERE task_id = ? AND user_id = ?");
    $stmt->bind_param("ii", $task_id, $user_id);

    if ($stmt->execute()) {
        header("Location: my_task.php?message=Task deleted successfully");
        exit;
    } else {
        header("Location: my_task.php?message=Error deleting task");
        exit;
    }

    $stmt->close();
} else {
    header("Location: my_task.php");
    exit;
}
?>

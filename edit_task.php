<?php
session_start();
require 'config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$task_id = $_GET['task_id'] ?? null;
$user_id = $_SESSION['user_id'];

if (!$task_id) {
    die('Task not found');
}

$errors = [];
$task_name = $description = $due_date = "";

$stmt = $conn->prepare("SELECT * FROM tasks WHERE task_id = ? AND user_id = ?");
$stmt->bind_param("ii", $task_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    die('Task not found or you do not have permission to edit this task.');
}

$task = $result->fetch_assoc();
$task_name = $task['task_name'];
$description = $task['description'];
$due_date = $task['due_date'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $task_name = trim($_POST['task_name']);
    $description = trim($_POST['description']);
    $due_date = $_POST['due_date'];

    if (empty($task_name)) {
        $errors[] = "Task name is required.";
    } elseif (strlen($task_name) > 100) {
        $errors[] = "Task name must not exceed 100 characters.";
    }

    if (empty($description)) {
        $errors[] = "Description is required.";
    }

    if (empty($due_date)) {
        $errors[] = "Due date is required.";
    } elseif (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $due_date)) {
        $errors[] = "Invalid date format.";
    }

    if (empty($errors)) {
        $stmt = $conn->prepare("UPDATE tasks SET task_name = ?, description = ?, due_date = ? WHERE task_id = ? AND user_id = ?");
        $stmt->bind_param("sssii", $task_name, $description, $due_date, $task_id, $user_id);

        if ($stmt->execute()) {
            header('Location: my_task.php?message=Task updated successfully!');
            exit;
        } else {
            $errors[] = "Error: " . $stmt->error;
        }
    }
}

$conn->close();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Task</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
    <?php include 'sidebar.php'; ?>
    
    <div class="main-content">
        <h1>Edit Task</h1>
        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <?php foreach ($errors as $error): ?>
                    <p><?php echo htmlspecialchars($error); ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        
        <form action="edit_task.php?task_id=<?php echo $task_id; ?>" method="POST" class="task-form">
            <label for="task_name">Task Name:</label>
            <input type="text" id="task_name" name="task_name" value="<?php echo htmlspecialchars($task_name); ?>" required>
            
            <label for="description">Description:</label>
            <textarea id="description" name="description" rows="4" required><?php echo htmlspecialchars($description); ?></textarea>
            
            <label for="due_date">Due Date:</label>
            <input type="date" id="due_date" name="due_date" value="<?php echo htmlspecialchars($due_date); ?>" required>
            
            <button type="submit" class="btn btn-primary">Update Task</button>
        </form>
    </div>
</div>
</body>
</html>

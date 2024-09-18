<?php
session_start();
require 'config.php'; 

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$errors = [];
$task_name = $description = $due_date = "";
$successMessage = "";

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
        $user_id = $_SESSION['user_id']; 
        $stmt = $conn->prepare("INSERT INTO tasks (task_name, description, due_date, user_id) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("sssi", $task_name, $description, $due_date, $user_id);

        if ($stmt->execute()) {
            $successMessage = "Task added successfully!";
            $task_name = $description = $due_date = "";
        } else {
            $errors[] = "Error: " . $stmt->error;
        }
        $stmt->close();
    }

    $conn->close();
}

$pageTitle = "Add Task";
ob_start();
?>

<h1>Add New Task</h1>

<?php if (!empty($errors)): ?>
    <div class="error">
        <?php foreach ($errors as $error): ?>
            <p><?php echo htmlspecialchars($error); ?></p>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php if (!empty($successMessage)): ?>
    <div class="success"><?php echo htmlspecialchars($successMessage); ?></div>
<?php endif; ?>

<form action="add_task.php" method="POST">
    <label for="task_name">Task Name:</label>
    <input type="text" id="task_name" name="task_name" required value="<?php echo htmlspecialchars($task_name); ?>">

    <label for="description">Description:</label>
    <textarea id="description" name="description" rows="4" required><?php echo htmlspecialchars($description); ?></textarea>

    <label for="due_date">Due Date:</label>
    <input type="date" id="due_date" name="due_date" required value="<?php echo htmlspecialchars($due_date); ?>">

    <input type="submit" value="Add Task">
</form>

<?php
$content = ob_get_clean();
include 'layout.php';
?>
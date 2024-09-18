<?php
session_start();
require 'config.php'; 

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id']; 

$tasks_per_page = 6;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $tasks_per_page;

$sql = "SELECT task_id, task_name, description, due_date, created_at FROM tasks WHERE user_id = ? LIMIT ? OFFSET ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iii", $user_id, $tasks_per_page, $offset);
$stmt->execute();
$result = $stmt->get_result();

$count_sql = "SELECT COUNT(*) AS total FROM tasks WHERE user_id = ?";
$count_stmt = $conn->prepare($count_sql);
$count_stmt->bind_param("i", $user_id);
$count_stmt->execute();
$count_result = $count_stmt->get_result();
$row = $count_result->fetch_assoc();
$total_tasks = $row['total'];
$total_pages = ceil($total_tasks / $tasks_per_page);

$stmt->close();
$count_stmt->close();
$conn->close();

$pageTitle = "Mytasks";
ob_start();
?>

<script>
        function confirmDelete(taskId) {
            if (confirm("Are you sure you want to delete this task? This action cannot be undone.")) {
                window.location.href = "delete_task.php?task_id=" + taskId;
            }
        }
    </script>

<h1>Your Tasks</h1>

<?php if ($result->num_rows > 0): ?>
    <?php while ($row = $result->fetch_assoc()): ?>
        <div class="task">
            <h3><?php echo htmlspecialchars($row['task_name']); ?></h3>
            <p><?php echo htmlspecialchars($row['description']); ?></p>
            <p><strong>Due Date:</strong> <?php echo htmlspecialchars($row['due_date']); ?></p>
            <p class="created-at"><small>Created At: <?php echo htmlspecialchars($row['created_at']); ?></small></p>
            <span class="btn-btn-small-btn-primary">
            <a href="edit_task.php?task_id=<?php echo $row['task_id']; ?>" >Edit</a>
            </span>
            <span class="btn-btn-small-btn-danger">
            <a href="javascript:void(0);" onclick="confirmDelete(<?php echo $row['task_id']; ?>)" >Delete</a>
            </span>
            

        </div>
    <?php endwhile; ?>
<?php else: ?>
    <p>No tasks found.</p>
<?php endif; ?>

<div class="pagination">
    <?php if ($page > 1): ?>
        <a href="my_task.php?page=<?php echo $page - 1; ?>">Previous</a>
    <?php endif; ?>

    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
        <a href="my_task.php?page=<?php echo $i; ?>"><?php echo $i; ?></a>
    <?php endfor; ?>

    <?php if ($page < $total_pages): ?>
        <a href="my_task.php?page=<?php echo $page + 1; ?>">Next</a>
    <?php endif; ?>
</div>

<?php
$content = ob_get_clean();
include 'layout.php';
?>
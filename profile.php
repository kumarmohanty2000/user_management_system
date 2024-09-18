<?php
session_start();
require 'config.php'; 

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id']; 

$sql = "SELECT username, email FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();
$conn->close();

$pageTitle = "Profile";
ob_start();
?>

<h1>Profile Page</h1>
<div class="profile">
    <h2><?php echo htmlspecialchars($user['username']); ?></h2>
    <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
    <a href="edit_profile.php" class="button">Edit Profile</a>
</div>

<?php
$content = ob_get_clean();
include 'layout.php';
?>

<?php
session_start();
require 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT username, email FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($username, $email);
$stmt->fetch();
$stmt->close();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $new_username = $_POST['username'];
    $new_email = $_POST['email'];
    $new_password = $_POST['password'];

    $update_query = "UPDATE users SET username = ?, email = ?";

    if (!empty($new_password)) {
        $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);
        $update_query .= ", password = ?";
    }

    $update_query .= " WHERE id = ?";

    $stmt = $conn->prepare($update_query);
    if (!empty($new_password)) {
        $stmt->bind_param("sssi", $new_username, $new_email, $hashed_password, $user_id);
    } else {
        $stmt->bind_param("ssi", $new_username, $new_email, $user_id);
    }

    if ($stmt->execute()) {
        $success_message = "Profile updated successfully!";
    } else {
        $error_message = "Error updating profile: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Profile</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            display: flex;
            min-height: 100vh;
            flex-direction: row;
        }
        .sidebar {
            background-color: #333;
            color: #fff;
            width: 250px;
            padding: 20px;
            box-sizing: border-box;
            height: 100%;
            position: fixed;
            top: 0;
            left: 0;
            display: flex;
            flex-direction: column;
        }
        .sidebar a {
            color: #fff;
            text-decoration: none;
            display: block;
            padding: 10px 0;
            margin-bottom: 10px;
            border-radius: 4px;
        }
        .sidebar a:hover {
            background-color: #575757;
        }
        .main-content {
            margin-left: 250px;
            padding: 20px;
            width: calc(100% - 250px);
            box-sizing: border-box;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
        }
        .form-group input {
            width: 100%;
            padding: 8px;
            box-sizing: border-box;
        }
        .form-group button {
            padding: 10px 20px;
            background-color: #5bc0de;
            color: white;
            border: none;
            cursor: pointer;
        }
        .form-group button:hover {
            background-color: #31b0d5;
        }
        @media (max-width: 768px) {
            .sidebar {
                width: 40%;
                position: static;
                height: auto;
                flex-direction: column;
                padding: 10px;
            }
            .main-content {
                margin-left: 0;
                width: 70%;
            }
        }
        @media (max-width: 600px) {
            .sidebar {
                width: 30%;
                position: static;
                height: auto;
                flex-direction: column;
                font-size: 14px;
                padding: 5px;
            }
        }
    </style>
</head>
<body>

<div class="sidebar">
    <h3>User Menu</h3>
    <a href="profile.php">Profile</a>
    <a href="logout.php">Logout</a>
</div>

<div class="main-content">
    <h2>Profile Page</h2>
    <p>Welcome, <?php echo htmlspecialchars($username); ?>!</p>
    
    <form method="post" action="">
        <div class="form-group">
            <label for="username">Username:</label>
            <input type="text" name="username" value="<?php echo htmlspecialchars($username); ?>" required>
        </div>
        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required>
        </div>
        <div class="form-group">
            <label for="password">New Password (leave blank to keep current password):</label>
            <input type="password" name="password" placeholder="New Password">
        </div>
        <div class="form-group">
            <button type="submit">Update Profile</button>
        </div>
    </form>
    
    <?php if (isset($success_message)): ?>
        <p style="color: green;"><?php echo htmlspecialchars($success_message); ?></p>
    <?php elseif (isset($error_message)): ?>
        <p style="color: red;"><?php echo htmlspecialchars($error_message); ?></p>
    <?php endif; ?>
</div>

</body>
</html>

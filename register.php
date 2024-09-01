<?php
require 'config.php'; 

$usernameErr = $emailErr = $passwordErr = "";
$username = $email = $password = "";
$successMessage = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (empty($_POST["username"])) {
        $usernameErr = "Username is required";
    } else {
        $username = trim($_POST["username"]);
        if (!preg_match("/^[a-zA-Z0-9]*$/", $username)) {
            $usernameErr = "Only letters and numbers allowed";
        }
    }

    if (empty($_POST["email"])) {
        $emailErr = "Email is required";
    } else {
        $email = trim($_POST["email"]);
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $emailErr = "Invalid email format";
        }
    }

    if (empty($_POST["password"])) {
        $passwordErr = "Password is required";
    } else {
        $password = trim($_POST["password"]);
        if (strlen($password) < 6) {
            $passwordErr = "Password must be at least 6 characters";
        }
    }

    if (empty($usernameErr) && empty($emailErr) && empty($passwordErr)) {
        $checkDuplicate = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $checkDuplicate->bind_param("ss", $username, $email);
        $checkDuplicate->execute();
        $result = $checkDuplicate->get_result();

        if ($result->num_rows > 0) {
            $duplicateErr = "Username or Email already exists. Please choose another.";
        } else {
            $hashed_password = password_hash($password, PASSWORD_BCRYPT);
            $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $username, $email, $hashed_password);

            if ($stmt->execute()) {
                $successMessage = "Registration successful! You can now log in.";
                $username = $email = $password = ""; 
            } else {
                echo "<div class='error'>Error: " . $stmt->error . "</div>";
            }

            $stmt->close();
        }

        $checkDuplicate->close();
    }

    $conn->close();
       
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register</title>
    <link rel="stylesheet" href="index.css">
</head>
<body>
<div class="container">
    <h2>Register</h2>
    <?php if (!empty($duplicateErr)) : ?>
        <div class="error"><?php echo $duplicateErr; ?></div>
    <?php endif; ?>
    <?php if (!empty($successMessage)) : ?>
        <div class="success"><?php echo $successMessage; ?></div>
        <a href="login.php" class="login-button">Login </a>
    <?php endif; ?>
    <form method="post" action="register.php">
        <div class="form-group">
            <label for="username">Username:</label>
            <input type="text" name="username" id="username" value="<?php echo htmlspecialchars($username); ?>">
            <div class="error"><?php echo $usernameErr; ?></div>
        </div>
        <div class="form-group">
            <label for="email">Email:</label>
            <input type="text" name="email" id="email" value="<?php echo htmlspecialchars($email); ?>">
            <div class="error"><?php echo $emailErr; ?></div>
        </div>
        <div class="form-group">
            <label for="password">Password:</label>
            <input type="password" name="password" id="password">
            <div class="error"><?php echo $passwordErr; ?></div>
        </div>
        <input type="submit" name="submit" value="Register">

        <a href="login.php" class="login-button">User Already Exist!!! </a>
    </form>
    
</div>

</body>
</html>

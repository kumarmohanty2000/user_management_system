<?php

$pageTitle = "Dashboard";
ob_start();
?>

<h1>Welcome To Task Management System</h1>
<div class="message">
    <p>You can create, update, delete your data seemlessly</p>
    <p>It is a secure connection cloud system</p>
    <p>Thank You</p>
    <p>From Saroj</p>

</div>

<?php
$content = ob_get_clean();
include 'layout.php';
?>
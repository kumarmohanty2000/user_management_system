<?php
$servername = "server_______name";
$username = "user________name";         
$password = "password";             
$dbname = "database__________name"; 

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>

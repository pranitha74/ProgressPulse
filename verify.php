<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "progresspulse";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_GET['token'])) {
    $token = $_GET['token'];
    $sql = "SELECT * FROM signup WHERE verification_token='$token' AND verified=FALSE";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $sql = "UPDATE signup SET verified=TRUE WHERE verification_token='$token'";
        if ($conn->query($sql) === TRUE) {
            echo "Email verified successfully! You can now <a href='home.html'>log in</a>.";
        } else {
            echo "Error updating record: " . $conn->error;
        }
    } else {
        echo "Invalid or expired token.";
    }
} else {
    echo "No token provided.";
}

$conn->close();
?>

<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['student_logged_in'])) {
    header('Location: studentlogin.php');
    exit();
}

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

$email = $_SESSION['student_email'];

// Fetch user data
$sql = "SELECT id, email FROM studentlogin WHERE email='$email'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $id = $row['id'];
    $email = $row['email'];
} else {
    echo "User not found.";
    exit();
}

// Handle password change
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    if ($new_password === $confirm_password) {
        $update_sql = "UPDATE studentlogin SET password='$new_password' WHERE email='$email'";
        if ($conn->query($update_sql) === TRUE) {
            echo "Password updated successfully.";
        } else {
            echo "Error updating password: " . $conn->error;
        }
    } else {
        echo "Passwords do not match.";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #f4f4f4;
            margin: 0;
        }
        .container {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            text-align: center;
            width: 300px;
        }
        input {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        .update {
            width: 100%;
            padding: 10px;
            background: #b3b3ff;
            border: none;
            border-radius: 5px;
            color: white;
            font-weight: bold;
            cursor: pointer;
            margin-top: 10px;
        }
        .update:hover {
            background: #8a8aff;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>User Profile</h2>
        <p>ID: <?php echo $id; ?></p>
        <p>Email: <?php echo $email; ?></p>
        <form action="" method="post">
            <input type="password" name="new_password" placeholder="Enter new password" required>
            <input type="password" name="confirm_password" placeholder="Confirm new password" required>
            <button type="submit" class="update">Update Password</button>
        </form>
    </div>
</body>
</html> 
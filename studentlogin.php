<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $servername = "localhost";
    $username = "root"; // Default XAMPP MySQL username
    $password = ""; // Default XAMPP MySQL password is empty
    $dbname = "progresspulse";

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $email = $_POST['email'];
    $password = $_POST['password'];

    // Check if the admin exists
    $sql = "SELECT * FROM studentlogin WHERE email='$email' AND password='$password'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // Set session variables
        $_SESSION['student_logged_in'] = true;
        $_SESSION['student_email'] = $email;

        // Redirect to teacher dashboard
        header("Location: Dashboard.php");
        exit();
    } else {
        echo "Invalid email or password.";
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teacher Login</title>
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
        .login {
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
        .login:hover {
            background: #8a8aff;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>student Login</h2>
        <form action="" method="post">
            <input type="email" name="email" placeholder="Enter your email" required>
            <input type="password" name="password" placeholder="Enter your password" required>
            <button type="submit" class="login">Login</button>
        </form>
    </div>
</body>
</html>

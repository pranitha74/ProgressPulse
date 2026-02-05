<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
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

    $code = $_POST['code'];

    $sql = "SELECT * FROM signup WHERE verification_code='$code' AND verified=FALSE";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $sql = "UPDATE signup SET verified=TRUE WHERE verification_code='$code'";
        if ($conn->query($sql) === TRUE) {
            header("Location: home.html");
            exit();
        } else {
            echo "Error updating record: " . $conn->error;
        }
    } else {
        echo "Invalid or expired code.";
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Your Email</title>
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
        .verify {
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
        .verify:hover {
            background: #8a8aff;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Enter Verification Code</h2>
        <form action="" method="post">
            <input type="text" name="code" placeholder="Enter your verification code" required>
            <button type="submit" class="verify">Verify</button>
        </form>
    </div>
</body>
</html>
<?php
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

    $universityName = $_POST['universityName'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    $verificationCode = rand(100000, 999999); // Generate a 6-digit random code
    $sql = "INSERT INTO signup (universityName, email, password, verification_code) VALUES ('$universityName', '$email', '$password', '$verificationCode')";

    if ($conn->query($sql) === TRUE) {
        // Use PHPMailer for SMTP
        require 'PHPMailer/PHPMailerAutoload.php';
        $mail = new PHPMailer;
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'your-email@gmail.com'; // Your Gmail address
        $mail->Password = 'your-email-password'; // Your Gmail password
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        $mail->setFrom('your-email@gmail.com', 'Your Name');
        $mail->addAddress($email);
        $mail->Subject = "Email Verification";
        $mail->Body    = "Your verification code is: $verificationCode";

        if(!$mail->send()) {
            echo 'Message could not be sent.';
            echo 'Mailer Error: ' . $mail->ErrorInfo;
        } else {
            echo 'Verification email has been sent.';
            // Redirect to the verification input page
            header("Location: verify_input.php");
            exit();
        }
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Your University</title>
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
            background-color: white;
        }
        .logo img {
            width: 50px;
            margin-bottom: 10px;
            background-color: white;
        }
        h2 {
            font-size: 18px;
            margin-bottom: 20px;
            color: #555;
        }
        input {
            width: 100%;
            padding: 10px;
            margin: 10px 0 ;
            border: 1px solid #ccc;
            border-radius: 5px;
            padding-right: 10px;
        }
        .password-wrapper {
            display: flex;
            align-items: center;
            border: 1px solid #ccc;
            border-radius: 5px;
            padding-right: 10px;
        }
        .password-wrapper input {
            border: none;
            flex: 1;
            padding: 10px;
        }
        .toggle-password {
            cursor: pointer;
        }
        .sign-up {
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
        .sign-up:hover {
            background: #8a8aff;
        }
        p {
            font-size: 14px;
            margin-top: 15px;
        }
        p a {
            color: #6a6aff;
            text-decoration: none;
        }
        p a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="logo">
            <img src="images/account-icon.jpg" alt="School Logo">
        </div>
        <h2>Register your University</h2>
        <form action="" method="post">
            <input type="text" name="universityName" placeholder="Enter your University Name" required>
            <input type="email" name="email" placeholder="Enter your email" required>
            <small>Email verification is required to log in.</small>
            <div class="password-wrapper">
                <input type="password" name="password" id="password" placeholder="Enter your password" required>
                <span class="toggle-password" onclick="togglePassword('password')">👁️</span>
            </div>
            <div class="password-wrapper">
                <input type="password" id="confirm-password" placeholder="Confirm your password" required>
                <span class="toggle-password" onclick="togglePassword('confirm-password')">👁️</span>
            </div>
            <button type="submit" class="sign-up">SIGN UP</button>
        </form>
        <p>Already have an account? <a href="home.html">Sign In</a> or <a href="#">Resend Verification Email</a></p>
    </div>
    <script>
        function togglePassword(fieldId) {
            const passwordField = document.getElementById(fieldId);
            passwordField.type = passwordField.type === "password" ? "text" : "password";
        }
    </script>
</body>
</html>

<?php
// Database connection parameters
$host = 'localhost';
$db = 'progresspulse';
$user = 'root';
$pass = '';

// Create a connection
$conn = new mysqli($host, $user, $pass, $db);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Prepare data for teacherlogin and teacher_p
$data_logins = [];
$data_teachers = [];
for ($i = 1; $i <= 50; $i++) {
    $email = "teacher$i@example.com"; // Generate a random email
    $random_password = bin2hex(random_bytes(4)); // Generate a random password
    $hashed_password = password_hash($random_password, PASSWORD_BCRYPT);
    $name = "Teacher $i";
    $profession = "Profession $i";

    $data_logins[] = "('$email', '$hashed_password')";
    $data_teachers[] = "('$name', '$profession')";
}

// Construct the SQL query for teacherlogin
$login_sql = "INSERT INTO teacherlogin (email, password) VALUES " . implode(", ", $data_logins);

// Construct the SQL query for teacher_p
$teacher_sql = "INSERT INTO teachers_p (name, profession) VALUES " . implode(", ", $data_teachers);

// Execute the queries
if ($conn->query($login_sql) === TRUE && $conn->query($teacher_sql) === TRUE) {
    echo "50 records inserted successfully into both teacherlogin and teacher_p tables";
} else {
    echo "Error: " . $conn->error;
}

// Close the connection
$conn->close();
?>

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

// SQL query to fetch student details and progress
$sql = "SELECT id, name, progress FROM mystudents";
$result = $conn->query($sql);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Details</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h1>Student Details and Progress</h1>
    <table border="1">
        <tr>
            <th>Student ID</th>
            <th>Student Name</th>
            <th>Progress</th>
        </tr>
        <?php
        if ($result->num_rows > 0) {
            // Output data of each row
            while($row = $result->fetch_assoc()) {
                echo "<tr><td>" . $row["id"]. "</td><td>" . $row["name"]. "</td><td>" . $row["progress"]. "</td></tr>";
            }
        } else {
            echo "<tr><td colspan='3'>No students found</td></tr>";
        }
        $conn->close();
        ?>
    </table>
</body>
</html>

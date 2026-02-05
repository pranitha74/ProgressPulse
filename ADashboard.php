<?php
session_start();

if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: Adminlogin.php");
    exit();
}

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

// Fetch admin details
$admin_email = $_SESSION['admin_email'];
$admin_sql = "SELECT * FROM admin_login WHERE email = ?";
$admin_stmt = $conn->prepare($admin_sql);
$admin_stmt->bind_param("s", $admin_email);
$admin_stmt->execute();
$admin_result = $admin_stmt->get_result();
$admin = $admin_result->fetch_assoc();

// Handle password change
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['new_password'])) {
    $new_password = password_hash($_POST['new_password'], PASSWORD_BCRYPT);
    $update_sql = "UPDATE admin_login SET password = ? WHERE email = ?";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param("ss", $new_password, $admin_email);
    if ($update_stmt->execute()) {
        $_SESSION['message'] = "Password updated successfully.";
    } else {
        $_SESSION['message'] = "Error updating password.";
    }
}

// Fetch student details
$student_sql = "SELECT id, name, progress FROM mystudents";
$student_result = $conn->query($student_sql);

// Fetch teacher details
$teacher_sql = "SELECT id, name, profession FROM teachers_p";
$teacher_result = $conn->query($teacher_sql);

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            display: grid;
            grid-template-columns: 250px 1fr;
            margin: 0;
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            height: 100vh;
        }
        .sidebar {
            background-color: #007BFF;
            color: white;
            padding: 15px;
            box-shadow: 2px 0 5px rgba(0,0,0,0.1);
        }
        .sidebar h2 {
            margin-top: 0;
        }
        .sidebar ul {
            list-style-type: none;
            padding: 0;
        }
        .sidebar ul li {
            margin: 15px 0;
        }
        .sidebar ul li a {
            color: white;
            text-decoration: none;
        }
        .content {
            padding: 20px;
            overflow-y: auto;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            background-color: white;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        th, td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <h2>Admin Dashboard</h2>
        <ul>
            <li><a href="?section=home">Home</a></li>
            <li><a href="?section=students">Students</a></li>
            <li><a href="?section=teachers">Teachers</a></li>
        </ul>
        <form action="logout.php" method="post" style="margin-top: 20px;">
            <input type="hidden" name="redirect" value="home.html">
            <input type="submit" value="Logout" style="color: #007BFF; padding: 5px 20px; border: 1px solid #007BFF; border-radius: 10px; cursor: pointer; width: 100%; background-color: transparent;">
        </form>
    </div>
    <div class="content">
        <?php
        $section = $_GET['section'] ?? 'home';

        if ($section == 'home') {
            echo "<h2>Admin Profile</h2>";
            echo "<p><strong>Email:</strong> " . $admin['email'] . "</p>";
            echo "<p><strong>id:</strong> " . $admin['id'] . "</p>";
            // Add more admin profile details here

            if (isset($_SESSION['message'])) {
                echo "<p>" . $_SESSION['message'] . "</p>";
                unset($_SESSION['message']);
            }

            echo '<form action="" method="post">
                    <label for="new_password">Change Password:</label>
                    <input type="password" name="new_password" required>
                    <input type="submit" value="Update Password">
                  </form>';
        } elseif ($section == 'students') {
            echo "<h2>Student Details</h2>";
            echo "<table>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Progress</th>
                    </tr>";
            if ($student_result->num_rows > 0) {
                while($row = $student_result->fetch_assoc()) {
                    echo "<tr><td>" . $row["id"]. "</td><td>" . $row["name"]. "</td><td>" . $row["progress"]. "</td></tr>";
                }
            } else {
                echo "<tr><td colspan='3'>No students found</td></tr>";
            }
            echo "</table>";
        } elseif ($section == 'teachers') {
            echo "<h2>Teacher Details</h2>";
            echo "<table>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Profession</th>
                    </tr>";
            if ($teacher_result->num_rows > 0) {
                while($row = $teacher_result->fetch_assoc()) {
                    echo "<tr><td>" . $row["id"]. "</td><td>" . $row["name"]. "</td><td>" . $row["profession"]. "</td></tr>";
                }
            } else {
                echo "<tr><td colspan='3'>No teachers found</td></tr>";
            }
            echo "</table>";
        }
        ?>
    </div>
</body>
</html>

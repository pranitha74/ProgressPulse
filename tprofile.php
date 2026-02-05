<?php
session_start();

// Handle logout action
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    // Destroy all session data
    session_unset();
    session_destroy();
    
    // Redirect to home page
    header("Location: home.html");
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

// Fetch teacher data from 'teachers' table
$teacher_id = 1; // Example ID, replace with dynamic session ID
$sql = "SELECT name, profession FROM teachers_p WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $teacher_id);
$stmt->execute();
$result = $stmt->get_result();
$teacher = $result->fetch_assoc();

// Fetch email and password from 'teacherlogin' table
$sql_login = "SELECT email, password FROM teacherlogin WHERE id = ?";
$stmt_login = $conn->prepare($sql_login);
$stmt_login->bind_param("i", $teacher_id);
$stmt_login->execute();
$result_login = $stmt_login->get_result();
$login_data = $result_login->fetch_assoc();

// Handle password change
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['new_password'])) {
    $new_password = password_hash($_POST['new_password'], PASSWORD_BCRYPT);
    $update_sql = "UPDATE teacherlogin SET password = ? WHERE id = ?";
    $update_stmt = $conn->prepare($update_sql);
    if ($update_stmt) {
        $update_stmt->bind_param("si", $new_password, $teacher_id);
        if ($update_stmt->execute()) {
            $_SESSION['message'] = "Password updated successfully.";
        } else {
            $_SESSION['message'] = "Error updating password: " . $update_stmt->error;
        }
    } else {
        $_SESSION['message'] = "Error preparing statement: " . $conn->error;
    }
}

// Handle name and profession update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['name']) && isset($_POST['profession'])) {
    $new_name = $_POST['name'];
    $new_profession = $_POST['profession'];
    $update_sql = "UPDATE teachers_p SET name = ?, profession = ? WHERE id = ?";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param("ssi", $new_name, $new_profession, $teacher_id);
    if ($update_stmt->execute()) {
        echo "Profile updated successfully.";
        $teacher['name'] = $new_name;
        $teacher['profession'] = $new_profession;
    } else {
        echo "Error updating profile.";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="styles.css">
    <title>Teacher Profile</title>
    <style>
        .logout-button {
            display: inline-block;
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            text-align: center;
            text-decoration: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }

        .logout-button:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    <div class="profile-container">
        <h2>Teacher Profile</h2>
        <?php
        if (isset($_SESSION['message'])) {
            echo "<p>" . $_SESSION['message'] . "</p>";
            unset($_SESSION['message']);
        }
        ?>

        <form action="tprofile.php" method="post">
            <label for="name">Name:</label>
            <input type="text" name="name" value="<?php echo $teacher['name']; ?>" required>
            <label for="profession">Profession:</label>
            <input type="text" name="profession" value="<?php echo $teacher['profession']; ?>" required>
            <label for="email">email id:</label>
            <input type="text" name="email" value="<?php echo $login_data['email']; ?>" required>
            <input type="submit" value="Update Profile">
        </form>

        <form action="tprofile.php" method="post">
            <label for="new_password">Change Password:</label>
            <input type="password" name="new_password" required>
            <input type="submit" value="Update Password">
        </form>

        <a href="tprofile.php?action=logout" class="logout-button">Logout</a>
    </div>
</body>
</html>

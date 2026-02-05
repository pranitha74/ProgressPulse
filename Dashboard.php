<?php
session_start();

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

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Query to get the user ID from the studentlogin table
    $query = "SELECT id FROM studentlogin WHERE username = ? AND password = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        $_SESSION['user_id'] = $row['id']; // Store the user ID in the session
        // Redirect to the dashboard or wherever you need
        header("Location: Dashboard.php");
        exit();
    } else {
        // Handle login failure
        echo "Invalid username or password.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard UI</title>
    <link rel="stylesheet" href="Dashboard.css">
    <script src="Dashboard.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free/js/all.min.js"></script>
</head>
<body>
    <div class="sidebar">
        <h2>Progress Pulse</h2>
        <ul>
            <li class="active">Dashboard</li>
            <li><a href="s_code.php">Code</a></li>
            <li><a href="studentdocument.php">Documents</a></li>
            <li><a href="user_profile.php">User Profile</a></li>
        </ul>
        <button class="logout">Logout</button>
    </div>
    <div class="main-content">
        
        <section class="dashboard-overview">
            <div class="card">GPA: 3.95</div>
            <div class="card">Attendance: 87%</div>
            <div class="card">Tuition: Paid</div>
        </section>
        <section class="academic-grades">
            <h3>Academic Grades</h3>
            <table>
                <tr>
                    <th>ID</th>
                    <th>Course Name</th>
                    <th>Semester</th>
                    <th>Credits</th>
                    <th>Points</th>
                    <th>Grade</th>
                </tr>
                <tr>
                    <td>CR1001</td>
                    <td>Data Structures and Algorithms</td>
                    <td>II</td>
                    <td>4</td>
                    <td>97.6</td>
                    <td>A</td>
                </tr>
            </table>
        </section>
    </div>
</body>
</html>

<?php
// Database connection parameters
$host = 'localhost';
$db = 'progresspulse'; // Ensure this is your database name
$user = 'root';
$pass = '';

// Create a connection
$conn = new mysqli($host, $user, $pass, $db);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['document'])) {
    $uploadDir = 'uploads/';
    $uploadFile = $uploadDir . basename($_FILES['document']['name']);

    if (move_uploaded_file($_FILES['document']['tmp_name'], $uploadFile)) {
        // File uploaded successfully, save the file path in the database
        $title = $_POST['title'];
        $accessLevel = $_POST['access_level'];
        $members = $_POST['members'];
        $size = $_FILES['document']['size'];
        $sender = $_POST['sender'];

        $sql = "INSERT INTO tdocuments (title, file_path, access_level, members, size, sender) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssiss", $title, $uploadFile, $accessLevel, $members, $size, $sender);
        $stmt->execute();
        echo "File uploaded and data saved successfully.";
    } else {
        echo "File upload failed.";
    }
}

// SQL query to fetch received documents
$sql = "SELECT id, file_path FROM studentdocuments";
$result = $conn->query($sql);

// Fetch replies including download time
$replies_sql = "SELECT document_id, feedback, file_path, download_time, time_submitted FROM document_replies";
$replies_result = $conn->query($replies_sql);

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <form action="TDocuments.php" method="post" enctype="multipart/form-data">
        <label for="title">Document Title:</label>
        <input type="text" name="title" required>
        <label for="document">Choose a file:</label>
        <input type="file" name="document" required>
        <label for="access_level">Access Level:</label>
        <input type="text" name="access_level">
        <label for="members">Members:</label>
        <input type="number" name="members">
        <label for="sender">Document Sender:</label>
        <input type="text" name="sender" required>
        <input type="submit" value="Upload Document">
    </form>

   

    <h2>Document Replies</h2>
    <table>
        <tr>
            <th>Document ID</th>
            <th>Feedback</th>
            <th>File</th>
            <th>Download Time</th>
            <th>Time Submitted</th>
        </tr>
        <?php
        if ($replies_result->num_rows > 0) {
            while($row = $replies_result->fetch_assoc()) {
                echo "<tr>
                        <td>" . htmlspecialchars($row["document_id"]) . "</td>
                        <td>" . htmlspecialchars($row["feedback"]) . "</td>
                        <td><a href='" . htmlspecialchars($row["file_path"]) . "' download>Download</a></td>
                        <td>" . (isset($row["download_time"]) ? htmlspecialchars($row["download_time"]) : 'Not downloaded') . "</td>
                        <td>" . htmlspecialchars($row["time_submitted"]) . "</td>
                      </tr>";
            }
        } else {
            echo "<tr><td colspan='5'>No replies found</td></tr>";
        }
        ?>
    </table>
</body>
</html>



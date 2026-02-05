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

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['document_id'])) {
    $documentId = $_POST['document_id'];

    // Update the document to mark it as sent to the teacher
    $sql = "UPDATE tdocuments SET sent_to_teacher = 1 WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $documentId);
    if ($stmt->execute()) {
        echo "Document sent to teacher successfully.";
    } else {
        echo "Error sending document to teacher.";
    }
    $stmt->close();
}

$conn->close();
?> 
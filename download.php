<?php
session_start();

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

if (isset($_GET['document_id'])) {
    $document_id = $_GET['document_id'];
    $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null; // Optional
    $download_time = date('Y-m-d H:i:s');

    // Log the download time
    $sql = "UPDATE document_replies SET download_time = ? WHERE document_id = ? AND user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sii", $download_time, $document_id, $user_id);
    $stmt->execute();

    // Fetch the file path
    $file_sql = "SELECT file_path FROM tdocuments WHERE id = ?";
    $file_stmt = $conn->prepare($file_sql);
    $file_stmt->bind_param("i", $document_id);
    $file_stmt->execute();
    $file_stmt->bind_result($file_path);
    $file_stmt->fetch();

    // Serve the file for download
    if (file_exists($file_path)) {
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . basename($file_path) . '"');
        header('Content-Length: ' . filesize($file_path));
        readfile($file_path);
        exit;
    } else {
        echo "File not found.";
    }
}

$conn->close();
?>

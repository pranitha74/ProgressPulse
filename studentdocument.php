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

// Fetch documents from tdocuments
$document_sql = "SELECT id, title, file_path, size, sender FROM tdocuments";
$document_result = $conn->query($document_sql);

// Fetch download times
$download_sql = "SELECT document_id, download_time FROM document_replies";
$download_result = $conn->query($download_sql);

$download_times = [];
while ($download_row = $download_result->fetch_assoc()) {
    $download_times[$download_row['document_id']] = $download_row['download_time'];
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Documents</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f4f4f4;
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
    <h2>Student Documents</h2>
    <table>
        <tr>
            <th>Title</th>
            <th>File Path</th>
            <th>Size (KB)</th>
            <th>Sender</th>
            <th>Download Time</th>
            <th>Actions</th>
        </tr>
        <?php
        if ($document_result->num_rows > 0) {
            while($row = $document_result->fetch_assoc()) {
                echo "<tr>
                        <td>" . htmlspecialchars($row["title"]) . "</td>
                        <td><a href='download.php?document_id=" . $row["id"] . "'>Download</a></td>
                        <td>" . htmlspecialchars($row["size"]) . "</td>
                        <td>" . htmlspecialchars($row["sender"]) . "</td>
                        <td>" . (isset($download_times[$row["id"]]) ? $download_times[$row["id"]] : 'Not downloaded') . "</td>
                        <td><a href='reply.php?document_id=" . $row["id"] . "'>Reply</a></td>
                      </tr>";
            }
        } else {
            echo "<tr><td colspan='5'>No documents found</td></tr>";
        }
        ?>
    </table>
</body>
</html>
<?php
session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

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

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $document_id = isset($_POST['document_id']) ? $_POST['document_id'] : null;
    if ($document_id === null) {
        die("Error: Document ID is missing.");
    }
    $feedback = $_POST['feedback'];
    $uploadDir = 'uploads/';
    $uploadFile = $uploadDir . basename($_FILES['file']['name']);
    $time_submitted = date('Y-m-d H:i:s');

    // Debugging output
    echo "Document ID: " . $document_id . "<br>";

    if (move_uploaded_file($_FILES['file']['tmp_name'], $uploadFile)) {
        $sql = "INSERT INTO document_replies (document_id, feedback, file_path, time_submitted) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("isss", $document_id, $feedback, $uploadFile, $time_submitted);
        $stmt->execute();
        echo "Reply submitted successfully.";
    } else {
        echo "File upload failed.";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reply to Document</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f4f4f4;
        }
        h2 {
            color: #333;
        }
        form {
            background-color: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            max-width: 600px;
            margin: 0 auto;
        }
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
        }
        input[type="text"],
        input[type="file"],
        textarea {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        input[type="submit"] {
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }
        input[type="submit"]:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    <h2>Reply to Document</h2>
    <form action="reply.php" method="post" enctype="multipart/form-data">
        <input type="hidden" name="document_id" value="<?php echo isset($_GET['document_id']) ? $_GET['document_id'] : ''; ?>">
        <label for="feedback">Feedback:</label>
        <textarea name="feedback" required></textarea>
        <label for="file">Attach File:</label>
        <input type="file" name="file" required>
        <input type="submit" value="Submit Reply">
    </form>
</body>
</html>

<?php
// Database connection
$conn = new mysqli("localhost", "root", "", "progresspulse");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$output = ""; // Variable to store the output of the code execution
$correct_answers = 0; // Initialize correct answers count

// Initialize a session variable to track correct outputs
if (!isset($_SESSION['correct_outputs'])) {
    $_SESSION['correct_outputs'] = [];
}

// Handle file download
if (isset($_GET['download_file'])) {
    $filename = $_GET['download_file'];

    // Fetch file content from the database
    $stmt = $conn->prepare("SELECT content FROM files WHERE filename = ?");
    $stmt->bind_param("s", $filename);
    $stmt->execute();
    $stmt->bind_result($filecontent);
    $stmt->fetch();
    $stmt->close();

    if ($filecontent) {
        // Serve the file content as a downloadable file
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . basename($filename) . '"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . strlen($filecontent));
        echo $filecontent;
        exit;
    } else {
        echo "File not found.";
    }
}

// Handle file deletion
if (isset($_POST['delete_file'])) {
    $filename = $_POST['filename'];
    $stmt = $conn->prepare("DELETE FROM files WHERE filename = ?");
    $stmt->bind_param("s", $filename);
    $stmt->execute();
    $stmt->close();

    // Optionally, delete the file from the server
    $filepath = "saved_files/" . $filename;
    if (file_exists($filepath)) {
        unlink($filepath);
    }

    // Refresh the page to update the file list
    header("Location: s_code.php");
    exit;
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['code']) && isset($_POST['language'])) {
        $code = $_POST['code'];
        $language = $_POST['language'];
        $filename = $_POST['filename'] ?? 'default_name';

        // Check if the file already exists
        $stmt = $conn->prepare("SELECT COUNT(*) FROM files WHERE filename = ?");
        $stmt->bind_param("s", $filename);
        $stmt->execute();
        $stmt->bind_result($count);
        $stmt->fetch();
        $stmt->close();

        if ($count > 0) {
            echo "<script>alert('A file with this name already exists. Please choose a different name.');</script>";
        } else {
            // Save code and filename to the new files table
            $stmt = $conn->prepare("INSERT INTO files (filename, content, language) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $filename, $code, $language);
            $stmt->execute();
            $stmt->close();
        }

        // Execute the code and capture the output
        $output = "";
        $error = false;
        if ($language === 'php') {
            ob_start();
            try {
                eval($code);
            } catch (ParseError $e) {
                $output = "PHP Parse Error: " . $e->getMessage();
                $error = true;
            }
            $output = ob_get_clean();
        } elseif ($language === 'python') {
            // Save the Python code to a temporary file
            $tempFile = tempnam(sys_get_temp_dir(), 'python_code');
            file_put_contents($tempFile, $code);

            // Execute the Python code
            exec("python $tempFile 2>&1", $execOutput, $execReturn);
            $output = implode("\n", $execOutput);
            $error = $execReturn !== 0;

            // Clean up temporary file
            if (file_exists($tempFile)) {
                unlink($tempFile);
            }
        } elseif ($language === 'javascript') {
            // For JavaScript, you would need to use a different method to execute the code
            $output = "JavaScript code execution is not supported in this example.";
            $error = true;
        } elseif ($language === 'c') {
            // Save the C code to a temporary file
            $tempFile = tempnam(sys_get_temp_dir(), 'c_code');
            file_put_contents($tempFile, $code);

            // Compile the C code
            $outputFile = tempnam(sys_get_temp_dir(), 'c_output');
            exec("gcc $tempFile -o $outputFile 2>&1", $compileOutput, $compileReturn);

            if ($compileReturn === 0) {
                // Execute the compiled binary
                exec($outputFile, $execOutput, $execReturn);
                $output = implode("\n", $execOutput);
                $error = $execReturn !== 0;
            } else {
                $output = "Compilation Error:\n" . implode("\n", $compileOutput);
                $error = true;
            }

            // Clean up temporary files
            if (file_exists($tempFile)) {
                unlink($tempFile);
            }
            if (file_exists($outputFile)) {
                unlink($outputFile);
            }
        } elseif ($language === 'cpp') {
            // Save the C++ code to a temporary file
            $tempFile = tempnam(sys_get_temp_dir(), 'cpp_code');
            file_put_contents($tempFile, $code);

            // Compile the C++ code
            $outputFile = tempnam(sys_get_temp_dir(), 'cpp_output');
            exec("g++ $tempFile -o $outputFile 2>&1", $compileOutput, $compileReturn);

            if ($compileReturn === 0) {
                // Execute the compiled binary
                exec($outputFile, $execOutput, $execReturn);
                $output = implode("\n", $execOutput);
                $error = $execReturn !== 0;
            } else {
                $output = "Compilation Error:\n" . implode("\n", $compileOutput);
                $error = true;
            }

            // Clean up temporary files
            if (file_exists($tempFile)) {
                unlink($tempFile);
            }
            if (file_exists($outputFile)) {
                unlink($outputFile);
            }
        } elseif ($language === 'html') {
            // For HTML, you would need to use a different method to execute the code
            $output = "HTML code execution is not supported in this example.";
            $error = true;
        }

        // Check if the output is correct and without errors
        if (!$error && !in_array($filename . $output, $_SESSION['correct_outputs'])) {
            // Increment correct answers count
            $correct_answers++;
            $_SESSION['correct_outputs'][] = $filename . $output;

            // Update the database with the new correct answers count
            $stmt = $conn->prepare("INSERT INTO user_stats (user_id, correct_answers) VALUES (?, ?) ON DUPLICATE KEY UPDATE correct_answers = ?");
            $stmt->bind_param("iii", $_SESSION['user_id'], $correct_answers, $correct_answers);
            $stmt->execute();
            $stmt->close();
        }
    }
}

// Handle file saving
if (isset($_POST['save_file'])) {
    $filename = $_POST['filename'];
    $filecontent = $_POST['filecontent'];
    file_put_contents("saved_files/" . $filename, $filecontent);
}

// Handle file opening
if (isset($_POST['open_file'])) {
    $filename = $_POST['filename'];
    $stmt = $conn->prepare("SELECT content FROM files WHERE filename = ?");
    $stmt->bind_param("s", $filename);
    $stmt->execute();
    $stmt->bind_result($filecontent);
    $stmt->fetch();
    $stmt->close();

    if ($filecontent) {
        echo json_encode(['content' => $filecontent]);
    } else {
        echo json_encode(['error' => 'File not found']);
    }
}

// Fetch stats from the database
$stats_result = $conn->query("SELECT correct_answers, time_spent FROM user_stats ORDER by time_spent DESC LIMIT 1");
$stats = $stats_result->fetch_assoc();
$correct_answers = $stats ? $stats['correct_answers'] : 0;
$time_spent = $stats ? $stats['time_spent'] : '00:00:00';

// Fetch list of files from the new table
$files_result = $conn->query("SELECT filename FROM files");
$saved_files = [];
while ($row = $files_result->fetch_assoc()) {
    $saved_files[] = $row['filename'];
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Code Practice</title>
    <style>
        body {
            display: flex;
            justify-content: flex-start;
            align-items: flex-start;
        }
        .container {
            width: 60%;
            margin-left: 20px;
        }
        .file-manager {
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 20%;
            max-width: 200px;
            height: 100vh;
            overflow-y: auto;
        }
        .file-manager h2 {
            font-size: 1.5em;
            margin-bottom: 10px;
        }
        .file-manager ul {
            list-style-type: none;
            padding: 0;
        }
        .file-manager li {
            margin-bottom: 10px;
            position: relative;
        }
        .file-manager a.download-link {
            display: none;
            position: absolute;
            right: 0;
            top: 0;
            background-color: #f4f4f4;
            padding: 5px;
            border-radius: 4px;
            text-decoration: none;
            color: #000;
        }
        .file-manager li:hover a.download-link {
            display: inline;
        }
        .file-manager button {
            padding: 5px 10px;
            border-radius: 4px;
            border: 1px solid #ccc;
            background-color: #f4f4f4;
            cursor: pointer;
        }
        textarea {
            width: 100%;
            height: 200px;
            margin-bottom: 10px;
            padding: 10px;
            border-radius: 4px;
            border: 1px solid #ccc;
            font-family: monospace;
        }
        select, input[type="submit"] {
            padding: 10px;
            border-radius: 4px;
            border: 1px solid #ccc;
            margin-bottom: 10px;
            width: 100%;
        }
        .stats {
            margin-top: 20px;
            font-size: 1.2em;
        }
        .file-manager li:hover {
            background-color: #e0e0e0;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div class="file-manager">
        <h2>File Manager</h2>
        <ul id="file-list">
            <?php foreach ($saved_files as $file): ?>
                <li>
                    <?php echo htmlspecialchars($file); ?>
                    <a href="?download_file=<?php echo urlencode($file); ?>" class="download-link">Download</a>
                    <form method="post" style="display:inline;">
                        <input type="hidden" name="filename" value="<?php echo htmlspecialchars($file); ?>">
                        <button type="submit" name="delete_file">Delete</button>
                    </form>
                </li>
            <?php endforeach; ?>
        </ul>
        <button onclick="saveFile()">Save File</button>
        <button onclick="openFile()">Open File</button>
        <button onclick="window.location.href='s_code.php'">Refresh</button>
        <button onclick="window.location.href='Dashboard.php'">Back to Dashboard</button>
    </div>
    <div class="container">
        <h1>Code Practice</h1>
        <form method="post" id="codeForm">
            <input type="hidden" id="filename" name="filename" value="<?php echo isset($_POST['filename']) ? htmlspecialchars($_POST['filename']) : ''; ?>">
            <textarea id="code" name="code" placeholder="Write your code here..."><?php echo isset($_POST['code']) ? htmlspecialchars($_POST['code']) : ''; ?></textarea><br>
            <select name="language">
                <option value="php" <?php echo (isset($_POST['language']) && $_POST['language'] === 'php') ? 'selected' : ''; ?>>PHP</option>
                <option value="python" <?php echo (isset($_POST['language']) && $_POST['language'] === 'python') ? 'selected' : ''; ?>>Python</option>
                <option value="javascript" <?php echo (isset($_POST['language']) && $_POST['language'] === 'javascript') ? 'selected' : ''; ?>>JavaScript</option>
                <option value="c" <?php echo (isset($_POST['language']) && $_POST['language'] === 'c') ? 'selected' : ''; ?>>C</option>
                <option value="cpp" <?php echo (isset($_POST['language']) && $_POST['language'] === 'cpp') ? 'selected' : ''; ?>>C++</option>
                <option value="html" <?php echo (isset($_POST['language']) && $_POST['language'] === 'html') ? 'selected' : ''; ?>>HTML</option>
            </select><br>
            <input type="submit" name="submit_code" value="Evaluate">
            <button type="button" onclick="saveFile()">Save</button>
        </form>

        <h2>Output</h2>
        <textarea id="output" name="output"><?php echo htmlspecialchars($output); ?></textarea>

        <div class="stats">
            <p>Correct Answers: <span id="correct_answers"><?php echo $correct_answers; ?></span></p>
            <p>Time Spent: <span id="time_spent"><?php echo $time_spent; ?></span></p>
        </div>
    </div>

    <script>
        function getFileExtension(language) {
            switch (language) {
                case 'php':
                    return '.php';
                case 'python':
                    return '.py';
                case 'javascript':
                    return '.js';
                case 'c':
                    return '.c';
                case 'cpp':
                    return '.cpp';
                case 'html':
                    return '.html';
                default:
                    return '';
            }
        }

        function saveFile() {
            const language = document.querySelector('select[name="language"]').value;
            const extension = getFileExtension(language);
            let filename = prompt('Enter the filename to save:');
            if (filename && !filename.endsWith(extension)) {
                filename += extension;
            }
            const filecontent = document.getElementById('code').value;
            if (filename) {
                document.getElementById('filename').value = filename;
                fetch('', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: new URLSearchParams({
                        'save_file': true,
                        'filename': filename,
                        'filecontent': filecontent,
                        'language': language
                    })
                }).then(response => response.text())
                  .then(data => {
                      alert('File saved successfully!');
                      const fileList = document.getElementById('file-list');
                      const listItem = document.createElement('li');
                      listItem.textContent = filename;
                      fileList.appendChild(listItem);
                  });
            }
        }

        function openFile() {
            const filename = prompt('Enter the filename to open:');
            if (filename) {
                fetch('', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: new URLSearchParams({
                        'open_file': true,
                        'filename': filename
                    })
                }).then(response => response.json())
                  .then(data => {
                      if (data.error) {
                          alert(data.error);
                      } else {
                          document.getElementById('code').value = data.content;
                      }
                  });
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            const fileList = document.getElementById('file-list');
            fetchFileList();

            fileList.addEventListener('click', function(event) {
                if (event.target.tagName === 'LI') {
                    const filename = event.target.textContent;
                    openFileByName(filename);
                }
            });
        });

        function fetchFileList() {
            fetch('fetch_files.php')
                .then(response => response.json())
                .then(data => {
                    const fileList = document.getElementById('file-list');
                    fileList.innerHTML = '';
                    data.files.forEach(file => {
                        const listItem = document.createElement('li');
                        listItem.textContent = file;
                        fileList.appendChild(listItem);
                    });
                });
        }

        function openFileByName(filename) {
            fetch('', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: new URLSearchParams({
                    'open_file': true,
                    'filename': filename
                })
            }).then(response => response.json())
              .then(data => {
                  if (data.error) {
                      alert(data.error);
                  } else {
                      document.getElementById('code').value = data.content;
                  }
              });
        }
    </script>
</body>
</html>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Code Editor</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 20px;
            display: flex;
            flex-direction: row;
            align-items: flex-start;
            min-height: 100vh;
        }
        .editor-container {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 65%;
            max-width: 1000px;
            margin-left: 20px;
        }
        .sidebar {
            width: 30%;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
            border-radius: 8px;
            overflow-y: auto;
            max-height: 80vh;
        }
        textarea, input[type="text"] {
            width: 100%;
            margin-bottom: 15px;
            padding: 12px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-family: monospace;
            font-size: 14px;
            box-sizing: border-box;
        }
        select, button {
            padding: 12px 20px;
            margin-right: 10px;
            border: 1px solid #ccc;
            border-radius: 6px;
            background-color: #007bff;
            color: #fff;
            cursor: pointer;
            font-size: 14px;
            transition: background-color 0.3s ease;
        }
        button:hover {
            background-color: #0056b3;
        }
        #output {
            margin-top: 20px;
            padding: 15px;
            background-color: #1e1e1e;
            border-radius: 6px;
            border: 1px solid #ccc;
            font-family: monospace;
            color: #d4d4d4;
            white-space: pre-wrap;
            height: 200px;
            overflow-y: auto;
        }
        .success {
            color: #28a745;
        }
        .error {
            color: #dc3545;
        }
        h3 {
            font-size: 18px;
            color: #333;
            margin-bottom: 15px;
        }
        ul {
            list-style-type: none;
            padding: 0;
        }
        li {
            padding: 10px 0;
            font-size: 14px;
            color: #555;
            cursor: pointer;
            transition: color 0.3s ease;
        }
        li:hover {
            color: #007bff;
        }
        @media (max-width: 768px) {
            body {
                padding: 10px;
            }
            .editor-container, .sidebar {
                width: 100%;
                margin-right: 0;
            }
        }
    </style>
</head>
<body>
    <div class="editor-container">
        <form action="s_assessment.php" method="post">
            <input type="text" name="filename" placeholder="Enter filename with extension" value="<?php echo isset($_POST['filename']) ? htmlspecialchars($_POST['filename']) : ''; ?>"><br>
            <textarea name="code" placeholder="Write your code here..."><?php echo isset($_POST['code']) ? htmlspecialchars($_POST['code']) : ''; ?></textarea><br>
            <select name="language">
                <option value="python" <?php echo (isset($_POST['language']) && $_POST['language'] == 'python') ? 'selected' : ''; ?>>Python</option>
                <option value="javascript" <?php echo (isset($_POST['language']) && $_POST['language'] == 'javascript') ? 'selected' : ''; ?>>JavaScript</option>
                <option value="php" <?php echo (isset($_POST['language']) && $_POST['language'] == 'php') ? 'selected' : ''; ?>>PHP</option>
                <option value="c" <?php echo (isset($_POST['language']) && $_POST['language'] == 'c') ? 'selected' : ''; ?>>C</option>
                <option value="cpp" <?php echo (isset($_POST['language']) && $_POST['language'] == 'cpp') ? 'selected' : ''; ?>>C++</option>
                <option value="sql" <?php echo (isset($_POST['language']) && $_POST['language'] == 'sql') ? 'selected' : ''; ?>>SQL</option>
            </select>
            <button type="submit" name="action" value="save">Save</button>
            <button type="submit" name="action" value="run">Run</button>
        </form>
        <div id="output" class="<?php echo isset($outputClass) ? $outputClass : ''; ?>">
            <?php
            ini_set('display_errors', 1);
            ini_set('display_startup_errors', 1);
            error_reporting(E_ALL);

            // Database connection parameters
            $host = 'localhost'; // Database host
            $db = 'progresspulse'; // Database name
            $user = 'root'; // Database username
            $pass = ''; // Database password
            $charset = 'utf8mb4';

            $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ];

            try {
                $pdo = new PDO($dsn, $user, $pass, $options);
            } catch (\PDOException $e) {
                echo "<span class='error'>Database connection failed: " . htmlspecialchars($e->getMessage()) . "</span>";
                exit;
            }

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $action = $_POST['action'];
                $filename = $_POST['filename'];
                $code = $_POST['code'];
                $language = $_POST['language'];

                if ($action === 'save') {
                    // Save code to the database
                    $stmt = $pdo->prepare("INSERT INTO code_files (filename, code, language) VALUES (:filename, :code, :language)");
                    $stmt->execute(['filename' => $filename, 'code' => $code, 'language' => $language]);
                    echo "<span class='success'>Code saved successfully as $filename.</span>";
                    // Refresh the file list after saving
                    echo "<script>window.location.reload();</script>";
                } elseif ($action === 'run') {
                    // Execute the code
                    $output = '';
                    $outputClass = 'success'; // Default to success

                    try {
                        if ($language === 'python') {
                            $tempFile = tempnam(sys_get_temp_dir(), 'code') . '.py';
                            file_put_contents($tempFile, $code);
                            $command = escapeshellcmd("python $tempFile 2>&1");
                            $output = shell_exec($command);
                            unlink($tempFile);
                        } elseif ($language === 'javascript') {
                            $tempFile = tempnam(sys_get_temp_dir(), 'code') . '.js';
                            file_put_contents($tempFile, $code);
                            $command = escapeshellcmd("node $tempFile 2>&1");
                            $output = shell_exec($command);
                            unlink($tempFile);
                        } elseif ($language === 'php') {
                            $tempFile = tempnam(sys_get_temp_dir(), 'code') . '.php';
                            file_put_contents($tempFile, $code);
                            $command = escapeshellcmd("php $tempFile 2>&1");
                            $output = shell_exec($command);
                            unlink($tempFile);
                        } elseif ($language === 'c') {
                            $tempFile = tempnam(sys_get_temp_dir(), 'code') . '.c';
                            file_put_contents($tempFile, $code);
                            $outputFile = tempnam(sys_get_temp_dir(), 'output');
                            $command = escapeshellcmd("gcc $tempFile -o $outputFile 2>&1 && $outputFile");
                            $output = shell_exec($command);
                            unlink($tempFile);
                            unlink($outputFile);
                        } elseif ($language === 'cpp') {
                            $tempFile = tempnam(sys_get_temp_dir(), 'code') . '.cpp';
                            file_put_contents($tempFile, $code);
                            $outputFile = tempnam(sys_get_temp_dir(), 'output');
                            $command = escapeshellcmd("g++ $tempFile -o $outputFile 2>&1 && $outputFile");
                            $output = shell_exec($command);
                            unlink($tempFile);
                            unlink($outputFile);
                        } elseif ($language === 'sql') {
                            // For SQL, you would need a database connection and execute the SQL code
                            // This is a placeholder for SQL execution logic
                            $output = "SQL execution is not implemented.";
                        }

                        // Determine if the output contains errors
                        if ($output !== null && strpos(strtolower($output), 'error') !== false) {
                            $outputClass = 'error';
                        }
                    } catch (Exception $e) {
                        $output = "An error occurred: " . $e->getMessage();
                        $outputClass = 'error';
                    }

                    // Display the output or error
                    if ($output !== null) {
                        echo "<span class='$outputClass'>" . nl2br(htmlspecialchars($output)) . "</span>";
                        // Display the raw output for debugging
                        echo "<pre>" . htmlspecialchars($output) . "</pre>";
                    }
                } elseif ($action === 'open_in_terminal') {
                    $stmt = $pdo->prepare("SELECT code, language FROM code_files WHERE filename = :filename");
                    $stmt->execute(['filename' => $filename]);
                    $file = $stmt->fetch();
                    if ($file) {
                        $language = $file['language'];
                        $code = $file['code'];
                        $tempFile = tempnam(sys_get_temp_dir(), 'code') . ".$language";
                        file_put_contents($tempFile, $code);
                        $command = escapeshellcmd("start powershell -NoExit -Command \"cd $tempFile\"");
                        shell_exec($command);
                    }
                }
            }
            ?>
        </div>
    </div>
    <div class="sidebar">
        <h3>Existing Files:</h3>
        <ul id="file-list">
            <?php
            // Display existing files
            $stmt = $pdo->query("SELECT filename FROM code_files");
            while ($row = $stmt->fetch()) {
                echo "<li onclick=\"loadFile('" . htmlspecialchars($row['filename']) . "')\">" . htmlspecialchars($row['filename']) . "</li>";
            }
            ?>
        </ul>
    </div>
    <script>
        function loadFile(filename) {
            console.log('Loading file:', filename); // Debugging
            const formData = new FormData();
            formData.append('action', 'load');
            formData.append('filename', filename);

            fetch('s_assessment.php', {
                method: 'POST',
                body: formData
            })
            .then(response => {
                console.log('Response received:', response); // Debugging
                return response.json();
            })
            .then(data => {
                console.log('Data received:', data); // Debugging
                if (data.error) {
                    alert(data.error);
                } else {
                    document.querySelector('input[name="filename"]').value = filename;
                    document.querySelector('textarea[name="code"]').value = data.code;
                    document.querySelector('select[name="language"]').value = data.language;
                    // Open the file in the terminal
                    fetch('s_assessment.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: new URLSearchParams({ action: 'open_in_terminal', filename: filename })
                    });
                }
            })
            .catch(error => console.error('Error loading file:', error));
        }
    </script>
</body>
</html>

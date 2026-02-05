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

// SQL query to fetch all student details and progress
$sql = "SELECT mystudents.id, mystudents.name, COALESCE(user_stats.correct_answers, 0) AS correct_answers FROM mystudents LEFT JOIN user_stats ON mystudents.id = user_stats.user_id";
$result = $conn->query($sql);

// Initialize an array to store student data
$students = [];

if ($result->num_rows > 0) {
    // Fetch data and store it in the $students array
    while($row = $result->fetch_assoc()) {
        $progress = $row['correct_answers'] / 10; // Calculate progress percentage
        $students[] = ['name' => $row["name"], 'progress' => $progress];
    }
} else {
    echo "No students found";
}

// SQL query to fetch documents
$sql_documents = "SELECT title, file_path, access_level, members, size FROM tdocuments";
$documents_result = $conn->query($sql_documents);

$documents = [];
if ($documents_result->num_rows > 0) {
    while($row = $documents_result->fetch_assoc()) {
        $documents[] = [
            'title' => $row['title'],
            'file' => $row['file_path'],
            'access' => $row['access_level'],
            'members' => $row['members'],
            'size' => $row['size'] . ' MB'
        ];
    }
} else {
    echo "No documents found";
}

// Update progress in mystudents table based on correct_answers
$updateProgressQuery = "
    UPDATE mystudents
    JOIN user_stats ON mystudents.id = user_stats.user_id
    SET mystudents.progress = user_stats.correct_answers / 10;
";
$conn->query($updateProgressQuery);

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Dashboard</title>
    <style>
        body {
            margin: 0;
            font-family: 'Segoe UI', sans-serif;
            background: #f5f7fb;
        }
        .sidebar {
            width: 200px;
            height: 100vh;
            background: #1e255e;
            color: white;
            position: fixed;
            top: 0;
            left: 0;
            padding-top: 30px;
        }
        .sidebar a {
            color: white;
            text-decoration: none;
            display: block;
            padding: 15px 20px;
        }
        .sidebar a:hover {
            background: #3b447a;
        }
        .main {
            margin-left: 220px;
            padding: 30px;
        }
        .card {
            background: white;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .students .student {
            margin-bottom: 15px;
        }
        .progress-bar {
            height: 6px;
            border-radius: 4px;
            background: #ddd;
            overflow: hidden;
        }
        .progress-bar span {
            display: block;
            height: 100%;
        }
        .badge {
            padding: 4px 8px;
            color: white;
            border-radius: 4px;
        }
        .A1 { background-color: #f6b93b; }
        .B1 { background-color: #eb3b5a; }
        .C2 { background-color: #38ada9; }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            text-align: left;
            padding: 10px;
        }
        th {
            background: #f0f0f0;
        }
        #workingProgress {
            font-size: 24px;
            font-weight: bold;
            color: #4b7bec;
        }
    </style>
</head>
<body>

<div class="sidebar">
    <a href="">Dashboard</a>
    <a href="mystudents.php">My Students</a>
    <a href="TDocuments.php">Documents</a>
    <a href="tprofile.php">profile</a>
</div>+

<div class="main">
    <div class="card">
        <h2>Welcome Back, <span style="color:#4b7bec;">Tiana!</span></h2>
        <p>Your Students completed <strong id="taskCompletion">...</strong> of the tasks. Progress is <strong id="progressNote"></strong></p>
    </div>

    <div class="card">
        <h3>TOP Students</h3>
        <div id="studentList" class="students"></div>
    </div>

    <div class="card">
        <h3>Working Hours</h3>
        <p>Progress: <span id="workingProgress">...</span>%</p>
    </div>

    <div class="card">
        <h3>Documents</h3>
        <table>
            <thead>
                <tr>
                    <th>Level</th>
                    <th>Title</th>
                    <th>File</th>
                    <th>Access</th>
                    <th>Members</th>
                    <th>Size</th>
                </tr>
            </thead>
            <tbody id="docTableBody"></tbody>
        </table>
    </div>
</div>

<script>
    const students = <?php echo json_encode($students); ?>;
    const documents = <?php echo json_encode($documents); ?>;

    const studentList = document.getElementById('studentList');
    const taskCompletion = document.getElementById('taskCompletion');
    const progressNote = document.getElementById('progressNote');
    const docTableBody = document.getElementById('docTableBody');
    const workingProgress = document.getElementById('workingProgress');

    // Load students dynamically
    let totalProgress = 0;
    students.forEach(student => {
        totalProgress += student.progress;
        const studentDiv = document.createElement('div');
        studentDiv.classList.add('student');
        studentDiv.innerHTML = `
            <strong>${student.name}</strong>
            <div class="progress-bar">
                <span style="width: ${student.progress}%; background: ${
                    student.progress >= 80 ? '#20bf6b' :
                    student.progress >= 65 ? '#f6b93b' : '#eb3b5a'
                };"></span>
            </div>
            <small>${student.progress}%</small>
        `;
        studentList.appendChild(studentDiv);
    });

    // Task completion summary
    const average = Math.round(totalProgress / students.length);
    taskCompletion.textContent = `${average}%`;
    progressNote.textContent = average >= 80 ? "very good!" : average >= 60 ? "good" : "needs improvement";
    progressNote.style.color = average >= 80 ? "#20bf6b" : average >= 60 ? "#f6b93b" : "#eb3b5a";

    // Working progress simulation
    const workingHours = Math.floor(Math.random() * 20) + 75;
    workingProgress.textContent = workingHours;

    // Document rendering
    documents.forEach(doc => {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td><span class="badge ${doc.level}">${doc.level}</span></td>
            <td>${doc.title}</td>
            <td>${doc.file}</td>
            <td>${doc.access}</td>
            <td>${doc.members}</td>
            <td>${doc.size}</td>
        `;
        docTableBody.appendChild(row);
    });
</script>

</body>
</html>

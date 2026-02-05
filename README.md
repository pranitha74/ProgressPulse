# ProgressPulse

**Feel the Pulse of Student Progress: Real-time Insights and Analytics**

ProgressPulse is a web-based educational platform that connects administrators, teachers, and students with role-based dashboards, document sharing, code practice, and progress tracking.

---

## Features

### For Everyone
- **Landing page** — Clear entry point with role-based login links
- **University registration** — Sign up with email verification (PHPMailer)

### Administrator
- Secure admin login
- **Admin dashboard** — View students and teachers, manage account
- Change password
- Oversight of platform users and data

### Staff (Teachers)
- Teacher login
- **Teacher dashboard** — Student list with progress (e.g. correct answers / 10)
- **Documents** — Upload and manage documents (title, access level, members, size)
- View documents sent to students and their replies/download times
- Student details and progress overview

### Students
- Student login
- **Student dashboard** — Overview cards (GPA, attendance, tuition status)
- **Academic grades** — Course table (ID, course name, semester, credits, points, grade)
- **Code** — Code editor with run/download/delete, correct-answer tracking and progress
- **Documents** — View and download teacher documents, submit replies
- **User profile** — View profile and change password
- Assessments and communication with teachers

---

## Tech Stack

- **Backend:** PHP
- **Database:** MySQL (database name: `progresspulse`)
- **Frontend:** HTML, CSS, JavaScript
- **Email:** PHPMailer (for verification emails)

---

## Requirements

- PHP 7.4+ (with mysqli, sessions, file uploads)
- MySQL 5.7+ or MariaDB
- Web server (e.g. Apache with mod_rewrite) or XAMPP/WAMP
- Composer (optional, for PHPMailer if installed via Composer)

---

## Installation

### 1. Clone the repository

```bash
git clone https://github.com/Nousheen47/ProgressPulse.git
cd ProgressPulse
```

### 2. Database setup

1. Create a MySQL database named `progresspulse`.
2. Create the required tables (e.g. `studentlogin`, `admin_login`, `teacherlogin`, `teachers_p`, `mystudents`, `user_stats`, `tdocuments`, `studentdocuments`, `document_replies`, `signup`, `files`). Table structures should match the queries used in the PHP files (see the project source for exact column names and types).

### 3. Web server

- Place the project in your web server document root (e.g. `htdocs` for XAMPP).
- Ensure the server is configured to run PHP and to use the database credentials below (or update them in the PHP files).

### 4. Configuration

- **Database:** In the PHP files that connect to MySQL, the default credentials are:
  - Host: `localhost`
  - User: `root`
  - Password: *(empty)*
  - Database: `progresspulse`  
  Update these in the relevant files if your environment differs.

- **Email (registration):** In `register.php`, set your SMTP details:
  - Gmail (or other) address and password/app password
  - Update `setFrom` and `addAddress` as needed for verification emails.

### 5. PHPMailer (for registration emails)

If you use PHPMailer, either:

- Place the PHPMailer files in a `PHPMailer` folder and use `require 'PHPMailer/PHPMailerAutoload.php';` as in the project, or  
- Install via Composer and adjust the `require` and autoload in `register.php`.

### 6. Run the application

- Open `http://localhost/ProgressPulse/home.html` (or your project URL) in a browser.
- Use **Create a Free Account** for registration or the role-specific login links (Administrator, Staff, Student).

---

## Project structure (overview)

| Path / file           | Purpose |
|-----------------------|--------|
| `home.html`           | Landing page |
| `register.php`        | University signup + email verification |
| `verify_input.php` / `verify.php` | Verification flow |
| `Adminlogin.php`      | Admin login |
| `ADashboard.php`      | Admin dashboard |
| `teacherlogin.php`    | Teacher login |
| `TDashboard.php`      | Teacher dashboard |
| `TDocuments.php`      | Teacher documents (upload, list) |
| `mystudents.php`      | Student list (e.g. for admin/teacher) |
| `studentlogin.php`    | Student login |
| `Dashboard.php`       | Student dashboard |
| `academic_grades.php`  | Academic grades (student) |
| `s_code.php`          | Student code editor / execution |
| `s_assessment.php`    | Student assessment |
| `studentdocument.php` | Student documents view |
| `user_profile.php`    | Student profile / password change |
| `reply.php` / `sendToTeacher.php` | Document replies / sending |
| `logout.php`          | Logout |
| `data.php`            | Sample/seed data (e.g. teachers) |
| `frontend/`           | Duplicate/copy of main front-end assets |

---

## Database (main entities)

- **studentlogin** — Student credentials (e.g. username, email, password)
- **admin_login** — Admin credentials
- **teacherlogin** — Teacher credentials
- **teachers_p** — Teacher profile (name, profession)
- **mystudents** — Student list and progress
- **user_stats** — e.g. correct answers for progress calculation
- **tdocuments** — Teacher-uploaded documents
- **studentdocuments** / **document_replies** — Student documents and replies
- **signup** — Registration and verification codes
- **files** — Stored file content for code/download features

---

## Security notes

- Use **prepared statements** and avoid concatenating user input into SQL (some files still use raw input; consider refactoring).
- Store **passwords** with `password_hash()` / `password_verify()` (e.g. in admin and teacher flows) and avoid plain text.
- Do not commit real credentials; use environment variables or a local config file excluded from version control.
- Keep PHPMailer and PHP updated for security.

---

## License

This project is open source. Use and modify according to your needs.

---

## Contributing

1. Fork the repository.
2. Create a branch for your feature or fix.
3. Submit a pull request to [Nousheen47/ProgressPulse](https://github.com/Nousheen47/ProgressPulse).

---

## Repository

**GitHub:** [https://github.com/Nousheen47/ProgressPulse](https://github.com/Nousheen47/ProgressPulse)

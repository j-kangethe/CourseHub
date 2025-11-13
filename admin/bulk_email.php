<?php 
include '../config/db.php';
include '../auth/auth_guard.php';
require_role('admin');

require '../vendor/phpmailer/phpmailer/src/Exception.php';
require '../vendor/phpmailer/phpmailer/src/PHPMailer.php';
require '../vendor/phpmailer/phpmailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

$success_message = '';
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === "POST"){
    $subject = trim($_POST['subject']);
    $message = trim($_POST['message']);

    if ($subject === '' || $message === ''){
        $error_message = "Subject and message are required.";
    }
    else{
        // FIXED: prepare correctly
        $stmt = $mysqli->prepare("SELECT id, fullname, email FROM users WHERE role = 'student'");
        $stmt->execute();

        // FIXED: get result set correctly
        $students = $stmt->get_result();

        // FIXED: check result set properly
        if ($students->num_rows === 0){
            $error_message = "No students found to send email to.";
        }
        else{
            $mail = new PHPMailer(true);

            try {
                $mail->isSMTP();
                $mail->Host       = "smtp.gmail.com";
                $mail->SMTPAuth   = true;
                $mail->Username   = "jeffkangethek@gmail.com";
                $mail->Password   = "ptmlfksqmndhrtyo"; // REMOVE SPACES
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port       = 587;

                $mail->setFrom("jeffkangethek@gmail.com", "CourseHub Admin");
                $mail->isHTML(true);
                $mail->Subject = $subject;

                $admin_id = $_SESSION['user_id'];
                $sent     = 0;
                $failed   = 0;

                // FIXED: loop through $students, not $result
                while ($row = $students->fetch_assoc()){

                    $receiver_id    = (int)$row['id'];
                    $receiver_email = $row['email'];

                    $mail->clearAddresses();
                    $mail->addAddress($receiver_email);
                    $mail->Body = nl2br($message);

                    try {
                        $mail->send();
                        $sent++;

                        // Save log
                        $log = $mysqli->prepare("
                            INSERT INTO communications (sender_id, receiver_id, type, subject, message)
                            VALUES (?, ?, 'email', ?, ?)
                        ");
                        $log->bind_param("iiss", $admin_id, $receiver_id, $subject, $message);
                        $log->execute();

                    } catch (Exception $e) {
                        $failed++;
                    }
                }

                $success_message = "Bulk email completed. Sent: $sent, Failed: $failed.";

            } catch (Exception $e){
                $error_message = "Bulk email could not be started. Error: " . $e->getMessage();
            }
        }
    }
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin | Send Email</title>
     <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            background-color: #f9fafb;
            font-family: 'Inter', sans-serif;
        }
        .sidebar {
            background: linear-gradient(180deg, #16a34a, #15803d);
        }
        .active-link {
            background-color: rgba(255, 255, 255, 0.2);
        }
    </style>
</head>
<body class="flex h-screen">
      <aside class="sidebar text-white w-64 flex flex-col justify-between p-5">
        <div>
            <h2 class="text-2xl font-bold mb-3" style="text-align: start;">CourseHub</h2>
            <nav class="space-y-2">
                <a href="dashboard.php" class="block px-2 py-1 rounded hover:bg-green-700">Dashboard</a>
                <a href="add_course.php" class="block px-2 py-1 rounded hover:bg-green-700">Add Course</a>
                <a href="add_content.php" class="block px-2 py-1 rounded hover:bg-green-700">Add Content</a>
                <!-- <a href="edit_course.php" class="block px-2 py-1 rounded hover:bg-green-700">Edit Course</a> -->
                <a href="create_admin.php" class="block px-2 py-1 rounded hover:bg-green-700">Create an Admin</a>
                <a href="manage_courses.php" class="block px-2 py-1 rounded hover:bg-green-700">Manage Courses</a>

                <div class="text-white communications py-2">
                    <h4 style="font-size: 17px;" class="font-bold">Communications</h4>

                    <div class="flex flex-col ml-3 space-y-1 py-2">
                        <a href="send_email.php" class="block px-2 py-1 rounded hover:bg-green-700">Emails</a>
                        <a href="bulk_email.php" class="block px-2 py-1 rounded hover:bg-green-700 active-link">Bulk Emails</a>
                        <a href="dashboard.php" class="block px-2 py-1 rounded hover:bg-green-700">SMS</a>
                    </div>
                </div>


        
            </nav>
        </div>


        <div class=" text-white">
            <div class="flex items-center justify-between cursor-pointer" id="userToggle">
                <p class="font-semibold"><?= htmlspecialchars($_SESSION['fullname']); ?></p>
                <span id="arrow" class="transition-transform duration-500">▼</span>
            </div>

            <!-- Dropdown -->

            <div  id="userDropdown" class="origin-top scale-y-0 opacity-0 left-0 mt-1 w-60 bg-green-700 text-white rounded-lg shadow-lg p-4 z-50 transform transition-all duration-500">
                <p class="text-sm mb-1">Logged in as: </p>
                <p class="font-semibold"><?= htmlspecialchars($_SESSION['email']); ?></p>
                <p class="text-xs text-green-100 mb-2"><?= htmlspecialchars($_SESSION['registration_no']); ?></p>
                <p class="text-xs border border-green-400 p-1 rounded mb-3"><?= htmlspecialchars($_SESSION['role']); ?></p>
                <a href="../auth/logout.php" class="block text-center bg-red-500 hover: bg-red-600 px-3 py-2 rounded font-semibold">Logout</a>
            </div>
        </div>
    </aside>   
    
    <main class="flex-1 p-10 overflow-y-auto min-h-screen flex flex-col items-center py-10">
        <div class="w-full max-w-lg bg-white p-8 rounded-xl shadow-2xl">
            <h1 class="text-xl font-bold mb-4">Send Bulk Email to All Students</h1>

            <?php if ($success_message): ?>
                <div class="bg-green-100 text-green-800 p-3 rounded mb-4">
                    <?= htmlspecialchars($success_message) ?>
                </div>
            <?php endif; ?>

            <?php if ($error_message): ?>
                <div class="bg-red-100 text-red-800 p-3 rounded mb-4">
                    <?= htmlspecialchars($error_message) ?>
                </div>
            <?php endif; ?>

            <form action="bulk_email.php" method="POST" class="space-y-4">
                <p class="text-sm text-gray-600">
                    This email will be sent to <strong>all users with role "student"</strong>.
                </p>

                <div>
                    <label class="font-semibold">Subject</label>
                    <input type="text" name="subject" class="w-full border rounded px-3 py-2" required>
                </div>

                <div>
                    <label class="font-semibold">Message</label>
                    <textarea name="message" rows="6" class="w-full border rounded px-3 py-2" required></textarea>
                </div>

                <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white py-2 rounded font-semibold">
                    Send Bulk Email
                </button>
            </form>
        </div>
    </main>

            <script>
        const toggle = document.getElementById('userToggle');
        const dropdown = document.getElementById('userDropdown');
        const arrow = document.getElementById('arrow');

        toggle.addEventListener('click', ()=>{
            const isOpen = dropdown.classList.contains('scale-y-100');
            if (isOpen){
                dropdown.classList.replace('scale-y-100', 'scale-y-0');
                dropdown.classList.replace('opacity-100', 'opacity-0');
                arrow.classList.remove('rotate-200');
            }
            else{
                dropdown.classList.replace('scale-y-0', 'scale-y-100');
                dropdown.classList.replace('opacity-0', 'opacity-100');
                arrow.classList.add('rotate-200');
            }
        });

        // Close on clicking outside

        document.addEventListener('click', ()=>{
            if (!toggle.contains(e.target) && !dropdown.contains(e.target)){
                dropdown.classList.replace('scale-y-100','scale-y-0');
                dropdown.classList.replace('opacity-100', 'opacity-0');
                arrow.classList.remove('rotate-200')
            }
        });
    </script>
</body>
</html>
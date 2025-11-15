<?php 
require_once '../config/db.php';
require_once '../auth/auth_guard.php';
require_role('admin');

$success_message = '';
$error_message = '';

global $mysqli;

$courses = $mysqli->query("SELECT id, title FROM courses ORDER BY title ASC")->fetch_all(MYSQLI_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === "POST") {
    $course_id = intval($_POST['course_id']);
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $uploaded_by = $_SESSION['user_id'];

    // File Data
    $file = $_FILES['file'];
    $file_name = $file['name'];
    $file_tmp = $file['tmp_name'];
    $file_size = $file['size'];
    $file_error = $file['error'];

    $allowed_types = ['pdf', 'doc', 'docx', 'ppt', 'pptx', 'mp4', 'zip', 'jpg', 'png'];
    $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

    // 🧠 Validation checks
    if (empty($title) || empty($course_id) || $file_error !== 0) {
        $error_message = "Please fill all fields and select a valid file.";
    }
    elseif (!in_array($file_ext, $allowed_types)) {
        $error_message = "Unsupported file type: .$file_ext";
    }
    elseif ($file_size > 15 * 1024 * 1024) { // 15 MB limit
        $error_message = "File is too large (Max 15MB).";
    }
    else {
        // ✅ Ensure uploads folder exists
        $upload_dir = __DIR__ . '/../uploads/';
        if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true); // <-- FIXED permissions + trailing slash

        // ✅ Build unique filename
        $new_file_name = uniqid("content_", true) . '.' . $file_ext;
        $file_path = $upload_dir . $new_file_name;

        // ✅ Move uploaded file
        if (move_uploaded_file($file_tmp, $file_path)) {
            try {
                $stmt = $mysqli->prepare("
                    INSERT INTO content 
                    (course_id, title, description, file_path, file_type, uploaded_by, uploaded_at) 
                    VALUES (?, ?, ?, ?, ?, ?, NOW())
                ");
                $stmt->bind_param("issssi", $course_id, $title, $description, $file_path, $file_ext, $uploaded_by);
                $stmt->execute();

                $success_message = "✅ Content uploaded successfully!";
            } catch (Exception $e) {
                $error_message = "Database Error: " . $e->getMessage();
            }
        } else {
            $error_message = "File upload failed. Please try again.";
        }
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin | Add Content</title>
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
                <a href="add_content.php" class="block px-2 py-1 rounded hover:bg-green-700 active-link">Add Content</a>
                <!-- <a href="edit_course.php" class="block px-2 py-1 rounded hover:bg-green-700">Edit Course</a> -->
                <a href="create_admin.php" class="block px-2 py-1 rounded hover:bg-green-700">Create an Admin</a>
                <a href="manage_courses.php" class="block px-2 py-1 rounded hover:bg-green-700">Manage Courses</a>

                <div class="text-white communications py-2">
                    <h4 style="font-size: 17px;" class="font-bold">Communications</h4>

                    <div class="flex flex-col ml-3 space-y-1 py-2">
                        <a href="send_email.php" class="block px-2 py-1 rounded hover:bg-green-700">Emails</a>
                        <a href="bulk_email.php" class="block px-2 py-1 rounded hover:bg-green-700">Bulk Emails</a>
                        <a href="dashboard.php" class="block px-2 py-1 rounded hover:bg-green-700">SMS</a>
                        <a href="announcement.php" class="block px-2 py-1 rounded hover:bg-green-700">Announcements</a>
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
    
    <main class="flex-1 p-10 overflow-y-auto min-h-screen flex flex-col items-center py-10 ">
        <div class="w-full max-w-lg bg-white p-8 rounded-xl shadow-2xl">
    <h1 class="text-2xl font-bold text-center text-green-600 mb-6">Upload Course Content</h1>

    <?php if ($error_message): ?>
      <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
        <strong>Error:</strong> <?= htmlspecialchars($error_message) ?>
      </div>
    <?php endif; ?>

    <?php if ($success_message): ?>
      <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
        <strong>Success:</strong> <?= htmlspecialchars($success_message) ?>
      </div>
    <?php endif; ?>

    <form action="" method="POST" enctype="multipart/form-data" class="space-y-6">
      
      <div>
        <label for="course_id" class="block text-sm font-medium text-gray-700">Select Course:</label>
        <select id="course_id" name="course_id" required
                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">
          <option value="" style="text-align: center;">Choose a Course</option>
          <?php foreach ($courses as $course): ?>
            <option value="<?= $course['id'] ?>"><?= htmlspecialchars($course['title']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>

      <div>
        <label for="title" class="block text-sm font-medium text-gray-700">Content Title:</label>
        <input type="text" id="title" name="title" required
               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">
      </div>

      <div>
        <label for="description" class="block text-sm font-medium text-gray-700">Description (optional):</label>
        <textarea id="description" name="description" rows="3"
                  class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500"></textarea>
      </div>

      <div>
        <label for="file" class="block text-sm font-medium text-gray-700">Select File:</label>
        <input type="file" id="file" name="file" required
               class="mt-2 block w-full text-sm text-gray-700 border border-gray-300 rounded-md p-2 cursor-pointer bg-gray-50">
        <p class="text-xs text-gray-500 mt-1">Allowed: PDF, DOCX, PPTX, MP4, ZIP, JPG, PNG (max 15MB)</p>
      </div>

      <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white py-2 px-4 rounded-md font-semibold">
        Upload Content
      </button>

      <!-- <div class="text-center mt-4">
        <a href="dashboard.php" class="text-sm text-green-600 hover:text-green-800">← Back to Dashboard</a>
      </div> -->
    </form>
  </div>
    </main>
</body>
</html>
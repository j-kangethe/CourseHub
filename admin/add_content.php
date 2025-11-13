<?php 
require_once '../config/db.php';
require_once '../auth/auth_guard.php';
require_role('admin');

$success_message = '';
$error_message = '';

global $mysqli;

// ✅ Fetch all available courses for dropdown
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
  <title>Upload Course Content | CourseHub Admin</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap');
    body { font-family: 'Inter', sans-serif; background-color: #f9fafb; }
  </style>
</head>
<body class="min-h-screen flex items-center justify-center bg-gray-100">

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

      <div class="text-center mt-4">
        <a href="dashboard.php" class="text-sm text-green-600 hover:text-green-800">← Back to Dashboard</a>
      </div>
    </form>
  </div>

</body>
</html>

<?php 
require_once '../config/db.php';
require_once '../auth/auth_guard.php';
require_role('admin');

global $mysqli;

$success_message = '';
$error_message = '';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Invalid course ID.");
}

$course_id = intval($_GET['id']);

$stmt = $mysqli->prepare("SELECT * FROM courses WHERE id = ? ");
$stmt->bind_param("i", $course_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0){
    die("Course not found");
}

$course = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === "POST"){
    $title = trim(($_POST['title']));
    $description = trim($_POST['description']);
    $is_published = isset($_POST['is_published']) ? 1 : 0;

    if (empty($title)){
        $error_message = "Title Cannot be empty";
    }
    else{
        $update = $mysqli->prepare("UPDATE courses SET title = ?, description = ?, is_published = ?, updated_at = NOW() WHERE id = ?");
        $update->bind_param("ssii", $title, $description, $is_published, $course_id);
        if($update->execute()){
            $success_message = "Course updated successfully";
            $course['title'] = $title;
            $course['description'] = $description;
            $course['is_published'] = $is_published;
        }
        else{
            $error_message = "Database error: " . $mysqli->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin | Edit Course</title>
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
                <!-- <a href="edit_course.php" class="block px-2 py-1 rounded hover:bg-green-700 active-link">Edit Course</a> -->
                <a href="create_admin.php" class="block px-2 py-1 rounded hover:bg-green-700">Create an Admin</a>
                <a href="manage_courses.php" class="block px-2 py-1 rounded hover:bg-green-700">Manage Courses</a>

                <div class="text-white communications py-2">
                    <h4 style="font-size: 17px;" class="font-bold">Communications</h4>

                    <div class="flex flex-col ml-3 space-y-1 py-2">
                        <a href="dashboard.php" class="block px-2 py-1 rounded hover:bg-green-700">Emails</a>
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

    <main class="flex-1 p-10 overflow-y-auto min-h-screen flex flex-col items-center py-10 ">
      <div class="bg-white shadow-2xl p-8 rounded-xl w-full max-w-lg">
    <h1 class="text-2xl font-bold text-center text-green-600 mb-6">Edit Course</h1>

    <?php if ($error_message): ?>
      <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
        <?= htmlspecialchars($error_message) ?>
      </div>
    <?php endif; ?>

    <?php if ($success_message): ?>
      <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
        <?= htmlspecialchars($success_message) ?>
      </div>
    <?php endif; ?>

    <form action="" method="POST" class="space-y-6">
      <div>
        <label class="block text-sm font-medium text-gray-700">Course Title:</label>
        <input type="text" name="title" value="<?= htmlspecialchars($course['title']) ?>"
          class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 shadow-sm focus:ring-green-500 focus:border-green-500">
      </div>

      <div>
        <label class="block text-sm font-medium text-gray-700">Description:</label>
        <textarea name="description" rows="3"
          class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 shadow-sm focus:ring-green-500 focus:border-green-500"><?= htmlspecialchars($course['description']) ?></textarea>
      </div>

      <div class="flex items-center space-x-2">
        <input type="checkbox" name="is_published" id="is_published" <?= $course['is_published'] ? 'checked' : '' ?>>
        <label for="is_published" class="text-gray-700">Published</label>
      </div>

      <button type="submit" class="w-full bg-green-600 text-white py-2 px-4 rounded-md hover:bg-green-700 font-semibold">
        Save Changes
      </button>

      <div class="text-center mt-4">
        <a href="manage_courses.php" class="text-green-600 hover:underline">← Back to Manage Courses</a>
      </div>
    </form>
  </div>
    </main>
</body>
</html>
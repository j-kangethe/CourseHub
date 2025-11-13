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
    <title>Edit Course | CourseHub</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="flex items-center justify-center min-h-screen bg-gray-100 font-[Inter]">
  <div class="bg-white shadow-2xl p-8 rounded-xl w-full max-w-lg">
    <h1 class="text-2xl font-bold text-center text-green-600 mb-6">✏️ Edit Course</h1>

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
</body>
</html>
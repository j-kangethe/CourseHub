<?php
require_once '../config/db.php';
require_once '../auth/auth_guard.php';
require_role('admin');

$success_message = '';
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === "POST"){
    global $mysqli;

    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $is_published = isset($_POST['is_published']) ? 1:0;
    $instructor_id = $_SESSION['user_id'];

    if  (empty($title) || empty($description)){
        $error_message = "Title and description are required";
    }
    else{
        try{
            $stmt = $mysqli->prepare("INSERT INTO courses (title, description, instructor_id, is_published, created_at) VALUES (?,?,?,?,NOW())");
            $stmt->bind_param("ssii", $title, $description, $instructor_id, $is_published);
            $stmt->execute();

            $success_message = "Course Added successfully";
            header("Location: manage_courses.php");
        } catch (Exception $e){
            $error_message = "Datbase Error: " . $e->getMessage();
        }
    }

}

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Post Course | CourseHub</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap');
        body { font-family: 'Inter', sans-serif; background-color: #f9fafb; }
    </style>
</head>
<body class="flex items-center justify-center min-h-screen p-4">
    <div class="w-full max-w-lg bg-white p-8 rounded-xl shadow-2xl">
    <h1 class="text-3xl font-bold text-center text-green-600 mb-6">Add a New Course</h1>

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

    <form action="" method="POST" class="space-y-6">
      <div>
        <label for="title" class="block text-sm font-medium text-gray-700">Course Title:</label>
        <input type="text" id="title" name="title" required
               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm
                      focus:outline-none focus:ring-green-500 focus:border-green-500">
      </div>

      <div>
        <label for="description" class="block text-sm font-medium text-gray-700">Description:</label>
        <textarea id="description" name="description" rows="4" required
                  class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm
                         focus:outline-none focus:ring-green-500 focus:border-green-500"></textarea>
      </div>

      <div class="flex items-center space-x-2">
        <input type="checkbox" id="is_published" name="is_published" class="h-4 w-4 text-green-600">
        <label for="is_published" class="text-gray-700">Publish Immediately</label>
      </div>

      <button type="submit"
              class="w-full bg-green-600 hover:bg-green-700 text-white py-2 px-4 rounded-md font-semibold">
        Add Course
      </button>

      <div class="text-center mt-4">
        <a href="dashboard.php" class="text-sm text-green-600 hover:text-green-800">← Back to Dashboard</a>
      </div>
    </form>
  </div>
  <!-- LOADING OVERLAY -->
<div id="overlay" class="fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center hidden z-50">
    <div class="bg-white px-6 py-4 rounded-md shadow-lg flex items-center space-x-3">
        <svg class="animate-spin h-6 w-6 text-green-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
        </svg>
        <span id="overlayText" class="text-gray-700 font-semibold">Processing registration...</span>
    </div>
</div>

<script>
// show overlay during submission
document.querySelector("form").addEventListener("submit", function() {
    const overlay = document.getElementById("overlay");
    overlay.classList.remove("hidden");
});
</script>

<?php if ($success_message): ?>
<script>
    const overlay = document.getElementById("overlay");
    const overlayText = document.getElementById("overlayText");

    // show overlay
    overlay.classList.remove("hidden");

    // change text and icon after 1.5s
    setTimeout(() => {
        overlayText.textContent = "Account created successfully!";
        document.querySelector("#overlay svg").outerHTML = `
            <svg class="h-6 w-6 text-green-600" xmlns="http://www.w3.org/2000/svg" fill="none" 
                 viewBox="0 0 24 24" stroke-width="3" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
            </svg>`;
    }, 1500);

    // fade out overlay after 3s
    setTimeout(() => {
        overlay.classList.add("fade-out");
    }, 3000);

    // redirect after fade-out
    setTimeout(() => {
        window.location.href = "login.php";
    }, 3800);
</script>
<?php endif; ?>
</body>
</html>
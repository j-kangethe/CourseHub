<?php 

require_once '../auth/auth_guard.php';
require_once '../config/db.php';
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
    <title>Admin | Add Course</title>
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

    <!-- Sidebar -->
    <aside class="sidebar text-white w-64 flex flex-col justify-between p-5">
        <div>
            <h2 class="text-2xl font-bold mb-3" style="text-align: start;">CourseHub</h2>
            <nav class="space-y-2">
                <a href="dashboard.php" class="block px-2 py-1 rounded hover:bg-green-700">Dashboard</a>
                <a href="add_course.php" class="block px-2 py-1 rounded hover:bg-green-700 active-link">Add Course</a>
                <a href="add_content.php" class="block px-2 py-1 rounded hover:bg-green-700">Add Content</a>
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

    <!-- Main Content -->
    <main class="flex-1 p-10 overflow-y-auto min-h-screen flex flex-col items-center py-10 ">
        <div class="w-full max-w-lg bg-white p-8 rounded-xl shadow-2xl">
          <h1 class="text-3xl font-bold text-center text-green-600 mb-6">Add a New Course</h1>

          <?php if ($error_message): ?>{
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
              <strong>Error:</strong> <?= htmlspecialchars($error_message) ?>
            </div>
          }
          <?php endif;?>

          <?php if ($success_message): ?>{
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
              <strong>Success:</strong> <?= htmlspecialchars($success_message) ?>
            </div>
          }
          <?php endif;?>
        
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

      <!-- <div class="text-center mt-4">
        <a href="dashboard.php" class="text-sm text-green-600 hover:text-green-800">← Back to Dashboard</a>
      </div> -->
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
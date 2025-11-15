<?php  
require_once '../config/db.php';
require_once '../auth/auth_guard.php';
require_role('admin');

global $mysqli;

$success_message = '';
$error_message = '';
$courses = $mysqli->query("SELECT * FROM courses ORDER BY created_at DESC");
$result = $courses->fetch_all(MYSQLI_ASSOC);

// delete

if (isset($_GET['delete_id'])){
    $course_id = intval($_GET['delete_id']);
    try{
        $check = $mysqli->prepare("SELECT * FROM courses WHERE id = ? ");
        $check->bind_param("i", $course_id);
        $check->execute();
        $result = $check->get_result();

        if ($result->num_rows === 0){
            $error_message = "Course not found";
        }
        else{
            $delete_stmt = $mysqli->prepare("DELETE FROM courses WHERE id = ?");
            $delete_stmt->bind_param("i", $course_id);
            $delete_stmt->execute();

            $success_message = "Course deleted successfully.";
        }
    }
    catch (Exception $e){
        $error_message = "Error deleting Course: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin | Manage Courses</title>
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
                <a href="manage_courses.php" class="block px-2 py-1 rounded hover:bg-green-700 active-link">Manage Courses</a>

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

    <div class="w-full max-w-100 bg-white shadow-xl rounded-xl p-8 min-h-screen flex flex-col items-center py-10">
        <h1 class="text-3xl font-bold text-center text-green-600 mb-6">Manage Courses</h1>

        <?php if ($success_message): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                <?= htmlspecialchars($success_message) ?>
            </div>
        <?php endif; ?>

        <?php if ($error_message): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <?= htmlspecialchars($error_message) ?>
            </div>
        <?php endif; ?>

        <table class="w-full text-left border-collapse">
            <thead class="bg-green-600 text-white">
                <tr class="text-center">
                    <th class="p3">#</th>
                    <th class="p3">Title</th>
                    <th class="p3">Description</th>
                    <th class="p3">Published</th>
                    <th class="p3">Actions</th>
                </tr>
            </thead>

            <tbody class="divide-y divide-gray-200">
                <?php if (empty($courses)): ?>
                    <tr>
                        <td colspan="5" class="p-4 text-center text-gray-500">No Courses found</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($courses as $index=> $course): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="p-3"><?= $index + 1 ?></td>
                            <td class="p-3 font-semibold text-gray-800"><?= htmlspecialchars($course['title']) ?></td>
                            <td class="p-3 text-gray-600"><?= htmlspecialchars($course['description']) ?></td>
                            <td class="p-3 text-center"><?= $course['is_published'] ? '✅': '❌' ?></td>
                            <td class="p-3 text-center">
                                <a href="edit_course.php?id=<?= $course['id'] ?>" class="text-blue-600 hover:underline mr-3">Edit</a>
                                <a href="?delete_id=<?= $course['id'] ?>" class="text-red-600 hover: underline"
                                onclick="return confirm('Are you sure you want to delete this course? This action cannot be undone!')">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
         <div class="text-center mt-8">
            <a href="dashboard.php" class="text-green-600 hover:underline">← Back to Dashboard</a>
        </div>

    </div>
    
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
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
    <title>Manage Courses | Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex flex-col items-center py-10 font-[Inter]">
    <div class="w-full max-w-4xl bg-white shadow-xl rounded-xl p-8">
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
    
</body>
</html>

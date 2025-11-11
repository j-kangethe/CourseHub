<?php
include '../config/db.php';
include '../auth/auth_guard.php';
require_role('student');

global $mysqli;

try{
    // Fetch published resources

    $result_courses = $mysqli->query("SELECT * FROM courses WHERE is_published = 1 ORDER BY created_at DESC");
    $courses = $result_courses->fetch_all(MYSQLI_ASSOC);
    $result_courses->free();

    // Fetch course content

    $result_content = $mysqli->query("SELECT c.id, c.title, c.file_type, c.course_id, co.title AS 
    course_title FROM content c JOIN courses co ON c.course_id = co.id WHERE co.is_published = 1 ORDER BY c.uploaded_at ASC");
    $raw_content = $result_content->fetch_all(MYSQLI_ASSOC);
    $result_content->free();

    $content_by_course = [];
    foreach ($raw_content as $content){
        $content_by_course[$content['course_id']][] = $content;
    }
}   catch (Exception $e){
        error_log("Error fetching student Courses: " . $e->getMessage());
        $courses = [];
        $error = "Could not load courses from the database.";
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Courses | CourseHub</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap');
        body { background-color: #f9fafb; font-family: 'Inter', sans-serif; }
        .sidebar { background: linear-gradient(180deg, #16a34a, #15803d); }
        .active-link { background-color: rgba(255,255,255,0.2); }
    </style>
</head>
<body class="flex h-screen">

    <!-- Sidebar -->
    <aside class="sidebar text-white w-64 flex flex-col justify-between p-5 flex-shrink-0">
        <div>
            <h2 class="text-2xl font-bold mb-8 text-center">CourseHub</h2>
            <nav class="space-y-2">
                <a href="dashboard.php" class="block px-4 py-2 rounded hover:bg-green-700">🏠 Dashboard</a>
                <a href="courses.php" class="block px-4 py-2 rounded hover:bg-green-700 active-link">📚 My Courses</a>
                <a href="profile.php" class="block px-4 py-2 rounded hover:bg-green-700">👤 Profile</a>
            </nav>
        </div>
        <div class="border-t border-green-500 mt-6 pt-4">
            <p class="text-sm mb-2">Logged in as:</p>
            <p class="font-semibold"><?= htmlspecialchars($_SESSION['fullname']); ?></p>
            <p class="text-xs text-green-100 mb-4"><?= htmlspecialchars($_SESSION['registration_no']); ?></p>
            <a href="../auth/logout.php"
               class="w-full inline-block text-center bg-red-500 hover:bg-red-600 px-3 py-2 rounded text-white font-semibold">
               Logout
            </a>
        </div>
    </aside>

    <main class="flex-1 p-10 overflow-y-auto">
        <h2 class="text-3xl font-bold text-gray-800 mb-6 border-b pb-3">Available Resources</h2>

        <?php if (isset($error)): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4"><?= $error ?></div>
        <?php endif; ?>

        <div class="space-y-6">
            <?php if (empty($courses)): ?>
                <div class="p-6 bg-white rounded-xl shadow-md text-center text-gray-500">
                    The repository is empty! Please check back later
                </div>
            <?php else: ?>
                <?php foreach($courses as $course): ?>
                    <div class="bg-white p-6 rounded-xl shadow-xl border-l-4 border-green-600">
                        <h3 class="text-2xl font-bold text-gray-900 mb-2"><?= htmlspecialchars($course['title']); ?></h3>
                        <p class="text-grat-600 mb-4"><?= htmlspecialchars($course['description']); ?></p>
                        <h4 class="font-semibold text-gray-700 mb-2 mt-4">Course Content: </h4>

                        <?php if (isset($content_by_course[$course['id']])): ?>
                            <ul class="list-disc pl-5 space-y-2 text-gray-700">
                                <?php foreach ($content_by_course[$course['id']] as $content): ?>
                                    <li class="flex justify-between items-center">
                                        <span><?= htmlspecialchars($content['title']) ?>(<?= strtoupper(htmlspecialchars($content['file_type'])) ?>)</span>
                                        <a href="view_content.php?id=<?= $content['id'] ?>" class="text-green-600 hover: text-green-700 font-medium flex items-center space-x-1">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L10 11.586l2.293-2.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" />
                                        </svg>
                                        Download
                                        </a>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php else: ?>
                            <p class="text-sm text-gray-500 italic">No Content uploaded yet for this Course.</p>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </main>
</body>
</html>

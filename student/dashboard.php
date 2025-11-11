<?php

include '../auth/auth_guard.php';
require_role('student')
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Dashboard | CourseHub</title>
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
            <h2 class="text-2xl font-bold mb-8 text-center">CourseHub</h2>
            <nav class="space-y-2">
                <a href="dashboard.php" class="block px-4 py-2 rounded hover:bg-green-700 active-link">🏠 Dashboard</a>
                <a href="courses.php" class="block px-4 py-2 rounded hover:bg-green-700">📚 My Courses</a>
                <a href="profile.php" class="block px-4 py-2 rounded hover:bg-green-700">👤 Profile</a>
            </nav>
        </div>

        <div class="border-t border-green-500 mt-6 pt-4">
            <p class="text-sm mb-2">Logged in as:</p>
            <p class="font-semibold"><?= htmlspecialchars($_SESSION['fullname']); ?></p>
            <p class="text-xs text-green-100 mb-4"><?= htmlspecialchars($_SESSION['registration_no']); ?></p>
            <p style="font-size: 17px; border:solid 1px green" class="text-xs text-green-70 mb-4"><?= htmlspecialchars($_SESSION['role']);?></p>
            <a href="../auth/logout.php" 
               class="w-full inline-block text-center bg-red-500 hover:bg-red-600 px-3 py-2 rounded text-white font-semibold">
               Logout
            </a>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 p-10 overflow-y-auto">
        <h1 class="text-3xl font-bold text-gray-800 mb-6">Welcome, <?= htmlspecialchars($_SESSION['fullname']); ?> 👋</h1>
        <p class="text-gray-600 mb-6">
            Here’s your personalized student dashboard where you can view your courses, progress, and updates.
        </p>

        <!-- Stats Section -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white p-6 rounded-lg shadow-md">
                <h2 class="text-xl font-semibold text-green-700 mb-2">Enrolled Courses</h2>
                <p class="text-3xl font-bold text-gray-800">5</p>
            </div>

            <div class="bg-white p-6 rounded-lg shadow-md">
                <h2 class="text-xl font-semibold text-green-700 mb-2">Assignments Due</h2>
                <p class="text-3xl font-bold text-gray-800">2</p>
            </div>

            <div class="bg-white p-6 rounded-lg shadow-md">
                <h2 class="text-xl font-semibold text-green-700 mb-2">Completed Modules</h2>
                <p class="text-3xl font-bold text-gray-800">8</p>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-2xl font-semibold text-gray-800 mb-4">Recent Activity</h2>
            <ul class="divide-y divide-gray-200">
                <li class="py-3">
                    <span class="font-semibold text-green-700">Uploaded:</span> Web Development Notes 📄
                </li>
                <li class="py-3">
                    <span class="font-semibold text-green-700">Viewed:</span> Introduction to Databases
                </li>
                <li class="py-3">
                    <span class="font-semibold text-green-700">Completed:</span> HTML & CSS Module ✅
                </li>
            </ul>
        </div>
    </main>

</body>
</html>

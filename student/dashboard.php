<?php

include '../auth/auth_guard.php';
include '../config/db.php';
require_role('student');

$today = date('Y-m-d');

$result = $mysqli->query("SELECT * FROM announcements WHERE status = 'active' AND (expires_at IS NULL OR expires_at >= '$today') ORDER BY created_at DESC LIMIT 5");

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


        <div class=" text-white">
            <div class="flex items-center justify-between cursor-pointer" id="userToggle">
                <p class="font-semibold"><?= htmlspecialchars($_SESSION['fullname']); ?></p>
                <span id="arrow" class="transition-transform duration-500">▼</span>
            </div>

            <!-- Dropdown -->

            <div  id="userDropdown" class="origin-top scale-y-0 opacity-0 left-0 mt-2 w-60 bg-green-700 text-white rounded-lg shadow-lg p-4 z-50 transform transition-all duration-500">
                <p class="text-sm mb-1">Logged in as: </p>
                <p class="font-semibold"><?= htmlspecialchars($_SESSION['email']); ?></p>
                <p class="text-xs text-green-100 mb-2"><?= htmlspecialchars($_SESSION['registration_no']); ?></p>
                <p class="text-xs border border-green-400 p-1 rounded mb-3"><?= htmlspecialchars($_SESSION['role']); ?></p>
                <a href="../auth/logout.php" class="block text-center bg-red-500 hover: bg-red-600 px-3 py-2 rounded font-semibold">Logout</a>
            </div>
        </div>

    </aside>

    <!-- Main Content -->
    <main class="flex-1 p-10 overflow-y-auto">
        <h1 class="text-3xl font-bold text-gray-800 mb-6">Welcome, <?= htmlspecialchars($_SESSION['fullname']); ?> 👋</h1>
        <p class="text-gray-600 mb-6">
            Here’s your personalized student dashboard where you can view your courses, progress, and updates.
        </p>

        <div class="bg-white p-6 rounded-xl shadow mb-6">
            <h2 class="text-xl font-bold mb-3">Latest Announcements</h2>

            <?php if ($result->num_rows == 0): ?>
                <p class="text-gray-500">No announcements available</p>

            <?php  else: ?>
                <ul class="space-y-3">
                    <?php while ($row = $result->fetch_assoc()) :?> 
                        <li class="border-b pb-2">
                            <h3 class="font-semibold text-green-600"><?= $row['title'] ?></h3>
                            <p class="text-gray-700"><?= substr($row['message'], 0 , 100) ?>...</p>
                            <a href="#" onclick="openModal(<?= $row['id'] ?>)" class="text-blue-600 text-sm">Read More</a>
                        </li>
                    <?php endwhile; ?>
                </ul>
            <?php endif; ?>
        </div>

        <!-- Stats Section -->
        <!-- <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
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

        Recent Activity 
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
        </div> -->
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

        function openModal(id){
            window.location.href = "announcement_view.php?id=" + id;

        }
    </script>
</body>
</html>

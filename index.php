<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CourseHub | Your Learning Repository</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap');
        body {
            font-family: 'Inter', sans-serif;
            background-color: #ffffff;
        }
        .hero-bg {
            background: linear-gradient(120deg, #4f46e5 0%, #7c3aed 100%);
        }
        
    </style>
</head>
<body>
    <!-- Navigation Bar -->
    <nav class="bg-white shadow-sm sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex-shrink-0 flex items-center">
                    <svg class="h-8 w-auto text-indigo-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18c-2.305 0-4.408.867-6 2.292m0-14.25v14.25" />
                    </svg>
                    <span class="font-bold text-2xl text-gray-800 ml-2">CourseHub</span>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="auth/login.php" class="text-gray-600 hover:text-indigo-600 font-medium transition duration-150">Sign In</a>
                    <a href="auth/register.php" class="px-5 py-2 bg-indigo-600 text-white font-semibold rounded-lg shadow-md hover:bg-indigo-700 transition duration-150">
                        Get Started
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <main>
        <div class="hero-bg text-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 md:py-32 text-center">
                <h1 class="text-4xl md:text-6xl font-extrabold mb-4">Your Central Learning Repository</h1>
                <p class="text-xl md:text-2xl text-indigo-200 max-w-3xl mx-auto mb-10">
                    Access and manage all your course materials, from lecture notes to project files, all in one secure place.
                </p>
                <a href="auth/login.php" class="px-8 py-4 bg-white text-indigo-700 font-bold rounded-lg shadow-xl text-lg hover:bg-gray-100 transition duration-200 transform hover:scale-105">
                    Start Learning Now
                </a>
            </div>
        </div>

        <!-- Features Section -->
        <section class="py-20 bg-gray-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-16">
                    <h2 class="text-3xl md:text-4xl font-bold text-gray-900">Features Designed for You</h2>
                    <p class="text-lg text-gray-600 mt-2">Whether you're an admin or a student, we've got you covered.</p>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-10">
                    <!-- Feature 1: Admin -->
                    <div class="bg-white p-8 rounded-xl shadow-lg border-t-4 border-indigo-500">
                        <div class="flex-shrink-0">
                            <svg class="h-12 w-12 text-indigo-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M10.34 15.84c-.688-.06-1.386-.09-2.09-.09H7.5a4.5 4.5 0 110-9h.75c.704 0 1.402-.03 2.09-.09m0 9.18c.253.962.584 1.892.985 2.783.247.55.06 1.21-.463 1.5-1.08.52-2.32.74-3.535.74H7.5a4.5 4.5 0 010-9h.75c1.215 0 2.455.22 3.535.74.523.29.71.95.463 1.5-.401.892-.732 1.82-.985 2.783m.01-9.18H10.5a1.5 1.5 0 001.5-1.5V6.75a1.5 1.5 0 00-1.5-1.5H8.658c-1.215 0-2.455.22-3.535.74-.523.29-.71.95-.463 1.5.401.892.732 1.82.985 2.783h1.85m0 0c.253.962.584 1.892.985 2.783.247.55.06 1.21-.463 1.5-1.08.52-2.32.74-3.535.74H7.5a4.5 4.5 0 010-9h.75c1.215 0 2.455.22 3.535.74.523.29.71.95.463 1.5-.401.892-.732 1.82-.985 2.783m.01-9.18H10.5a1.5 1.5 0 001.5-1.5V6.75a1.5 1.5 0 00-1.5-1.5H8.658c-1.215 0-2.455.22-3.535.74-.523.29-.71.95-.463 1.5.401.892.732 1.82.985 2.783h1.85m0 0a1.5 1.5 0 001.5-1.5V6.75a1.5 1.5 0 00-1.5-1.5H8.658c-1.215 0-2.455.22-3.535.74-.523.29-.71.95-.463 1.5.401.892.732 1.82.985 2.783h1.85M13.5 14.25H10.5m0-9H13.5" />
                            </svg>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900 mt-4 mb-2">For Admins</h3>
                        <p class="text-gray-600">Easily create, publish, and manage all course content. Upload files, organize lessons, and control student access from one powerful dashboard.</p>
                    </div>

                    <!-- Feature 2: Student -->
                    <div class="bg-white p-8 rounded-xl shadow-lg border-t-4 border-green-500">
                        <div class="flex-shrink-0">
                            <svg class="h-12 w-12 text-green-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.436 60.436 0 00-.491 6.347A48.627 48.627 0 0112 20.904a48.627 48.627 0 018.232-4.41 60.46 60.46 0 00-.491-6.347m-15.482 0a50.57 50.57 0 00-2.658-.813A59.905 59.905 0 0112 3.493a59.902 59.902 0 0110.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.697 50.697 0 0112 13.489a50.697 50.697 0 017.74-3.342M6.75 15a.75.75 0 100-1.5.75.75 0 000 1.5z" />
                            </svg>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900 mt-4 mb-2">For Students</h3>
                        <p class="text-gray-600">Never miss a file. Access all your published courses, view content online, and securely download materials for offline study.</p>
                    </div>

                    <!-- Feature 3: Secure -->
                    <div class="bg-white p-8 rounded-xl shadow-lg border-t-4 border-red-500">
                        <div class="flex-shrink-0">
                            <svg class="h-12 w-12 text-red-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.623 0-1.662-.509-3.227-1.395-4.598A11.961 11.961 0 0012 2.964c-.652 0-1.284.062-1.9.176z" />
                            </svg>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900 mt-4 mb-2">Secure & Centralized</h3>
                        <p class="text-gray-600">Built with security in mind. Role-based access ensures only authorized users can manage or view specific content.
                        </p>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <!-- Footer -->
    <footer class="bg-gray-800 text-gray-400 py-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <p>&copy; <?= date("Y") ?> CourseHub. All rights reserved.</p>
        </div>
    </footer>

</body>
</html>
<?php
session_start();
include '../config/db.php';

// Redirect logged-in users to their dashboards
if (isset($_SESSION['user_id'])) {
    if ($_SESSION['role'] === 'admin') {
        header('Location: ../admin/dashboard.php');
        exit;
    } elseif ($_SESSION['role'] === 'student') {
        header('Location: ../student/dashboard.php');
        exit;
    }
}

$error_message = '';
$success_message = '';

if ($_SERVER['REQUEST_METHOD'] === "POST") {
    global $mysqli;

    $registration_no = trim($_POST['registration_no']);
    $password = $_POST['password'];

    if (empty($registration_no) || empty($password)) {
        $error_message = "Both fields are required.";
    } elseif (!preg_match('/^[A-Z]{4,6}\/\d{4}\/\d{4,6}$/', $registration_no)) {
        $error_message = "Invalid registration number format. Use format like BSCCS/2024/55984.";
    } else {
        $sql = "SELECT * FROM users WHERE registration_no = ?";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("s", $registration_no);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();

            if (password_verify($password, $user['password'])) {
                // Create session
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['registration_no'] = $user['registration_no'];
                $_SESSION['fullname'] = $user['fullname'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['role'] = $user['role'];

                $success_message = "Welcome back, " . htmlspecialchars($user['fullname']) . "!";
                session_regenerate_id(true); // Prevent session fixation

                // Redirect based on role after short delay (JS overlay handles it)
                if ($user['role'] === "student") {
                    $redirect_url = "../student/dashboard.php";
                } else {
                    $redirect_url = "../admin/dashboard.php";
                }
            } else {
                $error_message = "Incorrect password.";
            }
        } else {
            $error_message = "No account found with that registration number.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CourseHub Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap');
        body { 
            background-color: #f3f4f6; 
            font-family: 'Inter', sans-serif; 
        }
        .fade-out {
            opacity: 0;
            transition: opacity 0.8s ease;
        }
    </style>
</head>
<body class="flex items-center justify-center min-h-screen p-4">
    <div class="w-full max-w-md bg-white p-8 rounded-xl shadow-2xl">
        <h1 class="text-3xl font-bold text-center text-green-600 mb-6">Login</h1>

        <?php if ($error_message): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4" role="alert">
                <strong class="font-bold">Error:</strong>
                <span class="block sm:inline"><?= htmlspecialchars($error_message) ?></span>
            </div>
        <?php endif; ?>

        <?php if ($success_message): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4" role="alert">
                <strong class="font-bold">Success!</strong>
                <span class="block sm:inline"><?= $success_message ?></span>
            </div>
        <?php endif; ?>

        <form action="" method="POST" class="space-y-6">
            <div>
                <label for="registration_no" class="block text-sm font-medium text-gray-800">Registration Number:</label>
                <input type="text" id="registration_no" name="registration_no" required 
                       oninput="this.value = this.value.toUpperCase()"
                       class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm 
                              focus:outline-none focus:ring-green-500 focus:border-green-500 uppercase"
                              pattern="^[A-Z]{4,6}/\d{4}/\d{4,6}$">
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-gray-800">Password:</label>
                <input type="password" id="password" name="password" required 
                       class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm 
                              focus:outline-none focus:ring-green-500 focus:border-green-500">
            </div>

            <button type="submit" id="loginBtn" 
                    class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm 
                           text-sm font-medium text-white bg-green-600 hover:bg-green-700 
                           focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                Login
            </button>
        </form>

        <div class="text-center mt-6">
            <a href="register.php" class="text-sm text-indigo-600 hover:text-indigo-500">
                Don’t have an account? 
                <span style="text-decoration: underline; color:green;">Register</span>
            </a>
        </div>
    </div>

    <!-- Overlay -->
    <div id="overlay" class="fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center hidden z-50">
        <div class="bg-white px-6 py-4 rounded-md shadow-lg flex items-center space-x-3">
            <svg class="animate-spin h-6 w-6 text-green-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
            </svg>
            <span id="overlayText" class="text-gray-700 font-semibold">Logging in...</span>
        </div>
    </div>

    <script>
        // show overlay when form submits
        document.querySelector("form").addEventListener("submit", function() {
            document.getElementById("overlay").classList.remove("hidden");
        });
    </script>

    <?php if ($success_message): ?>
    <script>
        const overlay = document.getElementById("overlay");
        const overlayText = document.getElementById("overlayText");

        // show overlay
        overlay.classList.remove("hidden");

        // change text + icon after 1.2s
        setTimeout(() => {
            overlayText.textContent = "Welcome back!";
            document.querySelector("#overlay svg").outerHTML = `
                <svg class="h-6 w-6 text-green-600" xmlns="http://www.w3.org/2000/svg" fill="none"
                     viewBox="0 0 24 24" stroke-width="3" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                </svg>`;
        }, 1200);

        // fade out + redirect after 2.5s
        setTimeout(() => {
            overlay.classList.add("fade-out");
        }, 2500);

        setTimeout(() => {
            window.location.href = "<?= $redirect_url ?>";
        }, 3300);
    </script>
    <?php endif; ?>
</body>
</html>

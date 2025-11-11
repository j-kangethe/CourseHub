<?php
include '../config/db.php';

$error_message  = '';
$success_message = '';

if ($_SERVER['REQUEST_METHOD'] === "POST") {
    global $mysqli;

    // collect user data
    $fullname = trim($_POST['fullname']);
    $registration_no = trim($_POST['registration_no']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['password2'];

    if (empty($fullname) || empty($registration_no) || empty($email) || empty($password)) {
        $error_message = "All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = "Please enter a valid email address.";
    } elseif ($password !== $confirm_password) {
        $error_message = "Passwords do not match.";
    } elseif (!preg_match('/^[A-Z]{4,6}\/\d{4}\/\d{4,6}$/', $registration_no)) {
        $error_message = "Invalid registration number format. Use format like BSCCS/2024/55984.";
    } else {
        // Check if user already exists
        $check_sql = "SELECT * FROM users WHERE email = ? OR registration_no = ?";
        $stmt = $mysqli->prepare($check_sql);
        $stmt->bind_param("ss", $email, $registration_no);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $error_message = "An account with that email or registration number already exists.";
        } else {
            // Hash password and insert new record
            $hashed_pass = password_hash($password, PASSWORD_DEFAULT);
            $role = 'student';

            $insert_sql = "INSERT INTO users (fullname, registration_no, email, password, role, created_at)
                           VALUES (?, ?, ?, ?, ?, NOW())";
            $insert_stmt = $mysqli->prepare($insert_sql);
            $insert_stmt->bind_param("sssss", $fullname, $registration_no, $email, $hashed_pass, $role);

            if ($insert_stmt->execute()) {
                $success_message = "Account created successfully! Redirecting to login...";
            } else {
                $error_message = "Database error: " . $mysqli->error;
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CourseHub | Register</title>
    <link rel="icon" href=""/>
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
    <h1 class="text-3xl font-bold text-center text-green-600 mb-6">Create Your Account</h1>
    
    <?php if ($error_message): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4" role="alert" style="text-align: center;">
            <strong class="font-bold">Error:</strong>
            <span class="block sm:inline"><?= htmlspecialchars($error_message) ?></span>
        </div>
    <?php endif; ?>

    <?php if ($success_message): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4" role="alert" style="text-align: center;">
            <strong class="font-bold">Success!</strong>
            <span class="block sm:inline"><?= $success_message ?></span>
        </div>
    <?php endif; ?>

    <!-- REGISTRATION FORM -->
    <form action="" method="POST" class="space-y-6">
        <h4 class="text-xl font-semibold text-gray-800 text-center">Register as Student</h4>
        
        <div>
            <label for="fullname" class="block text-sm font-medium text-gray-800">Full Name:</label>
            <input type="text" id="fullname" name="fullname" required 
                   class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm 
                   focus:outline-none focus:ring-green-500 focus:border-green-500" placeholder="John Doe">
        </div>

        <div>
            <label for="registration_no" class="block text-sm font-medium text-gray-800">Registration Number:</label>
            <input type="text" id="registration_no" name="registration_no" required
            pattern="^[A-Z]{4,6}/\d{4}/\d{4,6}$" oninput="this.value = this.value.toUpperCase()" 
                   class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm 
                   focus:outline-none focus:ring-green-500 focus:border-green-500" placeholder="John Doe">
        </div>


        <div>
            <label for="email" class="block text-sm font-medium text-gray-800">Email:</label>
            <input type="email" id="email" name="email" required 
                   class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm 
                   focus:outline-none focus:ring-green-500 focus:border-green-500" placeholder="johndoe@gmail.com">
        </div>

        <div>
            <label for="password" class="block text-sm font-medium text-gray-800">Password:</label>
            <input type="password" id="password" name="password" required 
                   class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm 
                   focus:outline-none focus:ring-green-500 focus:border-green-500">
        </div>

        <div>
            <label for="password2" class="block text-sm font-medium text-gray-800">Confirm Password:</label>
            <input type="password" id="password2" name="password2" required 
                   class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm 
                   focus:outline-none focus:ring-green-500 focus:border-green-500">
        </div>

        <div>
            <label for="role" class="block text-sm font-medium text-gray-700">Role:</label>
            <select disabled class="form-control mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm 
                    focus:outline-none focus:ring-green-500 focus:border-green-500 text-center">
                <option value="student">Student</option>
            </select>
        </div><br>
        
        <button type="submit" id="submit" 
                class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm 
                text-sm font-medium text-white bg-green-600 hover:bg-green-700 
                focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
            Create Account
        </button>
    </form>

    <div class="text-center mt-6">
        <a href="login.php" class="text-sm text-indigo-600 hover:text-indigo-500">
            Already have an account? 
            <span style="text-decoration: underline; color:green;">Sign In</span>
        </a>
    </div>
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

<?php

$host = "localhost";
$db = "courses_repository";
$pass = "";
$user = "root";

global $mysqli;
$mysqli = new mysqli($host, $user, $pass, $db);




if ($mysqli->connect_errno) {
    $error_message = "MySQLi connection failed: " . $mysqli->connect_error;
    error_log($error_message);
    
    die("<h1>System Error</h1><p>We are currently experiencing technical difficulties with the database connection. Please try again later.</p>");

}
$mysqli->set_charset("utf8mb4");

// // function require_auth($required_role) {
// //     // This function is purely session-based and does not require DB interaction, no change needed.
// //     session_start();
    
// //     // Check if user is logged in
// //     if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_role'])) {
// //         header('Location: /index.php?error=not_logged_in');
// //         exit;
// //     }

// //     // Check if the user has the required role
// //     if ($_SESSION['user_role'] !== $required_role) {
// //         // Redirect to a specific error or their dashboard
// //         header('Location: /index.php?error=unauthorized_access');
// //         exit;
// //     }
// // }

function hash_password($password) {
    // No DB change needed
    return password_hash($password, PASSWORD_BCRYPT);
}

function verify_password($password, $hash) {
    // No DB change needed
    return password_verify($password, $hash);
}

function secure_download($filepath, $filename) {
    // This function is purely file I/O based and does not require DB interaction, no change needed.
    if (!file_exists($filepath)) {
        header("HTTP/1.1 404 Not Found");
        die("File not found.");
    }

    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="' . basename($filename) . '"');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($filepath));
    readfile($filepath);
    exit;
}
?>
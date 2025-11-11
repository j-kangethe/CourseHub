<?php
include '../config/db.php';
include '../auth/auth_guard.php';
require_role('student');


if (!isset($_GET['id']) || !is_numeric($_GET['id'])){
    die ("Invalid File Id");
}

$content_id = intval($_GET['id']);
global $mysqli;

$sql = "SELECT c.file_path, c.title, c.file_type FROM content c JOIN courses co ON c.course_id = co.id WHERE c.id = ? AND co.is_published = 1";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param("i", $content_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows !== 1){
    die("File not found or access denied");
}

$file = $result->fetch_assoc();
$file_path = realpath($file['file_path']);
$uploads_dir = realpath("../uploads");

if (strpos($file_path, $uploads_dir) !== 0 ){
    die ("Access Denied");
}
if (!file_exists($file_path)){
    die("File missing on server");
}

$download_name = basename($file['title']) . '.' . strtolower($file['file_type']);

header('Content-Description: File Transfer');
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="' . $download_name . '"');
header('Expires: 0');
header('Cache-Control: must-revalidate');
header('Pragma: public');
header('Content-Length: ' . filesize($file_path));
readfile($file_path);
exit;

?>
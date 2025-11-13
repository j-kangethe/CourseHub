<?php

include '../auth/auth_guard.php';
require_role('admin');

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <a href="../auth/logout.php">Logout</a><br>
    <a href="add_course.php">Post Course</a><br>
    <a href="add_content.php">Post content</a><br>
    <a href="manage_courses.php">Manage Courses</a><br>
    <a href="create_admin.php">Create an Admin</a>
</body>
</html>
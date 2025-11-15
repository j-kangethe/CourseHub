<?php 
include '../config/db.php';
$id = $_GET['id'];

$result = $mysqli->query("SELECT * FROM announcements WHERE id = $id");
$row = $result->fetch_assoc();
?>


<!DOCTYPE html>
<html>
<head>
<title>View Announcement | CourseHub</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 p-10">
    <div class="max-w-2xl mx-auto bg-white p-8 rounded-xl shadow">
        <h1 class="text-2xl font-bold text-green-600 mb-4"><?= $row['title'] ?></h1>

        <p class="text-gray-800 leading-relaxed mb-6"><?= nl2br($row['message']) ?></p>

        <p class="text-sm text-gray-500">Posted on: <?= $row['created_at'] ?></p>   
        <?php if ($row['expires_at']): ?>
            <p class="text-sm text-gray-500">Expires on: <?= $row['expires_at'] ?></p>
        <?php endif; ?>

        <a href="dashboard.php" class="mt-4 block bg-green-600 text-white px-4 py-2 rounded text-center">Back</a>
    </div>
</body>
</html>

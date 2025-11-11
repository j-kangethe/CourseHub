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
    <a href="../auth/logout.php">Logout</a>
</body>
</html>
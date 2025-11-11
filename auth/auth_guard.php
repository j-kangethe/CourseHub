<?php
session_start();

$timeout_duration = 1800;

$_SESSION['LAST_ACTIVITY'] = time();

if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY']) > $timeout_duration){
    session_unset();
    session_destroy();

    header("Location: ../auth/login.php?session_expired = true");
    exit();
}
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])){
    header("Location: ../auth/login.php?error=not_logged_in");
    exit();
}

function require_role($role){
    if ($_SESSION['role'] !== $role ){
        header("Location: ../auth/login.php?error=Unauthorized");
        exit();
    }
}
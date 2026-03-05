<?php
session_start();

// Catat log logout sebelum session dihapus
if (isset($_SESSION['user_id'])) {
    require_once '../config/database.php';
    require_once '../config/function.php';
    catatLog($conn, 'Logout', 'users', $_SESSION['user_id'], 'Logout dari sistem');
}

session_destroy();
header("Location: login.php");
exit();
?>
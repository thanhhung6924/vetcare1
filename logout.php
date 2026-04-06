<?php
session_start();
session_unset();
session_destroy();

// Xóa Cookie Remember Me nếu có
if (isset($_COOKIE['remember_token'])) {
    setcookie('remember_token', '', time() - 3600, '/');
}

// Chuyển hướng thẳng về index hoặc login
header("Location: ../index.php");
exit;

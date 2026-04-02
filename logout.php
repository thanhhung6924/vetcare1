<?php
ob_start();
session_start();

// Hủy toàn bộ Session của người dùng hiện tại
session_unset();
session_destroy();

// Dọn dẹp Cookie 
if (isset($_COOKIE['remember_token'])) {
    setcookie('remember_token', '', time() - 3600, '/');
}
ob_clean();
// Trả về đúng cục JSON 
header('Content-Type: application/json; charset=utf-8');
echo json_encode([
    header('Location: /index.php') 
]);
exit;

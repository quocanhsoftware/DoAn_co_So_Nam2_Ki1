<?php
// Bắt đầu session
session_start();

// Hủy tất cả các biến session
$_SESSION = array();

// Nếu muốn xóa session cookie, xóa cả session ID.
// Lưu ý: Thao tác này sẽ chỉ hiệu quả nếu không có đầu ra nào trước đó.
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Hủy session
session_destroy();

// Chuyển hướng người dùng về trang chủ (index.php)
// Vì file này nằm trong Controller/ nên cần trỏ ra ngoài thêm 1 cấp để đến index.php
header("Location: ../index.php"); 
exit;

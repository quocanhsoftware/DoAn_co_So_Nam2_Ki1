<?php
include '../Model/db.php';

$user_id  = intval($_POST['user_id'] ?? 0);
$nameshop = trim($_POST['nameshop'] ?? '');
$password = trim($_POST['password'] ?? '');

if (!$user_id || !$nameshop || !$password) {
    echo "<script>alert('Thiếu thông tin'); window.history.back();</script>";
    exit;
}

// Kiểm tra nameshop trùng
$check = $conn->prepare("SELECT id FROM users WHERE nameshop = ? AND id <> ?");
$check->bind_param("si", $nameshop, $user_id);
$check->execute();
$check->store_result();

if ($check->num_rows > 0) {
    echo "<script>alert('Tên cửa hàng đã tồn tại'); window.history.back();</script>";
    exit;
}
$check->close();

// Hash password
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// Cập nhật DB
$stmt = $conn->prepare("UPDATE users SET nameshop=?, password=? WHERE id=?");
$stmt->bind_param("ssi", $nameshop, $hashed_password, $user_id);

if ($stmt->execute()) {
    echo "<script>alert('Tạo shop thành công!'); window.location.href='../index.php';</script>";
    exit;
} else {
    echo "<script>alert('Lỗi: " . $stmt->error . "'); window.history.back();</script>";
}

$stmt->close();
$conn->close();
?>

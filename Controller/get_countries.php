<?php
include '../Model/db.php'; // dùng biến $conn trong db.php
header('Content-Type: application/json; charset=utf-8');

// kiểm tra kết nối có tồn tại
if (!isset($conn) || !($conn instanceof mysqli)) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Không tìm thấy kết nối $conn. Kiểm tra lại db.php']);
    exit;
}

// truy vấn dữ liệu
$sql = "SELECT id, name, code, flag FROM countries ORDER BY name ASC";
$result = $conn->query($sql);

if (!$result) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Lỗi truy vấn: ' . $conn->error]);
    exit;
}

// trả JSON
$countries = [];
while ($row = $result->fetch_assoc()) {
    $countries[] = $row;
}

echo json_encode($countries);
$conn->close();
?>

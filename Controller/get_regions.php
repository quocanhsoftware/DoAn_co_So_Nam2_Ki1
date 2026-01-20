<?php
include '../Model/db.php';
header('Content-Type: application/json; charset=utf-8');

// Lấy country_id từ URL, đảm bảo là số nguyên
$country_id = isset($_GET['country_id']) ? intval($_GET['country_id']) : 0;

// Kiểm tra kết nối
if (!isset($conn) || !($conn instanceof mysqli)) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Không tìm thấy kết nối $conn. Kiểm tra lại db.php']);
    exit;
}

// Chuẩn bị truy vấn an toàn
$stmt = $conn->prepare("SELECT id, name FROM regions WHERE country_id = ? ORDER BY name ASC");
if (!$stmt) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Lỗi prepare: ' . $conn->error]);
    exit;
}

$stmt->bind_param('i', $country_id);
$stmt->execute();
$result = $stmt->get_result();

$regions = [];
while ($row = $result->fetch_assoc()) {
    $regions[] = $row;
}

echo json_encode($regions);

// Giải phóng tài nguyên
$stmt->close();
$conn->close();
?>

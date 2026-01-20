<?php
include '../Model/db.php';
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullname   = trim($_POST['fullname'] ?? '');
    $phone      = trim($_POST['phone'] ?? '');
    $country_id = intval($_POST['country'] ?? 0);
    $region_id  = intval($_POST['region'] ?? 0);

    // Kiểm tra thiếu thông tin
    if (!$fullname || !$phone || !$country_id || !$region_id) {
        echo json_encode(['success' => false, 'message' => 'Thiếu thông tin']);
        exit;
    }

    // Kiểm tra biến kết nối tồn tại
    if (!isset($conn) || !($conn instanceof mysqli)) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Không tìm thấy kết nối $conn. Kiểm tra lại db.php']);
        exit;
    }

    // Kiểm tra trùng số điện thoại
    $check = $conn->prepare("SELECT id FROM users WHERE phone = ?");
    if (!$check) {
        echo json_encode(['success' => false, 'message' => 'Lỗi prepare (check): ' . $conn->error]);
        exit;
    }
    $check->bind_param("s", $phone);
    $check->execute();
    $check->store_result();
    if ($check->num_rows > 0) {
        echo json_encode(['success' => false, 'message' => 'Số điện thoại đã tồn tại']);
        $check->close();
        $conn->close();
        exit;
    }
    $check->close();

    // Thêm người dùng mới
    $stmt = $conn->prepare("INSERT INTO users (fullname, phone, country_id, region_id) VALUES (?, ?, ?, ?)");
    if (!$stmt) {
        echo json_encode(['success' => false, 'message' => 'Lỗi prepare (insert): ' . $conn->error]);
        exit;
    }

    $stmt->bind_param("ssii", $fullname, $phone, $country_id, $region_id);
    $ok = $stmt->execute();

    if ($ok) {
        $new_user_id = $conn->insert_id; 
        echo json_encode([
            'success' => true,
            'user_id' => $new_user_id
        ]);
    }
    else {
        echo json_encode(['success' => false, 'message' => 'Lỗi khi lưu dữ liệu: ' . $stmt->error]);
    }

    $stmt->close();
    $conn->close();
}
?>

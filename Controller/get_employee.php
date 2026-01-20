<?php
header('Content-Type: application/json; charset=utf-8');

// BẮT ĐẦU SESSION VÀ KẾT NỐI DB
session_start(); 
require_once("../Model/db.php"); // dùng file DB sẵn có

// Lấy nameshop từ session (Đảm bảo bạn đã lưu nó trong login_account.php)
$current_nameshop = $_SESSION['nameshop'] ?? null; 

if ($current_nameshop === null) {
    // Nếu không có nameshop (chưa đăng nhập hoặc session hết hạn)
    http_response_code(401); // Trả về mã lỗi 401 Unauthorized
    echo json_encode(["status" => "error", "message" => "Vui lòng đăng nhập lại để xem danh sách nhân viên."]);
    $conn->close();
    exit;
}

// Lấy giá trị từ GET
$search     = trim($_GET['search'] ?? '');
$department = trim($_GET['department'] ?? '');
$position   = trim($_GET['position'] ?? '');
$status     = trim($_GET['status'] ?? '');

// KHỞI TẠO CÂU LỆNH SQL VÀ THAM SỐ
// Bắt đầu bằng điều kiện BẮT BUỘC: Lọc theo nameshop
$sql = "SELECT * FROM employees WHERE nameshop = ?";
$params_type = "s"; // Tham số đầu tiên (nameshop) là string (s)
$params_value = [$current_nameshop];

// 1. Lọc trạng thái (working / retired)
if (!empty($status) && $status !== 'all') { // Thêm check 'all' nếu có
    $sql .= " AND status = ?";
    $params_type .= "s";
    $params_value[] = $status;
}

// 2. Lọc phòng ban (Giả sử department_id là INT)
if (!empty($department)) {
    $sql .= " AND department_id = ?";
    $params_type .= "i";
    $params_value[] = (int)$department;
}

// 3. Lọc chức danh (Giả sử position_id là INT)
if (!empty($position)) {
    $sql .= " AND position_id = ?";
    $params_type .= "i";
    $params_value[] = (int)$position;
}

// 4. Tìm kiếm theo tên hoặc mã (search)
if (!empty($search)) {
    // Tìm kiếm cần hai tham số LIKE cho fullname VÀ employee_code
    $sql .= " AND (fullname LIKE ? OR employee_code LIKE ?)";
    $params_type .= "ss"; 
    $search_like = "%{$search}%";
    $params_value[] = $search_like;
    $params_value[] = $search_like;
}

$sql .= " ORDER BY id DESC"; // Sắp xếp để dữ liệu mới nhất lên đầu

// CHUẨN BỊ VÀ THỰC THI
$stmt = $conn->prepare($sql);

if ($stmt === false) {
    http_response_code(500);
    echo json_encode(["status" => "error", "message" => "Lỗi chuẩn bị truy vấn: " . $conn->error]);
    $conn->close();
    exit;
}

// Gắn tham số (Sử dụng '...' để truyền mảng giá trị vào bind_param)
$stmt->bind_param($params_type, ...$params_value);
$stmt->execute();
$result = $stmt->get_result();

$employees = [];

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $employees[] = $row;
    }
}

// Trả về JSON
echo json_encode([
    "status" => "success",
    "data" => $employees
]);

$stmt->close();
$conn->close();
?>
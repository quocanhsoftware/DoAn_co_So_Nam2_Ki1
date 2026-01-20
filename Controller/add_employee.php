<?php
header('Content-Type: application/json; charset=utf-8');
session_start(); // Bắt đầu session
require_once("../Model/db.php"); // Kết nối đến database

$nameshop_to_db = $_SESSION['nameshop'] ?? null; 
// Lấy ID của người đang quản lý (Chủ Shop/User ID)
$user_shop_id = $_SESSION['user_id'] ?? null;

if ($nameshop_to_db === null || $user_shop_id === null ) {
    http_response_code(401);
    echo json_encode(["status" => "error", "message" => "Lỗi: Vui lòng đăng nhập lại để thêm nhân viên."]);
    $conn->close();
    exit;
}
$id_shop_to_db = intval($user_shop_id); 


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Lấy dữ liệu từ POST
    $employee_code  = $_POST['employee_code'] ?? '';
    $password       = $_POST['password'] ?? ''; 
    $fullname       = $_POST['fullname'] ?? '';
    $phone          = $_POST['phone'] ?? '';
    $cccd           = $_POST['cccd'] ?? '';
    $department_id  = $_POST['department'] ?? null;
    $position_id    = $_POST['position'] ?? null;
    $avatar         = null;

    // Bắt buộc phải có Mật khẩu khi tạo mới
    if (empty($employee_code) || empty($fullname) || empty($password)) {
        echo json_encode(["status" => "error", "message" => "Mã nhân viên, Họ tên và Mật khẩu là bắt buộc"]);
        $conn->close();
        exit;
    }


    // 1. Xử lý upload ảnh (Giữ nguyên)
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = '../uploads/'; 
        $file_tmp = $_FILES['photo']['tmp_name'];
        $file_name = uniqid() . '_' . basename($_FILES['photo']['name']);
        $file_path = $upload_dir . $file_name;

        if (move_uploaded_file($file_tmp, $file_path)) {
            $avatar = $file_name;
        } else {
            echo json_encode(["status" => "error", "message" => "Lỗi khi lưu ảnh nhân viên"]);
            $conn->close();
            exit;
        }
    }
    
    // 2. KIỂM TRA TRÙNG LẶP THEO NAMESHOP
    // Kiểm tra mã nhân viên: cho phép tái sử dụng nếu nhân viên đã nghỉ (da_nghi)
    $check_code_sql = "SELECT employee_code, status FROM employees 
                       WHERE employee_code = ? AND nameshop = ?";
    $check_code_stmt = $conn->prepare($check_code_sql);
    $check_code_stmt->bind_param("ss", $employee_code, $nameshop_to_db);
    $check_code_stmt->execute();
    $check_code_result = $check_code_stmt->get_result();

    if ($check_code_result->num_rows > 0) {
        $existing_emp_code = $check_code_result->fetch_assoc();
        // Nếu mã nhân viên đã tồn tại và đang làm việc, báo lỗi
        if ($existing_emp_code['status'] === 'dang_lam_viec') {
            echo json_encode(["status" => "error", "message" => "Mã nhân viên **$employee_code** đã tồn tại trong cửa hàng này. Vui lòng chọn mã khác."]);
            $check_code_stmt->close();
            $conn->close();
            exit;
        }
        // Nếu trạng thái là 'da_nghi', cho phép tái sử dụng mã nhân viên
    }
    $check_code_stmt->close();

    // Kiểm tra số điện thoại: không cho phép trùng
    $check_phone_sql = "SELECT phone FROM employees 
                        WHERE phone = ? AND nameshop = ?";
    $check_phone_stmt = $conn->prepare($check_phone_sql);
    $check_phone_stmt->bind_param("ss", $phone, $nameshop_to_db);
    $check_phone_stmt->execute();
    $check_phone_result = $check_phone_stmt->get_result();

    if ($check_phone_result->num_rows > 0) {
        echo json_encode(["status" => "error", "message" => "Số điện thoại **$phone** đã được sử dụng bởi một nhân viên khác trong cửa hàng này."]);
        $check_phone_stmt->close();
        $conn->close();
        exit;
    }
    $check_phone_stmt->close();
    
    // 3. THỰC THI INSERT DỮ LIỆU BẰNG PREPARED STATEMENT
    $sql = "INSERT INTO employees 
             (employee_code, password, fullname, phone, cccd, department_id, position_id, avatar, status, nameshop, id_shop) 
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'dang_lam_viec', ?, ?)";
    
    $stmt = $conn->prepare($sql);
    // Sử dụng $password (chuỗi)
    // 10 tham số: s(employee_code), s(password), s, s, s, s, s, s(avatar), s(nameshop), i(id_shop)
    $stmt->bind_param("ssssssissi", 
        $employee_code, 
        $password, 
        $fullname, 
        $phone, 
        $cccd, 
        $department_id, 
        $position_id, 
        $avatar, 
        $nameshop_to_db, 
        $id_shop_to_db
    );

    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Thêm nhân viên **$fullname** thành công!"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Lỗi khi thêm nhân viên: " . $stmt->error]);
    }

    $stmt->close();
    $conn->close();
} 
else {
    echo json_encode(["status" => "error", "message" => "Phương thức yêu cầu không hợp lệ"]);
}
?>
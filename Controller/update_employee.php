<?php
// Controller/update_employee.php
header('Content-Type: application/json; charset=utf-8');
require_once("../Model/db.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1. Lấy và kiểm tra ID nhân viên
    $employee_id = $_POST['employee_id'] ?? null;

    if (empty($employee_id) || !is_numeric($employee_id)) {
        echo json_encode(["status" => "error", "message" => "ID nhân viên không hợp lệ hoặc bị thiếu."]);
        $conn->close();
        exit;
    }
    
    // Lấy dữ liệu từ form
    $id_safe = (int)$employee_id;
    $employee_code  = $_POST['employee_code'] ?? '';
    $password = $_POST['password'] ?? '';
    $fullname       = $_POST['fullname'] ?? '';
    $phone          = $_POST['phone'] ?? '';
    $cccd           = $_POST['cccd'] ?? '';
    $department_id  = $_POST['department'] ?? '';
    $position_id    = $_POST['position'] ?? '';
    
    // 2. Kiểm tra trạng thái nhân viên - không cho phép sửa nếu đã nghỉ
    $check_status_sql = "SELECT status FROM employees WHERE id = ?";
    $check_status_stmt = $conn->prepare($check_status_sql);
    $check_status_stmt->bind_param("i", $id_safe);
    $check_status_stmt->execute();
    $check_status_result = $check_status_stmt->get_result();
    
    if ($check_status_result->num_rows === 0) {
        echo json_encode(["status" => "error", "message" => "Không tìm thấy nhân viên với ID này."]);
        $check_status_stmt->close();
        $conn->close();
        exit;
    }
    
    $employee_data = $check_status_result->fetch_assoc();
    if ($employee_data['status'] === 'da_nghi') {
        echo json_encode(["status" => "error", "message" => "Không thể cập nhật thông tin nhân viên đã nghỉ việc."]);
        $check_status_stmt->close();
        $conn->close();
        exit;
    }
    $check_status_stmt->close();

    // Làm sạch dữ liệu đầu vào
    $employee_code  = $conn->real_escape_string($employee_code);
    $password = $conn->real_escape_string($password);
    $fullname       = $conn->real_escape_string($fullname);
    $phone          = $conn->real_escape_string($phone);
    $cccd           = $conn->real_escape_string($cccd);
    $department_id_db = $department_id ? "'" . $conn->real_escape_string($department_id) . "'" : "NULL";
    $position_id_db = $position_id ? "'" . $conn->real_escape_string($position_id) . "'" : "NULL";

    $avatar_update_clause = ""; // Mặc định không cập nhật ảnh

    // 3. Xử lý upload ảnh (nếu có file mới)
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = '../uploads/'; 
        $file_tmp = $_FILES['photo']['tmp_name'];
        $file_name = uniqid() . '_' . basename($_FILES['photo']['name']);
        $file_path = $upload_dir . $file_name;

        if (move_uploaded_file($file_tmp, $file_path)) {
            $avatar_update_clause = ", avatar = '" . $conn->real_escape_string($file_name) . "'";
        } else {
            echo json_encode(["status" => "error", "message" => "Lỗi khi lưu ảnh nhân viên"]);
            $conn->close();
            exit;
        }
    }


    // 4. Chuẩn bị và thực thi câu lệnh SQL UPDATE
    $sql = "UPDATE employees SET 
                employee_code = '$employee_code',
                password = '$password',
                fullname = '$fullname',
                phone = '$phone',
                cccd = '$cccd',
                department_id = {$department_id_db},
                position_id = {$position_id_db}
                {$avatar_update_clause}
            WHERE id = $id_safe";

    if ($conn->query($sql)) {
        echo json_encode(["status"=>"success","message"=>"Cập nhật nhân viên **$fullname** thành công!"]);
    } else {
        echo json_encode(["status"=>"error","message"=>"Lỗi khi cập nhật nhân viên: " . $conn->error]);
    }

    $conn->close();
} else {
    echo json_encode(["status" => "error", "message" => "Phương thức yêu cầu không hợp lệ"]);
}
?>
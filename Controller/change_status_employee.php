<?php
header('Content-Type: application/json; charset=utf-8');
require_once("../Model/db.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents("php://input"), true);
    $ids = $input['ids'] ?? [];

    if (empty($ids) || !is_array($ids)) {
        echo json_encode(["status"=>"error","message"=>"Danh sách ID không hợp lệ"]);
        exit;
    }

    $ids_safe = array_map('intval', $ids);
    $ids_str = implode(",", $ids_safe);

    $sql = "UPDATE employees SET status='da_nghi' WHERE id IN ($ids_str)";

    if ($conn->query($sql)) {
        echo json_encode(["status"=>"success","message"=>"Chuyển trạng thái thành công"]);
    } else {
        echo json_encode(["status"=>"error","message"=>"Lỗi khi cập nhật trạng thái"]);
    }

    $conn->close();
}
?>

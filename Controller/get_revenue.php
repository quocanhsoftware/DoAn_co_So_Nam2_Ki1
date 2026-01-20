<?php
header('Content-Type: application/json; charset=utf-8');
session_start();
require_once("../Model/db.php");

// Kiểm tra đăng nhập
if (!isset($_SESSION['fullname'])) {
    http_response_code(401);
    echo json_encode(["status" => "error", "message" => "Unauthorized"]);
    exit;
}

$time_range = $_GET['time'] ?? 'this_month';

// Xây dựng điều kiện WHERE thời gian
$where = "1=1";
$params = [];
$types = "";

switch ($time_range) {
    case 'today':
        $where .= " AND DATE(created_at) = CURDATE()";
        break;
    case 'yesterday':
        $where .= " AND DATE(created_at) = SUBDATE(CURDATE(), 1)";
        break;
    case '7days':
        $where .= " AND created_at >= SUBDATE(CURDATE(), 7)";
        break;
    case 'last_month':
        $where .= " AND MONTH(created_at) = MONTH(DATE_SUB(CURRENT_DATE(), INTERVAL 1 MONTH)) AND YEAR(created_at) = YEAR(DATE_SUB(CURRENT_DATE(), INTERVAL 1 MONTH))";
        break;
    case 'this_month':
    default:
        $where .= " AND MONTH(created_at) = MONTH(CURRENT_DATE()) AND YEAR(created_at) = YEAR(CURRENT_DATE())";
        break;
}

// Lọc theo shop đang đăng nhập (chỉ xem được dữ liệu của đúng shop đó)
if (!isset($_SESSION['shop_id'])) {
    http_response_code(403);
    echo json_encode(["status" => "error", "message" => "Shop không hợp lệ"]);
    exit;
}

// Giả sử bảng orders có cột id_shop
$where .= " AND id_shop = ?";
$params[] = (int)$_SESSION['shop_id'];
$types .= "i";

// 1. Lấy Summary
$sqlSummary = "SELECT 
    COALESCE(SUM(final_amount), 0) as total_revenue,
    COUNT(*) as total_orders,
    COALESCE(SUM(total_quantity), 0) as total_products
FROM orders WHERE $where";

$stmt = $conn->prepare($sqlSummary);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$summary = $stmt->get_result()->fetch_assoc();
$stmt->close();

// 2. Lấy dữ liệu biểu đồ (Theo ngày)
$sqlChart = "SELECT 
    DATE(created_at) as date,
    COALESCE(SUM(final_amount), 0) as revenue
FROM orders 
WHERE $where 
GROUP BY DATE(created_at) 
ORDER BY date ASC";

$stmt = $conn->prepare($sqlChart);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$chartResult = $stmt->get_result();
$chartData = [];
while ($row = $chartResult->fetch_assoc()) {
    $chartData[] = $row;
}
$stmt->close();

// 3. Lấy dữ liệu bảng chi tiết (Theo ngày)
$sqlTable = "SELECT 
    DATE(created_at) as date,
    COUNT(*) as order_count,
    COALESCE(SUM(total_amount), 0) as total_amount,
    COALESCE(SUM(discount), 0) as total_discount,
    COALESCE(SUM(final_amount), 0) as final_amount
FROM orders 
WHERE $where 
GROUP BY DATE(created_at) 
ORDER BY date DESC";

$stmt = $conn->prepare($sqlTable);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$tableResult = $stmt->get_result();
$tableData = [];
while ($row = $tableResult->fetch_assoc()) {
    $tableData[] = $row;
}
$stmt->close();

// Trả về JSON
echo json_encode([
    "status" => "success",
    "summary" => $summary,
    "chart" => $chartData,
    "table" => $tableData
]);

$conn->close();
?>
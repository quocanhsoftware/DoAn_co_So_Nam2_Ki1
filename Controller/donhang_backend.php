<?php
session_start();

if (!isset($_SESSION['fullname'])) {
    header("Location: ../View/login.php");
    exit;
}

include '../Model/db.php';

$user_name = $_SESSION['fullname'] ?? 'User';
$nameshop  = $_SESSION['nameshop'] ?? '';
$shop_id   = intval($_SESSION['shop_id'] ?? 0);

if ($shop_id === 0 && (($_SESSION['account_type'] ?? '') === 'user')) {
    $shop_id = intval($_SESSION['user_id'] ?? 0);
}

function vnd($n) {
    return number_format((float)$n, 0, ',', '.');
}

/* ===== FILTER ===== */
$time_mode   = $_GET['time_mode'] ?? 'month';
$from        = trim($_GET['from'] ?? '');
$to          = trim($_GET['to'] ?? '');
$employee_id = intval($_GET['employee_id'] ?? 0);

/* ===== LOAD EMPLOYEES ===== */
$employees = [];
if ($shop_id > 0) {
    $stmt = $conn->prepare(
        "SELECT id, fullname, employee_code 
         FROM employees 
         WHERE id_shop = ?
         ORDER BY fullname ASC"
    );
    $stmt->bind_param("i", $shop_id);
    $stmt->execute();
    $rs = $stmt->get_result();
    while ($r = $rs->fetch_assoc()) {
        $employees[] = $r;
    }
    $stmt->close();
}

/* ===== MAIN QUERY ===== */
$sql = "
    SELECT 
        o.id,
        o.order_code,
        o.total_quantity,
        o.total_amount,
        o.discount,
        o.final_amount,
        o.payment_method,
        o.created_at,
        o.updated_at,
        e.fullname AS employee_name,
        e.employee_code
    FROM orders o
    LEFT JOIN employees e ON o.employee_id = e.id
";

$where  = " WHERE o.id_shop = ?";
$types  = "i";
$params = [$shop_id];

/* ===== FILTER BY EMPLOYEE ===== */
if ($employee_id > 0) {
    $where .= " AND o.employee_id = ?";
    $types .= "i";
    $params[] = $employee_id;
}

/* ===== FILTER BY TIME ===== */
if ($time_mode === 'custom') {
    if ($from !== '') {
        $where .= " AND o.created_at >= ?";
        $types .= "s";
        $params[] = $from . " 00:00:00";
    }
    if ($to !== '') {
        $where .= " AND o.created_at <= ?";
        $types .= "s";
        $params[] = $to . " 23:59:59";
    }
} else {
    $start = date('Y-m-01 00:00:00');
    $end   = date('Y-m-d 00:00:00', strtotime('first day of next month'));
    $where .= " AND o.created_at >= ? AND o.created_at < ?";
    $types .= "ss";
    $params[] = $start;
    $params[] = $end;
}

$sql .= $where . " ORDER BY o.created_at DESC, o.id DESC";

/* ===== EXECUTE ===== */
$rows = [];
$sum_total    = 0;
$sum_final    = 0;
$sum_discount = 0;
$sum_qty      = 0;

$stmt = $conn->prepare($sql);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$rs = $stmt->get_result();

while ($r = $rs->fetch_assoc()) {
    $rows[] = $r;
    $sum_total    += (float)$r['total_amount'];
    $sum_final    += (float)$r['final_amount'];
    $sum_discount += (float)$r['discount'];
    $sum_qty      += (int)$r['total_quantity'];
}

$stmt->close();
?>

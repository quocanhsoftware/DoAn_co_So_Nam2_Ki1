<?php
include '../Model/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nameshop = trim($_POST['storeName'] ?? '');

    if ($nameshop === '') {
        echo "<script>alert('Vui lòng nhập tên cửa hàng'); history.back();</script>";
        exit;
    }

    $stmt = $conn->prepare("SELECT id FROM users WHERE nameshop = ?");
    $stmt->bind_param("s", $nameshop);
    
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();
        $shop_id = $row['id'];

        // Truyền shop_id sang login.php để dùng trong login_account.php
        header("Location: ../View/login.php?shop_id=$shop_id");
        exit;
    } else {
        echo "<script>alert('Tên cửa hàng không tồn tại!'); history.back();</script>";
        exit;
    }
}
?>

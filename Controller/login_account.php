<?php
include '../Model/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $phone    = trim($_POST['phone'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $action   = $_POST['action'] ?? '';
    // $shop_id là id của shop mà nhân viên/chủ shop muốn đăng nhập vào
    $shop_id  = intval($_POST['shop_id'] ?? 0);

    if ($phone === '' || $password === '' || $shop_id === 0) {
        echo "<script>alert('Thiếu thông tin!'); history.back();</script>";
        exit;
    }

    $user = null; // Khởi tạo biến $user
    $nameshop = ''; // Khởi tạo biến $nameshop

    // --- LOGIC CHO HÀNH ĐỘNG 'MANAGE' (ĐĂNG NHẬP CHỦ SHOP) ---
    if ($action === 'manage') {
        // Kiểm tra phone + password của đúng user_id = $shop_id trong bảng users
        $stmt = $conn->prepare("SELECT id, fullname, phone, password, nameshop FROM users WHERE id = ? AND phone = ?");
        $stmt->bind_param("is", $shop_id, $phone);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
        }

    // --- LOGIC CHO HÀNH ĐỘNG 'SELL' (ĐĂNG NHẬP NHÂN VIÊN) ---
    } elseif ($action === 'sell') {
        $stmt = $conn->prepare("SELECT id, fullname, phone, password, id_shop, status FROM employees WHERE id_shop = ? AND phone = ?");
        $stmt->bind_param("is", $shop_id, $phone);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 1) {
            $employee_data = $result->fetch_assoc();
            
            // Lấy tên shop từ bảng users (hoặc shop/nameshop nếu có)
            $shop_stmt = $conn->prepare("SELECT nameshop FROM users WHERE id = ?");
            $shop_stmt->bind_param("i", $shop_id);
            $shop_stmt->execute();
            $shop_result = $shop_stmt->get_result();
            if ($shop_result->num_rows === 1) {
                $nameshop = $shop_result->fetch_assoc()['nameshop'];
            }
            
            // Gán dữ liệu nhân viên vào biến $user và thêm nameshop
            $user = $employee_data;
            $user['nameshop'] = $nameshop;
        }
    }

    // --- XỬ LÝ KẾT QUẢ CHUNG ---
    
// Nếu không tìm thấy người dùng (chủ shop hoặc nhân viên)
if ($user === null) {
    echo "<script>alert('Thông tin đăng nhập không đúng!'); history.back();</script>";
    exit;
}

// --- BƯỚC 2: Kiểm tra mật khẩu theo ACTION ---

$password_ok = false;

if ($action === 'manage') {

    if (password_verify($password, $user['password'])) {
        $password_ok = true;
    }
} elseif ($action === 'sell') {
  
    if ($password === $user['password']) {
        $password_ok = true;
    }
}

// Nếu kiểm tra mật khẩu không thành công
if (!$password_ok) {
    echo "<script>alert('Mật khẩu không đúng!'); history.back();</script>";
    exit;
}

// --- BƯỚC 3: Kiểm tra trạng thái nhân viên (chỉ áp dụng cho action 'sell') ---
if ($action === 'sell' && isset($user['status']) && $user['status'] === 'da_nghi') {
    echo "<script>alert('Tài khoản này đã nghỉ việc, không thể đăng nhập!'); history.back();</script>";
    exit;
}

    // --- ĐĂNG NHẬP THÀNH CÔNG VÀ CHUYỂN TRANG ---
    session_start();
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['shop_id'] = $shop_id;
    $_SESSION['fullname'] = $user['fullname'];
    $_SESSION['phone'] = $user['phone'];
    $_SESSION['nameshop'] = $user['nameshop'];
    $_SESSION['account_type'] = $action === 'sell' ? 'employee' : 'user';

    // Chuyển trang theo action
    if ($action === 'manage') {
        header("Location: ../View/nhanvien.php");
        exit;
    }
    if ($action === 'sell') {
        header("Location: ../View/sell.php?shop_id=$shop_id");
        exit;
    }

    echo "<script>alert('Hành động không hợp lệ!'); history.back();</script>";
    exit;
}
?>

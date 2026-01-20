<?php
session_start();

/* =========================
   1. Kiểm tra đăng nhập
========================= */
if (!isset($_SESSION['fullname'])) {
    die("Bạn chưa đăng nhập!");
}

$employee_name  = $_SESSION['fullname'];
$employee_phone = $_SESSION['phone'] ?? '';

/* =========================
   2. Kết nối CSDL
========================= */
include '../Model/db.php';

/* =========================
   3. Hàm định dạng tiền
========================= */
function vnd($n) {
    return number_format((float)$n, 0, ',', '.');
}

/* =========================
   4. Khởi tạo giỏ hàng
========================= */
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

/* =========================
   5. Thông tin shop
========================= */
$PRODUCT_TABLE = 'products';

$shop_id = intval($_SESSION['shop_id'] ?? 0);
if ($shop_id === 0) {
    $shop_id = intval($_GET['shop_id'] ?? 0);
}
if ($shop_id > 0) {
    $_SESSION['shop_id'] = $shop_id;
}

/* =========================
   6. Kiểm tra products có id_shop không
========================= */
$has_shop_column = false;
if ($stmt = $conn->prepare("
    SELECT COUNT(*) AS cnt 
    FROM information_schema.columns 
    WHERE table_schema = DATABASE()
      AND table_name = ?
      AND column_name = 'id_shop'
")) {
    $stmt->bind_param('s', $PRODUCT_TABLE);
    $stmt->execute();
    $rs = $stmt->get_result();
    if ($row = $rs->fetch_assoc()) {
        $has_shop_column = ((int)$row['cnt'] > 0);
    }
    $stmt->close();
}

/* =========================
   7. Xử lý POST
========================= */
$clearSearch = false;
$checkout_print_token = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    /* --------- Thêm sản phẩm --------- */
    if ($action === 'add') {
        $pid = intval($_POST['product_id'] ?? 0);
        $qty = max(1, intval($_POST['qty'] ?? 1));

        if ($has_shop_column && $shop_id > 0) {
            $stmt = $conn->prepare("
                SELECT id, product_code, name, sale_price, image
                FROM products
                WHERE id = ? AND id_shop = ?
            ");
            $stmt->bind_param('ii', $pid, $shop_id);
        } else {
            $stmt = $conn->prepare("
                SELECT id, product_code, name, sale_price, image
                FROM products
                WHERE id = ?
            ");
            $stmt->bind_param('i', $pid);
        }

        if ($stmt->execute()) {
            $rs = $stmt->get_result();
            if ($p = $rs->fetch_assoc()) {
                if (!isset($_SESSION['cart'][$pid])) {
                    $_SESSION['cart'][$pid] = [
                        'id'    => $pid,
                        'code'  => $p['product_code'],
                        'name'  => $p['name'],
                        'price' => (float)$p['sale_price'],
                        'qty'   => 0,
                        'image' => $p['image']
                    ];
                }
                $_SESSION['cart'][$pid]['qty'] += $qty;
                $clearSearch = true;
            }
        }
        $stmt->close();

    /* --------- Cập nhật số lượng --------- */
    } elseif ($action === 'update') {
        $pid = intval($_POST['product_id'] ?? 0);
        $qty = max(0, intval($_POST['qty'] ?? 0));

        if (isset($_SESSION['cart'][$pid])) {
            if ($qty === 0) {
                unset($_SESSION['cart'][$pid]);
            } else {
                $_SESSION['cart'][$pid]['qty'] = $qty;
            }
        }

    /* --------- Xóa sản phẩm --------- */
    } elseif ($action === 'remove') {
        $pid = intval($_POST['product_id'] ?? 0);
        unset($_SESSION['cart'][$pid]);

    /* --------- THANH TOÁN / TẠO ĐƠN --------- */
    } elseif ($action === 'checkout') {

        if (empty($_SESSION['cart'])) {
            $checkout_msg = "Giỏ hàng trống";
            $clearSearch = true;
        } else {
        $discount = (float)($_POST['discount'] ?? 0);
        $payment_method = trim($_POST['payment_method'] ?? '');

        $total_qty = 0;
        $total_amount = 0.0;

        foreach ($_SESSION['cart'] as $it) {
            $total_qty += (int)$it['qty'];
            $total_amount += (float)$it['price'] * (int)$it['qty'];
        }

        $final_amount = max(0, $total_amount - $discount);
        $order_code = 'OD' . date('ymdHis') . substr((string)mt_rand(), 0, 3);

        $employee_id = (($_SESSION['account_type'] ?? '') === 'employee')
            ? intval($_SESSION['user_id'] ?? 0)
            : null;

        $conn->begin_transaction();
        $order_id = 0;
        $checkout_error = '';

        foreach ($_SESSION['cart'] as $it) {
            $pid = (int)($it['id'] ?? 0);
            $qty = max(0, (int)($it['qty'] ?? 0));
            if ($pid <= 0 || $qty <= 0) {
                continue;
            }

            if ($has_shop_column && $shop_id > 0) {
                $stmt = $conn->prepare("SELECT stock, name FROM products WHERE id = ? AND id_shop = ? FOR UPDATE");
                $stmt->bind_param('ii', $pid, $shop_id);
            } else {
                $stmt = $conn->prepare("SELECT stock, name FROM products WHERE id = ? FOR UPDATE");
                $stmt->bind_param('i', $pid);
            }

            if (!$stmt || !$stmt->execute()) {
                $checkout_error = "Không kiểm tra được tồn kho";
                if ($stmt) { $stmt->close(); }
                break;
            }

            $rs = $stmt->get_result();
            $p = $rs ? $rs->fetch_assoc() : null;
            $stmt->close();

            $stock = isset($p['stock']) ? (int)$p['stock'] : 0;
            $pname = (string)($p['name'] ?? '');

            if ($stock < $qty) {
                $checkout_error = "Tồn kho không đủ: " . ($pname !== '' ? $pname : ('SP #' . $pid));
                break;
            }
        }

        if ($checkout_error === '') {
            $stmt = $conn->prepare("
                INSERT INTO orders (
                    order_code,
                    id_shop,
                    employee_id,
                    total_quantity,
                    total_amount,
                    discount,
                    final_amount,
                    payment_method,
                    created_at
                ) VALUES (?,?,?,?,?,?,?,?,NOW())
            ");
            if ($stmt) {
                $stmt->bind_param(
                    'siidddds',
                    $order_code,
                    $shop_id,
                    $employee_id,
                    $total_qty,
                    $total_amount,
                    $discount,
                    $final_amount,
                    $payment_method
                );
                if ($stmt->execute()) {
                    $order_id = (int)$conn->insert_id;
                } else {
                    $checkout_error = "Tạo đơn thất bại";
                }
                $stmt->close();
            } else {
                $checkout_error = "Tạo đơn thất bại";
            }
        }

        if ($checkout_error === '') {
            foreach ($_SESSION['cart'] as $it) {
                $pid = (int)($it['id'] ?? 0);
                $qty = max(0, (int)($it['qty'] ?? 0));
                if ($pid <= 0 || $qty <= 0) {
                    continue;
                }

                if ($has_shop_column && $shop_id > 0) {
                    $stmt = $conn->prepare("UPDATE products SET stock = stock - ? WHERE id = ? AND id_shop = ? AND stock >= ?");
                    $stmt->bind_param('iiii', $qty, $pid, $shop_id, $qty);
                } else {
                    $stmt = $conn->prepare("UPDATE products SET stock = stock - ? WHERE id = ? AND stock >= ?");
                    $stmt->bind_param('iii', $qty, $pid, $qty);
                }

                if (!$stmt || !$stmt->execute() || $stmt->affected_rows !== 1) {
                    $checkout_error = "Không trừ được tồn kho";
                    if ($stmt) { $stmt->close(); }
                    break;
                }
                $stmt->close();
            }
        }

        if ($checkout_error === '') {
            $conn->commit();
            $checkout_print_token = bin2hex(random_bytes(16));
            $_SESSION['last_print_bill'] = [
                'token' => $checkout_print_token,
                'order_id' => $order_id,
                'order_code' => $order_code,
                'created_at' => date('Y-m-d H:i:s'),
                'id_shop' => $shop_id,
                'employee_id' => $employee_id,
                'employee_name' => $employee_name,
                'employee_phone' => $employee_phone,
                'total_quantity' => $total_qty,
                'total_amount' => $total_amount,
                'discount' => $discount,
                'final_amount' => $final_amount,
                'payment_method' => $payment_method,
                'items' => array_values($_SESSION['cart']),
            ];
            $_SESSION['cart'] = [];
            $checkout_msg = "Tạo đơn thành công: $order_code";
        } else {
            $conn->rollback();
            $checkout_msg = $checkout_error;
        }

        $clearSearch = true;
        }
    }
}

/* =========================
   8. Tìm kiếm / load sản phẩm
========================= */
$q = trim($_GET['q'] ?? '');
if ($clearSearch) $q = '';

$products = [];

if ($q !== '') {
    $like = "%$q%";
    if ($has_shop_column && $shop_id > 0) {
        $stmt = $conn->prepare("
            SELECT id, product_code, name, image, sale_price, stock
            FROM products
            WHERE (name LIKE ? OR product_code LIKE ?)
              AND id_shop = ?
            ORDER BY updated_at DESC, id DESC
        ");
        $stmt->bind_param('ssi', $like, $like, $shop_id);
    } else {
        $stmt = $conn->prepare("
            SELECT id, product_code, name, image, sale_price, stock
            FROM products
            WHERE name LIKE ? OR product_code LIKE ?
            ORDER BY updated_at DESC, id DESC
        ");
        $stmt->bind_param('ss', $like, $like);
    }

    if ($stmt->execute()) {
        $rs = $stmt->get_result();
        while ($r = $rs->fetch_assoc()) {
            $products[] = $r;
        }
    }
    $stmt->close();

} else {
    if ($has_shop_column && $shop_id > 0) {
        $stmt = $conn->prepare("
            SELECT id, product_code, name, image, sale_price, stock
            FROM products
            WHERE id_shop = ?
            ORDER BY updated_at DESC, id DESC
            LIMIT 40
        ");
        $stmt->bind_param('i', $shop_id);
        $stmt->execute();
        $rs = $stmt->get_result();
        while ($r = $rs->fetch_assoc()) {
            $products[] = $r;
        }
        $stmt->close();
    } else {
        $res = $conn->query("
            SELECT id, product_code, name, image, sale_price, stock
            FROM products
            ORDER BY updated_at DESC, id DESC
            LIMIT 40
        ");
        while ($r = $res->fetch_assoc()) {
            $products[] = $r;
        }
    }
}

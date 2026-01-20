<?php
session_start();

if (!isset($_SESSION['fullname'])) {
    die("Bạn chưa đăng nhập!");
}

function vnd($n) {
    return number_format((float)$n, 0, ',', '.');
}

$token = $_GET['token'] ?? '';
$bill = $_SESSION['last_print_bill'] ?? null;

$isValid = is_array($bill)
    && isset($bill['token'])
    && hash_equals((string)$bill['token'], (string)$token);

if (!$isValid) {
    $bill = null;
}

?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>In hóa đơn</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; background: #f3f4f6; color: #111827; }
        .wrap { max-width: 520px; margin: 16px auto; padding: 16px; }
        .card { background: #fff; border: 1px solid #e5e7eb; border-radius: 10px; padding: 16px; }
        .title { font-size: 18px; font-weight: 700; margin: 0 0 12px; text-align: center; }
        .meta { font-size: 13px; color: #374151; }
        .meta-row { display: flex; justify-content: space-between; gap: 10px; margin: 4px 0; }
        .table { width: 100%; border-collapse: collapse; margin-top: 12px; }
        .table th, .table td { padding: 8px 6px; border-bottom: 1px dashed #e5e7eb; font-size: 13px; vertical-align: top; }
        .table th { text-align: left; color: #374151; font-weight: 700; }
        .right { text-align: right; white-space: nowrap; }
        .sum { margin-top: 12px; border-top: 2px solid #111827; padding-top: 10px; }
        .sum-row { display: flex; justify-content: space-between; gap: 10px; margin: 6px 0; }
        .sum-row strong { font-size: 14px; }
        .actions { display: flex; gap: 10px; margin-top: 12px; }
        .btn { flex: 1; border: 1px solid #e5e7eb; background: #fff; padding: 10px 12px; border-radius: 8px; cursor: pointer; font-weight: 600; }
        .btn-primary { background: #0a66ff; border-color: #0a66ff; color: #fff; }
        @media print {
            body { background: #fff; }
            .wrap { margin: 0; padding: 0; max-width: none; }
            .card { border: none; border-radius: 0; padding: 0; }
            .actions { display: none; }
        }
    </style>
</head>
<body>
<div class="wrap">
    <div class="card">
        <?php if (!$bill) { ?>
            <div class="title">Không tìm thấy hóa đơn để in</div>
            <div class="actions">
                <button class="btn btn-primary" onclick="window.location.href='sell.php'">Quay lại bán hàng</button>
            </div>
        <?php } else { ?>
            <h1 class="title">HÓA ĐƠN BÁN HÀNG</h1>

            <div class="meta">
                <div class="meta-row">
                    <span>Mã đơn</span>
                    <strong><?php echo htmlspecialchars($bill['order_code']); ?></strong>
                </div>
                <div class="meta-row">
                    <span>Thời gian</span>
                    <span><?php echo htmlspecialchars($bill['created_at']); ?></span>
                </div>
                <div class="meta-row">
                    <span>Nhân viên</span>
                    <span><?php echo htmlspecialchars($bill['employee_name']); ?></span>
                </div>
                <div class="meta-row">
                    <span>Thanh toán</span>
                    <span><?php echo htmlspecialchars($bill['payment_method']); ?></span>
                </div>
            </div>

            <table class="table">
                <thead>
                <tr>
                    <th>Sản phẩm</th>
                    <th class="right">SL</th>
                    <th class="right">Giá</th>
                    <th class="right">T.tiền</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach (($bill['items'] ?? []) as $it) { ?>
                    <?php
                    $qty = (int)($it['qty'] ?? 0);
                    $price = (float)($it['price'] ?? 0);
                    $lineTotal = $qty * $price;
                    ?>
                    <tr>
                        <td>
                            <div style="font-weight:600;"><?php echo htmlspecialchars((string)($it['name'] ?? '')); ?></div>
                            <div style="color:#6b7280;font-size:12px;"><?php echo htmlspecialchars((string)($it['code'] ?? '')); ?></div>
                        </td>
                        <td class="right"><?php echo $qty; ?></td>
                        <td class="right"><?php echo vnd($price); ?></td>
                        <td class="right"><?php echo vnd($lineTotal); ?></td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>

            <div class="sum">
                <div class="sum-row">
                    <span>Tổng số lượng</span>
                    <strong><?php echo (int)$bill['total_quantity']; ?></strong>
                </div>
                <div class="sum-row">
                    <span>Tổng tiền hàng</span>
                    <strong><?php echo vnd($bill['total_amount']); ?></strong>
                </div>
                <div class="sum-row">
                    <span>Giảm giá</span>
                    <strong><?php echo vnd($bill['discount']); ?></strong>
                </div>
                <div class="sum-row">
                    <span>Khách cần trả</span>
                    <strong><?php echo vnd($bill['final_amount']); ?></strong>
                </div>
            </div>

            <div class="actions">
                <button class="btn" onclick="window.location.href='sell.php'">Quay lại bán hàng</button>
                <button class="btn btn-primary" onclick="window.print()">In hóa đơn</button>
            </div>
        <?php } ?>
    </div>
</div>

<?php if ($bill) { ?>
<script>
    window.addEventListener('load', function () {
        window.print();
    });
</script>
<?php } ?>
</body>
</html>


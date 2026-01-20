<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bán hàng</title>

    <!-- CSS -->
    <link rel="stylesheet" href="../sale.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>

<?php include '../Controller/sell_backend.php'; ?>

<!-- ================= POS WRAP ================= -->
<div class="pos-wrap">

    <!-- ===== TOP BAR ===== -->
    <div class="pos-topbar">
        <div class="left">

            <!-- SEARCH -->
            <form class="search-box" method="get">
                <i class="bi bi-search" style="color:#00aaff;"></i>
                <input
                    type="text"
                    name="q"
                    placeholder="Tìm hàng hóa (F3)"
                    value="<?php echo htmlspecialchars($q); ?>"
                >
            </form>

            <!-- SEARCH SUGGEST -->
            <?php if ($q !== '' && count($products) > 0) { ?>
                <div class="search-suggest">
                    <?php foreach (array_slice($products, 0, 6) as $p) { ?>
                        <div class="suggest-item">
                            <div class="si-left">
                                <?php if (!empty($p['image'])) { ?>
                                    <img src="<?php echo htmlspecialchars($p['image']); ?>" class="si-thumb" alt="">
                                <?php } ?>
                                <div>
                                    <div class="si-name"><?php echo htmlspecialchars($p['name']); ?></div>
                                    <div class="si-meta">
                                        <?php echo htmlspecialchars($p['product_code']); ?>
                                        · Tồn: <?php echo intval($p['stock']); ?>
                                    </div>
                                </div>
                            </div>

                            <div class="si-right">
                                <div class="si-price"><?php echo vnd($p['sale_price']); ?></div>
                                <form method="post">
                                    <input type="hidden" name="action" value="add">
                                    <input type="hidden" name="product_id" value="<?php echo intval($p['id']); ?>">
                                    <input type="number" name="qty" value="1" min="1" class="si-qty">
                                    <button type="submit" class="si-add">Chọn</button>
                                </form>
                            </div>
                        </div>
                    <?php } ?>
                    <div class="suggest-more">+ Thêm mới hàng hóa</div>
                </div>
            <?php } ?>

            <!-- INVOICE TAB -->
            <div class="invoice-tab">
                <i style="color:#00aaff;" class="bi bi-arrow-left-right"></i>
                <span>Hóa đơn</span>
            </div>
        </div>

        <!-- RIGHT TOOLS -->
        <div class="right-tools">
            <div class="user">
                <span>Tài khoản: <?php echo htmlspecialchars($employee_phone); ?></span>
            </div>

            <div class="dropdown-menu-container">
                <button class="icon-btn" id="menuToggle" type="button">
                    <i class="bi bi-list"></i>
                </button>
                <ul class="dropdown-menu" id="dropdownMenu">
                    <li>
                        <a class="dropdown-item" href="../Controller/logout.php">Đăng xuất</a>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <!-- ================= MAIN ================= -->
    <div class="pos-main">

        <!-- ===== ITEMS AREA ===== -->
        <div class="items-area">
            <div class="cart-list">
                <?php foreach ($_SESSION['cart'] as $it) { ?>
                    <div class="cart-row">

                        <div class="cart-left">
                            <?php if (!empty($it['image'])) { ?>
                                <img src="<?php echo htmlspecialchars($it['image']); ?>" class="cart-thumb" alt="">
                            <?php } ?>
                            <div>
                                <div class="cart-name"><?php echo htmlspecialchars($it['name']); ?></div>
                                <div class="cart-code"><?php echo htmlspecialchars($it['code']); ?></div>
                            </div>
                        </div>

                        <div class="cart-mid">
                            <form method="post" class="cart-qty">
                                <input type="hidden" name="action" value="update">
                                <input type="hidden" name="product_id" value="<?php echo intval($it['id']); ?>">
                                <input type="number" name="qty" value="<?php echo intval($it['qty']); ?>" min="0">
                                <button type="submit" class="cart-btn">Cập nhật</button>
                            </form>

                            <form method="post" class="cart-remove">
                                <input type="hidden" name="action" value="remove">
                                <input type="hidden" name="product_id" value="<?php echo intval($it['id']); ?>">
                                <button type="submit" class="cart-btn danger">Xóa</button>
                            </form>
                        </div>

                        <div class="cart-right">
                            <div class="cart-price"><?php echo vnd($it['price']); ?></div>
                            <div class="cart-sub">
                                <?php echo vnd($it['price'] * (int)$it['qty']); ?>
                            </div>
                        </div>

                    </div>
                <?php } ?>
            </div>
        </div>

        <!-- ===== BILL AREA ===== -->
        <div class="bill-area">

            <div class="employee-bar">
                <div class="employee-select">
                    <input type="text" value="<?php echo htmlspecialchars($employee_name); ?>" readonly>
                    <button class="dropdown">
                        <i class="bi bi-chevron-down"></i>
                    </button>
                </div>
                <div class="date-time" id="dateTime"></div>
            </div>

            <?php
            $total_qty = 0;
            $total_amount = 0.0;
            foreach ($_SESSION['cart'] as $it) {
                $total_qty += (int)$it['qty'];
                $total_amount += (float)$it['price'] * (int)$it['qty'];
            }
            ?>

            <form method="post" class="summary">

                <div class="row">
                    <span>Tổng tiền hàng</span>
                    <span class="value"><?php echo vnd($total_amount); ?></span>
                </div>

                <div class="row">
                    <span>Giảm giá</span>
                    <span class="value">
                        <input type="number" name="discount" step="0.01" value="0" class="discount-input">
                    </span>
                </div>

                <div class="row">
                    <span>Phương thức thanh toán</span>
                    <span class="value">
                        <select name="payment_method" id="paymentMethod" class="form-select form-select-sm">
                            <option value="cash">Tiền mặt</option>
                            <option value="bank">Chuyển khoản</option>
                        </select>
                    </span>
                </div>

                <input type="hidden" name="action" value="checkout">


                <!-- QR CODE -->
                <div id="qrCodeSection" class="qr-code-section" style="display:none;">
                    <div class="qr-code-container">
                        <div class="qr-code-title">Quét mã QR để thanh toán</div>
                        <div id="qrcode" class="qr-code-display"></div>
                        <div class="qr-code-amount">
                            <span class="qr-label">Số tiền:</span>
                            <span class="qr-value" id="qrAmount">0 đ</span>
                        </div>
                        <div class="qr-code-note">
                            Vui lòng quét mã QR bằng ứng dụng ngân hàng của bạn
                        </div>
                    </div>
                </div>

                <button class="pay-btn">THANH TOÁN</button>
            </form>

            <?php if (!empty($checkout_msg)) { ?>
                <div class="alert-info" style="margin:10px 0;">
                    <?php echo htmlspecialchars($checkout_msg); ?>
                </div>
            <?php } ?>

        </div>
    </div>

    <!-- ===== BOTTOM ===== -->
    <div class="pos-bottom">
        <div class="note-box">
            <i class="bi bi-pencil"></i>
            <input type="text" placeholder="Ghi chú đơn hàng">
        </div>

        <div class="sale-modes">
            <button class="mode active" data-mode="fast">Bán nhanh</button>
        </div>

        <div class="support">
            <span class="phone">1900 6522</span>
            <button class="chat">
                <i class="bi bi-chat-dots"></i>
            </button>
        </div>
    </div>

</div>

<!-- ================= SCRIPTS ================= -->

<script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>
<script src="../Controller_js/sale.js"></script>
<script src="../Controller_js/Get_Time.js"></script>



<script>
    window.TOTAL_AMOUNT = <?= json_encode($total_amount) ?>;
</script>

<script src="../Controller_js/chuyenkhoan.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const menuToggle = document.getElementById('menuToggle');
        const dropdownMenu = document.getElementById('dropdownMenu');

        if (menuToggle && dropdownMenu) {
            menuToggle.addEventListener('click', function (e) {
                e.stopPropagation();
                dropdownMenu.classList.toggle('show');
            });

            document.addEventListener('click', function (e) {
                if (!menuToggle.contains(e.target) && !dropdownMenu.contains(e.target)) {
                    dropdownMenu.classList.remove('show');
                }
            });
        }
    });
</script>

<?php if (!empty($checkout_print_token)) { ?>
<script>
    (function () {
        const token = <?= json_encode($checkout_print_token) ?>;
        const key = 'printed_bill_token_' + token;
        if (!localStorage.getItem(key)) {
            localStorage.setItem(key, '1');
            window.open('print_bill.php?token=' + encodeURIComponent(token), '_blank', 'noopener,noreferrer');
        }
    })();
</script>
<?php } ?>

</body>
</html>

<?php include '../Controller/donhang_backend.php'; ?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đơn hàng - KiotViet</title>

    <!-- ================= CSS ================= -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="../donhang.css">
</head>

<body>

<!-- ================= TOP HEADER ================= -->
<div class="top-header">
    <div class="container-fluid d-flex justify-content-between align-items-center">

        <div class="logo-brand d-flex align-items-center">
            <div class="brand-dot-small"></div>
            <span class="brand-text">KiotViet</span>
        </div>

        <div class="d-flex align-items-center gap-3">
            <div class="dropdown">
                <button class="icon-btn dropdown-toggle" type="button" data-bs-toggle="dropdown">
                    <i class="bi bi-gear"></i>
                </button>
                <ul class="dropdown-menu">
                    <li>
                        <a class="dropdown-item" href="../Controller/logout.php">Đăng xuất</a>
                    </li>
                </ul>
            </div>

            <div class="user-profile d-flex align-items-center">
                <div class="avatar-circle">
                    <i class="bi bi-person-fill"></i>
                </div>
                <span class="ms-2"><?php echo htmlspecialchars($user_name); ?></span>
            </div>
        </div>

    </div>
</div>

<!-- ================= NAV BAR ================= -->
<div class="nav-bar">
    <div class="container-fluid d-flex justify-content-between align-items-center">

        <ul class="nav-menu d-flex gap-4 mb-0">
            <li><a href="doanhthu.php" class="nav-menu-link">Tổng quan</a></li>
            <li><a href="quanly_sanpham.php" class="nav-menu-link">Sản phẩm</a></li>
            <li><a href="donhang.php" class="nav-menu-link active">Đơn hàng</a></li>
            <li><a href="nhanvien.php" class="nav-menu-link">Nhân viên</a></li>
        </ul>

        <a class="btn-sell" href="../View/sell.php">
            <i class="bi bi-cart-plus me-2"></i>Bán hàng
        </a>
    </div>
</div>

<!-- ================= MAIN CONTENT ================= -->
<div class="main-content">
    <div class="container-fluid">
        <div class="row">

            <!-- ===== SIDEBAR FILTER ===== -->
            <div class="col-md-3 sidebar-filter">
                <form method="get">

                    <!-- Thời gian -->
                    <div class="filter-section">
                        <h6 class="filter-title">Thời gian</h6>

                        <div class="form-check">
                            <input class="form-check-input" type="radio"
                                   name="time_mode" value="month"
                                   <?php echo ($time_mode === 'month') ? 'checked' : ''; ?>>
                            <label class="form-check-label">Tháng này</label>
                        </div>

                        <div class="form-check">
                            <input class="form-check-input" type="radio"
                                   name="time_mode" value="custom"
                                   <?php echo ($time_mode === 'custom') ? 'checked' : ''; ?>>
                            <label class="form-check-label">Tùy chỉnh</label>
                        </div>

                        <div class="mt-2">
                            <label class="form-label small mb-1">Từ ngày</label>
                            <input type="date" class="form-control form-control-sm"
                                   name="from" value="<?php echo htmlspecialchars($from); ?>">
                        </div>

                        <div class="mt-2">
                            <label class="form-label small mb-1">Đến ngày</label>
                            <input type="date" class="form-control form-control-sm"
                                   name="to" value="<?php echo htmlspecialchars($to); ?>">
                        </div>
                    </div>

                    <!-- Nhân viên -->
                    <div class="filter-section">
                        <h6 class="filter-title">Nhân viên</h6>
                        <select class="form-select form-select-sm" name="employee_id">
                            <option value="0">Tất cả nhân viên</option>
                            <?php foreach ($employees as $e) { ?>
                                <option value="<?php echo intval($e['id']); ?>"
                                    <?php echo ($employee_id === intval($e['id'])) ? 'selected' : ''; ?>>
                                    <?php
                                    echo htmlspecialchars(
                                        $e['fullname'] .
                                        (!empty($e['employee_code']) ? " ({$e['employee_code']})" : '')
                                    );
                                    ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>

                    <!-- Buttons -->
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary w-100">Lọc</button>
                        <a href="donhang.php" class="btn btn-outline-secondary w-100">Reset</a>
                    </div>

                </form>
            </div>

            <!-- ===== CONTENT AREA ===== -->
            <div class="col-md-9">
                <div class="content-wrapper">

                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h1 class="page-title">Hóa đơn</h1>
                    </div>

                    <div class="table-wrapper">
                        <table class="table invoice-table align-middle">

                            <thead>
                                <tr>
                                    <th>Mã đơn</th>
                                    <th>Thời gian</th>
                                    <th>Nhân viên</th>
                                    <th class="text-end">Số lượng</th>
                                    <th class="text-end">Tổng tiền</th>
                                    <th class="text-end">Giảm giá</th>
                                    <th class="text-end">Khách cần trả</th>
                                    <th>Thanh toán</th>
                                </tr>
                            </thead>

                            <tbody>
                                <!-- Tổng cộng -->
                                <tr class="summary-row">
                                    <td colspan="3">Tổng cộng</td>
                                    <td class="text-end"><?php echo intval($sum_qty); ?></td>
                                    <td class="text-end total-summary"><?php echo vnd($sum_total); ?></td>
                                    <td class="text-end"><?php echo vnd($sum_discount); ?></td>
                                    <td class="text-end total-summary"><?php echo vnd($sum_final); ?></td>
                                    <td></td>
                                </tr>

                                <!-- Danh sách đơn -->
                                <?php foreach ($rows as $o) { ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($o['order_code']); ?></td>
                                        <td><?php echo htmlspecialchars($o['created_at']); ?></td>
                                        <td><?php echo htmlspecialchars($o['employee_name'] ?? ''); ?></td>
                                        <td class="text-end"><?php echo intval($o['total_quantity']); ?></td>
                                        <td class="text-end"><?php echo vnd($o['total_amount']); ?></td>
                                        <td class="text-end"><?php echo vnd($o['discount']); ?></td>
                                        <td class="text-end"><?php echo vnd($o['final_amount']); ?></td>
                                        <td><?php echo htmlspecialchars($o['payment_method'] ?? ''); ?></td>
                                    </tr>
                                <?php } ?>
                            </tbody>

                        </table>
                    </div>

                </div>
            </div>

        </div>
    </div>
</div>

<!-- ================= SCRIPT ================= -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>

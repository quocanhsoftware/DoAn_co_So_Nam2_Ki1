<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tổng quan - KiotViet</title>

    <!-- ================= CSS ================= -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="../nhanvien.css">
    <link rel="stylesheet" href="../doanhthu.css">
</head>

<body>
<?php
session_start();

/* ================= KIỂM TRA ĐĂNG NHẬP ================= */
if (!isset($_SESSION['fullname'])) {
    header("Location: ../View/login.php");
    exit;
}

$user_name = $_SESSION['fullname'] ?? 'User';
?>

<!-- ================= TOP HEADER ================= -->
<div class="top-header">
    <div class="container-fluid d-flex justify-content-between align-items-center">

        <!-- Logo -->
        <div class="logo-brand d-flex align-items-center">
            <div class="brand-dot-small"></div>
            <span class="brand-text">KiotViet</span>
        </div>

        <!-- User actions -->
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
            <li><a href="doanhthu.php" class="nav-menu-link active">Tổng quan</a></li>
            <li><a href="quanly_sanpham.php" class="nav-menu-link">Sản phẩm</a></li>
            <li><a href="donhang.php" class="nav-menu-link">Đơn hàng</a></li>
            <li><a href="nhanvien.php" class="nav-menu-link">Nhân viên</a></li>
        </ul>

        <a class="btn-sell" href="../View/sell.php">
            <i class="bi bi-cart-plus me-2"></i>Bán hàng
        </a>
    </div>
</div>

<!-- ================= MAIN CONTENT ================= -->
<div class="main-content py-4">
    <div class="container-fluid">

        <!-- ===== Filter bar ===== -->
        <div class="filter-bar d-flex align-items-center">
            <div class="d-flex align-items-center gap-2">
                <i class="bi bi-calendar3 text-muted"></i>
                <select class="form-select form-select-sm" style="width:200px" id="timeFilter">
                    <option value="today">Hôm nay</option>
                    <option value="yesterday">Hôm qua</option>
                    <option value="7days">7 ngày qua</option>
                    <option value="this_month" selected>Tháng này</option>
                    <option value="last_month">Tháng trước</option>
                </select>
            </div>

            <button class="btn btn-primary btn-sm ms-auto" onclick="loadRevenueData()">
                <i class="bi bi-arrow-clockwise me-1"></i> Cập nhật
            </button>
        </div>

        <!-- ===== Summary cards ===== -->
        <div class="row g-4 mb-4">

            <div class="col-md-3">
                <div class="summary-card">
                    <div class="summary-icon bg-light-blue">
                        <i class="bi bi-currency-dollar"></i>
                    </div>
                    <div class="summary-title">Doanh thu thuần</div>
                    <div class="summary-value" id="summaryRevenue">0</div>
                    <div class="summary-trend text-success">
                        <i class="bi bi-arrow-up-short"></i> --
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="summary-card">
                    <div class="summary-icon bg-light-green">
                        <i class="bi bi-receipt"></i>
                    </div>
                    <div class="summary-title">Số lượng đơn hàng</div>
                    <div class="summary-value" id="summaryOrders">0</div>
                    <div class="summary-trend text-success">
                        <i class="bi bi-arrow-up-short"></i> --
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="summary-card">
                    <div class="summary-icon bg-light-orange">
                        <i class="bi bi-box-seam"></i>
                    </div>
                    <div class="summary-title">Sản phẩm bán ra</div>
                    <div class="summary-value" id="summaryProducts">0</div>
                    <div class="summary-trend text-danger">
                        <i class="bi bi-arrow-down-short"></i> --
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="summary-card">
                    <div class="summary-icon bg-light-red">
                        <i class="bi bi-arrow-return-left"></i>
                    </div>
                    <div class="summary-title">Trả hàng</div>
                    <div class="summary-value">0</div>
                    <div class="summary-trend text-muted">Chưa hỗ trợ</div>
                </div>
            </div>

        </div>

        <!-- ===== Chart ===== -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="chart-container">
                    <h5 class="mb-4">Biểu đồ doanh thu theo thời gian</h5>
                    <canvas id="revenueChart"></canvas>
                </div>
            </div>
        </div>

        <!-- ===== Table ===== -->
        <div class="table-container">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="mb-0">Chi tiết doanh thu theo ngày</h5>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Thời gian</th>
                            <th>Số đơn hàng</th>
                            <th>Doanh thu</th>
                            <th>Giảm giá</th>
                            <th>Doanh thu thuần</th>
                        </tr>
                    </thead>
                    <tbody id="revenueTableBody"></tbody>
                </table>
            </div>
        </div>

    </div>
</div>

<!-- ================= SCRIPT ================= -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="../Controller_js/doanhthu.js"></script>

</body>
</html>

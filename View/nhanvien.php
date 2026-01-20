<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nhân viên - KiotViet</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="../nhanvien.css">
</head>
<body>

<?php 
session_start();

// Kiểm tra đăng nhập
if (!isset($_SESSION['fullname'])) {
    header("Location: ../View/login.php");
    exit;
}

$user_name = $_SESSION['fullname'] ?? 'User';
?>

<!-- ================= TOP HEADER ================= -->
<div class="top-header">
    <div class="container-fluid d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center">
            <div class="logo-brand d-flex align-items-center me-4">
                <div class="brand-dot-small"></div>
                <span class="brand-text">KiotViet</span>
            </div>
        </div>

        <div class="d-flex align-items-center gap-3">
            <div class="dropdown">
                <button class="icon-btn dropdown-toggle" type="button" data-bs-toggle="dropdown" title="Cài đặt">
                    <i class="bi bi-gear"></i>
                </button>
                <ul class="dropdown-menu">
                    <li>
                        <a class="dropdown-item" href="../Controller/logout.php">Đăng xuất</a>
                    </li>
                </ul>
            </div>

            <div class="user-profile">
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
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center">
            <ul class="nav-menu d-flex gap-4 mb-0">
                <li><a href="doanhthu.php" class="nav-menu-link">Tổng quan</a></li>
                <li><a href="quanly_sanpham.php" class="nav-menu-link">Sản phẩm</a></li>
                <li><a href="donhang.php" class="nav-menu-link">Đơn hàng</a></li>
                <li><a href="nhanvien.php" class="nav-menu-link active">Nhân viên</a></li>
            </ul>

            <a class="btn-sell" href="../View/sell.php">
                <i class="bi bi-cart-plus me-2"></i>
                Bán hàng
            </a>
        </div>
    </div>
</div>

<!-- ================= MAIN CONTENT ================= -->
<div class="main-content">
    <div class="container-fluid">
        <div class="row">

            <!-- ===== SIDEBAR FILTER ===== -->
            <div class="col-md-3 sidebar-filter">

                <div class="filter-section">
                    <h6 class="filter-title">Trạng thái nhân viên</h6>

                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="employeeStatus" id="statusWorking" value="dang_lam_viec" checked>
                        <label class="form-check-label">Đang làm việc</label>
                    </div>

                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="employeeStatus" id="statusRetired" value="da_nghi">
                        <label class="form-check-label">Đã nghỉ</label>
                    </div>
                </div>

                <div class="filter-section">
                    <h6 class="filter-title">Phòng ban</h6>
                    <select class="form-select" id="departmentSelect">
                        <option value="">Chọn phòng ban</option>
                        <option value="1">Kinh doanh</option>
                        <option value="2">Kho</option>
                        <option value="3">Kế toán</option>
                    </select>
                </div>

                <div class="filter-section">
                    <h6 class="filter-title">Chức danh</h6>
                    <select class="form-select" id="positionSelect">
                        <option value="">Chọn chức danh</option>
                        <option value="1">Thu ngân</option>
                        <option value="2">Nhân viên</option>
                    </select>
                </div>
            </div>

            <!-- ===== CONTENT AREA ===== -->
            <div class="col-md-9">
                <div class="content-wrapper">

                    <!-- ACTION BAR -->
                    <div class="action-bar mb-3">
                        <div class="row g-3 align-items-center">

                            <div class="col-lg-6">
                                <div class="search-box-wrapper w-100">
                                    <i class="bi bi-search search-icon"></i>
                                    <input type="text" class="form-control search-input" id="searchEmployee" placeholder="Tìm theo mã, tên nhân viên">
                                </div>
                            </div>

                            <div class="col-lg-6 d-flex justify-content-lg-end flex-wrap gap-2">
                                <div class="d-flex align-items-center gap-2">
                                    <button class="btn-primary-action" data-bs-toggle="modal" data-bs-target="#addEmployeeModal">
                                        <i class="bi bi-plus-lg me-1"></i>
                                        Nhân viên
                                    </button>

                                    <button class="btn btn-outline-danger btn-sm" id="deleteSelectedBtn" title="Chuyển trạng thái nghỉ">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>

                                <button class="btn-secondary-action">
                                    <i class="bi bi-upload me-1"></i>
                                    Nhập file
                                </button>

                                <div class="dropdown">
                                    <button class="btn-secondary-action dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                        <i class="bi bi-download me-1"></i>
                                        Xuất file
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item" href="#">Xuất Excel</a></li>
                                        <li><a class="dropdown-item" href="#">Xuất CSV</a></li>
                                    </ul>
                                </div>

                                <button class="btn-icon" title="Chế độ xem">
                                    <i class="bi bi-list-ul"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- EMPLOYEE TABLE -->
                    <div class="table-wrapper">
                        <table class="table employee-table align-middle">
                            <thead>
                                <tr>
                                    <th><input type="checkbox" class="form-check-input" id="selectAll"></th>
                                    <th>Ảnh</th>
                                    <th>Mã nhân viên</th>
                                    <th>Tên nhân viên</th>
                                    <th>Số điện thoại</th>
                                    <th>Mật khẩu</th>
                                    <th>Số CMND/CCCD</th>
                                    <th class="text-center">Thao tác</th>
                                </tr>
                            </thead>
                            <tbody id="employeeTableBody"></tbody>
                        </table>
                    </div>

                </div>
            </div>

        </div>
    </div>
</div>

<!-- ================= ADD EMPLOYEE MODAL ================= -->
<div class="modal fade" id="addEmployeeModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Thêm nhân viên</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <form id="addEmployeeForm" method="post" action="Controller/add_employee.php">
                <input type="hidden" id="employeeIdInput" name="employee_id">

                <div class="modal-body">
                    <div class="row g-3">

                        <div class="col-md-6">
                            <label class="form-label">Mã nhân viên</label>
                            <input type="text" class="form-control" id="employeeCode" name="employee_code" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Họ và tên</label>
                            <input type="text" class="form-control" id="fullname" name="fullname" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Số điện thoại</label>
                            <input type="tel" class="form-control" id="phone" name="phone">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Mật khẩu</label>
                            <input type="text" class="form-control" id="password" name="password">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Số CMND/CCCD</label>
                            <input type="text" class="form-control" id="cccd" name="cccd">
                        </div>

                        <div class="col-12">
                            <label class="form-label">Ảnh nhân viên</label>
                            <div class="photo-upload-wrapper">
                                <div class="photo-upload-preview">
                                    <img src="../picture/default_user.jpg" id="employeePhotoPreview">
                                </div>
                                <input type="file" class="form-control" id="employeePhoto" name="photo" accept="image/*">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Phòng ban</label>
                            <select class="form-select" id="department" name="department">
                                <option value="">Chọn phòng ban</option>
                                <option value="1">Kinh doanh</option>
                                <option value="2">Kho</option>
                                <option value="3">Kế toán</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Chức danh</label>
                            <select class="form-select" id="position" name="position">
                                <option value="">Chọn chức danh</option>
                                <option value="1">Thu ngân</option>
                                <option value="2">Nhân viên</option>
                            </select>
                        </div>

                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary">Lưu nhân viên</button>
                </div>
            </form>

        </div>
    </div>
</div>

<!-- ================= SUPPORT ================= -->
<div class="support-button">
    <button class="btn-support">
        <i class="bi bi-telephone-fill me-2"></i>
        1900 6522
    </button>
</div>

<!-- JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="../Controller_js/nhanvien.js"></script>

</body>
</html>

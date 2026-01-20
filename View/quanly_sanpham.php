<?php include '../Controller/products.php'; ?>
<!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Quản lý sản phẩm - KiotViet</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link href="../nhanvien.css" rel="stylesheet">
</head>
<body>

<?php
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
<nav>
    <div class="nav-bar">
        <div class="container-fluid">
            <div class="d-flex justify-content-between align-items-center">

                <ul class="nav-menu d-flex gap-4 mb-0">
                    <li><a href="doanhthu.php" class="nav-menu-link">Tổng quan</a></li>
                    <li><a href="quanly_sanpham.php" class="nav-menu-link active">Sản phẩm</a></li>
                    <li><a href="donhang.php" class="nav-menu-link">Đơn hàng</a></li>
                    <li><a href="nhanvien.php" class="nav-menu-link">Nhân viên</a></li>
                </ul>

                <a class="btn-sell" href="../View/sell.php">
                    <i class="bi bi-cart-plus me-2"></i>
                    Bán hàng
                </a>

            </div>
        </div>
    </div>
</nav>

<!-- ================= MAIN CONTENT ================= -->
<div class="container py-4">

    <!-- HEADER + SEARCH -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="mb-0">Quản lý sản phẩm</h3>

        <form class="d-flex" method="get" style="max-width: 400px; height: 40px;">
            <input
                type="text"
                class="form-control me-2"
                name="q"
                placeholder="Tìm theo tên hoặc mã"
                value="<?php echo htmlspecialchars($q); ?>"
            >
            <button style="width: 150px;" class="btn btn-outline-primary" type="submit">
                Tìm kiếm
            </button>
        </form>
    </div>

    <!-- MESSAGE -->
    <?php if ($msg !== '') { ?>
        <div class="alert alert-info">
            <?php echo htmlspecialchars($msg); ?>
        </div>
    <?php } ?>

    <div class="row g-4">

        <!-- ===== ADD / EDIT PRODUCT ===== -->
        <div class="col-lg-4">
            <div class="card">

                <div class="card-header fw-semibold">
                    <?php echo $editing ? "Sửa sản phẩm" : "Thêm sản phẩm"; ?>
                </div>

                <div class="card-body">
                    <form method="post">

                        <input type="hidden" name="action" value="<?php echo $editing ? 'update' : 'create'; ?>">

                        <?php if ($editing) { ?>
                            <input type="hidden" name="id"
                                   value="<?php echo intval($editing['id']); ?>">
                        <?php } ?>

                        <div class="mb-2">
                            <label class="form-label">Tên sản phẩm</label>
                            <input
                                name="name"
                                class="form-control"
                                value="<?php echo $editing ? htmlspecialchars($editing['name']) : ''; ?>"
                                required
                            >
                        </div>

                        <div class="mb-2">
                            <label class="form-label">Ảnh (URL)</label>
                            <input
                                name="image"
                                class="form-control"
                                value="<?php echo $editing ? htmlspecialchars($editing['image']) : ''; ?>"
                            >
                        </div>

                        <div class="mb-2">
                            <label class="form-label">Giá vốn</label>
                            <input
                                name="cost_price"
                                type="number"
                                step="0.01"
                                class="form-control"
                                value="<?php echo $editing ? htmlspecialchars($editing['cost_price']) : ''; ?>"
                                required
                            >
                        </div>

                        <div class="mb-2">
                            <label class="form-label">Giá bán</label>
                            <input
                                name="sale_price"
                                type="number"
                                step="0.01"
                                class="form-control"
                                value="<?php echo $editing ? htmlspecialchars($editing['sale_price']) : ''; ?>"
                                required
                            >
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Tồn kho</label>
                            <input
                                name="stock"
                                type="number"
                                class="form-control"
                                value="<?php echo $editing ? htmlspecialchars($editing['stock']) : ''; ?>"
                                required
                            >
                        </div>

                        <button
                            class="btn btn-<?php echo $editing ? 'success' : 'primary'; ?> w-100"
                            type="submit"
                        >
                            <?php echo $editing ? 'Hoàn thành' : 'Thêm'; ?>
                        </button>

                    </form>
                </div>
            </div>
        </div>

        <!-- ===== PRODUCT LIST ===== -->
        <div class="col-lg-8">
            <div class="card">

                <div class="card-header fw-semibold">
                    Danh sách sản phẩm
                </div>

                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Ảnh</th>
                                <th>Mã</th>
                                <th>Tên</th>
                                <th class="text-end">Giá vốn</th>
                                <th class="text-end">Giá bán</th>
                                <th class="text-end">Tồn</th>
                                <th>Cập nhật</th>
                                <th>Hành động</th>
                            </tr>
                        </thead>
                        <tbody>

                        <?php foreach ($products as $p) { ?>
                            <tr>
                                <td><?php echo intval($p['id']); ?></td>

                                <td>
                                    <?php if (!empty($p['image'])) { ?>
                                        <img
                                            src="<?php echo htmlspecialchars($p['image']); ?>"
                                            alt="img"
                                            style="width:48px;height:48px;object-fit:cover;border-radius:6px;"
                                        >
                                    <?php } ?>
                                </td>

                                <td><?php echo htmlspecialchars($p['product_code']); ?></td>
                                <td><?php echo htmlspecialchars($p['name']); ?></td>
                                <td class="text-end"><?php echo currency_vnd($p['cost_price']); ?></td>
                                <td class="text-end"><?php echo currency_vnd($p['sale_price']); ?></td>
                                <td class="text-end"><?php echo intval($p['stock']); ?></td>
                                <td><?php echo htmlspecialchars($p['updated_at']); ?></td>

                                <td class="d-flex gap-2">
                                    <a
                                        class="btn btn-sm btn-outline-secondary"
                                        href="?<?php echo http_build_query([
                                            'q' => $q,
                                            'edit_id' => intval($p['id'])
                                        ]); ?>"
                                    >
                                        Sửa
                                    </a>

                                    <form method="post">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="id" value="<?php echo intval($p['id']); ?>">
                                        <button class="btn btn-sm btn-outline-danger" type="submit">
                                            Xóa
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php } ?>

                        </tbody>
                    </table>
                </div>

            </div>
        </div>

    </div>
</div>

<!-- JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

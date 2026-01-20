<?php
// index.php — Landing page tĩnh kiểu KiotViet (1 file)
?><!doctype html>
<html lang="vi">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Phần mềm quản lý bán hàng</title>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
	<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="style.css" rel="stylesheet">

</head>
<body>
	<nav class="navbar navbar-expand-lg sticky-top">
		<div class="container">
			<a class="navbar-brand" href="#">
				<span class="brand-dot"></span>
				KiotViet
			</a>
			<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#nv">
				<span class="navbar-toggler-icon"></span>
			</button>
			<div id="nv" class="collapse navbar-collapse">
				<ul class="navbar-nav me-auto mb-2 mb-lg-0">
					<li class="nav-item"><a class="nav-link" href="#">Sản phẩm</a></li>
					<li class="nav-item"><a class="nav-link" href="#">Giải pháp</a></li>
					<li class="nav-item"><a class="nav-link" href="#">Khách hàng</a></li>
					<li class="nav-item"><a class="nav-link" href="#">Phí dịch vụ</a></li>
					<li class="nav-item"><a class="nav-link" href="#">Hỗ trợ</a></li>
					<li class="nav-item"><a class="nav-link" href="#">Tin tức</a></li>
					<li class="nav-item"><a class="nav-link" href="#">Về chúng tôi</a></li>
				</ul>
			<div class="d-flex gap-2">
				<a class="btn btn-outline-primary" href="#" data-bs-toggle="modal" data-bs-target="#loginModal">Đăng nhập</a>
				<a class="btn btn-primary" href="View/dangky.php">Đăng ký</a>
			</div>
			</div>
		</div>
	</nav>

	<section class="hero">
		<div class="container">
			<div class="row align-items-center">
				<div class="col-lg-6">
					<h1 class="display-title mb-3">Phần mềm<br> quản lý bán hàng<br> phổ biến nhất</h1>
					<p class="lead-lg mb-4">Giúp bạn quản lý bán hàng, kho, hóa đơn điện tử, thanh toán và tăng trưởng doanh thu.</p>
					<div class="cta d-flex flex-wrap gap-3 mb-4">
						<a class="btn btn-primary" href="#">Dùng thử miễn phí</a>
						<div class="col-md-6">	
					<a class="btn btn-outline-primary float-card d-flex align-items-center gap-2" href="#">
							<span>Khám phá</span>
							<span class="bi bi-play-fill"></span>
						</a>
					</div>
					</div>
					
					<div class="d-flex stats flex-wrap mb-4">
						<div class="stat-item">
							<div class="num">300.000+</div>
							<div class="text-muted">nhà kinh doanh sử dụng</div>
						</div>
						<div class="stat-item">
							<div class="num">10.000+</div>
							<div class="text-muted">nhà kinh doanh mới mỗi tháng</div>
						</div>
					</div>

				</div>

				<div class="col-lg-6">
					<div class="hero-visual">
					<span class="ring">
						<img src="picture/black.jpg" alt="Chủ cửa hàng tươi cười">
					</span>

						<!-- Thẻ nổi 1 -->
						<div class="float-card fc-1">
							
						<span class="brand-dot"></span>
							<div>
								<div class="fw-bold">Phần mềm quản lý</div>
								<div class="text-muted small">bán hàng</div>
							</div>
						</div>

						<!-- Thẻ nổi 2 -->
						<div class="float-card fc-2">
							<div class="fc-pill" style="color:#16a34a;background:#eafff3;">
								<i class="bi bi-receipt"></i>
							</div>
							<div>
								<div class="fw-bold">Miễn phí Hóa đơn điện tử</div>
								<div class="text-muted small">và Chữ ký số</div>
							</div>
						</div>

						<!-- Thẻ nổi 3 -->
						<div class="float-card fc-3">
							<div class="fc-pill" style="color:#0ea5e9;background:#eef9ff;">
								<i class="bi bi-credit-card-2-front"></i>
							</div>
							<div>
								<div class="fw-bold">Giải pháp thanh toán</div>
								<div class="text-muted small">và vay vốn</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>

	<footer class="footer py-5">
		<div class="container d-flex flex-wrap justify-content-between align-items-center gap-3">
			<div>© <?php echo date('Y'); ?> KiotViet — Đồ Án Cơ Sở Minh Họa.</div>
			<div class="d-flex gap-3">
				<a class="text-decoration-none footer" href="#">Điều khoản</a>
				<a class="text-decoration-none footer" href="#">Chính sách</a>
				<a class="text-decoration-none footer" href="#">Liên hệ</a>
			</div>
		</div>
	</footer>

	<!-- Modal Đăng nhập -->
	<div class="modal fade" id="loginModal" tabindex="-1" aria-labelledby="loginModalLabel" aria-hidden="true">
		<div class="modal-dialog modal-dialog-centered">
			<div class="modal-content">
				<div class="modal-header pb-0">
					<h5 class="modal-title fw-bold" id="loginModalLabel">Đăng nhập tài khoản KiotViet</h5>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<div class="modal-body pt-3">
				<form id="loginForm" action="Controller/login_shop.php" method="POST">
						<div class="mb-4">
							<label for="storeName" class="form-label fw-semibold mb-2">Tên cửa hàng của bạn</label>
							<div class="input-group">
								<input type="text" class="form-control" name="storeName" id="storeName" placeholder="Nhập tên cửa hàng" required>
								<span class="input-group-text bg-light">.kiotviet.vn</span>
							</div>
						</div>

						<button type="submit" class="btn btn-primary w-100 py-2 fw-semibold">Vào cửa hàng</button>
					</form>
					
				</div>
			</div>
		</div>
	</div>

	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
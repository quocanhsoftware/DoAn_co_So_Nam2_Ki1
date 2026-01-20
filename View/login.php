<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KiotViet - Đăng nhập</title>

    <!-- CSS -->
    <link rel="stylesheet" href="../login.css">
</head>

<body class="login-page">

    <!-- ================= LOGIN CONTAINER ================= -->
    <div class="login-container">
        <div class="login-box">

            <!-- Logo -->
            <div class="logo">
                <img src="../picture/kiotvietlogo.svg" alt="KiotViet Logo">
            </div>

            <!-- ================= LOGIN FORM ================= -->
            <form action="../Controller/login_account.php" method="POST">

                <!-- Số điện thoại -->
                <div class="form-group">
                    <input 
                        type="text" 
                        name="phone" 
                        placeholder="Số điện thoại" 
                        required
                    >
                </div>

                <!-- Mật khẩu -->
                <div class="form-group password-group">
                    <input 
                        type="password" 
                        name="password" 
                        placeholder="Mật khẩu" 
                        required
                    >

                    <!-- Toggle password -->
                    <span class="toggle-password">
                        <img 
                            class="eye-open" 
                            src="../picture/eye-open.svg" 
                            alt="Hiện mật khẩu"
                        >
                        <img 
                            class="eye-close" 
                            src="../picture/eye-closed.png" 
                            alt="Ẩn mật khẩu" 
                            style="display:none;"
                        >
                    </span>
                </div>

                <!-- Shop ID -->
                <input 
                    type="hidden" 
                    name="shop_id" 
                    value="<?php echo intval($_GET['shop_id'] ?? 0); ?>"
                >

                <!-- Remember & Forgot -->
                <div class="form-check">
                    <div class="remember-me">
                        <input type="checkbox" name="remember" id="remember">
                        <label for="remember">Duy trì đăng nhập</label>
                    </div>

                    <a href="#" class="forgot-password">
                        Quên mật khẩu?
                    </a>
                </div>

                <!-- Action Buttons -->
                <div class="action-buttons">
                    <button 
                        type="submit" 
                        name="action" 
                        value="manage" 
                        class="btn btn-primary"
                    >
                        <i class="icon-manage"></i>
                        Quản lý
                    </button>

                    <button 
                        type="submit" 
                        name="action" 
                        value="sell" 
                        class="btn btn-success"
                    >
                        <i class="icon-sell"></i>
                        Bán hàng
                    </button>
                </div>

            </form>
            <!-- ================= END FORM ================= -->

        </div>
    </div>
    <!-- ================= END LOGIN ================= -->

    <!-- ================= FOOTER ================= -->
    <div class="footer">
        <div class="support">
            <span class="phone">
                <i class="icon-phone"></i>
                Hỗ trợ: 1900 6522
            </span>

            <span class="language">
                <img 
                    src="../picture/vietnamflag.png" 
                    alt="Tiếng Việt"
                >
                Tiếng Việt
            </span>
        </div>
    </div>

    <!-- JS -->
    <script src="../Controller_js/login.js"></script>

</body>
</html>

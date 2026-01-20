<?php
$user_id = intval($_GET['id'] ?? 0);
if ($user_id === 0) {
    die("Lỗi: Không xác định được user ID");
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tạo cửa hàng của bạn</title>

    <!-- ================= STYLE ================= -->
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: "Inter", "Segoe UI", Arial, sans-serif;
        }

        body {
            min-height: 100vh;
            background: radial-gradient(circle at top, #f4f8ff, #e4ebfb 45%, #dbe4fb);
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }

        /* Confetti */
        .confetti {
            position: absolute;
            width: 12px;
            height: 12px;
            border-radius: 2px;
            opacity: 0.9;
            animation: float 6s linear infinite;
            z-index: 0;
            pointer-events: none;
        }

        @keyframes float {
            0% {
                transform: translateY(0) rotate(0deg);
                opacity: 0;
            }
            10% { opacity: 0.9; }
            100% {
                transform: translateY(120vh) rotate(360deg);
                opacity: 0;
            }
        }

        /* Card */
        .card {
            position: relative;
            z-index: 1;
            width: min(420px, 90%);
            background: #fff;
            border-radius: 20px;
            padding: 48px 48px 56px;
            text-align: center;
            box-shadow: 0 30px 80px rgba(29, 78, 216, 0.25);
        }

        .card img {
            width: 110px;
            margin-bottom: 24px;
        }

        h1 {
            font-size: 28px;
            color: #0f172a;
            margin-bottom: 28px;
            font-weight: 700;
        }

        /* Form */
        .input-group {
            margin-bottom: 18px;
            text-align: left;
        }

        .input-group label {
            display: block;
            margin-bottom: 6px;
            font-size: 14px;
            color: #475569;
        }

        select,
        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 14px 16px;
            border: 1.5px solid #cbd5f5;
            border-radius: 12px;
            font-size: 16px;
            color: #0f172a;
            transition: border-color 0.2s, box-shadow 0.2s;
        }

        select:focus,
        input:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.2);
        }

        .toggle-wrapper {
            position: relative;
        }

        .toggle-wrapper button {
            position: absolute;
            right: 14px;
            top: 50%;
            transform: translateY(-50%);
            border: none;
            background: transparent;
            cursor: pointer;
            color: #94a3b8;
            font-size: 15px;
        }

        .submit-btn {
            margin-top: 10px;
            width: 100%;
            padding: 16px 0;
            border: none;
            border-radius: 999px;
            background: linear-gradient(120deg, #1d4ed8, #2563eb);
            color: #fff;
            font-size: 18px;
            font-weight: 600;
            cursor: pointer;
            box-shadow: 0 15px 30px rgba(37, 99, 235, 0.35);
            transition: transform 0.15s ease, box-shadow 0.15s ease;
        }

        .submit-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 20px 28px rgba(37, 99, 235, 0.45);
        }

        .submit-btn:active {
            transform: translateY(1px);
        }

        /* Popup */
        .popup {
            position: fixed;
            top: 20px;
            right: 20px;
            background: #333;
            color: #fff;
            padding: 12px 20px;
            border-radius: 8px;
            display: none;
            z-index: 9999;
        }

        .popup.show { display: block; }
        .popup.success { background: #4caf50; }
        .popup.error { background: #f44336; }

        @media (max-width: 480px) {
            .card {
                padding: 36px 28px 44px;
            }
            h1 { font-size: 24px; }
        }
    </style>
</head>

<body>

<!-- ================= CONFETTI ================= -->
<?php
$colors = ['#10b981', '#34d399', '#f97316', '#f43f5e', '#2563eb', '#facc15'];
for ($i = 0; $i < 18; $i++) {
    $delay = rand(0, 400) / 100;
    $left  = rand(5, 95);
    $size  = rand(8, 16);
    $color = $colors[array_rand($colors)];
    echo "<span class='confetti'
            style='left: {$left}%;
                   top: -{$size}px;
                   width: {$size}px;
                   height: {$size}px;
                   background: {$color};
                   animation-delay: {$delay}s;'>
          </span>";
}
?>

<!-- ================= CARD ================= -->
<div class="card">
    <img src="../picture/logo_create_shop.jpg" alt="Store badge">
    <h1>Tạo cửa hàng của bạn</h1>

    <form action="../Controller/save_shop.php" method="post">
        <input type="hidden" name="user_id" value="<?php echo $user_id; ?>">

        <div class="input-group">
            <label for="nameshop">Tên cửa hàng</label>
            <input type="text" id="nameshop" name="nameshop"
                   placeholder="Đặt tên cho cửa hàng của bạn" required>
        </div>

        <div class="input-group">
            <label for="password">Mật khẩu</label>
            <div class="toggle-wrapper">
                <input type="password" id="password" name="password"
                       placeholder="Nhập mật khẩu (tối thiểu 8 ký tự)"
                       minlength="8" required>
                <button type="button" onclick="togglePassword()">👁</button>
            </div>
        </div>

        <button type="submit" class="submit-btn">Tạo cửa hàng</button>
    </form>
</div>

<!-- ================= POPUP ================= -->
<div id="popup" class="popup"></div>

<!-- ================= SCRIPT ================= -->
<script src="../Controller_js/create_shop.js"></script>

<script>
function togglePassword() {
    const input = document.getElementById('password');
    input.type = input.type === 'password' ? 'text' : 'password';
}

function showPopup(message, type = "success", duration = 3000) {
    const popup = document.getElementById("popup");
    popup.textContent = message;
    popup.className = `popup show ${type}`;
    setTimeout(() => popup.className = "popup", duration);
}
</script>

</body>
</html>

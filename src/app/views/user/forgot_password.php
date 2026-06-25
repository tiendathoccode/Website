<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quên mật khẩu - Aurrelia</title>
    <link rel="stylesheet" type="text/css" href="/assets/css/auth.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="auth-body">
    <div class="auth-wrapper">

        <div class="auth-image">
            <div class="image-text">
                <h2>Khôi phục tài khoản</h2>
                <p>Đừng lo lắng, chúng tôi sẽ giúp bạn lấy lại quyền truy cập vào bộ sưu tập của mình.</p>
            </div>
        </div>

        <div class="auth-form-section">
            <h1 class="brand-logo">AURRELIA</h1>
            <h3 style="margin-bottom: 15px; font-family: 'Times New Roman', serif; text-align: center;">Quên mật khẩu?</h3>
            <p style="text-align: center; color: #666; margin-bottom: 30px; font-size: 14px; line-height: 1.5;">
                Vui lòng nhập địa chỉ email bạn đã đăng ký. Chúng tôi sẽ gửi một liên kết để bạn đặt lại mật khẩu.
            </p>

            <form id="forgotPasswordForm" method="POST" action="/index.php?page=process_forgot_password">
                <div class="input-group">
                    <label for="reset-email">ĐỊA CHỈ EMAIL</label>
                    <div class="input-wrapper">
                        <i class="fas fa-envelope icon"></i>
                        <input type="email" id="reset-email" name="email" placeholder="email_cua_ban@gmail.com" required>
                    </div>
                </div>

                <button type="submit" class="btn-primary" style="margin-top: 20px;">GỬI LIÊN KẾT ĐẶT LẠI</button>
            </form>

            <div style="text-align: center; margin-top: 30px;">
                <a href="/index.php?page=login" style="color: #bfa15f; text-decoration: none; font-weight: bold; font-size: 14px;">
                    <i class="fas fa-arrow-left" style="margin-right: 5px;"></i> Quay lại Đăng nhập
                </a>
            </div>

        </div>

    </div>

    <script src="/assets/js/auth.js"></script>
</body>
</html>

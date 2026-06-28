<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đổi mật khẩu - Aurrelia</title>
    <link rel="stylesheet" type="text/css" href="/assets/css/auth.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="auth-body">
    <div class="auth-wrapper">

        <div class="auth-image">
            <div class="image-text">
                <h2>Bảo mật tài khoản</h2>
                <p>Cập nhật mật khẩu mới để đảm bảo an toàn cho bộ sưu tập của bạn.</p>
            </div>
        </div>

        <div class="auth-form-section">
            <h1 class="brand-logo">AURRELIA</h1>
            <h3 style="margin-bottom: 15px; font-family: 'Times New Roman', serif; text-align: center;">Tạo mật khẩu mới</h3>
            <p style="text-align: center; color: #666; margin-bottom: 30px; font-size: 14px; line-height: 1.5;">
                Vui lòng nhập mật khẩu mới của bạn bên dưới. Hãy chắc chắn rằng hai ô nhập trùng khớp với nhau.
            </p>

            <?php if (isset($_SESSION["error_message"])): ?>
                <div style="color: #ff4d4d; background: #ffe6e6; padding: 10px; border-radius: 5px; text-align: center; margin-bottom: 20px; font-size: 14px;">
                    <i class="fas fa-exclamation-circle"></i> <?php
                    echo $_SESSION["error_message"];
                    unset($_SESSION["error_message"]);
                    ?>
                </div>
            <?php endif; ?>

            <form id="changePasswordForm" method="POST" action="/index.php?page=process_reset_password">

                <input type="hidden" name="token" value="<?php echo htmlspecialchars(
                    $token,
                ); ?>">

                <div class="input-group">
                    <label for="new-password">MẬT KHẨU MỚI</label>
                    <div class="input-wrapper">
                        <i class="fas fa-lock icon"></i>
                        <input type="password" id="new-password" name="password" placeholder="••••••••" required>
                    </div>
                </div>

                <div class="input-group" style="margin-top: 15px;">
                    <label for="confirm-new-password">XÁC NHẬN MẬT KHẨU MỚI</label>
                    <div class="input-wrapper">
                        <i class="fas fa-lock icon"></i>
                        <input type="password" id="confirm-new-password" name="confirm_password" placeholder="••••••••" required>
                    </div>
                </div>

                <button type="submit" class="btn-primary" style="margin-top: 25px;">LƯU MẬT KHẨU</button>
            </form>

            <div style="text-align: center; margin-top: 30px;">
                <a href="/index.php?page=login" style="color: #bfa15f; text-decoration: none; font-weight: bold; font-size: 14px;">
                    Hủy và quay lại Đăng nhập
                </a>
            </div>

        </div>

    </div>

    <script src="/assets/js/auth.js"></script>
</body>
</html>

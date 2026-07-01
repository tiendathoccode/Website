<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Đăng ký tài khoản - Aurrelia</title>
        <link rel="stylesheet" type="text/css" href="/assets/css/auth.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
        <link rel="icon" type="image/png" href="/favicon.png">
    </head>
    <body class="auth-body">
        <div class="auth-wrapper">

            <div class="auth-image">
                <div class="image-text">
                    <h2>Khám phá vẻ đẹp thanh lịch vượt thời gian</h2>
                    <p>Hãy cùng Aurrelia tạo nên bộ sưu tập trang sức thủ công tinh xảo, có giá trị đầu tư dành riêng cho bạn.</p>
                </div>
            </div>

            <div class="auth-form-section">
                <h1 class="brand-logo">AURRELIA</h1>

                <div class="auth-tabs">
                    <div class="tab" onclick="window.location.href='/index.php?page=login'">Đăng nhập</div>
                    <div class="tab active">Tạo tài khoản</div>
                </div>

                <form id="registerForm" method="POST" action="/index.php?page=process_register">
                    <div class="input-group">
                        <label for="reg-fullname">HỌ VÀ TÊN</label>
                        <div class="input-wrapper">
                            <i class="fas fa-user icon"></i>
                            <input type="text" id="reg-fullname" name="full_name" placeholder="Nhập họ và tên" required>
                        </div>
                    </div>

                    <div class="input-group">
                        <label for="reg-email">ĐỊA CHỈ EMAIL</label>
                        <div class="input-wrapper">
                            <i class="fas fa-envelope icon"></i>
                            <input type="email" id="reg-email" name="email" placeholder="email@cua-ban.com" required>
                        </div>
                    </div>

                    <div class="input-group">
                        <label for="reg-phone">SỐ ĐIỆN THOẠI</label>
                        <div class="input-wrapper">
                            <i class="fas fa-phone icon"></i>
                            <input type="tel" id="reg-phone" name="phone" placeholder="09xxxxxx" required>
                        </div>
                    </div>

                    <div class="input-group">
                        <label for="reg-password">MẬT KHẨU</label>
                        <div class="input-wrapper">
                            <i class="fas fa-lock icon"></i>
                            <input type="password" id="reg-password" name="password" placeholder="••••••••" required>
                        </div>
                    </div>

                    <div class="input-group">
                        <label for="reg-confirm-password">XÁC NHẬN MẬT KHẨU</label>
                        <div class="input-wrapper">
                            <i class="fas fa-lock icon"></i>
                            <input type="password" id="reg-confirm-password" name="confirm_password" placeholder="••••••••" required>
                            <i class="fas fa-eye-slash icon-right toggle-password" style="cursor: pointer;"></i>
                        </div>
                    </div>

                    <div style="font-size: 13px; color: #666; display: flex; align-items: center; gap: 8px; margin-top: 15px; margin-bottom: 20px;">
                        <input type="checkbox" id="terms" name="terms" required>
                        <label for="terms" style="margin: 0;">Tôi đồng ý với các Điều khoản và Điều kiện.</label>
                    </div>

                    <div class="alert alert-danger d-none text-center mb-3" style="font-size: 13px; color: red;" id="register-error"></div>

                    <button type="submit" class="btn-primary">ĐĂNG KÝ TÀI KHOẢN</button>
                </form>

            </div>

        </div>

        <script src="/assets/js/auth.js"></script>
    </body>
</html>

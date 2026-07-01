<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Đăng nhập</title>
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
                    <div class="tab active">Đăng nhập</div>
                    <div class="tab" onclick="window.location.href='/index.php?page=register'">Tạo tài khoản</div>
                </div>

                <!-- Chỉ 1 form duy nhất -->
                <form id="loginForm" method="POST" action="/index.php?page=process_login">

                    <?php if (isset($_SESSION["error_message"])): ?>
                        <div style="background:#fde8e8; color:#c0392b; border:1px solid #f5c6c6; border-radius:6px; padding:10px 14px; margin-bottom:14px; font-size:13px; text-align:center;">
                            <i class="fas fa-exclamation-circle"></i>
                            <?php
                            echo $_SESSION["error_message"];
                            unset($_SESSION["error_message"]);
                            ?>
                        </div>
                    <?php endif; ?>

                    <?php if (isset($_SESSION["success_message"])): ?>
                        <div style="background:#e8f8e8; color:#1e7e34; border:1px solid #c3e6cb; border-radius:6px; padding:10px 14px; margin-bottom:14px; font-size:13px; text-align:center;">
                            <i class="fas fa-check-circle"></i>
                            <?php
                            echo $_SESSION["success_message"];
                            unset($_SESSION["success_message"]);
                            ?>
                        </div>
                    <?php endif; ?>

                    <div class="input-group">
                        <label for="email">ĐỊA CHỈ EMAIL</label>
                        <div class="input-wrapper">
                            <i class="fas fa-envelope icon"></i>
                            <!-- id="email" để khớp với JS -->
                            <input type="email" id="email" name="email" placeholder="email@cua-ban.com" required>
                        </div>
                    </div>

                    <div class="input-group">
                        <label for="password">MẬT KHẨU</label>
                        <div class="input-wrapper">
                            <i class="fas fa-lock icon"></i>
                            <!-- id="password" để khớp với JS -->
                            <input type="password" id="password" name="password" placeholder="••••••••" required>
                            <!-- Con mắt toggle, data-target trỏ đúng id -->
                            <i class="fas fa-eye toggle-password" data-target="password" style="cursor:pointer; position:absolute; right:12px; top:50%; transform:translateY(-50%);"></i>
                        </div>
                    </div>

                    <div style="text-align: right; margin-bottom: 20px;">
                        <a href="/index.php?page=forgot_password" style="color: #bfa15f; text-decoration: none; font-size: 13px; font-weight: bold;">Quên mật khẩu?</a>
                    </div>

                    <button type="submit" class="btn-primary">ĐĂNG NHẬP</button>
                </form>

                <div class="divider">
                    <span>OR CONTINUE WITH</span>
                </div>
                <div class="social-login">
                    <button class="btn-social"><i class="fab fa-google"></i> Google</button>
                    <button class="btn-social"><i class="fab fa-apple"></i> Apple</button>
                </div>
            </div>
        </div>
        <!-- Sửa lại: load JS không phải CSS -->
        <script src="/assets/js/auth.js"></script>
    </body>
</html>

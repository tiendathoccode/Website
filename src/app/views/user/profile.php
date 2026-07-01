<!DOCTYPE html>
<html lang="vi">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Thông tin cá nhân - Aurelia</title>
        <link rel="stylesheet" type="text/css" href="/assets/css/auth.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    </head>
    <body class="auth-body">
        <div class="auth-wrapper" style="max-width: 900px;">

            <div class="auth-image">
                <div class="image-text">
                    <h2>Thông tin cá nhân</h2>
                    <p>Quản lý và cập nhật thông tin liên hệ, địa chỉ giao hàng mặc định của bạn tại Aurrelia Boutique.</p>
                </div>
            </div>

            <div class="auth-form-section" style="max-height: 90vh; overflow-y: auto;">
                <h1 class="brand-logo">AURRELIA</h1>
                <h3 style="margin-bottom: 30px; font-family: 'Times New Roman', serif; text-align: center;">Hồ sơ cá nhân</h3>

                <?php
                if (isset($_SESSION["success_message"])) {
                    echo '<p style="color: green; text-align: center; margin-bottom: 20px; font-size:14px; font-weight:bold;">' . htmlspecialchars($_SESSION["success_message"]) . '</p>';
                    unset($_SESSION["success_message"]);
                }
                if (isset($_SESSION["error_message"])) {
                    echo '<p style="color: red; text-align: center; margin-bottom: 20px; font-size:14px; font-weight:bold;">' . htmlspecialchars($_SESSION["error_message"]) . '</p>';
                    unset($_SESSION["error_message"]);
                }
                ?>

                <form id="profileForm" method="POST" action="/index.php?page=process_profile">
                    
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                        <div class="input-group">
                            <label for="full_name">HỌ VÀ TÊN</label>
                            <div class="input-wrapper">
                                <i class="fas fa-user icon"></i>
                                <input type="text" id="full_name" name="full_name" value="<?php echo htmlspecialchars($user["full_name"]); ?>" required placeholder="Nguyễn Văn A">
                            </div>
                        </div>

                        <div class="input-group">
                            <label for="phone">SỐ ĐIỆN THOẠI</label>
                            <div class="input-wrapper">
                                <i class="fas fa-phone icon"></i>
                                <input type="tel" id="phone" name="phone" value="<?php echo htmlspecialchars($user["phone"] ?? ""); ?>" required placeholder="0901234567">
                            </div>
                        </div>
                    </div>

                    <div class="input-group">
                        <label for="email">EMAIL (TÀI KHOẢN ĐĂNG NHẬP)</label>
                        <div class="input-wrapper">
                            <i class="fas fa-envelope icon"></i>
                            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user["email"]); ?>" required placeholder="email@example.com">
                        </div>
                    </div>

                    <h4 style="margin-top:25px; margin-bottom:15px; font-family:'Times New Roman', serif; color:#bfa15f; border-bottom: 1px solid #f0ece4; padding-bottom: 5px;">ĐỊA CHỈ GIAO HÀNG MẶC ĐỊNH</h4>

                    <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 10px;">
                        <div class="input-group">
                            <label for="province_city">TỈNH / THÀNH PHỐ</label>
                            <div class="input-wrapper">
                                <i class="fas fa-map-marker-alt icon"></i>
                                <input type="text" id="province_city" name="province_city" value="<?php echo htmlspecialchars($user["province_city"] ?? ""); ?>" placeholder="Ví dụ: Hà Nội">
                            </div>
                        </div>

                        <div class="input-group">
                            <label for="district">QUẬN / HUYỆN</label>
                            <div class="input-wrapper">
                                <i class="fas fa-map icon"></i>
                                <input type="text" id="district" name="district" value="<?php echo htmlspecialchars($user["district"] ?? ""); ?>" placeholder="Ví dụ: Cầu Giấy">
                            </div>
                        </div>

                        <div class="input-group">
                            <label for="ward_commune">PHƯỜNG / XÃ</label>
                            <div class="input-wrapper">
                                <i class="fas fa-directions icon"></i>
                                <input type="text" id="ward_commune" name="ward_commune" value="<?php echo htmlspecialchars($user["ward_commune"] ?? ""); ?>" placeholder="Ví dụ: Dịch Vọng">
                            </div>
                        </div>
                    </div>

                    <div class="input-group">
                        <label for="specific_address">ĐỊA CHỈ CHI TIẾT (SỐ NHÀ, TÊN ĐƯỜNG,...)</label>
                        <div class="input-wrapper">
                            <i class="fas fa-home icon"></i>
                            <input type="text" id="specific_address" name="specific_address" value="<?php echo htmlspecialchars($user["specific_address"] ?? ""); ?>" placeholder="Ví dụ: Số 12, ngõ 34">
                        </div>
                    </div>

                    <button type="submit" class="btn-primary" style="margin-top: 20px;">LƯU THÔNG TIN HỒ SƠ</button>
                </form>

                <div style="text-align: center; margin-top: 30px;">
                    <a href="/index.php?page=home" style="color: #bfa15f; text-decoration: none; font-weight: bold; font-size: 14px;">
                        Quay lại trang chủ
                    </a>
                </div>

            </div>

        </div>
    </body>
</html>

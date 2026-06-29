<?php
class AuthController
{
    // ==========================================
    // NHÓM 1: XỬ LÝ ĐĂNG KÝ
    // ==========================================
    public function showRegister()
    {
        require_once BASE_PATH . "/app/views/user/register.php";
    }

    public function handleRegister()
    {
        $fullName = $_POST["full_name"] ?? "";
        $email = $_POST["email"] ?? "";
        $phone = $_POST["phone"] ?? "";
        $password = $_POST["password"] ?? "";
        $confirmPassword = $_POST["confirm_password"] ?? "";

        if (
            empty($fullName) ||
            empty($email) ||
            empty($phone) ||
            empty($password)
        ) {
            echo "<script>alert('Vui lòng điền đầy đủ thông tin!'); window.history.back();</script>";
            return;
        }

        if ($password !== $confirmPassword) {
            echo "<script>alert('Mật khẩu xác nhận không khớp!'); window.history.back();</script>";
            return;
        }

        require_once BASE_PATH . "/app/models/UserModel.php";
        $userModel = new UserModel();

        if ($userModel->findByEmail($email)) {
            echo "<script>alert('Email này đã được đăng ký!'); window.history.back();</script>";
            return;
        }

        // Băm mật khẩu trước khi lưu
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        if ($userModel->create($fullName, $email, $phone, $hashedPassword)) {
            // Lưu thông báo vào Session thay vì alert
            $_SESSION["success_message"] =
                "Đăng ký thành công! Vui lòng đăng nhập.";
            header("Location: /index.php?page=login");
            exit();
        } else {
            $_SESSION["error_message"] = "Có lỗi xảy ra, vui lòng thử lại sau.";
            header("Location: /index.php?page=register");
            exit();
        }
    }

    // ==========================================
    // NHÓM 2: XỬ LÝ ĐĂNG NHẬP
    // ==========================================
    public function showLogin()
    {
        require_once BASE_PATH . "/app/views/user/login.php";
    }

    public function handleLogin()
    {
        // 1. Hứng dữ liệu từ Form
        $email = $_POST["email"] ?? "";
        $password = $_POST["password"] ?? "";

        // Kiểm tra rỗng
        if (empty($email) || empty($password)) {
            $_SESSION["error_message"] =
                "Vui lòng nhập đầy đủ email và mật khẩu!";
            header("Location: /index.php?page=login");
            exit();
        }

        require_once BASE_PATH . "/app/models/UserModel.php";
        $userModel = new UserModel();

        // 2. Đi tìm sự thật: Rút hồ sơ User từ DB lên dựa vào Email
        $user = $userModel->findByEmail($email);

        // 3. XÁC MINH BẢO MẬT KÉP
        if ($user && password_verify($password, $user["password"])) {
            if (($user["status"] ?? "active") !== "active") {
                $_SESSION["error_message"] =
                    "Tài khoản của bạn đang bị khóa!";
                header("Location: /index.php?page=login");
                exit();
            }

            // 4. Phát Thẻ Căn Cước (Session)
            session_regenerate_id(true);
            $_SESSION["user_logged_in"] = true;
            $_SESSION["user_id"] = $user["user_id"];
            $_SESSION["user_email"] = $user["email"];
            $_SESSION["user_name"] = $user["full_name"];
            $_SESSION["user_role"] = $user["role"];

            if ($user["role"] === "admin") {
                header("Location: /index.php?page=admin_dashboard");
                exit();
            }

            header("Location: /index.php?page=home");
            exit();
        } else {
            // ĐÒN BẨY RỦI RO: Đẩy lỗi vào Session và quay đầu
            $_SESSION["error_message"] = "Email hoặc mật khẩu không chính xác!";
            header("Location: /index.php?page=login");
            exit();
        }
    }
    public function showChangePassword()
    {
        require_once BASE_PATH . "/app/views/user/change_password.php";
    }

    public function handleChangePassword()
    {
        // 1. CHẶN TRẠNG THÁI (Ưu tiên số 1): Chưa đăng nhập thì không làm gì cả
        if (!isset($_SESSION["user_email"])) {
            header("Location: /index.php?page=login");
            exit();
        }

        // 2. NHẬN DỮ LIỆU
        $newPassword = $_POST["password"] ?? "";
        $confirmPassword = $_POST["confirm_password"] ?? "";

        // 3. KIỂM TRA ĐẦU VÀO
        if ($newPassword !== $confirmPassword) {
            $_SESSION["error_message"] = "Mật khẩu mới không khớp!";
            header("Location: /index.php?page=change_password");
            exit();
        }

        // 4. TƯƠNG TÁC DATABASE
        require_once BASE_PATH . "/app/models/UserModel.php";
        $userModel = new UserModel();

        $hashedNewPassword = password_hash($newPassword, PASSWORD_DEFAULT);

        if (
            $userModel->updatePassword(
                $_SESSION["user_email"],
                $hashedNewPassword,
            )
        ) {
            $_SESSION["success_message"] = "Đổi mật khẩu thành công!";
            header("Location: /index.php?page=home");
            exit();
        } else {
            $_SESSION["error_message"] =
                "Hệ thống đang bận, vui lòng thử lại sau!";
            header("Location: /index.php?page=change_password");
            exit();
        }
    }
    // --- NHÓM 3: XỬ LÝ QUÊN MẬT KHẨU ---

    // 1. Hiển thị form nhập email
    public function showForgotPassword()
    {
        require_once BASE_PATH . "/app/views/user/forgot_password.php";
    }

    // 2. Xử lý tạo Token và giả lập gửi email
    public function handleForgotPassword()
    {
        $email = $_POST["email"] ?? "";

        // 1. KIỂM TRA ĐẦU VÀO (Fail-fast): Chặn ngay nếu để trống
        if (empty($email)) {
            $_SESSION["error_message"] = "Vui lòng nhập địa chỉ email!";
            header("Location: /index.php?page=forgot_password");
            exit();
        }

        // 2. TƯƠNG TÁC DATABASE
        require_once BASE_PATH . "/app/models/UserModel.php";
        $userModel = new UserModel();

        if ($user = $userModel->findByEmail($email)) {
            // Tạo token và thời hạn
            $token = bin2hex(random_bytes(16));
            $expiry = date("Y-m-d H:i:s", strtotime("+15 minutes"));

            $userModel->setResetToken($email, $token, $expiry);

            // Tạm thời in link ra màn hình để bạn test ngay
            // BẢN THỰC TẾ: Bạn sẽ thay phần này bằng hàm gửi Mail và header() về trang Login
            echo "<h1>Mô phỏng gửi email</h1>";
            echo "<p>Link reset (chỉ dùng được trong 15p): <a href='/index.php?page=reset_password&token=$token'>Đặt lại mật khẩu</a></p>";
        } else {
            // 3. XỬ LÝ LỖI CHUẨN MVC: Gắn thông báo vào Session và đẩy về trang cũ
            $_SESSION["error_message"] = "Email không tồn tại trong hệ thống!";
            header("Location: /index.php?page=forgot_password");
            exit();
        }
    }

    // 3. Hiển thị form nhập mật khẩu mới (khi đã có token)
    public function showResetPasswordForm($token)
    {
        require_once BASE_PATH . "/app/models/UserModel.php";
        $userModel = new UserModel();

        // Kiểm tra token có hợp lệ không
        if ($userModel->findByToken($token)) {
            // Truyền token vào view qua biến $token
            require BASE_PATH . "/app/views/user/reset_password.php";
        } else {
            die("Token không hợp lệ hoặc đã hết hạn!");
        }
    }

    // 4. Xử lý lưu mật khẩu mới vào DB
    public function handleResetPassword()
    {
        $token = $_POST["token"] ?? "";
        $newPassword = $_POST["password"] ?? "";
        $confirmPassword = $_POST["confirm_password"] ?? "";

        // 1. BẢO VỆ ĐẦU VÀO: Tránh submit form rỗng
        if (empty($token) || empty($newPassword) || empty($confirmPassword)) {
            $_SESSION["error_message"] = "Vui lòng nhập đầy đủ thông tin!";
            header(
                "Location: /index.php?page=reset_password&token=" .
                    urlencode($token),
            );
            exit();
        }

        // 2. KIỂM TRA LOGIC: Mật khẩu không khớp
        if ($newPassword !== $confirmPassword) {
            $_SESSION["error_message"] = "Mật khẩu không khớp!";
            // Bắt buộc phải gắn kèm token cũ để form reset có thể tiếp tục hiển thị
            header(
                "Location: /index.php?page=reset_password&token=" .
                    urlencode($token),
            );
            exit();
        }

        require_once BASE_PATH . "/app/models/UserModel.php";
        $userModel = new UserModel();

        // 3. XÁC THỰC VÀ CẬP NHẬT
        // Xác thực lại token lần cuối
        if ($user = $userModel->findByToken($token)) {
            $hashed = password_hash($newPassword, PASSWORD_DEFAULT);
            $userModel->updatePassword($user["email"], $hashed);
            $userModel->clearResetToken($user["email"]); // Xóa token sau khi dùng xong

            $_SESSION["success_message"] =
                "Đổi mật khẩu thành công! Vui lòng đăng nhập.";
            header("Location: /index.php?page=login");
            exit();
        } else {
            // Lỗi token (hết hạn hoặc bị sửa bậy) -> Bắt người dùng làm lại từ đầu
            $_SESSION["error_message"] =
                "Liên kết không hợp lệ hoặc đã hết hạn!";
            header("Location: /index.php?page=forgot_password");
            exit();
        }
    }
}
?>

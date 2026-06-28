<?php
// 1. Nhúng thủ công thư viện PHPMailer từ thư mục lib của bạn
require_once "/var/www/html/vendor/autoload.php";
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
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
            // 4. Phát Thẻ Căn Cước (Session)
            $_SESSION["user_logged_in"] = true;
            $_SESSION["user_email"] = $user["email"];
            $_SESSION["user_name"] = $user["full_name"];

            // Chuyển hướng vào nhà
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
    // ==========================================
    // NHÓM 3: XỬ LÝ QUÊN MẬT KHẨU
    // ==========================================
    public function showForgotPassword()
    {
        require_once BASE_PATH . "/app/views/user/forgot_password.php";
    }

    // THAY ĐỔI HOÀN TOÀN LOGIC THỰC TẾ GỬI MAIL QUA MAILPIT Ở ĐÂY
    public function handleForgotPassword()
    {
        $email = $_POST["email"] ?? "";

        if (empty($email)) {
            $_SESSION["error_message"] = "Vui lòng nhập email!";
            header("Location: /index.php?page=forgot_password");
            exit();
        }

        require_once BASE_PATH . "/app/models/UserModel.php";
        $userModel = new UserModel();
        $user = $userModel->findByEmail($email);

        // Luôn hiện thông báo thành công (tránh lộ email tồn tại)
        if ($user) {
            $token = bin2hex(random_bytes(16));
            $expiry = date("Y-m-d H:i:s", strtotime("+15 minutes"));
            $userModel->setResetToken($email, $token, $expiry);

            $resetLink =
                "http://localhost:8080/index.php?page=reset_password&token=" .
                $token;

            $mail = new PHPMailer(true);
            try {
                $mail->isSMTP();
                $mail->Host = "mailpit";
                $mail->SMTPAuth = false;
                $mail->Port = 1025;
                $mail->SMTPAutoTLS = false;
                $mail->SMTPSecure = false;
                $mail->CharSet = "UTF-8";

                $mail->setFrom("no-reply@aurrelia.local", "Aurrelia Boutique");
                $mail->addAddress($email);
                $mail->isHTML(true);
                $mail->Subject = "Khôi phục mật khẩu Aurrelia";
                $mail->Body = "<p>Click để đặt lại: <a href='{$resetLink}'>{$resetLink}</a></p>";
                $mail->send();
            } catch (Exception $e) {
                // Log lỗi nội bộ, không lộ ra ngoài
                error_log("Mailer Error: " . $mail->ErrorInfo);
            }
        }

        $_SESSION["success_message"] =
            "Nếu email tồn tại, chúng tôi đã gửi liên kết đặt lại mật khẩu!";
        header("Location: /index.php?page=forgot_password");
        exit();
    }

    public function showResetPasswordForm($token)
    {
        require_once BASE_PATH . "/app/models/UserModel.php";
        $userModel = new UserModel();

        if ($userModel->findByToken($token)) {
            require BASE_PATH . "/app/views/user/reset_password.php";
        } else {
            die(
                "<h3 style='text-align:center; color:red; margin-top:50px;'>Token không hợp lệ hoặc đã hết hạn! Vui lòng thử lại.</h3>"
            );
        }
    }

    public function handleResetPassword()
    {
        // Kiểm tra kết nối
        $token = $_POST["token"] ?? "";
        $newPassword = $_POST["password"] ?? "";
        $confirmPassword = $_POST["confirm_password"] ?? "";

        if (empty($token) || empty($newPassword) || empty($confirmPassword)) {
            $_SESSION["error_message"] = "Vui lòng nhập đầy đủ thông tin!";
            header(
                "Location: /index.php?page=reset_password&token=" .
                    urlencode($token),
            );
            exit();
        }

        if ($newPassword !== $confirmPassword) {
            $_SESSION["error_message"] = "Mật khẩu không khớp!";
            header(
                "Location: /index.php?page=reset_password&token=" .
                    urlencode($token),
            );
            exit();
        }

        require_once BASE_PATH . "/app/models/UserModel.php";
        $userModel = new UserModel();

        if ($user = $userModel->findByToken($token)) {
            $hashed = password_hash($newPassword, PASSWORD_DEFAULT);
            $userModel->updatePassword($user["email"], $hashed);
            $userModel->clearResetToken($user["email"]);

            $_SESSION["success_message"] =
                "Đổi mật khẩu thành công! Vui lòng đăng nhập.";
            header("Location: /index.php?page=login");
            exit();
        } else {
            $_SESSION["error_message"] =
                "Liên kết không hợp lệ hoặc đã hết hạn!";
            header("Location: /index.php?page=forgot_password");
            exit();
        }
    }
}
?>

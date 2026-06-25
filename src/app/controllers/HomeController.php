<?php
class HomeController
{
    public function index()
    {
        // BẢO VỆ: Kiểm tra xem khách có Thẻ Session chưa?
        if (
            !isset($_SESSION["user_logged_in"]) ||
            $_SESSION["user_logged_in"] !== true
        ) {
            // Không có thẻ -> Đuổi về trang đăng nhập
            header("Location: /index.php?page=login");
            exit();
        }

        // Có thẻ thì mới bưng giao diện ra
        require_once BASE_PATH . "/app/views/user/index.php";
    }
    public function showGioHang()
    {
        if (
            !isset($_SESSION["user_logged_in"]) ||
            $_SESSION["user_logged_in"] !== true
        ) {
            header("Location: /index.php?page=login");
            exit();
        }
        require_once BASE_PATH . "/app/views/user/gio_hang.php";
    }
    public function showThanhToan()
    {
        if (
            !isset($_SESSION["user_logged_in"]) ||
            $_SESSION["user_logged_in"] !== true
        ) {
            header("Location: /index.php?page=login");
            exit();
        }
        ini_set("display_errors", 1);
        error_reporting(E_ALL);
        include BASE_PATH . "/app/views/user/thanh_toan.php";
    }
}

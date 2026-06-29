<?php
session_start();

// 1. Tọa độ gốc của toàn bộ dự án
define("BASE_PATH", dirname(__DIR__));

// 2. Lấy trang khách hàng muốn vào (mặc định là home)
$page = isset($_GET["page"]) ? $_GET["page"] : "home";

// 3. ĐIỀU PHỐI LOGIC
switch ($page) {
    case "home":
        require_once BASE_PATH . "/app/controllers/HomeController.php";
        $controller = new HomeController();
        $controller->index();
        break;

    case "login":
        require_once BASE_PATH . "/app/controllers/AuthController.php";
        $controller = new AuthController();
        $controller->showLogin();
        break;

    case "process_login":
        require_once BASE_PATH . "/app/controllers/AuthController.php";
        $controller = new AuthController();
        $controller->handleLogin();
        break;

    // --- 2 CỬA MỚI CHO TÍNH NĂNG ĐĂNG KÝ ---
    case "register":
        require_once BASE_PATH . "/app/controllers/AuthController.php";
        $controller = new AuthController();
        $controller->showRegister();
        break;

    case "process_register":
        require_once BASE_PATH . "/app/controllers/AuthController.php";
        $controller = new AuthController();
        $controller->handleRegister();
        break;
    // ----------------------------------------
    case "change_password":
        require_once BASE_PATH . "/app/controllers/AuthController.php";
        $controller = new AuthController();
        $controller->showChangePassword();
        break;

    case "process_change_password":
        require_once BASE_PATH . "/app/controllers/AuthController.php";
        $controller = new AuthController();
        $controller->handleChangePassword();
        break;

    // --- CỬA CHO TÍNH NĂNG QUÊN MẬT KHẨU ---
    case "forgot_password":
        require_once BASE_PATH . "/app/controllers/AuthController.php";
        $controller = new AuthController();
        $controller->showForgotPassword();
        break;

    case "process_forgot_password":
        require_once BASE_PATH . "/app/controllers/AuthController.php";
        $controller = new AuthController();
        $controller->handleForgotPassword();
        break;

    case "reset_password":
        $token = $_GET["token"] ?? ""; // Lấy token từ URL
        require_once BASE_PATH . "/app/controllers/AuthController.php";
        $controller = new AuthController();
        $controller->showResetPasswordForm($token);
        break;

    case "process_reset_password":
        require_once BASE_PATH . "/app/controllers/AuthController.php";
        $controller = new AuthController();
        $controller->handleResetPassword();
        break;
    // ----------------------------------------
    case "logout":
        session_destroy();
        header("Location: /index.php?page=home");
        exit();
        break;
    case "gio_hang":
        require_once BASE_PATH . "/app/controllers/HomeController.php";
        $controller = new HomeController();
        $controller->showGioHang();
        break;

    case "thanh_toan":
        require_once BASE_PATH . "/app/controllers/HomeController.php";
        $controller = new HomeController();
        $controller->showThanhToan();
        break;

    case "san_pham":
        require_once BASE_PATH . "/app/controllers/HomeController.php";
        $controller = new HomeController();
        $controller->showSanPham();
        break;

    case "chi_tiet":
        $product_id = isset($_GET["id"]) ? (int) $_GET["id"] : 0;
        require_once BASE_PATH . "/app/controllers/ProductController.php";
        $controller = new ProductController();
        $controller->showChiTiet($product_id);
        break;
    case "cart":
        require_once BASE_PATH . "/app/controllers/CartController.php";
        $controller = new CartController();
        $action = $_GET["action"] ?? ($_POST["action"] ?? "");
        switch ($action) {
            case "add":
                $controller->add();
                break;
            case "update":
                $controller->update();
                break;
            case "remove":
                $controller->remove();
                break;
            case "clear":
                $controller->clear();
                break;
            case "get":
                $controller->get();
                break;
            case "check_voucher":
                $controller->checkVoucher();
                break;
            default:
                header("Content-Type: application/json");
                echo json_encode([
                    "status" => "error",
                    "message" => "Action không hợp lệ",
                ]);
        }
        break;
    default:
        echo "<h1 style='text-align:center;'>Lỗi 404 - Không tìm thấy trang!</h1>";
        break;
}

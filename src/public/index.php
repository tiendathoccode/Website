<?php
session_start();

// 1. Tá»a Ä‘á»™ gá»‘c cá»§a toÃ n bá»™ dá»± Ã¡n
define("BASE_PATH", dirname(__DIR__));

// 2. Láº¥y trang khÃ¡ch hÃ ng muá»‘n vÃ o (máº·c Ä‘á»‹nh lÃ  home)
$page = isset($_GET["page"]) ? $_GET["page"] : "home";

// 3. ÄIá»€U PHá»I LOGIC
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

    case "admin_dashboard":
        require_once BASE_PATH . "/app/controllers/AdminController.php";
        $controller = new AdminController();
        $controller->dashboard();
        break;

    case "admin_categories":
        require_once BASE_PATH . "/app/controllers/AdminController.php";
        $controller = new AdminController();
        $controller->categories();
        break;

    case "admin_category_store":
        require_once BASE_PATH . "/app/controllers/AdminController.php";
        $controller = new AdminController();
        $controller->storeCategory();
        break;

    case "admin_category_update":
        require_once BASE_PATH . "/app/controllers/AdminController.php";
        $controller = new AdminController();
        $controller->updateCategory();
        break;

    case "admin_category_toggle":
        require_once BASE_PATH . "/app/controllers/AdminController.php";
        $controller = new AdminController();
        $controller->toggleCategoryStatus();
        break;

    case "admin_products":
        require_once BASE_PATH . "/app/controllers/AdminController.php";
        $controller = new AdminController();
        $controller->products();
        break;

    case "admin_product_create":
        require_once BASE_PATH . "/app/controllers/AdminController.php";
        $controller = new AdminController();
        $controller->createProduct();
        break;

    case "admin_product_store":
        require_once BASE_PATH . "/app/controllers/AdminController.php";
        $controller = new AdminController();
        $controller->storeProduct();
        break;

    case "admin_product_edit":
        require_once BASE_PATH . "/app/controllers/AdminController.php";
        $controller = new AdminController();
        $controller->editProduct();
        break;

    case "admin_product_update":
        require_once BASE_PATH . "/app/controllers/AdminController.php";
        $controller = new AdminController();
        $controller->updateProduct();
        break;

    case "admin_product_toggle":
        require_once BASE_PATH . "/app/controllers/AdminController.php";
        $controller = new AdminController();
        $controller->toggleProductStatus();
        break;

    case "admin_orders":
        require_once BASE_PATH . "/app/controllers/AdminController.php";
        $controller = new AdminController();
        $controller->orders();
        break;

    case "admin_order_detail":
        require_once BASE_PATH . "/app/controllers/AdminController.php";
        $controller = new AdminController();
        $controller->orderDetail();
        break;

    case "admin_order_update_status":
        require_once BASE_PATH . "/app/controllers/AdminController.php";
        $controller = new AdminController();
        $controller->updateOrderStatus();
        break;

    // --- 2 Cá»¬A Má»šI CHO TÃNH NÄ‚NG ÄÄ‚NG KÃ ---
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

    // --- Cá»¬A CHO TÃNH NÄ‚NG QUÃŠN Máº¬T KHáº¨U ---
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
        $token = $_GET["token"] ?? ""; // Láº¥y token tá»« URL
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
    default:
        echo "<h1 style='text-align:center;'>Lá»—i 404 - KhÃ´ng tÃ¬m tháº¥y trang!</h1>";
        break;
}

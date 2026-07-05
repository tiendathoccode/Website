<?php
session_start();

// 1. Tọa độ gốc của toàn bộ dự án
define("BASE_PATH", dirname(__DIR__));

// Kiểm tra xem người dùng hiện tại có bị khóa tài khoản (Ban) hay không
if (isset($_SESSION["user_logged_in"]) && $_SESSION["user_logged_in"] === true) {
    require_once BASE_PATH . "/config/database.php";
    $db = new Database();
    $conn = $db->getConnection();
    $stmtCheck = $conn->prepare("SELECT status FROM users WHERE user_id = :uid");
    $stmtCheck->execute([":uid" => $_SESSION["user_id"]]);
    $userStatus = $stmtCheck->fetchColumn();
    if ($userStatus === "locked") {
        session_destroy();
        session_start();
        $_SESSION["error_message"] = "Tài khoản của bạn đã bị khóa bởi quản trị viên!";
        header("Location: /index.php?page=login");
        exit();
    }
}

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

    case "don_hang":
        require_once BASE_PATH . "/app/controllers/HomeController.php";
        $controller = new HomeController();
        $controller->showDonHang();
        break;

    case "order_details":
        require_once BASE_PATH . "/app/controllers/CartController.php";
        $controller = new CartController();
        $controller->orderDetails();
        break;

    case "order_action":
        require_once BASE_PATH . "/app/controllers/CartController.php";
        $controller = new CartController();
        $controller->orderAction();
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

    case "place_order":
        require_once BASE_PATH . "/app/controllers/CartController.php";
        $controller = new CartController();
        $controller->placeOrder();
        break;

    case "profile":
        require_once BASE_PATH . "/app/controllers/AuthController.php";
        $controller = new AuthController();
        $controller->showProfile();
        break;

    case "process_profile":
        require_once BASE_PATH . "/app/controllers/AuthController.php";
        $controller = new AuthController();
        $controller->handleUpdateProfile();
        break;

    // --- CÁC ROUTE ADMIN ---
    case "admin_dashboard":
        require_once BASE_PATH . "/app/controllers/AdminController.php";
        $controller = new AdminController();
        $controller->showDashboard();
        break;

    case "admin_products":
        require_once BASE_PATH . "/app/controllers/AdminController.php";
        $controller = new AdminController();
        $controller->showProducts();
        break;

    case "admin_add_product":
        require_once BASE_PATH . "/app/controllers/AdminController.php";
        $controller = new AdminController();
        $controller->showAddProduct();
        break;

    case "admin_orders":
        require_once BASE_PATH . "/app/controllers/AdminController.php";
        $controller = new AdminController();
        $controller->showOrders();
        break;

    case "admin_users":
        require_once BASE_PATH . "/app/controllers/AdminController.php";
        $controller = new AdminController();
        $controller->showUsers();
        break;

    case "admin_categories":
        require_once BASE_PATH . "/app/controllers/AdminController.php";
        $controller = new AdminController();
        $controller->showCategories();
        break;

    case "admin_content":
        require_once BASE_PATH . "/app/controllers/AdminController.php";
        $controller = new AdminController();
        $controller->showContent();
        break;

    case "admin_api_update_user":
        require_once BASE_PATH . "/app/controllers/AdminController.php";
        $controller = new AdminController();
        $controller->apiUpdateUser();
        break;

    // --- CÁC ROUTE QUẢN LÝ NỘI DUNG API ---
    case "admin_api_get_banners":
        require_once BASE_PATH . "/app/controllers/AdminController.php";
        $controller = new AdminController();
        $controller->apiGetBanners();
        break;

    case "admin_api_save_banner":
        require_once BASE_PATH . "/app/controllers/AdminController.php";
        $controller = new AdminController();
        $controller->apiSaveBanner();
        break;

    case "admin_api_toggle_banner":
        require_once BASE_PATH . "/app/controllers/AdminController.php";
        $controller = new AdminController();
        $controller->apiToggleBanner();
        break;

    case "admin_api_delete_banner":
        require_once BASE_PATH . "/app/controllers/AdminController.php";
        $controller = new AdminController();
        $controller->apiDeleteBanner();
        break;

    case "admin_api_reorder_banners":
        require_once BASE_PATH . "/app/controllers/AdminController.php";
        $controller = new AdminController();
        $controller->apiReorderBanners();
        break;

    case "admin_api_get_reviews":
        require_once BASE_PATH . "/app/controllers/AdminController.php";
        $controller = new AdminController();
        $controller->apiGetReviews();
        break;

    case "admin_api_update_review_status":
        require_once BASE_PATH . "/app/controllers/AdminController.php";
        $controller = new AdminController();
        $controller->apiUpdateReviewStatus();
        break;

    case "admin_api_delete_review":
        require_once BASE_PATH . "/app/controllers/AdminController.php";
        $controller = new AdminController();
        $controller->apiDeleteReview();
        break;

    case "admin_api_get_faqs":
        require_once BASE_PATH . "/app/controllers/AdminController.php";
        $controller = new AdminController();
        $controller->apiGetFaqs();
        break;

    case "admin_api_save_faq":
        require_once BASE_PATH . "/app/controllers/AdminController.php";
        $controller = new AdminController();
        $controller->apiSaveFaq();
        break;

    case "admin_api_delete_faq":
        require_once BASE_PATH . "/app/controllers/AdminController.php";
        $controller = new AdminController();
        $controller->apiDeleteFaq();
        break;

    case "admin_api_reorder_faqs":
        require_once BASE_PATH . "/app/controllers/AdminController.php";
        $controller = new AdminController();
        $controller->apiReorderFaqs();
        break;

    case "admin_api_get_messages":
        require_once BASE_PATH . "/app/controllers/AdminController.php";
        $controller = new AdminController();
        $controller->apiGetMessages();
        break;

    case "admin_api_read_message":
        require_once BASE_PATH . "/app/controllers/AdminController.php";
        $controller = new AdminController();
        $controller->apiReadMessage();
        break;

    case "admin_api_mark_all_read":
        require_once BASE_PATH . "/app/controllers/AdminController.php";
        $controller = new AdminController();
        $controller->apiMarkAllRead();
        break;

    case "admin_api_delete_message":
        require_once BASE_PATH . "/app/controllers/AdminController.php";
        $controller = new AdminController();
        $controller->apiDeleteMessage();
        break;


    // --- CÁC ROUTE ADMIN API ---
    case "admin_api_add_product":
        require_once BASE_PATH . "/app/controllers/AdminController.php";
        $controller = new AdminController();
        $controller->apiAddProduct();
        break;

    case "admin_api_edit_product":
        require_once BASE_PATH . "/app/controllers/AdminController.php";
        $controller = new AdminController();
        $controller->apiEditProduct();
        break;

    case "admin_api_delete_product":
        require_once BASE_PATH . "/app/controllers/AdminController.php";
        $controller = new AdminController();
        $controller->apiDeleteProduct();
        break;

    case "admin_api_update_order":
        require_once BASE_PATH . "/app/controllers/AdminController.php";
        $controller = new AdminController();
        $controller->apiUpdateOrderStatus();
        break;

    case "admin_api_sales_chart":
        require_once BASE_PATH . "/app/controllers/AdminController.php";
        $controller = new AdminController();
        $controller->apiSalesChart();
        break;

    case "admin_export_sales":
        require_once BASE_PATH . "/app/controllers/AdminController.php";
        $controller = new AdminController();
        $controller->exportSales();
        break;

    case "admin_export_inventory":
        require_once BASE_PATH . "/app/controllers/AdminController.php";
        $controller = new AdminController();
        $controller->exportInventory();
        break;

    case "admin_api_add_category":
        require_once BASE_PATH . "/app/controllers/AdminController.php";
        $controller = new AdminController();
        $controller->apiAddCategory();
        break;

    case "admin_api_edit_category":
        require_once BASE_PATH . "/app/controllers/AdminController.php";
        $controller = new AdminController();
        $controller->apiEditCategory();
        break;

    case "admin_api_delete_category":
        require_once BASE_PATH . "/app/controllers/AdminController.php";
        $controller = new AdminController();
        $controller->apiDeleteCategory();
        break;

    case "admin_chat":
        require_once BASE_PATH . "/app/controllers/AdminController.php";
        $controller = new AdminController();
        $controller->showChat();
        break;

    case "chat":
        require_once BASE_PATH . "/app/controllers/ChatController.php";
        $controller = new ChatController();
        $controller->handleRequest();
        break;

    default:
        echo "<h1 style='text-align:center;'>Lỗi 404 - Không tìm thấy trang!</h1>";
        break;
}

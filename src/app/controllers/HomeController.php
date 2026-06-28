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
            header("Location: /index.php?page=login");
            exit();
        }

        // TỰ KHỞI TẠO KẾT NỐI DATABASE RIÊNG BIỆT TRONG HOME
        require_once BASE_PATH . "/config/database.php";
        $db = new Database();
        $dbConn = $db->getConnection(); // Lấy biến kết nối PDO gốc ra

        try {
            // Chỉ định đích danh bảng 'products' để tránh xung đột với bảng 'users'
            $query =
                "SELECT * FROM products WHERE status = 'show' ORDER BY product_id DESC";
            $stmt = $dbConn->prepare($query);
            $stmt->execute();
            $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $products = []; // Nếu lỗi phát sinh, trả về mảng rỗng để bảo vệ giao diện không bị sập
        }

        // Bưng giao diện ra (Biến $products đã sẵn sàng để đổ ra vòng lặp foreach)
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
        require_once BASE_PATH . "/app/views/user/shopping_cart.php"; // ← đổi tên file
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
        require_once BASE_PATH . "/app/views/user/pay.php"; // ← đổi tên file
    }
    public function showSanPham()
    {
        if (
            !isset($_SESSION["user_logged_in"]) ||
            $_SESSION["user_logged_in"] !== true
        ) {
            header("Location: /index.php?page=login");
            exit();
        }
        require_once BASE_PATH . "/app/views/user/products.php";
    }

    public function showChiTietSanPham()
    {
        if (
            !isset($_SESSION["user_logged_in"]) ||
            $_SESSION["user_logged_in"] !== true
        ) {
            header("Location: /index.php?page=login");
            exit();
        }
        require_once BASE_PATH . "/app/views/user/product-details.php";
    }
}

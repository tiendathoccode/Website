<?php
class HomeController
{
    public function index()
    {
        // TỰ KHỞI TẠO KẾT NỐI DATABASE RIÊNG BIỆT TRONG HOME
        require_once BASE_PATH . "/config/database.php";
        $db = new Database();
        $dbConn = $db->getConnection(); // Lấy biến kết nối PDO gốc ra

        // Lấy danh sách danh mục để hiển thị ở bộ lọc
        try {
            $stmtCats = $dbConn->query("SELECT * FROM categories WHERE status = 'show'");
            $categoriesList = $stmtCats->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $categoriesList = [];
        }

        // Lấy danh sách banner để hiển thị ở trang chủ
        try {
            $stmtBanners = $dbConn->query("SELECT * FROM banners WHERE status = 'show' ORDER BY display_order ASC");
            $bannersList = $stmtBanners->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $bannersList = [];
        }

        $categoryId = isset($_GET["category_id"]) ? (int)$_GET["category_id"] : 0;
        $priceFilter = isset($_GET["price_range"]) ? $_GET["price_range"] : "";
        $sortOrder = isset($_GET["sort"]) ? $_GET["sort"] : "newest";
        $search = isset($_GET["search"]) ? trim($_GET["search"]) : "";

        try {
            $query = "SELECT * FROM v_product_details WHERE status = 'show'";
            $params = [];

            if ($categoryId > 0) {
                $query .= " AND category_id = :category_id";
                $params[":category_id"] = $categoryId;
            }

            if (!empty($search)) {
                $query .= " AND (product_name LIKE :search OR description LIKE :search)";
                $params[":search"] = "%$search%";
            }

            if ($priceFilter === "under10m") {
                $query .= " AND price < 10000000";
            } elseif ($priceFilter === "10m-25m") {
                $query .= " AND price BETWEEN 10000000 AND 25000000";
            } elseif ($priceFilter === "over25m") {
                $query .= " AND price > 25000000";
            }

            if ($sortOrder === "price_asc") {
                $query .= " ORDER BY price ASC";
            } elseif ($sortOrder === "price_desc") {
                $query .= " ORDER BY price DESC";
            } else {
                $query .= " ORDER BY product_id DESC";
            }

            $stmt = $dbConn->prepare($query);
            $stmt->execute($params);
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
        require_once BASE_PATH . "/app/views/user/shopping_cart.php"; //
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

        require_once BASE_PATH . "/app/models/UserModel.php";
        $userModel = new UserModel();
        $user = $userModel->getDetailedProfile($_SESSION["user_id"]);

        require_once BASE_PATH . "/app/views/user/pay.php";
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

    public function showDonHang()
    {
        if (
            !isset($_SESSION["user_logged_in"]) ||
            $_SESSION["user_logged_in"] !== true
        ) {
            header("Location: /index.php?page=login");
            exit();
        }

        require_once BASE_PATH . "/config/database.php";
        $db = new Database();
        $conn = $db->getConnection();

        $user_id = $_SESSION["user_id"];

        // Lấy danh sách đơn hàng của user này
        $stmt = $conn->prepare("SELECT * FROM orders WHERE user_id = :uid ORDER BY order_id DESC");
        $stmt->execute([":uid" => $user_id]);
        $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

        require_once BASE_PATH . "/app/views/user/don_hang.php";
    }
}

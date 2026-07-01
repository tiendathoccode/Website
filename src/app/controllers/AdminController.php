<?php
class AdminController
{
    private $conn;

    public function __construct()
    {
        // Kiểm tra quyền Admin trước khi thực hiện bất cứ hành động nào
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $this->requireAdmin();

        require_once BASE_PATH . "/config/database.php";
        $db = new Database();
        $this->conn = $db->getConnection();
    }

    private function requireAdmin()
    {
        if (
            !isset($_SESSION["user_logged_in"]) ||
            $_SESSION["user_logged_in"] !== true ||
            ($_SESSION["user_role"] ?? "") !== "admin"
        ) {
            // Nếu là yêu cầu API, trả về JSON
            if (isset($_GET["action"]) || (isset($_GET["page"]) && strpos($_GET["page"], "api") !== false)) {
                header("Content-Type: application/json");
                echo json_encode(["status" => "error", "message" => "Unauthorized access."]);
                exit();
            }
            // Ngược lại, điều hướng về trang đăng nhập
            header("Location: /index.php?page=login");
            exit();
        }
    }

    private function json($data)
    {
        header("Content-Type: application/json");
        echo json_encode($data);
        exit();
    }

    // ==========================================
    // 1. RENDER TRANG GIAO DIỆN
    // ==========================================
    public function showDashboard()
    {
        // Lấy thông số tổng quan từ DB đổ thẳng vào view PHP
        $totalSales = $this->conn->query("SELECT SUM(final_amount) FROM orders WHERE status != 'cancelled'")->fetchColumn() ?? 0;
        $totalOrders = $this->conn->query("SELECT COUNT(*) FROM orders")->fetchColumn() ?? 0;
        $activeCustomers = $this->conn->query("SELECT COUNT(*) FROM users WHERE role = 'customer'")->fetchColumn() ?? 0;
        $lowStock = $this->conn->query("SELECT COUNT(*) FROM products WHERE stock_quantity <= 5")->fetchColumn() ?? 0;

        // Lấy 5 đơn hàng mới nhất
        $stmt = $this->conn->prepare(
            "SELECT o.*, u.full_name FROM orders o 
             JOIN users u ON o.user_id = u.user_id 
             ORDER BY o.order_id DESC LIMIT 5"
        );
        $stmt->execute();
        $recentOrders = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Lấy 5 cảnh báo tồn kho thấp
        $lowStockProducts = $this->conn->query("SELECT * FROM products WHERE stock_quantity <= 5 ORDER BY stock_quantity ASC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);

        require_once BASE_PATH . "/app/views/admin/dashboard.php";
    }

    public function showProducts()
    {
        // Lấy toàn bộ sản phẩm kèm danh mục từ view v_product_details
        $stmt = $this->conn->prepare("SELECT * FROM v_product_details ORDER BY product_id DESC");
        $stmt->execute();
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Lấy tất cả danh mục để hiển thị trong bộ lọc/modal sửa
        $categories = $this->conn->query("SELECT * FROM categories WHERE status = 'show'")->fetchAll(PDO::FETCH_ASSOC);

        require_once BASE_PATH . "/app/views/admin/productsmanage.php";
    }

    public function showAddProduct()
    {
        $categories = $this->conn->query("SELECT * FROM categories WHERE status = 'show'")->fetchAll(PDO::FETCH_ASSOC);
        require_once BASE_PATH . "/app/views/admin/add-products.php";
    }

    public function showOrders()
    {
        // Lấy danh sách tất cả các đơn hàng kèm tên khách hàng
        $stmt = $this->conn->prepare(
            "SELECT o.*, u.full_name FROM orders o 
             JOIN users u ON o.user_id = u.user_id 
             ORDER BY o.order_id DESC"
        );
        $stmt->execute();
        $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

        require_once BASE_PATH . "/app/views/admin/ordersmanage.php";
    }

    // ==========================================
    // 2. CÁC API XỬ LÝ ACTIONS (AJAX)
    // ==========================================

    // API thêm sản phẩm mới
    public function apiAddProduct()
    {
        if ($_SERVER["REQUEST_METHOD"] !== "POST") {
            $this->json(["status" => "error", "message" => "Method not allowed."]);
        }

        $name = trim($_POST["name"] ?? "");
        $categoryId = (int)($_POST["category_id"] ?? 0);
        $price = (int)($_POST["price"] ?? 0);
        $salePrice = (int)($_POST["sale_price"] ?? 0);
        $stock = (int)($_POST["stock"] ?? 0);
        $description = trim($_POST["description"] ?? "");
        
        // Mặc định ảnh mẫu sang trọng nếu không upload
        $mainImage = "assets/images/product/image_2076968380_1.jpg";

        // Xử lý upload ảnh nếu có
        if (isset($_FILES["main_image"]) && $_FILES["main_image"]["error"] === UPLOAD_ERR_OK) {
            $fileTmpPath = $_FILES["main_image"]["tmp_name"];
            $fileName = $_FILES["main_image"]["name"];
            $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

            $newFileName = "img_" . time() . "_" . rand(1000, 9999) . "." . $fileExtension;
            $uploadFileDir = BASE_PATH . "/public/assets/images/product/";

            // Đảm bảo thư mục tồn tại
            if (!is_dir($uploadFileDir)) {
                mkdir($uploadFileDir, 0755, true);
            }

            $dest_path = $uploadFileDir . $newFileName;
            if (move_uploaded_file($fileTmpPath, $dest_path)) {
                $mainImage = "assets/images/product/" . $newFileName;
            }
        }

        if (empty($name) || !$categoryId || !$price) {
            $_SESSION["error_message"] = "Vui lòng nhập tên, danh mục và giá sản phẩm.";
            header("Location: /index.php?page=admin_add_product");
            exit();
        }

        try {
            $stmt = $this->conn->prepare(
                "INSERT INTO products (category_id, product_name, description, price, sale_price, stock_quantity, main_image, status) 
                 VALUES (:cat, :name, :desc, :price, :sale, :stock, :img, 'show')"
            );
            $stmt->execute([
                ":cat" => $categoryId,
                ":name" => $name,
                ":desc" => $description,
                ":price" => $price,
                ":sale" => $salePrice,
                ":stock" => $stock,
                ":img" => $mainImage
            ]);

            $_SESSION["success_message"] = "Thêm sản phẩm thành công!";
            header("Location: /index.php?page=admin_products");
            exit();
        } catch (Exception $e) {
            $_SESSION["error_message"] = "Lỗi thêm sản phẩm: " . $e->getMessage();
            header("Location: /index.php?page=admin_add_product");
            exit();
        }
    }

    // API Cập nhật trạng thái đơn hàng (sử dụng AJAX từ ordersmanage)
    public function apiUpdateOrderStatus()
    {
        $orderId = (int)($_POST["order_id"] ?? 0);
        $status = $_POST["status"] ?? "";
        $paymentMethod = $_POST["payment_method"] ?? "";

        if (!$orderId || empty($status)) {
            $this->json(["status" => "error", "message" => "Thiếu thông tin cập nhật."]);
        }

        try {
            // Cập nhật trạng thái đơn hàng
            $stmt = $this->conn->prepare("UPDATE orders SET status = :status, payment_method = :pmethod WHERE order_id = :oid");
            $stmt->execute([
                ":status" => $status,
                ":pmethod" => $paymentMethod ? $paymentMethod : "cod",
                ":oid" => $orderId
            ]);

            $this->json(["status" => "ok", "message" => "Cập nhật đơn hàng thành công."]);
        } catch (Exception $e) {
            $this->json(["status" => "error", "message" => $e->getMessage()]);
        }
    }

    // API Chỉnh sửa sản phẩm
    public function apiEditProduct()
    {
        $productId = (int)($_POST["product_id"] ?? 0);
        $name = trim($_POST["name"] ?? "");
        $categoryId = (int)($_POST["category_id"] ?? 0);
        $price = (int)($_POST["price"] ?? 0);
        $stock = (int)($_POST["stock"] ?? 0);
        $description = trim($_POST["description"] ?? "");

        if (!$productId || empty($name) || !$categoryId || !$price) {
            $this->json(["status" => "error", "message" => "Vui lòng nhập đầy đủ thông tin bắt buộc."]);
        }

        try {
            $stmt = $this->conn->prepare(
                "UPDATE products SET 
                    product_name = :name, 
                    category_id = :cat, 
                    price = :price, 
                    stock_quantity = :stock, 
                    description = :desc 
                 WHERE product_id = :pid"
            );
            $stmt->execute([
                ":name" => $name,
                ":cat" => $categoryId,
                ":price" => $price,
                ":stock" => $stock,
                ":desc" => $description,
                ":pid" => $productId
            ]);

            $this->json(["status" => "ok", "message" => "Cập nhật sản phẩm thành công."]);
        } catch (Exception $e) {
            $this->json(["status" => "error", "message" => $e->getMessage()]);
        }
    }

    // API Xóa sản phẩm
    public function apiDeleteProduct()
    {
        $productId = (int)($_POST["product_id"] ?? 0);

        if (!$productId) {
            $this->json(["status" => "error", "message" => "Thiếu product_id."]);
        }

        try {
            $stmt = $this->conn->prepare("DELETE FROM products WHERE product_id = :pid");
            $stmt->execute([":pid" => $productId]);

            $this->json(["status" => "ok", "message" => "Đã xóa sản phẩm thành công."]);
        } catch (Exception $e) {
            $this->json(["status" => "error", "message" => $e->getMessage()]);
        }
    }

    // API Lấy dữ liệu thống kê biểu đồ doanh thu dạng JSON
    public function apiSalesChart()
    {
        // Mặc định 6 tháng nếu trống hoặc phát sinh lỗi truy vấn
        $months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'];
        $sales = [65000000, 58000000, 81000000, 80000000, 55000000, 92000000];

        try {
            // Nhóm doanh thu theo tháng của các đơn hàng (trừ đơn bị hủy)
            $query = "SELECT DATE_FORMAT(created_at, '%b') as month, SUM(final_amount) as sales 
                      FROM orders 
                      WHERE status != 'cancelled' 
                      GROUP BY DATE_FORMAT(created_at, '%b'), MONTH(created_at)
                      ORDER BY MONTH(created_at) ASC";
            $results = $this->conn->query($query)->fetchAll(PDO::FETCH_ASSOC);

            if (!empty($results)) {
                $months = [];
                $sales = [];
                foreach ($results as $row) {
                    $months[] = $row["month"];
                    $sales[] = (int)$row["sales"];
                }
            }
        } catch (PDOException $e) {
            // Ghi nhận lỗi ngầm nhưng không làm sập giao diện biểu đồ
            error_log("Error generating sales chart statistics: " . $e->getMessage());
        }

        $this->json([
            "months" => $months,
            "sales" => $sales
        ]);
    }

    public function showUsers()
    {
        $users = $this->conn->query("SELECT * FROM users ORDER BY user_id DESC")->fetchAll(PDO::FETCH_ASSOC);
        require_once BASE_PATH . "/app/views/admin/usersmanage.php";
    }

    public function apiUpdateUser()
    {
        if ($_SERVER["REQUEST_METHOD"] !== "POST") {
            $this->json(["status" => "error", "message" => "Phương thức không được hỗ trợ."]);
        }

        $userId = isset($_POST["user_id"]) ? (int)$_POST["user_id"] : 0;
        $fullName = trim($_POST["full_name"] ?? "");
        $phone = trim($_POST["phone"] ?? "");
        $role = trim($_POST["role"] ?? "");
        $status = trim($_POST["status"] ?? "");

        if ($userId <= 0 || empty($fullName) || empty($phone) || !in_array($role, ["admin", "customer"]) || !in_array($status, ["active", "locked"])) {
            $this->json(["status" => "error", "message" => "Thông tin không hợp lệ hoặc bị thiếu."]);
        }

        if ($userId === (int)($_SESSION["user_id"] ?? 0)) {
            if ($status !== "active" || $role !== "admin") {
                $this->json(["status" => "error", "message" => "Bạn không thể tự vô hiệu hóa hoặc đổi vai trò tài khoản quản trị của chính mình!"]);
            }
        }

        try {
            $stmt = $this->conn->prepare(
                "UPDATE users 
                 SET full_name = :name, phone = :phone, role = :role, status = :status 
                 WHERE user_id = :id"
            );
            $stmt->execute([
                ":name" => $fullName,
                ":phone" => $phone,
                ":role" => $role,
                ":status" => $status,
                ":id" => $userId
            ]);

            $this->json(["status" => "ok", "message" => "Cập nhật thông tin người dùng thành công!"]);
        } catch (Exception $e) {
            $this->json(["status" => "error", "message" => "Lỗi hệ thống: " . $e->getMessage()]);
        }
    }
}

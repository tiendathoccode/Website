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
                @mkdir($uploadFileDir, 0777, true);
            }

            $dest_path = $uploadFileDir . $newFileName;
            if (@move_uploaded_file($fileTmpPath, $dest_path)) {
                $mainImage = "assets/images/product/" . $newFileName;
            } else {
                $_SESSION["error_message"] = "Không thể ghi tệp tải lên (Lỗi phân quyền thư mục). Hệ thống đã tạo sản phẩm với ảnh mặc định.";
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
            // Nếu chuyển trạng thái sang cancelled, khôi phục tồn kho sản phẩm
            if ($status === 'cancelled') {
                $this->restoreOrderStock($orderId);
            }

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

    private function restoreOrderStock($orderId)
    {
        $stmtStatus = $this->conn->prepare("SELECT status FROM orders WHERE order_id = :oid");
        $stmtStatus->execute([":oid" => $orderId]);
        $oldStatus = $stmtStatus->fetchColumn();

        if ($oldStatus && $oldStatus !== 'cancelled') {
            $stmtItems = $this->conn->prepare("SELECT product_id, quantity FROM order_details WHERE order_id = :oid");
            $stmtItems->execute([":oid" => $orderId]);
            $items = $stmtItems->fetchAll(PDO::FETCH_ASSOC);

            $stmtRestore = $this->conn->prepare("UPDATE products SET stock_quantity = stock_quantity + :qty WHERE product_id = :pid");
            foreach ($items as $item) {
                $stmtRestore->execute([
                    ":qty" => $item["quantity"],
                    ":pid" => $item["product_id"]
                ]);
            }
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

    public function showChat()
    {
        require_once BASE_PATH . "/app/views/admin/chat.php";
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

    // ==========================================
    // 3. QUẢN LÝ DANH MỤC
    // ==========================================
    public function showCategories()
    {
        $categories = $this->conn->query("SELECT * FROM categories ORDER BY category_id DESC")->fetchAll(PDO::FETCH_ASSOC);
        require_once BASE_PATH . "/app/views/admin/categoriesmanage.php";
    }

    public function apiAddCategory()
    {
        if ($_SERVER["REQUEST_METHOD"] !== "POST") {
            $this->json(["status" => "error", "message" => "Phương thức không được hỗ trợ."]);
        }

        $name = trim($_POST["category_name"] ?? "");
        $desc = trim($_POST["description"] ?? "");
        $status = trim($_POST["status"] ?? "show");

        if (empty($name) || !in_array($status, ["show", "hide"])) {
            $this->json(["status" => "error", "message" => "Vui lòng nhập tên danh mục hợp lệ."]);
        }

        try {
            $stmt = $this->conn->prepare("INSERT INTO categories (category_name, description, status) VALUES (:name, :desc, :status)");
            $stmt->execute([
                ":name" => $name,
                ":desc" => $desc,
                ":status" => $status
            ]);
            $this->json(["status" => "ok", "message" => "Thêm danh mục mới thành công!"]);
        } catch (Exception $e) {
            $this->json(["status" => "error", "message" => "Lỗi: " . $e->getMessage()]);
        }
    }

    public function apiEditCategory()
    {
        if ($_SERVER["REQUEST_METHOD"] !== "POST") {
            $this->json(["status" => "error", "message" => "Phương thức không được hỗ trợ."]);
        }

        $categoryId = isset($_POST["category_id"]) ? (int)$_POST["category_id"] : 0;
        $name = trim($_POST["category_name"] ?? "");
        $desc = trim($_POST["description"] ?? "");
        $status = trim($_POST["status"] ?? "show");

        if ($categoryId <= 0 || empty($name) || !in_array($status, ["show", "hide"])) {
            $this->json(["status" => "error", "message" => "Thông tin danh mục không hợp lệ."]);
        }

        try {
            $stmt = $this->conn->prepare("UPDATE categories SET category_name = :name, description = :desc, status = :status WHERE category_id = :id");
            $stmt->execute([
                ":name" => $name,
                ":desc" => $desc,
                ":status" => $status,
                ":id" => $categoryId
            ]);
            $this->json(["status" => "ok", "message" => "Cập nhật danh mục thành công!"]);
        } catch (Exception $e) {
            $this->json(["status" => "error", "message" => "Lỗi: " . $e->getMessage()]);
        }
    }

    public function apiDeleteCategory()
    {
        if ($_SERVER["REQUEST_METHOD"] !== "POST") {
            $this->json(["status" => "error", "message" => "Phương thức không được hỗ trợ."]);
        }

        $categoryId = isset($_POST["category_id"]) ? (int)$_POST["category_id"] : 0;
        if ($categoryId <= 0) {
            $this->json(["status" => "error", "message" => "Mã danh mục không hợp lệ."]);
        }

        try {
            // Kiểm tra xem danh mục có đang chứa sản phẩm nào không
            $stmtCheck = $this->conn->prepare("SELECT COUNT(*) FROM products WHERE category_id = :id");
            $stmtCheck->execute([":id" => $categoryId]);
            $productCount = $stmtCheck->fetchColumn();

            if ($productCount > 0) {
                // Không được xóa cứng để tránh lỗi foreign key, đổi trạng thái ẩn danh mục hoặc cảnh báo
                $stmtUpdate = $this->conn->prepare("UPDATE categories SET status = 'hide' WHERE category_id = :id");
                $stmtUpdate->execute([":id" => $categoryId]);
                $this->json(["status" => "ok", "message" => "Danh mục hiện đang có sản phẩm. Hệ thống đã chuyển trạng thái sang ẨN thay vì xóa cứng!"]);
            } else {
                // Xóa cứng nếu không có sản phẩm nào
                $stmtDel = $this->conn->prepare("DELETE FROM categories WHERE category_id = :id");
                $stmtDel->execute([":id" => $categoryId]);
                $this->json(["status" => "ok", "message" => "Xóa danh mục thành công!"]);
            }
        } catch (Exception $e) {
            $this->json(["status" => "error", "message" => "Lỗi: " . $e->getMessage()]);
        }
    }

    // ==========================================
    // 4. XUẤT BÁO CÁO CSV
    // ==========================================
    public function exportSales()
    {
        try {
            $stmt = $this->conn->prepare(
                "SELECT o.order_id, o.order_code, u.full_name, u.email, o.receiver_name, o.receiver_phone, o.shipping_address, o.total_amount, o.discount_amount, o.final_amount, o.payment_method, o.status, o.created_at 
                 FROM orders o 
                 JOIN users u ON o.user_id = u.user_id 
                 ORDER BY o.order_id DESC"
            );
            $stmt->execute();
            $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Xóa bộ đệm đầu ra để tránh lỗi font hoặc khoảng trắng trước file download
            if (ob_get_level()) {
                ob_end_clean();
            }

            header('Content-Type: text/csv; charset=utf-8');
            header('Content-Disposition: attachment; filename="aurrelia_bao_cao_doanh_thu_' . date('Ymd_His') . '.csv"');
            
            $output = fopen('php://output', 'w');
            fwrite($output, "\xEF\xBB\xBF"); // UTF-8 BOM
            
            // Header cột
            fputcsv($output, [
                'ID Đơn', 
                'Mã Đơn Hàng', 
                'Khách Hàng', 
                'Email', 
                'Người Nhận', 
                'Số Điện Thoại', 
                'Địa Chỉ Giao Hàng', 
                'Tổng Tiền (đ)', 
                'Giảm Giá (đ)', 
                'Thành Tiền (đ)', 
                'Phương Thức', 
                'Trạng Thái', 
                'Thời Gian Đặt'
            ]);

            foreach ($orders as $o) {
                fputcsv($output, [
                    $o['order_id'],
                    $o['order_code'],
                    $o['full_name'],
                    $o['email'],
                    $o['receiver_name'],
                    $o['receiver_phone'],
                    $o['shipping_address'],
                    (int)$o['total_amount'],
                    (int)$o['discount_amount'],
                    (int)$o['final_amount'],
                    strtoupper($o['payment_method']),
                    $this->getOrderStatusLabel($o['status']),
                    date('d/m/Y H:i:s', strtotime($o['created_at']))
                ]);
            }
            fclose($output);
            exit();
        } catch (Exception $e) {
            echo "Lỗi xuất báo cáo: " . $e->getMessage();
            exit();
        }
    }

    public function exportInventory()
    {
        try {
            $stmt = $this->conn->prepare(
                "SELECT p.product_id, p.product_name, c.category_name, p.price, p.sale_price, p.stock_quantity, p.status, p.created_at 
                 FROM products p 
                 LEFT JOIN categories c ON p.category_id = c.category_id 
                 ORDER BY p.product_id DESC"
            );
            $stmt->execute();
            $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Xóa bộ đệm đầu ra để tránh lỗi font hoặc khoảng trắng trước file download
            if (ob_get_level()) {
                ob_end_clean();
            }

            header('Content-Type: text/csv; charset=utf-8');
            header('Content-Disposition: attachment; filename="aurrelia_bao_cao_ton_kho_' . date('Ymd_His') . '.csv"');
            
            $output = fopen('php://output', 'w');
            fwrite($output, "\xEF\xBB\xBF"); // UTF-8 BOM
            
            fputcsv($output, [
                'Mã SP', 
                'Tên Sản Phẩm', 
                'Danh Mục', 
                'Giá Gốc (đ)', 
                'Giá Giảm (đ)', 
                'Số Lượng Tồn', 
                'Trạng Thái', 
                'Ngày Tạo'
            ]);

            foreach ($products as $p) {
                fputcsv($output, [
                    $p['product_id'],
                    $p['product_name'],
                    $p['category_name'] ?? 'Chưa phân loại',
                    (int)$p['price'],
                    (int)$p['sale_price'],
                    (int)$p['stock_quantity'],
                    ($p['status'] === 'show' ? 'Đang bán' : 'Đang ẩn'),
                    date('d/m/Y H:i:s', strtotime($p['created_at']))
                ]);
            }
            fclose($output);
            exit();
        } catch (Exception $e) {
            echo "Lỗi xuất báo cáo: " . $e->getMessage();
            exit();
        }
    }

    private function getOrderStatusLabel($status)
    {
        $labels = [
            'pending' => 'Chờ xử lý',
            'processing' => 'Đang xử lý',
            'shipped' => 'Đang giao hàng',
            'delivered' => 'Đã giao hàng',
            'cancelled' => 'Đã hủy',
            'returned' => 'Đã hoàn trả',
            'return_requested' => 'Yêu cầu hoàn trả'
        ];
        return $labels[$status] ?? $status;
    }

    // ==========================================
    // 3. QUẢN LÝ NỘI DUNG (BANNERS, REVIEWS, FAQS, CONTACTS)
    // ==========================================
    
    public function showContent()
    {
        require_once BASE_PATH . "/app/views/admin/contentmanage.php";
    }

    // --- BANNERS API ---
    public function apiGetBanners()
    {
        try {
            $stmt = $this->conn->query("SELECT * FROM banners ORDER BY display_order ASC");
            $banners = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $this->json([
                "status" => "success",
                "data" => $banners
            ]);
        } catch (Exception $e) {
            $this->json([
                "status" => "error",
                "message" => $e->getMessage()
            ]);
        }
    }

    public function apiSaveBanner()
    {
        try {
            $banner_id = isset($_POST["banner_id"]) && $_POST["banner_id"] !== '' ? (int)$_POST["banner_id"] : null;
            $title = trim($_POST["title"] ?? "");
            $target_link = trim($_POST["target_link"] ?? "");
            $display_order = isset($_POST["display_order"]) ? (int)$_POST["display_order"] : 0;
            $status = isset($_POST["status"]) && ($_POST["status"] === "show" || $_POST["status"] === "1" || $_POST["status"] === "true") ? "show" : "hide";

            $image_url = null;
            if ($banner_id) {
                $stmt = $this->conn->prepare("SELECT image_url FROM banners WHERE banner_id = ?");
                $stmt->execute([$banner_id]);
                $image_url = $stmt->fetchColumn();
            }

            if (isset($_FILES["image"]) && $_FILES["image"]["error"] === UPLOAD_ERR_OK) {
                $fileTmpPath = $_FILES["image"]["tmp_name"];
                $fileName = $_FILES["image"]["name"];
                $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
                $newFileName = "banner_" . time() . "_" . rand(1000, 9999) . "." . $fileExtension;
                $uploadFileDir = BASE_PATH . "/public/assets/images/banner/";

                if (!is_dir($uploadFileDir)) {
                    @mkdir($uploadFileDir, 0777, true);
                }

                $dest_path = $uploadFileDir . $newFileName;
                if (@move_uploaded_file($fileTmpPath, $dest_path)) {
                    $image_url = "assets/images/banner/" . $newFileName;
                }
            }

            if (empty($image_url)) {
                $this->json(["status" => "error", "message" => "Vui lòng tải lên ảnh banner."]);
            }

            if ($banner_id) {
                $stmt = $this->conn->prepare("UPDATE banners SET title = ?, image_url = ?, target_link = ?, display_order = ?, status = ? WHERE banner_id = ?");
                $stmt->execute([$title, $image_url, $target_link, $display_order, $status, $banner_id]);
                $this->json(["status" => "success", "message" => "Đã cập nhật banner thành công."]);
            } else {
                $stmt = $this->conn->prepare("INSERT INTO banners (title, image_url, target_link, display_order, status) VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([$title, $image_url, $target_link, $display_order, $status]);
                $this->json(["status" => "success", "message" => "Đã thêm banner thành công."]);
            }
        } catch (Exception $e) {
            $this->json(["status" => "error", "message" => $e->getMessage()]);
        }
    }

    public function apiToggleBanner()
    {
        try {
            $banner_id = (int)($_POST["banner_id"] ?? 0);
            if (!$banner_id) {
                $this->json(["status" => "error", "message" => "ID không hợp lệ."]);
            }
            $stmt = $this->conn->prepare("SELECT status FROM banners WHERE banner_id = ?");
            $stmt->execute([$banner_id]);
            $current = $stmt->fetchColumn();
            $newStatus = ($current === "show") ? "hide" : "show";

            $stmt = $this->conn->prepare("UPDATE banners SET status = ? WHERE banner_id = ?");
            $stmt->execute([$newStatus, $banner_id]);
            $this->json(["status" => "success", "message" => "Đã cập nhật trạng thái banner.", "new_status" => $newStatus]);
        } catch (Exception $e) {
            $this->json(["status" => "error", "message" => $e->getMessage()]);
        }
    }

    public function apiDeleteBanner()
    {
        try {
            $banner_id = (int)($_POST["banner_id"] ?? 0);
            if (!$banner_id) {
                $this->json(["status" => "error", "message" => "ID không hợp lệ."]);
            }
            $stmt = $this->conn->prepare("DELETE FROM banners WHERE banner_id = ?");
            $stmt->execute([$banner_id]);
            $this->json(["status" => "success", "message" => "Đã xoá banner thành công."]);
        } catch (Exception $e) {
            $this->json(["status" => "error", "message" => $e->getMessage()]);
        }
    }

    public function apiReorderBanners()
    {
        try {
            $orders = $_POST["orders"] ?? [];
            if (empty($orders)) {
                $input = json_decode(file_get_contents('php://input'), true);
                $orders = $input["orders"] ?? [];
            }
            $this->conn->beginTransaction();
            $stmt = $this->conn->prepare("UPDATE banners SET display_order = ? WHERE banner_id = ?");
            foreach ($orders as $item) {
                $stmt->execute([$item['order'], $item['id']]);
            }
            $this->conn->commit();
            $this->json(["status" => "success", "message" => "Đã cập nhật thứ tự banner."]);
        } catch (Exception $e) {
            if ($this->conn->inTransaction()) {
                $this->conn->rollBack();
            }
            $this->json(["status" => "error", "message" => $e->getMessage()]);
        }
    }

    // --- REVIEWS API ---
    public function apiGetReviews()
    {
        try {
            $status = $_GET["status"] ?? "all";
            $sql = "SELECT r.*, u.full_name, p.product_name 
                    FROM reviews r 
                    JOIN users u ON r.user_id = u.user_id 
                    JOIN products p ON r.product_id = p.product_id";
            
            $params = [];
            if ($status !== "all") {
                $sql .= " WHERE r.status = ?";
                $params[] = $status;
            }
            $sql .= " ORDER BY r.review_id DESC";

            $stmt = $this->conn->prepare($sql);
            $stmt->execute($params);
            $reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $this->json([
                "status" => "success",
                "data" => $reviews
            ]);
        } catch (Exception $e) {
            $this->json([
                "status" => "error",
                "message" => $e->getMessage()
            ]);
        }
    }

    public function apiUpdateReviewStatus()
    {
        try {
            $review_id = (int)($_POST["review_id"] ?? 0);
            $status = trim($_POST["status"] ?? "");
            if (!$review_id || !in_array($status, ['pending', 'approved', 'hidden'])) {
                $this->json(["status" => "error", "message" => "Dữ liệu không hợp lệ."]);
            }

            $stmt = $this->conn->prepare("UPDATE reviews SET status = ? WHERE review_id = ?");
            $stmt->execute([$status, $review_id]);
            $this->json(["status" => "success", "message" => "Đã cập nhật trạng thái đánh giá."]);
        } catch (Exception $e) {
            $this->json(["status" => "error", "message" => $e->getMessage()]);
        }
    }

    public function apiDeleteReview()
    {
        try {
            $review_id = (int)($_POST["review_id"] ?? 0);
            if (!$review_id) {
                $this->json(["status" => "error", "message" => "ID không hợp lệ."]);
            }
            $stmt = $this->conn->prepare("DELETE FROM reviews WHERE review_id = ?");
            $stmt->execute([$review_id]);
            $this->json(["status" => "success", "message" => "Đã xoá đánh giá."]);
        } catch (Exception $e) {
            $this->json(["status" => "error", "message" => $e->getMessage()]);
        }
    }

    // --- FAQS API ---
    public function apiGetFaqs()
    {
        try {
            $stmt = $this->conn->query("SELECT * FROM faqs ORDER BY display_order ASC");
            $faqs = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $this->json([
                "status" => "success",
                "data" => $faqs
            ]);
        } catch (Exception $e) {
            $this->json([
                "status" => "error",
                "message" => $e->getMessage()
            ]);
        }
    }

    public function apiSaveFaq()
    {
        try {
            $faq_id = isset($_POST["faq_id"]) && $_POST["faq_id"] !== '' ? (int)$_POST["faq_id"] : null;
            $question = trim($_POST["question"] ?? "");
            $answer = trim($_POST["answer"] ?? "");
            $display_order = isset($_POST["display_order"]) ? (int)$_POST["display_order"] : 0;
            $status = isset($_POST["status"]) && ($_POST["status"] === "hide" || $_POST["status"] === "0" || $_POST["status"] === "false") ? "hide" : "show";

            if (empty($question) || empty($answer)) {
                $this->json(["status" => "error", "message" => "Vui lòng nhập đầy đủ câu hỏi và câu trả lời."]);
            }

            if ($faq_id) {
                $stmt = $this->conn->prepare("UPDATE faqs SET question = ?, answer = ?, display_order = ?, status = ? WHERE faq_id = ?");
                $stmt->execute([$question, $answer, $display_order, $status, $faq_id]);
                $this->json(["status" => "success", "message" => "Đã cập nhật câu hỏi FAQ thành công."]);
            } else {
                $stmt = $this->conn->prepare("INSERT INTO faqs (question, answer, display_order, status) VALUES (?, ?, ?, ?)");
                $stmt->execute([$question, $answer, $display_order, $status]);
                $this->json(["status" => "success", "message" => "Đã thêm câu hỏi FAQ thành công."]);
            }
        } catch (Exception $e) {
            $this->json(["status" => "error", "message" => $e->getMessage()]);
        }
    }

    public function apiDeleteFaq()
    {
        try {
            $faq_id = (int)($_POST["faq_id"] ?? 0);
            if (!$faq_id) {
                $this->json(["status" => "error", "message" => "ID không hợp lệ."]);
            }
            $stmt = $this->conn->prepare("DELETE FROM faqs WHERE faq_id = ?");
            $stmt->execute([$faq_id]);
            $this->json(["status" => "success", "message" => "Đã xoá câu hỏi."]);
        } catch (Exception $e) {
            $this->json(["status" => "error", "message" => $e->getMessage()]);
        }
    }

    public function apiReorderFaqs()
    {
        try {
            $orders = $_POST["orders"] ?? [];
            if (empty($orders)) {
                $input = json_decode(file_get_contents('php://input'), true);
                $orders = $input["orders"] ?? [];
            }
            $this->conn->beginTransaction();
            $stmt = $this->conn->prepare("UPDATE faqs SET display_order = ? WHERE faq_id = ?");
            foreach ($orders as $item) {
                $stmt->execute([$item['order'], $item['id']]);
            }
            $this->conn->commit();
            $this->json(["status" => "success", "message" => "Đã cập nhật thứ tự FAQ."]);
        } catch (Exception $e) {
            if ($this->conn->inTransaction()) {
                $this->conn->rollBack();
            }
            $this->json(["status" => "error", "message" => $e->getMessage()]);
        }
    }

    // --- CONTACT MESSAGES API ---
    public function apiGetMessages()
    {
        try {
            $stmt = $this->conn->query("SELECT * FROM contacts ORDER BY contact_id DESC");
            $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $this->json([
                "status" => "success",
                "data" => $messages
            ]);
        } catch (Exception $e) {
            $this->json([
                "status" => "error",
                "message" => $e->getMessage()
            ]);
        }
    }

    public function apiReadMessage()
    {
        try {
            $contact_id = (int)($_POST["contact_id"] ?? 0);
            if (!$contact_id) {
                $this->json(["status" => "error", "message" => "ID không hợp lệ."]);
            }
            $stmt = $this->conn->prepare("UPDATE contacts SET is_read = 1 WHERE contact_id = ?");
            $stmt->execute([$contact_id]);
            $this->json(["status" => "success", "message" => "Đã đánh dấu đã đọc."]);
        } catch (Exception $e) {
            $this->json(["status" => "error", "message" => $e->getMessage()]);
        }
    }

    public function apiMarkAllRead()
    {
        try {
            $this->conn->query("UPDATE contacts SET is_read = 1");
            $this->json(["status" => "success", "message" => "Đã đánh dấu tất cả đã đọc."]);
        } catch (Exception $e) {
            $this->json(["status" => "error", "message" => $e->getMessage()]);
        }
    }

    public function apiDeleteMessage()
    {
        try {
            $contact_id = (int)($_POST["contact_id"] ?? 0);
            if (!$contact_id) {
                $this->json(["status" => "error", "message" => "ID không hợp lệ."]);
            }
            $stmt = $this->conn->prepare("DELETE FROM contacts WHERE contact_id = ?");
            $stmt->execute([$contact_id]);
            $this->json(["status" => "success", "message" => "Đã xoá tin nhắn."]);
        } catch (Exception $e) {
            $this->json(["status" => "error", "message" => $e->getMessage()]);
        }
    }
}


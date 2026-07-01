<?php
class AdminController
{
    private function requireAdmin()
    {
        if (
            empty($_SESSION["user_logged_in"]) ||
            ($_SESSION["user_role"] ?? "") !== "admin"
        ) {
            $_SESSION["error_message"] = "Ban can dang nhap bang tai khoan admin.";
            header("Location: /index.php?page=login");
            exit();
        }
    }

    public function dashboard()
    {
        $this->requireAdmin();

        require_once BASE_PATH . "/app/models/DashboardModel.php";
        $dashboardModel = new DashboardModel();

        $cardStats = $dashboardModel->getCardStats();
        $recentOrders = $dashboardModel->getRecentOrders(5);
        $lowStockProducts = $dashboardModel->getLowStockProducts(5, 5);
        $salesTrend = $dashboardModel->getMonthlySalesTrend(6);

        require_once BASE_PATH . "/app/views/admin/dashboard.php";
    }

    public function categories()
    {
        $this->requireAdmin();

        require_once BASE_PATH . "/app/models/CategoryModel.php";
        $categoryModel = new CategoryModel();
        $categories = $categoryModel->getAll();
        $editingCategory = null;

        if (!empty($_GET["edit_id"])) {
            $editingCategory = $categoryModel->findById((int) $_GET["edit_id"]);
        }

        require_once BASE_PATH . "/app/views/admin/categories.php";
    }

    public function storeCategory()
    {
        $this->requireAdmin();

        $categoryName = trim($_POST["category_name"] ?? "");
        $description = trim($_POST["description"] ?? "");
        $status = $_POST["status"] ?? "show";

        if ($categoryName === "") {
            $_SESSION["error_message"] = "Vui long nhap ten danh muc.";
            header("Location: /index.php?page=admin_categories");
            exit();
        }

        if (!in_array($status, ["show", "hide"], true)) {
            $status = "show";
        }

        require_once BASE_PATH . "/app/models/CategoryModel.php";
        $categoryModel = new CategoryModel();

        if ($categoryModel->existsByName($categoryName)) {
            $_SESSION["error_message"] = "Ten danh muc da ton tai.";
            header("Location: /index.php?page=admin_categories");
            exit();
        }

        $categoryModel->create($categoryName, $description, $status);
        $_SESSION["success_message"] = "Them danh muc thanh cong.";
        header("Location: /index.php?page=admin_categories");
        exit();
    }

    public function updateCategory()
    {
        $this->requireAdmin();

        $categoryId = (int) ($_POST["category_id"] ?? 0);
        $categoryName = trim($_POST["category_name"] ?? "");
        $description = trim($_POST["description"] ?? "");
        $status = $_POST["status"] ?? "show";

        if ($categoryId <= 0 || $categoryName === "") {
            $_SESSION["error_message"] = "Du lieu danh muc khong hop le.";
            header("Location: /index.php?page=admin_categories");
            exit();
        }

        if (!in_array($status, ["show", "hide"], true)) {
            $status = "show";
        }

        require_once BASE_PATH . "/app/models/CategoryModel.php";
        $categoryModel = new CategoryModel();

        if ($categoryModel->existsByName($categoryName, $categoryId)) {
            $_SESSION["error_message"] = "Ten danh muc da ton tai.";
            header("Location: /index.php?page=admin_categories&edit_id=" . $categoryId);
            exit();
        }

        $categoryModel->update($categoryId, $categoryName, $description, $status);
        $_SESSION["success_message"] = "Cap nhat danh muc thanh cong.";
        header("Location: /index.php?page=admin_categories");
        exit();
    }

    public function toggleCategoryStatus()
    {
        $this->requireAdmin();

        $categoryId = (int) ($_POST["category_id"] ?? 0);
        $status = $_POST["status"] ?? "show";

        if ($categoryId <= 0 || !in_array($status, ["show", "hide"], true)) {
            $_SESSION["error_message"] = "Khong the cap nhat trang thai danh muc.";
            header("Location: /index.php?page=admin_categories");
            exit();
        }

        require_once BASE_PATH . "/app/models/CategoryModel.php";
        $categoryModel = new CategoryModel();
        $categoryModel->updateStatus($categoryId, $status);

        $_SESSION["success_message"] = "Cap nhat trang thai danh muc thanh cong.";
        header("Location: /index.php?page=admin_categories");
        exit();
    }

    public function products()
    {
        $this->requireAdmin();

        require_once BASE_PATH . "/app/models/ProductModel.php";
        require_once BASE_PATH . "/app/models/CategoryModel.php";

        $productModel = new ProductModel();
        $categoryModel = new CategoryModel();

        $filters = [
            "keyword" => trim($_GET["keyword"] ?? ""),
            "category_id" => (int) ($_GET["category_id"] ?? 0),
            "sort" => $_GET["sort"] ?? "newest",
        ];

        $products = $productModel->getAll($filters);
        $categories = $categoryModel->getAll();

        require_once BASE_PATH . "/app/views/admin/productsmanage.php";
    }

    public function createProduct()
    {
        $this->requireAdmin();

        require_once BASE_PATH . "/app/models/CategoryModel.php";
        $categoryModel = new CategoryModel();
        $categories = $categoryModel->getAll();

        require_once BASE_PATH . "/app/views/admin/add-products.php";
    }

    public function storeProduct()
    {
        $this->requireAdmin();

        $categoryId = (int) ($_POST["category_id"] ?? 0);
        $productName = trim($_POST["product_name"] ?? "");
        $sku = strtoupper(trim($_POST["sku"] ?? ""));
        $description = trim($_POST["description"] ?? "");
        $price = (int) ($_POST["price"] ?? 0);
        $salePrice = (int) ($_POST["sale_price"] ?? 0);
        $stockQuantity = (int) ($_POST["stock_quantity"] ?? 0);
        $material = trim($_POST["material"] ?? "");

        if ($categoryId <= 0 || $productName === "" || $sku === "" || $price < 0 || $stockQuantity < 0) {
            $_SESSION["error_message"] = "Vui long nhap day du thong tin san pham hop le.";
            header("Location: /index.php?page=admin_product_create");
            exit();
        }

        $mainImage = $this->uploadProductImage();
        if ($mainImage === null) {
            $_SESSION["error_message"] = "Vui long chon anh san pham dinh dang JPG, PNG hoac WEBP.";
            header("Location: /index.php?page=admin_product_create");
            exit();
        }

        require_once BASE_PATH . "/app/models/ProductModel.php";
        $productModel = new ProductModel();

        if ($productModel->skuExists($sku)) {
            $_SESSION["error_message"] = "Ma SKU da ton tai.";
            header("Location: /index.php?page=admin_product_create");
            exit();
        }

        $productId = $productModel->create([
            "category_id" => $categoryId,
            "product_name" => $productName,
            "sku" => $sku,
            "description" => $description,
            "price" => $price,
            "sale_price" => $salePrice,
            "stock_quantity" => $stockQuantity,
            "main_image" => $mainImage,
            "status" => "show",
        ]);

        $productModel->addImage($productId, $mainImage);
        $productModel->addAttribute($productId, "material", $material);

        $_SESSION["success_message"] = "Them san pham thanh cong.";
        header("Location: /index.php?page=admin_products");
        exit();
    }

    public function editProduct()
    {
        $this->requireAdmin();

        $productId = (int) ($_GET["id"] ?? 0);

        require_once BASE_PATH . "/app/models/ProductModel.php";
        require_once BASE_PATH . "/app/models/CategoryModel.php";

        $productModel = new ProductModel();
        $categoryModel = new CategoryModel();

        $product = $productModel->findById($productId);
        if (!$product) {
            $_SESSION["error_message"] = "Khong tim thay san pham.";
            header("Location: /index.php?page=admin_products");
            exit();
        }

        $material = $productModel->getAttributeValue($productId, "material");
        $categories = $categoryModel->getAll();

        require_once BASE_PATH . "/app/views/admin/edit-product.php";
    }

    public function updateProduct()
    {
        $this->requireAdmin();

        $productId = (int) ($_POST["product_id"] ?? 0);
        $categoryId = (int) ($_POST["category_id"] ?? 0);
        $productName = trim($_POST["product_name"] ?? "");
        $sku = strtoupper(trim($_POST["sku"] ?? ""));
        $description = trim($_POST["description"] ?? "");
        $price = (int) ($_POST["price"] ?? 0);
        $salePrice = (int) ($_POST["sale_price"] ?? 0);
        $stockQuantity = (int) ($_POST["stock_quantity"] ?? 0);
        $material = trim($_POST["material"] ?? "");
        $status = $_POST["status"] ?? "show";

        if (
            $productId <= 0 ||
            $categoryId <= 0 ||
            $productName === "" ||
            $sku === "" ||
            $price < 0 ||
            $salePrice < 0 ||
            $stockQuantity < 0 ||
            !in_array($status, ["show", "hide"], true)
        ) {
            $_SESSION["error_message"] = "Du lieu san pham khong hop le.";
            header("Location: /index.php?page=admin_product_edit&id=" . $productId);
            exit();
        }

        require_once BASE_PATH . "/app/models/ProductModel.php";
        $productModel = new ProductModel();
        $product = $productModel->findById($productId);

        if (!$product) {
            $_SESSION["error_message"] = "Khong tim thay san pham.";
            header("Location: /index.php?page=admin_products");
            exit();
        }

        if ($productModel->skuExists($sku, $productId)) {
            $_SESSION["error_message"] = "Ma SKU da ton tai.";
            header("Location: /index.php?page=admin_product_edit&id=" . $productId);
            exit();
        }

        $mainImage = $this->uploadProductImage(false);
        if ($mainImage === null) {
            $mainImage = $product["main_image"];
        }

        $productModel->update($productId, [
            "category_id" => $categoryId,
            "product_name" => $productName,
            "sku" => $sku,
            "description" => $description,
            "price" => $price,
            "sale_price" => $salePrice,
            "stock_quantity" => $stockQuantity,
            "main_image" => $mainImage,
            "status" => $status,
        ]);

        if ($mainImage !== $product["main_image"]) {
            $productModel->addImage($productId, $mainImage);
        }

        $productModel->replaceAttribute($productId, "material", $material);

        $_SESSION["success_message"] = "Cap nhat san pham thanh cong.";
        header("Location: /index.php?page=admin_products");
        exit();
    }

    public function toggleProductStatus()
    {
        $this->requireAdmin();

        $productId = (int) ($_POST["product_id"] ?? 0);
        $status = $_POST["status"] ?? "show";

        if ($productId <= 0 || !in_array($status, ["show", "hide"], true)) {
            $_SESSION["error_message"] = "Khong the cap nhat trang thai san pham.";
            header("Location: /index.php?page=admin_products");
            exit();
        }

        require_once BASE_PATH . "/app/models/ProductModel.php";
        $productModel = new ProductModel();
        $productModel->updateStatus($productId, $status);

        $_SESSION["success_message"] = "Cap nhat trang thai san pham thanh cong.";
        header("Location: /index.php?page=admin_products");
        exit();
    }

    public function orders()
    {
        $this->requireAdmin();

        require_once BASE_PATH . "/app/models/OrderModel.php";
        $orderModel = new OrderModel();

        $filters = [
            "keyword" => trim($_GET["keyword"] ?? ""),
            "status" => $_GET["status"] ?? "all",
        ];

        $orders = $orderModel->getAll($filters);

        require_once BASE_PATH . "/app/views/admin/ordersmanage.php";
    }

    public function orderDetail()
    {
        $this->requireAdmin();

        $orderId = (int)($_GET["order_id"] ?? 0);
        if ($orderId <= 0) {
            $_SESSION["error_message"] = "Don hang khong hop le.";
            header("Location: /index.php?page=admin_orders");
            exit();
        }

        require_once BASE_PATH . "/app/models/OrderModel.php";
        $orderModel = new OrderModel();

        $order = $orderModel->findById($orderId);
        if (!$order) {
            $_SESSION["error_message"] = "Khong tim thay don hang.";
            header("Location: /index.php?page=admin_orders");
            exit();
        }

        $orderDetails = $orderModel->getOrderDetails($orderId);

        require_once BASE_PATH . "/app/views/admin/orderdetail.php";
    }

    public function updateOrderStatus()
    {
        $this->requireAdmin();

        $orderId = (int)($_POST["order_id"] ?? 0);
        $status = $_POST["status"] ?? "";

        $validStatuses = ["pending", "processing", "shipping", "delivered", "cancelled"];
        $redirectUrl = "/index.php?page=admin_orders";
        if (isset($_POST["redirect"]) && $_POST["redirect"] === "detail") {
            $redirectUrl = "/index.php?page=admin_order_detail&order_id=" . $orderId;
        }

        if ($orderId <= 0 || !in_array($status, $validStatuses, true)) {
            $_SESSION["error_message"] = "Trang thai don hang khong hop le.";
            header("Location: /index.php?page=admin_orders");
            exit();
        }

        require_once BASE_PATH . "/app/models/OrderModel.php";
        $orderModel = new OrderModel();

        $order = $orderModel->findById($orderId);
        if (!$order) {
            $_SESSION["error_message"] = "Khong tim thay don hang.";
            header("Location: /index.php?page=admin_orders");
            exit();
        }

        if ($order["status"] === "cancelled" && $status !== "cancelled") {
            $_SESSION["error_message"] = "Don hang da huy khong nen mo lai truc tiep, tranh sai so luong ton kho.";
            header("Location: " . $redirectUrl);
            exit();
        }

        if ($order["status"] !== "cancelled" && $status === "cancelled") {
            $orderModel->restoreStock($orderId);
        }

        if (!$orderModel->updateStatus($orderId, $status)) {
            $_SESSION["error_message"] = "Khong the cap nhat trang thai don hang.";
            header("Location: " . $redirectUrl);
            exit();
        }

        $_SESSION["success_message"] = "Cap nhat trang thai don hang thanh cong.";
        header("Location: " . $redirectUrl);
        exit();
    }
    private function uploadProductImage($required = true)
    {
        if (empty($_FILES["main_image"]) || $_FILES["main_image"]["error"] !== UPLOAD_ERR_OK) {
            if (!$required && ($_FILES["main_image"]["error"] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
                return null;
            }
            return null;
        }

        $allowedExtensions = ["jpg", "jpeg", "png", "webp"];
        $originalName = $_FILES["main_image"]["name"];
        $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));

        if (!in_array($extension, $allowedExtensions, true)) {
            return null;
        }

        $uploadDir = BASE_PATH . "/public/uploads/products";
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0775, true);
        }

        $fileName = uniqid("product_", true) . "." . $extension;
        $targetPath = $uploadDir . "/" . $fileName;

        if (!move_uploaded_file($_FILES["main_image"]["tmp_name"], $targetPath)) {
            return null;
        }

        return "/uploads/products/" . $fileName;
    }
}
?>

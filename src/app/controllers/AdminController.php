<?php
class AdminController
{
    private function requireAdmin()
    {
        if (
            empty($_SESSION["user_logged_in"]) ||
            ($_SESSION["user_role"] ?? "") !== "admin"
        ) {
            $_SESSION["error_message"] = "Bạn cần đăng nhập bằng tài khoản admin.";
            header("Location: /index.php?page=login");
            exit();
        }
    }

    public function dashboard()
    {
        $this->requireAdmin();
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
            $_SESSION["error_message"] = "Vui lòng nhập tên danh mục.";
            header("Location: /index.php?page=admin_categories");
            exit();
        }

        if (!in_array($status, ["show", "hide"], true)) {
            $status = "show";
        }

        require_once BASE_PATH . "/app/models/CategoryModel.php";
        $categoryModel = new CategoryModel();

        if ($categoryModel->existsByName($categoryName)) {
            $_SESSION["error_message"] = "Tên danh mục đã tồn tại.";
            header("Location: /index.php?page=admin_categories");
            exit();
        }

        $categoryModel->create($categoryName, $description, $status);
        $_SESSION["success_message"] = "Thêm danh mục thành công.";
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
            $_SESSION["error_message"] = "Dữ liệu danh mục không hợp lệ.";
            header("Location: /index.php?page=admin_categories");
            exit();
        }

        if (!in_array($status, ["show", "hide"], true)) {
            $status = "show";
        }

        require_once BASE_PATH . "/app/models/CategoryModel.php";
        $categoryModel = new CategoryModel();

        if ($categoryModel->existsByName($categoryName, $categoryId)) {
            $_SESSION["error_message"] = "Tên danh mục đã tồn tại.";
            header("Location: /index.php?page=admin_categories&edit_id=" . $categoryId);
            exit();
        }

        $categoryModel->update($categoryId, $categoryName, $description, $status);
        $_SESSION["success_message"] = "Cập nhật danh mục thành công.";
        header("Location: /index.php?page=admin_categories");
        exit();
    }

    public function toggleCategoryStatus()
    {
        $this->requireAdmin();

        $categoryId = (int) ($_POST["category_id"] ?? 0);
        $status = $_POST["status"] ?? "show";

        if ($categoryId <= 0 || !in_array($status, ["show", "hide"], true)) {
            $_SESSION["error_message"] = "Không thể cập nhật trạng thái danh mục.";
            header("Location: /index.php?page=admin_categories");
            exit();
        }

        require_once BASE_PATH . "/app/models/CategoryModel.php";
        $categoryModel = new CategoryModel();
        $categoryModel->updateStatus($categoryId, $status);

        $_SESSION["success_message"] = "Cập nhật trạng thái danh mục thành công.";
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
            $_SESSION["error_message"] = "Vui lòng nhập đầy đủ thông tin sản phẩm hợp lệ.";
            header("Location: /index.php?page=admin_product_create");
            exit();
        }

        $mainImage = $this->uploadProductImage();
        if ($mainImage === null) {
            $_SESSION["error_message"] = "Vui lòng chọn ảnh sản phẩm định dạng JPG, PNG hoặc WEBP.";
            header("Location: /index.php?page=admin_product_create");
            exit();
        }

        require_once BASE_PATH . "/app/models/ProductModel.php";
        $productModel = new ProductModel();

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

        $_SESSION["success_message"] = "Thêm sản phẩm thành công.";
        header("Location: /index.php?page=admin_products");
        exit();
    }

    public function toggleProductStatus()
    {
        $this->requireAdmin();

        $productId = (int) ($_POST["product_id"] ?? 0);
        $status = $_POST["status"] ?? "show";

        if ($productId <= 0 || !in_array($status, ["show", "hide"], true)) {
            $_SESSION["error_message"] = "Không thể cập nhật trạng thái sản phẩm.";
            header("Location: /index.php?page=admin_products");
            exit();
        }

        require_once BASE_PATH . "/app/models/ProductModel.php";
        $productModel = new ProductModel();
        $productModel->updateStatus($productId, $status);

        $_SESSION["success_message"] = "Cập nhật trạng thái sản phẩm thành công.";
        header("Location: /index.php?page=admin_products");
        exit();
    }

    private function uploadProductImage()
    {
        if (empty($_FILES["main_image"]) || $_FILES["main_image"]["error"] !== UPLOAD_ERR_OK) {
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

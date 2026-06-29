<?php
class CartController
{
    private $conn;

    public function __construct()
    {
        require_once BASE_PATH . "/config/database.php";
        $db = new Database();
        $this->conn = $db->getConnection();
    }

    // Kiểm tra đăng nhập, trả JSON nếu chưa
    private function requireLogin()
    {
        if (
            !isset($_SESSION["user_logged_in"]) ||
            $_SESSION["user_logged_in"] !== true
        ) {
            $this->json(["status" => "guest"]);
            exit();
        }
    }

    private function json($data)
    {
        header("Content-Type: application/json");
        echo json_encode($data);
        exit();
    }

    // GET: hiển thị trang giỏ hàng
    public function showGioHang()
    {
        require_once BASE_PATH . "/app/views/user/shopping_cart.php";
    }

    // AJAX: thêm sản phẩm
    public function add()
    {
        $this->requireLogin();
        $user_id = $_SESSION["user_id"];
        $product_id = (int) ($_POST["product_id"] ?? 0);
        $quantity = (int) ($_POST["quantity"] ?? 1);
        $size = $_POST["selected_size"] ?? null;
        $color = $_POST["selected_color"] ?? null;
        $material = $_POST["selected_material"] ?? null;

        if (!$product_id) {
            $this->json(["status" => "error", "message" => "Thiếu product_id"]);
        }

        $stmt = $this->conn->prepare(
            "SELECT cart_id, quantity FROM cart_items
             WHERE user_id = :uid AND product_id = :pid",
        );
        $stmt->execute([":uid" => $user_id, ":pid" => $product_id]);
        $existing = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($existing) {
            $stmt = $this->conn->prepare(
                "UPDATE cart_items SET quantity = quantity + :qty, updated_at = NOW()
                 WHERE cart_id = :cid",
            );
            $stmt->execute([
                ":qty" => $quantity,
                ":cid" => $existing["cart_id"],
            ]);
        } else {
            $stmt = $this->conn->prepare(
                "INSERT INTO cart_items (user_id, product_id, quantity, selected_size, selected_color, selected_material)
                 VALUES (:uid, :pid, :qty, :size, :color, :material)",
            );
            $stmt->execute([
                ":uid" => $user_id,
                ":pid" => $product_id,
                ":qty" => $quantity,
                ":size" => $size,
                ":color" => $color,
                ":material" => $material,
            ]);
        }
        $this->json(["status" => "ok"]);
    }

    // AJAX: cập nhật số lượng
    public function update()
    {
        $this->requireLogin();
        $user_id = $_SESSION["user_id"];
        $product_id = (int) ($_POST["product_id"] ?? 0);
        $quantity = (int) ($_POST["quantity"] ?? 0);

        if ($quantity <= 0) {
            $stmt = $this->conn->prepare(
                "DELETE FROM cart_items WHERE user_id = :uid AND product_id = :pid",
            );
            $stmt->execute([":uid" => $user_id, ":pid" => $product_id]);
        } else {
            $stmt = $this->conn->prepare(
                "UPDATE cart_items SET quantity = :qty, updated_at = NOW()
                 WHERE user_id = :uid AND product_id = :pid",
            );
            $stmt->execute([
                ":qty" => $quantity,
                ":uid" => $user_id,
                ":pid" => $product_id,
            ]);
        }
        $this->json(["status" => "ok"]);
    }

    // AJAX: xóa 1 sản phẩm
    public function remove()
    {
        $this->requireLogin();
        $user_id = $_SESSION["user_id"];
        $product_id = (int) ($_POST["product_id"] ?? 0);

        $stmt = $this->conn->prepare(
            "DELETE FROM cart_items WHERE user_id = :uid AND product_id = :pid",
        );
        $stmt->execute([":uid" => $user_id, ":pid" => $product_id]);
        $this->json(["status" => "ok"]);
    }

    // AJAX: xóa toàn bộ
    public function clear()
    {
        $this->requireLogin();
        $user_id = $_SESSION["user_id"];

        $stmt = $this->conn->prepare(
            "DELETE FROM cart_items WHERE user_id = :uid",
        );
        $stmt->execute([":uid" => $user_id]);
        $this->json(["status" => "ok"]);
    }

    // AJAX: lấy giỏ hàng từ DB
    public function get()
    {
        $this->requireLogin();
        $user_id = $_SESSION["user_id"];

        $stmt = $this->conn->prepare(
            "SELECT ci.product_id as id, p.product_name as name,
                    CASE WHEN p.sale_price > 0 THEN p.sale_price ELSE p.price END as price,
                    ci.quantity, p.main_image as image,
                    ci.selected_size, ci.selected_color, ci.selected_material
             FROM cart_items ci
             JOIN products p ON ci.product_id = p.product_id
             WHERE ci.user_id = :uid",
        );
        $stmt->execute([":uid" => $user_id]);
        $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $this->json(["status" => "ok", "items" => $items]);
    }
    // AJAX: kiểm tra voucher
    public function checkVoucher()
    {
        $code = strtoupper(trim($_POST["code"] ?? ""));
        $subtotal = (int) ($_POST["subtotal"] ?? 0);

        if (!$code) {
            $this->json([
                "status" => "error",
                "message" => "Vui lòng nhập mã giảm giá.",
            ]);
        }

        $stmt = $this->conn->prepare(
            "SELECT * FROM vouchers
                 WHERE voucher_code = :code
                   AND status = 'active'
                   AND used_count < usage_limit
                   AND NOW() BETWEEN start_date AND end_date",
        );
        $stmt->execute([":code" => $code]);
        $voucher = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$voucher) {
            $this->json([
                "status" => "error",
                "message" => "Mã không tồn tại hoặc đã hết hạn.",
            ]);
        }

        if ($subtotal < $voucher["min_order_value"]) {
            $this->json([
                "status" => "error",
                "message" =>
                    "Đơn hàng cần tối thiểu " .
                    number_format($voucher["min_order_value"], 0, ",", ".") .
                    "₫ để dùng mã này.",
            ]);
        }

        $discount = 0;
        if ($voucher["discount_type"] === "percent") {
            $discount = ($subtotal * $voucher["discount_value"]) / 100;
            if ($voucher["max_discount_amount"]) {
                $discount = min($discount, $voucher["max_discount_amount"]);
            }
        } else {
            $discount = $voucher["discount_value"];
        }

        $this->json(["status" => "ok", "discount" => (int) $discount]);
    }
}

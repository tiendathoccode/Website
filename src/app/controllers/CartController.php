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

    // POST: Xử lý đặt hàng thực tế
    public function placeOrder()
    {
        $this->requireLogin();
        $user_id = $_SESSION["user_id"];

        // 1. Nhận dữ liệu
        $fullName = trim($_POST["fullName"] ?? "");
        $phone = trim($_POST["phone"] ?? "");
        $province = $_POST["province"] ?? "";
        $district = $_POST["district"] ?? "";
        $ward = $_POST["ward"] ?? "";
        $addressDetail = trim($_POST["addressDetail"] ?? "");
        $paymentMethod = $_POST["paymentMethod"] ?? "cod";
        $voucherCode = strtoupper(trim($_POST["voucher_code"] ?? ""));

        // Map payment method sang COD hoặc Bank Transfer
        if ($paymentMethod === "ewallet") {
            $paymentMethod = "bank_transfer";
        } elseif ($paymentMethod !== "bank_transfer") {
            $paymentMethod = "cod";
        }

        if (empty($fullName) || empty($phone) || empty($province) || empty($district) || empty($ward) || empty($addressDetail)) {
            $this->json(["status" => "error", "message" => "Vui lòng nhập đầy đủ thông tin giao hàng."]);
        }

        // Map sơ bộ tỉnh thành cho gọn
        $provincesMap = [
            "hcm" => "TP. Hồ Chí Minh",
            "hn" => "Hà Nội",
            "dn" => "Đà Nẵng",
            "ct" => "Cần Thơ",
            "hp" => "Hải Phòng"
        ];
        $provinceName = $provincesMap[$province] ?? $province;
        $shippingAddress = "$addressDetail, $ward, $district, $provinceName";

        // 2. Lấy giỏ hàng (hỗ trợ mua trực tiếp qua JSON gửi từ client hoặc lấy từ database)
        $cartItemsJson = $_POST["cart_items"] ?? "";
        $isDirectCheckout = false;
        $cartItems = [];

        if (!empty($cartItemsJson)) {
            $decodedItems = json_decode($cartItemsJson, true);
            if (is_array($decodedItems) && !empty($decodedItems)) {
                $isDirectCheckout = true;
                foreach ($decodedItems as $dItem) {
                    $prodId = (int)($dItem["id"] ?? $dItem["product_id"] ?? 0);
                    $qty = (int)($dItem["quantity"] ?? 1);
                    $metal = $dItem["metal"] ?? $dItem["selected_material"] ?? "";

                    if ($prodId > 0) {
                        $stmt = $this->conn->prepare("SELECT price, sale_price FROM products WHERE product_id = :pid");
                        $stmt->execute([":pid" => $prodId]);
                        $pInfo = $stmt->fetch(PDO::FETCH_ASSOC);
                        if ($pInfo) {
                            $cartItems[] = [
                                "product_id" => $prodId,
                                "quantity" => $qty,
                                "price" => $pInfo["price"],
                                "sale_price" => $pInfo["sale_price"],
                                "selected_size" => $dItem["size"] ?? $dItem["selected_size"] ?? null,
                                "selected_color" => $dItem["color"] ?? $dItem["selected_color"] ?? null,
                                "selected_material" => $metal
                            ];
                        }
                    }
                }
            }
        }

        if (empty($cartItems)) {
            $stmt = $this->conn->prepare(
                "SELECT ci.*, p.price, p.sale_price 
                 FROM cart_items ci
                 JOIN products p ON ci.product_id = p.product_id
                 WHERE ci.user_id = :uid"
            );
            $stmt->execute([":uid" => $user_id]);
            $cartItems = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        if (empty($cartItems)) {
            $this->json(["status" => "error", "message" => "Giỏ hàng trống."]);
        }

        // 3. Tính toán tổng tiền
        $totalAmount = 0;
        foreach ($cartItems as $item) {
            $price = ($item["sale_price"] > 0) ? $item["sale_price"] : $item["price"];
            $totalAmount += $price * $item["quantity"];
        }

        // 4. Xử lý Voucher
        $discountAmount = 0;
        $voucherId = null;
        if (!empty($voucherCode)) {
            $stmt = $this->conn->prepare(
                "SELECT * FROM vouchers 
                 WHERE voucher_code = :code 
                   AND status = 'active'
                   AND used_count < usage_limit
                   AND NOW() BETWEEN start_date AND end_date"
            );
            $stmt->execute([":code" => $voucherCode]);
            $voucher = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($voucher && $totalAmount >= $voucher["min_order_value"]) {
                $voucherId = $voucher["voucher_id"];
                if ($voucher["discount_type"] === "percent") {
                    $discountAmount = ($totalAmount * $voucher["discount_value"]) / 100;
                    if ($voucher["max_discount_amount"]) {
                        $discountAmount = min($discountAmount, $voucher["max_discount_amount"]);
                    }
                } else {
                    $discountAmount = $voucher["discount_value"];
                }
                $discountAmount = (int)$discountAmount;
            }
        }

        $finalAmount = max($totalAmount - $discountAmount, 0);
        $orderCode = "ORD-" . date("Ymd") . strtoupper(bin2hex(random_bytes(3)));

        // 5. Thực hiện Transaction tạo Đơn hàng
        try {
            $this->conn->beginTransaction();

            // Tạo order
            $stmt = $this->conn->prepare(
                "INSERT INTO orders (user_id, order_code, receiver_name, receiver_phone, shipping_address, total_amount, discount_amount, final_amount, payment_method, status) 
                 VALUES (:uid, :code, :rname, :rphone, :addr, :total, :disc, :final, :pmethod, 'pending')"
            );
            $stmt->execute([
                ":uid" => $user_id,
                ":code" => $orderCode,
                ":rname" => $fullName,
                ":rphone" => $phone,
                ":addr" => $shippingAddress,
                ":total" => $totalAmount,
                ":disc" => $discountAmount,
                ":final" => $finalAmount,
                ":pmethod" => $paymentMethod
            ]);
            $orderId = $this->conn->lastInsertId();

            // Tạo order details
            $stmtDetail = $this->conn->prepare(
                "INSERT INTO order_details (order_id, product_id, quantity, price, selected_size, selected_color, selected_material) 
                 VALUES (:oid, :pid, :qty, :price, :size, :color, :material)"
            );
            foreach ($cartItems as $item) {
                $price = ($item["sale_price"] > 0) ? $item["sale_price"] : $item["price"];
                $stmtDetail->execute([
                    ":oid" => $orderId,
                    ":pid" => $item["product_id"],
                    ":qty" => $item["quantity"],
                    ":price" => $price,
                    ":size" => $item["selected_size"],
                    ":color" => $item["selected_color"],
                    ":material" => $item["selected_material"]
                ]);
            }

            // Cập nhật lượt dùng voucher
            if ($voucherId) {
                $stmtVoucher = $this->conn->prepare(
                    "UPDATE vouchers SET used_count = used_count + 1 WHERE voucher_id = :vid"
                );
                $stmtVoucher->execute([":vid" => $voucherId]);
            }

            // Xóa giỏ hàng chỉ khi mua qua giỏ hàng (không phải mua trực tiếp) và thanh toán COD
            if (!$isDirectCheckout && $paymentMethod === "cod") {
                $stmtClear = $this->conn->prepare("DELETE FROM cart_items WHERE user_id = :uid");
                $stmtClear->execute([":uid" => $user_id]);
            }

            $this->conn->commit();

            $this->json([
                "status" => "ok",
                "order_id" => $orderId,
                "order_code" => $orderCode,
                "final_amount" => $finalAmount
            ]);
        } catch (Exception $e) {
            $this->conn->rollBack();
            $this->json(["status" => "error", "message" => "Lỗi hệ thống: " . $e->getMessage()]);
        }
    }

    public function orderDetails()
    {
        $this->requireLogin();
        $user_id = $_SESSION["user_id"];
        $order_id = isset($_GET["order_id"]) ? (int)$_GET["order_id"] : 0;

        if ($order_id <= 0) {
            $this->json(["status" => "error", "message" => "Mã đơn hàng không hợp lệ."]);
        }

        try {
            $stmt = $this->conn->prepare("SELECT * FROM orders WHERE order_id = :oid AND user_id = :uid");
            $stmt->execute([":oid" => $order_id, ":uid" => $user_id]);
            $order = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$order) {
                $this->json(["status" => "error", "message" => "Đơn hàng không tồn tại."]);
            }

            $stmtDetails = $this->conn->prepare(
                "SELECT od.*, p.product_name, p.main_image 
                 FROM order_details od
                 JOIN products p ON od.product_id = p.product_id
                 WHERE od.order_id = :oid"
            );
            $stmtDetails->execute([":oid" => $order_id]);
            $items = $stmtDetails->fetchAll(PDO::FETCH_ASSOC);

            $this->json([
                "status" => "ok",
                "order" => $order,
                "items" => $items
            ]);
        } catch (Exception $e) {
            $this->json(["status" => "error", "message" => "Lỗi hệ thống: " . $e->getMessage()]);
        }
    }

    public function orderAction()
    {
        $this->requireLogin();
        $user_id = $_SESSION["user_id"];
        $order_id = isset($_POST["order_id"]) ? (int)$_POST["order_id"] : 0;
        $action = isset($_POST["action"]) ? $_POST["action"] : "";

        if ($order_id <= 0 || !in_array($action, ["cancel", "return"])) {
            $this->json(["status" => "error", "message" => "Thao tác không hợp lệ."]);
        }

        try {
            $stmt = $this->conn->prepare("SELECT * FROM orders WHERE order_id = :oid AND user_id = :uid");
            $stmt->execute([":oid" => $order_id, ":uid" => $user_id]);
            $order = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$order) {
                $this->json(["status" => "error", "message" => "Đơn hàng không tồn tại."]);
            }

            if ($action === "cancel") {
                if (!in_array($order["status"], ["pending", "processing"])) {
                    $this->json(["status" => "error", "message" => "Đơn hàng hiện tại không thể hủy."]);
                }

                // Khôi phục tồn kho sản phẩm
                $this->restoreOrderStock($order_id);

                $stmtUpdate = $this->conn->prepare("UPDATE orders SET status = 'cancelled' WHERE order_id = :oid");
                $stmtUpdate->execute([":oid" => $order_id]);

                $this->json(["status" => "ok", "message" => "Hủy đơn hàng thành công!"]);
            } elseif ($action === "return") {
                if ($order["status"] !== "delivered") {
                    $this->json(["status" => "error", "message" => "Chỉ đơn hàng đã giao mới có thể hoàn trả."]);
                }

                $stmtUpdate = $this->conn->prepare("UPDATE orders SET status = 'return_requested' WHERE order_id = :oid");
                $stmtUpdate->execute([":oid" => $order_id]);

                $this->json(["status" => "ok", "message" => "Yêu cầu hoàn trả đơn hàng thành công! Đang chờ shipper thu hồi."]);
            }
        } catch (Exception $e) {
            $this->json(["status" => "error", "message" => "Lỗi hệ thống: " . $e->getMessage()]);
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
}

<?php
require_once BASE_PATH . "/config/database.php";

class OrderModel
{
    private $conn;
    private $table = "orders";

    public function __construct()
    {
        $db = new Database();
        $this->conn = $db->getConnection();
    }

    public function getAll($filters = [])
    {
        $query = "SELECT o.*, u.full_name, u.email 
                  FROM {$this->table} o
                  LEFT JOIN users u ON o.user_id = u.user_id
                  WHERE 1 = 1";
        
        $params = [];

        if (!empty($filters["keyword"])) {
            $query .= " AND (o.order_code LIKE :keyword OR u.full_name LIKE :keyword OR o.receiver_name LIKE :keyword)";
            $params[":keyword"] = "%" . $filters["keyword"] . "%";
        }

        if (!empty($filters["status"]) && $filters["status"] !== "all") {
            $query .= " AND o.status = :status";
            $params[":status"] = $filters["status"];
        }

        $query .= " ORDER BY o.order_id DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->execute($params);

        return $stmt->fetchAll();
    }

    public function findById($orderId)
    {
        $query = "SELECT o.*, u.full_name, u.email, u.phone AS user_phone 
                  FROM {$this->table} o
                  LEFT JOIN users u ON o.user_id = u.user_id
                  WHERE o.order_id = :order_id 
                  LIMIT 1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute([":order_id" => $orderId]);

        return $stmt->fetch();
    }

    public function getOrderDetails($orderId)
    {
        $query = "SELECT od.*, p.product_name, p.main_image, p.sku 
                  FROM order_details od
                  INNER JOIN products p ON od.product_id = p.product_id
                  WHERE od.order_id = :order_id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute([":order_id" => $orderId]);

        return $stmt->fetchAll();
    }

    public function updateStatus($orderId, $status)
    {
        $query = "UPDATE {$this->table} SET status = :status WHERE order_id = :order_id";
        $stmt = $this->conn->prepare($query);

        return $stmt->execute([
            ":status" => $status,
            ":order_id" => $orderId
        ]);
    }

    public function restoreStock($orderId)
    {
        $details = $this->getOrderDetails($orderId);
        if (empty($details)) {
            return false;
        }

        foreach ($details as $item) {
            $query = "UPDATE products 
                      SET stock_quantity = stock_quantity + :quantity 
                      WHERE product_id = :product_id";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([
                ":quantity" => (int)$item["quantity"],
                ":product_id" => (int)$item["product_id"]
            ]);
        }

        return true;
    }
}
?>

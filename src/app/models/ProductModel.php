<?php
require_once BASE_PATH . "/config/database.php";

class ProductModel
{
    private $conn;
    private $table = "products";

    public function __construct()
    {
        $db = new Database();
        $this->conn = $db->getConnection();
    }

    public function getAll($filters = [])
    {
        $query =
            "SELECT p.*, c.category_name
             FROM {$this->table} p
             INNER JOIN categories c ON p.category_id = c.category_id
             WHERE 1 = 1";
        $params = [];

        if (!empty($filters["keyword"])) {
            $query .= " AND p.product_name LIKE :keyword";
            $params[":keyword"] = "%" . $filters["keyword"] . "%";
        }

        if (!empty($filters["category_id"])) {
            $query .= " AND p.category_id = :category_id";
            $params[":category_id"] = (int) $filters["category_id"];
        }

        $sort = $filters["sort"] ?? "newest";
        if ($sort === "price_asc") {
            $query .= " ORDER BY p.price ASC";
        } elseif ($sort === "price_desc") {
            $query .= " ORDER BY p.price DESC";
        } else {
            $query .= " ORDER BY p.product_id DESC";
        }

        $stmt = $this->conn->prepare($query);
        $stmt->execute($params);

        return $stmt->fetchAll();
    }

    public function create($data)
    {
        $query =
            "INSERT INTO {$this->table}
                (category_id, product_name, sku, description, price, sale_price, stock_quantity, main_image, status)
             VALUES
                (:category_id, :product_name, :sku, :description, :price, :sale_price, :stock_quantity, :main_image, :status)";
        $stmt = $this->conn->prepare($query);

        $stmt->execute([
            ":category_id" => $data["category_id"],
            ":product_name" => $data["product_name"],
            ":sku" => $data["sku"],
            ":description" => $data["description"],
            ":price" => $data["price"],
            ":sale_price" => $data["sale_price"],
            ":stock_quantity" => $data["stock_quantity"],
            ":main_image" => $data["main_image"],
            ":status" => $data["status"],
        ]);

        return (int) $this->conn->lastInsertId();
    }

    public function updateStatus($productId, $status)
    {
        $query = "UPDATE {$this->table} SET status = :status WHERE product_id = :product_id";
        $stmt = $this->conn->prepare($query);

        return $stmt->execute([
            ":product_id" => $productId,
            ":status" => $status,
        ]);
    }

    public function addImage($productId, $imageUrl)
    {
        $query =
            "INSERT INTO product_images (product_id, image_url)
             VALUES (:product_id, :image_url)";
        $stmt = $this->conn->prepare($query);

        return $stmt->execute([
            ":product_id" => $productId,
            ":image_url" => $imageUrl,
        ]);
    }

    public function addAttribute($productId, $attributeType, $attributeValue)
    {
        if ($attributeValue === "") {
            return true;
        }

        $query =
            "INSERT INTO product_attributes (product_id, attribute_type, attribute_value)
             VALUES (:product_id, :attribute_type, :attribute_value)";
        $stmt = $this->conn->prepare($query);

        return $stmt->execute([
            ":product_id" => $productId,
            ":attribute_type" => $attributeType,
            ":attribute_value" => $attributeValue,
        ]);
    }
}
?>

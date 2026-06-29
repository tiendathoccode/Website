<?php
require_once BASE_PATH . "/config/database.php";

class CategoryModel
{
    private $conn;
    private $table = "categories";

    public function __construct()
    {
        $db = new Database();
        $this->conn = $db->getConnection();
    }

    public function getAll()
    {
        $query = "SELECT * FROM {$this->table} ORDER BY category_id DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function findById($categoryId)
    {
        $query = "SELECT * FROM {$this->table} WHERE category_id = :category_id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([":category_id" => $categoryId]);

        return $stmt->fetch();
    }

    public function existsByName($categoryName, $excludeId = null)
    {
        $query = "SELECT category_id FROM {$this->table} WHERE category_name = :category_name";
        $params = [":category_name" => $categoryName];

        if ($excludeId !== null) {
            $query .= " AND category_id != :category_id";
            $params[":category_id"] = $excludeId;
        }

        $query .= " LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->execute($params);

        return (bool) $stmt->fetch();
    }

    public function create($categoryName, $description, $status)
    {
        $query =
            "INSERT INTO {$this->table} (category_name, description, status)
             VALUES (:category_name, :description, :status)";
        $stmt = $this->conn->prepare($query);

        return $stmt->execute([
            ":category_name" => $categoryName,
            ":description" => $description,
            ":status" => $status,
        ]);
    }

    public function update($categoryId, $categoryName, $description, $status)
    {
        $query =
            "UPDATE {$this->table}
             SET category_name = :category_name,
                 description = :description,
                 status = :status
             WHERE category_id = :category_id";
        $stmt = $this->conn->prepare($query);

        return $stmt->execute([
            ":category_id" => $categoryId,
            ":category_name" => $categoryName,
            ":description" => $description,
            ":status" => $status,
        ]);
    }

    public function updateStatus($categoryId, $status)
    {
        $query = "UPDATE {$this->table} SET status = :status WHERE category_id = :category_id";
        $stmt = $this->conn->prepare($query);

        return $stmt->execute([
            ":category_id" => $categoryId,
            ":status" => $status,
        ]);
    }
}
?>

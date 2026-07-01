<?php
// Bắt buộc gọi file cấu hình DB để hệ thống biết mật khẩu MySQL
require_once BASE_PATH . "/config/database.php";

class UserModel
{
    private $conn;
    private $table = "users";

    public function __construct()
    {
        $db = new Database();
        $this->conn = $db->getConnection();
    }

    // [Dùng cho Đăng ký & Đăng nhập] Tìm xem email có trong DB chưa
    public function findByEmail($email)
    {
        $query =
            "SELECT * FROM " . $this->table . " WHERE email = :email LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":email", $email);
        $stmt->execute();

        return $stmt->fetch();
    }

    // [Dùng cho Đăng ký] Đẩy thông tin người dùng mới xuống DB
    public function create($fullName, $email, $phone, $password)
    {
        $query =
            "INSERT INTO " .
            $this->table .
            " (full_name, email, phone, password) VALUES (:full_name, :email, :phone, :password)";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":full_name", $fullName);
        $stmt->bindParam(":email", $email);
        $stmt->bindParam(":phone", $phone);
        $stmt->bindParam(":password", $password);

        return $stmt->execute();
    }
    public function updatePassword($email, $newHashedPassword)
    {
        $query =
            "UPDATE " .
            $this->table .
            " SET password = :password WHERE email = :email";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":password", $newHashedPassword);
        $stmt->bindParam(":email", $email);
        return $stmt->execute();
    }
    // 1. Lưu token và thời gian hết hạn vào DB
    public function setResetToken($email, $token, $expiry)
    {
        $query =
            "UPDATE users SET reset_token = :token, reset_token_expire = :expiry WHERE email = :email";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([
            ":token" => $token,
            ":expiry" => $expiry,
            ":email" => $email,
        ]);
    }

    // 2. Tìm User dựa trên Token (để kiểm tra xem link còn hạn không)
    public function findByToken($token)
    {
        // Kiểm tra token khớp VÀ thời gian hiện tại phải nhỏ hơn thời gian hết hạn (NOW())
        $query =
            "SELECT * FROM users WHERE reset_token = :token AND reset_token_expire > NOW() LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([":token" => $token]);
        return $stmt->fetch();
    }

    // 3. Xóa token sau khi đổi xong (để bảo mật, không cho dùng lại link cũ)
    public function clearResetToken($email)
    {
        $query =
            "UPDATE users SET reset_token = NULL, reset_token_expire = NULL WHERE email = :email";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([":email" => $email]);
    }

    public function getAll($filters = [])
    {
        $query = "SELECT * FROM {$this->table} WHERE 1 = 1";
        $params = [];

        if (!empty($filters["keyword"])) {
            $query .= " AND (full_name LIKE :keyword OR email LIKE :keyword OR phone LIKE :keyword)";
            $params[":keyword"] = "%" . $filters["keyword"] . "%";
        }

        $query .= " ORDER BY user_id DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->execute($params);

        return $stmt->fetchAll();
    }

    public function findById($userId)
    {
        $query = "SELECT * FROM {$this->table} WHERE user_id = :user_id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([":user_id" => $userId]);

        return $stmt->fetch();
    }

    public function updateStatus($userId, $status)
    {
        $query = "UPDATE {$this->table} SET status = :status WHERE user_id = :user_id";
        $stmt = $this->conn->prepare($query);

        return $stmt->execute([
            ":status" => $status,
            ":user_id" => $userId
        ]);
    }
}
?>

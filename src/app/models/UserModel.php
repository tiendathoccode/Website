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
    public function setResetToken($email, $token, $expiry)
    {
        $query =
            "UPDATE users SET reset_token = :token, reset_token_expiry = :expiry WHERE email = :email";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([
            ":token" => $token,
            ":expiry" => $expiry,
            ":email" => $email,
        ]);
    }

    public function findByToken($token)
    {
        $sql =
            "SELECT * FROM users WHERE reset_token = :token AND reset_token_expiry > NOW()";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([":token" => $token]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function clearResetToken($email)
    {
        $sql =
            "UPDATE users SET reset_token = NULL, reset_token_expiry = NULL WHERE email = :email";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([":email" => $email]);
    }
}
?>

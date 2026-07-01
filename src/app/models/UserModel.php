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

    public function getDetailedProfile($userId)
    {
        $sql = "SELECT * FROM v_user_profiles WHERE user_id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([":id" => $userId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updateDetailedProfile($userId, $fullName, $email, $phone, $province, $district, $ward, $specific)
    {
        try {
            $this->conn->beginTransaction();

            // 1. Cập nhật bảng users
            $sqlUser = "UPDATE users SET full_name = :name, email = :email, phone = :phone WHERE user_id = :id";
            $stmtUser = $this->conn->prepare($sqlUser);
            $stmtUser->execute([
                ":id" => $userId,
                ":name" => $fullName,
                ":email" => $email,
                ":phone" => $phone
            ]);

            // 2. Kiểm tra xem địa chỉ mặc định đã tồn tại chưa
            $sqlCheck = "SELECT address_id FROM user_addresses WHERE user_id = :uid AND is_default = 1 LIMIT 1";
            $stmtCheck = $this->conn->prepare($sqlCheck);
            $stmtCheck->execute([":uid" => $userId]);
            $exists = $stmtCheck->fetch(PDO::FETCH_ASSOC);

            if ($exists) {
                // Đã tồn tại -> Cập nhật
                $sqlUpdateAddr = "UPDATE user_addresses 
                                  SET receiver_name = :name, 
                                      receiver_phone = :phone, 
                                      province_city = :province, 
                                      district = :district, 
                                      ward_commune = :ward, 
                                      specific_address = :specific 
                                  WHERE user_id = :uid AND is_default = 1";
                $stmtUpdateAddr = $this->conn->prepare($sqlUpdateAddr);
                $stmtUpdateAddr->execute([
                    ":uid" => $userId,
                    ":name" => $fullName,
                    ":phone" => $phone,
                    ":province" => $province,
                    ":district" => $district,
                    ":ward" => $ward,
                    ":specific" => $specific
                ]);
            } else {
                // Chưa tồn tại -> Thêm mới địa chỉ mặc định
                $sqlInsertAddr = "INSERT INTO user_addresses (user_id, receiver_name, receiver_phone, province_city, district, ward_commune, specific_address, is_default) 
                                  VALUES (:uid, :name, :phone, :province, :district, :ward, :specific, 1)";
                $stmtInsertAddr = $this->conn->prepare($sqlInsertAddr);
                $stmtInsertAddr->execute([
                    ":uid" => $userId,
                    ":name" => $fullName,
                    ":phone" => $phone,
                    ":province" => $province,
                    ":district" => $district,
                    ":ward" => $ward,
                    ":specific" => $specific
                ]);
            }

            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            if ($this->conn->inTransaction()) {
                $this->conn->rollBack();
            }
            throw $e;
        }
    }
}
?>

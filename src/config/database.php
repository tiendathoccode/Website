<?php
class Database
{
    // Thông số dựa theo cấu hình Docker Compose của bạn
    private $host = "mysql"; // Tên service mysql
    private $db_name = "recruitment_db"; // Tên database
    private $username = "recruitment_user"; // User
    private $password = "caeltia"; // Password
    public $conn;

    // Hàm lấy kết nối
    public function getConnection()
    {
        $this->conn = null;

        try {
            // 1. Tạo chuỗi DSN
            $dsn =
                "mysql:host=" .
                $this->host .
                ";dbname=" .
                $this->db_name .
                ";charset=utf8mb4";

            // 2. Khởi tạo đối tượng PDO
            $this->conn = new PDO($dsn, $this->username, $this->password);

            $this->conn->setAttribute(
                PDO::ATTR_ERRMODE,
                PDO::ERRMODE_EXCEPTION,
            );


            $this->conn->setAttribute(
                PDO::ATTR_DEFAULT_FETCH_MODE,
                PDO::FETCH_ASSOC,
            );
        } catch (PDOException $exception) {
            echo "<h3 style='color:red;'>Lỗi kết nối Cơ sở dữ liệu: " .
                $exception->getMessage() .
                "</h3>";
            exit();
        }

        return $this->conn;
    }
}
?>

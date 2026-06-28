<?php
class Database
{
    // Thông số dựa theo cấu hình Docker Compose của bạn
    private $host = "mysql"; // Tên service mysql trong mạng Docker
    private $db_name = "recruitment_db"; // Tên database mặc định
    private $username = "recruitment_user"; // User mặc định
    private $password = "caeltia"; // Password mặc định
    public $conn;

    // Hàm lấy kết nối
    public function getConnection()
    {
        $this->conn = null;

        try {
            // 1. Tạo chuỗi DSN (Data Source Name)
            $dsn =
                "mysql:host=" .
                $this->host .
                ";dbname=" .
                $this->db_name .
                ";charset=utf8mb4";

            // 2. Khởi tạo đối tượng PDO
            $this->conn = new PDO($dsn, $this->username, $this->password);

            // 3. ĐÒN BẨY QUẢN TRỊ LỖI: Bắt hệ thống la lên nếu viết sai câu SQL
            $this->conn->setAttribute(
                PDO::ATTR_ERRMODE,
                PDO::ERRMODE_EXCEPTION,
            );

            // 4. CHUẨN HÓA DỮ LIỆU RA: Chỉ lấy mảng dữ liệu sạch (loại bỏ index số thừa thãi)
            $this->conn->setAttribute(
                PDO::ATTR_DEFAULT_FETCH_MODE,
                PDO::FETCH_ASSOC,
            );
        } catch (PDOException $exception) {
            // Chặn đứng hệ thống nếu database sập, không cho chạy tiếp
            echo "<h3 style='color:red;'>Lỗi kết nối Cơ sở dữ liệu: " .
                $exception->getMessage() .
                "</h3>";
            exit();
        }

        return $this->conn;
    }
}
?>

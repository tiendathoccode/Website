<?php
class ChatController
{
    private $conn;

    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        require_once BASE_PATH . "/config/database.php";
        $db = new Database();
        $this->conn = $db->getConnection();
    }

    private function json($data)
    {
        header("Content-Type: application/json; charset=utf-8");
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit();
    }

    private function checkLoggedIn()
    {
        if (!isset($_SESSION["user_logged_in"]) || $_SESSION["user_logged_in"] !== true) {
            $this->json(["status" => "error", "message" => "Vui lòng đăng nhập để sử dụng chức năng chat."]);
        }
    }

    public function handleRequest()
    {
        $this->checkLoggedIn();
        $action = $_GET["action"] ?? "";

        switch ($action) {
            case "history":
                $this->getHistory();
                break;
            case "send":
                $this->sendMessage();
                break;
            case "list":
                $this->listUsers();
                break;
            case "unread_count":
                $this->getUnreadCount();
                break;
            default:
                $this->json(["status" => "error", "message" => "Hành động không hợp lệ."]);
                break;
        }
    }

    private function getHistory()
    {
        $myId = (int)$_SESSION["user_id"];
        $myRole = $_SESSION["user_role"] ?? "customer";
        $lastId = isset($_GET["last_id"]) ? (int)$_GET["last_id"] : 0;

        if ($myRole === "admin") {
            $targetUserId = isset($_GET["user_id"]) ? (int)$_GET["user_id"] : 0;
            if ($targetUserId <= 0) {
                $this->json(["status" => "error", "message" => "Thiếu mã người dùng."]);
            }
            $userId = $targetUserId;
            $adminId = $myId;
        } else {
            $userId = $myId;
            // Admin chính mặc định là user_id = 1
            $adminId = 1;
        }

        try {
            // Đánh dấu các tin nhắn đối phương gửi cho mình là đã đọc
            if ($myRole === "admin") {
                $stmtRead = $this->conn->prepare("UPDATE chat_messages SET is_read = 1 WHERE sender_id = :uid AND receiver_id = :aid AND is_read = 0");
                $stmtRead->execute([":uid" => $userId, ":aid" => $adminId]);
            } else {
                $stmtRead = $this->conn->prepare("UPDATE chat_messages SET is_read = 1 WHERE sender_id = :aid AND receiver_id = :uid AND is_read = 0");
                $stmtRead->execute([":aid" => $adminId, ":uid" => $userId]);
            }

            // Lấy lịch sử chat (tối ưu hóa chỉ lấy những tin nhắn sau last_id)
            if ($lastId > 0) {
                $stmt = $this->conn->prepare(
                    "SELECT * FROM chat_messages 
                     WHERE ((sender_id = :uid AND receiver_id = :aid) 
                        OR (sender_id = :aid AND receiver_id = :uid))
                       AND id > :last_id
                     ORDER BY id ASC"
                );
                $stmt->execute([":uid" => $userId, ":aid" => $adminId, ":last_id" => $lastId]);
            } else {
                // Tải lần đầu: Lấy 50 tin nhắn mới nhất
                $stmt = $this->conn->prepare(
                    "SELECT * FROM (
                        SELECT * FROM chat_messages 
                        WHERE (sender_id = :uid AND receiver_id = :aid) 
                           OR (sender_id = :aid AND receiver_id = :uid) 
                        ORDER BY id DESC LIMIT 50
                     ) sub ORDER BY id ASC"
                );
                $stmt->execute([":uid" => $userId, ":aid" => $adminId]);
            }
            $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Định dạng lại thời gian hiển thị thân thiện
            foreach ($messages as &$msg) {
                $time = strtotime($msg["created_at"]);
                $msg["formatted_time"] = date("H:i, d/m/Y", $time);
            }

            $this->json(["status" => "success", "messages" => $messages]);
        } catch (Exception $e) {
            $this->json(["status" => "error", "message" => $e->getMessage()]);
        }
    }

    private function sendMessage()
    {
        if ($_SERVER["REQUEST_METHOD"] !== "POST") {
            $this->json(["status" => "error", "message" => "Phương thức không được hỗ trợ."]);
        }

        $myId = (int)$_SESSION["user_id"];
        $myRole = $_SESSION["user_role"] ?? "customer";
        $message = trim($_POST["message"] ?? "");

        if (empty($message)) {
            $this->json(["status" => "error", "message" => "Tin nhắn không được để trống."]);
        }

        if ($myRole === "admin") {
            $targetUserId = isset($_POST["user_id"]) ? (int)$_POST["user_id"] : 0;
            if ($targetUserId <= 0) {
                $this->json(["status" => "error", "message" => "Thiếu mã người nhận tin nhắn."]);
            }
            $senderId = $myId;
            $receiverId = $targetUserId;
        } else {
            $senderId = $myId;
            $receiverId = 1; // Mặc định gửi cho admin chính (user_id = 1)
        }

        try {
            $stmt = $this->conn->prepare(
                "INSERT INTO chat_messages (sender_id, receiver_id, message, is_read) 
                 VALUES (:sender, :receiver, :msg, 0)"
            );
            $stmt->execute([
                ":sender" => $senderId,
                ":receiver" => $receiverId,
                ":msg" => $message
            ]);

            $newId = $this->conn->lastInsertId();

            $this->json([
                "status" => "success",
                "message_id" => $newId,
                "msg" => [
                    "id" => $newId,
                    "sender_id" => $senderId,
                    "receiver_id" => $receiverId,
                    "message" => $message,
                    "is_read" => 0,
                    "created_at" => date("Y-m-d H:i:s"),
                    "formatted_time" => date("H:i")
                ]
            ]);
        } catch (Exception $e) {
            $this->json(["status" => "error", "message" => $e->getMessage()]);
        }
    }

    private function listUsers()
    {
        $myRole = $_SESSION["user_role"] ?? "customer";
        if ($myRole !== "admin") {
            $this->json(["status" => "error", "message" => "Bạn không có quyền truy cập thông tin này."]);
        }

        try {
            // Lấy danh sách khách hàng đã nhắn tin với admin kèm thông tin tin nhắn cuối
            $sql = "SELECT u.user_id, u.full_name, u.email, u.avatar, u.phone,
                           (SELECT message FROM chat_messages 
                            WHERE (sender_id = u.user_id AND receiver_id = 1) 
                               OR (sender_id = 1 AND receiver_id = u.user_id) 
                            ORDER BY id DESC LIMIT 1) AS last_message,
                           (SELECT created_at FROM chat_messages 
                            WHERE (sender_id = u.user_id AND receiver_id = 1) 
                               OR (sender_id = 1 AND receiver_id = u.user_id) 
                            ORDER BY id DESC LIMIT 1) AS last_message_time,
                           (SELECT COUNT(*) FROM chat_messages 
                            WHERE sender_id = u.user_id AND receiver_id = 1 AND is_read = 0) AS unread_count
                    FROM users u
                    WHERE u.role = 'customer'
                      AND EXISTS (
                          SELECT 1 FROM chat_messages 
                          WHERE (sender_id = u.user_id AND receiver_id = 1) 
                             OR (sender_id = 1 AND receiver_id = u.user_id)
                      )
                    ORDER BY last_message_time DESC";

            $stmt = $this->conn->query($sql);
            $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($users as &$user) {
                if ($user["last_message_time"]) {
                    $time = strtotime($user["last_message_time"]);
                    $user["formatted_time"] = date("H:i, d/m/Y", $time);
                } else {
                    $user["formatted_time"] = "";
                }
                // Avatar mặc định nếu chưa có
                if (empty($user["avatar"])) {
                    $user["avatar"] = "assets/images/avatar-default.png"; // Đường dẫn mặc định hoặc dùng ký tự initials
                }
            }

            $this->json(["status" => "success", "users" => $users]);
        } catch (Exception $e) {
            $this->json(["status" => "error", "message" => $e->getMessage()]);
        }
    }

    private function getUnreadCount()
    {
        $myId = (int)$_SESSION["user_id"];
        $myRole = $_SESSION["user_role"] ?? "customer";

        try {
            if ($myRole === "admin") {
                // Đếm tổng số tin nhắn chưa đọc gửi đến admin
                $stmt = $this->conn->prepare("SELECT COUNT(*) FROM chat_messages WHERE receiver_id = :my_id AND is_read = 0");
                $stmt->execute([":my_id" => $myId]);
            } else {
                // Đếm tin nhắn chưa đọc từ admin gửi cho customer
                $stmt = $this->conn->prepare("SELECT COUNT(*) FROM chat_messages WHERE sender_id = 1 AND receiver_id = :my_id AND is_read = 0");
                $stmt->execute([":my_id" => $myId]);
            }
            $count = (int)$stmt->fetchColumn();
            $this->json(["status" => "success", "unread_count" => $count]);
        } catch (Exception $e) {
            $this->json(["status" => "error", "message" => $e->getMessage()]);
        }
    }
}

<?php
$host = $_ENV['DB_HOST'] ?? 'mysql';
$db   = $_ENV['DB_NAME'] ?? 'app_db';
$user = $_ENV['DB_USER'] ?? 'app_user';
$pass = $_ENV['DB_PASSWORD'] ?? 'app_password';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Lỗi kết nối: " . $e->getMessage());
}

// 1. Tự tạo bảng nếu chưa có -> test quyền CREATE
$pdo->exec("CREATE TABLE IF NOT EXISTS candidates (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

// 2. Thêm ứng viên mới -> test quyền INSERT
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['name'])) {
    $stmt = $pdo->prepare("INSERT INTO candidates (name, email) VALUES (?, ?)");
    $stmt->execute([$_POST['name'], $_POST['email']]);
}

// 3. Xóa ứng viên -> test quyền DELETE
if (isset($_GET['delete'])) {
    $stmt = $pdo->prepare("DELETE FROM candidates WHERE id = ?");
    $stmt->execute([$_GET['delete']]);
}

// 4. Lấy danh sách -> test quyền SELECT
$candidates = $pdo->query("SELECT * FROM candidates ORDER BY id DESC")->fetchAll();
?>
<!DOCTYPE html>
<html>
<head><meta charset="utf-8"><title>Test CRUD</title></head>
<body style="font-family: sans-serif; max-width: 600px; margin: 40px auto;">
    <h2>Test CRUD - Candidates</h2>

    <form method="POST">
        <input type="text" name="name" placeholder="Tên" required>
        <input type="email" name="email" placeholder="Email" required>
        <button type="submit">Thêm</button>
    </form>

    <table border="1" cellpadding="8" style="margin-top:20px; width:100%">
        <tr><th>ID</th><th>Tên</th><th>Email</th><th>Tạo lúc</th><th></th></tr>
        <?php foreach ($candidates as $c): ?>
        <tr>
            <td><?= $c['id'] ?></td>
            <td><?= htmlspecialchars($c['name']) ?></td>
            <td><?= htmlspecialchars($c['email']) ?></td>
            <td><?= $c['created_at'] ?></td>
            <td><a href="?delete=<?= $c['id'] ?>">Xóa</a></td>
        </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>

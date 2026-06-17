<?php
$host = $_ENV['DB_HOST'] ?? 'mysql';
$db   = $_ENV['DB_NAME'] ?? 'app_db';
$user = $_ENV['DB_USER'] ?? 'app_user';
$pass = $_ENV['DB_PASSWORD'] ?? 'app_password';

echo "<h1>PHP " . phpversion() . " đang chạy ngon!</h1>";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    echo "<p style='color:green'>✅ Kết nối MySQL thành công tới database <b>$db</b></p>";
} catch (PDOException $e) {
    echo "<p style='color:red'>❌ Kết nối MySQL thất bại: " . $e->getMessage() . "</p>";
}

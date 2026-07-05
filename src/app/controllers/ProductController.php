<?php
class ProductController
{
    public function showChiTiet($product_id)
    {
        if (!$product_id) {
            header("Location: /index.php?page=home");
            exit();
        }

        require_once BASE_PATH . "/config/database.php";
        $db = new Database();
        $conn = $db->getConnection();

        // Lấy thông tin sản phẩm từ view v_product_details
        $stmt = $conn->prepare("SELECT * FROM v_product_details WHERE product_id = :id AND status = 'show'");
        $stmt->execute([":id" => $product_id]);
        $product = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$product) {
            header("Location: /index.php?page=home");
            exit();
        }

        // Lấy ảnh phụ từ product_images
        $stmt2 = $conn->prepare(
            "SELECT * FROM product_images WHERE product_id = :id",
        );
        $stmt2->execute([":id" => $product_id]);
        $extraImages = $stmt2->fetchAll(PDO::FETCH_ASSOC);

        // Lấy danh sách đánh giá của sản phẩm (chỉ lấy những đánh giá đã được duyệt)
        $stmtReviews = $conn->prepare(
            "SELECT r.*, u.full_name 
             FROM reviews r 
             JOIN users u ON r.user_id = u.user_id 
             WHERE r.product_id = :pid AND r.status = 'approved' 
             ORDER BY r.review_id DESC"
        );
        $stmtReviews->execute([":pid" => $product_id]);
        $reviewsList = $stmtReviews->fetchAll(PDO::FETCH_ASSOC);

        require_once BASE_PATH . "/app/views/user/product-details.php";
    }
}

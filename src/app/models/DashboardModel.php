<?php
require_once BASE_PATH . "/config/database.php";

class DashboardModel
{
    private $conn;

    public function __construct()
    {
        $db = new Database();
        $this->conn = $db->getConnection();
    }

    public function getCardStats()
    {
        $currentStart = date("Y-m-01 00:00:00");
        $previousStart = date("Y-m-01 00:00:00", strtotime("-1 month"));
        $nextStart = date("Y-m-01 00:00:00", strtotime("+1 month"));

        $revenue = $this->getDeliveredRevenueBetween($currentStart, $nextStart);
        $previousRevenue = $this->getDeliveredRevenueBetween($previousStart, $currentStart);

        $orders = $this->getOrderCountBetween($currentStart, $nextStart);
        $previousOrders = $this->getOrderCountBetween($previousStart, $currentStart);

        $customers = $this->getCustomerCountBetween($currentStart, $nextStart);
        $previousCustomers = $this->getCustomerCountBetween($previousStart, $currentStart);

        $query = "SELECT COUNT(*) AS total_products FROM products WHERE status = 'show'";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $products = $stmt->fetch()["total_products"] ?? 0;

        return [
            "total_revenue" => (int)$revenue,
            "total_orders" => (int)$orders,
            "new_customers" => (int)$customers,
            "total_products" => (int)$products,
            "revenue_growth_percent" => $this->calculateGrowthPercent($revenue, $previousRevenue),
            "orders_growth_percent" => $this->calculateGrowthPercent($orders, $previousOrders),
            "new_customers_growth" => (int)$customers - (int)$previousCustomers,
        ];
    }

    public function getRecentOrders($limit = 5)
    {
        $query = "SELECT
                    o.order_id,
                    o.order_code,
                    o.final_amount,
                    o.created_at,
                    od.quantity,
                    p.product_name,
                    p.main_image
                  FROM orders o
                  LEFT JOIN order_details od ON od.order_detail_id = (
                       SELECT order_detail_id
                       FROM order_details
                       WHERE order_id = o.order_id
                       ORDER BY order_detail_id ASC
                       LIMIT 1
                   )
                  LEFT JOIN products p ON od.product_id = p.product_id
                  ORDER BY o.order_id DESC
                  LIMIT :limit";

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(":limit", (int)$limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function getLowStockProducts($threshold = 5, $limit = 5)
    {
        $query = "SELECT product_name, sku, stock_quantity
                  FROM products
                  WHERE stock_quantity <= :threshold AND status = 'show'
                  ORDER BY stock_quantity ASC
                  LIMIT :limit";

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(":threshold", (int)$threshold, PDO::PARAM_INT);
        $stmt->bindValue(":limit", (int)$limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function getMonthlySalesTrend($monthsCount = 6)
    {
        $monthsCount = max(1, (int)$monthsCount);
        $startDate = date("Y-m-01 00:00:00", strtotime("-" . ($monthsCount - 1) . " months"));

        $query = "SELECT
                    DATE_FORMAT(created_at, '%Y-%m') AS month_key,
                    SUM(final_amount) AS total_sales
                  FROM orders
                  WHERE status = 'delivered' AND created_at >= :start_date
                  GROUP BY month_key
                  ORDER BY month_key ASC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(":start_date", $startDate);
        $stmt->execute();

        $salesByMonth = [];
        foreach ($stmt->fetchAll() as $row) {
            $salesByMonth[$row["month_key"]] = (int)$row["total_sales"];
        }

        $result = [];
        $baseMonth = strtotime(date("Y-m-01 00:00:00"));
        for ($i = $monthsCount - 1; $i >= 0; $i--) {
            $time = strtotime("-" . $i . " months", $baseMonth);
            $monthKey = date("Y-m", $time);
            $result[] = [
                "month_key" => $monthKey,
                "month_name" => date("M", $time),
                "total_sales" => $salesByMonth[$monthKey] ?? 0,
            ];
        }

        return $result;
    }

    private function getDeliveredRevenueBetween($startDate, $endDate)
    {
        $query = "SELECT COALESCE(SUM(final_amount), 0) AS total
                  FROM orders
                  WHERE status = 'delivered'
                    AND created_at >= :start_date
                    AND created_at < :end_date";

        $stmt = $this->conn->prepare($query);
        $stmt->execute([
            ":start_date" => $startDate,
            ":end_date" => $endDate,
        ]);

        return (int)$stmt->fetch()["total"];
    }

    private function getOrderCountBetween($startDate, $endDate)
    {
        $query = "SELECT COUNT(*) AS total
                  FROM orders
                  WHERE created_at >= :start_date
                    AND created_at < :end_date";

        $stmt = $this->conn->prepare($query);
        $stmt->execute([
            ":start_date" => $startDate,
            ":end_date" => $endDate,
        ]);

        return (int)$stmt->fetch()["total"];
    }

    private function getCustomerCountBetween($startDate, $endDate)
    {
        $query = "SELECT COUNT(*) AS total
                  FROM users
                  WHERE role = 'customer'
                    AND created_at >= :start_date
                    AND created_at < :end_date";

        $stmt = $this->conn->prepare($query);
        $stmt->execute([
            ":start_date" => $startDate,
            ":end_date" => $endDate,
        ]);

        return (int)$stmt->fetch()["total"];
    }

    private function calculateGrowthPercent($currentValue, $previousValue)
    {
        $currentValue = (float)$currentValue;
        $previousValue = (float)$previousValue;

        if ($previousValue <= 0) {
            return $currentValue > 0 ? 100.0 : 0.0;
        }

        return round((($currentValue - $previousValue) / $previousValue) * 100, 1);
    }
}
?>

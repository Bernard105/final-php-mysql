<?php
namespace App\Models;

class Order extends BaseModel
{
    protected $table = 'orders';
    protected $primaryKey = 'order_id';

    /**
     * Create an order + its order_items.
     * Expected $data:
     *  - user_id (optional)
     *  - total_amount
     *  - payment_method (optional)
     *  - payment_status (optional)
     *  - shipping_address (optional)
     *  - order_status (optional)
     *  - items: array of ['product_id','quantity','price'] (required)
     */
    public function createOrder(array $data)
    {
        $orderData = [
            'user_id' => $data['user_id'] ?? null,
            'total_amount' => $data['total_amount'],
            'payment_method' => $data['payment_method'] ?? null,
            'payment_status' => $data['payment_status'] ?? 'pending',
            'shipping_address' => $data['shipping_address'] ?? null,
            'order_status' => $data['order_status'] ?? 'pending',
            'created_at' => date('Y-m-d H:i:s')
        ];

        $orderId = $this->create($orderData);

        if ($orderId && !empty($data['items'])) {
            $orderItemModel = new OrderItem();
            foreach ($data['items'] as $item) {
                $orderItemModel->addItem((int)$orderId, $item);
            }
        }

        return $orderId;
    }

    public function getOrderWithItems($orderId)
    {
        $sql = "SELECT * FROM {$this->table} WHERE order_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([(int)$orderId]);
        $order = $stmt->fetch();

        if (!$order) return null;

        $orderItemModel = new OrderItem();
        $order['items'] = $orderItemModel->getItemsForOrder((int)$orderId);

        return $order;
    }

    public function getUserOrders($userId)
    {
        $sql = "SELECT o.*, 
                       COUNT(oi.order_item_id) as item_count
                FROM {$this->table} o 
                LEFT JOIN order_items oi ON o.order_id = oi.order_id 
                WHERE o.user_id = ? 
                GROUP BY o.order_id 
                ORDER BY o.created_at DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([(int)$userId]);
        return $stmt->fetchAll();
    }

    public function updateStatus($orderId, $status)
    {
        $allowedStatuses = ['pending', 'processing', 'shipped', 'delivered', 'cancelled'];

        if (in_array($status, $allowedStatuses, true)) {
            return $this->update((int)$orderId, ['order_status' => $status]);
        }

        return false;
    }

    public function getStats($period = 'month')
    {
        $interval = $this->getInterval($period);

        $sql = "SELECT 
                    COUNT(*) as total_orders,
                    SUM(total_amount) as total_revenue,
                    AVG(total_amount) as avg_order_value,
                    DATE(created_at) as date 
                FROM {$this->table} 
                WHERE created_at >= DATE_SUB(NOW(), INTERVAL {$interval}) 
                GROUP BY DATE(created_at) 
                ORDER BY date DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    private function getInterval($period)
    {
        $intervals = [
            'day' => '1 DAY',
            'week' => '7 DAY',
            'month' => '30 DAY',
            'year' => '365 DAY'
        ];

        return $intervals[$period] ?? '30 DAY';
    }


    public function getRecentOrders(int $limit = 10): array
    {
        $sql = "SELECT * FROM {$this->table} WHERE order_status != 'cancelled' ORDER BY created_at DESC LIMIT ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(1, $limit, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function searchOrders($status = null, $search = null): array
    {
        $where = [];
        $params = [];

        if ($status && $status !== 'all') {
            $where[] = "order_status = ?";
            $params[] = $status;
        }

        if ($search) {
            if (is_numeric($search)) {
                $where[] = "order_id = ?";
                $params[] = (int)$search;
            }
        }

        $whereSql = $where ? ("WHERE " . implode(" AND ", $where)) : "";
        $sql = "SELECT * FROM {$this->table} {$whereSql} ORDER BY created_at DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

}

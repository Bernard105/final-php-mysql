<?php
namespace App\Models;

class OrderItem extends BaseModel
{
    protected $table = 'order_items';
    protected $primaryKey = 'order_item_id';

    public function addItem(int $orderId, array $item): int
    {
        $data = [
            'order_id' => $orderId,
            'product_id' => (int)($item['product_id'] ?? $item['id'] ?? 0),
            'quantity' => (int)($item['quantity'] ?? 1),
            'price' => (float)($item['price'] ?? 0),
        ];

        return (int)$this->create($data);
    }

    public function getItemsForOrder(int $orderId): array
    {
        $sql = "SELECT oi.*, p.product_title, p.product_image1 
                FROM {$this->table} oi
                LEFT JOIN products p ON oi.product_id = p.product_id
                WHERE oi.order_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$orderId]);
        return $stmt->fetchAll();
    }
}

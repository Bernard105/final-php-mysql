<?php
namespace App\Models;

class Cart extends BaseModel
{
    protected $table = 'cart_items';
    protected $primaryKey = 'cart_id';
    
    public function getUserCart($userId)
    {
        $sql = "SELECT ci.*, p.product_title, p.product_price, p.product_image1, 
                       p.product_description, p.product_keywords 
                FROM {$this->table} ci 
                JOIN products p ON ci.product_id = p.product_id 
                WHERE ci.user_id = ?";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }
    
    public function getCartBySession($sessionId)
    {
        $sql = "SELECT ci.*, p.product_title, p.product_price, p.product_image1 
                FROM {$this->table} ci 
                JOIN products p ON ci.product_id = p.product_id 
                WHERE ci.session_id = ?";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$sessionId]);
        return $stmt->fetchAll();
    }
    
    public function addItem($userId, $productId, $quantity = 1, $sessionId = null)
    {
        // Check if item already exists
        $existing = $this->findItem($userId, $productId, $sessionId);
        
        if ($existing) {
            // Update quantity
            $newQuantity = $existing['quantity'] + $quantity;
            return $this->update($existing['cart_id'], ['quantity' => $newQuantity]);
        } else {
            // Add new item
            $data = [
                'user_id' => $userId,
                'product_id' => $productId,
                'quantity' => $quantity,
                'session_id' => $sessionId,
                'created_at' => date('Y-m-d H:i:s')
            ];
            
            return $this->create($data);
        }
    }
    
    public function findItem($userId, $productId, $sessionId = null)
    {
        if ($userId) {
            $sql = "SELECT * FROM {$this->table} WHERE user_id = ? AND product_id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$userId, $productId]);
        } else {
            $sql = "SELECT * FROM {$this->table} WHERE session_id = ? AND product_id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$sessionId, $productId]);
        }
        
        return $stmt->fetch();
    }
    
    public function updateQuantity($cartId, $quantity)
    {
        if ($quantity <= 0) {
            return $this->delete($cartId);
        }
        
        return $this->update($cartId, ['quantity' => $quantity]);
    }
    
    public function clearUserCart($userId)
    {
        $sql = "DELETE FROM {$this->table} WHERE user_id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$userId]);
    }
    
    public function migrateSessionCart($sessionId, $userId)
    {
        // Get session cart items
        $sessionItems = $this->getCartBySession($sessionId);
        
        foreach ($sessionItems as $item) {
            // Check if user already has this item
            $existing = $this->findItem($userId, $item['product_id']);
            
            if ($existing) {
                // Update quantity
                $newQuantity = $existing['quantity'] + $item['quantity'];
                $this->update($existing['cart_id'], ['quantity' => $newQuantity]);
                // Delete session item
                $this->delete($item['cart_id']);
            } else {
                // Transfer to user
                $this->update($item['cart_id'], [
                    'user_id' => $userId,
                    'session_id' => null
                ]);
            }
        }
        
        return true;
    }

    public function clearSessionCart($sessionId)
    {
        $sql = "DELETE FROM {$this->table} WHERE session_id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$sessionId]);
    }

}

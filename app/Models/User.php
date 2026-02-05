<?php
namespace App\Models;

class User extends BaseModel
{
    protected $table = 'users';
    protected $primaryKey = 'id';
    
    public function findByEmail($email)
    {
        $sql = "SELECT * FROM {$this->table} WHERE email = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$email]);
        return $stmt->fetch();
    }
    
    public function findByUsername($username)
    {
        $sql = "SELECT * FROM {$this->table} WHERE username = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$username]);
        return $stmt->fetch();
    }
    
    public function updatePassword($id, $password)
    {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        return $this->update($id, ['password' => $hashedPassword]);
    }
    
    public function updateProfile($id, $data)
    {
        $allowedFields = ['username', 'email', 'address', 'mobile', 'user_image'];
        $updateData = [];
        
        foreach ($data as $key => $value) {
            if (in_array($key, $allowedFields)) {
                $updateData[$key] = $value;
            }
        }
        
        if (!empty($updateData)) {
            return $this->update($id, $updateData);
        }
        
        return false;
    }
    
    public function getOrders($userId)
    {
        $sql = "SELECT o.*, COUNT(oi.order_item_id) as item_count 
                FROM orders o 
                LEFT JOIN order_items oi ON o.order_id = oi.order_id 
                WHERE o.user_id = ? 
                GROUP BY o.order_id 
                ORDER BY o.created_at DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }
    
    public function getCartCount($userId)
    {
        $sql = "SELECT COUNT(*) as count FROM cart_items WHERE user_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId]);
        $result = $stmt->fetch();
        return $result['count'] ?? 0;
    }


    public function searchUsers($search = null): array
    {
        $params = [];
        $whereSql = "";

        if ($search) {
            $whereSql = "WHERE username LIKE ? OR email LIKE ? OR mobile LIKE ?";
            $s = "%{$search}%";
            $params = [$s, $s, $s];
        }

        $sql = "SELECT * FROM {$this->table} {$whereSql} ORDER BY created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

}

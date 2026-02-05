<?php
namespace App\Models;

class Category extends BaseModel
{
    protected $table = 'categories';
    protected $primaryKey = 'category_id';
    
    public function getWithProductCount()
    {
        $sql = "SELECT c.*, COUNT(p.product_id) as product_count 
                FROM {$this->table} c 
                LEFT JOIN products p ON c.category_id = p.category_id 
                GROUP BY c.category_id";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    public function getProducts($categoryId, $limit = null)
    {
        $sql = "SELECT * FROM products WHERE category_id = ?";
        
        if ($limit) {
            $sql .= " LIMIT ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$categoryId, $limit]);
        } else {
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$categoryId]);
        }
        
        return $stmt->fetchAll();
    }
    
    public function getPopularCategories($limit = 5)
    {
        $sql = "SELECT c.*, COUNT(p.product_id) as product_count 
                FROM {$this->table} c 
                JOIN products p ON c.category_id = p.category_id 
                GROUP BY c.category_id 
                ORDER BY product_count DESC 
                LIMIT ?";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$limit]);
        return $stmt->fetchAll();
    }
}
<?php
namespace App\Models;

class Brand extends BaseModel
{
    protected $table = 'brands';
    protected $primaryKey = 'brand_id';
    
    public function getWithProductCount()
    {
        $sql = "SELECT b.*, COUNT(p.product_id) as product_count 
                FROM {$this->table} b 
                LEFT JOIN products p ON b.brand_id = p.brand_id 
                GROUP BY b.brand_id";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    public function getProducts($brandId, $limit = null)
    {
        $sql = "SELECT * FROM products WHERE brand_id = ?";
        
        if ($limit) {
            $sql .= " LIMIT ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$brandId, $limit]);
        } else {
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$brandId]);
        }
        
        return $stmt->fetchAll();
    }
    
    public function getPopularBrands($limit = 5)
    {
        $sql = "SELECT b.*, COUNT(p.product_id) as product_count 
                FROM {$this->table} b 
                JOIN products p ON b.brand_id = p.brand_id 
                GROUP BY b.brand_id 
                ORDER BY product_count DESC 
                LIMIT ?";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$limit]);
        return $stmt->fetchAll();
    }
}
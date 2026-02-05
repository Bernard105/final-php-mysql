<?php
namespace App\Models;

class Product extends BaseModel
{
    protected $table = 'products';
    protected $primaryKey = 'product_id';

    public function withCategory()
    {
        $sql = "SELECT p.*, c.category_title 
                FROM {$this->table} p 
                LEFT JOIN categories c ON p.category_id = c.category_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Filter + sort + paginate product list without breaking existing structure.
     * Returns: ['items'=>[], 'total'=>int, 'page'=>int, 'perPage'=>int, 'pages'=>int]
     */
    public function paginateFiltered(array $filters, int $page = 1, int $perPage = 9): array
    {
        $page = max(1, (int)$page);
        $perPage = max(1, min(60, (int)$perPage));
        $offset = ($page - 1) * $perPage;

        $where = [];
        $params = [];

        $q = trim((string)($filters['q'] ?? ''));
        if ($q !== '') {
            $where[] = "(p.product_title LIKE ? OR p.product_description LIKE ? OR p.product_keywords LIKE ?)";
            $like = '%' . $q . '%';
            $params[] = $like;
            $params[] = $like;
            $params[] = $like;
        }

        $categoryId = (int)($filters['category_id'] ?? 0);
        if ($categoryId > 0) {
            $where[] = "p.category_id = ?";
            $params[] = $categoryId;
        }

        $brandId = (int)($filters['brand_id'] ?? 0);
        if ($brandId > 0) {
            $where[] = "p.brand_id = ?";
            $params[] = $brandId;
        }

        $minPrice = $filters['min_price'] ?? null;
        if ($minPrice !== null && $minPrice !== '' && is_numeric($minPrice)) {
            $where[] = "p.product_price >= ?";
            $params[] = (float)$minPrice;
        }

        $maxPrice = $filters['max_price'] ?? null;
        if ($maxPrice !== null && $maxPrice !== '' && is_numeric($maxPrice)) {
            $where[] = "p.product_price <= ?";
            $params[] = (float)$maxPrice;
        }

        $whereSql = $where ? ("WHERE " . implode(" AND ", $where)) : "";

        // Sort whitelist
        $sort = (string)($filters['sort'] ?? 'newest');
        $orderBy = "p.product_id DESC";
        switch ($sort) {
            case 'price_asc':
                $orderBy = "p.product_price ASC";
                break;
            case 'price_desc':
                $orderBy = "p.product_price DESC";
                break;
            case 'title_asc':
                $orderBy = "p.product_title ASC";
                break;
            case 'oldest':
                $orderBy = "p.product_id ASC";
                break;
            case 'newest':
            default:
                $orderBy = "p.product_id DESC";
                break;
        }

        // Total
        $countSql = "SELECT COUNT(*) AS cnt
                    FROM {$this->table} p
                    $whereSql";
        $stmt = $this->db->prepare($countSql);
        $stmt->execute($params);
        $total = (int)($stmt->fetch()['cnt'] ?? 0);

        $pages = (int)max(1, (int)ceil($total / $perPage));
        if ($page > $pages) {
            $page = $pages;
            $offset = ($page - 1) * $perPage;
        }

        // Items (join for display if needed)
        $itemsSql = "SELECT p.*, c.category_title, b.brand_title
                    FROM {$this->table} p
                    LEFT JOIN categories c ON p.category_id = c.category_id
                    LEFT JOIN brands b ON p.brand_id = b.brand_id
                    $whereSql
                    ORDER BY $orderBy
                    LIMIT $perPage OFFSET $offset";
        $stmt = $this->db->prepare($itemsSql);
        $stmt->execute($params);
        $items = $stmt->fetchAll();

        return [
            'items' => $items,
            'total' => $total,
            'page' => (int)$page,
            'perPage' => (int)$perPage,
            'pages' => (int)$pages,
        ];
    }

    public function getByCategory($categoryId)
    {
        $sql = "SELECT * FROM {$this->table} WHERE category_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$categoryId]);
        return $stmt->fetchAll();
    }

    public function getByBrand($brandId)
    {
        $sql = "SELECT * FROM {$this->table} WHERE brand_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$brandId]);
        return $stmt->fetchAll();
    }

    public function search($keyword)
    {
        $sql = "SELECT * FROM {$this->table} 
                WHERE product_title LIKE ? 
                OR product_description LIKE ? 
                OR product_keywords LIKE ?";
        $stmt = $this->db->prepare($sql);
        $keyword = "%{$keyword}%";
        $stmt->execute([$keyword, $keyword, $keyword]);
        return $stmt->fetchAll();
    }

    public function getRandom($limit = 9)
    {
        $sql = "SELECT * FROM {$this->table} ORDER BY RAND() LIMIT ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$limit]);
        return $stmt->fetchAll();
    }
}

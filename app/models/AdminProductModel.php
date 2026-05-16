<?php

class AdminProductModel extends Model
{
    public function getGlobalTotalProducts()
    {
        $sql = "SELECT COUNT(*) as total FROM products WHERE delete_flag = 0";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'] ?? 0;
    }

    public function getGlobalActiveProducts()
    {
        $sql = "SELECT COUNT(*) as active FROM products WHERE visibility = 1 AND delete_flag = 0";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['active'] ?? 0;
    }

    public function getPlatformRevenue()
    {
        $sql = "SELECT COALESCE(SUM(platform_fee), 0) AS total_platform_fee
                FROM payments
                WHERE status = 'paid'
                AND payment_method = 'stripe'";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total_platform_fee'] ?? 0;
    }

    public function getGlobalTotalBannedProducts()
    {
        $sql = "SELECT COUNT(*) as banned_products FROM products WHERE is_banned = 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['banned_products'] ?? 0;
    }

    public function getTotalProductsForPagination()
    {
        $sql = "SELECT COUNT(*) as total FROM products";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'] ?? 0;
    }

    //get 
    public function getProductsPaginated($page = 1, $limit = 5)
    {
        $offset = ($page - 1) * $limit;
        $sql = "SELECT
                p.*,
                c.name AS category_name,
                s.name AS store_name
                FROM products p
                LEFT JOIN categories c ON c.id = p.category_id
                LEFT JOIN stores s ON s.id = p.store_id
                ORDER BY p.created_at DESC
                LIMIT :limit OFFSET :offset";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAllProducts()
    {
        $sql = "SELECT
                p.*,
                c.name AS category_name,
                s.name AS store_name
                FROM products p
                LEFT JOIN categories c ON c.id = p.category_id
                LEFT JOIN stores s ON s.id = p.store_id
                ORDER BY p.created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function banProductById($productId)
    {
        $sql = "UPDATE products
                SET is_banned = 1, delete_flag = 1
                WHERE id = :product_id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':product_id', $productId, PDO::PARAM_STR);
        return $stmt->execute();
    }

    public function unbanProductById($productId)
    {
        $sql = "UPDATE products
                SET is_banned = 0, delete_flag = 0
                WHERE id = :product_id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':product_id', $productId, PDO::PARAM_STR);
        return $stmt->execute();
    }
}

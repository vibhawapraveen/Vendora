<?php

class StoreModel extends Model
{
    public function getStoreById($code)
    {
        $stmt = $this->db->prepare("SELECT s.id as store_id, visibility, template_id, file_path as file_path
        FROM stores as s JOIN templates as t ON t.id = s.template_id WHERE code=:storecode LIMIT 1");
        $stmt->execute(['storecode' => $code]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updateStoreViews($store_id)
    {
        $cookieName = "store_viewed_" . $store_id;

        // Check if the user has already viewed the store
        if (!isset($_COOKIE[$cookieName])) {
            // Set cookie for 24 hours
            setcookie($cookieName, "1", time() + 60, "/");

            $stmt = $this->db->prepare("
            INSERT INTO store_views (store_id, view_date, view_count)
            VALUES (:store_id, CURDATE(), 1)
            ON DUPLICATE KEY UPDATE
                view_count = view_count + 1
        ");

            return $stmt->execute(['store_id' => $store_id]);
        }

        return false;
    }

    public function getStoreViews($store_id)
    {
        $stmt = $this->db->prepare("SELECT SUM(view_count) as total_views FROM store_views WHERE store_id = :store_id");
        $stmt->execute(['store_id' => $store_id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total_views'] ?? 0;
    }

    public function getStoreViewsOverTime($store_id, $days = 30)
    {
        $stmt = $this->db->prepare("
            SELECT view_date, view_count 
            FROM store_views 
            WHERE store_id = :store_id AND view_date >= DATE_SUB(CURDATE(), INTERVAL :days DAY)
            ORDER BY view_date ASC
        ");
        $stmt->execute(['store_id' => $store_id, 'days' => $days]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getStoreViewsStats($store_id)
    {
        $stmt = $this->db->prepare("
            SELECT 
                SUM(view_count) as total_views,
                AVG(view_count) as avg_daily_views,
                MAX(view_count) as max_daily_views,
                COUNT(*) as days_with_views,
                DATE(MAX(view_date)) as last_view_date
            FROM store_views 
            WHERE store_id = :store_id
        ");
        $stmt->execute(['store_id' => $store_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getStoreWebData($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM store_contents WHERE store_id=:storeid");
        $stmt->execute(['storeid' => $id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getStorefrontOverview($storeId)
    {
        $stmt = $this->db->prepare("SELECT code, visibility, views FROM stores WHERE id=:storeid LIMIT 1");
        $stmt->execute(['storeid' => $storeId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updateStoreVisibility($storeId, $visibility)
    {
        $stmt = $this->db->prepare("UPDATE stores SET visibility=:visibility WHERE id=:storeid");
        return $stmt->execute(['visibility' => $visibility, 'storeid' => $storeId]);
    }

    public function isStoreCodeTaken($code)
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) as count FROM stores WHERE code = :code");
        $stmt->execute(['code' => $code]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'] > 0;
    }

    public function updateStoreCode($storeId, $code)
    {
        $stmt = $this->db->prepare("UPDATE stores SET code = :code WHERE id = :store_id");
        return $stmt->execute([
            'code' => $code,
            'store_id' => $storeId
        ]);
    }

    public function getAdminStores($search = '', $status = 'all')
    {
        $sql = "
            SELECT st.id, st.name as store_name, s.name as owner_name, 
                   st.created_at, st.visibility, 
                   (SELECT COUNT(id) FROM products WHERE store_id = st.id) as products_count
            FROM stores st
            JOIN sellers s ON st.seller_id = s.id
            WHERE st.name LIKE :search
        ";

        if ($status === 'active') {
            $sql .= " AND st.visibility = 1";
        } else if ($status === 'disabled') {
            $sql .= " AND st.visibility = 0";
        }

        $sql .= " ORDER BY st.created_at DESC";

        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute(['search' => '%' . $search . '%']);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Get admin stores error: " . $e->getMessage());
            return [];
        }
    }

    public function getStoreDetailsInfo($storeId)
    {
        try {
            $stmt = $this->db->prepare("
                SELECT st.id, st.name as store_name, s.name as owner_name, st.created_at
                FROM stores st
                JOIN sellers s ON st.seller_id = s.id
                WHERE st.id = :id LIMIT 1
            ");
            $stmt->execute(['id' => $storeId]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return false;
        }
    }

    public function getStoreProductsWithImages($storeId)
    {
        try {
            $stmt = $this->db->prepare("
                SELECT p.id, p.name, p.visibility, p.is_variant,
                       IF(p.is_variant = 1, (SELECT MIN(price) FROM product_variants WHERE product_id = p.id), p.price) as price,
                       IF(p.is_variant = 1, (SELECT SUM(stock_quantity) FROM product_variants WHERE product_id = p.id), p.stock_quantity) as stock_quantity, 
                       COALESCE(
                           (SELECT image_url FROM product_images pi WHERE pi.product_id = p.id ORDER BY pi.created_at ASC LIMIT 1),
                           (SELECT image FROM product_variants pv WHERE pv.product_id = p.id AND pv.image IS NOT NULL LIMIT 1)
                       ) as image_url
                FROM products p
                WHERE p.store_id = :storeId
                ORDER BY p.created_at DESC
            ");
            $stmt->execute(['storeId' => $storeId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Get store products mapping error: " . $e->getMessage());
            return [];
        }
    }

    public function getStoreSellerStripeAccountId($store_id)
    {
        $stmt = $this->db->prepare("SELECT sellers.stripe_account_id FROM stores
        LEFT JOIN sellers ON stores.seller_id = sellers.id
        WHERE stores.id = :store_id
        ");
        $stmt->execute(['store_id' => $store_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getSellerIdByStoreId($storeId)
    {
        try {
            $stmt = $this->db->prepare("SELECT seller_id FROM stores WHERE id = :id LIMIT 1");
            $stmt->execute(['id' => $storeId]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['seller_id'] ?? false;
        } catch (PDOException $e) {
            error_log("Get seller ID by store ID error: " . $e->getMessage());
            return false;
        }
    }
}

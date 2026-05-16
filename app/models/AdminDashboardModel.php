<?php

class AdminDashboardModel extends Model
{
    /**
     * Get high-level stats for the dashboard cards
     */
    public function getStats()
    {
        try {
            // Total Sellers
            $sellers = $this->db->query("SELECT COUNT(*) FROM sellers")->fetchColumn();
            
            // Total Stores
            $stores = $this->db->query("SELECT COUNT(*) FROM stores")->fetchColumn();
            
            // Total Orders
            $orders = $this->db->query("SELECT COUNT(*) FROM orders")->fetchColumn();
            
            // Total Revenue (excluding cancelled)
            $revenue = $this->db->query("SELECT SUM(total_amount) FROM orders WHERE status != 'cancelled'")->fetchColumn() ?: 0;
            
            // Pending Orders
            $pending = $this->db->query("SELECT COUNT(*) FROM orders WHERE status = 'pending'")->fetchColumn();
            
            // Active Products
            $products = $this->db->query("SELECT COUNT(*) FROM products")->fetchColumn();
            
            return [
                'total_sellers' => (int)$sellers,
                'total_stores' => (int)$stores,
                'total_orders' => (int)$orders,
                'total_revenue' => (float)$revenue,
                'pending_orders' => (int)$pending,
                'active_products' => (int)$products
            ];
        } catch (PDOException $e) {
            error_log("Get dashboard stats error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get monthly revenue for the current year
     */
    public function getMonthlyRevenue()
    {
        try {
            $stmt = $this->db->prepare("
                SELECT 
                    MONTHNAME(created_at) as month, 
                    SUM(total_amount) as revenue 
                FROM orders 
                WHERE status != 'cancelled' 
                AND YEAR(created_at) = YEAR(CURRENT_DATE())
                GROUP BY MONTH(created_at)
                ORDER BY MONTH(created_at)
            ");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Get monthly revenue error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get monthly order counts for the current year
     */
    public function getMonthlyOrders()
    {
        try {
            $stmt = $this->db->prepare("
                SELECT 
                    MONTHNAME(created_at) as month, 
                    COUNT(*) as count 
                FROM orders 
                WHERE YEAR(created_at) = YEAR(CURRENT_DATE())
                GROUP BY MONTH(created_at)
                ORDER BY MONTH(created_at)
            ");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Get monthly orders error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get top 10 performing stores by revenue
     */
    public function getTopStores($limit = 6)
    {
        try {
            $stmt = $this->db->prepare("
                SELECT 
                    s.name as store_name, 
                    SUM(o.total_amount) as revenue 
                FROM stores s 
                JOIN orders o ON s.id = o.store_id 
                WHERE o.status != 'cancelled' 
                GROUP BY s.id 
                ORDER BY revenue DESC 
                LIMIT :limit
            ");
            $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Get top stores error: " . $e->getMessage());
            return [];
        }
    }
    /**
     * Get sales by category breakdown
     */
    public function getSalesByCategory()
    {
        try {
            $stmt = $this->db->prepare("
                SELECT 
                    c.name as category_name, 
                    COALESCE(SUM(o.total_amount), 0) as revenue 
                FROM categories c 
                LEFT JOIN products p ON c.id = p.category_id 
                LEFT JOIN order_items oi ON p.id = oi.product_id 
                LEFT JOIN orders o ON oi.order_id = o.id AND o.status != 'cancelled'
                GROUP BY c.id 
                ORDER BY revenue DESC
            ");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Get sales by category error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get monthly new seller signups for the current year
     */
    public function getNewSellerSignups()
    {
        try {
            $stmt = $this->db->prepare("
                SELECT 
                    MONTHNAME(created_at) as month, 
                    COUNT(*) as count 
                FROM sellers 
                WHERE YEAR(created_at) = YEAR(CURRENT_DATE())
                GROUP BY MONTH(created_at)
                ORDER BY MONTH(created_at)
            ");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Get new seller signups error: " . $e->getMessage());
            return [];
        }
    }
}

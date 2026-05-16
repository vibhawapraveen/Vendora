<?php

class EarningsModel extends Model
{
    /**
     * Get all-time total earnings for a store
     */
    public function getTotalEarnings($store_id)
    {
        $stmt = $this->db->prepare("
            SELECT 
                SUM(vendor_amount) as total_earned,
                COUNT(id) as total_orders,
                SUM(platform_fee) as total_platform_fee
            FROM payments 
            WHERE store_id = :store_id 
            AND status = 'paid'
        ");
        $stmt->execute(['store_id' => $store_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Get current month earnings for a store
     */
    public function getCurrentMonthEarnings($store_id)
    {
        $stmt = $this->db->prepare("
            SELECT 
                SUM(vendor_amount) as month_total,
                COUNT(id) as month_orders
            FROM payments 
            WHERE store_id = :store_id 
            AND status = 'paid'
            AND MONTH(created_at) = MONTH(CURRENT_DATE())
            AND YEAR(created_at) = YEAR(CURRENT_DATE())
        ");
        $stmt->execute(['store_id' => $store_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Get pending earnings for current month
     */
    public function getPendingMonthEarnings($store_id)
    {
        $stmt = $this->db->prepare("
            SELECT 
                SUM(vendor_amount) as pending_total,
                COUNT(id) as pending_orders
            FROM payments 
            WHERE store_id = :store_id 
            AND status = 'pending'
            AND MONTH(created_at) = MONTH(CURRENT_DATE())
            AND YEAR(created_at) = YEAR(CURRENT_DATE())
        ");
        $stmt->execute(['store_id' => $store_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Get all payment transactions for a store with optional filters
     */
    public function getPaymentTransactions($store_id, $page = 1, $limit = 10, $filters = [])
    {
        $offset = ($page - 1) * $limit;
        
        $sql = "
            SELECT 
                p.*,
                o.order_number,
                c.name as customer_name, 
                c.email as customer_email
            FROM payments p
            LEFT JOIN orders o ON p.order_id = o.id
            LEFT JOIN customers c ON p.customer_id = c.id
            WHERE p.store_id = :store_id
        ";
        
        $params = ['store_id' => $store_id];
        
        // Apply status filter
        if (!empty($filters['status'])) {
            $sql .= " AND p.status = :status";
            $params['status'] = $filters['status'];
        }
        
        // Apply payment method filter
        if (!empty($filters['method'])) {
            $sql .= " AND p.payment_method = :method";
            $params['method'] = $filters['method'];
        }
        
        // Apply date range filter
        if (!empty($filters['from_date'])) {
            $sql .= " AND DATE(p.created_at) >= :from_date";
            $params['from_date'] = $filters['from_date'];
        }
        
        if (!empty($filters['to_date'])) {
            $sql .= " AND DATE(p.created_at) <= :to_date";
            $params['to_date'] = $filters['to_date'];
        }
        
        $sql .= " ORDER BY p.created_at DESC LIMIT :limit OFFSET :offset";
        
        $stmt = $this->db->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue(':' . $key, $value);
        }
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get total count of payment transactions for pagination
     */
    public function getPaymentTransactionsCount($store_id, $filters = [])
    {
        $sql = "SELECT COUNT(*) as total FROM payments WHERE store_id = :store_id";
        
        $params = ['store_id' => $store_id];
        
        if (!empty($filters['status'])) {
            $sql .= " AND status = :status";
            $params['status'] = $filters['status'];
        }
        
        if (!empty($filters['method'])) {
            $sql .= " AND payment_method = :method";
            $params['method'] = $filters['method'];
        }
        
        if (!empty($filters['from_date'])) {
            $sql .= " AND DATE(created_at) >= :from_date";
            $params['from_date'] = $filters['from_date'];
        }
        
        if (!empty($filters['to_date'])) {
            $sql .= " AND DATE(created_at) <= :to_date";
            $params['to_date'] = $filters['to_date'];
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }

    /**
     * Get earnings breakdown by month
     */
    public function getMonthlyBreakdown($store_id, $months = 12)
    {
        $stmt = $this->db->prepare("
            SELECT 
                DATE_FORMAT(created_at, '%Y-%m') as month,
                DATE_FORMAT(created_at, '%b %Y') as month_label,
                SUM(vendor_amount) as earnings,
                COUNT(id) as transactions,
                SUM(amount) as total_revenue
            FROM payments 
            WHERE store_id = :store_id 
            AND status = 'paid'
            AND created_at >= DATE_SUB(CURRENT_DATE(), INTERVAL :months MONTH)
            GROUP BY DATE_FORMAT(created_at, '%Y-%m')
            ORDER BY created_at DESC
        ");
        $stmt->bindValue(':store_id', $store_id);
        $stmt->bindValue(':months', $months, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get payment by ID
     */
    public function getPaymentById($payment_id)
    {
        $stmt = $this->db->prepare("
            SELECT p.*, o.order_number, c.name as customer_name, c.email as customer_email
            FROM payments p
            LEFT JOIN orders o ON p.order_id = o.id
            LEFT JOIN customers c ON p.customer_id = c.id
            WHERE p.id = :id
        ");
        $stmt->execute(['id' => $payment_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
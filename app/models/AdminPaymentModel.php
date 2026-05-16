<?php

class AdminPaymentModel extends Model
{
    /**
     * Get total platform fee for a specific month
     */
    public function getMonthlyRevenue($year, $month)
    {
        try {
            $stmt = $this->db->prepare("
                SELECT SUM(platform_fee) as total 
                FROM payments 
                WHERE YEAR(created_at) = :year 
                AND MONTH(created_at) = :month
                AND status = 'paid'
            ");
            $stmt->execute(['year' => $year, 'month' => $month]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['total'] ?? 0;
        } catch (PDOException $e) {
            error_log("Get monthly revenue error: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Get daily platform fee breakdown for a specific month (for graph)
     */
    public function getDailyRevenue($year, $month)
    {
        try {
            $stmt = $this->db->prepare("
                SELECT DAY(created_at) as day, SUM(platform_fee) as revenue
                FROM payments
                WHERE YEAR(created_at) = :year 
                AND MONTH(created_at) = :month
                AND status = 'paid'
                GROUP BY DAY(created_at)
                ORDER BY day ASC
            ");
            $stmt->execute(['year' => $year, 'month' => $month]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Get daily revenue error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get all platform transactions with additional details (Store, Customer, Order)
     */
    public function getAdminTransactions($filters = [], $page = 1, $limit = 10)
    {
        try {
            $offset = ($page - 1) * $limit;
            
            $sql = "
                SELECT p.*, o.order_number, st.name as store_name, c.name as customer_name, c.email as customer_email
                FROM payments p
                LEFT JOIN orders o ON p.order_id = o.id
                LEFT JOIN stores st ON p.store_id = st.id
                LEFT JOIN customers c ON p.customer_id = c.id
                WHERE 1=1
            ";
            
            $params = [];
            
            if (!empty($filters['status'])) {
                $sql .= " AND p.status = :status";
                $params['status'] = $filters['status'];
            }
            
            if (!empty($filters['method'])) {
                $sql .= " AND p.payment_method = :method";
                $params['method'] = $filters['method'];
            }
            
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
            $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
            
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Get admin transactions error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get total count for admin transactions pagination
     */
    public function getAdminTransactionsCount($filters = [])
    {
        try {
            $sql = "SELECT COUNT(*) as total FROM payments p WHERE 1=1";
            $params = [];
            
            if (!empty($filters['status'])) {
                $sql .= " AND p.status = :status";
                $params['status'] = $filters['status'];
            }
            
            if (!empty($filters['method'])) {
                $sql .= " AND p.payment_method = :method";
                $params['method'] = $filters['method'];
            }
            
            if (!empty($filters['from_date'])) {
                $sql .= " AND DATE(p.created_at) >= :from_date";
                $params['from_date'] = $filters['from_date'];
            }
            
            if (!empty($filters['to_date'])) {
                $sql .= " AND DATE(p.created_at) <= :to_date";
                $params['to_date'] = $filters['to_date'];
            }
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['total'] ?? 0;
        } catch (PDOException $e) {
            error_log("Get admin transactions count error: " . $e->getMessage());
            return 0;
        }
    }
}

<?php

class DashboardModel extends Model
{
    public function getTotalRevenue($store_id)
    {
        $stmt = $this->db->prepare("\n            SELECT IFNULL(SUM(vendor_amount), 0) AS total_revenue\n            FROM payments\n            WHERE store_id = :store_id\n              AND status = 'paid'\n        ");
        $stmt->execute(['store_id' => $store_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Get daily revenue for the last N days (including days with zero revenue)
     */
    public function getDailyRevenueBreakdown($store_id, $days = 30)
    {
        $days = max(1, (int)$days);

        $stmt = $this->db->prepare("
            SELECT
                DATE(created_at) as day_date,
                SUM(vendor_amount) as revenue
            FROM payments
            WHERE store_id = :store_id
            AND status = 'paid'
            AND DATE(created_at) >= DATE_SUB(CURRENT_DATE(), INTERVAL :days DAY)
            GROUP BY DATE(created_at)
            ORDER BY day_date ASC
        ");
        $stmt->bindValue(':store_id', $store_id);
        $stmt->bindValue(':days', $days - 1, PDO::PARAM_INT);
        $stmt->execute();

        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $revenueByDay = [];
        foreach ($rows as $row) {
            $revenueByDay[$row['day_date']] = (float)$row['revenue'];
        }

        $labels = [];
        $values = [];
        $start = new DateTime('-' . ($days - 1) . ' days');
        for ($i = 0; $i < $days; $i++) {
            $dateKey = $start->format('Y-m-d');
            $labels[] = $start->format('M j');
            $values[] = isset($revenueByDay[$dateKey]) ? (float)$revenueByDay[$dateKey] : 0.0;
            $start->modify('+1 day');
        }

        return [
            'labels' => $labels,
            'values' => $values,
        ];
    }

    public function getStatusDistribution($store_id)
    {
        $stmt = $this->db->prepare("
            SELECT status, COUNT(*) as total
            FROM orders
            WHERE store_id = :store_id
            GROUP BY status
        ");
        $stmt->execute(['store_id' => $store_id]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $distribution = [
            'delivered' => 0,
            'shipped' => 0,
            'pending' => 0,
            'cancelled' => 0,
        ];

        foreach ($rows as $row) {
            $status = strtolower((string)$row['status']);
            if (array_key_exists($status, $distribution)) {
                $distribution[$status] = (int)$row['total'];
            }
        }

        return $distribution;
    }
}

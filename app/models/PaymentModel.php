<?php

class PaymentModel extends Model
{
    public function createPayment($payment_data)
    {
        try {
            $stmt = $this->db->prepare("
                INSERT INTO payments (
                    order_id,
                    payment_number,
                    store_id,
                    customer_id,
                    payment_method,
                    stripe_session_id,
                    stripe_payment_intent_id,
                    amount,
                    currency,
                    platform_fee,
                    vendor_amount,
                    status
                ) VALUES (
                    :order_id,
                    :payment_number,
                    :store_id,
                    :customer_id,
                    :payment_method,
                    :stripe_session_id,
                    :stripe_payment_intent_id,
                    :amount,
                    :currency,
                    :platform_fee,
                    :vendor_amount,
                    :status
                )
            ");

            $result = $stmt->execute([
                'order_id' => $payment_data['order_id'],
                'payment_number' => $payment_data['payment_number'],
                'store_id' => $payment_data['store_id'],
                'customer_id' => $payment_data['customer_id'],
                'payment_method' => $payment_data['payment_method'] ?? 'stripe',
                'stripe_session_id' => $payment_data['stripe_session_id'] ?? null,
                'stripe_payment_intent_id' => $payment_data['stripe_payment_intent_id'] ?? null,
                'amount' => $payment_data['amount'],
                'currency' => $payment_data['currency'] ?? 'usd',
                'platform_fee' => $payment_data['platform_fee'] ?? 0,
                'vendor_amount' => $payment_data['vendor_amount'] ?? 0,
                'status' => 'paid'
            ]);

            if ($result) {
                // Get last inserted payment
                $getPayment = $this->db->prepare("SELECT id FROM payments WHERE payment_number = :payment_number LIMIT 1");
                $getPayment->execute(['payment_number' => $payment_data['payment_number']]);
                return $getPayment->fetch(PDO::FETCH_ASSOC);
            }

            return false;
        } catch (Exception $e) {
            error_log("Payment creation error: " . $e->getMessage());
            return false;
        }
    }

    public function getPaymentByStripeSessionId($session_id)
    {
        $stmt = $this->db->prepare("
            SELECT * FROM payments 
            WHERE stripe_session_id = :session_id 
            LIMIT 1
        ");
        $stmt->execute(['session_id' => $session_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updatePaymentStatus($payment_id, $status)
    {
        $stmt = $this->db->prepare("
            UPDATE payments 
            SET status = :status 
            WHERE id = :id
        ");
        return $stmt->execute(['status' => $status, 'id' => $payment_id]);
    }

    public function getPaymentsByStore($store_id, $status = null)
    {
        $sql = "SELECT * FROM payments WHERE store_id = :store_id";
        $params = ['store_id' => $store_id];

        if ($status) {
            $sql .= " AND status = :status";
            $params['status'] = $status;
        }

        $sql .= " ORDER BY created_at DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTotalEarnings($store_id)
    {
        $stmt = $this->db->prepare("
            SELECT 
                SUM(vendor_amount) as total_earnings,
                COUNT(id) as total_transactions,
                SUM(amount) as total_revenue
            FROM payments 
            WHERE store_id = :store_id 
            AND status = 'paid'
        ");
        $stmt->execute(['store_id' => $store_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getMonthlyEarnings($store_id)
    {
        $stmt = $this->db->prepare("
            SELECT 
                DATE_FORMAT(created_at, '%Y-%m') as month,
                SUM(vendor_amount) as earnings,
                COUNT(id) as transactions,
                SUM(amount) as revenue
            FROM payments 
            WHERE store_id = :store_id 
            AND status = 'paid'
            GROUP BY DATE_FORMAT(created_at, '%Y-%m')
            ORDER BY created_at DESC
        ");
        $stmt->execute(['store_id' => $store_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

?>

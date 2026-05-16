<?php

class CheckoutModel extends Model
{
    /**
     * Create a new checkout record to store cart data temporarily
     * This avoids Stripe's 500-character metadata limit
     * 
     * @param array $checkoutData
     * @return array|false The checkout record with id, or false on failure
     */
    public function createCheckout($checkoutData)
    {
        try {
            $checkoutId = uuidv4();
            
            $stmt = $this->db->prepare("
                INSERT INTO checkouts (
                    id, store_id, cart_json, customer_name, 
                    customer_email, address_line1, address_line2, 
                    city, total, status
                ) VALUES (
                    :id, :store_id, :cart_json, :customer_name,
                    :customer_email, :address_line1, :address_line2,
                    :city, :total, :status
                )
            ");
            
            $stmt->execute([
                'id' => $checkoutId,
                'store_id' => $checkoutData['store_id'],
                'cart_json' => $checkoutData['cart_json'],
                'customer_name' => $checkoutData['customer_name'],
                'customer_email' => $checkoutData['customer_email'],
                'address_line1' => $checkoutData['address_line1'],
                'address_line2' => $checkoutData['address_line2'] ?? null,
                'city' => $checkoutData['city'],
                'total' => $checkoutData['total'],
                'status' => 'pending'
            ]);
            
            return $this->getCheckoutById($checkoutId);
        } catch (\Exception $e) {
            error_log("CheckoutModel::createCheckout error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get checkout by ID
     * 
     * @param string $checkoutId
     * @return array|false The checkout record or false if not found
     */
    public function getCheckoutById($checkoutId)
    {
        try {
            $stmt = $this->db->prepare("
                SELECT * FROM checkouts WHERE id = :id
            ");
            
            $stmt->execute(['id' => $checkoutId]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return $result ?: false;
        } catch (\Exception $e) {
            error_log("CheckoutModel::getCheckoutById error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Update checkout status and link to Stripe session
     * 
     * @param string $checkoutId
     * @param string $stripeSessionId
     * @param string $status
     * @return bool Success status
     */
    public function updateCheckoutStatus($checkoutId, $stripeSessionId = null, $status = 'pending')
    {
        try {
            $stmt = $this->db->prepare("
                UPDATE checkouts 
                SET stripe_session_id = :stripe_session_id, status = :status
                WHERE id = :id
            ");
            
            $stmt->execute([
                'id' => $checkoutId,
                'stripe_session_id' => $stripeSessionId,
                'status' => $status
            ]);
            
            return true;
        } catch (\Exception $e) {
            error_log("CheckoutModel::updateCheckoutStatus error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Mark checkout as completed
     * 
     * @param string $checkoutId
     * @return bool Success status
     */
    public function markCompleted($checkoutId)
    {
        return $this->updateCheckoutStatus($checkoutId, null, 'completed');
    }

    /**
     * Get cart data from checkout record
     * Returns the cart as decoded array
     * 
     * @param string $checkoutId
     * @return array|false Decoded cart array or false if not found
     */
    public function getCheckoutCart($checkoutId)
    {
        $checkout = $this->getCheckoutById($checkoutId);
        
        if (!$checkout) {
            return false;
        }
        
        try {
            return json_decode($checkout['cart_json'], true);
        } catch (\Exception $e) {
            error_log("CheckoutModel::getCheckoutCart JSON decode error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Clean up expired checkouts (older than 24 hours)
     * Call this periodically from a cron job
     * 
     * @return int Number of deleted rows
     */
    public function cleanupExpiredCheckouts()
    {
        try {
            $stmt = $this->db->prepare("
                DELETE FROM checkouts 
                WHERE status = 'pending' AND expires_at < NOW()
            ");
            
            $stmt->execute();
            return $stmt->rowCount();
        } catch (\Exception $e) {
            error_log("CheckoutModel::cleanupExpiredCheckouts error: " . $e->getMessage());
            return 0;
        }
    }
}

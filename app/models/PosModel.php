<?php

class PosModel extends Model
{
    public function getProductsByStore($store_id)
    {
        $stmt = $this->db->prepare("
            SELECT 
                p.*,
                c.name AS category_name,
                GROUP_CONCAT(pi.image_url) AS images,
                COALESCE(
                    CASE 
                        WHEN p.is_variant = 1 THEN (
                            SELECT SUM(stock_quantity) 
                            FROM product_variants 
                            WHERE product_id = p.id
                        )
                        ELSE p.stock_quantity
                    END,
                    0
                ) AS total_stock,
                COALESCE(
                    CASE 
                        WHEN p.is_variant = 1 THEN (
                            SELECT MIN(price) 
                            FROM product_variants 
                            WHERE product_id = p.id
                        )
                        ELSE p.price
                    END,
                    0
                ) AS display_price
            FROM products p
            LEFT JOIN categories c ON p.category_id = c.id
            LEFT JOIN product_images pi ON p.id = pi.product_id
            WHERE p.store_id = :store_id
            AND p.delete_flag = 0
            GROUP BY p.id
            ORDER BY p.created_at DESC
        ");

        $stmt->execute(['store_id' => $store_id]);
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($products as &$product) {
            $product['images'] = $product['images']
                ? explode(',', $product['images'])
                : [];
            // Use total_stock instead of stock_quantity for POS display
            $product['stock_quantity'] = (int)$product['total_stock'];
            // Use display_price (min price for variants, regular price for non-variants)
            $product['price'] = (float)$product['display_price'];
            
            // For variant products, get variant options
            if ($product['is_variant'] == 1) {
                $product['variants'] = $this->getVariantsWithAttributes($product['id']);
                $product['attributes'] = $this->getProductAttributes($product['id']);
            }
        }

        unset($product); // ← IMPORTANT: break the reference after foreach &$product

        return $products;
    }

    /**
     * Get all variants with their attribute values
     */
    private function getVariantsWithAttributes($product_id)
    {
        $sql = "SELECT pv.id, pv.sku, pv.price, pv.stock_quantity, pv.image,
                GROUP_CONCAT(pav.value SEPARATOR ' | ') as variant_label,
                GROUP_CONCAT(CONCAT(pa.name, ':', pav.value) SEPARATOR '|') as attributes
                FROM product_variants pv
                LEFT JOIN product_variant_values pvv ON pv.id = pvv.variant_id
                LEFT JOIN product_attribute_values pav ON pvv.attribute_value_id = pav.id
                LEFT JOIN product_attributes pa ON pav.attribute_id = pa.id
                WHERE pv.product_id = :product_id
                GROUP BY pv.id
                ORDER BY pv.created_at ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':product_id', $product_id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get product attributes (e.g., Color, Size)
     */
    private function getProductAttributes($product_id)
    {
        $sql = "SELECT pa.id, pa.name,
                GROUP_CONCAT(pav.id SEPARATOR ',') as value_ids,
                GROUP_CONCAT(pav.value SEPARATOR ',') as attribute_values
                FROM product_attributes pa
                LEFT JOIN product_attribute_values pav ON pa.id = pav.attribute_id
                WHERE pa.product_id = :product_id
                GROUP BY pa.id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':product_id', $product_id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function searchStoreCustomersByMobile($storeId, $query, $limit = 10)
    {
        $trimmed = trim((string)$query);
        if ($trimmed === '') {
            return [];
        }

        $stmt = $this->db->prepare("\n            SELECT
                c.id,
                c.name,
                c.mobile_number,
                c.address_line1,
                c.address_line2,
                c.city
            FROM store_customers sc
            INNER JOIN customers c ON c.id = sc.customer_id
            WHERE sc.store_id = :store_id
              AND c.mobile_number LIKE :mobile
            ORDER BY c.name ASC
            LIMIT :limit
        ");

        $stmt->bindValue(':store_id', $storeId);
        $stmt->bindValue(':mobile', '%' . $trimmed . '%');
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function createPhysicalOrder($storeId, $customerType, $customerInfo, $cart)
    {
        if (empty($cart) || !is_array($cart)) {
            throw new Exception('Cart is empty.');
        }

        $mobile = preg_replace('/\D+/', '', trim((string)($customerInfo['mobile'] ?? '')));
        $name = trim((string)($customerInfo['name'] ?? ''));
        $address1 = trim((string)($customerInfo['address1'] ?? ''));
        $address2 = trim((string)($customerInfo['address2'] ?? ''));
        $city = trim((string)($customerInfo['city'] ?? ''));

        if ($customerType === 'new' && ($mobile === '' || $name === '')) {
            throw new Exception('New customer requires name and mobile number.');
        }

        if ($customerType !== 'new' && $mobile === '') {
            throw new Exception('Existing customer requires a mobile number.');
        }

        if ($mobile !== '' && !preg_match('/^\d{10}$/', $mobile)) {
            throw new Exception('Mobile number must contain exactly 10 digits.');
        }

        $normalizedCart = [];
        foreach ($cart as $item) {
            $productId = $item['productId'] ?? $item['id'] ?? null;
            if (!$productId) {
                throw new Exception('Invalid cart item: missing product id.');
            }

            $normalizedCart[] = [
                'product_id' => $productId,
                'variant_id' => $item['variantId'] ?? null,
                'name' => $item['name'] ?? '',
                'variant_description' => $item['variantDescription'] ?? null,
                'unit_price' => (float)($item['price'] ?? 0),
                'quantity' => (int)($item['qty'] ?? $item['quantity'] ?? 1),
            ];
        }

        $orderId = uuidv4();
        $orderNumber = 'ORD-' . uniqid();
        $paymentNumber = 'PAY-' . uniqid();

        $getCustomerStmt = $this->db->prepare("SELECT * FROM customers WHERE mobile_number = :mobile LIMIT 1");
        $insertCustomerStmt = $this->db->prepare("\n            INSERT INTO customers (name, email, mobile_number, address_line1, address_line2, city)\n            VALUES (:name, :email, :mobile, :address1, :address2, :city)\n        ");

        $insertOrderStmt = $this->db->prepare("\n            INSERT INTO orders (id, store_id, customer_id, order_number, total_amount, status, order_type, address_line1, address_line2, city)\n            VALUES (:id, :store_id, :customer_id, :order_number, :total_amount, :status, :order_type, :address_line1, :address_line2, :city)\n        ");

        $insertOrderItemStmt = $this->db->prepare("\n            INSERT INTO order_items (order_id, product_id, variant_id, product_name, variant_description, unit_price, quantity)\n            VALUES (:order_id, :product_id, :variant_id, :product_name, :variant_description, :unit_price, :quantity)\n        ");

        $getProductStmt = $this->db->prepare("SELECT id, stock_quantity, name FROM products WHERE id = :id AND store_id = :store_id LIMIT 1");
        $getVariantStmt = $this->db->prepare("SELECT id, product_id, stock_quantity FROM product_variants WHERE id = :id LIMIT 1");
        $updateProductStockStmt = $this->db->prepare("UPDATE products SET stock_quantity = :stock WHERE id = :id");
        $updateVariantStockStmt = $this->db->prepare("UPDATE product_variants SET stock_quantity = :stock WHERE id = :id");

        $updateOrderTotalStmt = $this->db->prepare("UPDATE orders SET total_amount = :total_amount WHERE id = :id");

        $insertPaymentStmt = $this->db->prepare("\n            INSERT INTO payments (\n                order_id, payment_number, store_id, customer_id, payment_method,\n                stripe_session_id, stripe_payment_intent_id, amount, currency,\n                platform_fee, vendor_amount, status\n            ) VALUES (\n                :order_id, :payment_number, :store_id, :customer_id, :payment_method,\n                :stripe_session_id, :stripe_payment_intent_id, :amount, :currency,\n                :platform_fee, :vendor_amount, :status\n            )\n        ");

        $upsertStoreCustomerStmt = $this->db->prepare("\n            INSERT INTO store_customers (customer_id, store_id, total_spent)\n            VALUES (:customer_id, :store_id, :total_spent)\n            ON DUPLICATE KEY UPDATE total_spent = total_spent + :total_spent\n        ");

        try {
            $this->db->beginTransaction();

            $getCustomerStmt->execute(['mobile' => $mobile]);
            $customer = $getCustomerStmt->fetch(PDO::FETCH_ASSOC);

            if (!$customer) {
                $insertCustomerStmt->execute([
                    'name' => $name !== '' ? $name : $mobile,
                    'email' => $customerInfo['email'] ?? null,
                    'mobile' => $mobile,
                    'address1' => $address1,
                    'address2' => $address2,
                    'city' => $city,
                ]);

                $getCustomerStmt->execute(['mobile' => $mobile]);
                $customer = $getCustomerStmt->fetch(PDO::FETCH_ASSOC);
            }

            if (!$customer) {
                throw new Exception('Could not create or resolve customer.');
            }

            $insertOrderStmt->execute([
                'id' => $orderId,
                'store_id' => $storeId,
                'customer_id' => $customer['id'],
                'order_number' => $orderNumber,
                'total_amount' => 0,
                'status' => 'delivered',
                'order_type' => 'physical',
                'address_line1' => $address1,
                'address_line2' => $address2,
                'city' => $city,
            ]);

            $total = 0;

            foreach ($normalizedCart as $item) {
                $getProductStmt->execute([
                    'id' => $item['product_id'],
                    'store_id' => $storeId,
                ]);
                $product = $getProductStmt->fetch(PDO::FETCH_ASSOC);

                if (!$product) {
                    throw new Exception('Invalid product in cart.');
                }

                if ($item['quantity'] <= 0) {
                    throw new Exception('Invalid quantity in cart.');
                }

                if ($item['variant_id']) {
                    $getVariantStmt->execute(['id' => $item['variant_id']]);
                    $variant = $getVariantStmt->fetch(PDO::FETCH_ASSOC);

                    if (!$variant || $variant['product_id'] !== $item['product_id']) {
                        throw new Exception('Invalid variant for selected product.');
                    }

                    if ((int)$variant['stock_quantity'] < $item['quantity']) {
                        throw new Exception('Insufficient stock for variant: ' . $item['name']);
                    }

                    $updateVariantStockStmt->execute([
                        'id' => $item['variant_id'],
                        'stock' => (int)$variant['stock_quantity'] - $item['quantity'],
                    ]);
                } else {
                    if ((int)$product['stock_quantity'] < $item['quantity']) {
                        throw new Exception('Insufficient stock for product: ' . $item['name']);
                    }

                    $updateProductStockStmt->execute([
                        'id' => $item['product_id'],
                        'stock' => (int)$product['stock_quantity'] - $item['quantity'],
                    ]);
                }

                $insertOrderItemStmt->execute([
                    'order_id' => $orderId,
                    'product_id' => $item['product_id'],
                    'variant_id' => $item['variant_id'],
                    'product_name' => $item['name'],
                    'variant_description' => $item['variant_description'],
                    'unit_price' => $item['unit_price'],
                    'quantity' => $item['quantity'],
                ]);

                $total += $item['unit_price'] * $item['quantity'];
            }

            $updateOrderTotalStmt->execute([
                'id' => $orderId,
                'total_amount' => $total,
            ]);

            $insertPaymentStmt->execute([
                'order_id' => $orderId,
                'payment_number' => $paymentNumber,
                'store_id' => $storeId,
                'customer_id' => $customer['id'],
                'payment_method' => 'cash',
                'stripe_session_id' => null,
                'stripe_payment_intent_id' => null,
                'amount' => $total,
                'currency' => 'usd',
                'platform_fee' => 0,
                'vendor_amount' => $total,
                'status' => 'paid',
            ]);

            $upsertStoreCustomerStmt->execute([
                'customer_id' => $customer['id'],
                'store_id' => $storeId,
                'total_spent' => $total,
            ]);

            $this->db->commit();

            return [
                'order_id' => $orderId,
                'order_number' => $orderNumber,
            ];
        } catch (Exception $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            throw $e;
        }
    }
}
<?php

class OrderModel extends Model
{
    public function exampleFunction() {}

    public function getCustomerOrders($customerId, $storeId)
    {
        $stmt = $this->db->prepare("
            SELECT 
                o.id,
                o.order_number,
                o.total_amount,
                o.status,
                o.address_line1,
                o.address_line2,
                o.city,
                o.created_at
            FROM orders o
            WHERE o.customer_id = :customer_id 
            AND o.store_id = :store_id
            ORDER BY o.created_at DESC
        ");

        $stmt->execute([
            'customer_id' => $customerId,
            'store_id' => $storeId
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getCustomerOrdersFiltered($customerId, $storeId, $filters)
    {
        $sql = "SELECT 
                o.id,
                o.order_number,
                o.total_amount,
                o.status,
                o.address_line1,
                o.address_line2,
                o.city,
                o.created_at
            FROM orders o
            WHERE o.customer_id = :customer_id 
            AND o.store_id = :store_id";
        // AND created_at BETWEEN :start_date AND :end_date

        $params = ['customer_id' => $customerId, 'store_id' => $storeId];

        if ($filters['start_date']) {
            $sql .= " AND created_at > :start_date";
            $params['start_date'] = $filters['start_date'];
        }
        if ($filters['end_date']) {
            $sql .= " AND created_at < :end_date";
            $params['end_date'] = $filters['end_date'];
        }

        $sql .= " ORDER BY o.created_at DESC";

        $stmt = $this->db->prepare($sql);

        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    public function getOrderItems($orderId)
    {
        $stmt = $this->db->prepare("
            SELECT 
                oi.id,
                oi.product_id,
                oi.variant_id,
                oi.product_name,
                oi.variant_description,
                oi.unit_price,
                oi.quantity,
                oi.subtotal,
                p.description as product_description,
                pv.image as variant_image
            FROM order_items oi
            LEFT JOIN products p ON oi.product_id = p.id
            LEFT JOIN product_variants pv ON oi.variant_id = pv.id
            WHERE oi.order_id = :order_id
            ORDER BY oi.product_name
        ");

        $stmt->execute(['order_id' => $orderId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAllOrdersPaginated($store_id, $page = 1, $limit = 25, $filters = [])
    {
        $offset = ($page - 1) * $limit;

        $sql = "SELECT o.*, c.name as customer_name, c.email as customer_email 
                FROM orders o 
                JOIN customers c ON o.customer_id = c.id 
                JOIN products p ON EXISTS (
                    SELECT 1 FROM order_items oi 
                    WHERE oi.order_id = o.id AND oi.product_id = p.id AND p.store_id = :store_id
                )
                WHERE 1=1";

        $params = ['store_id' => $store_id];

        // Apply filters
        if (!empty($filters['search'])) {
            $sql .= " AND (o.order_number LIKE :search OR c.name LIKE :search OR c.email LIKE :search)";
            $params['search'] = '%' . $filters['search'] . '%';
        }

        if (!empty($filters['status'])) {
            $sql .= " AND o.status = :status";
            $params['status'] = $filters['status'];
        }

        if (!empty($filters['from_date'])) {
            $sql .= " AND DATE(o.created_at) >= :from_date";
            $params['from_date'] = $filters['from_date'];
        }

        if (!empty($filters['to_date'])) {
            $sql .= " AND DATE(o.created_at) <= :to_date";
            $params['to_date'] = $filters['to_date'];
        }

        $sql .= " GROUP BY o.id ORDER BY o.created_at DESC LIMIT :limit OFFSET :offset";

        $stmt = $this->db->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue(':' . $key, $value);
        }
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTotalOrdersCount($store_id, $filters = [])
    {
        $sql = "SELECT COUNT(DISTINCT o.id) as total 
                FROM orders o 
                JOIN customers c ON o.customer_id = c.id 
                JOIN products p ON EXISTS (
                    SELECT 1 FROM order_items oi 
                    WHERE oi.order_id = o.id AND oi.product_id = p.id AND p.store_id = :store_id
                )
                WHERE 1=1";

        $params = ['store_id' => $store_id];

        // Apply same filters as getAllOrdersPaginated
        if (!empty($filters['search'])) {
            $sql .= " AND (o.order_number LIKE :search OR c.name LIKE :search OR c.email LIKE :search)";
            $params['search'] = '%' . $filters['search'] . '%';
        }

        if (!empty($filters['status'])) {
            $sql .= " AND o.status = :status";
            $params['status'] = $filters['status'];
        }

        if (!empty($filters['from_date'])) {
            $sql .= " AND DATE(o.created_at) >= :from_date";
            $params['from_date'] = $filters['from_date'];
        }

        if (!empty($filters['to_date'])) {
            $sql .= " AND DATE(o.created_at) <= :to_date";
            $params['to_date'] = $filters['to_date'];
        }

        $stmt = $this->db->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue(':' . $key, $value);
        }
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }

    public function getTotalOrders($store_id)
    {
        $sql = "SELECT COUNT(DISTINCT o.id) as total 
                FROM orders o 
                JOIN products p ON EXISTS (
                    SELECT 1 FROM order_items oi 
                    WHERE oi.order_id = o.id AND oi.product_id = p.id AND p.store_id = :store_id
                )";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':store_id', $store_id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }

    public function getOrdersByStatus($store_id, $status)
    {
        $sql = "SELECT COUNT(DISTINCT o.id) as total 
                FROM orders o 
                JOIN products p ON EXISTS (
                    SELECT 1 FROM order_items oi 
                    WHERE oi.order_id = o.id AND oi.product_id = p.id AND p.store_id = :store_id
                )
                WHERE o.status = :status";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':store_id', $store_id);
        $stmt->bindParam(':status', $status);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }

    public function getOrderById($order_id, $store_id)
    {
        $sql = "SELECT o.*, c.name as customer_name, c.email as customer_email 
                FROM orders o 
                JOIN customers c ON o.customer_id = c.id 
                WHERE o.id = :order_id 
                AND EXISTS (
                    SELECT 1 FROM order_items oi 
                    JOIN products p ON oi.product_id = p.id 
                    WHERE oi.order_id = o.id AND p.store_id = :store_id
                )";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':order_id', $order_id);
        $stmt->bindParam(':store_id', $store_id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updateOrder($order_id, $data, $store_id)
    {
        // Verify this order belongs to the store
        $order = $this->getOrderById($order_id, $store_id);
        if (!$order) {
            return false;
        }

        $sql = "UPDATE orders SET 
                status = :status,
                total_amount = :total_amount,
                address_line1 = :address_line1,
                address_line2 = :address_line2,
                city = :city,
                updated_at = CURRENT_TIMESTAMP
                WHERE id = :order_id";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':status', $data['status']);
        $stmt->bindParam(':total_amount', $data['total_amount']);
        $stmt->bindParam(':address_line1', $data['address_line1']);
        $stmt->bindParam(':address_line2', $data['address_line2']);
        $stmt->bindParam(':city', $data['city']);
        $stmt->bindParam(':order_id', $order_id);

        return $stmt->execute();
    }

    public function createOrder($orderData, $cart)
    {
        try {
            $orderId = uuidv4();

            $this->db->beginTransaction();

            $stmt = $this->db->prepare("
                INSERT INTO orders (id,store_id,customer_id, order_number, total_amount, address_line1, address_line2, city) 
                VALUES (:id, :store_id, :customer_id, :order_number, :total_amount, :address_line1, :address_line2, :city)
            ");
            $stmt->execute([
                'id' => $orderId,
                'store_id' => $orderData['store_id'],
                'customer_id' => $orderData['customer_id'],
                'order_number' => $orderData['order_number'],
                'total_amount' => 0,
                'address_line1' => $orderData['address_line1'],
                'address_line2' => $orderData['address_line2'],
                'city' => $orderData['city']
            ]);

            $itemStmt = $this->db->prepare("
                INSERT INTO order_items (order_id, product_id, variant_id, product_name, variant_description, unit_price, quantity) 
                VALUES (:order_id, :product_id, :variant_id, :product_name, :variant_description, :unit_price, :quantity)
            ");

            $total = 0;
            $productStmt = $this->db->prepare("SELECT * FROM products WHERE id = :id");
            $updateStockStmt = $this->db->prepare("UPDATE products SET stock_quantity = :stock WHERE id = :id");
            $variantStmt = $this->db->prepare("SELECT * FROM product_variants WHERE id = :id");
            $updateVariantStockStmt = $this->db->prepare("UPDATE product_variants SET stock_quantity = :stock WHERE id = :id");

            foreach ($cart as $item) {

                $productStmt->execute(['id' => $item['id']]);
                $product = $productStmt->fetch(PDO::FETCH_ASSOC);

                // Use price from cart item (what customer paid), not from product
                $unitPrice = isset($item['price']) ? $item['price'] : 0;

                // Handle variant info if present
                $variantId = isset($item['variantId']) ? $item['variantId'] : NULL;
                $variantDesc = isset($item['variantDescription']) ? $item['variantDescription'] : NULL;

                $itemStmt->execute([
                    'order_id' => $orderId,
                    'product_id' => $item['id'],
                    'variant_id' => $variantId,
                    'product_name' => $item['name'],
                    'variant_description' => $variantDesc,
                    'unit_price' => $unitPrice,
                    'quantity' => $item['quantity']
                ]);

                $total += $unitPrice * $item['quantity'];

                // Update stock for variant if variant_id is present
                if ($variantId) {
                    $variantStmt->execute(['id' => $variantId]);
                    $variant = $variantStmt->fetch(PDO::FETCH_ASSOC);

                    if ($variant) {
                        $new_variant_stock = $variant['stock_quantity'] - $item['quantity'];
                        $updateVariantStockStmt->execute(['id' => $variantId, 'stock' => $new_variant_stock]);
                    }
                }

                // Also update main product stock if product exists
                if ($product) {
                    $new_stock = $product['stock_quantity'] - $item['quantity'];
                    $updateStockStmt->execute(['id' => $item['id'], 'stock' => $new_stock]);
                }
            }

            $stmtTotal = $this->db->prepare("
                UPDATE orders SET total_amount = :total_amount WHERE id = :id
            ");
            $stmtTotal->execute([
                'total_amount' => $total,
                'id' => $orderId
            ]);

            $this->db->commit();
            return ['order_id' => $orderId, 'total' => $total];
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function getRecentOrders($store_id, $limit = 5)
    {
        $sql = "SELECT DISTINCT o.*, c.name as customer_name, c.email as customer_email 
                FROM orders o 
                JOIN customers c ON o.customer_id = c.id 
                WHERE EXISTS (
                    SELECT 1 FROM order_items oi 
                    JOIN products p ON oi.product_id = p.id 
                    WHERE oi.order_id = o.id AND p.store_id = :store_id
                )
                ORDER BY o.created_at DESC 
                LIMIT :limit";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':store_id', $store_id);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getOrderDetails($order_id, $store_id)
    {
        // First verify the order belongs to this store
        $order = $this->getOrderById($order_id, $store_id);
        if (!$order) {
            return false;
        }

        // Get order items with product details
        $itemsSql = "SELECT oi.*, p.name as product_name, p.description as product_description
                     FROM order_items oi
                     JOIN products p ON oi.product_id = p.id
                     WHERE oi.order_id = :order_id AND p.store_id = :store_id
                     ORDER BY oi.product_name";

        $itemsStmt = $this->db->prepare($itemsSql);
        $itemsStmt->bindParam(':order_id', $order_id);
        $itemsStmt->bindParam(':store_id', $store_id);
        $itemsStmt->execute();
        $orderItems = $itemsStmt->fetchAll(PDO::FETCH_ASSOC);

        // Get customer details
        $customerSql = "SELECT * FROM customers WHERE id = :customer_id";
        $customerStmt = $this->db->prepare($customerSql);
        $customerStmt->bindParam(':customer_id', $order['customer_id']);
        $customerStmt->execute();
        $customer = $customerStmt->fetch(PDO::FETCH_ASSOC);

        return [
            'order' => $order,
            'items' => $orderItems,
            'customer' => $customer
        ];
    }

    // =============================
    // ADMIN-LEVEL METHODS (all stores)
    // =============================

    public function getAdminAllOrders($filters = [])
    {
        $sql = "SELECT o.*, c.name as customer_name, c.email as customer_email, s.name as store_name
                FROM orders o
                LEFT JOIN customers c ON o.customer_id = c.id
                LEFT JOIN stores s ON o.store_id = s.id
                WHERE 1=1";

        $params = [];

        if (!empty($filters['search'])) {
            $sql .= " AND (o.order_number LIKE :search OR c.name LIKE :search2 OR s.name LIKE :search3)";
            $params['search'] = '%' . $filters['search'] . '%';
            $params['search2'] = '%' . $filters['search'] . '%';
            $params['search3'] = '%' . $filters['search'] . '%';
        }

        if (!empty($filters['status'])) {
            $sql .= " AND o.status = :status";
            $params['status'] = $filters['status'];
        }

        $sql .= " ORDER BY o.created_at DESC";

        $stmt = $this->db->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue(':' . $key, $value);
        }
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAdminOrdersCount($filters = [])
    {
        $sql = "SELECT COUNT(*) as total
                FROM orders o
                LEFT JOIN customers c ON o.customer_id = c.id
                LEFT JOIN stores s ON o.store_id = s.id
                WHERE 1=1";

        $params = [];

        if (!empty($filters['search'])) {
            $sql .= " AND (o.order_number LIKE :search OR c.name LIKE :search2 OR s.name LIKE :search3)";
            $params['search'] = '%' . $filters['search'] . '%';
            $params['search2'] = '%' . $filters['search'] . '%';
            $params['search3'] = '%' . $filters['search'] . '%';
        }

        if (!empty($filters['status'])) {
            $sql .= " AND o.status = :status";
            $params['status'] = $filters['status'];
        }

        $stmt = $this->db->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue(':' . $key, $value);
        }
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }

    public function getAdminOrderStatusCounts()
    {
        $sql = "SELECT 
                    COUNT(*) as total,
                    SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
                    SUM(CASE WHEN status = 'shipped' THEN 1 ELSE 0 END) as shipped,
                    SUM(CASE WHEN status = 'delivered' THEN 1 ELSE 0 END) as delivered,
                    SUM(CASE WHEN status = 'cancelled' THEN 1 ELSE 0 END) as cancelled
                FROM orders";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Get full order details for admin view (no store restriction)
     */
    public function getAdminOrderDetails($order_id)
    {
        try {
            // Get order details with store and customer info
            $sql = "SELECT o.*, c.name as customer_name, c.email as customer_email, s.name as store_name
                    FROM orders o 
                    LEFT JOIN customers c ON o.customer_id = c.id 
                    LEFT JOIN stores s ON o.store_id = s.id
                    WHERE o.id = :order_id";

            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':order_id', $order_id);
            $stmt->execute();
            $order = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$order) {
                return false;
            }

            // Get order items
            $itemsSql = "SELECT oi.*
                         FROM order_items oi
                         WHERE oi.order_id = :order_id
                         ORDER BY oi.product_name";

            $itemsStmt = $this->db->prepare($itemsSql);
            $itemsStmt->bindParam(':order_id', $order_id);
            $itemsStmt->execute();
            $orderItems = $itemsStmt->fetchAll(PDO::FETCH_ASSOC);

            return [
                'order' => $order,
                'items' => $orderItems
            ];
        } catch (PDOException $e) {
            error_log("Get admin order details error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get order counts grouped by date for a specific store over a period of time.
     */
    public function getOrdersOverTime($store_id, $days = 30)
    {
        try {
            $stmt = $this->db->prepare("
                SELECT 
                    DATE(created_at) as date, 
                    COUNT(*) as count 
                FROM orders 
                WHERE store_id = :store_id 
                AND created_at >= DATE_SUB(CURRENT_DATE(), INTERVAL :days DAY)
                GROUP BY DATE(created_at)
                ORDER BY date ASC
            ");
            $stmt->bindValue(':store_id', $store_id);
            $stmt->bindValue(':days', (int)$days, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Get orders over time error: " . $e->getMessage());
            return [];
        }
    }
}


// -- ---------------------------
// -- ORDERS
// -- ---------------------------
// CREATE TABLE orders (
//     id CHAR(36) PRIMARY KEY DEFAULT (UUID()),
//     customer_id CHAR(36) NOT NULL,
//     order_number VARCHAR(100) UNIQUE NOT NULL,
//     total_amount DECIMAL(10,2) NOT NULL,
//     status ENUM('pending','shipped','delivered','cancelled') DEFAULT 'pending',
//     address_line1 VARCHAR(255) NOT NULL,
//     address_line2 VARCHAR(255),
//     city VARCHAR(100) NOT NULL,
//     created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
//     updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
//     FOREIGN KEY (customer_id) REFERENCES customers(id)
// );

// -- ---------------------------
// -- ORDER ITEMS
// -- ---------------------------
// CREATE TABLE order_items (
//     id CHAR(36) PRIMARY KEY DEFAULT (UUID()),
//     order_id CHAR(36) NOT NULL,
//     product_id CHAR(36) NOT NULL,
//     variant_id CHAR(36) NULL,
//     product_name VARCHAR(255) NOT NULL,
//     variant_description VARCHAR(255),
//     unit_price DECIMAL(10,2) NOT NULL,
//     quantity INT NOT NULL,
//     subtotal DECIMAL(10,2) GENERATED ALWAYS AS (unit_price * quantity) STORED,
//     FOREIGN KEY (order_id) REFERENCES orders(id),
//     FOREIGN KEY (product_id) REFERENCES products(id),
//     FOREIGN KEY (variant_id) REFERENCES product_variants(id)
// );
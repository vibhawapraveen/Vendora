<?php

class CustomerModel extends Model
{
    public function exampleFunction() {}

    /**
     * Top customers by total spending (sum of orders.total_amount) for a specific store.
     *
     * Returns rows shaped for dashboard widgets:
     * - customer_id
     * - customer_name
     * - email
     * - mobile_number
     * - total_orders
     * - total_spent
     * - last_order_at
     */
    public function getTopCustomersBySpending($store_id, $limit = 5)
    {
        $limit = (int)$limit;
        if ($limit <= 0) {
            $limit = 5;
        }

        $sql = "
            SELECT
                c.id AS customer_id,
                c.name AS customer_name,
                c.email,
                c.mobile_number,
                COUNT(o.id) AS total_orders,
                IFNULL(SUM(o.total_amount), 0) AS total_spent,
                MAX(o.created_at) AS last_order_at
            FROM customers c
            INNER JOIN store_customers sc ON c.id = sc.customer_id
            INNER JOIN orders o ON c.id = o.customer_id AND o.store_id = :store_id
            WHERE sc.store_id = :store_id
            GROUP BY c.id, c.name, c.email, c.mobile_number
            ORDER BY total_spent DESC
            LIMIT :limit
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':store_id', $store_id, PDO::PARAM_STR);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function checkLogin($email, $password)
    {
        $stmt = $this->db->prepare("SELECT * FROM customers WHERE email = :email LIMIT 1");
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && !empty($user['password_hash']) && password_verify($password, $user['password_hash'])) {
            return $user; // success
        }
        return false; // fail
    }

    public function checkLoginByMobile($mobile, $password)
    {
        $stmt = $this->db->prepare("SELECT * FROM customers WHERE mobile_number = :mobile LIMIT 1");
        $stmt->execute(['mobile' => $mobile]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && !empty($user['password_hash']) && password_verify($password, $user['password_hash'])) {
            return $user; // success
        }
        return false; // fail
    }

    public function getCurrentCustomer()
    {
        $cutomerId = Session::user()['customer_id'] ?? "";
        if (!$cutomerId) {
            return null;
        }
        $stmt = $this->db->prepare("SELECT id, name, email, mobile_number FROM customers WHERE id = :id LIMIT 1");
        $stmt->execute(['id' => $cutomerId]);
        $customer = $stmt->fetch(PDO::FETCH_ASSOC);
        return $customer;
    }

    public function getCustomerByEmail($email)
    {
        $stmt = $this->db->prepare("SELECT id, name, email, mobile_number FROM customers WHERE email = :email LIMIT 1");
        $stmt->execute(['email' => $email]);
        $customer = $stmt->fetch(PDO::FETCH_ASSOC);
        return $customer;
    }

    public function getCustomerByMobile($mobile)
    {
        $stmt = $this->db->prepare("SELECT * FROM customers WHERE mobile_number = :mobile LIMIT 1");
        $stmt->execute(['mobile' => $mobile]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function addNewCustomer($customer_info)
    {
        $stmt = $this->db->prepare("
        INSERT INTO customers (name,email,mobile_number,address_line1,address_line2,city) VALUES
        (:name,:email,:mobile,:address1,:address2,:city);
        ");
        $stmt->execute([
            'name' => $customer_info['name'],
            'email' => $customer_info['email'] ?? null,
            'mobile' => $customer_info['mobile'] ?? null,
            'address1' => $customer_info['address1'] ?? null,
            'address2' => $customer_info['address2'] ?? null,
            'city' => $customer_info['city'] ?? null,
        ]);
        $customer = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!empty($customer_info['mobile'])) {
            return $this->getCustomerByMobile($customer_info['mobile']);
        }

        if (!empty($customer_info['email'])) {
            return $this->getCustomerByEmail($customer_info['email']);
        }

        return false;
    }

    public function upsertCustomerToStore($customer_id, $store_id, $total_spent)
    {
        $stmt = $this->db->prepare("
        INSERT INTO store_customers (customer_id, store_id, total_spent) VALUES
        (:customer_id, :store_id, :total_spent)
        ON DUPLICATE KEY UPDATE total_spent = total_spent + :total_spent;
        ");
        return $stmt->execute(['customer_id' => $customer_id, 'store_id' => $store_id, 'total_spent' => $total_spent]);
    }

    public function createCustomerAccount($name, $email, $password, $mobile = '')
    {
        try {
            $password_hash = password_hash($password, PASSWORD_DEFAULT);

            $stmt = $this->db->prepare("
                INSERT INTO customers (name, email, password_hash, mobile_number, address_line1, address_line2, city) 
                VALUES (:name, :email, :password_hash, :mobile_number, '', '', '')
            ");

            $result = $stmt->execute([
                'name' => $name,
                'email' => $email,
                'password_hash' => $password_hash,
                'mobile_number' => $mobile
            ]);
            
            return $result;
        } catch (PDOException $e) {
            error_log("Customer registration error: " . $e->getMessage());
            return false;
        }
    }

    public function updateCustomerEmailAndPassword($customerId, $name, $email, $password)
    {
        try {
            $password_hash = password_hash($password, PASSWORD_DEFAULT);
            
            $stmt = $this->db->prepare("
                UPDATE customers 
                SET name = :name, email = :email, password_hash = :password_hash 
                WHERE id = :id
            ");
            
            $result = $stmt->execute([
                'id' => $customerId,
                'name' => $name,
                'email' => $email,
                'password_hash' => $password_hash
            ]);

            return $result;
        } catch (PDOException $e) {
            error_log("Customer email/password update error: " . $e->getMessage());
            return false;
        }
    }
    // Get all customers with orders info for a specific store
    public function getAllCustomers($store_id)
    {
        $sql = '
            SELECT 
                c.id AS customer_id,
                c.name AS customer_name,
                c.email,
                c.mobile_number,
                COUNT(o.id) AS total_orders,
                IFNULL(SUM(o.total_amount), 0) AS total_spent,
                DATE_FORMAT(MAX(o.created_at), "%Y-%m-%d") AS last_order_date,
                DATE_FORMAT(MAX(o.created_at), "%H:%i:%s") AS last_order_time
            FROM customers c
            INNER JOIN store_customers sc ON c.id = sc.customer_id
            LEFT JOIN orders o ON c.id = o.customer_id AND o.store_id = :store_id
            WHERE sc.store_id = :store_id
            GROUP BY c.id, c.name, c.email, c.mobile_number
        ';
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':store_id', $store_id, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get customers with pagination and optional search / status filter / sorting for a specific store
    public function getCustomersPaginated($store_id, $page = 1, $perPage = 2, $search = '', $status = '', $orderSort = '', $spentSort = '')
    {
        $offset = ($page - 1) * $perPage;

        $sql = '
            SELECT 
                c.id AS customer_id,
                c.name AS customer_name,
                c.email,
                c.mobile_number,
                COUNT(o.id) AS total_orders,
                IFNULL(SUM(o.total_amount), 0) AS total_spent,
                DATE_FORMAT(MAX(o.created_at), "%Y-%m-%d") AS last_order_date,
                DATE_FORMAT(MAX(o.created_at), "%H:%i:%s") AS last_order_time
            FROM customers c
            INNER JOIN store_customers sc ON c.id = sc.customer_id
            LEFT JOIN orders o ON c.id = o.customer_id AND o.store_id = :store_id
        ';
        $sql .= ' WHERE sc.store_id = :store_id';

        $where = [];
        if (!empty($search)) {
            $where[] = '(c.name LIKE :search OR c.email LIKE :search)';
        }

        // Support a simple blocked/active flag.
        // Assumption: customers table has either `status` (values like 'active'/'blocked')
        // OR `is_blocked` (0/1). We detect which exists at runtime.
        $status = strtolower(trim((string)$status));
        if ($status === 'active' || $status === 'blocked') {
            $hasStatusCol = $this->columnExists('customers', 'status');
            $hasIsBlockedCol = $this->columnExists('customers', 'is_blocked');
            if ($hasStatusCol) {
                $where[] = 'c.status = :status';
            } elseif ($hasIsBlockedCol) {
                $where[] = 'c.is_blocked = :is_blocked';
            }
        }

        if (!empty($where)) {
            $sql .= ' AND ' . implode(' AND ', $where);
        }

        $sql .= ' GROUP BY c.id, c.name, c.email, c.mobile_number';

        // Allow-listed, injection-safe ordering.
        $orderBy = 'c.name ASC';
        if ($spentSort === 'spent_desc') {
            $orderBy = 'total_spent DESC';
        } elseif ($spentSort === 'spent_asc') {
            $orderBy = 'total_spent ASC';
        } elseif ($orderSort === 'orders_desc') {
            $orderBy = 'total_orders DESC';
        } elseif ($orderSort === 'orders_asc') {
            $orderBy = 'total_orders ASC';
        }

        $sql .= ' ORDER BY ' . $orderBy . ' LIMIT :perPage OFFSET :offset';

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':store_id', $store_id, PDO::PARAM_STR);

        if (!empty($search)) {
            $stmt->bindValue(':search', '%' . $search . '%', PDO::PARAM_STR);
        }

        if ($status === 'active' || $status === 'blocked') {
            $hasStatusCol = $this->columnExists('customers', 'status');
            $hasIsBlockedCol = $this->columnExists('customers', 'is_blocked');
            if ($hasStatusCol) {
                $stmt->bindValue(':status', $status, PDO::PARAM_STR);
            } elseif ($hasIsBlockedCol) {
                $stmt->bindValue(':is_blocked', $status === 'blocked' ? 1 : 0, PDO::PARAM_INT);
            }
        }
        $stmt->bindValue(':perPage', (int)$perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get total customers count for a specific store
    public function getTotalCustomers($store_id, $search = '', $status = '')
    {
        $sql = 'SELECT COUNT(DISTINCT c.id) AS total_customers FROM customers c INNER JOIN store_customers sc ON c.id = sc.customer_id WHERE sc.store_id = :store_id';

        $where = [];
        if (!empty($search)) {
            $where[] = '(c.name LIKE :search OR c.email LIKE :search)';
        }

        $status = strtolower(trim((string)$status));
        if ($status === 'active' || $status === 'blocked') {
            $hasStatusCol = $this->columnExists('customers', 'status');
            $hasIsBlockedCol = $this->columnExists('customers', 'is_blocked');
            if ($hasStatusCol) {
                $where[] = 'c.status = :status';
            } elseif ($hasIsBlockedCol) {
                $where[] = 'c.is_blocked = :is_blocked';
            }
        }

        if (!empty($where)) {
            $sql .= ' AND ' . implode(' AND ', $where);
        }

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':store_id', $store_id, PDO::PARAM_STR);

        if (!empty($search)) {
            $stmt->bindValue(':search', '%' . $search . '%', PDO::PARAM_STR);
        }

        if ($status === 'active' || $status === 'blocked') {
            $hasStatusCol = $this->columnExists('customers', 'status');
            $hasIsBlockedCol = $this->columnExists('customers', 'is_blocked');
            if ($hasStatusCol) {
                $stmt->bindValue(':status', $status, PDO::PARAM_STR);
            } elseif ($hasIsBlockedCol) {
                $stmt->bindValue(':is_blocked', $status === 'blocked' ? 1 : 0, PDO::PARAM_INT);
            }
        }

        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    private function columnExists($table, $column)
    {
        try {
            $sql = "SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = :t AND COLUMN_NAME = :c LIMIT 1";
            $stmt = $this->db->prepare($sql);
            $stmt->execute(['t' => $table, 'c' => $column]);
            return (bool)$stmt->fetchColumn();
        } catch (Exception $e) {
            // If metadata access is restricted, don't break the page.
            return false;
        }
    }

    // Get new customers count for current month for a specific store
    public function getNewCustomersThisMonth($store_id)
    {
        $sql = "
            SELECT COUNT(DISTINCT sc.customer_id) AS new_customers
            FROM store_customers sc
            INNER JOIN customers c ON sc.customer_id = c.id
            WHERE sc.store_id = :store_id
              AND MONTH(c.created_at) = MONTH(CURRENT_DATE())
              AND YEAR(c.created_at) = YEAR(CURRENT_DATE())
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':store_id', $store_id, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Get total revenue from orders for a specific store
    public function getTotalRevenue($store_id)
    {
        $sql = '
            SELECT IFNULL(SUM(total_amount), 0) AS total_revenue
            FROM orders
            WHERE store_id = :store_id
        ';
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':store_id', $store_id, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public function getCustomerById($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM customers WHERE id = :id LIMIT 1");
        $stmt->bindValue(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getAdminCustomers($search = '')
    {
        $sql = "
            SELECT c.id, c.name as customer_name, c.email, c.mobile_number,
                   COUNT(o.id) as total_orders,
                   IFNULL(SUM(o.total_amount), 0) as total_spent,
                   IFNULL(MAX(o.created_at), 'Never') as last_order
            FROM customers c
            LEFT JOIN orders o ON c.id = o.customer_id
            WHERE c.name LIKE :search OR c.email LIKE :search
            GROUP BY c.id
            ORDER BY c.created_at ASC
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['search' => '%' . $search . '%']);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getCustomerOrders($customerId)
    {
        $stmt = $this->db->prepare("
            SELECT id, order_number, store_id, status, total_amount, created_at 
            FROM orders 
            WHERE customer_id = :customer_id 
            ORDER BY created_at DESC
        ");
        $stmt->execute(['customer_id' => $customerId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

<?php

class SellerModel extends Model
{
    public function getAdminSellers($search = '')
    {
        try {
            $query = "SELECT id, name, email, mobile_number, profile_picture, created_at FROM sellers";
            $params = [];

            if (!empty($search)) {
                $query .= " WHERE name LIKE :search OR email LIKE :search";
                $params['search'] = '%' . $search . '%';
            }

            $query .= " ORDER BY created_at DESC";

            $stmt = $this->db->prepare($query);
            $stmt->execute($params);

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Get admin sellers error: " . $e->getMessage());
            return [];
        }
    }

    public function deleteSeller($id)
    {
        try {
            $this->db->beginTransaction();

            $stmt = $this->db->prepare("SELECT id FROM stores WHERE seller_id = :seller_id");
            $stmt->execute(['seller_id' => $id]);
            $stores = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($stores as $store) {
                $storeId = $store['id'];

                // 1. Delete platform/technical associations
                $this->db->prepare("DELETE FROM store_views WHERE store_id = :store_id")->execute(['store_id' => $storeId]);
                $this->db->prepare("DELETE FROM checkouts WHERE store_id = :store_id")->execute(['store_id' => $storeId]);
                $this->db->prepare("DELETE FROM template_beam_featured_products WHERE store_id = :store_id")->execute(['store_id' => $storeId]);
                $this->db->prepare("DELETE FROM categories WHERE store_id = :store_id")->execute(['store_id' => $storeId]);
                
                // 2. Delete financial records
                $this->db->prepare("DELETE FROM payments WHERE store_id = :store_id")->execute(['store_id' => $storeId]);

                // 3. Delete order-related data
                $this->db->prepare("DELETE FROM order_items WHERE order_id IN (SELECT id FROM orders WHERE store_id = :store_id)")->execute(['store_id' => $storeId]);
                $this->db->prepare("DELETE FROM orders WHERE store_id = :store_id")->execute(['store_id' => $storeId]);

                // 4. Delete product-related data (variants, images, attributes)
                $this->db->prepare("DELETE FROM product_images WHERE product_id IN (SELECT id FROM products WHERE store_id = :store_id)")->execute(['store_id' => $storeId]);
                $this->db->prepare("DELETE FROM product_variant_values WHERE variant_id IN (SELECT id FROM product_variants WHERE product_id IN (SELECT id FROM products WHERE store_id = :store_id))")->execute(['store_id' => $storeId]);
                $this->db->prepare("DELETE FROM product_variants WHERE product_id IN (SELECT id FROM products WHERE store_id = :store_id)")->execute(['store_id' => $storeId]);
                $this->db->prepare("DELETE FROM product_attribute_values WHERE attribute_id IN (SELECT id FROM product_attributes WHERE product_id IN (SELECT id FROM products WHERE store_id = :store_id))")->execute(['store_id' => $storeId]);
                $this->db->prepare("DELETE FROM product_attributes WHERE product_id IN (SELECT id FROM products WHERE store_id = :store_id)")->execute(['store_id' => $storeId]);
                $this->db->prepare("DELETE FROM products WHERE store_id = :store_id")->execute(['store_id' => $storeId]);

                // 5. Delete store content and customers
                $this->db->prepare("DELETE FROM store_contents WHERE store_id = :store_id")->execute(['store_id' => $storeId]);
                $this->db->prepare("DELETE FROM store_customers WHERE store_id = :store_id")->execute(['store_id' => $storeId]);
            }

            // 6. Finally delete the stores and the seller
            $this->db->prepare("DELETE FROM stores WHERE seller_id = :seller_id")->execute(['seller_id' => $id]);

            $stmt = $this->db->prepare("DELETE FROM sellers WHERE id = :id");
            $result = $stmt->execute(['id' => $id]);

            $this->db->commit();
            return $result;
        } catch (PDOException $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            error_log("Delete seller cascading error: " . $e->getMessage());
            return false;
        }
    }

    public function getSellerDetailsById($id)
    {
        try {
            $stmt = $this->db->prepare("
                SELECT s.id, s.name, s.email, s.mobile_number, s.profile_picture, s.created_at, st.name as store_name
                FROM sellers s
                LEFT JOIN stores st ON st.seller_id = s.id
                WHERE s.id = :id LIMIT 1
            ");
            $stmt->execute(['id' => $id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Get seller details error: " . $e->getMessage());
            return false;
        }
    }

    public function getSellerProducts($sellerId)
    {
        try {
            $stmt = $this->db->prepare("
                SELECT p.id, p.name, p.price, p.stock_quantity, p.created_at 
                FROM products p
                JOIN stores s ON p.store_id = s.id
                WHERE s.seller_id = :seller_id
                ORDER BY p.created_at DESC
            ");
            $stmt->execute(['seller_id' => $sellerId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Get seller products error: " . $e->getMessage());
            return [];
        }
    }

    public function registerSeller($email, $password, $name, $mobile, $storeName)
    {
        try {
            $sellerId = uuidv4();
            $storeId = uuidv4();

            // Hash the password
            $passwordHash = password_hash($password, PASSWORD_DEFAULT);

            // Insert seller
            $stmt = $this->db->prepare("INSERT INTO sellers (id, name, email, mobile_number, password_hash) VALUES (:id, :name, :email, :mobile, :password_hash)");
            $stmt->execute([
                'id' => $sellerId,
                'name' => $name,
                'email' => $email,
                'mobile' => $mobile,
                'password_hash' => $passwordHash
            ]);

            // Create default store for this seller
            $storeStmt = $this->db->prepare("INSERT INTO stores (id, seller_id, name) VALUES (:id, :seller_id, :name)");
            $storeStmt->execute([
                'id' => $storeId,
                'seller_id' => $sellerId,
                'name' => $storeName,
            ]);

            $user = [
                'id' => $sellerId,
                'name' => $name,
                'email' => $email,
                'mobile_number' => $mobile,
                'profile_picture' => '',
                'store_id' => $storeId,
                'store_name' => $storeName,
                'role' => 'seller'
            ];

            return $user;
        } catch (PDOException $e) {
            // Check for duplicate email
            if ($e->getCode() == 23000) {
                return false;
            }
            throw $e;
        }
    }

    public function checkLogin($email, $password)
    {
        $stmt = $this->db->prepare("SELECT * FROM sellers WHERE email = :email LIMIT 1");
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password_hash'])) {
            // fetch store for this seller
            $storeStmt = $this->db->prepare("SELECT id, name FROM stores WHERE seller_id = :seller_id LIMIT 1");
            $storeStmt->execute(['seller_id' => $user['id']]);
            $store = $storeStmt->fetch(PDO::FETCH_ASSOC);

            // attach store info to user array
            if ($store) {
                $user['store_id'] = $store['id'];
                $user['store_name'] = $store['name'];
            } else {
                $user['store_id'] = null;
                $user['store_name'] = null;
            }
            return $user; // success
        }
        return false; // fail
    }

    public function saveStripeAccount($sellerId, $stripeAccountId)
    {
        $stmt = $this->db->prepare("UPDATE sellers
                SET stripe_account_id = :account_id
                WHERE id = :seller_id");

        $stmt->execute([
            ':account_id' => $stripeAccountId,
            ':seller_id' => $sellerId
        ]);
    }

    public function getStripeAccount($sellerId)
    {
        $stmt = $this->db->prepare("SELECT stripe_account_id FROM sellers
                WHERE id = :seller_id");

        $stmt->execute([
            ':seller_id' => $sellerId
        ]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public function getSellerById($id)
    {
        $stmt = $this->db->prepare("SELECT id, name, email, mobile_number, profile_picture, created_at FROM sellers WHERE id = :id LIMIT 1");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updateSellerProfile($id, $name, $email, $mobile_number)
    {
        $stmt = $this->db->prepare("UPDATE sellers SET name = :name, email = :email, mobile_number = :mobile_number WHERE id = :id");
        return $stmt->execute(['name' => $name, 'email' => $email, 'mobile_number' => $mobile_number, 'id' => $id]);
    }

    public function updateSellerProfilePicture($id, $picturePath)
    {
        $stmt = $this->db->prepare("UPDATE sellers SET profile_picture = :picture WHERE id = :id");
        return $stmt->execute(['picture' => $picturePath, 'id' => $id]);
    }

    public function updateStoreName($storeId, $name)
    {
        $stmt = $this->db->prepare("UPDATE stores SET name = :name WHERE id = :id");
        return $stmt->execute(['name' => $name, 'id' => $storeId]);
    }

    public function updateSellerPassword($id, $currentPassword, $newPassword)
    {
        $stmt = $this->db->prepare("SELECT password_hash FROM sellers WHERE id = :id LIMIT 1");
        $stmt->execute(['id' => $id]);
        $seller = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$seller || !password_verify($currentPassword, $seller['password_hash'])) {
            return false;
        }

        $newHash = password_hash($newPassword, PASSWORD_DEFAULT);
        $stmt = $this->db->prepare("UPDATE sellers SET password_hash = :hash WHERE id = :id");
        return $stmt->execute(['hash' => $newHash, 'id' => $id]);
    }

    public function disconnectStripeAccount($seller_id)
    {
        $stmt = $this->db->prepare("UPDATE sellers SET stripe_account_id = NULL WHERE id = :seller_id");
        $stmt->execute(['seller_id' => $seller_id]);
    }
}

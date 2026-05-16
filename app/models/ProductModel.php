<?php

class ProductModel extends Model
{
    public function createBasicProduct($data)
    {
        // Use the uuidv4() function from Utils.php
        $uuid = uuidv4();

        $sql = "INSERT INTO products (id, store_id, name, description, category_id, is_variant, visibility) 
            VALUES (:id, :store_id, :name, :description, :category_id, :is_variant, :visibility)";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $uuid);
        $stmt->bindParam(':store_id', $data['store_id']);
        $stmt->bindParam(':name', $data['name']);
        $stmt->bindParam(':description', $data['description']);
        $stmt->bindValue(':category_id', $data['category_id'], $data['category_id'] === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
        $stmt->bindParam(':is_variant', $data['is_variant']);
        $stmt->bindParam(':visibility', $data['visibility']);

        if ($stmt->execute()) {
            return $uuid;
        }
        return false;
    }



    public function updateProductPricing($product_id, $price, $stock_quantity, $low_stock_alert)
    {
        $sql = "UPDATE products SET price = :price, stock_quantity = :stock_quantity, low_stock_alert = :low_stock_alert 
                WHERE id = :product_id";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':price', $price);
        $stmt->bindParam(':stock_quantity', $stock_quantity);
        $stmt->bindParam(':low_stock_alert', $low_stock_alert);
        $stmt->bindParam(':product_id', $product_id);

        $result = $stmt->execute();

        if ($result) {
            $product = $this->getProductById($product_id);
            if ($product) {
                $threshold = ($low_stock_alert !== null && $low_stock_alert !== '')
                    ? (int)$low_stock_alert
                    : 10;
                $this->syncStockAlert($product['store_id'], $product_id, null, (int)$stock_quantity, $threshold);
            }
        }

        return $result;
    }

    public function createProduct($data)
    {
        $sql = "INSERT INTO products (store_id, name, description, price, stock_quantity, visibility) 
                VALUES (:store_id, :name, :description, :price, :stock_quantity, :visibility)";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':store_id', $data['store_id']);
        $stmt->bindParam(':name', $data['name']);
        $stmt->bindParam(':description', $data['description']);
        $stmt->bindParam(':price', $data['price']);
        $stmt->bindParam(':stock_quantity', $data['stock_quantity']);
        $stmt->bindParam(':visibility', $data['visibility']);

        return $stmt->execute();
    }

    public function getAllProducts($store_id)
    {
        $sql = "SELECT p.*, c.name AS category_name
                FROM products p
                LEFT JOIN categories c ON c.id = p.category_id
                WHERE p.store_id = :store_id
                AND p.delete_flag = 0
                ORDER BY p.created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':store_id', $store_id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getProductById($id)
    {
        $sql = "SELECT * FROM products WHERE id = :id AND delete_flag = 0";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updateStock($product_id, $new_stock)
    {
        $sql = "UPDATE products SET stock_quantity = :stock WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':stock', $new_stock);
        $stmt->bindParam(':id', $product_id);
        $result = $stmt->execute();

        if ($result) {
            $product = $this->getProductById($product_id);
            if ($product) {
                $threshold = isset($product['low_stock_alert']) && $product['low_stock_alert'] !== null
                    ? (int)$product['low_stock_alert']
                    : 10;
                $this->syncStockAlert($product['store_id'], $product_id, null, (int)$new_stock, $threshold);
            }
        }

        return $result;
    }

    public function getTotalProducts($store_id)
    {
        $sql = "SELECT COUNT(*) as total FROM products WHERE store_id = :store_id AND delete_flag = 0";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':store_id', $store_id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }

    public function getActiveProducts($store_id)
    {
        $sql = "SELECT COUNT(*) as active FROM products WHERE store_id = :store_id AND visibility = 1 AND delete_flag = 0";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':store_id', $store_id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC)['active'];
    }

    public function getInactiveProducts($store_id)
    {
        $sql = "SELECT COUNT(*) as active FROM products WHERE store_id = :store_id AND visibility = 0 AND delete_flag = 0";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':store_id', $store_id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC)['active'];
    }

    public function getLowStockProducts($store_id, $threshold = 10)
    {
        // Count low stock single products (non-variant)
        // Use individual low_stock_alert if set, otherwise use default threshold
        $sql = "SELECT COUNT(*) as low_stock 
                FROM products 
                WHERE store_id = :store_id 
                AND stock_quantity IS NOT NULL 
                AND stock_quantity < COALESCE(low_stock_alert, :threshold)
                AND visibility = 1 
            AND delete_flag = 0
                AND is_variant = 0";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':store_id', $store_id);
        $stmt->bindParam(':threshold', $threshold, PDO::PARAM_INT);
        $stmt->execute();
        $singleProductsCount = $stmt->fetch(PDO::FETCH_ASSOC)['low_stock'] ?? 0;

        // Count low stock variant products (any variant below its threshold)
        // Use individual variant low_stock_alert if set, otherwise use default threshold
        $sql = "SELECT COUNT(DISTINCT p.id) as low_stock 
                FROM products p
                INNER JOIN product_variants pv ON p.id = pv.product_id
                WHERE p.store_id = :store_id 
                AND pv.stock_quantity < COALESCE(pv.low_stock_alert, :threshold)
                AND p.visibility = 1 
                AND p.delete_flag = 0
                AND p.is_variant = 1";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':store_id', $store_id);
        $stmt->bindParam(':threshold', $threshold, PDO::PARAM_INT);
        $stmt->execute();
        $variantProductsCount = $stmt->fetch(PDO::FETCH_ASSOC)['low_stock'] ?? 0;

        return $singleProductsCount + $variantProductsCount;
    }

    public function getLowStockProductsList($store_id, $threshold = 10)
    {
        $lowStockProducts = [];

        // Get single products with low stock
        // Use individual low_stock_alert if set, otherwise use default threshold
        $sql = "SELECT id, name, stock_quantity, low_stock_alert, is_variant 
                FROM products 
                WHERE store_id = :store_id 
                AND stock_quantity IS NOT NULL 
                AND stock_quantity < COALESCE(low_stock_alert, :threshold)
                AND visibility = 1 
            AND delete_flag = 0
                AND is_variant = 0 
                ORDER BY stock_quantity ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':store_id', $store_id);
        $stmt->bindParam(':threshold', $threshold, PDO::PARAM_INT);
        $stmt->execute();
        $singleProducts = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Get variant products with any variant below its threshold
        // Use individual variant low_stock_alert if set, otherwise use default threshold
        $sql = "SELECT DISTINCT p.id, p.name, p.is_variant, MIN(pv.stock_quantity) as stock_quantity
                FROM products p
                INNER JOIN product_variants pv ON p.id = pv.product_id
                WHERE p.store_id = :store_id 
                AND pv.stock_quantity < COALESCE(pv.low_stock_alert, :threshold)
                AND p.visibility = 1 
                AND p.delete_flag = 0
                AND p.is_variant = 1
                GROUP BY p.id, p.name, p.is_variant
                ORDER BY stock_quantity ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':store_id', $store_id);
        $stmt->bindParam(':threshold', $threshold, PDO::PARAM_INT);
        $stmt->execute();
        $variantProducts = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Merge both arrays
        $lowStockProducts = array_merge($singleProducts, $variantProducts);

        // Sort by stock_quantity
        usort($lowStockProducts, function ($a, $b) {
            return $a['stock_quantity'] <=> $b['stock_quantity'];
        });

        return $lowStockProducts;
    }

    public function getTotalValue($store_id)
    {
        // Calculate total value from single products (non-variant)
        $sql = "SELECT SUM(price * stock_quantity) as total_value 
                FROM products 
                WHERE store_id = :store_id 
                AND visibility = 1 
            AND delete_flag = 0
                AND is_variant = 0";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':store_id', $store_id);
        $stmt->execute();
        $singleProductsValue = $stmt->fetch(PDO::FETCH_ASSOC)['total_value'] ?? 0;

        // Calculate total value from variant products
        $sql = "SELECT SUM(pv.price * pv.stock_quantity) as total_value 
                FROM product_variants pv
                INNER JOIN products p ON pv.product_id = p.id
                WHERE p.store_id = :store_id 
                AND p.visibility = 1 
                AND p.delete_flag = 0
                AND p.is_variant = 1";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':store_id', $store_id);
        $stmt->execute();
        $variantProductsValue = $stmt->fetch(PDO::FETCH_ASSOC)['total_value'] ?? 0;

        return $singleProductsValue + $variantProductsValue;
    }

    public function getProductImages($product_id)
    {
        $sql = "SELECT * FROM product_images WHERE product_id = :product_id ORDER BY created_at ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':product_id', $product_id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getFirstProductImage($product_id, $is_variant = false)
    {
        // Always prefer product-level main thumbnail first.
        $sql = "SELECT image_url FROM product_images WHERE product_id = :product_id ORDER BY created_at ASC LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':product_id', $product_id);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result && !empty($result['image_url'])) {
            return $result['image_url'];
        }

        // Fallback: for variant products without product-level thumbnail, use first variant image.
        if ($is_variant) {
            $variantSql = "SELECT image FROM product_variants WHERE product_id = :product_id AND image IS NOT NULL LIMIT 1";
            $variantStmt = $this->db->prepare($variantSql);
            $variantStmt->bindParam(':product_id', $product_id);
            $variantStmt->execute();
            $variantResult = $variantStmt->fetch(PDO::FETCH_ASSOC);
            return $variantResult ? $variantResult['image'] : null;
        }

        return null;
    }

    public function getRecentProducts($store_id, $limit = 5)
    {
        $sql = "SELECT * FROM products WHERE store_id = :store_id AND delete_flag = 0 ORDER BY created_at DESC LIMIT :limit";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':store_id', $store_id);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getRecentOrders($store_id, $limit = 5)
    {
        $sql = "SELECT o.*, c.name as customer_name, c.email as customer_email,
                (
                    SELECT oi.product_name
                    FROM order_items oi
                    JOIN products p ON p.id = oi.product_id
                    WHERE oi.order_id = o.id AND p.store_id = :store_id
                    ORDER BY oi.id ASC
                    LIMIT 1
                ) as product_name,
                (
                    SELECT COALESCE(
                        pv.image,
                        (
                            SELECT pi.image_url
                            FROM product_images pi
                            WHERE pi.product_id = p.id
                            ORDER BY pi.created_at ASC
                            LIMIT 1
                        )
                    )
                    FROM order_items oi
                    JOIN products p ON p.id = oi.product_id
                    LEFT JOIN product_variants pv ON pv.id = oi.variant_id
                    WHERE oi.order_id = o.id AND p.store_id = :store_id
                    ORDER BY oi.id ASC
                    LIMIT 1
                ) as product_image
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

    public function getProductsPaginated($store_id, $page = 1, $limit = 5)
    {
        $offset = ($page - 1) * $limit;
        $sql = "SELECT p.*, c.name AS category_name
            FROM products p
            LEFT JOIN categories c ON c.id = p.category_id
            WHERE p.store_id = :store_id
            AND p.delete_flag = 0
            ORDER BY p.created_at DESC
            LIMIT :limit OFFSET :offset";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':store_id', $store_id);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function deleteProduct($product_id)
    {
        // Soft delete the product so historical relations (orders, variants, images) remain intact.
        $sql = "UPDATE products
                SET delete_flag = 1,
                    updated_at = CURRENT_TIMESTAMP
                WHERE id = :product_id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':product_id', $product_id);
        return $stmt->execute();
    }

    public function updateProduct($product_id, $data)
    {
        $existingProduct = $this->getProductById($product_id);

        // Keep existing pricing/stock fields when updating variant products from forms
        // that only send name/description/visibility.
        if (!array_key_exists('price', $data)) {
            $data['price'] = $existingProduct['price'] ?? null;
        }
        if (!array_key_exists('stock_quantity', $data)) {
            $data['stock_quantity'] = $existingProduct['stock_quantity'] ?? null;
        }
        if (!array_key_exists('low_stock_alert', $data)) {
            $data['low_stock_alert'] = $existingProduct['low_stock_alert'] ?? null;
        }

        $sql = "UPDATE products SET 
                name = :name, 
                description = :description, 
                price = :price, 
                stock_quantity = :stock_quantity, 
                low_stock_alert = :low_stock_alert,
                visibility = :visibility,
                updated_at = CURRENT_TIMESTAMP
                WHERE id = :product_id";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':name', $data['name']);
        $stmt->bindParam(':description', $data['description']);
        $stmt->bindParam(':price', $data['price']);
        $stmt->bindParam(':stock_quantity', $data['stock_quantity']);
        $stmt->bindParam(':low_stock_alert', $data['low_stock_alert']);
        $stmt->bindParam(':visibility', $data['visibility']);
        $stmt->bindParam(':product_id', $product_id);

        $result = $stmt->execute();

        if ($result) {
            $product = $this->getProductById($product_id);
            if (
                $product
                && (int)($product['is_variant'] ?? 0) === 0
                && isset($data['stock_quantity'])
                && $data['stock_quantity'] !== null
                && $data['stock_quantity'] !== ''
            ) {
                $threshold = isset($data['low_stock_alert']) && $data['low_stock_alert'] !== null && $data['low_stock_alert'] !== ''
                    ? (int)$data['low_stock_alert']
                    : (isset($product['low_stock_alert']) && $product['low_stock_alert'] !== null ? (int)$product['low_stock_alert'] : 10);
                $this->syncStockAlert($product['store_id'], $product_id, null, (int)$data['stock_quantity'], $threshold);
            }
        }

        return $result;
    }

    public function updateProductVisibility($product_id, $visibility)
    {
        $sql = "UPDATE products SET visibility = :visibility, updated_at = CURRENT_TIMESTAMP WHERE id = :product_id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':visibility', $visibility);
        $stmt->bindParam(':product_id', $product_id);
        return $stmt->execute();
    }

    public function addProductImage($product_id, $image_url)
    {
        $uuid = uuidv4();

        // Debug logging
        error_log("Adding product image - UUID: $uuid, Product ID: $product_id, Image URL: $image_url");

        $sql = "INSERT INTO product_images (id, product_id, image_url) VALUES (:id, :product_id, :image_url)";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $uuid);
        $stmt->bindParam(':product_id', $product_id);
        $stmt->bindParam(':image_url', $image_url);

        $result = $stmt->execute();

        if (!$result) {
            error_log("SQL Error in addProductImage: " . print_r($stmt->errorInfo(), true));
        } else {
            error_log("Successfully inserted image into database");
        }

        return $result;
    }

    public function setMainProductImage($product_id, $image_url)
    {
        // Main thumbnail is stored as the first image row for the product.
        $sql = "SELECT id, image_url FROM product_images WHERE product_id = :product_id ORDER BY created_at ASC LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':product_id', $product_id);
        $stmt->execute();
        $existing = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($existing) {
            $updateSql = "UPDATE product_images SET image_url = :image_url WHERE id = :image_id";
            $updateStmt = $this->db->prepare($updateSql);
            $updateStmt->bindParam(':image_url', $image_url);
            $updateStmt->bindParam(':image_id', $existing['id']);
            $updated = $updateStmt->execute();

            if ($updated && !empty($existing['image_url']) && $existing['image_url'] !== $image_url) {
                $oldPath = dirname(__DIR__, 2) . '/public/' . ltrim(str_replace('\\', '/', $existing['image_url']), '/');
                if (file_exists($oldPath)) {
                    @unlink($oldPath);
                }
            }

            return $updated;
        }

        return $this->addProductImage($product_id, $image_url);
    }

    public function deleteProductImage($image_id)
    {
        // Get image URL before deleting for file cleanup
        $sql = "SELECT image_url FROM product_images WHERE id = :image_id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':image_id', $image_id);
        $stmt->execute();
        $image = $stmt->fetch(PDO::FETCH_ASSOC);

        // Delete from database
        $sql = "DELETE FROM product_images WHERE id = :image_id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':image_id', $image_id);
        $result = $stmt->execute();

        // Optionally delete physical file
        if ($result && $image && file_exists('../public/' . $image['image_url'])) {
            @unlink('../public/' . $image['image_url']);
        }

        return $result;
    }

    public function deleteProductImageForProduct($product_id, $image_id)
    {
        // Delete image only if it belongs to the given product.
        $sql = "SELECT image_url FROM product_images WHERE id = :image_id AND product_id = :product_id LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':image_id', $image_id);
        $stmt->bindParam(':product_id', $product_id);
        $stmt->execute();
        $image = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$image) {
            return false;
        }

        $deleteSql = "DELETE FROM product_images WHERE id = :image_id AND product_id = :product_id";
        $deleteStmt = $this->db->prepare($deleteSql);
        $deleteStmt->bindParam(':image_id', $image_id);
        $deleteStmt->bindParam(':product_id', $product_id);
        $result = $deleteStmt->execute();

        if ($result && !empty($image['image_url'])) {
            $publicPath = dirname(__DIR__, 2) . '/public/' . ltrim(str_replace('\\', '/', $image['image_url']), '/');
            if (file_exists($publicPath)) {
                @unlink($publicPath);
            }
        }

        return $result;
    }

    // attribute methods
    public function saveProductAttributes($product_id, $attribute_name)
    {
        $uuid = uuidv4();

        $sql = "INSERT INTO product_attributes (id, product_id, name) 
                VALUES (:id, :product_id, :name)";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $uuid);
        $stmt->bindParam(':product_id', $product_id);
        $stmt->bindParam(':name', $attribute_name);

        if ($stmt->execute()) {
            return $uuid;
        }
        return false;
    }

    // all atributes of a product
    public function getProductAttributes($product_id)
    {
        $sql = "SELECT * FROM product_attributes WHERE product_id = :product_id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':product_id', $product_id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // delete a product attributes 
    public function deleteProductAttribute($attribute_id)
    {
        // First delete all values for this attribute
        $sql = "DELETE FROM product_attribute_values WHERE attribute_id = :attribute_id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':attribute_id', $attribute_id);
        $stmt->execute();

        // Then delete the attribute itself'
        $sql = "DELETE FROM product_attributes WHERE id = :attribute_id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':attribute_id', $attribute_id);
        return $stmt->execute();
    }

    // save an attribute value 
    public function saveAttributeValue($attribute_id, $value)
    {
        $uuid = uuidv4();
        $sql = "INSERT INTO product_attribute_values (id, attribute_id, value)
        VALUES (:id, :attribute_id, :value)";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $uuid);
        $stmt->bindParam(':attribute_id', $attribute_id);
        $stmt->bindParam(':value', $value);

        if ($stmt->execute()) {
            return $uuid;
        }
        return false;
    }

    // get all values for an attributes
    public function getAttributeValues($attribute_id)
    {
        $sql = "SELECT * FROM product_attribute_values 
        WHERE attribute_id = :attribute_id 
        ORDER BY value ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':attribute_id', $attribute_id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get all attributes with their values for a product
    public function getProductAttributesWithValues($product_id)
    {
        $attributes = $this->getProductAttributes($product_id);

        foreach ($attributes as &$attribute) {
            $attribute['values'] = $this->getAttributeValues($attribute['id']);
        }

        return $attributes;
    }
    //Delete all attributes for a product (used when changing variant type)
    public function deleteAllProductAttributes($product_id)
    {
        // Get all attributes for this product
        $attributes = $this->getProductAttributes($product_id);

        foreach ($attributes as $attribute) {
            $this->deleteProductAttribute($attribute['id']);
        }

        return true;
    }

    // ==========================================
    // VARIANT METHODS
    // ==========================================

    /**
     * Generate all possible variant combinations from attributes
     */
    public function generateVariantCombinations($product_id)
    {
        $attributes = $this->getProductAttributesWithValues($product_id);

        if (empty($attributes)) {
            return [];
        }

        // Extract just the values for cartesian product
        $valueArrays = [];
        foreach ($attributes as $attribute) {
            $valueArrays[$attribute['name']] = $attribute['values'];
        }

        // Generate cartesian product
        $combinations = $this->cartesianProduct($valueArrays);

        // Format combinations for display
        $variants = [];
        foreach ($combinations as $index => $combination) {
            $optionsParts = [];
            $skuParts = [];

            foreach ($combination as $attrName => $valueObj) {
                $optionsParts[] = $attrName . ': ' . $valueObj['value'];
                // Take first 3 letters of value for SKU
                $skuParts[] = strtoupper(substr($valueObj['value'], 0, 3));
            }

            $variants[] = [
                'combination_index' => $index,
                'options' => implode(' / ', $optionsParts),
                'sku' => 'PROD-' . implode('-', $skuParts) . '-' . str_pad($index + 1, 3, '0', STR_PAD_LEFT),
                'price' => '0.00',
                'stock' => '0',
                'low_stock_alert' => '',
                'enabled' => true,
                'attribute_value_ids' => array_column($combination, 'id')
            ];
        }

        return $variants;
    }

    /**
     * Helper function to generate cartesian product
     */
    private function cartesianProduct($arrays)
    {
        $result = [[]];

        foreach ($arrays as $key => $values) {
            $temp = [];
            foreach ($result as $resultItem) {
                foreach ($values as $value) {
                    $temp[] = array_merge($resultItem, [$key => $value]);
                }
            }
            $result = $temp;
        }

        return $result;
    }

    /**
     * Save a product variant
     */
    public function saveProductVariant($product_id, $sku, $price, $stock, $attribute_value_ids, $low_stock_alert = null)
    {
        $uuid = uuidv4();

        $sql = "INSERT INTO product_variants (id, product_id, sku, price, stock_quantity, low_stock_alert) 
                VALUES (:id, :product_id, :sku, :price, :stock_quantity, :low_stock_alert)";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $uuid);
        $stmt->bindParam(':product_id', $product_id);
        $stmt->bindParam(':sku', $sku);
        $stmt->bindParam(':price', $price);
        $stmt->bindParam(':stock_quantity', $stock);
        $stmt->bindParam(':low_stock_alert', $low_stock_alert);

        if ($stmt->execute()) {
            // Link variant to attribute values
            foreach ($attribute_value_ids as $value_id) {
                $linkSql = "INSERT INTO product_variant_values (variant_id, attribute_value_id) 
                           VALUES (:variant_id, :attribute_value_id)";
                $linkStmt = $this->db->prepare($linkSql);
                $linkStmt->bindParam(':variant_id', $uuid);
                $linkStmt->bindParam(':attribute_value_id', $value_id);
                $linkStmt->execute();
            }
            $product = $this->getProductById($product_id);
            if ($product) {
                $threshold = ($low_stock_alert !== null && $low_stock_alert !== '')
                    ? (int)$low_stock_alert
                    : 10;
                $this->syncStockAlert($product['store_id'], $product_id, $uuid, (int)$stock, $threshold);
            }

            return $uuid;
        }

        return false;
    }

    /**
     * Get all variants for a product
     */
    public function getProductVariants($product_id)
    {
        $sql = "SELECT * FROM product_variants WHERE product_id = :product_id ORDER BY created_at ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':product_id', $product_id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get all variants with their attribute values for a product
     */
    public function getProductVariantsWithAttributes($product_id)
    {
        $sql = "SELECT pv.*, 
                GROUP_CONCAT(CONCAT(pa.name, ': ', pav.value) SEPARATOR ' / ') as variant_name
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
     * Get a single variant by ID
     */
    public function getVariantById($variant_id)
    {
        $sql = "SELECT * FROM product_variants WHERE id = :variant_id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':variant_id', $variant_id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Delete all variants for a product
     */
    public function deleteAllProductVariants($product_id)
    {
        // First delete variant value links
        $sql = "DELETE pvv FROM product_variant_values pvv
                INNER JOIN product_variants pv ON pvv.variant_id = pv.id
                WHERE pv.product_id = :product_id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':product_id', $product_id);
        $stmt->execute();

        // Then delete variants
        $sql = "DELETE FROM product_variants WHERE product_id = :product_id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':product_id', $product_id);
        return $stmt->execute();
    }

    /**
     * Get total stock quantity for a variant product (sum of all variants)
     */
    public function getTotalVariantStock($product_id)
    {
        $sql = "SELECT SUM(stock_quantity) as total_stock 
                FROM product_variants 
                WHERE product_id = :product_id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':product_id', $product_id);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        // Handle NULL case when no variants exist
        if ($result && $result['total_stock'] !== null) {
            return (int)$result['total_stock'];
        }
        return 0;
    }

    /**
     * Get price range for a variant product (min and max prices)
     */
    public function getVariantPriceRange($product_id)
    {
        $sql = "SELECT MIN(price) as min_price, MAX(price) as max_price 
                FROM product_variants 
                WHERE product_id = :product_id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':product_id', $product_id);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result && $result['min_price'] !== null) {
            return [
                'min' => (float)$result['min_price'],
                'max' => (float)$result['max_price']
            ];
        }
        return null;
    }

    /**
     * Update a product variant's price and stock
     */
    public function updateProductVariant($variant_id, $price, $stock_quantity, $low_stock_alert = null)
    {
        $sql = "UPDATE product_variants 
                SET price = :price, 
                    stock_quantity = :stock_quantity,
                    low_stock_alert = :low_stock_alert,
                    updated_at = NOW() 
                WHERE id = :variant_id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':price', $price);
        $stmt->bindParam(':stock_quantity', $stock_quantity);
        $stmt->bindParam(':low_stock_alert', $low_stock_alert);
        $stmt->bindParam(':variant_id', $variant_id);
        $result = $stmt->execute();

        if ($result) {
            $variantInfoSql = "SELECT pv.product_id, p.store_id, pv.low_stock_alert
                               FROM product_variants pv
                               INNER JOIN products p ON p.id = pv.product_id
                               WHERE pv.id = :variant_id
                               LIMIT 1";
            $variantStmt = $this->db->prepare($variantInfoSql);
            $variantStmt->bindParam(':variant_id', $variant_id);
            $variantStmt->execute();
            $variant = $variantStmt->fetch(PDO::FETCH_ASSOC);

            if ($variant) {
                $threshold = ($low_stock_alert !== null && $low_stock_alert !== '')
                    ? (int)$low_stock_alert
                    : (isset($variant['low_stock_alert']) && $variant['low_stock_alert'] !== null ? (int)$variant['low_stock_alert'] : 10);
                $this->syncStockAlert($variant['store_id'], $variant['product_id'], $variant_id, (int)$stock_quantity, $threshold);
            }
        }

        return $result;
    }

    public function getOpenStockAlertStats($store_id)
    {
        $sql = "SELECT
                    COUNT(*) as total_alerts,
                    SUM(CASE WHEN threshold > 0 AND current_stock <= (threshold * 0.30) THEN 1 ELSE 0 END) as critical_count,
                    SUM(CASE WHEN threshold > 0 AND current_stock > (threshold * 0.30) AND current_stock <= (threshold * 0.70) THEN 1 ELSE 0 END) as warning_count,
                    SUM(CASE WHEN threshold <= 0 OR current_stock > (threshold * 0.70) THEN 1 ELSE 0 END) as restock_soon_count
                FROM (
                    SELECT
                        p.stock_quantity AS current_stock,
                        COALESCE(p.low_stock_alert, 10) AS threshold
                    FROM products p
                                        WHERE p.store_id = :store_id_single
                                            AND p.delete_flag = 0
                      AND p.visibility = 1
                      AND p.is_variant = 0
                      AND p.stock_quantity IS NOT NULL
                      AND p.stock_quantity < COALESCE(p.low_stock_alert, 10)

                    UNION ALL

                    SELECT
                        pv.stock_quantity AS current_stock,
                        COALESCE(pv.low_stock_alert, 10) AS threshold
                    FROM products p
                    INNER JOIN product_variants pv ON pv.product_id = p.id
                                        WHERE p.store_id = :store_id_variant
                                            AND p.delete_flag = 0
                      AND p.visibility = 1
                      AND p.is_variant = 1
                      AND pv.stock_quantity < COALESCE(pv.low_stock_alert, 10)
                ) AS alerts";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':store_id_single', $store_id);
        $stmt->bindParam(':store_id_variant', $store_id);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC) ?: [];

        return [
            'total_alerts' => (int)($row['total_alerts'] ?? 0),
            'critical_count' => (int)($row['critical_count'] ?? 0),
            'warning_count' => (int)($row['warning_count'] ?? 0),
            'restock_soon_count' => (int)($row['restock_soon_count'] ?? 0)
        ];
    }

    public function getOpenStockAlerts($store_id)
    {
        $sql = "SELECT
                                        CONCAT('product-', p.id) AS id,
                                        p.id AS product_id,
                                        NULL AS variant_id,
                                        p.stock_quantity AS current_stock,
                                        COALESCE(p.low_stock_alert, 10) AS threshold,
                                        COALESCE(p.updated_at, p.created_at) AS created_at,
                                        p.name AS product_name,
                                        c.name AS category_name,
                                        p.is_variant,
                                        p.price AS product_price,
                                        NULL AS variant_sku,
                                        NULL AS variant_price,
                                        NULL AS variant_updated_at,
                                        p.updated_at AS product_updated_at
                                FROM products p
                                LEFT JOIN categories c ON c.id = p.category_id
                                WHERE p.store_id = :store_id
                                    AND p.delete_flag = 0
                                    AND p.visibility = 1
                                    AND p.is_variant = 0
                                    AND p.stock_quantity IS NOT NULL
                                    AND p.stock_quantity < COALESCE(p.low_stock_alert, 10)

                                UNION ALL

                                SELECT
                                        CONCAT('variant-', pv.id) AS id,
                                        p.id AS product_id,
                                        pv.id AS variant_id,
                                        pv.stock_quantity AS current_stock,
                                        COALESCE(pv.low_stock_alert, 10) AS threshold,
                                        COALESCE(pv.updated_at, p.updated_at, p.created_at) AS created_at,
                                        p.name AS product_name,
                                        c.name AS category_name,
                                        p.is_variant,
                                        p.price AS product_price,
                                        pv.sku AS variant_sku,
                                        pv.price AS variant_price,
                                        pv.updated_at AS variant_updated_at,
                                        p.updated_at AS product_updated_at
                                FROM products p
                                INNER JOIN product_variants pv ON pv.product_id = p.id
                                LEFT JOIN categories c ON c.id = p.category_id
                                WHERE p.store_id = :store_id
                                    AND p.delete_flag = 0
                                    AND p.visibility = 1
                                    AND p.is_variant = 1
                                    AND pv.stock_quantity < COALESCE(pv.low_stock_alert, 10)

                                ORDER BY created_at DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':store_id', $store_id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getOpenStockAlertsPaginated($store_id, $page = 1, $limit = 5)
    {
        $offset = ($page - 1) * $limit;
        $sql = "SELECT *
                                FROM (
                                        SELECT
                                                CONCAT('product-', p.id) AS id,
                                                p.id AS product_id,
                                                NULL AS variant_id,
                                                p.stock_quantity AS current_stock,
                                                COALESCE(p.low_stock_alert, 10) AS threshold,
                                                COALESCE(p.updated_at, p.created_at) AS created_at,
                                                p.name AS product_name,
                                                c.name AS category_name,
                                                p.is_variant,
                                                p.price AS product_price,
                                                NULL AS variant_sku,
                                                NULL AS variant_price,
                                                NULL AS variant_updated_at,
                                                p.updated_at AS product_updated_at
                                        FROM products p
                                        LEFT JOIN categories c ON c.id = p.category_id
                                        WHERE p.store_id = :store_id
                                            AND p.delete_flag = 0
                                            AND p.visibility = 1
                                            AND p.is_variant = 0
                                            AND p.stock_quantity IS NOT NULL
                                            AND p.stock_quantity < COALESCE(p.low_stock_alert, 10)

                                        UNION ALL

                                        SELECT
                                                CONCAT('variant-', pv.id) AS id,
                                                p.id AS product_id,
                                                pv.id AS variant_id,
                                                pv.stock_quantity AS current_stock,
                                                COALESCE(pv.low_stock_alert, 10) AS threshold,
                                                COALESCE(pv.updated_at, p.updated_at, p.created_at) AS created_at,
                                                p.name AS product_name,
                                                c.name AS category_name,
                                                p.is_variant,
                                                p.price AS product_price,
                                                pv.sku AS variant_sku,
                                                pv.price AS variant_price,
                                                pv.updated_at AS variant_updated_at,
                                                p.updated_at AS product_updated_at
                                        FROM products p
                                        INNER JOIN product_variants pv ON pv.product_id = p.id
                                        LEFT JOIN categories c ON c.id = p.category_id
                                        WHERE p.store_id = :store_id
                                            AND p.delete_flag = 0
                                            AND p.visibility = 1
                                            AND p.is_variant = 1
                                            AND pv.stock_quantity < COALESCE(pv.low_stock_alert, 10)
                                ) AS alerts
                                ORDER BY created_at DESC
                                LIMIT :limit OFFSET :offset";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':store_id', $store_id);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTotalOpenStockAlerts($store_id)
    {
        $sql = "SELECT
                                        (
                                                SELECT COUNT(*)
                                                FROM products p
                                            WHERE p.store_id = :store_id_single
                                                    AND p.delete_flag = 0
                                                    AND p.visibility = 1
                                                    AND p.is_variant = 0
                                                    AND p.stock_quantity IS NOT NULL
                                                    AND p.stock_quantity < COALESCE(p.low_stock_alert, 10)
                                        )
                                        +
                                        (
                                                SELECT COUNT(*)
                                                FROM products p
                                                INNER JOIN product_variants pv ON pv.product_id = p.id
                                                WHERE p.store_id = :store_id_variant
                                                    AND p.delete_flag = 0
                                                    AND p.visibility = 1
                                                    AND p.is_variant = 1
                                                    AND pv.stock_quantity < COALESCE(pv.low_stock_alert, 10)
                                        ) AS total";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':store_id_single', $store_id);
        $stmt->bindParam(':store_id_variant', $store_id);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int)($row['total'] ?? 0);
    }

    private function syncStockAlert($store_id, $product_id, $variant_id, $current_stock, $threshold)
    {
        // Alerts are now derived directly from products and variants.
        // Keep this method as a no-op for backward compatibility with existing call sites.
        return;
    }

    /**
     * Update a product variant's image
     */
    public function updateVariantImage($variant_id, $image_url)
    {
        // Verify variant exists first
        $checkSql = "SELECT id, image FROM product_variants WHERE id = :variant_id";
        $checkStmt = $this->db->prepare($checkSql);
        $checkStmt->bindParam(':variant_id', $variant_id);
        $checkStmt->execute();
        $existingVariant = $checkStmt->fetch(PDO::FETCH_ASSOC);

        if (!$existingVariant) {
            error_log("Variant not found: $variant_id");
            return false;
        }

        $sql = "UPDATE product_variants 
                SET image = :image_url,
                    updated_at = NOW() 
                WHERE id = :variant_id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':image_url', $image_url);
        $stmt->bindParam(':variant_id', $variant_id);

        $result = $stmt->execute();

        if ($result) {
            $oldImage = $existingVariant['image'] ?? null;
            if (!empty($oldImage) && $oldImage !== $image_url) {
                $publicPath = dirname(__DIR__, 2) . '/public/' . ltrim(str_replace('\\', '/', $oldImage), '/');
                if (file_exists($publicPath)) {
                    @unlink($publicPath);
                }
            }
            error_log("Updated variant image - Variant ID: $variant_id, Image: $image_url");
        } else {
            error_log("Failed to update variant image - Variant ID: $variant_id, Error: " . print_r($stmt->errorInfo(), true));
        }

        return $result;
    }
}

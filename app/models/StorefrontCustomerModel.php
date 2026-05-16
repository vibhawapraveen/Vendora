<?php
class StorefrontCustomerModel extends Model
{
    public function getStoreByCode($code)
    {
        $stmt = $this->db->prepare("SELECT s.id as store_id, visibility, template_id, file_path as file_path
        FROM stores as s JOIN templates as t ON t.id = s.template_id WHERE code=:storecode LIMIT 1");
        $stmt->execute(['storecode' => $code]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public function getStorefrontContent($store_id)
    {
        $stmt = $this->db->prepare("SELECT * FROM stores LEFT JOIN templates ON stores.template_id = templates.id WHERE stores.id=:store_id LIMIT 1");
        $stmt->execute(['store_id' => $store_id]);
        $stmt = $stmt->fetch(PDO::FETCH_ASSOC);
        if (isset($stmt['file_path'])) {
            $file_path = $stmt['file_path'];
        } else {
            $file_path = '';
        };

        $store_contents = $this->db->prepare("SELECT *
        FROM template_fields tf
        LEFT JOIN store_contents sc
            ON tf.id = sc.template_field_id
            AND sc.store_id = :store_id
        WHERE tf.template_id = :template_id;
        ");
        $store_contents->execute(['template_id' => $stmt['template_id'], 'store_id' => $store_id]);
        $store_contents = $store_contents->fetchAll(PDO::FETCH_ASSOC);

        $data = [];
        foreach ($store_contents as &$content) {
            $data[$content['field_name']] = $content['field_value'] ? $content['field_value'] : $content['default_value'];
        }

        return ['store_contents' => $data, 'file_path' => $file_path];
    }

    public function getProductsStorefront($store_id)
    {
        $products = $this->db->prepare("SELECT p.*, GROUP_CONCAT(pi.image_url) as images 
            FROM products p 
            LEFT JOIN product_images pi ON p.id = pi.product_id 
            WHERE p.store_id = :store_id AND p.visibility=1 
            GROUP BY p.id 
            ORDER BY p.created_at DESC");
        $products->execute(['store_id' => $store_id]);
        $products = $products->fetchAll(PDO::FETCH_ASSOC);

        foreach ($products as &$product) {
            if ($product['images']) {
                $images = explode(',', $product['images']);
                foreach ($images as &$img) {
                    $img = ROOT . $img;
                }
                $product['images'] = $images;
            } else {
                $product['images'] = [];
            }
        }

        return $products;
    }


    public function getProductsStorefrontFiltered($store_id, $options = [])
    {
        $search = isset($options['search']) ? trim($options['search']) : '';
        $sort = isset($options['sort']) ? $options['sort'] : 'newest';
        $category = isset($options['category']) ? $options['category'] : '';
        $page = isset($options['page']) ? max(1, (int)$options['page']) : 1;
        $limit = isset($options['limit']) ? (int)$options['limit'] : 12;

        $baseQuery = "SELECT p.*,c.name as category_name, GROUP_CONCAT(pi.image_url) as images,
                      CASE 
                        WHEN p.is_variant = 1 THEN COALESCE(MIN(pv.price), 0)
                        ELSE p.price
                      END as display_price,
                      CASE
                        WHEN p.is_variant = 1 THEN COALESCE(SUM(pv.stock_quantity),0)
                        ELSE p.stock_quantity
                        END as total_stock
                      FROM products p 
                      LEFT JOIN product_images pi ON p.id = pi.product_id 
                      LEFT JOIN product_variants pv ON p.id = pv.product_id AND p.is_variant = 1
                      LEFT JOIN categories c on c.id = p.category_id
                      WHERE p.store_id = :store_id AND p.visibility=1 AND p.delete_flag = 0
                      ";

        $params = ['store_id' => $store_id];
        if (!empty($search)) {
            $baseQuery .= " AND (p.name LIKE :search OR p.description LIKE :search)";
            $params['search'] = '%' . $search . '%';
        }

        if (!empty($category)) {
            $baseQuery .= " AND c.name = :category";
            $params['category'] = $category;
        }

        $baseQuery .= " GROUP BY p.id";

        switch ($sort) {
            case 'price-low':
                $baseQuery .= " ORDER BY p.price ASC";
                break;
            case 'price-high':
                $baseQuery .= " ORDER BY p.price DESC";
                break;
            case 'name-asc':
                $baseQuery .= " ORDER BY p.name ASC";
                break;
            case 'name-desc':
                $baseQuery .= " ORDER BY p.name DESC";
                break;
            case 'newest':
            default:
                $baseQuery .= " ORDER BY p.created_at DESC";
                break;
        }

        $countQuery = "SELECT COUNT(DISTINCT p.id) as total FROM products p 
                       LEFT JOIN categories as c on c.id = p.category_id
                       WHERE p.store_id = :store_id AND p.visibility=1
                       ";
        if (!empty($search)) {
            $countQuery .= " AND (p.name LIKE :search OR p.description LIKE :search)";
        }
        if (!empty($category)) {
            $countQuery .= " AND c.name = :category";
        }

        $countStmt = $this->db->prepare($countQuery);
        $countStmt->execute($params);
        $countResult = $countStmt->fetch(PDO::FETCH_ASSOC);
        $totalProducts = (int)$countResult['total'];
        $totalPages = $totalProducts > 0 ? ceil($totalProducts / $limit) : 1;

        $page = min($page, max(1, $totalPages));

        $offset = ($page - 1) * $limit;
        $baseQuery .= " LIMIT " . intval($limit) . " OFFSET " . intval($offset);

        $stmt = $this->db->prepare($baseQuery);
        $stmt->execute($params);
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($products as &$product) {
            if ($product['images']) {
                $images = explode(',', $product['images']);
                foreach ($images as &$img) {
                    $img = ROOT . $img;
                }
                $product['images'] = $images;
            } else {
                $product['images'] = [];
            }
        }

        return [
            'products' => $products,
            'totalProducts' => $totalProducts,
            'totalPages' => $totalPages,
            'currentPage' => $page,
            'startIndex' => $offset,
            'limit' => $limit
        ];
    }

    public function getProductById($id, $store_id)
    {
        $product_stmt = $this->db->prepare("SELECT * FROM products WHERE id = :id AND store_id = :store_id AND visibility = 1 AND delete_flag = 0");
        $product_stmt->execute(['id' => $id, 'store_id' => $store_id]);
        $product = $product_stmt->fetch(PDO::FETCH_ASSOC);

        if (!$product) {
            return null;
        }

        $images_stmt = $this->db->prepare("SELECT image_url FROM product_images WHERE product_id = :id");
        $images_stmt->execute(['id' => $id]);
        $images = $images_stmt->fetchAll(PDO::FETCH_COLUMN);

        $product['images'] = array_map(function ($img) {
            return ROOT . $img;
        }, $images);

        if ($product['is_variant']) {
            $attributes_stmt = $this->db->prepare("
                SELECT pa.id, pa.name,
                       GROUP_CONCAT(CONCAT(pav.id, '|', pav.value) ORDER BY pav.id) as attribute_values
                FROM product_attributes pa
                LEFT JOIN product_attribute_values pav ON pav.attribute_id = pa.id
                WHERE pa.product_id = :id
                GROUP BY pa.id, pa.name
                ORDER BY pa.id
            ");
            $attributes_stmt->execute(['id' => $id]);
            $attributes_raw = $attributes_stmt->fetchAll(PDO::FETCH_ASSOC);

            $attributes = [];
            foreach ($attributes_raw as $attr) {
                $values = [];
                if ($attr['attribute_values']) {
                    foreach (explode(',', $attr['attribute_values']) as $val) {
                        list($val_id, $val_name) = explode('|', $val);
                        $values[] = ['id' => $val_id, 'value' => $val_name];
                    }
                }
                $attributes[] = [
                    'id' => $attr['id'],
                    'name' => $attr['name'],
                    'values' => $values
                ];
            }
            $product['attributes'] = $attributes;

            $variants_stmt = $this->db->prepare("
                SELECT pv.id, pv.sku, pv.price, pv.stock_quantity,
                       pv.image,
                       GROUP_CONCAT(CAST(pvv.attribute_value_id AS CHAR)) as attribute_values
                FROM product_variants pv
                LEFT JOIN product_variant_values pvv ON pvv.variant_id = pv.id
                WHERE pv.product_id = :id
                GROUP BY pv.id, pv.sku, pv.price, pv.stock_quantity, pv.image
                ORDER BY pv.id
            ");
            $variants_stmt->execute(['id' => $id]);
            $variants_raw = $variants_stmt->fetchAll(PDO::FETCH_ASSOC);

            $variants = [];
            foreach ($variants_raw as $var) {
                $attribute_ids = $var['attribute_values'] ? explode(',', $var['attribute_values']) : [];
                $variant = [
                    'id' => $var['id'],
                    'sku' => $var['sku'],
                    'price' => floatval($var['price']),
                    'stock' => intval($var['stock_quantity']),
                    'image' => $var['image'] ? ROOT . $var['image'] : null,
                    'attributes' => $attribute_ids
                ];
                $variants[] = $variant;
            }
            $product['variants'] = $variants;
        } else {
            $product['price'] = floatval($product['price']);
            $product['stock'] = intval($product['stock_quantity']);
        }

        return $product;
    }

    public function getStorefrontCategories($store_id)
    {
        $stmt = $this->db->prepare("SELECT * FROM categories WHERE store_id = :store_id");
        $stmt->execute(['store_id' => $store_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

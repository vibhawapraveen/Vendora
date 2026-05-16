<?php

class CategoryModel extends Model
{
    public function categoryExistsByName($store_id, $name)
    {
        $sql = "SELECT id
                FROM categories
                WHERE store_id = :store_id
                AND LOWER(name) = LOWER(:name)
                LIMIT 1";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':store_id', $store_id);
        $stmt->bindParam(':name', $name);
        $stmt->execute();

        return (bool)$stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function createCategory($store_id, $name)
    {
        $uuid = uuidv4();

        $sql = "INSERT INTO categories (id, store_id, name)
                VALUES (:id, :store_id, :name)";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $uuid);
        $stmt->bindParam(':store_id', $store_id);
        $stmt->bindParam(':name', $name);

        return $stmt->execute() ? $uuid : false;
    }

    public function getCategoriesByStore($store_id)
    {
        $sql = "SELECT c.id, c.name, c.created_at,
                       COUNT(p.id) AS products,
                       CASE
                           WHEN COUNT(p.id) > 0 AND SUM(CASE WHEN p.visibility = 1 THEN 1 ELSE 0 END) = 0 THEN 'inactive'
                           ELSE 'active'
                       END AS status
                FROM categories c
                LEFT JOIN products p ON p.category_id = c.id AND p.delete_flag = 0
                WHERE c.store_id = :store_id
                GROUP BY c.id, c.name, c.created_at
                ORDER BY c.created_at DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':store_id', $store_id);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getCategoriesByStorePaginated($store_id, $page = 1, $limit = 10)
    {
        $offset = ($page - 1) * $limit;
        $sql = "SELECT c.id, c.name, c.created_at,
                       COUNT(p.id) AS products,
                       CASE
                           WHEN COUNT(p.id) > 0 AND SUM(CASE WHEN p.visibility = 1 THEN 1 ELSE 0 END) = 0 THEN 'inactive'
                           ELSE 'active'
                       END AS status
                FROM categories c
                LEFT JOIN products p ON p.category_id = c.id AND p.delete_flag = 0
                WHERE c.store_id = :store_id
                GROUP BY c.id, c.name, c.created_at
                ORDER BY c.created_at DESC
                LIMIT :limit OFFSET :offset";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':store_id', $store_id);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTotalCategoriesByStore($store_id)
    {
        $sql = "SELECT COUNT(*) AS total FROM categories WHERE store_id = :store_id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':store_id', $store_id);
        $stmt->execute();

        return (int)($stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0);
    }

    public function categoryBelongsToStore($category_id, $store_id)
    {
        $sql = "SELECT id
                FROM categories
                WHERE id = :category_id AND store_id = :store_id
                LIMIT 1";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':category_id', $category_id);
        $stmt->bindParam(':store_id', $store_id);
        $stmt->execute();

        return (bool)$stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function setProductsVisibilityByCategory($category_id, $store_id, $visibility)
    {
        $sql = "UPDATE products p
                INNER JOIN categories c ON c.id = p.category_id
                SET p.visibility = :visibility,
                    p.updated_at = NOW()
                WHERE c.id = :category_id
                  AND c.store_id = :store_id
                  AND p.delete_flag = 0";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':visibility', $visibility, PDO::PARAM_INT);
        $stmt->bindParam(':category_id', $category_id);
        $stmt->bindParam(':store_id', $store_id);

        return $stmt->execute();
    }

    public function softDeleteProductsByCategory($category_id, $store_id)
    {
        $sql = "UPDATE products p
                INNER JOIN categories c ON c.id = p.category_id
                SET p.delete_flag = 1,
                    p.visibility = 0,
                    p.updated_at = NOW()
                WHERE c.id = :category_id
                  AND c.store_id = :store_id
                  AND p.delete_flag = 0";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':category_id', $category_id);
        $stmt->bindParam(':store_id', $store_id);

        return $stmt->execute();
    }

    public function deleteCategoryByIdAndStore($category_id, $store_id)
    {
        $sql = "DELETE FROM categories WHERE id = :category_id AND store_id = :store_id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':category_id', $category_id);
        $stmt->bindParam(':store_id', $store_id);
        return $stmt->execute();
    }

    public function deleteCategoryAndSoftDeleteProducts($category_id, $store_id)
    {
        $this->db->beginTransaction();

        try {
            $okSoftDeleteProducts = $this->softDeleteProductsByCategory($category_id, $store_id);
            $okDeleteCategory = $this->deleteCategoryByIdAndStore($category_id, $store_id);

            if ($okSoftDeleteProducts && $okDeleteCategory) {
                $this->db->commit();
                return true;
            }

            $this->db->rollBack();
            return false;
        } catch (Throwable $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            return false;
        }
    }
}

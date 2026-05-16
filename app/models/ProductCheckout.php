<?php

class ProductCheckout extends Model
{
    public function getSingleVariantProduct($store_id, $id)
    {
        $stmt = $this->db->prepare("SELECT * FROM products WHERE id=:id AND store_id=:store_id");
        $stmt->execute(['store_id' => $store_id, 'id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getVariantProduct($store_id, $id, $variant_id)
    {
        $stmt = $this->db->prepare("SELECT pv.price as price, pv.stock_quantity as stock_quantity FROM products as p JOIN product_variants as pv ON pv.product_id=p.id WHERE p.id=:id AND store_id=:store_id AND pv.id=:variant_id");
        $stmt->execute(['store_id' => $store_id, 'id' => $id, 'variant_id' => $variant_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}

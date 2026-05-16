<?php

class Template_beam_Model extends Model
{
    public function getEditTabContent($tab, $store_id)
    {
        if ($tab == "featured") {
            $featuredProducts = $this->db->prepare("SELECT tb.*, products.*, pi.image_url FROM template_beam_featured_products as tb
            LEFT JOIN products ON tb.product_id = products.id
            LEFT JOIN product_images as pi ON pi.id = (
            SELECT id 
            FROM product_images 
            WHERE product_id = tb.product_id 
            ORDER BY id ASC
            LIMIT 1
            )
            WHERE tb.store_id = :store_id;
            ");

            $featuredProducts->execute(['store_id' => $store_id]);
            $featuredProducts = $featuredProducts->fetchAll(PDO::FETCH_ASSOC);

            $notFeaturedProducts =  $this->db->prepare("SELECT p.*, pi.image_url FROM products as p
            LEFT JOIN product_images as pi ON pi.id = (SELECT id FROM product_images WHERE product_id = p.id LIMIT 1)
            WHERE p.store_id=:store_id AND p.delete_flag = 0 AND p.id NOT IN (SELECT product_id FROM template_beam_featured_products WHERE store_id=:store_id )
            ");

            $notFeaturedProducts->execute(['store_id' => $store_id]);
            $notFeaturedProducts = $notFeaturedProducts->fetchAll(PDO::FETCH_ASSOC);

            return ['featured_products' => $featuredProducts, 'not_featured_products' => $notFeaturedProducts];
        }
    }

    public function saveTabContents($tab, $store_id)
    {
        if ($tab == "featured") {
            if (!isset($_POST['product_id'])) {
                return;
            }

            $stmt = $this->db->prepare("INSERT INTO template_beam_featured_products
            (store_id, product_id) VALUES (:store_id, :product_id)
            ");

            $stmt->execute(['store_id' => $store_id, 'product_id' => $_POST['product_id']]);
        }
    }

    public function deleteTabContents($tab, $store_id)
    {
        if ($tab == "featured") {
            if (!isset($_POST['product_id'])) {
                return;
            }

            $stmt = $this->db->prepare("DELETE FROM template_beam_featured_products WHERE
            product_id = :product_id AND store_id = :store_id
            ");

            $stmt->execute(['product_id' => $_POST['product_id'], 'store_id' => $store_id]);
        }
    }

    public function getHomePageContents($store_id)
    {
        $stmt = $this->db->prepare("SELECT p.*, GROUP_CONCAT(DISTINCT pi.image_url) as images,
        CASE
            WHEN p.is_variant = 1 THEN MIN(pv.price)
            ELSE p.price
        END as base_price FROM template_beam_featured_products as tb
        LEFT JOIN products as p ON p.id = tb.product_id
        LEFT JOIN product_images as pi ON pi.product_id = tb.product_id
        LEFT JOIN product_variants as pv ON pv.product_id = tb.product_id
        WHERE tb.store_id = :store_id AND p.visibility = 1 AND p.delete_flag = 0
        GROUP BY p.id
        ");

        $stmt->execute(['store_id' => $store_id]);
        $featuredProducts = $stmt->fetchAll(PDO::FETCH_ASSOC);


        // pre($featuredProducts);
        foreach ($featuredProducts as &$prod) {
            if ($prod['images']) {
                $images = explode(",", $prod['images']);
                $prod['images'] = $images;
                foreach ($images as &$img) {
                    $img = ROOT . $img;
                }
                $prod['images'] = $images;
            } else {
                $prod['images'] = [];
            }
        };

        return $featuredProducts;
    }

    public function removeTemplate($store_id)
    {
        $stmt = $this->db->prepare("DELETE FROM template_beam_featured_products WHERE store_id = :store_id");
        $stmt->execute(['store_id' => $store_id]);
    }
}

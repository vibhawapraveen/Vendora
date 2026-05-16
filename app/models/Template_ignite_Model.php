<?php

class Template_ignite_Model extends Model
{
    public function getEditTabContent($tab, $store_id)
    {
        if ($tab == "featured") {
        }

        if ($tab == "carousel") {
            $stmt = $this->db->prepare("SELECT tp.*, p.name FROM template_prestige_carousel_slides as tp
            LEFT JOIN products as p ON p.id = tp.product_id
            WHERE tp.store_id = :store_id
            ORDER BY created_at
            ");
            $stmt->execute(['store_id' => $store_id]);
            $stmt = $stmt->fetchAll(PDO::FETCH_ASSOC);


            $prodStatement = $this->db->prepare("SELECT * FROM products
            WHERE store_id = :store_id AND visibility = 1 AND delete_flag = 0
            ");
            $prodStatement->execute(['store_id' => $store_id]);
            $prodStatement = $prodStatement->fetchAll(PDO::FETCH_ASSOC);
            return ['slides' => $stmt, 'products' => $prodStatement];
        }

        if ($tab == "sections") {
            $section_data = $this->getSectionsData($store_id);
            $products = $this->getProducts($store_id);
            return ['section_data' => $section_data, 'products' => $products];
        }
    }

    public function saveTabContents($tab, $store_id)
    {
        if ($tab == "carousel") {
            $url = $this->carouselUploadBackground();

            if ($url === null) {
                return; // or throw an error
            }

            $stmt = $this->db->prepare("INSERT INTO template_prestige_carousel_slides
            (store_id, background, title, subtitle, product_id) VALUES (:store_id, :background, :title, :subtitle, :product_id)
            ");
            $stmt->execute([
                'store_id' => $store_id,
                'background' => $url,
                'title' => $_POST['title'],
                'subtitle' => $_POST['subtitle'],
                'product_id' => $_POST['product_id']
            ]);
        }
        if ($tab == "sections") {
            if ($_POST['section_type'] == 'product_feature') {
                $this->addNewProductFeatureSection($store_id);
            } else if ($_POST['section_type'] == 'promotional_banner') {
                $this->addNewPromotionalBannerSection($store_id);
            } elseif ($_POST['product_feature_section_id']) {
                $this->addProductToProductFeatureSection($store_id);
            }
        }
    }

    public function deleteTabContents($tab, $store_id)
    {
        if ($tab == "carousel") {
            $stmt = $this->db->prepare("DELETE FROM template_prestige_carousel_slides
        WHERE store_id = :store_id AND id = :slide_id
        ");
            $stmt->execute(['store_id' => $store_id, 'slide_id' => $_POST['slide_id']]);
        }

        if ($tab == "sections") {
            if ($_POST['feature_item_id']) {
                $this->removeProductFromProductFeatureSection($store_id);
            } else if ($_POST['delete_promotional_section_id']) {
                $this->deletePromotionalBannerSection($store_id);
            } else if ($_POST['delete_product_feature_section_id']) {
                $this->deleteProductFeatureSection($store_id);
            }
        }
    }

    public function getHomePageContents($store_id)
    {
        $carouselItems = $this->db->prepare("SELECT * FROM template_prestige_carousel_slides
        WHERE store_id = :store_id
        ORDER BY created_at
        ");

        $categories = $this->db->prepare("SELECT * FROM categories WHERE store_id = :store_id");
        $categories->execute(['store_id'=>$store_id]);
        $categories = $categories->fetchAll(PDO::FETCH_ASSOC);

        $carouselItems->execute(['store_id' => $store_id]);
        $carouselItems = $carouselItems->fetchAll(PDO::FETCH_ASSOC);

        $sections = $this->getSectionsData($store_id);
        return ['carousel_items' => $carouselItems, 'sections' => $sections, 'categories'=>$categories];
    }

    private function carouselUploadBackground()
    {
        $background = $_FILES['background'];
        $tmpName = $background['tmp_name'];
        $extension = pathinfo($background['name'], PATHINFO_EXTENSION);

        $fileName = uuidv4() . "." . $extension;

        $uploadDir = "../public/assets/img/user-uploads/";
        $uploadPath = $uploadDir . $fileName;

        if (move_uploaded_file($tmpName, $uploadPath)) {
            $url = ROOT . "/assets/img/user-uploads/" . $fileName;
            return $url;
        }
        return null;
    }

    private function getSectionsData($store_id)
    {
        $stmt = $this->db->prepare("SELECT * FROM template_prestige_sections
            WHERE store_id = :store_id
            ORDER BY created_at
            ");
        $stmt->execute(['store_id' => $store_id]);
        $sections = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($sections as &$section) {

            if ($section['section_type'] == 'product_feature') {
                $section_data = $this->db->prepare("SELECT tp.*, p.name, pi.image_url as image_url,
                CASE
                    WHEN p.is_variant = 1 THEN COALESCE(MIN(pv.price), 0)
                    ELSE p.price
                END AS display_price
                 FROM template_prestige_product_feature_items as tp
                    LEFT JOIN products as p on p.id = tp.product_id
                    LEFT JOIN product_images as pi on pi.id = (SELECT id from product_images WHERE product_id = tp.product_id LIMIT 1)
                    LEFT JOIN product_variants as pv on pv.product_id = tp.product_id
                    WHERE section_id = :section_id AND p.delete_flag = 0
                    GROUP BY tp.id
                    ORDER BY tp.created_at
                    ");
                $section_data->execute(['section_id' => $section['id']]);
                $section_data = $section_data->fetchAll(PDO::FETCH_ASSOC);
                $section['products'] = [];
                if (isset($section_data)) {
                    $section['products'] = $section_data;
                }
            }
        };
        return $sections;
    }

    private function addNewProductFeatureSection($store_id)
    {
        $stmt = $this->db->prepare("INSERT INTO template_prestige_sections
        (store_id, section_type, title) VALUES (:store_id, 'product_feature', :title)
        ");
        $stmt->execute(['store_id' => $store_id, 'title' => $_POST['title']]);
    }

    private function addNewPromotionalBannerSection($store_id)
    {
        $file = $_FILES['background'];
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $fileName = uuidv4() . "." . $extension;
        $uploadPath = "../public/assets/img/user-uploads/" . $fileName;

        if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
            $url = ROOT . 'assets/img/user-uploads/' . $fileName;

            $stmt = $this->db->prepare("INSERT INTO template_prestige_sections
            (store_id, section_type, title, background_image) VALUES (:store_id, 'promotional_banner', :title, :url)
            ");
            $stmt->execute(['store_id' => $store_id, 'title' => $_POST['title'], 'url' => $url]);
        }
    }

    private function getProducts($store_id)
    {
        $stmt = $this->db->prepare("SELECT p.*, pi.image_url as image_url FROM products as p
        LEFT JOIN product_images as pi on pi.id = (SELECT id from product_images WHERE product_id = p.id LIMIT 1)
        WHERE p.store_id = :store_id AND visibility = 1 AND delete_flag = 0
        ");
        $stmt->execute(['store_id' => $store_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    private function addProductToProductFeatureSection($store_id)
    {
        $stmt = $this->db->prepare("INSERT INTO template_prestige_product_feature_items
        (section_id, product_id) VALUES (:section_id, :product_id)
        ");
        $stmt->execute(['section_id' => $_POST['product_feature_section_id'], 'product_id' => $_POST['product_id']]);
    }

    private function removeProductFromProductFeatureSection($store_id)
    {
        $stmt = $this->db->prepare("DELETE FROM template_prestige_product_feature_items
        WHERE id = :feature_item_id
        ");
        $stmt->execute(['feature_item_id' => $_POST['feature_item_id']]);
    }

    private function deletePromotionalBannerSection($store_id)
    {
        $stmt = $this->db->prepare("DELETE FROM template_prestige_sections
        WHERE id = :delete_promotional_section_id
        ");
        $stmt->execute(['delete_promotional_section_id' => $_POST['delete_promotional_section_id']]);
    }

    private function deleteProductFeatureSection($store_id)
    {
        $stmt = $this->db->prepare("DELETE FROM template_prestige_sections
        WHERE id = :section_id
        ");
        $stmt->execute(['section_id' => $_POST['delete_product_feature_section_id']]);
    }

    public function removeTemplate($store_id)
    {
        $sections = $this->db->prepare("DELETE FROM template_prestige_sections WHERE store_id = :store_id");
        $carousel = $this->db->prepare("DELETE FROM template_prestige_carousel_slides WHERE store_id = :store_id");
        $sections->execute(['store_id' => $store_id]);
        $carousel->execute(['store_id' => $store_id]);
    }
}

<?php

class StorefrontModel extends Model
{
    public function getStorefrontEditData($store_id)
    {
        $stmt = $this->db->prepare("SELECT * FROM stores JOIN templates ON stores.template_id = templates.id WHERE stores.id=:store_id LIMIT 1");
        $stmt->execute(['store_id' => $store_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getCurrentTemplate($store_id)
    {
        $stmt = $this->db->prepare("SELECT * FROM stores
        JOIN templates ON templates.id = stores.template_id
        WHERE stores.id=:store_id");
        $stmt->execute(['store_id' => $store_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTemplates()
    {
        $stmt = $this->db->prepare("SELECT * FROM templates");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function pickTemplate($store_id, $template_id)
    {
        $template = $this->db->prepare("SELECT * FROM templates WHERE id=:template_id");
        $template->execute(['template_id' => $template_id]);
        $template = $template->fetch(PDO::FETCH_ASSOC);

        $store = $this->db->prepare("SELECT * FROM stores WHERE id=:store_id");
        $store->execute(['store_id' => $store_id]);
        $store = $store->fetch(PDO::FETCH_ASSOC);

        if (!isset($template) || !isset($store)) {
            header("Location: " . ROOT . 'dashboard/storefront/template');
        };

        $stmt = $this->db->prepare("UPDATE stores SET template_id=:template_id WHERE id=:store_id;");
        $stmt->execute(['template_id' => $template_id, 'store_id' => $store_id]);
        header("Location: " . ROOT . 'dashboard/storefront/template?success=true');
    }

    public function removeTemplate($store_id)
    {
        $remove_store_contents = $this->db->prepare("DELETE FROM store_contents WHERE store_id = :store_id");
        $remove_store_contents->execute(['store_id' => $store_id]);

        $update_store = $this->db->prepare("UPDATE stores SET template_id = NULL WHERE id = :store_id LIMIT 1");
        $update_store->execute(['store_id' => $store_id]);
    }

    public function updateCustomizeTemplate($store_id)
    {
        $store = $this->db->prepare("SELECT * FROM stores WHERE id=:store_id");
        $store->execute(['store_id' => $store_id]);
        $store = $store->fetch(PDO::FETCH_ASSOC);

        $template_fields = $this->db->prepare("SELECT * FROM template_fields WHERE template_id=:template_id");
        $template_fields->execute(['template_id' => $store['template_id']]);
        $template_fields = $template_fields->fetchAll(PDO::FETCH_ASSOC);


        foreach ($template_fields as $row) {
            echo "<pre>";
            $field_value = ($_POST[$row['field_name']]);
            print_r($field_value);

            $subquery = $this->db->prepare("INSERT INTO store_contents (store_id, template_field_id, field_value)
            VALUES (:store_id,:template_field_id,:field_value)
            ON DUPLICATE KEY UPDATE
            field_value = VALUES(field_value);
            ");

            $subquery->execute(['store_id' => $store_id, 'template_field_id' => $row['id'], 'field_value' => $field_value]);
        }
    }

    public function getCustomizeTemplate($store_id)
    {
        $stmt = $this->db->prepare("SELECT * FROM stores JOIN templates ON stores.template_id = templates.id WHERE stores.id=:store_id LIMIT 1");
        $stmt->execute(['store_id' => $store_id]);
        $stmt = $stmt->fetch(PDO::FETCH_ASSOC);
        if (isset($stmt['file_path'])) {
            $file_path = $stmt['file_path'];
        } else {
            $file_path = '';
        };

        $store_contents = $this->db->prepare("SELECT * FROM template_fields LEFT JOIN store_contents
        ON template_fields.id = store_contents.template_field_id
         WHERE template_id=:template_id AND (store_id=:store_id OR store_id IS NULL)");
        $store_contents->execute(['template_id' => $stmt['template_id'], 'store_id' => $store_id]);
        $store_contents = $store_contents->fetchAll(PDO::FETCH_ASSOC);


        return ['store_contents' => $store_contents, 'file_path' => $file_path];
    }

    public function getTemplatePreviewData($template_id)
    {
        // Get template information
        $stmt = $this->db->prepare("SELECT * FROM templates WHERE id = :template_id LIMIT 1");
        $stmt->execute(['template_id' => $template_id]);
        $template = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$template) {
            return null;
        }

        // Get all default values for template fields
        $fields_stmt = $this->db->prepare("SELECT field_name, default_value FROM template_fields WHERE template_id = :template_id");
        $fields_stmt->execute(['template_id' => $template_id]);
        $fields = $fields_stmt->fetchAll(PDO::FETCH_ASSOC);

        // Build data array with default values
        $data = [];
        foreach ($fields as $field) {
            $data[$field['field_name']] = $field['default_value'];
        }

        return [
            'template' => $template,
            'data' => $data
        ];
    }
}

<?php

class PreviewTemplate extends Controller
{
    public function __construct($PREV_URL, $URL, $SLUG_DATA = NULL)
    {
        require "../app/core/ChainRouter.php";
    }

    public function index()
    {
        $template_id = $_GET['template_id'] ?? null;
        if ($template_id) {
            $storefrontM = $this->model("StorefrontModel");
            $templateData = $storefrontM->getTemplatePreviewData($template_id);

            if ($templateData && isset($templateData['template']['file_path'])) {
                $template_path = $templateData['template']['file_path'];
                // Pass both template info and default field values
                $this->view("templates/".$template_path."/render/home", [
                    'storecode' => 'preview',
                    'template' => $templateData['template'],
                    'content' => $templateData['data'],
                    'products' => [
                        [
                            'id' => 1,
                            'name' => 'Sample Product 1',
                            'description' => 'This is a description for Sample Product 1.',
                            'price' => 1999.99,
                            'stock_quantity' => 10,
                            'images' => ['https://placehold.co/600x400']
                        ],
                        [
                            'id' => 2,
                            'name' => 'Sample Product 2',
                            'description' => 'This is a description for Sample Product 2.',
                            'price' => 2999.99,
                            'stock_quantity' => 5,
                            'images' => ['https://placehold.co/600x400']
                        ],
                        [
                            'id' => 3,
                            'name' => 'Sample Product 3',
                            'description' => 'This is a description for Sample Product 3.',
                            'price' => 3999.99,
                            'stock_quantity' => 0,
                            'images' => ['https://placehold.co/600x400']
                        ]
                    ]
                ]);
            } else {
                echo "Template not found or invalid template ID";
            }
        } else {
            echo "Template ID is required";
        }
    }
}

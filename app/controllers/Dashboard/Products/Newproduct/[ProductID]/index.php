<?php
class ProductID extends Controller
{
    private $product_id;

    public function __construct($PREV_URL, $URL, $SLUG_DATA = NULL)
    {
        require_once __DIR__ . "/../../../../../models/ProductModel.php";
        
        // Extract product ID from SLUG_DATA
        $this->product_id = $SLUG_DATA['ProductID'];
        
        require "../app/core/ChainRouter.php";
    }

    public function index()
    {
        // This handles the product ID level - redirect to basepricing or other actions
        header('Location: ' . ROOT . 'dashboard/products/newproduct/' . $this->product_id . '/basepricing');
        exit;
    }
}
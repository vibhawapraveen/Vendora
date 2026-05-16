<?php

class ProductId extends Controller
{
    private $product_id;

    public function __construct($PREV_URL, $URL, $SLUG_DATA = NULL)
    {
        Session::requireRole(["seller"]);

        // Store product_id from URL slug
        $this->product_id = $SLUG_DATA['ProductId'] ?? null;

        require "../app/core/ChainRouter.php";
    }

    public function index()
    {
        // This handles /dashboard/products/[product_id]
        // Redirect to products list
        header('Location: ' . ROOT . 'dashboard/products/all');
        exit;
    }
}

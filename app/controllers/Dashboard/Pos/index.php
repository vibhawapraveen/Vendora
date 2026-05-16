<?php

class Pos extends Controller
{
    public function __construct($PREV_URL, $URL, $SLUG_DATA = NULL)
    {
        require "../app/core/ChainRouter.php";
    }

    public function index()
    {
        $user = Session::user();
        $store_id = $user['store_id'] ?? null;

        if (!$store_id) {
            die("Store ID not found");
        }

        $posModel = $this->model("PosModel");
        $products = $posModel->getProductsByStore($store_id);

        $this->view("dashboard/pos/index", [
            'products' => $products
        ]);
    }
}

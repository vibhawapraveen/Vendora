<?php
class Cart extends Controller
{
    private $StoreCode;
    public function __construct($PREV_URL, $URL, $SLUG_DATA = NULL)
    {
        $this->StoreCode = $SLUG_DATA['StoreCode'];
        require "../app/core/ChainRouter.php";
    }

    public function index()
    {
        $storeM = $this->model("StoreModel");
        $store = ($storeM->getStoreById($this->StoreCode));

        $storefrontCustomerM = $this->model("StorefrontCustomerModel");
        $storefrontContent = $storefrontCustomerM->getStorefrontContent($store['store_id'], $store['template_id']);


        $this->view("templates/" . $store['file_path'] . "/render/cart", ['content'=>$storefrontContent['store_contents'], 'storecode'=>$this->StoreCode]);

        // print_r($storefrontContent);
    }
}

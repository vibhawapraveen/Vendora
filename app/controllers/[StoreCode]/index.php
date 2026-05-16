<?php
class StoreCode extends Controller
{
    private $StoreCode;
    public function __construct($PREV_URL, $URL, $SLUG_DATA = NULL)
    {
        $this->StoreCode = $SLUG_DATA['StoreCode'];

        $storeM = $this->model("StoreModel");
        $store = ($storeM->getStoreById($this->StoreCode));
        
        if (!$store || !$store['visibility']) {
            $this->view("404");
            die;
        }

        $storeM->updateStoreViews($store['store_id']);

        require "../app/core/ChainRouter.php";
    }

    public function index()
    {
        $storeM = $this->model("StoreModel");
        $store = ($storeM->getStoreById($this->StoreCode));

        $storefrontCustomerM = $this->model("StorefrontCustomerModel");

        $storefrontContent = $storefrontCustomerM->getStorefrontContent($store['store_id'], $store['template_id']);
        $products = $storefrontCustomerM->getProductsStorefront($store['store_id']);

        // pre($storefrontContent);

        $templateM = $this->model("Template_" . $storefrontContent['file_path'] . "_Model");
        $homeContents = $templateM->getHomePageContents($store['store_id']);




        $this->view("templates/" . $store['file_path'] . "/render/home", ['content' => $storefrontContent['store_contents'], 'storecode' => $this->StoreCode, 'products' => $products, 'home_contents' => $homeContents]);

        // print_r($storefrontContent);
    }
}

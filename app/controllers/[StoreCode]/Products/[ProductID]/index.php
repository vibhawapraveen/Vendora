<?php
class ProductID extends Controller
{
    private $StoreCode;
    private $ProductID;
    public function __construct($PREV_URL, $URL, $SLUG_DATA = NULL)
    {
        $this->StoreCode = $SLUG_DATA['StoreCode'];
        $this->ProductID = $SLUG_DATA['ProductID'];
        require "../app/core/ChainRouter.php";
    }

    public function index()
    {
        $storeM = $this->model("StoreModel");
        $store = ($storeM->getStoreById($this->StoreCode));

        if(!isset($this->ProductID) || empty($this->ProductID)){
            $this->view("404-product");
            die;
        }

        $storefrontCustomerM = $this->model("StorefrontCustomerModel");

        $storefrontContent = $storefrontCustomerM->getStorefrontContent($store['store_id'],$store['template_id']);
        $product = $storefrontCustomerM->getProductById($this->ProductID, $store['store_id']);

        if(!$product){
            $this->view("404-product");
            die;
        }



        $this->view("templates/" . $store['file_path'] . "/render/product", ['content' => $storefrontContent['store_contents'], 'storecode' => $this->StoreCode, 'product'=>$product]);

        // print_r($product);
    }
}

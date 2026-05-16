<?php
class Validate extends Controller
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

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $cart = jsonRequest();

            $productCheckout = $this->model("ProductCheckout");

            $validCart = [];
            foreach ($cart as &$item) {
                //                 Array
                // (
                //     [id] => 00e4438c-5d74-4eba-87f7-c12f8e3ade6a
                //     [name] => Iphone 15
                //     [price] => 10
                //     [quantity] => 1
                //     [variantId] => 83638428-c21a-476d-a231-40652fbba165
                //     [variantSku] => PROD-128-WHI-003
                //     [variantDescription] => Storage: 128 GB, Color: White
                //     [image] => http://localhost/vendora/public/assets/img/products/variant_83638428-c21a-476d-a231-40652fbba165_1775446258_69d328f2ee5ce.jpg
                // )

                if (isset($item['variantId'])) {
                    // Multi
                    $dbVariant = $productCheckout->getVariantProduct($store['store_id'], $item['id'], $item['variantId']);
                    if (!$dbVariant || $dbVariant['stock_quantity'] < $item['quantity']) {
                        continue;
                    }
                    $item['price'] = $dbVariant['price'];
                    $validCart[] = $item;

                } else {
                    // Single
                    $dbProd = $productCheckout->getSingleVariantProduct($store['store_id'], $item['id']);
                    if (!$dbProd || $dbProd['visibility'] == 0 || $dbProd['stock_quantity'] < $item['quantity']) {
                        continue;
                    }
                    $item['price'] = $dbProd['price'];
                    $validCart[] = $item;
                }
            }


            jsonResponse([
                'success' => true,
                'cart' => $validCart
            ], 200);
        }
    }
}

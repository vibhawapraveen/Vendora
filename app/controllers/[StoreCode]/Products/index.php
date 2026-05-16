<?php
class Products extends Controller
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

        $filterOptions = [
            'search' => isset($_GET['search']) ? $_GET['search'] : '',
            'sort' => isset($_GET['sort']) ? $_GET['sort'] : 'newest',
            'category' => isset($_GET['category']) ? $_GET['category'] : '',
            'page' => isset($_GET['page']) ? $_GET['page'] : 1,
            'limit' => 12
        ];

        $result = $storefrontCustomerM->getProductsStorefrontFiltered($store['store_id'], $filterOptions);
        $categories = $storefrontCustomerM->getStorefrontCategories($store['store_id']);

        $storefrontContent = $storefrontCustomerM->getStorefrontContent($store['store_id'], $store['template_id']);

        // pre($storefrontContent);

        $this->view("templates/" . $store['file_path'] . "/render/shop", [
            'store' => $store,
            'storecode' => $this->StoreCode,
            'products' => $result['products'],
            'content' => $storefrontContent['store_contents'],
            'categories' => $categories,
            'pagination' => [
                'totalProducts' => $result['totalProducts'],
                'totalPages' => $result['totalPages'],
                'currentPage' => $result['currentPage'],
                'startIndex' => $result['startIndex'],
                'limit' => $result['limit'],
                'search' => $filterOptions['search'],
                'sort' => $filterOptions['sort'],
                'category' => $filterOptions['category']
            ]
        ]);
    }
}

<?php
class Orders extends Controller
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

        if (!isset(Session::user()['customer_id'])) {
            header("Location: " . ROOT . "authcustomer/?redirect_url=" . urlencode(ROOT . $this->StoreCode . "/orders"));
            die;
        }

        $orderM = $this->model("OrderModel");
        // $orders = $orderM->getCustomerOrders(Session::user()['customer_id'], $store['store_id']);

        $filters = [
            'start_date' => NULL,
            'end_date' => NULL
        ];
        isset($_GET['start_date']) && $filters['start_date'] = $_GET['start_date'];
        isset($_GET['end_date']) && $filters['end_date'] = $_GET['end_date'];

        $orders = $orderM->getCustomerOrdersFiltered(Session::user()['customer_id'], $store['store_id'],$filters);

        // Fetch items for each order
        foreach ($orders as &$order) {
            $order['items'] = $orderM->getOrderItems($order['id']);
        }

        $this->view("templates/orders", [
            'storecode' => $this->StoreCode,
            'orders' => $orders
        ]);

        // print_r($storefrontContent);
    }
}

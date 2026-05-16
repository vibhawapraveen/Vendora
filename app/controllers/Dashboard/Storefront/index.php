<?php

class Storefront extends Controller
{
    public function __construct($PREV_URL, $URL, $SLUG_DATA = NULL)
    {
        require "../app/core/ChainRouter.php";
    }

    public function index()
    {
        if ($_SERVER['REQUEST_METHOD'] == "GET") {
            $this->GET();
        } elseif ($_SERVER['REQUEST_METHOD'] == "POST") {
            $this->POST();
        }
    }

    public function GET()
    {
        $sellerM = $this->model('SellerModel');
        $strip_data = $sellerM->getStripeAccount(Session::user()['id']);
        $stripe_account_id = $strip_data['stripe_account_id'];

        $storeModel = $this->model('StoreModel');
        $storefrontOverviewData = $storeModel->getStorefrontOverview(Session::user()['store_id']);
        $storeViews = $storeModel->getStoreViews(Session::user()['store_id']);
        $storeViewsOverTime = $storeModel->getStoreViewsOverTime(Session::user()['store_id'], 30);
        $storeStats = $storeModel->getStoreViewsStats(Session::user()['store_id']);
        $this->view('dashboard/storefront/index', [
            'data' => $storefrontOverviewData,
            'views' => $storeViews,
            'viewsOverTime' => $storeViewsOverTime,
            'stats' => $storeStats,
            'stripe_account_id' => $stripe_account_id
        ]);
    }

    public function POST()
    {
        $storeModel = $this->model('StoreModel');
        if ($_POST['action'] == 'toggle_visibility') {
            $currentData = $storeModel->getStorefrontOverview(Session::user()['store_id']);
            $newVisibility = $currentData['visibility'] ? 0 : 1;

            $storeModel->updateStoreVisibility(Session::user()['store_id'], $newVisibility);
            header("Location: " . ROOT . "dashboard/storefront?success=" . urlencode("Store visibility updated successfully."));
            exit();
        }
        // Handle POST requests here
    }
}

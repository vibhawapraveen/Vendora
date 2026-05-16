<?php

class Stripeaccount extends Controller
{
    public function __construct($PREV_URL, $URL, $SLUG_DATA = NULL)
    {
        Session::requireRole(["seller"]);
        require "../app/core/ChainRouter.php";
    }

    public function index()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'GET') {
            $this->GET();
        } elseif ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $this->POST();
        }
    }

    public function GET()
    {
        // Get current user's store_id from session
        $user = Session::user();
        $store_id = $user['store_id'];

        $sellerM = $this->model('SellerModel');

        $stripeData = $sellerM->getStripeAccount($user['id']);
        $isConnected = !empty($stripeData['stripe_account_id']);

        $viewData = [
            'isConnected' => $isConnected,
            'account' => null,
            'stripeAccount' => null
        ];

        if ($isConnected) {
            require_once '../app/libs/stripe-php/init.php';
            $config = require '../app/core/stripe.php';
            \Stripe\Stripe::setApiKey($config['secret_key']);

            $stripeAccountId = $stripeData['stripe_account_id'];
            $stripeAccount = \Stripe\Account::retrieve($stripeAccountId);

            $viewData['account'] = $stripeAccount['individual'];
            $viewData['stripeAccount'] = $stripeAccount;
        }

        // Pass data to view
        $this->view('dashboard/earnings/stripe-account', $viewData);
    }

    public function POST()
    {
        $sellerM = $this->model('SellerModel');
        $sellerM->disconnectStripeAccount(Session::user()['id']);
        header("Location: " . ROOT . "dashboard/earnings/stripeaccount");
    }
}

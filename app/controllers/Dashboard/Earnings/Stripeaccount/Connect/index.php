<?php

class Connect extends Controller
{
    private $config = [];
    public function __construct($PREV_URL, $URL, $SLUG_DATA = NULL)
    {
        Session::requireRole(["seller"]);
        require "../app/core/ChainRouter.php";
    }

    public function index()
    {
        $sellerM = $this->model('SellerModel');

        require_once '../app/libs/stripe-php/init.php';
        $config = require '../app/core/stripe.php';

        \Stripe\Stripe::setApiKey($config['secret_key']);

        $account = \Stripe\Account::create([
            'type' => 'express',
            'email' => Session::user()['email'],
            'capabilities' => [
                'transfers' => ['requested' => true],
            ]
        ]);

        $stripeAccountId = $account->id;
        $sellerM->saveStripeAccount(Session::user()['id'], $stripeAccountId);

        $accountLink = \Stripe\AccountLink::create([
            'account' => $account->id,
            'refresh_url' => ROOT . 'dashboard/earnings/stripeaccount',
            'return_url' => ROOT . 'dashboard/earnings/stripeaccount?account_id=' . $account->id,
            'type' => 'account_onboarding',
        ]);

        header("Location: " . $accountLink->url);
        exit;
    }
}

<?php

class Delete extends Controller
{
    public function __construct($PREV_URL, $URL, $SLUG_DATA = NULL)
    {
        require "../app/core/ChainRouter.php";
    }

    public function index()
    {
        $store_id = Session::user()['store_id'];

        $storefrontM = $this->model("StorefrontModel");

        $storefrontContent = ($storefrontM->getStorefrontEditData($store_id));

        $templateM = $this->model("Template_" . $storefrontContent['file_path'] . "_Model");

        $templateM->removeTemplate($store_id);
        $storefrontM->removeTemplate($store_id);


        header("Location: " . ROOT . "dashboard/storefront/template");
    }
}

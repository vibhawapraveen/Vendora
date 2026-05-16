<?php

class Template extends Controller
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
        $storefrontM = $this->model("StorefrontModel");
        $currentTemplate = $storefrontM->getCurrentTemplate(Session::user()['store_id']);

        if ($currentTemplate) {
            $this->view("dashboard/storefront/template", ['template' => $currentTemplate[0]]);
        } else {
            $templates = $storefrontM->getTemplates();
            $this->view("dashboard/storefront/template-browser", ['templates' => $templates]);
        }
    }

    public function POST()
    {
        $storefrontM = $this->model("StorefrontModel");
        $templateId = $_POST['templateId'];
        $storefrontM->pickTemplate(Session::user()['store_id'], $templateId);
    }
}

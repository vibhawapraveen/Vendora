<?php

class Customize extends Controller
{
    public function __construct($PREV_URL, $URL, $SLUG_DATA = NULL)
    {
        require "../app/core/ChainRouter.php";
    }

    public function index()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'GET') {
            $this->GET();
        } elseif ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (isset($_POST['METHOD']) && $_POST['METHOD'] == "DELETE") {
                $this->DELETE();
                return;
            }
            $this->POST();
        }
    }

    public function GET()
    {
        $store_id = Session::user()['store_id'];
        $storefrontCustomerM = $this->model("StorefrontCustomerModel");
        $storefrontContents = $storefrontCustomerM->getStorefrontContent($store_id);

        if ($storefrontContents['file_path']) {

            $tab = "index";
            $tab_contents = $storefrontContents['store_contents'];

            if (isset($_GET['tab'])) {
                $tab = $_GET['tab'];
                $templateM = $this->model("Template_" . $storefrontContents['file_path'] . "_Model");
                $res = $templateM->getEditTabContent($tab, $store_id);
                if (isset($res)) {
                    $tab_contents = $res;
                }
            }

            $this->view("dashboard/storefront/customize", ['file_path' => $storefrontContents['file_path'], 'tab_contents' => $tab_contents, 'tab' => $tab]);
        } else {
            $this->view("dashboard/storefront/customize-info");
        }
    }

    public function POST()
    {
        $storefrontM = $this->model("StorefrontModel");

        $store_id = Session::user()['store_id'];
        $storefrontCustomerM = $this->model("StorefrontCustomerModel");
        $storefrontContents = $storefrontCustomerM->getStorefrontContent($store_id);

        $tab = isset($_GET['tab']) ? $_GET['tab'] : NULL;
        if (isset($tab) && $tab != "index") {
            $templateM = $this->model("Template_" . $storefrontContents['file_path'] . "_Model");
            $templateM->saveTabContents($tab, $store_id);
        } else {

            if (!empty($_FILES)) {
                foreach ($_FILES as $fieldName => $fileInfo) {
                    if ($fileInfo['error'] !== 0) continue;

                    $tmpName = $fileInfo['tmp_name'];
                    $extension = pathinfo($fileInfo['name'], PATHINFO_EXTENSION);

                    $fileName = uuidv4() . "." . $extension;

                    $uploadDir = "../public/assets/img/user-uploads/";
                    $uploadPath = $uploadDir . $fileName;

                    if (move_uploaded_file($tmpName, $uploadPath)) {
                        // Save the relative path to $_POST so updateCustomizeTemplate can use it
                        $_POST[$fieldName] = ROOT . "/assets/img/user-uploads/" . $fileName;
                    }
                }
            }
            $storefrontM->updateCustomizeTemplate(Session::user()['store_id']);
        }
        header(
            'Location: ' . ROOT . 'dashboard/storefront/customize?success=true' .
                ($tab ? "&tab=" . $tab : "")
        );
    }

    public function DELETE()
    {
        $storefrontM = $this->model("StorefrontModel");

        $store_id = Session::user()['store_id'];
        $storefrontCustomerM = $this->model("StorefrontCustomerModel");
        $storefrontContents = $storefrontCustomerM->getStorefrontContent($store_id);

        $tab = $_GET['tab'];
        if (isset($tab)) {
            $templateM = $this->model("Template_" . $storefrontContents['file_path'] . "_Model");
            $templateM->deleteTabContents($tab, $store_id);
        }

        header(
            'Location: ' . ROOT . 'dashboard/storefront/customize?success=true' .
                ($tab ? "&tab=" . $tab : "")
        );
    }
}

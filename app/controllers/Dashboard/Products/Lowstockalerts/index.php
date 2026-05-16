<?php

class Lowstockalerts extends Controller
{
    public function __construct($PREV_URL, $URL, $SLUG_DATA = NULL)
    {
        Session::requireRole(["seller"]);
        require "../app/core/ChainRouter.php";
    }

    public function index()
    {
        $user = Session::user();
        $store_id = $user['store_id'];

        $productModel = $this->model("ProductModel");

        // Export all open low-stock alerts as printable view (Save as PDF from print dialog).
        if (isset($_GET['export']) && $_GET['export'] === 'pdf') {
            $exportAlerts = $productModel->getOpenStockAlerts($store_id);

            foreach ($exportAlerts as &$alert) {
                $alert['first_image'] = $productModel->getFirstProductImage($alert['product_id'], (int)$alert['is_variant'] === 1);
            }
            unset($alert);

            $this->view('dashboard/products/print_lowstockalerts', [
                'alerts' => $exportAlerts,
                'store_name' => Session::user()['store_name'] ?? 'Your Store'
            ]);
            exit;
        }

        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $page = max(1, $page);
        $limit = 5;

        $stats = $productModel->getOpenStockAlertStats($store_id);
        $alerts = $productModel->getOpenStockAlertsPaginated($store_id, $page, $limit);
        $totalAlerts = $productModel->getTotalOpenStockAlerts($store_id);
        $totalPages = max(1, (int)ceil($totalAlerts / $limit));

        if ($page > $totalPages) {
            $page = $totalPages;
            $alerts = $productModel->getOpenStockAlertsPaginated($store_id, $page, $limit);
        }

        foreach ($alerts as &$alert) {
            $alert['first_image'] = $productModel->getFirstProductImage($alert['product_id'], (int)$alert['is_variant'] === 1);
        }
        unset($alert);

        $this->view('dashboard/products/lowstockalerts', [
            'stats' => $stats,
            'alerts' => $alerts,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'totalAlerts' => $totalAlerts,
            'limit' => $limit
        ]);
    }
}

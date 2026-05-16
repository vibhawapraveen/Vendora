<?php

class Managecategories extends Controller
{
    private $categoryModel;

    public function __construct($PREV_URL, $URL, $SLUG_DATA = NULL)
    {
        Session::requireRole(["seller"]);
        require "../app/core/ChainRouter.php";
        require_once __DIR__ . "/../../../../models/CategoryModel.php";

        $this->categoryModel = new CategoryModel();
    }

    private function getCategoryModel()
    {
        if ($this->categoryModel === null) {
            require_once __DIR__ . "/../../../../models/CategoryModel.php";
            $this->categoryModel = new CategoryModel();
        }

        return $this->categoryModel;
    }

    public function index()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action = $_POST['action'] ?? 'create';

            if ($action === 'update_status') {
                $this->updateCategoryStatus();
            } elseif ($action === 'delete_category') {
                $this->deleteCategory();
            } else {
                $this->saveCategory();
            }
            return;
        }

        $user = Session::user();
        $store_id = $user['store_id'];
        $categoryModel = $this->getCategoryModel();

        // Get current page from URL parameter
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $page = max(1, $page); // Ensure page is at least 1
        $limit = 10; // Categories per page

        $totalCategories = $categoryModel->getTotalCategoriesByStore($store_id);
        $categories = $categoryModel->getCategoriesByStorePaginated($store_id, $page, $limit);
        $totalPages = ceil($totalCategories / $limit);

        $this->view('dashboard/products/managecategories', [
            'categoryList' => $categories,
            'totalCategories' => $totalCategories,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'limit' => $limit
        ]);
    }

    private function saveCategory()
    {
        $user = Session::user();
        $store_id = $user['store_id'];

        $name = trim($_POST['category_name'] ?? '');

        if ($name === '') {
            header('Location: ' . ROOT . 'dashboard/products/managecategories?error=empty');
            exit;
        }

        if ($this->getCategoryModel()->categoryExistsByName($store_id, $name)) {
            header('Location: ' . ROOT . 'dashboard/products/managecategories?error=duplicate');
            exit;
        }

        $ok = $this->getCategoryModel()->createCategory($store_id, $name);

        if ($ok) {
            header('Location: ' . ROOT . 'dashboard/products/managecategories?success=1');
        } else {
            header('Location: ' . ROOT . 'dashboard/products/managecategories?error=1');
        }
        exit;
    }

    private function updateCategoryStatus()
    {
        $user = Session::user();
        $store_id = $user['store_id'];

        $category_id = trim($_POST['category_id'] ?? '');
        $status = trim($_POST['category_status'] ?? 'active');

        if ($category_id === '' || !in_array($status, ['active', 'inactive'], true)) {
            header('Location: ' . ROOT . 'dashboard/products/managecategories?error=1');
            exit;
        }

        $model = $this->getCategoryModel();

        if (!$model->categoryBelongsToStore($category_id, $store_id)) {
            header('Location: ' . ROOT . 'dashboard/products/managecategories?error=1');
            exit;
        }

        $visibility = $status === 'active' ? 1 : 0;
        $ok = $model->setProductsVisibilityByCategory($category_id, $store_id, $visibility);

        if ($ok) {
            header('Location: ' . ROOT . 'dashboard/products/managecategories?success=status_updated');
        } else {
            header('Location: ' . ROOT . 'dashboard/products/managecategories?error=1');
        }
        exit;
    }

    private function deleteCategory()
    {
        $user = Session::user();
        $store_id = $user['store_id'];
        $category_id = trim($_POST['category_id'] ?? '');

        if ($category_id === '') {
            header('Location: ' . ROOT . 'dashboard/products/managecategories?error=1');
            exit;
        }

        $model = $this->getCategoryModel();

        if (!$model->categoryBelongsToStore($category_id, $store_id)) {
            header('Location: ' . ROOT . 'dashboard/products/managecategories?error=1');
            exit;
        }

        $ok = $model->deleteCategoryAndSoftDeleteProducts($category_id, $store_id);

        if ($ok) {
            header('Location: ' . ROOT . 'dashboard/products/managecategories?success=deleted');
        } else {
            header('Location: ' . ROOT . 'dashboard/products/managecategories?error=1');
        }

        exit;
    }
}

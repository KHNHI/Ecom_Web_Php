<?php
/**
 * CategoriesController - Admin Category Management
 * Quản lý danh mục cho admin - Tuân thủ chuẩn MVC và OOP
 */
class CategoriesController extends BaseController {
    private $categoryService;
    private $categoryModel;
    
    // Upload configuration
    private $uploadPath = 'public/uploads/categories/';
    private $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    private $maxFileSize = 2097152; // 2MB

    public function __construct() {
        // Dependency Injection
        $this->categoryService = new CategoryService();
        $this->categoryModel = $this->model('Category');
    }

    /**
     * Hiển thị danh sách danh mục
     * Method: GET
     */
    public function index() {
        try {
            // Lấy tất cả categories từ Service
            $categories = $this->categoryService->getAllCategoriesWithStats();

            $data = [
                'title' => 'Danh Mục',
                'categories' => $categories
            ];

            // Render admin page with layout
            $this->renderAdminPage('admin/pages/categories', $data);

        } catch (Exception $e) {
            $this->view('admin/error', ['message' => 'Có lỗi xảy ra: ' . $e->getMessage()]);
        }
    }

    /**
     * Hiển thị form thêm danh mục mới
     * Method: GET
     */
    public function showAddForm() {
        try {
            // Lấy danh sách parent categories (nếu có hỗ trợ nested categories)
            $parentCategories = $this->categoryModel->getParentCategories();
            
            // Lấy old input nếu có lỗi validation
            $oldInput = $_SESSION['old_input'] ?? [];
            unset($_SESSION['old_input']);
            
            $data = [
                'title' => 'Thêm Danh Mục Mới',
                'pageTitle' => 'Thêm Danh Mục',
                'breadcrumb' => 'Home > Danh Mục > Thêm Mới',
                'parentCategories' => $parentCategories,
                'oldInput' => $oldInput
            ];

            // Render view qua layout
            $this->renderAdminPage('admin/pages/add-category', $data);

        } catch (Exception $e) {
            $_SESSION['error'] = 'Có lỗi xảy ra: ' . $e->getMessage();
            $this->redirect(BASE_URL . '/admin/categories');
        }
    }

    /**
     * Tạo danh mục mới
     * Method: POST
     */
    public function create() {
        // Chỉ chấp nhận POST request
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $_SESSION['error'] = 'Invalid request method!';
            $this->redirect(BASE_URL . '/admin/categories');
            return;
        }

        try {
            // Validate input data
            $validationErrors = $this->validateCategoryData($_POST);
            
            if (!empty($validationErrors)) {
                $_SESSION['error'] = implode('<br>', $validationErrors);
                $_SESSION['old_input'] = $_POST;
                $this->redirect(BASE_URL . '/admin/add-category');
                return;
            }

            // Tạo category qua Service (xử lý cả image upload)
            $categoryId = $this->categoryService->createCategoryWithImage($_POST, $_FILES);
            
            if (!$categoryId) {
                throw new Exception('Không thể tạo danh mục trong database');
            }

            $_SESSION['success'] = 'Tạo danh mục thành công!';
            error_log("Category created successfully with ID: $categoryId");
            
            $this->redirect(BASE_URL . '/admin/categories');
            
        } catch (Exception $e) {
            $_SESSION['error'] = 'Lỗi: ' . $e->getMessage();
            $_SESSION['old_input'] = $_POST;
            error_log('CategoriesController::create Error: ' . $e->getMessage());
            $this->redirect(BASE_URL . '/admin/add-category');
        }
    }

    /**
     * Hiển thị form chỉnh sửa danh mục
     */
    public function showEditForm() {
        try {
            $categoryId = $_GET['id'] ?? null;
            
            if (!$categoryId) {
                throw new Exception('Không tìm thấy ID danh mục');
            }

            $category = $this->categoryModel->findById($categoryId);
            
            if (!$category) {
                throw new Exception('Danh mục không tồn tại');
            }

            $parentCategories = $this->categoryModel->getParentCategories();
            
            $data = [
                'title' => 'Chỉnh Sửa Danh Mục',
                'category' => $category,
                'parentCategories' => $parentCategories,
                'pageTitle' => 'Chỉnh Sửa Danh Mục',
                'breadcrumb' => 'Home > Danh Mục > Chỉnh Sửa'
            ];

            $this->renderAdminPage('admin/pages/edit-category', $data);

        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            $this->redirect(BASE_URL . '/admin/categories');
        }
    }

    /**
     * Cập nhật danh mục
     * Method: POST
     */
    public function update() {
        // Chỉ chấp nhận POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $_SESSION['error'] = 'Invalid request method!';
            $this->redirect(BASE_URL . '/admin/categories');
            return;
        }

        try {
            $categoryId = $_GET['id'] ?? null;
            
            if (!$categoryId) {
                throw new Exception('Không tìm thấy ID danh mục');
            }

            // Validate input
            $validationErrors = $this->validateCategoryData($_POST);
            
            if (!empty($validationErrors)) {
                $_SESSION['error'] = implode('<br>', $validationErrors);
                $this->redirect(BASE_URL . '/admin/edit-category?id=' . $categoryId);
                return;
            }

            // Update qua Service
            if ($this->categoryService->updateCategory($categoryId, $_POST, $_FILES)) {
                $_SESSION['success'] = 'Cập nhật danh mục thành công!';
            } else {
                $_SESSION['error'] = 'Có lỗi xảy ra khi cập nhật danh mục!';
            }
            
        } catch (Exception $e) {
            $_SESSION['error'] = 'Lỗi: ' . $e->getMessage();
            error_log('CategoriesController::update Error: ' . $e->getMessage());
        }
        
        $this->redirect(BASE_URL . '/admin/categories');
    }

    /**
     * Xóa danh mục
     * Method: POST (via action parameter)
     */
    public function delete() {
        try {
            $categoryId = $_GET['id'] ?? null;
            
            if (!$categoryId) {
                throw new Exception('Không tìm thấy ID danh mục');
            }

            if ($this->categoryService->deleteCategory($categoryId)) {
                $_SESSION['success'] = 'Xóa danh mục thành công!';
            } else {
                $_SESSION['error'] = 'Có lỗi xảy ra khi xóa danh mục!';
            }
        } catch (Exception $e) {
            $_SESSION['error'] = 'Lỗi: ' . $e->getMessage();
            error_log('CategoriesController::delete Error: ' . $e->getMessage());
        }
        
        $this->redirect(BASE_URL . '/admin/categories');
    }

    // =================== PRIVATE HELPER METHODS (OOP Best Practice) ===================
    
    /**
     * Validate dữ liệu danh mục
     * @return array Mảng các lỗi validation
     */
    private function validateCategoryData($data) {
        $errors = [];

        // Validate tên danh mục
        if (empty($data['name']) || strlen(trim($data['name'])) < 2) {
            $errors[] = 'Tên danh mục phải có ít nhất 2 ký tự';
        }

        // Validate icon_url nếu có
        if (!empty($data['icon_url']) && !filter_var($data['icon_url'], FILTER_VALIDATE_URL)) {
            $errors[] = 'Icon URL không hợp lệ';
        }

        // Validate image nếu có upload
        if (isset($_FILES['image']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {
            $imageErrors = $this->validateImageUpload($_FILES['image']);
            $errors = array_merge($errors, $imageErrors);
        }

        return $errors;
    }

    /**
     * Validate file upload
     * @return array Mảng các lỗi
     */
    private function validateImageUpload($file) {
        $errors = [];

        if ($file['error'] !== UPLOAD_ERR_OK && $file['error'] !== UPLOAD_ERR_NO_FILE) {
            $errors[] = 'Lỗi khi upload file';
            return $errors;
        }

        // Check file size
        if ($file['size'] > $this->maxFileSize) {
            $errors[] = 'Kích thước file không được vượt quá 2MB';
        }

        // Check file extension
        $fileExt = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($fileExt, $this->allowedExtensions)) {
            $errors[] = 'Chỉ chấp nhận file: JPG, JPEG, PNG, GIF, WEBP';
        }

        return $errors;
    }

    /**
     * Redirect helper method
     */
    private function redirect($url) {
        header("Location: $url");
        exit;
    }
}

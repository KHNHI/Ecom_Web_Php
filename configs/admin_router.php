<?php
/**
 * AdminRouter - Routing cho Admin Panel
 * Tương tự Route class nhưng dành cho admin
 */
class AdminRouter {
    protected $controller = 'DashboardController'; // Controller mặc định cho admin
    protected $method = 'index';
    protected $params = [];

    public function __construct() {
        $url = $this->getUrl();
       
        // Xử lý Controller với Clean URLs cho admin
        if (isset($url[0])) {
            // Clean URL routing cho admin
            switch ($url[0]) {
                case 'login':
                    $this->controller = 'LoginController';
                    $this->method = 'index';
                    unset($url[0]);
                    break;
                    
                case 'logout':
                    $this->controller = 'LoginController';
                    $this->method = 'logout';
                    unset($url[0]);
                    break;
                    
                case 'dashboard':
                    $this->controller = 'DashboardController';
                    $this->method = 'index';
                    unset($url[0]);
                    break;
                    
                case 'products':
                    $this->controller = 'ProductsController';
                    // Check for action parameter
                    if (isset($_GET['action'])) {
                        switch ($_GET['action']) {
                            case 'create':
                                $this->method = 'create';
                                break;
                            case 'update':
                                $this->method = 'update';
                                break;
                            case 'delete':
                                $this->method = 'delete';
                                break;
                            default:
                                $this->method = 'index';
                        }
                    } else {
                        $this->method = 'index';
                    }
                    unset($url[0]);
                    break;
                    
                case 'add-product':
                    $this->controller = 'ProductsController';
                    $this->method = 'showAddForm';
                    unset($url[0]);
                    break;
                    
                case 'edit-product':
                    $this->controller = 'ProductsController';
                    $this->method = 'showEditForm';
                    unset($url[0]);
                    break;
                    
                case 'product-details':
                    $this->controller = 'ProductsController';
                    $this->method = 'showDetails';
                    unset($url[0]);
                    break;
                    
                case 'categories':
                    $this->controller = 'CategoriesController';
                    // Check for action parameter
                    if (isset($_GET['action'])) {
                        switch ($_GET['action']) {
                            case 'create':
                                $this->method = 'create';
                                break;
                            case 'update':
                                $this->method = 'update';
                                break;
                            case 'delete':
                                $this->method = 'delete';
                                break;
                            default:
                                $this->method = 'index';
                        }
                    } else {
                        $this->method = 'index';
                    }
                    unset($url[0]);
                    break;
                    
                case 'add-category':
                    $this->controller = 'CategoriesController';
                    $this->method = 'showAddForm';
                    unset($url[0]);
                    break;
                    
                case 'edit-category':
                    $this->controller = 'CategoriesController';
                    $this->method = 'showEditForm';
                    unset($url[0]);
                    break;
                    
                case 'collections':
                    $this->controller = 'CollectionsController';
                    // Check for action parameter
                    if (isset($_GET['action'])) {
                        switch ($_GET['action']) {
                            case 'create':
                                $this->method = 'create';
                                break;
                            case 'update':
                                $this->method = 'update';
                                break;
                            case 'delete':
                                $this->method = 'delete';
                                break;
                            case 'toggle':
                                $this->method = 'toggle';
                                break;
                            default:
                                $this->method = 'index';
                        }
                    } else {
                        $this->method = 'index';
                    }
                    unset($url[0]);
                    break;
                    
                case 'add-collection':
                    $this->controller = 'CollectionsController';
                    $this->method = 'showAddForm';
                    unset($url[0]);
                    break;
                    
                case 'edit-collection':
                    $this->controller = 'CollectionsController';
                    $this->method = 'showEditForm';
                    unset($url[0]);
                    break;
                    
                case 'orders':
                    $this->controller = 'OrdersController';
                    // Check for action parameter
                    if (isset($_GET['action'])) {
                        switch ($_GET['action']) {
                            case 'updatePayment':
                                $this->method = 'updatePayment';
                                break;
                            case 'updateStatus':
                                $this->method = 'updateStatus';
                                break;
                            case 'updateOrder':
                                $this->method = 'updateOrder';
                                break;
                            case 'delete':
                                $this->method = 'delete';
                                break;
                            default:
                                $this->method = 'index';
                        }
                    } else {
                        $this->method = 'index';
                    }
                    unset($url[0]);
                    break;
                    
                case 'order-details':
                    $this->controller = 'OrdersController';
                    $this->method = 'showDetails';
                    unset($url[0]);
                    break;
                    
                case 'customers':
                    $this->controller = 'CustomersController';
                    // Check for action parameter
                    if (isset($_GET['action'])) {
                        switch ($_GET['action']) {
                            case 'edit':
                                $this->method = 'showEditForm';
                                break;
                            case 'update':
                                $this->method = 'update';
                                break;
                            case 'delete':
                                $this->method = 'delete';
                                break;
                            case 'toggle':
                                $this->method = 'toggle';
                                break;
                            default:
                                $this->method = 'index';
                        }
                    } else {
                        $this->method = 'index';
                    }
                    unset($url[0]);
                    break;
                    
                case 'reviews':
                    $this->controller = 'ReviewsController';
                    $this->method = 'index';
                    unset($url[0]);
                    break;
                    
                case 'media':
                    $this->controller = 'MediaController';
                    $this->method = 'index';
                    unset($url[0]);
                    break;
                    
                default:
                    // Chuyển đổi tên từ URL (vd: 'users') thành tên Class (vd: 'UsersController')
                    $controllerName = ucfirst($url[0]) . 'Controller';
                    // Tạo đường dẫn tuyệt đối đến file controller trong thư mục admin
                    $controllerFile = __DIR__ . '/../app/controllers/admin/' . $controllerName . '.php';

                    if (file_exists($controllerFile)) {
                        $this->controller = $controllerName;
                        unset($url[0]);
                    }
            }
        } else {
            // Default to dashboard
            $this->controller = 'DashboardController';
            $this->method = 'index';
        }

        // Nạp BaseController trước
        require_once __DIR__ . '/../core/BaseController.php';
        
        // Nạp controller từ thư mục admin
        $controllerFilePath = __DIR__ . '/../app/controllers/admin/' . $this->controller . '.php';
        if (file_exists($controllerFilePath)) {
            require_once $controllerFilePath;
            $this->controller = new $this->controller;
        } else {
            // Xử lý lỗi nếu không tìm thấy file controller
            die("Admin Controller '{$this->controller}' not found in admin folder.");
        }

        // Xử lý Method từ URL hoặc action parameter
        if (isset($url[1])) {
            if (method_exists($this->controller, $url[1])) {
                $this->method = $url[1];
                unset($url[1]);
            }
        } elseif (isset($_GET['action'])) {
            $action = $_GET['action'];
            if (method_exists($this->controller, $action)) {
                $this->method = $action;
            }
        }

        // Xử lý Params từ URL hoặc id parameter
        $this->params = $url ? array_values($url) : [];
        if (isset($_GET['id']) && empty($this->params)) {
            $this->params = [$_GET['id']];
        }

        // Gọi controller, method với các tham số
        call_user_func_array([$this->controller, $this->method], $this->params);
    }

    public function getUrl() {
        if (isset($_GET['url'])) {
            $url = rtrim($_GET['url'], '/');
            $url = filter_var($url, FILTER_SANITIZE_URL);
            $url = explode('/', $url);
            return $url;
        }

        // Fallback for PHP Built-in Server or missing .htaccess
        $requestUri = parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH);
        
        // Remove BASE_URL/admin prefix
        $baseUrl = defined('BASE_URL') ? rtrim(BASE_URL, '/') : '';
        $adminPrefix = $baseUrl . '/admin';
        if (strpos($requestUri, $adminPrefix) === 0) {
            $requestUri = substr($requestUri, strlen($adminPrefix));
        }

        $url = trim($requestUri, '/');
        if (!empty($url)) {
            $url = filter_var($url, FILTER_SANITIZE_URL);
            return explode('/', $url);
        }

        return [];
    }
}

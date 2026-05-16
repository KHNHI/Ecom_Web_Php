<?php
class Route {
    protected $controller = 'HomeController'; // Controller mặc định
    protected $method = 'index';
    protected $params = [];

    /**
     * @var array Bảng điều hướng (Routing Table)
     * Ánh xạ (maps) URL tới [Controller, Method]
     */
    protected $routes = [
        // Trang chủ (key rỗng)
        '' => ['CustomerController', 'index'],

        // Các route cấp 1 của CustomerController
        'products' => ['CustomerController', 'products'],
        'product' => ['CustomerController', 'productDetail'],
        'about' => ['CustomerController', 'about'],
        'search' => ['CustomerController', 'searchApi'],

        // Collection routes
        'collections' => ['CollectionController', 'index'],
        'collection' => ['CollectionController', 'show'],

        // Các route API (cấp 2)
        'api/new-arrivals' => ['CustomerController', 'getNewArrivals'],
        'api/popular' => ['CustomerController', 'getPopularProducts'],
        'api/popular-products' => ['CustomerController', 'getPopularProducts'],
        'api/category' => ['CustomerController', 'getProductsByCategory'],
        'api/categories' => ['CustomerController', 'getCategories'],
        'api/collections' => ['CustomerController', 'getCollections'],
        
        // Collection API routes
        'api/collection/products' => ['CollectionController', 'getProducts'],
        'api/collections/list' => ['CollectionController', 'getCollectionsApi'],
        
        // Review routes (cấp 2)
        'api/reviews/add' => ['ReviewController', 'add'],
        'api/reviews/get' => ['ReviewController', 'getProductReviews'],

        // Wishlist routes
        'wishlist' => ['WishlistController', 'index'],
        'wishlist/add' => ['WishlistController', 'add'],
        'wishlist/remove' => ['WishlistController', 'remove'],
        'wishlist/toggle' => ['WishlistController', 'toggle'],
        'wishlist/count' => ['WishlistController', 'count'],
        'wishlist/clear' => ['WishlistController', 'clear'],
        'wishlist/status' => ['WishlistController', 'status'],

        // Các route hiển thị form Auth (cấp 1)
        'signin' => ['AuthController', 'showSignIn'],
        'login' => ['AuthController', 'showSignIn'],
        'signup' => ['AuthController', 'showSignUp'],
        'register' => ['AuthController', 'showSignUp'],
        'forgot-password' => ['AuthController', 'showForgotPassword'],
        'reset-password' => ['AuthController', 'showResetPassword'],

        // Các route xử lý Auth (cấp 2)
        'auth/signin' => ['AuthController', 'signIn'],
        'auth/login' => ['AuthController', 'signIn'],
        'auth/signup' => ['AuthController', 'signUp'],
        'auth/register' => ['AuthController', 'signUp'],
        'auth/verify' => ['AuthController', 'verifyEmail'],
        'auth/logout' => ['AuthController', 'logout'],
        'auth/resend-verification' => ['AuthController', 'resendVerification'],
        'auth/forgot-password' => ['AuthController', 'forgotPassword'],
        'auth/reset-password' => ['AuthController', 'resetPassword'],

        // Profile (cấp 1 & 2)
        'profile' => ['ProfileController', 'index'],
        'profile/update' => ['ProfileController', 'update'],
        'profile/upload-avatar' => ['ProfileController', 'uploadAvatar'],
        'profile/change-password' => ['ProfileController', 'changePassword'],

        // Cart (cấp 1 & 2)
        'cart' => ['CartController', 'index'],
        'cart/add' => ['CartController', 'add'],
        'cart/update' => ['CartController', 'update'],
        'cart/remove' => ['CartController', 'remove'],
        'cart/clear' => ['CartController', 'clear'],
        'cart/count' => ['CartController', 'count'],
        'cart/summary' => ['CartController', 'summary'],
        'cart/buynow' => ['CartController', 'buyNow'],

        // Checkout (cấp 1 & 2)
        'checkout' => ['CheckoutController', 'index'],
        'checkout/process' => ['CheckoutController', 'process'],

        // Order (cấp 1 & 2)
        'order' => ['OrderController', 'index'],
        'order/buynow' => ['OrderController', 'buyNow'],
        'order-success' => ['CheckoutController', 'orderSuccess'],

        // Location API routes
        'api/locations/provinces' => ['LocationController', 'getProvinces'],
        'api/locations/wards' => ['LocationController', 'getWards'],
    ];

    public function __construct() {
        $url = $this->getUrl();
        $routeFound = false;

        // Xử lý Controller với Bảng điều hướng
        
        // 1. Kiểm tra route 3 phần (VD: 'api/reviews/add')
        if (isset($url[0]) && isset($url[1]) && isset($url[2])) {
            $key = $url[0] . '/' . $url[1] . '/' . $url[2];
            if (array_key_exists($key, $this->routes)) {
                $this->controller = $this->routes[$key][0];
                $this->method = $this->routes[$key][1];
                unset($url[0], $url[1], $url[2]);
                $routeFound = true;
            }
        }
        
        // 2. Kiểm tra route 2 phần (VD: 'api/new-arrivals')
        if (!$routeFound && isset($url[0]) && isset($url[1])) {
            $key = $url[0] . '/' . $url[1];
            if (array_key_exists($key, $this->routes)) {
                $this->controller = $this->routes[$key][0];
                $this->method = $this->routes[$key][1];
                unset($url[0], $url[1]);
                $routeFound = true;
            }
        }
        
        // 3. Xử lý đặc biệt cho collection với slug (VD: 'collection/diamond-rings')
        if (!$routeFound && isset($url[0]) && $url[0] === 'collection' && isset($url[1])) {
            $this->controller = 'CollectionController';
            $this->method = 'show';
            // Giữ slug làm parameter
            $this->params = [$url[1]];
            $routeFound = true;
        }
        
        // 3.5. Xử lý đặc biệt cho product với id (VD: 'product/1')
        if (!$routeFound && isset($url[0]) && $url[0] === 'product' && isset($url[1])) {
            $this->controller = 'CustomerController';
            $this->method = 'productDetail';
            // Giữ product id làm parameter
            $this->params = [$url[1]];
            $routeFound = true;
        }
        
        // 4. Nếu không khớp, kiểm tra route 1 phần (VD: 'products')
        if (!$routeFound && isset($url[0])) {
            $key = $url[0];
            if (array_key_exists($key, $this->routes)) {
                $this->controller = $this->routes[$key][0];
                $this->method = $this->routes[$key][1];
                unset($url[0]);
                $routeFound = true;
            }
        }
        
        // 3. Xử lý trang chủ (không có $url[0])
        if (!$routeFound && !isset($url[0])) {
             if (array_key_exists('', $this->routes)) {
                $this->controller = $this->routes[''][0];
                $this->method = $this->routes[''][1];
                $routeFound = true;
             }
        }

        // 4. (Giữ nguyên) Xử lý controller động (fallback từ 'default' case)
        if (!$routeFound && isset($url[0])) {
            $controllerName = ucfirst($url[0]) . 'Controller';
            $controllerFile = __DIR__ . '/../app/controllers/' . $controllerName . '.php';

            if (file_exists($controllerFile)) {
                $this->controller = $controllerName;
                unset($url[0]);
                // $routeFound = true; // Không cần gán, vì logic bên dưới sẽ xử lý
            }
            // Nếu file không tồn tại, nó sẽ dùng $this->controller mặc định ('HomeController')
        }

        // --- Bắt đầu phần logic giữ nguyên từ code gốc của bạn ---

        // Nạp controller
        $controllerFilePath = __DIR__ . '/../app/controllers/' . $this->controller . '.php';
        if (file_exists($controllerFilePath)) {
            require_once $controllerFilePath;
            // Kiểm tra xem class có tồn tại không trước khi khởi tạo
            if (class_exists($this->controller)) {
                 $this->controller = new $this->controller;
            } else {
                 die("Controller class '{$this->controller}' not found in file.");
            }
        } else {
            // Xử lý lỗi nếu không tìm thấy file controller
            die("Controller file '{$this->controller}.php' not found.");
        }

        // Xử lý Method (phần này rất quan trọng, giữ lại để xử lý method động)
        // Ví dụ: /users/edit/1 -> controller 'UsersController', method 'edit'
        if (isset($url[1])) {
            if (method_exists($this->controller, $url[1])) {
                $this->method = $url[1];
                unset($url[1]);
            }
        }

        // Xử lý Params (chỉ nếu chưa được set bởi custom route)
        if (!isset($this->params)) {
            $this->params = $url ? array_values($url) : [];
        }

        // error_log("Router: Calling " . get_class($this->controller) . "::{$this->method} with params: " . json_encode($this->params));

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
        
        // Remove BASE_URL prefix
        $baseUrl = defined('BASE_URL') ? BASE_URL : '';
        if ($baseUrl && strpos($requestUri, $baseUrl) === 0) {
            $requestUri = substr($requestUri, strlen($baseUrl));
        }

        $url = trim($requestUri, '/');
        if (!empty($url)) {
            $url = filter_var($url, FILTER_SANITIZE_URL);
            return explode('/', $url);
        }

        return [];
    }
}

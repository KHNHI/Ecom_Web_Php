<?php
/**
 * URL Helper Functions
 * Tự động tạo URL chính xác bất kể tên thư mục gốc là gì.
 */

/**
 * Tạo base URL gốc cho ứng dụng một cách tự động.
 * Hàm này sẽ luôn trả về đường dẫn đúng, ví dụ: '/Ecom_website' hoặc '/ten_folder_khac'
 * hoặc rỗng '' nếu dự án nằm ngay trong thư mục gốc htdocs.
 * @return string
 */
function getBaseUrl() {
    // Lấy đường dẫn của script đang chạy (ví dụ: /Ecom_website/index.php)
    $script_name = $_SERVER['SCRIPT_NAME'];

    // Lấy tên thư mục chứa script đó (ví dụ: /Ecom_website)
    $base_path = dirname($script_name);

    // Xử lý trường hợp đặc biệt khi dự án nằm ở thư mục gốc của web server (htdocs)
    // Trong trường hợp này, dirname sẽ trả về '/' hoặc '\', ta cần đổi nó thành chuỗi rỗng.
    if ($base_path === '/' || $base_path === '\\') {
        $base_path = '';
    }
    
    return $base_path;
}

/**
 * Tạo URL đầy đủ từ một đường dẫn tương đối.
 * @param string $path Đường dẫn con, ví dụ: 'products/show/1'
 * @return string URL hoàn chỉnh, ví dụ: /Ecom_website/products/show/1
 */
function url($path = '') {
    $base = getBaseUrl();
    // Đảm bảo $path không có dấu / ở đầu để tránh lỗi nối chuỗi
    $path = ltrim($path, '/');
    
    // Nếu path rỗng thì chỉ trả về base, ngược lại thì nối thêm dấu /
    return $base . ($path ? '/' . $path : '');
}

/**
 * Tạo URL cho các file tài nguyên (CSS, JS, Images, etc.).
 * @param string $path Đường dẫn file trong thư mục public/assets, ví dụ: 'css/style.css'
 * @return string URL hoàn chỉnh đến file tài nguyên.
 */
function asset($path) {
    // If already absolute URL (starts with http or /), return as-is
    if (strpos($path, 'http') === 0 || strpos($path, '/') === 0) {
        return $path;
    }
    
    // If path starts with 'public/', use it directly
    if (strpos($path, 'public/') === 0) {
        return url($path);
    }
    
    // Default behavior for assets folder
    return url('public/assets/' . ltrim($path, '/'));
}

/**
 * Tạo URL theo route đã định nghĩa, hỗ trợ clean URLs.
 * @param string $route Tên route, ví dụ: 'product'
 * @param array $params Mảng các tham số cho URL, ví dụ: ['id' => 123]
 * @return string URL hoàn chỉnh.
 */
function route($route, $params = []) {
    // Convert old customer routes to clean URLs
    $cleanRoutes = [
        'customer/products' => 'products',
        'customer/productDetail' => 'product',
        'customer/product' => 'product',
        'customer/search' => 'search',
        'customer/api' => 'api',
        'customer' => '', // Home page
    ];
    
    // Check if route needs conversion
    foreach ($cleanRoutes as $old => $new) {
        if (strpos($route, $old) === 0) {
            $route = str_replace($old, $new, $route);
            break;
        }
    }
    
    $url = url($route);
    if (!empty($params)) {
        // Nối các tham số vào URL, ví dụ: /product?id=123
        // Nếu bạn muốn URL dạng /product/123 thì cần sửa logic ở đây và trong Router.
        $url .= '?' . http_build_query($params);
    }
    return $url;
}

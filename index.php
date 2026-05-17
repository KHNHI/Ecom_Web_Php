<?php
// Turn off error display for production - errors will still be logged
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

date_default_timezone_set("Asia/Ho_Chi_Minh");
session_start();
ob_start(); //tránh lỗi khi dùng hàm liên quan header, côkie (kiểm soát việc xuất dữ liệu ra trình duyệt)

// Load required files

// Create logs directory if it doesn't exist
if (!is_dir(__DIR__ . '/logs')) {
    mkdir(__DIR__ . '/logs', 0777, true);
}

// Load configs first (needed for BASE_URL)
foreach (glob(__DIR__ . '/configs/*.php') as $file) {
    require_once $file;
}

// Detect admin routes and delegate to admin/index.php
$requestPath = parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH);
$baseUrl = defined('BASE_URL') ? BASE_URL : '';
$relativePath = $baseUrl ? substr($requestPath, strlen($baseUrl)) : $requestPath;
if (preg_match('#^/admin(/|$)#', $relativePath)) {
    require_once __DIR__ . '/admin/index.php';
    exit;
}
foreach (glob(__DIR__ . '/core/*.php') as $file) {
    require_once $file;
}


// Nạp các file helper
foreach (glob(__DIR__ . '/helpers/*.php') as $file) {
    require_once $file;
}

// --- GLOBAL SESSION TIMEOUT CHECK ---
if (class_exists('SessionHelper') && SessionHelper::isLoggedIn()) {
    if (SessionHelper::isSessionExpired()) {
        SessionHelper::destroyUserSession();
        // Redirect to login if not an API request
        $isApi = strpos($_SERVER['REQUEST_URI'] ?? '', '/api/') !== false;
        if (!$isApi) {
            header('Location: ' . BASE_URL . '/login?timeout=1');
            exit;
        } else {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Phiên đăng nhập đã hết hạn. Vui lòng đăng nhập lại.']);
            exit;
        }
    } else {
        SessionHelper::refreshSession();
    }
}

// Nạp các file model

foreach (glob(__DIR__ . '/app/models/*.php') as $file) {
    require_once $file;
}
foreach (glob(__DIR__ . '/app/services/*.php') as $file) {
    require_once $file;
}


// Register error handlers
ErrorHandler::register();

// Nạp các file controller
foreach (glob(__DIR__ . '/app/controllers/*.php') as $file) {
    require_once $file;
}


// Khởi tạo Router để xử lý URL
$router = new Route();
?>
<?php
/**
 * LoginController - Xử lý đăng nhập admin
 */
class LoginController extends BaseController {
    private $userModel;

    public function __construct() {
        $this->userModel = $this->model('User');
    }

    /**
     * Hiển thị trang login
     */
    public function index() {
        // Nếu đã login, redirect về dashboard
        if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in']) {
            header('Location: ' . BASE_URL . '/admin/dashboard');
            exit;
        }

        // Xử lý form submit
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->processLogin();
            return;
        }

        // Clear output buffer trước khi hiển thị login
        while (ob_get_level()) {
            ob_end_clean();
        }
        
        // Hiển thị form login (không dùng layout admin)
        include __DIR__ . '/../../views/admin/pages/admin-login.php';
        die(); // Dừng hoàn toàn
    }

    /**
     * Xử lý login
     */
    private function processLogin() {
        error_log("========== ADMIN LOGIN ATTEMPT ==========");
        
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        
        error_log("Login attempt with email: " . $email);

        // Validate
        if (empty($email) || empty($password)) {
            error_log("ERROR: Empty email or password");
            $error = 'Vui lòng nhập đầy đủ email và password';
            while (ob_get_level()) ob_end_clean();
            include __DIR__ . '/../../views/admin/pages/admin-login.php';
            die();
        }

        try {
            // Tìm user
            $user = $this->userModel->findByEmail($email);

            if (!$user) {
                $error = 'Email hoặc mật khẩu không đúng';
                while (ob_get_level()) ob_end_clean();
                include __DIR__ . '/../../views/admin/pages/admin-login.php';
                die();
            }

            // Verify password
            if (!password_verify($password, $user->password_hash)) {
                $error = 'Email hoặc mật khẩu không đúng';
                while (ob_get_level()) ob_end_clean();
                include __DIR__ . '/../../views/admin/pages/admin-login.php';
                die();
            }

            // Check active
            if (!$user->is_active) {
                $error = 'Tài khoản chưa được kích hoạt';
                while (ob_get_level()) ob_end_clean();
                include __DIR__ . '/../../views/admin/pages/admin-login.php';
                die();
            }

            // Check admin role - QUAN TRỌNG: CHỈ ADMIN MỚI VÀO ĐƯỢC
            $isAdmin = false;
            
            // Debug log
            error_log("Checking admin role for user: " . $user->email);
            error_log("User role: " . ($user->role ?? 'NULL'));
            error_log("User role_id: " . ($user->role_id ?? 'NULL'));
            
            // Kiểm tra role_id = 1 (admin) hoặc role = 'admin'
            if (isset($user->role_id) && $user->role_id == 1) {
                $isAdmin = true;
                error_log("✓ Admin confirmed via role_id = 1");
            } elseif (isset($user->role) && strtolower($user->role) === 'admin') {
                $isAdmin = true;
                error_log("✓ Admin confirmed via role = 'admin'");
            } else {
                error_log("✗ NOT ADMIN - role_id: " . ($user->role_id ?? 'NULL') . ", role: " . ($user->role ?? 'NULL'));
            }

            if (!$isAdmin) {
                error_log("Access DENIED for user: " . $user->email);
                $error = 'Bạn không có quyền truy cập Admin Panel. Chỉ tài khoản Admin mới được phép đăng nhập.';
                while (ob_get_level()) ob_end_clean();
                include __DIR__ . '/../../views/admin/pages/admin-login.php';
                die();
            }
            
            error_log("✓✓✓ Admin login SUCCESS for: " . $user->email);

            // Tạo session
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_user_id'] = $user->user_id;
            $_SESSION['admin_email'] = $user->email;
            $_SESSION['admin_name'] = $user->name ?? 'Admin';
            $_SESSION['admin_role'] = $user->role ?? 'admin';

            // Redirect
            header('Location: ' . BASE_URL . '/admin/dashboard');
            exit;

        } catch (Exception $e) {
            error_log("Admin login error: " . $e->getMessage());
            $error = 'Có lỗi xảy ra: ' . $e->getMessage();
            while (ob_get_level()) ob_end_clean();
            include __DIR__ . '/../../views/admin/pages/admin-login.php';
            die();
        }
    }

    /**
     * Logout - Đăng xuất admin
     */
    public function logout() {
        // Log logout action
        if (isset($_SESSION['admin_email'])) {
            error_log("Admin logout: " . $_SESSION['admin_email']);
        }
        
        // Xóa tất cả session variables
        $_SESSION = array();
        
        // Xóa session cookie
        if (isset($_COOKIE[session_name()])) {
            setcookie(session_name(), '', time() - 42000, '/');
        }
        
        // Destroy session
        session_destroy();
        
        // Redirect về login page với message
        header('Location: ' . BASE_URL . '/admin/login?logout=success');
        exit;
    }
}

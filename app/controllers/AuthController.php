

<?php
// Clear any previous output to prevent header issues
while (ob_get_level()) {
    ob_end_clean();
}

require_once __DIR__ . '/../../core/BaseController.php'; 
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../../helpers/email_helper.php';
require_once __DIR__ . '/../../helpers/session_helper.php';
require_once __DIR__ . '/../../helpers/url_helper.php';


class AuthController extends BaseController {
    private $userModel;

    public function __construct() {
        $this->userModel = new User();
    }

    /**
     * Show sign up form
     */
    public function showSignUp() {
        $this->view('auth/signup');
    }

    /**
     * Show sign in form  
     */
    public function showSignIn() {
        $this->view('auth/signin');
    }

    /**
     * Handle sign up registration
     */
    public function signUp() {
        // Clear any previous output
        while (ob_get_level()) {
            ob_end_clean();
        }
        
        // Set JSON header immediately
        header('Content-Type: application/json; charset=utf-8');
        
        try {
            // Debug log
            error_log("SignUp method called");
            error_log("POST data: " . print_r($_POST, true));
            
            // Validate input
            $errors = $this->validateSignUpData($_POST);
            if (!empty($errors)) {
                error_log("Validation errors: " . print_r($errors, true));
                $this->jsonResponse(false, 'Dữ liệu không hợp lệ', $errors);
                return;
            }

            $email = trim($_POST['email']);
            $password = $_POST['password'];
            $name = trim($_POST['name'] ?? '');
            $phone = trim($_POST['phone'] ?? '');

            // Check if email already exists
            $existingUser = $this->userModel->findByEmail($email);
            if ($existingUser) {
                $this->jsonResponse(false, 'Email này đã được sử dụng');
                return;
            }

            // Generate verification token
            $verificationToken = $this->generateVerificationToken();
            $tokenExpires = date('Y-m-d H:i:s', strtotime('+' . VERIFICATION_EXPIRE_MINUTES . ' minutes'));

            // Create user
            $userData = [
                'email' => $email,
                'password_hash' => password_hash($password, PASSWORD_DEFAULT),
                'name' => $name,
                'phone' => $phone,
                'verification_token' => $verificationToken,
                'token_expires_at' => $tokenExpires
            ];

            $created = $this->userModel->create($userData);
            
            if (!$created) {
                $this->jsonResponse(false, 'Có lỗi xảy ra khi tạo tài khoản');
                return;
            }

            // Send verification email
            error_log("Attempting to send verification email to: " . $email);
            $emailSent = EmailHelper::sendVerificationEmail($email, $verificationToken);
            error_log("Email sent result: " . ($emailSent ? 'true' : 'false'));
            
            if (!$emailSent) {
                // User created but email failed - still success but with warning
                error_log("Email failed but user was created successfully");
                $this->jsonResponse(true, 'Tài khoản được tạo thành công nhưng không thể gửi email xác thực. Vui lòng liên hệ admin.');
                return;
            }

            error_log("User created and email sent successfully");
            $this->jsonResponse(true, 'Đăng ký thành công! Vui lòng kiểm tra email để xác thực tài khoản.');

        }catch (Exception $e) {
            error_log("SignUp Error: " . $e->getMessage());
            $this->jsonResponse(false, 'Có lỗi hệ thống xảy ra: ' . $e->getMessage());
        }
    }

    /**
     * Handle sign in authentication
     */
    public function signIn() {
        // Clear any previous output and set encoding
        while (ob_get_level()) {
            ob_end_clean();
        }
        
        // Set proper encoding
        if (function_exists('mb_internal_encoding')) {
            mb_internal_encoding('UTF-8');
        }
        
        // Set headers with proper encoding
        header('Content-Type: application/json; charset=utf-8');
        header('Cache-Control: no-cache, must-revalidate');
        
        try {
            error_log("SignIn method called with POST data: " . print_r($_POST, true));
            
            // Validate input
            $errors = $this->validateSignInData($_POST);
            if (!empty($errors)) {
                error_log("Validation errors: " . print_r($errors, true));
                $this->jsonResponse(false, 'Dữ liệu không hợp lệ', $errors);
                return;
            }

            $email = trim($_POST['email']);
            $password = $_POST['password'];
            error_log("Processing signin for email: " . $email);

            // Find user by email
            $user = $this->userModel->findByEmail($email);
            
            if (!$user) {
                error_log("User not found for email: " . $email);
                $this->jsonResponse(false, 'Email hoặc mật khẩu không đúng');
                return;
            }

            error_log("User found: " . $user->email . ", is_active: " . ($user->is_active ? 'true' : 'false'));

            // Verify password
            if (!password_verify($password, $user->password_hash)) {
                error_log("Password verification failed for user: " . $email);
                $this->jsonResponse(false, 'Email hoặc mật khẩu không đúng');
                return;
            }

            // Check if email is verified
            if (!$user->is_active) {
                error_log("User not active: " . $email);
                $this->jsonResponse(false, 'Tài khoản chưa được xác thực email. Vui lòng kiểm tra email của bạn.');
                return;
            }

            // Create session
            error_log("Creating session for user: " . $user->email);
            $this->createUserSession($user);

            // Load user's previous cart from database
            error_log("Loading cart for user: " . $user->user_id);
            $this->loadUserCartFromDatabase($user->user_id);

            $redirectUrl = url('/');
            error_log("Signin successful, redirecting to: " . $redirectUrl);
            
            $this->jsonResponse(true, 'Đăng nhập thành công', [
                'redirect' => $redirectUrl
            ]);

        } catch (Exception $e) {
            error_log("SignIn Error: " . $e->getMessage() . "\nTrace: " . $e->getTraceAsString());
            $this->jsonResponse(false, 'Có lỗi hệ thống xảy ra: ' . $e->getMessage());
        }
    }

    /**
     * Handle email verification
     */
    public function verifyEmail() {
        try {
            $token = $_GET['token'] ?? '';
            
            if (empty($token)) {
                $this->view('auth/verification-result', [
                    'success' => false,
                    'message' => 'Token xác thực không hợp lệ'
                ]);
                return;
            }

            // Find user by verification token
            $user = $this->userModel->findByVerificationToken($token);
            
            if (!$user) {
                $this->view('auth/verification-result', [
                    'success' => false,
                    'message' => 'Token xác thực không hợp lệ hoặc đã hết hạn'
                ]);
                return;
            }

            // Verify email
            $verified = $this->userModel->verifyEmail($user->user_id);
            
            if (!$verified) {
                $this->view('auth/verification-result', [
                    'success' => false,
                    'message' => 'Có lỗi xảy ra khi xác thực email'
                ]);
                return;
            }

            $this->view('auth/verification-result', [
                'success' => true,
                'message' => 'Email được xác thực thành công! Bạn có thể đăng nhập ngay bây giờ.'
            ]);

        } catch (Exception $e) {
            error_log("Email Verification Error: " . $e->getMessage());
            $this->view('auth/verification-result', [
                'success' => false,
                'message' => 'Có lỗi hệ thống xảy ra'
            ]);
        }
    }

    /**
     * Handle logout
     */
    public function logout() {
        // Lưu cart của user vào database trước khi logout (nếu có)
        if (SessionHelper::isLoggedIn() && isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
            $this->saveUserCartToDatabase();
        }
        
        // Xóa user session
        SessionHelper::destroyUserSession();
        
        // Reset cart về trống cho guest user
        unset($_SESSION['cart']);
        
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
            $this->jsonResponse(true, 'Đăng xuất thành công');
        } else {
            header('Location: ' . url('signin'));
            exit;
        }
    }

    /**
     * Resend verification email
     */
    public function resendVerification() {
        try {
            if (empty($_POST['email'])) {
                $this->jsonResponse(false, 'Email là bắt buộc');
                return;
            }

            $email = trim($_POST['email']);
            
            // Find user by email
            $user = $this->userModel->findByEmail($email);
            
            if (!$user) {
                $this->jsonResponse(false, 'Không tìm thấy tài khoản với email này');
                return;
            }

            // Check if already verified
            if ($user->is_active) {
                $this->jsonResponse(false, 'Tài khoản đã được xác thực');
                return;
            }

            // Generate new verification token
            $verificationToken = $this->generateVerificationToken();
            $tokenExpires = date('Y-m-d H:i:s', strtotime('+' . VERIFICATION_EXPIRE_MINUTES . ' minutes'));

            // Update token in database
            $updated = $this->userModel->updateVerificationToken($email, $verificationToken, $tokenExpires);
            
            if (!$updated) {
                $this->jsonResponse(false, 'Có lỗi xảy ra khi cập nhật token');
                return;
            }

            // Send verification email
            $emailSent = EmailHelper::sendVerificationEmail($email, $verificationToken);
            
            if (!$emailSent) {
                $this->jsonResponse(false, 'Không thể gửi email xác thực');
                return;
            }

            $this->jsonResponse(true, 'Email xác thực đã được gửi lại. Vui lòng kiểm tra hộp thư của bạn.');

        } catch (Exception $e) {
            error_log("Resend Verification Error: " . $e->getMessage());
            $this->jsonResponse(false, 'Có lỗi hệ thống xảy ra');
        }
    }

    /**
     * Show forgot password form
     */
    public function showForgotPassword() {
        $this->view('auth/forgot-password');
    }

    /**
     * Handle forgot password request
     */
    public function forgotPassword() {
        try {
            if (empty($_POST['email'])) {
                $this->jsonResponse(false, 'Email là bắt buộc');
                return;
            }

            $email = trim($_POST['email']);
            
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $this->jsonResponse(false, 'Email không hợp lệ');
                return;
            }

            // Find user by email
            $user = $this->userModel->findByEmail($email);
            
            if (!$user) {
                // Don't reveal if email exists for security
                $this->jsonResponse(true, 'Nếu email này tồn tại, bạn sẽ nhận được link đặt lại mật khẩu.');
                return;
            }

            // Check if user is active
            if (!$user->is_active) {
                $this->jsonResponse(false, 'Tài khoản chưa được xác thực. Vui lòng xác thực email trước.');
                return;
            }

            // Generate reset token
            $resetToken = $this->generateVerificationToken();
            $tokenExpires = date('Y-m-d H:i:s', strtotime('+' . RESET_EXPIRE_MINUTES . ' minutes'));

            // Debug log
            error_log("Generated reset token: " . $resetToken . " for email: " . $email);
            error_log("Token expires at: " . $tokenExpires);

            // Save reset token
            $updated = $this->userModel->updateResetToken($email, $resetToken, $tokenExpires);
            
            // Debug log
            error_log("Update result: " . ($updated ? 'SUCCESS' : 'FAILED'));
            
            if (!$updated) {
                $this->jsonResponse(false, 'Có lỗi xảy ra khi tạo yêu cầu đặt lại mật khẩu');
                return;
            }

            // Send reset email
            $emailSent = EmailHelper::sendResetPasswordEmail($email, $resetToken);
            
            if (!$emailSent) {
                $this->jsonResponse(false, 'Không thể gửi email đặt lại mật khẩu');
                return;
            }

            $this->jsonResponse(true, 'Link đặt lại mật khẩu đã được gửi đến email của bạn.');

        } catch (Exception $e) {
            error_log("Forgot Password Error: " . $e->getMessage());
            $this->jsonResponse(false, 'Có lỗi hệ thống xảy ra');
        }
    }

    /**
     * Show reset password form
     */
    public function showResetPassword() {
        $token = $_GET['token'] ?? '';
        
        error_log("DEBUG showResetPassword - Token received: " . $token);
        
        if (empty($token)) {
            error_log("DEBUG showResetPassword - Token is empty");
            $this->view('auth/reset-result', [
                'success' => false,
                'message' => 'Token đặt lại mật khẩu không hợp lệ'
            ]);
            return;
        }

        // Verify token exists and not expired
        $user = $this->userModel->findByResetToken($token);
        
        error_log("DEBUG showResetPassword - User found: " . ($user ? 'YES' : 'NO'));
        if ($user) {
            error_log("DEBUG showResetPassword - User email: " . $user->email);
            error_log("DEBUG showResetPassword - Token expires: " . $user->reset_expires_at);
            error_log("DEBUG showResetPassword - Current time: " . date('Y-m-d H:i:s'));
        }
        
        if (!$user) {
            error_log("DEBUG showResetPassword - Token not found or expired");
            $this->view('auth/reset-result', [
                'success' => false,
                'message' => 'Token đặt lại mật khẩu không hợp lệ hoặc đã hết hạn'
            ]);
            return;
        }

        // Show reset password form
        error_log("DEBUG showResetPassword - Showing reset form");
        $this->view('auth/reset-password', ['token' => $token]);
    }

    /**
     * Handle reset password submission
     */
    public function resetPassword() {
        try {
            $token = $_POST['token'] ?? '';
            $password = $_POST['password'] ?? '';
            $confirmPassword = $_POST['confirm_password'] ?? '';

            // Validate input
            if (empty($token)) {
                $this->jsonResponse(false, 'Token không hợp lệ');
                return;
            }

            if (empty($password)) {
                $this->jsonResponse(false, 'Mật khẩu mới là bắt buộc');
                return;
            }

            if (strlen($password) < 6) {
                $this->jsonResponse(false, 'Mật khẩu phải có ít nhất 6 ký tự');
                return;
            }

            if ($password !== $confirmPassword) {
                $this->jsonResponse(false, 'Mật khẩu xác nhận không khớp');
                return;
            }

            // Find user by reset token
            $user = $this->userModel->findByResetToken($token);
            
            if (!$user) {
                $this->jsonResponse(false, 'Token đặt lại mật khẩu không hợp lệ hoặc đã hết hạn');
                return;
            }

            // Update password and clear reset token
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $updated = $this->userModel->resetPassword($user->user_id, $hashedPassword);
            
            if (!$updated) {
                $this->jsonResponse(false, 'Có lỗi xảy ra khi đặt lại mật khẩu');
                return;
            }

            $this->jsonResponse(true, 'Mật khẩu đã được đặt lại thành công. Bạn có thể đăng nhập bằng mật khẩu mới.');

        } catch (Exception $e) {
            error_log("Reset Password Error: " . $e->getMessage());
            $this->jsonResponse(false, 'Có lỗi hệ thống xảy ra');
        }
    }

    /**
     * Validate sign up data
     */
    private function validateSignUpData($data) {
        $errors = [];
        
        // Email validation
        if (empty($data['email'])) {
            $errors['email'] = 'Email là bắt buộc';
        } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Email không hợp lệ';
        }
        
        // Password validation
        if (empty($data['password'])) {
            $errors['password'] = 'Mật khẩu là bắt buộc';
        } elseif (strlen($data['password']) < 6) {
            $errors['password'] = 'Mật khẩu phải có ít nhất 6 ký tự';
        }
        
        // Confirm password validation
        if (empty($data['confirm_password'])) {
            $errors['confirm_password'] = 'Xác nhận mật khẩu là bắt buộc';
        } elseif ($data['password'] !== $data['confirm_password']) {
            $errors['confirm_password'] = 'Mật khẩu xác nhận không khớp';
        }

        // Phone validation (optional)
        if (!empty($data['phone']) && !preg_match('/^[0-9]{10,11}$/', $data['phone'])) {
            $errors['phone'] = 'Số điện thoại không hợp lệ';
        }
        
        return $errors;
    }

    /**
     * Validate sign in data
     */
    private function validateSignInData($data) {
        $errors = [];
        
        // Email validation
        if (empty($data['email'])) {
            $errors['email'] = 'Email là bắt buộc';
        } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Email không hợp lệ';
        }
        
        // Password validation
        if (empty($data['password'])) {
            $errors['password'] = 'Mật khẩu là bắt buộc';
        }
        
        return $errors;
    }

    /**
     * Generate secure verification token
     */
    private function generateVerificationToken() {
        return bin2hex(random_bytes(32));
    }

    /**
     * Create user session
     */
    private function createUserSession($user) {
        SessionHelper::createUserSession($user);
    }

    /**
     * Save user cart to database before logout
     */
    private function saveUserCartToDatabase() {
        try {
            $userId = SessionHelper::getUserId();
            
            if (empty($_SESSION['cart'])) {
                return; // No cart to save
            }
            
            require_once __DIR__ . '/../models/Cart.php';
            require_once __DIR__ . '/../models/Product.php';
            
            $cartModel = new Cart();
            $productModel = new Product();
            
            // Get or create user cart in database
            $cart = $cartModel->getCartByUser($userId);
            if (!$cart) {
                $cartId = $cartModel->createCart($userId);
            } else {
                $cartId = $cart->cart_id;
                // Clear existing items
                $cartModel->clearCart($cartId);
            }
            
            if ($cartId) {
                // Save each session cart item to database with variant info
                foreach ($_SESSION['cart'] as $cartKey => $cartItem) {
                    $product = $productModel->findById($cartItem['product_id']);
                    if ($product) {
                        $this->insertCartItemWithVariants(
                            $cartId,
                            $cartItem['product_id'],
                            $product->base_price,
                            $cartItem['quantity'],
                            $cartItem['size'],
                            $cartItem['color']
                        );
                    }
                }
            }
            
            error_log("Cart saved to database for user $userId");
            
        } catch (Exception $e) {
            error_log("Error saving user cart: " . $e->getMessage());
        }
    }

    /**
     * Load user cart from database after login
     */
    private function loadUserCartFromDatabase($userId) {
        try {
            // Load user cart from database using simple approach
            $cartData = $this->getUserCartFromDatabase($userId);
            
            if ($cartData && !empty($cartData)) {
                $_SESSION['cart'] = $cartData;
            } else {
                // Start with empty cart if no saved cart found
                $_SESSION['cart'] = [];
            }
            
        } catch (Exception $e) {
            error_log("Error loading user cart: " . $e->getMessage());
            $_SESSION['cart'] = []; // Fallback to empty cart
        }
    }

    /**
     * Get user cart data from database
     */
    private function getUserCartFromDatabase($userId) {
        try {
            require_once __DIR__ . '/../models/Cart.php';
            require_once __DIR__ . '/../models/Product.php';
            
            $cartModel = new Cart();
            $productModel = new Product();
            
            // Get user cart from database
            $cart = $cartModel->getCartByUser($userId);
            if (!$cart) {
                return []; // No cart found
            }
            
            // Get cart items
            $cartItems = $cartModel->getCartItems($cart->cart_id);
            if (empty($cartItems)) {
                return []; // Empty cart
            }
            
            // Convert database cart items to session cart format
            $sessionCart = [];
            foreach ($cartItems as $item) {
                // Get variant info from variant_id
                $variantInfo = $this->getVariantInfo($item->variant_id ?? null);
                $size = $variantInfo['size'];
                $color = $variantInfo['color'];
                
                // Use the same key generation as in CartController
                $cartKey = $item->product_id . '_' . ($size ?? 'default') . '_' . ($color ?? 'default');
                
                $sessionCart[$cartKey] = [
                    'product_id' => $item->product_id,
                    'quantity' => $item->quantity,
                    'size' => $size,
                    'color' => $color,
                    'added_at' => strtotime($item->added_at)
                ];
            }
            
            return $sessionCart;
            
        } catch (Exception $e) {
            error_log("Error getting user cart from database: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get or create variant_id based on size and color
     */
    private function getOrCreateVariantId($productId, $size, $color) {
        if (!$size && !$color) {
            return null; // No variant needed
        }
        
        try {
            require_once __DIR__ . '/../../configs/database.php';
            $db = Database::getInstance();
            
            // Try to find existing variant
            $db->query("SELECT variant_id FROM product_variants WHERE product_id = :product_id AND size = :size AND color = :color");
            $db->bind(':product_id', $productId);
            $db->bind(':size', $size);
            $db->bind(':color', $color);
            $result = $db->single();
            
            if ($result) {
                return $result->variant_id;
            }
            
            // Create new variant if not exists
            $db->query("INSERT INTO product_variants (product_id, size, color, stock_quantity, price) VALUES (:product_id, :size, :color, 0, 0)");
            $db->bind(':product_id', $productId);
            $db->bind(':size', $size);
            $db->bind(':color', $color);
            $db->execute();
            
            return $db->lastInsertId();
            
        } catch (Exception $e) {
            error_log("Error getting/creating variant: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Get variant info (size, color) from variant_id
     */
    private function getVariantInfo($variantId) {
        if (!$variantId) {
            return ['size' => null, 'color' => null];
        }
        
        try {
            require_once __DIR__ . '/../../configs/database.php';
            $db = Database::getInstance();
            
            $db->query("SELECT size, color FROM product_variants WHERE variant_id = :variant_id");
            $db->bind(':variant_id', $variantId);
            $result = $db->single();
            
            if ($result) {
                return [
                    'size' => $result->size,
                    'color' => $result->color
                ];
            }
            
            return ['size' => null, 'color' => null];
            
        } catch (Exception $e) {
            error_log("Error getting variant info: " . $e->getMessage());
            return ['size' => null, 'color' => null];
        }
    }

    /**
     * Insert cart item with variant_id
     */
    private function insertCartItemWithVariants($cartId, $productId, $price, $quantity, $size = null, $color = null) {
        try {
            $variantId = $this->getOrCreateVariantId($productId, $size, $color);
            
            require_once __DIR__ . '/../../configs/database.php';
            $db = Database::getInstance();
            
            // Insert cart item with variant_id
            $db->query("INSERT INTO cart_items (cart_id, product_id, variant_id, quantity, price, added_at, created_at, updated_at) 
                       VALUES (:cart_id, :product_id, :variant_id, :quantity, :price, NOW(), NOW(), NOW())");
            
            $db->bind(':cart_id', $cartId);
            $db->bind(':product_id', $productId);
            $db->bind(':variant_id', $variantId);
            $db->bind(':quantity', $quantity);
            $db->bind(':price', $price);
            $db->execute();
            
            return true;
            
        } catch (Exception $e) {
            error_log("Error inserting cart item: " . $e->getMessage());
            return false;
        }
    }
}
?>
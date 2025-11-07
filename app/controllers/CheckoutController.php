<?php
require_once __DIR__ . '/../../core/BaseController.php';
require_once __DIR__ . '/../models/Product.php';
require_once __DIR__ . '/../models/Order.php';
require_once __DIR__ . '/../../helpers/session_helper.php';

class CheckoutController extends BaseController {
    private $productModel;
    private $orderModel;

    public function __construct() {
        $this->productModel = new Product();
        $this->orderModel = new Order();
        SessionHelper::start();
    }

    /**
     * Show checkout page
     */
    public function index() {
        $cartItems = [];
        $cartSummary = [];
        
        // Check if this is a buy now checkout (via URL parameter or specific session)
        $isCartCheckout = isset($_GET['cart']);
        $isBuyNow = isset($_GET['buy_now']) || (!$isCartCheckout && isset($_SESSION['buy_now_item']) && empty($_SESSION['cart']));
        
        if ($isBuyNow && isset($_SESSION['buy_now_item'])) {
            // Buy now checkout
            $cartItems = [$this->getBuyNowItem()];
            $cartSummary = $this->calculateBuyNowSummary($_SESSION['buy_now_item']);
        } else {
            // Regular cart checkout - prioritize cart over buy_now_item
            if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
                header('Location: /Ecom_website/cart');
                exit;
            }
            $cartItems = $this->getCartItems();
            $cartSummary = $this->calculateCartSummary($cartItems);
            
            // Clear buy_now_item if we're doing cart checkout
            if (isset($_SESSION['buy_now_item'])) {
                unset($_SESSION['buy_now_item']);
            }
        }

        // Get user info if logged in
        $userInfo = null;
        if (SessionHelper::isLoggedIn()) {
            $userInfo = SessionHelper::getUser();
        }

        $this->view('customer/pages/checkout', [
            'title' => 'Thanh toán',
            'cartItems' => $cartItems,
            'cartSummary' => $cartSummary,
            'userInfo' => $userInfo
        ]);
    }

    /**
     * Process checkout
     */
    public function process() {
        // Ensure JSON response headers
        if (!headers_sent()) {
            header('Content-Type: application/json; charset=utf-8');
        }
        
        try {
            error_log("CheckoutController::process() started");
            error_log("POST data: " . print_r($_POST, true));
            
            // Validate cart or buy now item
            if (!isset($_SESSION['cart']) && !isset($_SESSION['buy_now_item'])) {
                error_log("No cart or buy_now_item in session");
                $this->jsonResponse(false, 'Không có sản phẩm để thanh toán');
                return;
            }

            if (!isset($_SESSION['buy_now_item']) && (empty($_SESSION['cart']))) {
                error_log("Empty cart");
                $this->jsonResponse(false, 'Giỏ hàng trống');
                return;
            }

            // Validate form data
            $errors = $this->validateCheckoutData($_POST);
            if (!empty($errors)) {
                error_log("Validation errors: " . print_r($errors, true));
                $this->jsonResponse(false, 'Dữ liệu không hợp lệ', $errors);
                return;
            }

        // Get payment delivery method
        $paymentDeliveryMethod = $_POST['payment_delivery_method'] ?? 'bank_transfer_home';
        
        // Get customer info
        $customerInfo = [
            'name' => trim($_POST['name']),
            'email' => trim($_POST['email']),
            'phone' => trim($_POST['phone']),
            'notes' => trim($_POST['notes'] ?? '')
        ];

        // Add address info only for home delivery
        if ($paymentDeliveryMethod === 'bank_transfer_home') {
            $customerInfo['address'] = trim($_POST['address'] ?? '');
            $customerInfo['ward'] = trim($_POST['ward'] ?? '');
            $customerInfo['province'] = trim($_POST['province'] ?? '');
        } else {
            // Store pickup - no address needed
            $customerInfo['address'] = null;
            $customerInfo['ward'] = null;
            $customerInfo['province'] = null;
        }            // Calculate order totals
            if (isset($_SESSION['buy_now_item'])) {
                // Buy now checkout
                $cartItems = [$this->getBuyNowItem()];
                $cartSummary = $this->calculateBuyNowSummary($_SESSION['buy_now_item']);
            } else {
                // Regular cart checkout
                $cartItems = $this->getCartItems();
                $cartSummary = $this->calculateCartSummary($cartItems);
            }

            // Validate stock availability
            foreach ($cartItems as $item) {
                if ($item['quantity'] > $item['product']->stock_quantity) {
                    $this->jsonResponse(false, "Sản phẩm '{$item['product']->name}' không đủ số lượng trong kho");
                    return;
                }
            }

            // Create order
            $orderId = $this->createOrder($customerInfo, $cartItems, $cartSummary, $paymentDeliveryMethod);

            if ($orderId) {
                error_log("Order created successfully with ID: " . $orderId);
                
                // Clear appropriate session data after successful order
                if (isset($_SESSION['buy_now_item'])) {
                    unset($_SESSION['buy_now_item']);
                } else {
                    unset($_SESSION['cart']);
                }

                $this->jsonResponse(true, 'Đặt hàng thành công!', [
                    'order_id' => $orderId,
                    'redirect' => url('/order-success?order_id=' . $orderId)
                ]);
            } else {
                error_log("Failed to create order");
                $this->jsonResponse(false, 'Có lỗi xảy ra khi tạo đơn hàng');
            }

        } catch (Exception $e) {
            error_log("Checkout Error: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            $this->jsonResponse(false, 'Có lỗi hệ thống xảy ra: ' . $e->getMessage());
        }
    }

    /**
     * Show order success page
     */
    public function orderSuccess() {
        $orderId = $_GET['order_id'] ?? null;
        
        if (!$orderId) {
            header('Location: /Ecom_website/');
            exit;
        }

        // Get order details (simplified - in real app would fetch from database)
        $this->view('customer/pages/order-success', [
            'title' => 'Đặt hàng thành công',
            'order_id' => $orderId
        ]);
    }

    /**
     * Create order in database
     */
    private function createOrder($customerInfo, $cartItems, $cartSummary, $paymentDeliveryMethod) {
        try {
            // Prepare order data to match database structure
            $orderData = [
                'user_id' => SessionHelper::isLoggedIn() ? SessionHelper::getUserId() : null,
                'full_name' => $customerInfo['name'],
                'email' => $customerInfo['email'],
                'phone' => $customerInfo['phone'],
                'street' => $customerInfo['address'], // Null for store pickup
                'ward' => $customerInfo['ward'], // Null for store pickup
                'province' => $customerInfo['province'], // Null for store pickup
                'country' => 'Vietnam',
                'order_status' => 'pending', // Always pending, admin will change manually
                'shipping_fee' => $cartSummary['shipping'],
                'total_amount' => $cartSummary['total'],
                'discount_code' => null,
                'discount_amount' => 0,
                'notes' => $customerInfo['notes']
            ];

            // Create order using proper database method
            error_log("Creating order with data: " . json_encode($orderData));
            $created = $this->orderModel->createOrder($orderData);
            error_log("Order creation result: " . ($created ? 'SUCCESS' : 'FAILED'));

            if ($created) {
                // Get the actual order ID from database (auto-increment primary key)
                $actualOrderId = $this->orderModel->getLastInsertId();
                error_log("Actual order ID: " . $actualOrderId);
                
                // Create order items
                foreach ($cartItems as $item) {
                    // Get variant_id if product has size and color
                    $variantId = null;
                    if (!empty($item['size']) && !empty($item['color'])) {
                        $variant = $this->productModel->getVariant(
                            $item['product']->product_id, 
                            $item['size'], 
                            $item['color']
                        );
                        if ($variant) {
                            $variantId = $variant->variant_id;
                        }
                        error_log("Found variant ID: $variantId for product {$item['product']->product_id}, size: {$item['size']}, color: {$item['color']}");
                    }
                    
                    $itemData = [
                        'order_id' => $actualOrderId,
                        'product_id' => $item['product']->product_id,
                        'variant_id' => $variantId, // Now properly set
                        'quantity' => $item['quantity'],
                        'unit_price' => $item['product']->base_price,
                        'total_price' => $item['subtotal'] ?? ($item['quantity'] * $item['product']->base_price)
                    ];
                    
                    error_log("Creating order item: " . json_encode($itemData));
                    $itemCreated = $this->orderModel->addOrderItem($itemData);
                    error_log("Order item creation result: " . ($itemCreated ? 'SUCCESS' : 'FAILED'));
                }

                // Create payment record
                $paymentData = [
                    'order_id' => $actualOrderId,
                    'payment_method' => strtoupper($paymentDeliveryMethod), // BANK_TRANSFER_HOME or CASH_STORE
                    'payment_status' => 'pending', // Always pending, admin will change manually
                    'amount' => $cartSummary['total']
                ];
                
                error_log("Creating payment record: " . json_encode($paymentData));
                $paymentCreated = $this->orderModel->createPayment($paymentData);
                error_log("Payment creation result: " . ($paymentCreated ? 'SUCCESS' : 'FAILED'));

                // Update product stock - Trừ stock ngay khi đặt hàng thành công
                foreach ($cartItems as $item) {
                    if (isset($item['size']) && isset($item['color']) && 
                        !empty($item['size']) && !empty($item['color'])) {
                        // Sản phẩm có variant (size + color) - sử dụng stored procedure
                        $stockUpdated = $this->productModel->updateStock(
                            $item['product']->product_id, 
                            $item['size'], 
                            $item['color'], 
                            $item['quantity'] // SỐ DƯƠNG - SP sẽ tự trừ (stock - quantity)
                        );
                        error_log("Stock update for variant product {$item['product']->product_id}: " . ($stockUpdated ? 'SUCCESS' : 'FAILED'));
                    } else {
                        // Sản phẩm không có variant - tạo method updateSimpleProductStock
                        $newStock = max(0, $item['product']->stock_quantity - $item['quantity']); // Không cho âm
                        $stockUpdated = $this->updateSimpleProductStock($item['product']->product_id, $newStock);
                        error_log("Stock update for simple product {$item['product']->product_id}: " . ($stockUpdated ? 'SUCCESS' : 'FAILED'));
                    }
                }

                return $actualOrderId;
            }

            return false;

        } catch (Exception $e) {
            error_log("Create Order Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Validate checkout form data
     */
    private function validateCheckoutData($data) {
        $errors = [];

        if (empty($data['name'])) {
            $errors['name'] = 'Họ tên là bắt buộc';
        }

        if (empty($data['email'])) {
            $errors['email'] = 'Email là bắt buộc';
        } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Email không hợp lệ';
        }

        if (empty($data['phone'])) {
            $errors['phone'] = 'Số điện thoại là bắt buộc';
        } elseif (!preg_match('/^[0-9]{10,11}$/', $data['phone'])) {
            $errors['phone'] = 'Số điện thoại không hợp lệ';
        }

        // Validate payment delivery method
        $paymentDeliveryMethod = $data['payment_delivery_method'] ?? '';
        $allowedMethods = ['bank_transfer_home', 'cash_store'];
        
        if (empty($paymentDeliveryMethod) || !in_array($paymentDeliveryMethod, $allowedMethods)) {
            $errors['payment_delivery_method'] = 'Phương thức thanh toán và nhận hàng không hợp lệ';
        }

        // Validate address only for home delivery
        if ($paymentDeliveryMethod === 'bank_transfer_home') {
            if (empty($data['address'])) {
                $errors['address'] = 'Địa chỉ giao hàng là bắt buộc khi chọn giao hàng tận nơi';
            }
            if (empty($data['province'])) {
                $errors['province'] = 'Tỉnh/Thành phố là bắt buộc khi chọn giao hàng tận nơi';
            }
        }

        return $errors;
    }

    /**
     * Get cart items with product details
     */
    private function getCartItems() {
        if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
            return [];
        }

        $cartItems = [];
        foreach ($_SESSION['cart'] as $cartKey => $cartItem) {
            $product = $this->productModel->findById($cartItem['product_id']);
            if ($product) {
                $cartItems[] = [
                    'cart_key' => $cartKey,
                    'product' => $product,
                    'quantity' => $cartItem['quantity'],
                    'size' => $cartItem['size'],
                    'color' => $cartItem['color'],
                    'subtotal' => $product->base_price * $cartItem['quantity']
                ];
            }
        }

        return $cartItems;
    }

    /**
     * Calculate cart summary
     */
    private function calculateCartSummary($cartItems) {
        $subtotal = 0;
        $totalQuantity = 0;

        foreach ($cartItems as $item) {
            $subtotal += $item['subtotal'];
            $totalQuantity += $item['quantity'];
        }

        $shipping = $subtotal >= 500000 ? 0 : 30000; // Free shipping over 500k VND
        $tax = $subtotal * 0.1; // 10% tax
        $total = $subtotal + $shipping + $tax;

        return [
            'subtotal' => $subtotal,
            'shipping' => $shipping,
            'tax' => $tax,
            'total' => $total,
            'totalQuantity' => $totalQuantity
        ];
    }

    /**
     * Get buy now item formatted for checkout
     */
    private function getBuyNowItem() {
        $buyNowData = $_SESSION['buy_now_item'];
        $product = $buyNowData['product'];
        
        return [
            'product_id' => $buyNowData['product_id'],
            'product' => $product,
            'quantity' => $buyNowData['quantity'],
            'price' => $buyNowData['price'],
            'size' => $buyNowData['size'],
            'color' => $buyNowData['color'],
            'subtotal' => $buyNowData['price'] * $buyNowData['quantity']
        ];
    }

    /**
     * Calculate buy now summary
     */
    private function calculateBuyNowSummary($buyNowItem) {
        $subtotal = $buyNowItem['price'] * $buyNowItem['quantity'];
        $shipping = $subtotal >= 500000 ? 0 : 30000; // Free shipping over 500k VND
        $tax = $subtotal * 0.1; // 10% tax
        $total = $subtotal + $shipping + $tax;

        return [
            'subtotal' => $subtotal,
            'shipping' => $shipping,
            'tax' => $tax,
            'total' => $total,
            'totalQuantity' => $buyNowItem['quantity']
        ];
    }

    /**
     * Update stock for simple products (without variants)
     */
    private function updateSimpleProductStock($productId, $newStock) {
        try {
            // Sử dụng update method để cập nhật stock_quantity
            return $this->productModel->update($productId, ['stock_quantity' => $newStock]);
        } catch (Exception $e) {
            error_log("Update Simple Product Stock Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Send JSON response
     */
    // private function jsonResponse($success, $message, $data = null) {
    //     header('Content-Type: application/json');
    //     $response = [
    //         'success' => $success,
    //         'message' => $message
    //     ];
        
    //     if ($data !== null) {
    //         $response['data'] = $data;
    //     }
        
    //     echo json_encode($response, JSON_UNESCAPED_UNICODE);
    //     exit;
    // }
}
?>
<?php
/**
 * OrdersController - Admin Order Management
 * Tuân thủ chuẩn MVC và OOP
 */

// Load EmailHelper để gửi email (sửa path đúng từ admin/controllers/admin → helpers)
require_once __DIR__ . '/../../../helpers/email_helper.php';

class OrdersController extends BaseController {
    private $orderModel;

    public function __construct() {
        // Dependency Injection
        $this->orderModel = $this->model('Order');
    }

    /**
     * Hiển thị danh sách đơn hàng
     * Method: GET
     */
    public function index() {
        try {
            // Lấy tất cả orders từ Model
            $orders = $this->orderModel->getAllOrders();

            $data = [
                'title' => 'Đơn Hàng',
                'orders' => $orders
            ];

            // Render admin page with layout
            $this->renderAdminPage('admin/pages/orders', $data);

        } catch (Exception $e) {
            $this->view('admin/error', ['message' => 'Có lỗi xảy ra: ' . $e->getMessage()]);
        }
    }

    /**
     * Cập nhật trạng thái thanh toán trong bảng PAYMENTS
     * Method: POST
     * Payment statuses: 'pending', 'completed', 'failed', 'refunded'
     * Khi status = 'completed' + BANK_TRANSFER → GỬI EMAIL
     */
    public function updatePayment() {
        // Chỉ chấp nhận POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $_SESSION['error'] = 'Invalid request method!';
            $this->redirect('index.php?url=orders');
            return;
        }

        try {
            $orderId = $_GET['id'] ?? null;
            $paymentStatus = $_POST['payment_status'] ?? null;

            error_log("========== UPDATE PAYMENT STATUS ==========");
            error_log("Order ID: $orderId");
            error_log("New Status: $paymentStatus");
            error_log("POST data: " . print_r($_POST, true));

            // Validate input
            if (!$orderId || !$paymentStatus) {
                throw new Exception('Thiếu thông tin: Order ID hoặc Payment Status');
            }

            // Validate status (theo enum trong database)
            $validStatuses = ['pending', 'completed', 'failed', 'refunded'];
            if (!in_array($paymentStatus, $validStatuses)) {
                throw new Exception("Trạng thái không hợp lệ: $paymentStatus. Chỉ chấp nhận: " . implode(', ', $validStatuses));
            }

            // Cập nhật payment_status trong bảng PAYMENTS
            $updateResult = $this->orderModel->updatePaymentStatus($orderId, $paymentStatus);

            if (!$updateResult) {
                throw new Exception('Không thể cập nhật trạng thái thanh toán - Vui lòng kiểm tra log');
            }

            // NẾU payment_status = 'completed' VÀ payment_method = 'BANK_TRANSFER_HOME' → GỬI EMAIL
            if ($paymentStatus === 'completed') {
                $order = $this->orderModel->getOrderWithCustomerEmail($orderId);
                $paymentMethod = $order->payment_method ?? null;
                
                error_log("Payment method: $paymentMethod");
                
                if ($paymentMethod === 'BANK_TRANSFER_HOME') {
                    error_log("Sending payment confirmation email...");
                    $this->sendPaymentConfirmationEmail($orderId);
                } else {
                    error_log("No email sent - Payment method is not BANK_TRANSFER_HOME");
                }
            }

            $_SESSION['success'] = 'Cập nhật trạng thái thanh toán thành công!';
            error_log("✓ SUCCESS: Payment status updated to '$paymentStatus'");
            error_log("==========================================");
            
        } catch (Exception $e) {
            $_SESSION['error'] = 'Lỗi: ' . $e->getMessage();
            error_log("✗ ERROR: " . $e->getMessage());
            error_log("==========================================");
        }
        //abc 

        // Redirect
        $this->redirect('index.php?url=orders&t=' . time());
    }

    /**
     * Cập nhật trạng thái đơn hàng
     * Method: POST
     * Database enum: 'pending', 'paid', 'shipped', 'delivered', 'cancelled'
     */
    public function updateOrder() {
        // Chỉ chấp nhận POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $_SESSION['error'] = 'Invalid request method!';
            $this->redirect('index.php?url=orders');
            return;
        }

        error_log("=== OrdersController::updateOrder START ===");
        error_log("Request Method: " . $_SERVER['REQUEST_METHOD']);
        error_log("POST Data: " . json_encode($_POST));
        error_log("GET Data: " . json_encode($_GET));

        try {
            $orderId = $_GET['id'] ?? null;
            $orderStatus = $_POST['order_status'] ?? null;

            error_log("Order ID: $orderId");
            error_log("Order Status (raw): $orderStatus");

            // Validate input
            if (!$orderId || !$orderStatus) {
                throw new Exception('Thiếu thông tin cần thiết');
            }

            // Validate order status theo database enum
            $validStatuses = ['pending', 'paid', 'shipped', 'delivered', 'cancelled'];
            if (!in_array($orderStatus, $validStatuses)) {
                error_log("Invalid order status attempted: $orderStatus");
                error_log("Valid statuses: " . implode(', ', $validStatuses));
                throw new Exception('Trạng thái đơn hàng không hợp lệ: ' . $orderStatus);
            }

            error_log("Order status validated successfully");

            // Cập nhật order status trong database
            error_log("Calling orderModel->updateStatus($orderId, $orderStatus)");
            $updateResult = $this->orderModel->updateStatus($orderId, $orderStatus);
            error_log("Update result: " . ($updateResult ? 'SUCCESS' : 'FAILED'));

            if (!$updateResult) {
                throw new Exception('Không thể cập nhật trạng thái đơn hàng');
            }

            $_SESSION['success'] = 'Cập nhật trạng thái đơn hàng thành công!';
            
            // Log success
            error_log("✓ Order #$orderId order status updated to: $orderStatus");
            error_log("=== OrdersController::updateOrder END (SUCCESS) ===");
            
        } catch (Exception $e) {
            $_SESSION['error'] = 'Lỗi: ' . $e->getMessage();
            error_log('✗ OrdersController::updateOrder Error: ' . $e->getMessage());
            error_log("=== OrdersController::updateOrder END (ERROR) ===");
        }

        // Redirect với cache busting
        $this->redirect('index.php?url=orders&t=' . time());
    }

    /**
     * Hiển thị chi tiết đơn hàng
     * Method: GET
     * Hỗ trợ cả VIEW mode và EDIT mode (inline edit với dropdowns)
     */
    public function showDetails() {
        try {
            $orderId = $_GET['id'] ?? null;
            
            if (!$orderId) {
                throw new Exception('Không tìm thấy ID đơn hàng');
            }

            // Lấy thông tin đơn hàng từ Model
            $order = $this->orderModel->getOrderDetails($orderId);
            
            if (!$order) {
                throw new Exception('Đơn hàng không tồn tại');
            }

            // Lấy danh sách items trong đơn hàng
            $orderItems = $this->orderModel->getOrderItems($orderId);
            
            // Tính toán totals (Private helper method - OOP)
            $calculations = $this->calculateOrderTotals($orderItems, $order);
            
            // Prepare data cho View
            $data = [
                'title' => 'Chi Tiết Đơn Hàng',
                'pageTitle' => 'Chi Tiết Đơn Hàng #' . $orderId,
                'breadcrumb' => 'Home > Đơn Hàng > Chi Tiết',
                'order' => $order,
                'orderItems' => $orderItems,
                'calculations' => $calculations,
                'editMode' => true  // Admin luôn có thể edit (inline via dropdowns)
            ];

            // Render admin page with layout
            $this->renderAdminPage('admin/pages/order-details', $data);

        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            error_log('OrdersController::showDetails Error: ' . $e->getMessage());
            $this->redirect('index.php?url=orders');
        }
    }

    /**
     * Legacy method - giữ lại để tương thích ngược
     */
    public function updateStatus() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $orderId = $_GET['id'] ?? null;
                $status = $_POST['order_status'] ?? null;
                
                if (!$orderId || !$status) {
                    throw new Exception('Thiếu thông tin đơn hàng hoặc trạng thái');
                }
                
                if ($this->orderModel->updateStatus($orderId, $status)) {
                    $_SESSION['success'] = 'Cập nhật trạng thái đơn hàng thành công!';
                } else {
                    $_SESSION['error'] = 'Có lỗi xảy ra khi cập nhật trạng thái!';
                }
            } catch (Exception $e) {
                $_SESSION['error'] = 'Lỗi: ' . $e->getMessage();
            }
        }
        
        header('Location: ' . BASE_URL . '/admin/index.php?url=orders');
        exit;
    }
    
    /**
     * Xóa vĩnh viễn đơn hàng khỏi database
     * Hard delete: xóa order, order_items và payments
     */
    public function delete() {
        try {
            $orderId = $_GET['id'] ?? null;
            
            if (!$orderId) {
                throw new Exception('Không tìm thấy ID đơn hàng');
            }

            error_log("=== Deleting Order #$orderId ===");
            
            // Gọi model để xóa (sẽ xóa cả order_items và payments)
            if ($this->orderModel->deleteById($orderId)) {
                $_SESSION['success'] = 'Đã xóa đơn hàng thành công!';
                error_log("✓ Order #$orderId deleted successfully");
            } else {
                $_SESSION['error'] = 'Có lỗi xảy ra khi xóa đơn hàng!';
                error_log("✗ Failed to delete order #$orderId");
            }
        } catch (Exception $e) {
            $_SESSION['error'] = 'Lỗi: ' . $e->getMessage();
            error_log("✗ Delete order error: " . $e->getMessage());
        }
        
        $this->redirect('index.php?url=orders');
    }

    // =================== PRIVATE HELPER METHODS (OOP Best Practice) ===================

    /**
     * Tính toán totals cho đơn hàng
     * @param array $orderItems
     * @param object $order
     * @return array
     */
    private function calculateOrderTotals($orderItems, $order) {
        $subtotal = 0;
        
        // Tính subtotal từ order items
        foreach ($orderItems as $item) {
            $subtotal += $item->total_price ?? ($item->quantity * $item->unit_price_snapshot);
        }
        
        // Lấy shipping fee và discount từ order
        $shippingFee = $order->shipping_fee ?? 0;
        $discountAmount = $order->discount_amount ?? 0;
        
        // Tính total
        $total = $subtotal + $shippingFee - $discountAmount;
        
        return [
            'subtotal' => $subtotal,
            'shipping_fee' => $shippingFee,
            'discount_amount' => $discountAmount,
            'discount_code' => $order->discount_code ?? null,
            'total' => $total
        ];
    }

    /**
     * Gửi email xác nhận thanh toán cho khách hàng
     * @param int $orderId
     * @return void
     */
    private function sendPaymentConfirmationEmail($orderId) {
        try {
            // Lấy thông tin đơn hàng kèm email khách hàng
            $order = $this->orderModel->getOrderWithCustomerEmail($orderId);

            if (!$order) {
                throw new Exception('Không tìm thấy đơn hàng');
            }

            // Kiểm tra có email không
            $customerEmail = $order->customer_email ?? $order->email;
            if (!$customerEmail) {
                throw new Exception('Không tìm thấy email khách hàng');
            }

            // Gửi email xác nhận thanh toán
            $emailSent = EmailHelper::sendPaymentConfirmationEmail($order);

            if ($emailSent) {
                // Log thành công
                error_log("Payment confirmation email sent to: $customerEmail for order #$orderId");
                
                // Thêm thông báo cho admin
                $_SESSION['email_status'] = 'Email xác nhận đã được gửi đến khách hàng';
            } else {
                throw new Exception('Không thể gửi email xác nhận');
            }

        } catch (Exception $e) {
            // Log lỗi nhưng không dừng quá trình cập nhật
            error_log('Email sending error: ' . $e->getMessage());
            $_SESSION['email_warning'] = 'Cảnh báo: Không thể gửi email xác nhận - ' . $e->getMessage();
        }
    }

    /**
     * Redirect helper method
     */
    private function redirect($url) {
        header("Location: $url");
        exit;
    }
}

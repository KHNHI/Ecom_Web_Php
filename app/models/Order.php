<?php

class Order extends BaseModel {
    protected $table = 'orders';
    protected $primaryKey = 'order_id';

    public function createOrder($data) {
        $this->db->query("INSERT INTO " . $this->table . " 
                         (user_id, full_name, email, phone, street, ward, province, country, 
                          order_status, shipping_fee, total_amount, discount_code, discount_amount, notes, created_at, updated_at) 
                         VALUES (:user_id, :full_name, :email, :phone, :street, :ward, :province, :country, 
                                 :order_status, :shipping_fee, :total_amount, :discount_code, :discount_amount, :notes, NOW(), NOW())");
        
        $this->db->bind(':user_id', $data['user_id'] ?? null);
        $this->db->bind(':full_name', $data['full_name']);
        $this->db->bind(':email', $data['email']);
        $this->db->bind(':phone', $data['phone']);
        $this->db->bind(':street', $data['street'] ?? null);
        $this->db->bind(':ward', $data['ward'] ?? null);
        $this->db->bind(':province', $data['province'] ?? null);
        $this->db->bind(':country', $data['country'] ?? 'Vietnam');
        $this->db->bind(':order_status', $data['order_status'] ?? 'pending');
        $this->db->bind(':shipping_fee', $data['shipping_fee'] ?? 0);
        $this->db->bind(':total_amount', $data['total_amount']);
        $this->db->bind(':discount_code', $data['discount_code'] ?? null);
        $this->db->bind(':discount_amount', $data['discount_amount'] ?? 0);
        $this->db->bind(':notes', $data['notes'] ?? null);
        
        return $this->db->execute();
    }

    /**
     * Get last inserted order ID
     */
    public function getLastInsertId() {
        return $this->db->lastInsertId();
    }

    /**
     * Create new order (simplified version for checkout)
     */
    // public function create($data) {
    //     try {
    //         $this->db->query("INSERT INTO " . $this->table . " 
    //                          (order_id, user_id, customer_name, customer_email, customer_phone,
    //                           shipping_address, payment_method, order_status, subtotal,
    //                           shipping_fee, tax_amount, total_amount, notes, created_at) 
    //                          VALUES (:order_id, :user_id, :customer_name, :customer_email, :customer_phone,
    //                                  :shipping_address, :payment_method, :order_status, :subtotal,
    //                                  :shipping_fee, :tax_amount, :total_amount, :notes, :created_at)");

    //         $this->db->bind(':order_id', $data['order_id']);
    //         $this->db->bind(':user_id', $data['user_id']);
    //         $this->db->bind(':customer_name', $data['customer_name']);
    //         $this->db->bind(':customer_email', $data['customer_email']);
    //         $this->db->bind(':customer_phone', $data['customer_phone']);
    //         $this->db->bind(':shipping_address', $data['shipping_address']);
    //         $this->db->bind(':payment_method', $data['payment_method']);
    //         $this->db->bind(':order_status', $data['order_status']);
    //         $this->db->bind(':subtotal', $data['subtotal']);
    //         $this->db->bind(':shipping_fee', $data['shipping_fee']);
    //         $this->db->bind(':tax_amount', $data['tax_amount']);
    //         $this->db->bind(':total_amount', $data['total_amount']);
    //         $this->db->bind(':notes', $data['notes']);
    //         $this->db->bind(':created_at', $data['created_at']);

    //         return $this->db->execute();

    //     } catch (Exception $e) {
    //         error_log("Create Order Error: " . $e->getMessage());
    //         return false;
    //     }
    // }

    /**
     * Find order by order ID
     */
    public function findByOrderId($orderId) {
        try {
            $this->db->query("SELECT * FROM " . $this->table . " WHERE order_id = :order_id");
            $this->db->bind(':order_id', $orderId);
            
            return $this->db->single();

        } catch (Exception $e) {
            error_log("Find Order Error: " . $e->getMessage());
            return false;
        }
    }

    public function updateOrderStatus($id, $status) {
        $validStatuses = ['pending', 'paid', 'shipped', 'delivered', 'cancelled'];
        if (!in_array($status, $validStatuses)) {
            return false;
        }
        
        $this->db->query("UPDATE " . $this->table . " SET order_status = :status, updated_at = NOW() WHERE order_id = :id");
        $this->db->bind(':status', $status);
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }

    public function getOrdersByUserId($userId) {
        $this->db->query("SELECT * FROM " . $this->table . " WHERE user_id = :user_id ORDER BY created_at DESC");
        $this->db->bind(':user_id', $userId);
        return $this->db->resultSet();
    }

    public function findByStatus($status) {
        $this->db->query("SELECT o.*, u.name as user_name FROM " . $this->table . " o 
                         LEFT JOIN users u ON o.user_id = u.user_id 
                         WHERE o.order_status = :status 
                         ORDER BY o.created_at DESC");
        $this->db->bind(':status', $status);
        return $this->db->resultSet();
    }

    /**
     * Hard delete - Xóa vĩnh viễn đơn hàng khỏi database
     * Xóa cả order_items và payments liên quan
     */
    public function deleteById($id) {
        try {
            // Bắt đầu transaction
            $this->db->beginTransaction();
            
            // 1. Xóa order_items
            $this->db->query("DELETE FROM order_items WHERE order_id = :order_id");
            $this->db->bind(':order_id', $id);
            $this->db->execute();
            
            // 2. Xóa payments
            $this->db->query("DELETE FROM payments WHERE order_id = :order_id");
            $this->db->bind(':order_id', $id);
            $this->db->execute();
            
            // 3. Xóa order
            $this->db->query("DELETE FROM " . $this->table . " WHERE order_id = :id");
            $this->db->bind(':id', $id);
            $result = $this->db->execute();
            
            // Commit transaction
            $this->db->commit();
            
            error_log("✓ Order #$id deleted successfully (hard delete)");
            return $result;
            
        } catch (Exception $e) {
            // Rollback nếu có lỗi
            $this->db->rollback();
            error_log("✗ Error deleting order #$id: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Update payment status in payments table
     * Valid statuses: 'pending', 'completed', 'failed', 'refunded'
     */
    public function updatePaymentStatus($orderId, $status) {
        try {
            // Validate status theo enum trong database
            $validStatuses = ['pending', 'completed', 'failed', 'refunded'];
            if (!in_array($status, $validStatuses)) {
                error_log("✗ Invalid payment status: $status");
                return false;
            }
            
            // Kiểm tra xem payment record có tồn tại không
            $this->db->query("SELECT payment_id FROM payments WHERE order_id = :order_id");
            $this->db->bind(':order_id', $orderId);
            $payment = $this->db->single();
            
            if ($payment) {
                // Payment record tồn tại - UPDATE
                $this->db->query("UPDATE payments 
                                 SET payment_status = :status,
                                     paid_at = CASE WHEN :status = 'completed' THEN NOW() ELSE paid_at END
                                 WHERE order_id = :order_id");
                $this->db->bind(':status', $status);
                $this->db->bind(':order_id', $orderId);
                $result = $this->db->execute();
                
                if ($result) {
                    error_log("✓ Payment status updated: Order #$orderId -> '$status'");
                } else {
                    error_log("✗ Failed to update payment status for order #$orderId");
                }
                
                return $result;
                
            } else {
                // Payment record KHÔNG tồn tại - INSERT mới
                error_log("⚠ No payment record for order #$orderId, creating new one...");
                
                // Lấy thông tin order
                $orderInfo = $this->findByOrderId($orderId);
                if (!$orderInfo) {
                    error_log("✗ Order #$orderId not found");
                    return false;
                }
                
                // Tạo payment record mới
                $this->db->query("INSERT INTO payments 
                                 (order_id, payment_method, payment_status, amount, created_at) 
                                 VALUES (:order_id, 'CASH_STORE', :status, :amount, NOW())");
                $this->db->bind(':order_id', $orderId);
                $this->db->bind(':status', $status);
                $this->db->bind(':amount', $orderInfo->total_amount ?? 0);
                $result = $this->db->execute();
                
                if ($result) {
                    error_log("✓ Created payment record: Order #$orderId with status '$status'");
                } else {
                    error_log("✗ Failed to create payment record for order #$orderId");
                }
                
                return $result;
            }
            
        } catch (Exception $e) {
            error_log("❌ Error in updatePaymentStatus: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get payment info for an order
     */
    public function getPaymentInfo($orderId) {
        $this->db->query("SELECT * FROM payments WHERE order_id = :order_id LIMIT 1");
        $this->db->bind(':order_id', $orderId);
        return $this->db->single();
    }
    
    /**
     * Get order with customer email for sending notifications
     */
    public function getOrderWithCustomerEmail($orderId) {
        $this->db->query("SELECT o.*, 
                         COALESCE(o.email, u.email) as customer_email,
                         COALESCE(o.full_name, u.name) as customer_name,
                         p.payment_method,
                         p.payment_status
                         FROM " . $this->table . " o 
                         LEFT JOIN users u ON o.user_id = u.user_id 
                         LEFT JOIN payments p ON o.order_id = p.order_id
                         WHERE o.order_id = :order_id");
        $this->db->bind(':order_id', $orderId);
        return $this->db->single();
    }

    /**
     * Update order status
     */
    public function updateStatus($orderId, $status) {
        $this->db->query("UPDATE " . $this->table . " 
                         SET order_status = :status, updated_at = NOW() 
                         WHERE order_id = :order_id");
        $this->db->bind(':status', $status);
        $this->db->bind(':order_id', $orderId);
        return $this->db->execute();
    }

    public function countOrders() {
        $this->db->query("SELECT COUNT(*) as total FROM " . $this->table);
        $result = $this->db->single();
        return $result->total;
    }

    public function countUserOrders($userId) {
        $this->db->query("SELECT COUNT(*) as total FROM " . $this->table . " WHERE user_id = :user_id");
        $this->db->bind(':user_id', $userId);
        $result = $this->db->single();
        return $result->total;
    }

    public function getAllOrders(){
        $this->db->query("SELECT o.*, 
                         u.name as user_name,
                         COALESCE(o.full_name, u.name) as customer_name,
                         COALESCE(o.email, u.email) as customer_email,
                         p.payment_status,
                         p.payment_method
                         FROM " . $this->table . " o 
                         LEFT JOIN users u ON o.user_id = u.user_id 
                         LEFT JOIN payments p ON o.order_id = p.order_id
                         ORDER BY o.created_at DESC");
        return $this->db->resultSet();
    }

    public function getOrderDetails($orderId) {
        $this->db->query("SELECT o.*, 
                         u.name as user_name, 
                         u.email as user_email,
                         p.payment_status,
                         p.payment_method,
                         p.transaction_code
                         FROM " . $this->table . " o 
                         LEFT JOIN users u ON o.user_id = u.user_id 
                         LEFT JOIN payments p ON o.order_id = p.order_id
                         WHERE o.order_id = :id");
        $this->db->bind(':id', $orderId);
        return $this->db->single();
    }

    // Get order items
    public function getOrderItems($orderId) {
        $this->db->query("SELECT oi.*, p.name as product_name, p.base_price, pv.variant_id, pv.price 
                         FROM order_items oi 
                         JOIN products p ON oi.product_id = p.product_id 
                         LEFT JOIN product_variants pv ON oi.variant_id = pv.variant_id 
                         WHERE oi.order_id = :order_id");
        $this->db->bind(':order_id', $orderId);
        return $this->db->resultSet();
    }

    // Add item to order
    public function addOrderItem($orderData) {
        $this->db->query("INSERT INTO order_items 
                         (order_id, product_id, variant_id, quantity, unit_price_snapshot, total_price) 
                         VALUES (:order_id, :product_id, :variant_id, :quantity, :unit_price_snapshot, :total_price)");
        
        $this->db->bind(':order_id', $orderData['order_id']);
        $this->db->bind(':product_id', $orderData['product_id']);
        $this->db->bind(':variant_id', $orderData['variant_id'] ?? null);
        $this->db->bind(':quantity', $orderData['quantity']);
        $this->db->bind(':unit_price_snapshot', $orderData['unit_price']);
        $this->db->bind(':total_price', $orderData['total_price']);
        
        return $this->db->execute();
    }

    /**
     * Create payment record for order
     */
    public function createPayment($paymentData) {
        try {
            $this->db->query("INSERT INTO payments 
                             (order_id, payment_method, payment_status, amount, created_at) 
                             VALUES (:order_id, :payment_method, :payment_status, :amount, NOW())");
            
            $this->db->bind(':order_id', $paymentData['order_id']);
            $this->db->bind(':payment_method', $paymentData['payment_method']);
            $this->db->bind(':payment_status', $paymentData['payment_status']);
            $this->db->bind(':amount', $paymentData['amount']);
            
            return $this->db->execute();
        } catch (Exception $e) {
            error_log("Create Payment Error: " . $e->getMessage());
            return false;
        }
    }

    // Get orders with pagination
    public function getOrdersWithPagination($limit = 10, $offset = 0) {
        $this->db->query("SELECT o.*, u.name as user_name 
                         FROM " . $this->table . " o 
                         LEFT JOIN users u ON o.user_id = u.user_id 
                         ORDER BY o.created_at DESC 
                         LIMIT :limit OFFSET :offset");
        $this->db->bind(':limit', $limit);
        $this->db->bind(':offset', $offset);
        return $this->db->resultSet();
    }

    // Calculate total revenue
    public function calculateTotalRevenue() {
        $this->db->query("SELECT SUM(total_amount) as total_revenue 
                         FROM " . $this->table . " 
                         WHERE order_status IN ('paid', 'shipped', 'delivered')");
        $result = $this->db->single();
        return $result->total_revenue ?? 0;
    }

    // Get orders by date range
    public function getOrdersByDateRange($startDate, $endDate) {
        $this->db->query("SELECT o.*, u.name as user_name 
                         FROM " . $this->table . " o 
                         LEFT JOIN users u ON o.user_id = u.user_id 
                         WHERE DATE(o.created_at) BETWEEN :start_date AND :end_date 
                         ORDER BY o.created_at DESC");
        $this->db->bind(':start_date', $startDate);
        $this->db->bind(':end_date', $endDate);
        return $this->db->resultSet();
    }

    // Get revenue by date range
    public function getRevenueByDateRange($startDate, $endDate) {
        $this->db->query("SELECT DATE(created_at) as date, SUM(total_amount) as daily_revenue 
                         FROM " . $this->table . " 
                         WHERE order_status IN ('paid', 'shipped', 'delivered') 
                         AND DATE(created_at) BETWEEN :start_date AND :end_date 
                         GROUP BY DATE(created_at) 
                         ORDER BY date DESC");
        $this->db->bind(':start_date', $startDate);
        $this->db->bind(':end_date', $endDate);
        return $this->db->resultSet();
    }

    // Get order statistics
    public function getOrderStatistics() {
        $this->db->query("SELECT 
                             order_status, 
                             COUNT(*) as count, 
                             SUM(total_amount) as total_amount 
                         FROM " . $this->table . " 
                         GROUP BY order_status");
        return $this->db->resultSet();
    }
     public function getTotalCount() {
        $sql = "SELECT COUNT(*) as total FROM {$this->table}";
        $this->db->query($sql);
        $result = $this->db->single();
        return $result ? (int)$result->total : 0;
    }
    public function getTotalRevenue() {
        return $this->calculateTotalRevenue();
    }
       public function getRecentOrders($limit = 10) {
        $sql = "SELECT o.*, u.name as customer_name, u.email as customer_email
                FROM {$this->table} o 
                LEFT JOIN users u ON o.user_id = u.user_id
                ORDER BY o.created_at DESC 
                LIMIT :limit";
        $this->db->query($sql);
        $this->db->bind(':limit', $limit, PDO::PARAM_INT);
        return $this->db->resultSet();
    }
    
}
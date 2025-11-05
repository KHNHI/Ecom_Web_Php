<?php
/**
 * Order Details View - Pure MVC View
 * Hiển thị chi tiết đơn hàng từ database
 * Hỗ trợ cả VIEW mode và EDIT mode (giống product-details pattern)
 * 
 * Biến được truyền từ Controller:
 * - $order: Thông tin đơn hàng từ database
 * - $orderItems: Danh sách sản phẩm trong đơn
 * - $calculations: Totals (subtotal, shipping, discount, total)
 * - $title, $pageTitle, $breadcrumb
 * - $editMode: true/false (cho phép chỉnh sửa hay chỉ xem)
 */

$editMode = $editMode ?? true; // Mặc định là edit mode (để admin có thể sửa)
$order = $order ?? null;

if (!$order) {
    echo "<div class='alert alert-danger'>Không tìm thấy đơn hàng!</div>";
    return;
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'Chi Tiết Đơn Hàng') ?> - Trang Sức Admin</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="app/views/admin/assets/css/variables.css">
    <link rel="stylesheet" href="app/views/admin/assets/css/main.css">
</head>
<body>
    <div class="admin-wrapper">
        <!-- Sidebar Component Container -->
        <div id="sidebar-container"></div>

        <!-- Main Content -->
        <div class="main-content">
            <!-- Header Component Container -->
            <div id="header-container"></div>

            <!-- Content -->
            <main class="content">
                <!-- Back Button -->
                <div class="mb-3">
                    <a href="index.php?url=orders" class="btn btn-outline-secondary btn-sm">
                        <img src="https://cdn-icons-png.flaticon.com/512/271/271220.png" alt="Back" width="14" height="14" class="me-1">
                        Quay lại Danh Sách
                    </a>
                </div>

                <!-- Order Header -->
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 gap-3">
                    <div class="w-100 w-md-auto">
                        <h3 class="mb-2 fs-4 fs-md-3">Đơn Hàng: <span class="badge bg-primary px-2 px-md-3 py-2">#<?= $order->order_id ?></span></h3>
                        <div class="d-flex align-items-center text-muted small">
                            <img src="https://cdn-icons-png.flaticon.com/512/2693/2693507.png" alt="Calendar" width="16" height="16" class="me-2">
                            <span><?= date('d/m/Y H:i', strtotime($order->created_at)) ?></span>
                        </div>
                    </div>
                    <div class="w-100 w-md-auto">
                        <?php
                        // Order status theo database: 'pending', 'paid', 'shipped', 'delivered', 'cancelled'
                        $statusConfig = [
                            'pending' => ['class' => 'warning', 'text' => 'Chờ xử lý'],
                            'paid' => ['class' => 'info', 'text' => 'Đã thanh toán'],
                            'shipped' => ['class' => 'primary', 'text' => 'Đang giao hàng'],
                            'delivered' => ['class' => 'success', 'text' => 'Đã giao hàng'],
                            'cancelled' => ['class' => 'danger', 'text' => 'Đã hủy']
                        ];
                        $currentStatus = $statusConfig[$order->order_status ?? 'pending'] ?? $statusConfig['pending'];
                        ?>
                        <span class="badge bg-<?= $currentStatus['class'] ?> px-2 px-md-3 py-2 w-100 w-md-auto text-center">
                            <?= $currentStatus['text'] ?>
                        </span>
                    </div>
                </div>

                <!-- Flash Messages -->
                <?php if (isset($_SESSION['success'])): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?= $_SESSION['success'] ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    <?php unset($_SESSION['success']); ?>
                <?php endif; ?>
                
                <?php if (isset($_SESSION['error'])): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?= $_SESSION['error'] ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    <?php unset($_SESSION['error']); ?>
                <?php endif; ?>

                <!-- Order Information Cards -->
                <div class="row mb-4">
                    <!-- Customer Info -->
                    <div class="col-lg-4 col-md-6 col-12 mb-3">
                        <div class="card h-100">
                            <div class="card-body">
                                <div class="text-center mb-3">
                                    <div class="bg-primary bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                        <img src="https://cdn-icons-png.flaticon.com/512/3135/3135715.png" alt="Customer" width="28" height="28">
                                    </div>
                                </div>
                                <h6 class="fw-bold text-center mb-3 small">Thông Tin Khách Hàng</h6>
                                
                                <div class="mb-2">
                                    <label class="small text-muted">Họ Tên:</label>
                                    <div class="fw-bold"><?= htmlspecialchars($order->full_name ?? $order->customer_name ?? 'N/A') ?></div>
                                </div>
                                
                                <div class="mb-2">
                                    <label class="small text-muted">Email:</label>
                                    <div class="small"><?= htmlspecialchars($order->email ?? $order->customer_email ?? 'N/A') ?></div>
                                </div>
                                
                                <div class="mb-2">
                                    <label class="small text-muted">Điện Thoại:</label>
                                    <div class="small"><?= htmlspecialchars($order->phone ?? 'N/A') ?></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Order Status Management (EDIT MODE) -->
                    <div class="col-lg-4 col-md-6 col-12 mb-3">
                        <div class="card h-100">
                            <div class="card-body">
                                <div class="text-center mb-3">
                                    <div class="bg-warning bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                        <img src="https://cdn-icons-png.flaticon.com/512/3063/3063822.png" alt="Order" width="28" height="28">
                                    </div>
                                </div>
                                <h6 class="fw-bold text-center mb-3 small">Trạng Thái Đơn Hàng</h6>
                                
                                <?php if ($editMode): ?>
                                    <!-- Editable: Dropdown -->
                                    <div class="mb-3">
                                        <label class="small text-muted mb-1">Trạng Thái Thanh Toán:</label>
                                        <?php
                                        // Payment statuses: 'pending', 'completed', 'failed', 'refunded' (theo database)
                                        $paymentStatus = $order->payment_status ?? 'pending';
                                        $paymentStatusTexts = [
                                            'pending' => 'Chờ thanh toán',
                                            'completed' => 'Hoàn thành',
                                            'failed' => 'Thất bại',
                                            'refunded' => 'Hoàn tiền'
                                        ];
                                        ?>
                                        <select class="form-select form-select-sm" 
                                                onchange="updatePaymentStatus(<?= $order->order_id ?>, this.value)"
                                                data-original="<?= $paymentStatus ?>">
                                            <option value="pending" <?= $paymentStatus === 'pending' ? 'selected' : '' ?>>
                                                Chờ thanh toán
                                            </option>
                                            <option value="completed" <?= $paymentStatus === 'completed' ? 'selected' : '' ?>>
                                                Hoàn thành
                                            </option>
                                            <option value="failed" <?= $paymentStatus === 'failed' ? 'selected' : '' ?>>
                                                Thất bại
                                            </option>
                                            <option value="refunded" <?= $paymentStatus === 'refunded' ? 'selected' : '' ?>>
                                                Hoàn tiền
                                            </option>
                                        </select>
                                        <small class="text-muted d-block mt-1">
                                            Hiện tại: <strong><?= $paymentStatusTexts[$paymentStatus] ?? $paymentStatus ?></strong>
                                        </small>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="small text-muted mb-1">Trạng Thái Đơn:</label>
                                        <select class="form-select form-select-sm" 
                                                onchange="updateOrderStatus(<?= $order->order_id ?>, this.value)"
                                                data-original="<?= $order->order_status ?? 'pending' ?>">
                                            <option value="pending" <?= ($order->order_status ?? 'pending') === 'pending' ? 'selected' : '' ?>>Chờ xử lý</option>
                                            <option value="paid" <?= ($order->order_status ?? '') === 'paid' ? 'selected' : '' ?>>Đã thanh toán</option>
                                            <option value="shipped" <?= ($order->order_status ?? '') === 'shipped' ? 'selected' : '' ?>>Đang giao hàng</option>
                                            <option value="delivered" <?= ($order->order_status ?? '') === 'delivered' ? 'selected' : '' ?>>Đã giao hàng</option>
                                            <option value="cancelled" <?= ($order->order_status ?? '') === 'cancelled' ? 'selected' : '' ?>>Đã hủy</option>
                                        </select>
                                        <small class="text-muted d-block mt-1">
                                            Hiện tại: <strong><?php 
                                            $orderStatusTexts = [
                                                'pending' => 'Chờ xử lý',
                                                'paid' => 'Đã thanh toán',
                                                'shipped' => 'Đang giao hàng',
                                                'delivered' => 'Đã giao hàng',
                                                'cancelled' => 'Đã hủy'
                                            ];
                                            echo $orderStatusTexts[$order->order_status ?? 'pending'] ?? $order->order_status;
                                            ?></strong>
                                        </small>
                                    </div>
                                <?php else: ?>
                                    <!-- Read-only -->
                                    <div class="mb-2">
                                        <label class="small text-muted">Thanh toán:</label>
                                        <?php
                                        $paymentStatus = $order->payment_status ?? 'pending';
                                        $paymentBadgeClass = [
                                            'pending' => 'warning',
                                            'completed' => 'success',
                                            'failed' => 'danger',
                                            'refunded' => 'info'
                                        ];
                                        $paymentTexts = [
                                            'pending' => 'Chờ thanh toán',
                                            'completed' => 'Hoàn thành',
                                            'failed' => 'Thất bại',
                                            'refunded' => 'Hoàn tiền'
                                        ];
                                        ?>
                                        <div>
                                            <span class="badge bg-<?= $paymentBadgeClass[$paymentStatus] ?? 'secondary' ?>">
                                                <?= $paymentTexts[$paymentStatus] ?? $paymentStatus ?>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="mb-2">
                                        <label class="small text-muted">Trạng thái đơn:</label>
                                        <div><?= $currentStatus['text'] ?></div>
                                    </div>
                                <?php endif; ?>
                                
                                <div class="text-center small text-muted mt-2">
                                    Ngày đặt: <?= date('d/m/Y', strtotime($order->created_at)) ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Shipping Address -->
                    <div class="col-lg-4 col-md-12 col-12 mb-3">
                        <div class="card h-100">
                            <div class="card-body">
                                <div class="text-center mb-3">
                                    <div class="bg-success bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                        <img src="https://cdn-icons-png.flaticon.com/512/2072/2072130.png" alt="Delivery" width="28" height="28">
                                    </div>
                                </div>
                                <h6 class="fw-bold text-center mb-3 small">Địa Chỉ Giao Hàng</h6>
                                
                                <div class="small">
                                    <div class="mb-1"><strong><?= htmlspecialchars($order->street ?? 'N/A') ?></strong></div>
                                    <div class="mb-1 text-muted"><?= htmlspecialchars($order->ward ?? '') ?></div>
                                    <div class="mb-1 text-muted"><?= htmlspecialchars($order->province ?? '') ?></div>
                                    <div class="text-muted"><?= htmlspecialchars($order->country ?? 'Vietnam') ?></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Products Table -->
                <div class="table-card">
                    <div class="table-header">
                        <h5 class="table-title">Sản Phẩm Trong Đơn Hàng</h5>
                    </div>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th class="text-nowrap">Sản Phẩm</th>
                                    <th class="text-center text-nowrap d-none d-sm-table-cell">Số Lượng</th>
                                    <th class="text-end text-nowrap d-none d-md-table-cell">Đơn Giá</th>
                                    <th class="text-end text-nowrap">Thành Tiền</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($orderItems)): ?>
                                    <?php foreach ($orderItems as $item): ?>
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <img src="https://via.placeholder.com/40x40?text=P" 
                                                         alt="Product" 
                                                         width="40" 
                                                         height="40" 
                                                         class="rounded me-2 flex-shrink-0">
                                                    <div class="min-w-0">
                                                        <div class="fw-bold text-truncate small"><?= htmlspecialchars($item->product_name ?? 'N/A') ?></div>
                                                        <?php if (!empty($item->variant_name)): ?>
                                                            <div class="small text-muted text-truncate">Variant: <?= htmlspecialchars($item->variant_name) ?></div>
                                                        <?php endif; ?>
                                                        <!-- Mobile: show quantity -->
                                                        <div class="d-sm-none small mt-1">
                                                            <span class="badge bg-secondary">SL: <?= $item->quantity ?></span>
                                                            <span class="d-md-none text-muted ms-2"><?= number_format($item->unit_price_snapshot, 0, ',', '.') ?>đ</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="text-center d-none d-sm-table-cell">
                                                <span class="badge bg-secondary"><?= $item->quantity ?></span>
                                            </td>
                                            <td class="text-end text-nowrap d-none d-md-table-cell small"><?= number_format($item->unit_price_snapshot, 0, ',', '.') ?> VND</td>
                                            <td class="text-end fw-bold text-primary text-nowrap small">
                                                <?= number_format($item->total_price ?? ($item->quantity * $item->unit_price_snapshot), 0, ',', '.') ?><span class="d-none d-sm-inline"> VND</span><span class="d-sm-none">đ</span>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="4" class="text-center py-4 text-muted">
                                            Không có sản phẩm trong đơn hàng này
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Order Summary -->
                    <div class="card-footer bg-light">
                        <div class="row justify-content-end">
                            <div class="col-md-4">
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-muted">Tạm tính:</span>
                                    <span class="fw-bold"><?= number_format($calculations['subtotal'], 0, ',', '.') ?> VND</span>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-muted">Phí vận chuyển:</span>
                                    <span class="fw-bold"><?= number_format($calculations['shipping_fee'], 0, ',', '.') ?> VND</span>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-muted">Giảm giá:</span>
                                    <span class="fw-bold text-danger">
                                        <?php if ($calculations['discount_amount'] > 0): ?>
                                            -<?= number_format($calculations['discount_amount'], 0, ',', '.') ?> VND
                                            <?php if (!empty($calculations['discount_code'])): ?>
                                                <br><small class="text-muted">(<?= htmlspecialchars($calculations['discount_code']) ?>)</small>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            0 VND
                                        <?php endif; ?>
                                    </span>
                                </div>
                                <hr>
                                <div class="d-flex justify-content-between">
                                    <span class="fw-bold fs-6">Tổng Cộng:</span>
                                    <span class="fw-bold fs-5 text-success"><?= number_format($calculations['total'], 0, ',', '.') ?> VND</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="d-flex gap-2 justify-content-end mt-4">
                    <a href="index.php?url=orders" class="btn btn-outline-secondary">
                        <img src="https://cdn-icons-png.flaticon.com/512/271/271220.png" alt="Back" width="16" height="16" class="me-1">
                        Quay Lại
                    </a>
                    <button class="btn btn-primary" onclick="printOrder()">
                        <img src="https://cdn-icons-png.flaticon.com/512/2965/2965937.png" alt="Print" width="16" height="16" class="me-1">
                        In Đơn Hàng
                    </button>
                </div>
            </main>
        </div>
    </div>

    <!-- Component Manager -->
    <script src="app/views/admin/components/component-manager.js"></script>
    
    <!-- Page Configuration -->
    <script>
        window.pageConfig = {
            sidebar: {
                brandName: 'Trang Sức',
                activePage: 'orders',
                links: {
                    dashboard: 'index.php?url=dashboard',
                    products: 'index.php?url=products',
                    categories: 'index.php?url=categories',
                    collections: 'index.php?url=collections',
                    orders: 'index.php?url=orders',
                    customers: 'index.php?url=customers',
                    reviews: 'index.php?url=reviews'
                },
                categoriesTitle: 'DANH MỤC'
            },
            header: {
                title: '<?= htmlspecialchars($pageTitle ?? 'Chi Tiết Đơn Hàng') ?>',
                breadcrumb: '<?= htmlspecialchars($breadcrumb ?? 'Home > Đơn Hàng > Chi Tiết') ?>'
            }
        };

        const editMode = <?= $editMode ? 'true' : 'false' ?>;

        // Print order function
        function printOrder() {
            window.print();
        }

        // Update payment status (Inline edit - MVC compliant)
        function updatePaymentStatus(orderId, newStatus) {
            const selectElement = event.target;
            const originalValue = selectElement.getAttribute('data-original');
            
            const statusTexts = {
                'pending': 'Chờ thanh toán',
                'completed': 'Hoàn thành',
                'failed': 'Thất bại',
                'refunded': 'Hoàn tiền'
            };
            
            const statusText = statusTexts[newStatus] || newStatus;
            
            console.log('=== Update Payment Status (Order Details) ===');
            console.log('Order ID:', orderId);
            console.log('New Status:', newStatus);
            console.log('Status Text:', statusText);
            
            if (confirm(`Bạn có chắc chắn muốn thay đổi trạng thái thanh toán thành "${statusText}"?`)) {
                selectElement.disabled = true;
                selectElement.style.opacity = '0.6';
                
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = 'index.php?url=orders&action=updatePayment&id=' + orderId;
                
                const statusInput = document.createElement('input');
                statusInput.type = 'hidden';
                statusInput.name = 'payment_status';
                statusInput.value = newStatus;
                
                form.appendChild(statusInput);
                document.body.appendChild(form);
                
                console.log('Form action:', form.action);
                console.log('Submitting form...');
                
                form.submit();
            } else {
                console.log('User cancelled');
                selectElement.value = originalValue;
            }
        }

        // Update order status (Inline edit - MVC compliant)
        function updateOrderStatus(orderId, newStatus) {
            const selectElement = event.target;
            const originalValue = selectElement.getAttribute('data-original');
            
            // Order status theo database: 'pending', 'paid', 'shipped', 'delivered', 'cancelled'
            const statusTexts = {
                'pending': 'Chờ xử lý',
                'paid': 'Đã thanh toán',
                'shipped': 'Đang giao hàng',
                'delivered': 'Đã giao hàng',
                'cancelled': 'Đã hủy'
            };
            
            const statusText = statusTexts[newStatus] || newStatus;
            
            console.log('=== Update Order Status (Order Details) ===');
            console.log('Order ID:', orderId);
            console.log('New Status:', newStatus);
            console.log('Status Text:', statusText);
            
            if (confirm(`Bạn có chắc chắn muốn thay đổi trạng thái đơn hàng thành "${statusText}"?`)) {
                selectElement.disabled = true;
                selectElement.style.opacity = '0.6';
                
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = 'index.php?url=orders&action=updateOrder&id=' + orderId;
                
                const statusInput = document.createElement('input');
                statusInput.type = 'hidden';
                statusInput.name = 'order_status';
                statusInput.value = newStatus;
                
                form.appendChild(statusInput);
                document.body.appendChild(form);
                
                console.log('Form action:', form.action);
                console.log('Submitting form...');
                
                form.submit();
            } else {
                console.log('User cancelled');
            
                selectElement.value = originalValue;
            }
        }
    </script>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Main JS -->
    <script src="app/views/admin/assets/js/main.js"></script>
</body>
</html>

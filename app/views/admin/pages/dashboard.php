<?php
/**
 * Dashboard View - Pure MVC View
 * Tuân thủ nguyên tắc MVC/OOP
 */

// Biến được truyền từ Controller:
// $stats - thống kê tổng quan từ database
// $recentOrders - đơn hàng gần đây từ database
// $bestSellers - sản phẩm bán chạy từ database
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Biểu đồ - Trang Sức Admin</title>
    
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


<!-- Stats Cards -->
<div class="row mb-4">
    <div class="col-lg-3 col-md-6 col-sm-6 mb-3">
        <div class="stats-card">
            <div class="stats-value">$<?= number_format($stats['total_revenue'] ?? 0, 0, ',', '.') ?></div>
            <div class="stats-label">Tổng Doanh Thu</div>
           
        </div>
    </div>
    <div class="col-lg-3 col-md-6 col-sm-6 mb-3">
        <div class="stats-card">
            <div class="stats-value"><?= number_format($stats['total_orders'] ?? 0, 0, ',', '.') ?></div>
            <div class="stats-label">Tổng Đơn Hàng</div>
           
        </div>
    </div>
    <div class="col-lg-3 col-md-6 col-sm-6 mb-3">
        <div class="stats-card">
            <div class="stats-value"><?= number_format($stats['total_products'] ?? 0, 0, ',', '.') ?></div>
            <div class="stats-label">Tổng Sản Phẩm</div>
            
        </div>
    </div>
    <div class="col-lg-3 col-md-6 col-sm-6 mb-3">
        <div class="stats-card">
            <div class="stats-value"><?= number_format($stats['total_customers'] ?? 0, 0, ',', '.') ?></div>
            <div class="stats-label">Tổng Khách Hàng</div>
            
        </div>
    </div>
</div>

<!-- Main Content Grid -->
<div class="row">
    <!-- Recent Orders - Left Side (70%) -->
    <div class="col-lg-8 col-md-12 mb-4">
        <div class="table-card">
            <div class="table-header">
                <h5 class="table-title">Đơn Hàng Gần Đây</h5>
                
            </div>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th style="background-color: white; color: #b8860b; font-weight: bold; text-align: center;">Mã Đơn Hàng</th>
                            <th style="background-color: white; color: #b8860b; font-weight: bold; text-align: center;">Khách Hàng</th>
                            <th style="background-color: white; color: #b8860b; font-weight: bold; text-align: center;">Sản Phẩm</th>
                            <th style="background-color: white; color: #b8860b; font-weight: bold; text-align: center;">Ngày Đặt</th>
                            <th style="background-color: white; color: #b8860b; font-weight: bold; text-align: center;">Trạng Thái</th>
                            <th style="background-color: white; color: #b8860b; font-weight: bold; text-align: center;">Tổng Tiền</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($recentOrders)): ?>
                            <?php foreach ($recentOrders as $order): ?>
                                <tr>
                                    <td class="text-center">
                                        <strong>#<?= $order->order_id ?></strong>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img src="https://cdn-icons-png.flaticon.com/512/3135/3135715.png" alt="User" width="24" height="24" class="rounded-circle me-2">
                                            <div>
                                                <div class="fw-bold"><?= htmlspecialchars($order->customer_name ?? 'Khách hàng') ?></div>
                                                <small class="text-muted d-none d-md-block"><?= htmlspecialchars($order->customer_email ?? '') ?></small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <?php if ($order->product_names === 'Chưa có sản phẩm'): ?>
                                            <span class="text-muted"><?= htmlspecialchars($order->product_names) ?></span>
                                        <?php else: ?>
                                            <div class="product-names">
                                                <?= htmlspecialchars($order->product_names) ?>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="text-nowrap">
                                            <?= date('d/m/Y', strtotime($order->created_at)) ?>
                                            <br>
                                            <small class="text-muted"><?= date('H:i', strtotime($order->created_at)) ?></small>
                                        </div>
                                    </td>
                                    <td>
                                        <?php
                                        // Xử lý trạng thái đơn hàng - tuân thủ MVC (logic đơn giản trong view)
                                        $statusConfig = [
                                            'delivered' => ['class' => 'delivered', 'text' => 'Đã giao'],
                                            'shipped' => ['class' => 'shipped', 'text' => 'Đang giao'],
                                            'paid' => ['class' => 'paid', 'text' => 'Đã thanh toán'],
                                            'cancelled' => ['class' => 'canceled', 'text' => 'Đã hủy'],
                                            'pending' => ['class' => 'pending', 'text' => 'Chờ xử lý']
                                        ];
                                        
                                        $status = $statusConfig[$order->order_status] ?? $statusConfig['pending'];
                                        ?>
                                        <span class="badge badge-<?= $status['class'] ?> badge-custom">
                                            <?= $status['text'] ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="text-end">
                                            <strong class="text-success">$<?= number_format($order->total_amount, 2) ?></strong>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center py-4">
                                    <div class="text-muted">
                                        <img src="https://cdn-icons-png.flaticon.com/512/2920/2920277.png" alt="No Data" width="48" height="48" class="mb-3 opacity-50">
                                        <p>Chưa có đơn hàng nào</p>
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Best Sellers - Right Side (30%) -->
    <div class="col-lg-4 col-md-12 mb-4">
        <div class="table-card h-100">
            <div class="table-header">
                <h5 class="table-title">Sản Phẩm Bán Chạy</h5>
                
            </div>
            <div class="p-2 p-sm-3">
                <?php if (!empty($bestSellers)): ?>
                    <?php foreach ($bestSellers as $index => $product): ?>
                        <div class="d-flex align-items-center mb-2 mb-sm-3 pb-2 pb-sm-3 <?= $index < count($bestSellers) - 1 ? 'border-bottom border-custom' : '' ?>">
                            <img src="<?= !empty($product->primary_image) ? $product->primary_image : 'https://images.unsplash.com/photo-1708221382764-299d9e3ad257?q=80&w=687&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D' ?>" alt="Product" class="product-img me-2 me-sm-3 flex-shrink-0 rounded" style="width: 48px; height: 48px; object-fit: cover;">
                            <div class="flex-grow-1 min-w-0">
                                <div class="fw-bold text-truncate small"><?= htmlspecialchars($product->name) ?></div>
                                <div class="text-muted-custom" style="font-size: 0.75rem;"><?= $product->total_sold ?? 0 ?> Sales</div>
                            </div>
                            <div class="text-end flex-shrink-0 ms-2">
                                <div class="fw-bold text-success">$<?= number_format($product->base_price, 2) ?></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="text-center py-5">
                        <div class="text-muted">
                            <img src="https://cdn-icons-png.flaticon.com/512/2920/2920277.png" alt="No Data" width="48" height="48" class="mb-3 opacity-50">
                            <p>Chưa có sản phẩm nào</p>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
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
                activePage: 'dashboard',
                links: {
                    dashboard: 'index.php?url=dashboard',
                    products: 'index.php?url=products',
                    categories: 'index.php?url=categories',
                    collections: 'index.php?url=collections',
                    orders: 'index.php?url=orders',
                    customers: 'index.php?url=customers',
                    reviews: 'index.php?url=reviews'
                },
                categories: [],
                categoriesTitle: 'DANH MỤC'
            },
            header: {
                title: 'Dashboard',
                breadcrumb: 'Home > Dashboard'
            }
        };
    </script>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Main JS -->
    <script src="app/views/admin/assets/js/main.js"></script>
</body>
</html>

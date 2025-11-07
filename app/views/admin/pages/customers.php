<?php
/**
 * Customers View - Pure MVC View
 * Hiển thị data từ database, không có hardcode
 */

// Biến được truyền từ Controller:
// $users - danh sách users từ database
// $stats - thống kê users
// $message - thông báo thành công
// $error - thông báo lỗi
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>JEWELLERY Admin - Customer Management</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
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
                <!-- Flash Messages -->
                <?php if (!empty($message)): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <strong>Thành công:</strong> <?= htmlspecialchars($message) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <strong>Lỗi:</strong> <?= htmlspecialchars($error) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <!-- Customer Statistics Cards -->
                <div class="row mb-4">
                    <div class="col-lg-3 col-md-6 mb-3">
                        <div class="card text-center h-100">
                            <div class="card-body">
                                <div class="d-flex align-items-center justify-content-center mb-2">
                                    <img src="https://cdn-icons-png.flaticon.com/512/1077/1077114.png" alt="Total Customers" width="32" height="32" class="me-2">
                                    <h3 class="mb-0 text-primary"><?= $stats['total'] ?? 0 ?></h3>
                                </div>
                                <h6 class="fw-bold">Tổng Khách Hàng</h6>
                                
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6 mb-3">
                        <div class="card text-center h-100">
                            <div class="card-body">
                                <div class="d-flex align-items-center justify-content-center mb-2">
                                    <img src="https://cdn-icons-png.flaticon.com/512/5610/5610944.png" alt="Active Users" width="32" height="32" class="me-2">
                                    <h3 class="mb-0 text-success"><?= $stats['active'] ?? 0 ?></h3>
                                </div>
                                <h6 class="fw-bold">Đang Hoạt Động</h6>
                                <p class="text-muted small mb-0"><?= $stats['total'] > 0 ? number_format(($stats['active'] / $stats['total']) * 100, 1) : 0 ?>% tổng khách hàng</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6 mb-3">
                        <div class="card text-center h-100">
                            <div class="card-body">
                                <div class="d-flex align-items-center justify-content-center mb-2">
                                    <img src="https://cdn-icons-png.flaticon.com/512/3063/3063822.png" alt="Orders" width="32" height="32" class="me-2">
                                    <h3 class="mb-0 text-warning"><?= $stats['with_orders'] ?? 0 ?></h3>
                                </div>
                                <h6 class="fw-bold">Đã Đặt Hàng</h6>
                                <p class="text-muted small mb-0"><?= $stats['total'] > 0 ? number_format(($stats['with_orders'] / $stats['total']) * 100, 1) : 0 ?>% khách hàng</p>
                            </div>
                        </div>
                    </div>
                    
                </div>

                <!-- Customer Management Table -->
                <div class="table-card">
                    <div class="table-header flex-column flex-md-row align-items-start align-items-md-center">
                      
                        <div class="d-flex flex-wrap gap-2 w-100 w-md-auto">
                            <!-- Filters -->
                            <div class="dropdown">
                                    <span class="d-sm-none">
                                        <img src="https://cdn-icons-png.flaticon.com/512/3031/3031293.png" alt="Filter" width="16" height="16">
                                    </span>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="#" onclick="filterCustomers('all')">Tất cả</a></li>
                                    <li><a class="dropdown-item" href="#" onclick="filterCustomers('active')">Đang hoạt động</a></li>
                                    <li><a class="dropdown-item" href="#" onclick="filterCustomers('inactive')">Không hoạt động</a></li>
                                    <li><a class="dropdown-item" href="#" onclick="filterCustomers('has-orders')">Có đơn hàng</a></li>
                                </ul>
                            </div>

                            <!-- Search -->
                            <div class="position-relative flex-grow-1" style="max-width: 100%; min-width: 150px;">
                                <input type="text" class="form-control form-control-sm" placeholder="Tìm kiếm..." style="padding-left: 35px;" id="customerSearch">
                                <img src="https://cdn-icons-png.flaticon.com/512/751/751463.png" alt="Search" width="16" height="16" class="position-absolute" style="left: 10px; top: 50%; transform: translateY(-50%);">
                            </div>

                            
                        </div>
                    </div>
                    
                    <div class="table-responsive">
                        <table class="table" id="customersTable">
                            <thead>
                                <tr>
                                    <th class="text-nowrap">Khách Hàng</th>
                                    <th class="d-none d-md-table-cell text-nowrap">Email</th>
                                    <th class="d-none d-lg-table-cell text-nowrap">SĐT</th>
                                    <th class="d-none d-xl-table-cell text-nowrap">Vai Trò</th>
                                    <th class="d-none d-md-table-cell text-nowrap">Trạng Thái</th>
                                    <th class="d-none d-lg-table-cell text-nowrap">Đơn Hàng</th>
                                    <th class="d-none d-xl-table-cell text-nowrap">Ngày TG</th>
                                    <th class="text-nowrap">Thao Tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($users)): ?>
                                    <?php foreach ($users as $user): ?>
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <img src="https://cdn-icons-png.flaticon.com/512/3135/3135715.png" alt="User" width="32" height="32" class="rounded-circle me-2 flex-shrink-0">
                                                    <div class="min-w-0">
                                                        <div class="fw-bold text-truncate"><?= htmlspecialchars($user->name ?? 'N/A') ?></div>
                                                        <div class="small text-muted d-none d-sm-block">ID: #<?= $user->user_id ?></div>
                                                        <!-- Mobile info -->
                                                        <div class="d-md-none small">
                                                            <div class="text-muted text-truncate"><?= htmlspecialchars($user->email) ?></div>
                                                            <?php if ($user->is_active): ?>
                                                                <span class="badge badge-delivered badge-custom">Hoạt động</span>
                                                            <?php else: ?>
                                                                <span class="badge badge-canceled badge-custom">Không hoạt động</span>
                                                            <?php endif; ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="d-none d-md-table-cell">
                                                <span class="text-truncate d-inline-block" style="max-width: 200px;"><?= htmlspecialchars($user->email) ?></span>
                                            </td>
                                            <td class="d-none d-lg-table-cell"><?= htmlspecialchars($user->phone ?? 'N/A') ?></td>
                                            <td class="d-none d-xl-table-cell">
                                                <?php if ($user->role_name): ?>
                                                    <span class="badge bg-primary"><?= htmlspecialchars($user->role_name) ?></span>
                                                <?php else: ?>
                                                    <span class="badge bg-secondary">Customer</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="d-none d-md-table-cell">
                                                <?php if ($user->is_active): ?>
                                                    <span class="badge badge-delivered badge-custom">Hoạt động</span>
                                                <?php else: ?>
                                                    <span class="badge badge-canceled badge-custom">Không hoạt động</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="d-none d-lg-table-cell">
                                                <div class="text-center">
                                                    <div class="fw-bold"><?= $user->order_count ?? 0 ?></div>
                                                    <div class="small text-muted">đơn hàng</div>
                                                </div>
                                            </td>
                                            <td class="d-none d-xl-table-cell">
                                                <div class="text-nowrap small"><?= date('d/m/Y', strtotime($user->created_at)) ?></div>
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm" role="group">
                                                    <button type="button" class="btn btn-outline-success btn-sm p-1" 
                                                            onclick="editCustomer(<?= $user->user_id ?>)" title="Chỉnh sửa">
                                                        <img src="https://cdn-icons-png.flaticon.com/512/1159/1159633.png" alt="Edit" width="14" height="14">
                                                    </button>
                                                    <button type="button" class="btn btn-outline-danger btn-sm p-1" 
                                                            onclick="deleteCustomer(<?= $user->user_id ?>)" title="Xóa">
                                                        <img src="https://cdn-icons-png.flaticon.com/512/3096/3096673.png" alt="Delete" width="14" height="14">
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="9" class="text-center py-5">
                                            <img src="https://cdn-icons-png.flaticon.com/512/2920/2920277.png" alt="No Data" width="64" height="64" class="mb-3 opacity-50">
                                            <p class="text-muted mb-0">Chưa có khách hàng nào</p>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Pagination -->
                <div id="pagination-container"></div>
            </main>
        </div>
    </div>

    <!-- Modal Xác Nhận Xóa -->
    <div class="modal fade" id="deleteCustomerModal" tabindex="-1" aria-labelledby="deleteCustomerModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="deleteCustomerModalLabel">
                        <i class="fas fa-exclamation-triangle me-2"></i>Xác Nhận Xóa Khách Hàng
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-3"><strong>Cảnh báo:</strong> Hành động này sẽ xóa vĩnh viễn khách hàng và không thể hoàn tác!</p>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="confirmDeleteCustomer">
                        <label class="form-check-label" for="confirmDeleteCustomer">
                            Tôi hiểu và muốn xóa khách hàng này
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="button" class="btn btn-danger" id="confirmDeleteCustomerBtn" disabled>
                        <i class="fas fa-trash me-2"></i>Xóa Khách Hàng
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Component Manager -->
    <script src="app/views/admin/components/component-manager.js"></script>
    
    <!-- Page Configuration -->
    <script>
        window.pageConfig = {
            sidebar: {
                brandName: 'JEWELLERY',
                activePage: 'customers',
                links: {
                    dashboard: '<?= BASE_URL ?>' + '/admin/index.php?url=dashboard',
                    products: '<?= BASE_URL ?>' + '/admin/index.php?url=products',
                    categories: '<?= BASE_URL ?>' + '/admin/index.php?url=categories',
                    orders: '<?= BASE_URL ?>' + '/admin/index.php?url=orders',
                    customers: '<?= BASE_URL ?>' + '/admin/index.php?url=customers',
                    collections: '<?= BASE_URL ?>' + '/admin/index.php?url=collections',
                    reviews: '<?= BASE_URL ?>' + '/admin/index.php?url=reviews',
                    media: '<?= BASE_URL ?>' + '/admin/index.php?url=media'
                }
            },
            header: {
                title: 'Customer Management',
                breadcrumb: 'Home > Customers > All Customers'
            }
        };
    </script>
    
    <!-- Customers Page Script -->
    <script src="app/views/admin/assets/js/customers.js"></script>
    
    <!-- Customer Management Functions -->
    <script>
        function viewCustomer(userId) {
            // Không còn dùng customer-details, chuyển sang edit
            window.location.href = '<?= BASE_URL ?>' + '/admin/index.php?url=customers&action=edit&id=' + userId;
        }
        
        function editCustomer(userId) {
            window.location.href = '<?= BASE_URL ?>' + '/admin/index.php?url=customers&action=edit&id=' + userId;
        }
        
        function toggleCustomerStatus(userId) {
            if (confirm('Bạn có chắc chắn muốn thay đổi trạng thái của khách hàng này không?')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '<?= BASE_URL ?>' + '/admin/index.php?url=customers&action=toggle&id=' + userId;
                document.body.appendChild(form);
                form.submit();
            }
        }
        
        let customerIdToDelete = null;

        function deleteCustomer(userId) {
            customerIdToDelete = userId;
            const modal = new bootstrap.Modal(document.getElementById('deleteCustomerModal'));
            modal.show();
        }

        // Handle checkbox change
        document.getElementById('confirmDeleteCustomer').addEventListener('change', function() {
            document.getElementById('confirmDeleteCustomerBtn').disabled = !this.checked;
        });

        // Handle confirm delete button
        document.getElementById('confirmDeleteCustomerBtn').addEventListener('click', function() {
            if (customerIdToDelete) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '<?= BASE_URL ?>' + '/admin/index.php?url=customers&action=delete&id=' + customerIdToDelete;
                document.body.appendChild(form);
                form.submit();
            }
        });

        // Reset modal when closed
        document.getElementById('deleteCustomerModal').addEventListener('hidden.bs.modal', function() {
            customerIdToDelete = null;
            document.getElementById('confirmDeleteCustomer').checked = false;
            document.getElementById('confirmDeleteCustomerBtn').disabled = true;
        });
        
        function filterCustomers(filter) {
            // TODO: Implement filtering logic
            console.log('Filter customers by:', filter);
        }
    </script>

    <!-- Bootstrap JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

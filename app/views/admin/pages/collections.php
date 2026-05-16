<?php
/**
 * Collections View - Pure MVC View
 * Hiển thị data từ database, không có hardcode
 */

// Biến được truyền từ Controller:
// $collections - danh sách collections từ database
// $stats - thống kê collections
// $message - thông báo thành công
// $error - thông báo lỗi
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>JEWELLERY Admin - Collections Management</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="app/views/admin/assets/css/variables.css">
    <link rel="stylesheet" href="app/views/admin/assets/css/main.css">
</head>
<body>
    <script>var APP_BASE_URL = '<?= BASE_URL ?>';</script>
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

                <!-- Collections Statistics Cards -->
                <div class="row mb-4">
                    <div class="col-lg-3 col-md-6 mb-3">
                        <div class="card text-center h-100">
                            <div class="card-body">
                                <div class="d-flex align-items-center justify-content-center mb-2">
                                    <img src="https://cdn-icons-png.flaticon.com/512/3208/3208676.png" alt="Total Collections" width="32" height="32" class="me-2">
                                    <h3 class="mb-0 text-primary"><?= $stats['total'] ?? 0 ?></h3>
                                </div>
                                <h6 class="fw-bold">Tổng Bộ Sưu Tập</h6>
                                <p class="text-muted small mb-0">Tất cả bộ sưu tập</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6 mb-3">
                        <div class="card text-center h-100">
                            <div class="card-body">
                                <div class="d-flex align-items-center justify-content-center mb-2">
                                    <img src="https://cdn-icons-png.flaticon.com/512/5610/5610944.png" alt="Active Collections" width="32" height="32" class="me-2">
                                    <h3 class="mb-0 text-success"><?= $stats['active'] ?? 0 ?></h3>
                                </div>
                                <h6 class="fw-bold">Đang Hoạt Động</h6>
                                <p class="text-muted small mb-0"><?= $stats['total'] > 0 ? number_format(($stats['active'] / $stats['total']) * 100, 1) : 0 ?>% tổng số</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6 mb-3">
                        <div class="card text-center h-100">
                            <div class="card-body">
                                <div class="d-flex align-items-center justify-content-center mb-2">
                                    <img src="https://cdn-icons-png.flaticon.com/512/3081/3081559.png" alt="Products" width="32" height="32" class="me-2">
                                    <h3 class="mb-0 text-warning"><?= $stats['total_products'] ?? 0 ?></h3>
                                </div>
                                <h6 class="fw-bold">Tổng Sản Phẩm</h6>
                                <p class="text-muted small mb-0">Trong các bộ sưu tập</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6 mb-3">
                        <div class="card text-center h-100">
                            <div class="card-body">
                                <div class="d-flex align-items-center justify-content-center mb-2">
                                    <img src="https://cdn-icons-png.flaticon.com/512/3208/3208676.png" alt="Best Seller" width="32" height="32" class="me-2">
                                    <h3 class="mb-0 text-info"><?= $stats['best_sellers'] ?? 0 ?></h3>
                                </div>
                                <h6 class="fw-bold">Bán Chạy Nhất</h6>
                                <p class="text-muted small mb-0">Bộ sưu tập hàng đầu</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Collections Management Table -->
                <div class="table-card">
                    <div class="table-header flex-column flex-md-row align-items-start align-items-md-center">
                        
                        <div class="d-flex flex-wrap gap-2 w-100 w-md-auto">
                            <!-- Search -->
                            <div class="position-relative flex-grow-1" style="max-width: 100%; min-width: 150px;">
                                <input type="text" class="form-control form-control-sm" placeholder="Tìm kiếm..." style="padding-left: 35px;" id="collectionSearch">
                                <img src="https://cdn-icons-png.flaticon.com/512/751/751463.png" alt="Search" width="16" height="16" class="position-absolute" style="left: 10px; top: 50%; transform: translateY(-50%);">
                            </div>
                            
                            <!-- Add Collection -->
                            <button class="btn btn-success-custom btn-sm flex-shrink-0" onclick="window.location.href='<?= BASE_URL ?>/admin/add-collection'">
                                <img src="https://cdn-icons-png.flaticon.com/512/748/748113.png" alt="Add" width="16" height="16" class="me-1 d-none d-sm-inline">
                                <span class="d-none d-sm-inline">Thêm BST</span>
                                <span class="d-sm-none">
                                    <img src="https://cdn-icons-png.flaticon.com/512/748/748113.png" alt="Add" width="16" height="16">
                                </span>
                            </button>
                            
                            
                        </div>
                    </div>
                    
                    <div class="table-responsive">
                        <table class="table" id="collectionsTable">
                            <thead>
                                <tr>
                                    <th class="d-none d-md-table-cell text-nowrap">ID</th>
                                    <th class="text-nowrap">Tên BST</th>
                                    <th class="d-none d-xl-table-cell text-nowrap">Slug</th>
                                    <th class="d-none d-lg-table-cell text-nowrap">Mô Tả</th>
                                    <th class="d-none d-md-table-cell text-nowrap">Sản Phẩm</th>
                                    <th class="d-none d-sm-table-cell text-nowrap">Trạng Thái</th>
                                    <th class="d-none d-lg-table-cell text-nowrap">Ngày Tạo</th>
                                    <th class="text-nowrap">Thao Tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($collections)): ?>
                                    <?php foreach ($collections as $collection): ?>
                                        <tr>
                                            <td class="fw-bold d-none d-md-table-cell">#<?= $collection->collection_id ?></td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                  
                                                    <div class="min-w-0">
                                                        <div class="fw-bold text-truncate"><?= htmlspecialchars($collection->collection_name) ?></div>
                                                        <div class="small text-muted d-none d-sm-block text-truncate"><?= htmlspecialchars($collection->slug) ?></div>
                                                        <!-- Mobile info -->
                                                        <div class="d-sm-none small mt-1">
                                                            <?php if ($collection->is_active): ?>
                                                                <span class="badge badge-delivered badge-custom">Hoạt động</span>
                                                            <?php else: ?>
                                                                <span class="badge badge-canceled badge-custom">Không hoạt động</span>
                                                            <?php endif; ?>
                                                            <span class="text-muted ms-2"><?= $collection->product_count ?? 0 ?> SP</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="d-none d-xl-table-cell"><code class="small"><?= htmlspecialchars($collection->slug) ?></code></td>
                                            <td class="d-none d-lg-table-cell">
                                                <span class="d-inline-block text-truncate" style="max-width: 200px;"><?= htmlspecialchars($collection->description ?? 'Không có mô tả') ?></span>
                                            </td>
                                            <td class="d-none d-md-table-cell">
                                                <div class="text-center">
                                                    <span class="fw-bold text-primary"><?= $collection->product_count ?? 0 ?></span>
                                                    <div class="small text-muted d-none d-lg-block">sản phẩm</div>
                                                </div>
                                            </td>
                                            <td class="d-none d-sm-table-cell">
                                                <?php if ($collection->is_active): ?>
                                                    <span class="badge badge-delivered badge-custom">Hoạt động</span>
                                                <?php else: ?>
                                                    <span class="badge badge-canceled badge-custom">Không hoạt động</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="d-none d-lg-table-cell">
                                                <div class="text-nowrap small"><?= date('d/m/Y', strtotime($collection->created_at)) ?></div>
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm" role="group">
                                                    <button type="button" class="btn btn-outline-success btn-sm p-1" 
                                                            onclick="editCollection(<?= $collection->collection_id ?>)" title="Chỉnh sửa">
                                                        <img src="https://cdn-icons-png.flaticon.com/512/1159/1159633.png" alt="Edit" width="14" height="14">
                                                    </button>
                                                    <button type="button" class="btn btn-outline-danger btn-sm p-1" 
                                                            onclick="deleteCollection(<?= $collection->collection_id ?>)" title="Xóa">
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
                                            <p class="text-muted mb-0">Chưa có bộ sưu tập nào</p>
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

    <!-- Component Manager -->
    <script src="app/views/admin/components/component-manager.js"></script>
    
    <!-- Page Configuration -->
    <script>
        window.pageConfig = {
            sidebar: {
                brandName: 'Trang Sức',
                activePage: 'collections',
                links: {
                    dashboard: '/admin/dashboard',
                    products: '/admin/products',
                    categories: '/admin/categories',
                    orders: '/admin/orders',
                    customers: '/admin/customers',
                    collections: '/admin/collections',
                    reviews: '/admin/reviews',
                    media: '/admin/media'
                }
            }
        };
    </script>
    
    <!-- Collections Page Script -->
    <script src="app/views/admin/assets/js/collections.js"></script>
    
    <!-- Collection Management Functions -->
    <script>
        function viewCollection(collectionId) {
            // Không còn dùng collection-details, chuyển sang edit
            window.location.href = '<?= BASE_URL ?>/admin/edit-collection?id=' + collectionId;
        }
        
        function editCollection(collectionId) {
            window.location.href = '<?= BASE_URL ?>/admin/edit-collection?id=' + collectionId;
        }
        
        function viewProducts(collectionId) {
            window.location.href = '<?= BASE_URL ?>/admin/products?collection=' + collectionId;
        }
        
        function toggleCollection(collectionId) {
            if (confirm('Bạn có chắc chắn muốn thay đổi trạng thái của bộ sưu tập này?')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '<?= BASE_URL ?>/admin/collections?action=toggle&id=' + collectionId;
                document.body.appendChild(form);
                form.submit();
            }
        }
        
        function deleteCollection(collectionId) {
            // Tạo modal Bootstrap để xác nhận
            const modalHtml = `
                <div class="modal fade" id="deleteCollectionModal" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Xác Nhận Xóa Bộ Sưu Tập</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <p><strong>⚠️ CẢNH BÁO:</strong> Bạn đang thực hiện xóa VĨNH VIỄN bộ sưu tập!</p>
                                <p>Bộ sưu tập #${collectionId} và tất cả dữ liệu liên quan sẽ bị xóa hoàn toàn khỏi hệ thống.</p>
                                <p class="text-danger">Hành động này KHÔNG THỂ hoàn tác!</p>
                                
                                <div class="form-check mt-3">
                                    <input class="form-check-input" type="checkbox" id="confirmDeleteCheckbox">
                                    <label class="form-check-label" for="confirmDeleteCheckbox">
                                        Tôi hiểu và chấp nhận xóa vĩnh viễn bộ sưu tập này
                                    </label>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                                <button type="button" class="btn btn-danger" id="confirmDeleteBtn" disabled onclick="confirmDeleteCollection(${collectionId})">
                                    Xóa Vĩnh Viễn
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            
            // Xóa modal cũ nếu có
            const oldModal = document.getElementById('deleteCollectionModal');
            if (oldModal) {
                oldModal.remove();
            }
            
            // Thêm modal mới
            document.body.insertAdjacentHTML('beforeend', modalHtml);
            
            // Enable/disable button dựa trên checkbox
            const checkbox = document.getElementById('confirmDeleteCheckbox');
            const confirmBtn = document.getElementById('confirmDeleteBtn');
            checkbox.addEventListener('change', function() {
                confirmBtn.disabled = !this.checked;
            });
            
            // Hiển thị modal
            const modal = new bootstrap.Modal(document.getElementById('deleteCollectionModal'));
            modal.show();
        }
        
        function confirmDeleteCollection(collectionId) {
            const form = document.createElement('form');
            form.method = 'GET';
            form.action = 'index.php';
            
            const urlInput = document.createElement('input');
            urlInput.type = 'hidden';
            urlInput.name = 'url';
            urlInput.value = 'collections';
            
            const actionInput = document.createElement('input');
            actionInput.type = 'hidden';
            actionInput.name = 'action';
            actionInput.value = 'delete';
            
            const idInput = document.createElement('input');
            idInput.type = 'hidden';
            idInput.name = 'id';
            idInput.value = collectionId;
            
            form.appendChild(urlInput);
            form.appendChild(actionInput);
            form.appendChild(idInput);
            document.body.appendChild(form);
            form.submit();
        }

        // Search functionality
        document.getElementById('collectionSearch').addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase();
            const rows = document.querySelectorAll('#collectionsTable tbody tr');
            
            rows.forEach(row => {
                const collectionName = row.querySelector('.fw-bold')?.textContent.toLowerCase() || '';
                if (collectionName.includes(searchTerm)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });

        // Select all checkbox
        document.getElementById('selectAll').addEventListener('change', function() {
            const checkboxes = document.querySelectorAll('.row-checkbox');
            checkboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
        });
    </script>

    <!-- Bootstrap JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>


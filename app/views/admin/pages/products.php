<?php
/**
 * Products View - Pure MVC View
 * Hiển thị data từ database, không có hardcode
 */

// Biến được truyền từ Controller:
// $products - danh sách products từ database
// $categories - danh sách categories từ database
// $collections - danh sách collections từ database
// $materials - danh sách materials có sẵn
// $materialStats - thống kê theo material
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>JEWELRY Admin - All Products</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="app/views/admin/assets/css/variables.css?v=3.0">
    <link rel="stylesheet" href="app/views/admin/assets/css/main.css?v=3.0">
    
    <style>
        /* Force responsive - inline CSS for products page */
        @media (max-width: 991px) {
            .mobile-menu-toggle {
                display: block !important;
                background: none;
                border: none;
                font-size: 1.5rem;
                cursor: pointer;
                padding: 0.5rem;
            }
            
            .sidebar {
                position: fixed !important;
                transform: translateX(-100%) !important;
                transition: transform 0.3s ease;
                z-index: 1050 !important;
                width: 250px !important;
                left: 0 !important;
                top: 0 !important;
                height: 100vh !important;
            }
            
            .sidebar.show {
                transform: translateX(0) !important;
            }
            
            .main-content {
                margin-left: 0 !important;
                width: 100% !important;
            }
        }
    </style>
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
                <?php if (!empty($flashMessage)): ?>
                    <?= $flashMessage ?>
                <?php endif; ?>
                
                <!-- Product Management Table -->
                <div class="table-card">
                    <div class="table-header flex-column flex-md-row align-items-start align-items-md-center">
                       
                        <div class="d-flex flex-wrap gap-2 w-100 w-md-auto">
                            <!-- Filters -->
                            <div class="dropdown">
                                    <span class="d-sm-none">
                                        <img src="https://cdn-icons-png.flaticon.com/512/3031/3031293.png" alt="Filter" width="16" height="16">
                                    </span>
                                
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="#" onclick="filterProducts('all')">Tất cả</a></li>
                                    <li><a class="dropdown-item" href="#" onclick="filterProducts('active')">Đang bán</a></li>
                                    <li><a class="dropdown-item" href="#" onclick="filterProducts('inactive')">Ngừng bán</a></li>
                                    <li><a class="dropdown-item" href="#" onclick="filterProducts('best-seller')">Bán chạy</a></li>
                                </ul>
                            </div>
                            
                            <!-- Search -->
                            <div class="position-relative flex-grow-1" style="max-width: 100%; min-width: 150px;">
                                <input type="text" class="form-control form-control-sm" placeholder="Tìm kiếm..." style="padding-left: 35px;" id="productSearch">
                                <img src="https://cdn-icons-png.flaticon.com/512/751/751463.png" alt="Search" width="16" height="16" class="position-absolute" style="left: 10px; top: 50%; transform: translateY(-50%);">
                            </div>
                            
                            <!-- Add Product -->
                            <button class="btn btn-success-custom btn-sm flex-shrink-0" onclick="window.location.href='index.php?url=add-product'">
                                <img src="https://cdn-icons-png.flaticon.com/512/748/748113.png" alt="Add" width="16" height="16" class="me-1 d-none d-sm-inline">
                                <span class="d-none d-sm-inline">Thêm Sản Phẩm</span>
                                <span class="d-sm-none">
                                    <img src="https://cdn-icons-png.flaticon.com/512/748/748113.png" alt="Add" width="16" height="16">
                                </span>
                            </button>
                            
                          
                        </div>
                    </div>
                    
                    <!-- Desktop: Table View -->
                    <div class="table-responsive d-none d-md-block">
                        <table class="table" id="productsTable">
                            <thead>
                                <tr>
                                    <th class="text-nowrap">Sản Phẩm</th>
                                    <th class="d-none d-xl-table-cell text-nowrap">Chất Liệu</th>
                                    <th class="d-none d-lg-table-cell text-nowrap">Danh Mục</th>
                                    <th class="text-nowrap">Giá</th>
                                    <th class="d-none d-lg-table-cell text-nowrap">SKU</th>
                                    <th class="text-nowrap">Trạng Thái</th>
                                    <th class="d-none d-xl-table-cell text-nowrap">Ngày Tạo</th>
                                    <th width="100" class="text-nowrap">Thao Tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($products)): ?>
                                    <?php foreach ($products as $product): ?>
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <?php 
                                                    // Load ảnh từ database (images + image_usages)
                                                    $imageSrc = 'https://via.placeholder.com/40x40?text=No+Image';
                                                    
                                                    if (!empty($product->primary_image) && isset($product->primary_image->file_path)) {
                                                        // Ưu tiên: primary_image từ image_usages
                                                        $imagePath = $product->primary_image->file_path;
                                                        
                                                        // Kiểm tra nếu là URL đầy đủ (http/https) thì dùng trực tiếp
                                                        if (preg_match('/^https?:\/\//', $imagePath)) {
                                                            $imageSrc = htmlspecialchars($imagePath);
                                                        } else {
                                                            // Nếu là đường dẫn tương đối, thêm BASE_URL
                                                            $imageSrc = htmlspecialchars('/Ecom_website/' . ltrim($imagePath, '/'));
                                                        }
                                                    } elseif (!empty($product->main_image)) {
                                                        // Fallback: main_image column
                                                        $imagePath = $product->main_image;
                                                        
                                                        // Kiểm tra nếu là URL đầy đủ (http/https) thì dùng trực tiếp
                                                        if (preg_match('/^https?:\/\//', $imagePath)) {
                                                            $imageSrc = htmlspecialchars($imagePath);
                                                        } else {
                                                            // Nếu là đường dẫn tương đối, thêm BASE_URL
                                                            $imageSrc = htmlspecialchars('/Ecom_website/' . ltrim($imagePath, '/'));
                                                        }
                                                    }
                                                    ?>
                                                    <img src="<?= $imageSrc ?>" 
                                                         alt="Product" 
                                                         width="40" 
                                                         height="40" 
                                                         class="rounded me-2 flex-shrink-0"
                                                         onerror="this.src='https://via.placeholder.com/40x40?text=No+Image'">
                                                    <div class="flex-grow-1 min-w-0">
                                                        <div class="fw-bold text-truncate"><?= htmlspecialchars($product->name) ?></div>
                                                        <div class="small text-muted d-none d-sm-block">SKU: #<?= htmlspecialchars($product->sku ?? 'N/A') ?></div>
                                                        <!-- Mobile info -->
                                                        <div class="d-md-none small">
                                                            <span class="badge bg-warning text-dark me-1"><?= htmlspecialchars(ucfirst($product->material ?? 'gold')) ?></span>
                                                            <?php if ($product->is_active): ?>
                                                                <span class="badge badge-delivered badge-custom">Đang bán</span>
                                                            <?php else: ?>
                                                                <span class="badge badge-canceled badge-custom">Ngừng bán</span>
                                                            <?php endif; ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="d-none d-xl-table-cell">
                                                <span class="badge bg-warning text-dark">
                                                    <?= htmlspecialchars(ucfirst($product->material ?? 'gold')) ?>
                                                </span>
                                            </td>
                                            <td class="d-none d-lg-table-cell">
                                                <span class="badge bg-info text-dark">
                                                    <?= htmlspecialchars($product->category_name ?? 'Chưa phân loại') ?>
                                                </span>
                                            </td>
                                            <td class="fw-bold text-success text-nowrap">
                                                <span class="d-none d-sm-inline"><?= number_format($product->base_price, 0, ',', '.') ?> VND</span>
                                                <span class="d-sm-none"><?= number_format($product->base_price, 0, ',', '.') ?>đ</span>
                                            </td>
                                            <td class="d-none d-lg-table-cell">
                                                <code class="small"><?= htmlspecialchars($product->sku ?? 'N/A') ?></code>
                                            </td>
                                            <td>
                                                <?php if ($product->is_active): ?>
                                                    <span class="badge badge-delivered badge-custom">Đang bán</span>
                                                <?php else: ?>
                                                    <span class="badge badge-canceled badge-custom">Ngừng bán</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="d-none d-xl-table-cell">
                                                <div class="text-nowrap small">
                                                    <?= date('d/m/Y', strtotime($product->created_at)) ?>
                                                    <br>
                                                    <small class="text-muted"><?= date('H:i', strtotime($product->created_at)) ?></small>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm" role="group">
                                                    <button type="button" class="btn btn-outline-success btn-sm p-1" 
                                                            onclick="editProduct(<?= $product->product_id ?>)" title="Chỉnh sửa">
                                                        <img src="https://cdn-icons-png.flaticon.com/512/1159/1159633.png" alt="Edit" width="14" height="14">
                                                    </button>
                                                    <button type="button" class="btn btn-outline-danger btn-sm p-1" 
                                                            onclick="deleteProduct(<?= $product->product_id ?>)" title="Xóa">
                                                        <img src="https://cdn-icons-png.flaticon.com/512/3096/3096673.png" alt="Delete" width="14" height="14">
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="9" class="text-center py-4">
                                            <div class="text-muted">
                                                <img src="https://cdn-icons-png.flaticon.com/512/2920/2920277.png" alt="No Data" width="48" height="48" class="mb-3 opacity-50">
                                                <p>Chưa có sản phẩm nào</p>
                                                <button class="btn btn-primary" onclick="window.location.href='index.php?url=add-product'">
                                                    Thêm sản phẩm đầu tiên
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Mobile: Card View -->
                    <div class="d-md-none">
                        <?php if (!empty($products)): ?>
                            <?php foreach ($products as $product): ?>
                                <div class="card mb-3">
                                    <div class="card-body p-3">
                                        <div class="d-flex align-items-start mb-2">
                                            <?php 
                                            // Load ảnh từ database
                                            $imageSrc = 'https://via.placeholder.com/60x60?text=No+Image';
                                            
                                            if (!empty($product->primary_image) && isset($product->primary_image->file_path)) {
                                                $imagePath = $product->primary_image->file_path;
                                                if (preg_match('/^https?:\/\//', $imagePath)) {
                                                    $imageSrc = htmlspecialchars($imagePath);
                                                } else {
                                                    $imageSrc = htmlspecialchars('/Ecom_website/' . ltrim($imagePath, '/'));
                                                }
                                            } elseif (!empty($product->main_image)) {
                                                $imagePath = $product->main_image;
                                                if (preg_match('/^https?:\/\//', $imagePath)) {
                                                    $imageSrc = htmlspecialchars($imagePath);
                                                } else {
                                                    $imageSrc = htmlspecialchars('/Ecom_website/' . ltrim($imagePath, '/'));
                                                }
                                            }
                                            ?>
                                            <img src="<?= $imageSrc ?>" 
                                                 alt="Product" 
                                                 width="60" 
                                                 height="60" 
                                                 class="rounded me-3 flex-shrink-0"
                                                 onerror="this.src='https://via.placeholder.com/60x60?text=No+Image'">
                                            <div class="flex-grow-1 min-w-0">
                                                <h6 class="fw-bold mb-1"><?= htmlspecialchars($product->name) ?></h6>
                                                <div class="small text-muted mb-2">SKU: #<?= htmlspecialchars($product->sku ?? 'N/A') ?></div>
                                                <div class="mb-2">
                                                    <span class="badge bg-warning text-dark me-1"><?= htmlspecialchars(ucfirst($product->material ?? 'gold')) ?></span>
                                                    <?php if ($product->is_active): ?>
                                                        <span class="badge badge-delivered badge-custom">Đang bán</span>
                                                    <?php else: ?>
                                                        <span class="badge badge-canceled badge-custom">Ngừng bán</span>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center pt-2 border-top">
                                            <div>
                                                <div class="fw-bold text-success"><?= number_format($product->base_price, 0, ',', '.') ?>đ</div>
                                                <small class="text-muted"><?= date('d/m/Y', strtotime($product->created_at)) ?></small>
                                            </div>
                                            <div class="btn-group btn-group-sm">
                                                <button type="button" class="btn btn-outline-success btn-sm" 
                                                        onclick="editProduct(<?= $product->product_id ?>)" title="Sửa">
                                                    <img src="https://cdn-icons-png.flaticon.com/512/1159/1159633.png" alt="Edit" width="16" height="16">
                                                </button>
                                                <button type="button" class="btn btn-outline-danger btn-sm" 
                                                        onclick="deleteProduct(<?= $product->product_id ?>)" title="Xóa">
                                                    <img src="https://cdn-icons-png.flaticon.com/512/3096/3096673.png" alt="Delete" width="16" height="16">
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="text-center py-5">
                                <img src="https://cdn-icons-png.flaticon.com/512/2920/2920277.png" alt="No Data" width="48" height="48" class="mb-3 opacity-50">
                                <p class="text-muted">Chưa có sản phẩm nào</p>
                                <button class="btn btn-primary" onclick="window.location.href='index.php?url=add-product'">
                                    Thêm sản phẩm đầu tiên
                                </button>
                            </div>
                        <?php endif; ?>
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
        // Prepare categories data
        <?php
        $categoriesData = [];
        if (!empty($categories)) {
            foreach ($categories as $category) {
                $categoriesData[] = [
                    'name' => $category->name,
                    'count' => $category->product_count ?? 0
                ];
            }
        } else {
            $categoriesData = [
                ['name' => 'Vòng cổ', 'count' => 0],
                ['name' => 'Vòng tay', 'count' => 0],
                ['name' => 'Bông tai', 'count' => 0],
                ['name' => 'Nhẫn', 'count' => 0],
                ['name' => 'Lắc chân', 'count' => 0]
            ];
        }
        ?>
        
        window.pageConfig = {
            sidebar: {
                brandName: 'Trang Sức',
                activePage: 'products',
                links: {
                    dashboard: 'index.php?url=dashboard',
                    products: 'index.php?url=products',
                    categories: 'index.php?url=categories',
                    collections: 'index.php?url=collections',
                    orders: 'index.php?url=orders',
                    customers: 'index.php?url=customers',
                    reviews: 'index.php?url=reviews'
                },
                categories: <?= json_encode($categoriesData) ?>,
                categoriesTitle: 'DANH MỤC'
            },
            header: {
                title: 'Sản Phẩm',
                breadcrumb: 'Home > Sản Phẩm > Tất cả sản phẩm'
            }
        };

        // Product management functions
        function viewProduct(productId) {
            window.location.href = 'index.php?url=product-details&id=' + productId;
        }

        function editProduct(productId) {
            // Mở form edit (tái sử dụng add-product.php)
            window.location.href = 'index.php?url=edit-product&id=' + productId;
        }

        function deleteProduct(productId) {
            // Tạo modal xác nhận xóa
            const modalHtml = `
                <div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header bg-danger text-white">
                                <h5 class="modal-title">
                                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                                    Xác Nhận Xóa Vĩnh Viễn
                                </h5>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <div class="form-check mt-3">
                                    <input class="form-check-input" type="checkbox" id="confirmDelete">
                                    <label class="form-check-label text-danger" for="confirmDelete">
                                        Tôi hiểu và muốn xóa vĩnh viễn sản phẩm này
                                    </label>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy Bỏ</button>
                                <button type="button" class="btn btn-danger" id="confirmDeleteBtn" disabled>
                                    <i class="bi bi-trash-fill me-1"></i>
                                    Xóa Vĩnh Viễn
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            
            // Xóa modal cũ nếu có
            const oldModal = document.getElementById('deleteModal');
            if (oldModal) oldModal.remove();
            
            // Thêm modal vào body
            document.body.insertAdjacentHTML('beforeend', modalHtml);
            
            // Khởi tạo modal
            const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
            modal.show();
            
            // Enable/disable nút xóa khi checkbox thay đổi
            document.getElementById('confirmDelete').addEventListener('change', function() {
                document.getElementById('confirmDeleteBtn').disabled = !this.checked;
            });
            
            // Xử lý khi nhấn nút xóa
            document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
                modal.hide();
                window.location.href = 'index.php?url=products&action=hardDelete&id=' + productId;
            });
        }

        function filterProducts(filter) {
            console.log('Filtering products by:', filter);
            // Implement filtering logic here
        }

        // Search functionality
        document.getElementById('productSearch').addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase();
            const rows = document.querySelectorAll('#productsTable tbody tr');
            
            rows.forEach(row => {
                const productName = row.querySelector('.fw-bold')?.textContent.toLowerCase() || '';
                if (productName.includes(searchTerm)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });

        // Select all checkbox functionality
        document.getElementById('selectAll').addEventListener('change', function() {
            const checkboxes = document.querySelectorAll('.row-checkbox');
            checkboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
        });

        // Initialize pagination
        document.addEventListener('DOMContentLoaded', function() {
            const paginationContainer = document.getElementById('pagination-container');
            if (paginationContainer && window.ComponentManager) {
                window.ComponentManager.renderPagination(paginationContainer, {
                    ariaLabel: 'Products pagination'
                });
            }
        });
    </script>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Main JS -->
    <script src="app/views/admin/assets/js/main.js"></script>
    
    <!-- Products JS -->
    <script src="app/views/admin/assets/js/products.js"></script>
</body>
</html>
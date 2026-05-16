<?php
/**
 * Edit Customer View - Pure MVC View
 * CHỈ DÙNG CHO CHỈNH SỬA KHÁCH HÀNG (Admin không được thêm khách hàng mới)
 * 
 * Biến được truyền từ Controller:
 * - $isEdit: true (luôn là edit mode)
 * - $customer: Dữ liệu customer
 */

// Kiểm tra edit mode - CHỈ CHO PHÉP EDIT
$isEdit = true;
$customer = $customer ?? null;

// Nếu không có customer data, redirect về customers list
if (!$customer) {
    header('Location: ' . BASE_URL . '/admin/customers');
    exit;
}

// Xác định action URL - CHỈ UPDATE
$formAction = BASE_URL . "/admin/customers&action=update&id={$customer->user_id}";
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chỉnh Sửa Khách Hàng - Trang Sức Admin</title>
    
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
                <?php if (isset($_SESSION['success'])): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?= htmlspecialchars($_SESSION['success']) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    <?php unset($_SESSION['success']); ?>
                <?php endif; ?>
                <?php if (isset($_SESSION['error'])): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?= htmlspecialchars($_SESSION['error']) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    <?php unset($_SESSION['error']); ?>
                <?php endif; ?>

                <div class="row">
                    <!-- Customer Form - Full Width -->
                    <div class="col-12 mb-4">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-4">
                                    <img src="https://cdn-icons-png.flaticon.com/512/1077/1077114.png" alt="Customer" width="32" height="32" class="me-3">
                                    <h4 class="fw-bold mb-0">Chỉnh Sửa Khách Hàng</h4>
                                </div>

                                <form id="customerForm" method="POST" action="<?= $formAction ?>">
                                    <input type="hidden" name="user_id" value="<?= $customer->user_id ?>">
                                    <!-- Profile Image -->
                                    <div class="row">
                                        <div class="col-12 mb-4">
                                            <h6 class="fw-bold border-bottom pb-2 mb-3">Ảnh Đại Diện</h6>
                                            <div class="text-center">
                                                <img id="profileImagePreview" src="https://cdn-icons-png.flaticon.com/512/3135/3135715.png" alt="Profile" class="rounded-circle mb-3" style="width: 120px; height: 120px; object-fit: cover; border: 3px solid #dee2e6;">
                                                <div>
                                                    <div class="border border-2 border-dashed rounded p-3 d-inline-block" style="cursor: pointer;" onclick="document.getElementById('profileImageInput').click()">
                                                        <input type="file" id="profileImageInput" accept="image/*" style="display: none;">
                                                        <img src="https://cdn-icons-png.flaticon.com/512/1160/1160358.png" alt="Upload" width="24" height="24" class="me-2">
                                                        <span class="small">Upload ảnh profile</span>
                                                    </div>
                                                    <p class="text-muted small mt-2 mb-0">JPG, PNG (Max 2MB) - Kích thước đề xuất: 400x400px</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Basic Information -->
                                    <div class="row">
                                        <div class="col-md-12 mb-4">
                                            <h6 class="fw-bold border-bottom pb-2 mb-3">Thông Tin Cá Nhân</h6>
                                        </div>
                                        
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="customerName" class="form-label">Họ và Tên <span class="text-danger">*</span></label>
                                                <input type="text" 
                                                       class="form-control" 
                                                       id="customerName" 
                                                       name="name" 
                                                       placeholder="Nhập họ và tên"
                                                       value="<?= htmlspecialchars($customer->name ?? '') ?>" 
                                                       required>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="customerPhone" class="form-label">Số Điện Thoại <span class="text-danger">*</span></label>
                                                <input type="tel" 
                                                       class="form-control" 
                                                       id="customerPhone" 
                                                       name="phone" 
                                                       placeholder="0901234567"
                                                       value="<?= htmlspecialchars($customer->phone ?? '') ?>" 
                                                       required>
                                            </div>
                                        </div>

                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="customerEmail" class="form-label">Email <span class="text-danger">*</span></label>
                                                <input type="email" 
                                                       class="form-control" 
                                                       id="customerEmail" 
                                                       name="email" 
                                                       placeholder="example@gmail.com"
                                                       value="<?= htmlspecialchars($customer->email ?? '') ?>" 
                                                       readonly>
                                                <small class="text-muted">Email không thể thay đổi</small>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Account Information -->
                                    <div class="row">
                                        <div class="col-md-12 mb-4">
                                            <h6 class="fw-bold border-bottom pb-2 mb-3">Thông Tin Tài Khoản</h6>
                                        </div>
                                        
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="customerRole" class="form-label">Vai Trò</label>
                                                <select class="form-control" id="customerRole" name="role_id">
                                                    <option value="">Chọn vai trò</option>
                                                    <option value="1" <?= ($customer->role_id ?? '') == '1' ? 'selected' : '' ?>>Admin</option>
                                                    <option value="2" <?= ($customer->role_id ?? '') == '2' ? 'selected' : '' ?>>Customer</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="customerPassword" class="form-label">Mật Khẩu</label>
                                                <div class="position-relative">
                                                    <input type="password" 
                                                           class="form-control" 
                                                           id="customerPassword" 
                                                           name="password" 
                                                           placeholder="Để trống nếu không đổi">
                                                    <button type="button" class="btn btn-sm position-absolute end-0 top-50 translate-middle-y me-2" onclick="togglePassword('customerPassword')">
                                                        <img src="https://cdn-icons-png.flaticon.com/512/709/709612.png" alt="Show" width="16" height="16">
                                                    </button>
                                                </div>
                                                <small class="text-muted">Để trống nếu không muốn thay đổi mật khẩu</small>
                                            </div>
                                        </div>

                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <div class="form-check">
                                                    <input class="form-check-input" 
                                                           type="checkbox" 
                                                           id="isActive" 
                                                           name="is_active" 
                                                           value="1"
                                                           <?= ($customer->is_active ?? 1) ? 'checked' : '' ?>>
                                                    <label class="form-check-label fw-bold" for="isActive">
                                                        Kích Hoạt Tài Khoản
                                                    </label>
                                                    <div class="form-text">Tài khoản sẽ có thể đăng nhập ngay lập tức</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Address Information -->
                                    <div class="row">
                                        <div class="col-md-12 mb-4">
                                            <h6 class="fw-bold border-bottom pb-2 mb-3">Địa Chỉ Mặc Định</h6>
                                        </div>
                                        
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="addressStreet" class="form-label">Địa Chỉ</label>
                                                <textarea class="form-control" 
                                                          id="addressStreet" 
                                                          name="address_street" 
                                                          rows="2" 
                                                          placeholder="Số nhà, tên đường..."><?= htmlspecialchars($customer->address_street ?? '') ?></textarea>
                                            </div>
                                        </div>

                                        <div class="col-md-6 col-12">
                                            <div class="form-group">
                                                <label for="addressCountry" class="form-label">Quốc gia</label>
                                                <input type="text" 
                                                       class="form-control" 
                                                       id="addressCountry" 
                                                       name="country"
                                                       value="Việt Nam" 
                                                       placeholder="Việt Nam">
                                            </div>
                                        </div>

                                        <div class="col-md-6 col-12">
                                            <div class="form-group">
                                                <label for="addressProvince" class="form-label">Tỉnh/Thành phố</label>
                                                <input type="text" 
                                                       class="form-control" 
                                                       id="addressProvince" 
                                                       name="province"
                                                       value="<?= htmlspecialchars($customer->province ?? '') ?>"
                                                       placeholder="Nhập tỉnh/thành phố">
                                            </div>
                                        </div>

                                        <div class="col-md-6 col-12">
                                            <div class="form-group">
                                                <label for="addressWard" class="form-label">Phường/Xã</label>
                                                <input type="text" 
                                                       class="form-control" 
                                                       id="addressWard"
                                                       name="ward"
                                                       value="<?= htmlspecialchars($customer->ward ?? '') ?>"
                                                       placeholder="Nhập phường/xã">
                                            </div>
                                        </div>

                                        <div class="col-md-6 col-12">
                                            <div class="form-group">
                                                <label for="postalCode" class="form-label">Mã bưu điện</label>
                                                <input type="text" 
                                                       class="form-control" 
                                                       id="postalCode"
                                                       name="postal_code"
                                                       value="<?= htmlspecialchars($customer->postal_code ?? '') ?>"
                                                       placeholder="Nhập mã bưu điện (tùy chọn)">
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Action Buttons -->
                                    <div class="d-flex gap-2 mt-4">
                                        <button type="submit" class="btn btn-success-custom btn-custom px-4">
                                            <img src="https://cdn-icons-png.flaticon.com/512/5610/5610944.png" alt="Save" width="16" height="16" class="me-1">
                                            CẬP NHẬT
                                        </button>
                                        <button type="button" class="btn btn-outline-secondary btn-custom px-4" onclick="cancelAdd()">
                                            <img src="https://cdn-icons-png.flaticon.com/512/189/189665.png" alt="Cancel" width="16" height="16" class="me-1">
                                            HỦY BỎ
                                        </button>
                                    </div>
                                </form>
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
                activePage: 'customers',
                links: {
                    dashboard: '../index.html',
                    products: 'products.html',
                    orders: 'orders.html',
                    customers: 'customers.html',
                    'customer-roles': 'customer-roles.html'
                },
                categories: [
                    { name: 'Vòng cổ', count: 12 },
                    { name: 'Vòng tay', count: 10 },
                    { name: 'Bông tai', count: 13 },
                    { name: 'Nhẫn', count: 4 },
                    { name: 'Lắc chân', count: 6 }
                ],
                categoriesTitle: 'DANH MỤC'
            },
            header: {
                title: 'Add New Customer',
                breadcrumb: 'Home > Customers > Add New Customer'
            }
        };
    </script>
    
    <!-- Customer Form Scripts -->
    <script>
        // Toggle password visibility
        function togglePassword(inputId) {
            const input = document.getElementById(inputId);
            input.type = input.type === 'password' ? 'text' : 'password';
        }

        // Preview profile image
        document.getElementById('profileImageInput')?.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(event) {
                    document.getElementById('profileImagePreview').src = event.target.result;
                };
                reader.readAsDataURL(file);
            }
        });
    </script>
    
    <!-- Add Customer Page Script -->
    <script src="app/views/admin/assets/js/add-customer.js"></script>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="app/views/admin/assets/js/main.js"></script>
</body>
</html>

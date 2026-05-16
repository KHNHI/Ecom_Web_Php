<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title ?? 'Thông tin cá nhân'; ?> - Jewelry Store</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <html xmlns:th="http://www.thymeleaf.org">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link href="<?= asset('css/css.css?v=' . time()) ?>" rel="stylesheet">  
    
    <style>
        :root {
            --gold: #d4af37;
            --dark-gold: #b8941f;
            --light-gold: #f0e68c;
            --cream: #f8f6f0;
            --dark-brown: #3a2f28;
            --light-gray: #f5f5f5;
        }
        
        .profile-sidebar {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 30px 25px;
            box-shadow: 0 5px 25px rgba(212, 175, 55, 0.2);
            border: 1px solid rgba(212, 175, 55, 0.1);
        }
        
        .profile-avatar {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid var(--gold);
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 5px 15px rgba(212, 175, 55, 0.3);
        }
        
        .profile-avatar:hover {
            transform: scale(1.05);
            border-color: var(--dark-gold);
        }
        
        .profile-content {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 5px 25px rgba(212, 175, 55, 0.2);
            border: 1px solid rgba(212, 175, 55, 0.1);
        }
        
        .nav-pills .nav-link {
            border-radius: 10px;
            margin-bottom: 10px;
            transition: all 0.3s ease;
            color: var(--dark-brown);
            font-weight: 500;
        }
        
        .nav-pills .nav-link:hover {
            background-color: var(--cream);
            color: var(--gold);
        }
        
        .nav-pills .nav-link.active {
            background: var(--gold);
            color: white;
        }
        
        .nav-pills .nav-link.active:hover {
            background: var(--dark-gold);
            color: white;
        }
        
        .form-control {
            border-radius: 10px;
            border: 2px solid #e9ecef;
            padding: 12px 15px;
            transition: all 0.3s ease;
            background: rgba(255, 255, 255, 0.9);
        }
        
        .form-control:focus {
            border-color: var(--gold);
            box-shadow: 0 0 0 0.2rem rgba(212, 175, 55, 0.25);
            background: white;
        }
        
        .btn-primary {
            background: var(--gold);
            border: none;
            border-radius: 10px;
            padding: 12px 30px;
            font-weight: 600;
            transition: all 0.3s ease;
            color: white;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .btn-primary:hover {
            background: var(--dark-gold);
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(212, 175, 55, 0.3);
        }
        
        .alert {
            border-radius: 10px;
            border: none;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .upload-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(212, 175, 55, 0.7);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            transition: opacity 0.3s ease;
            cursor: pointer;
        }
        
        .avatar-container:hover .upload-overlay {
            opacity: 1;
        }
        
        .badge.bg-success {
            background-color: var(--gold) !important;
        }
        
        .text-primary {
            color: var(--gold) !important;
        }
        
        h5.fw-bold {
            color: var(--dark-brown);
        }
        
        .form-label {
            color: var(--dark-brown);
        }
        
        select.form-control {
            background-image: linear-gradient(45deg, transparent 50%, var(--dark-brown) 50%), 
                            linear-gradient(135deg, var(--dark-brown) 50%, transparent 50%);
            background-position: calc(100% - 20px) calc(1em + 2px), 
                               calc(100% - 15px) calc(1em + 2px);
            background-size: 5px 5px, 5px 5px;
            background-repeat: no-repeat;
            appearance: none;
        }
        
        select.form-control:focus {
            background-image: linear-gradient(45deg, var(--gold) 50%, transparent 50%), 
                            linear-gradient(135deg, transparent 50%, var(--gold) 50%);
        }
        
        body {
            background: linear-gradient(rgba(255, 255, 255, 0.5), rgba(255, 255, 255, 0.7)),
                        url("https://images.unsplash.com/photo-1608042314453-ae338d80c427") center/cover fixed;
        }
        
        .wishlist-card {
            border: none;
            border-radius: 15px;
            overflow: hidden;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
        }
        
        .wishlist-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.15);
        }
        
        .wishlist-card img {
            height: 200px;
            object-fit: cover;
        }
        
        .wishlist-card .card-body {
            padding: 20px;
        }
        
        .price {
            color: #667eea;
            font-weight: bold;
            font-size: 1.1rem;
        }
        
        .btn-remove-wishlist {
            position: absolute;
            top: 10px;
            right: 10px;
            background: rgba(255,255,255,0.9);
            border: none;
            border-radius: 50%;
            width: 35px;
            height: 35px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #dc3545;
            transition: all 0.3s ease;
        }
        
        .btn-remove-wishlist:hover {
            background: #dc3545;
            color: white;
        }
    </style>
</head>
<body class="bg-light">
    
    <!-- Include Header -->
    <?php include __DIR__ . '/../components/header.php'; ?>
    
    <div class="container mt-5 pt-4">
        <div class="row">
            <!-- Profile Sidebar -->
            <div class="col-lg-4">
                <div class="profile-sidebar text-center">
                    <!-- Avatar -->
                    <div class="avatar-container position-relative d-inline-block mb-4">
                        <img id="profileAvatar" 
                             src="<?php echo !empty($user->avatar) ? $user->avatar : 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&background=667eea&color=fff&size=120'; ?>" 
                             alt="Avatar" 
                             class="profile-avatar">
                        <div class="upload-overlay" onclick="document.getElementById('avatarInput').click()">
                            <i class="fas fa-camera fa-2x text-white"></i>
                        </div>
                        <input type="file" id="avatarInput" accept="image/*" style="display: none;">
                    </div>
                    
                    <!-- User Info -->
                    <h4 class="fw-bold mb-2"><?php echo htmlspecialchars($user->name); ?></h4>
                    <p class="text-muted mb-3"><?php echo htmlspecialchars($user->email); ?></p>
                    <span class="badge bg-success mb-4">
                        <i class="fas fa-check-circle me-1"></i>
                        Đã xác thực
                    </span>
                    
                    <!-- Navigation Pills -->
                    <ul class="nav nav-pills flex-column" id="profileTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active w-100 text-start" id="info-tab" data-bs-toggle="pill" data-bs-target="#info" type="button" role="tab">
                                <i class="fas fa-user me-2"></i>Thông tin cá nhân
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link w-100 text-start" id="password-tab" data-bs-toggle="pill" data-bs-target="#password" type="button" role="tab">
                                <i class="fas fa-key me-2"></i>Đổi mật khẩu
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link w-100 text-start" id="wishlist-tab" data-bs-toggle="pill" data-bs-target="#wishlist" type="button" role="tab">
                                <i class="fas fa-heart me-2"></i>Danh sách yêu thích
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link w-100 text-start" id="orders-tab" data-bs-toggle="pill" data-bs-target="#orders" type="button" role="tab">
                                <i class="fas fa-shopping-bag me-2"></i>Đơn hàng của tôi
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link w-100 text-start" id="settings-tab" data-bs-toggle="pill" data-bs-target="#settings" type="button" role="tab">
                                <i class="fas fa-cog me-2"></i>Cài đặt
                            </button>
                        </li>
                    </ul>
                </div>
            </div>
            
            <!-- Profile Content -->
            <div class="col-lg-8">
                <div class="profile-content">
                    <div class="tab-content" id="profileTabContent">
                        <!-- Personal Info Tab -->
                        <div class="tab-pane fade show active" id="info" role="tabpanel">
                            <h5 class="fw-bold mb-4">
                                <i class="fas fa-user text-primary me-2"></i>
                                Thông tin cá nhân
                            </h5>
                            
                            <div id="alertContainer"></div>
                            
                            <form id="profileForm">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-semibold">Họ và tên *</label>
                                        <input type="text" class="form-control" name="name" value="<?php echo htmlspecialchars($user->name); ?>" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-semibold">Email</label>
                                        <input type="email" class="form-control" value="<?php echo htmlspecialchars($user->email); ?>" readonly>
                                        <small class="text-muted">Email không thể thay đổi</small>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-semibold">Số điện thoại</label>
                                        <input type="tel" class="form-control" name="phone" value="<?php echo htmlspecialchars($user->phone ?? ''); ?>">
                                        <!-- Debug: <?php echo "DEBUG: phone = '" . ($user->phone ?? 'NULL') . "'"; ?> -->
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-semibold">Ngày sinh</label>
                                        <input type="date" class="form-control" name="date_of_birth" value="<?php echo $user->date_of_birth ?? ''; ?>">
                                        <!-- Debug: <?php echo "DEBUG: date_of_birth = '" . ($user->date_of_birth ?? 'NULL') . "'"; ?> -->
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-semibold">Giới tính</label>
                                        <select class="form-control" name="gender">
                                            <option value="">Chọn giới tính</option>
                                            <option value="male" <?php echo ($user->gender ?? '') === 'male' ? 'selected' : ''; ?>>Nam</option>
                                            <option value="female" <?php echo ($user->gender ?? '') === 'female' ? 'selected' : ''; ?>>Nữ</option>
                                            <option value="other" <?php echo ($user->gender ?? '') === 'other' ? 'selected' : ''; ?>>Khác</option>
                                        </select>
                                        <!-- Debug: <?php echo "DEBUG: gender = '" . ($user->gender ?? 'NULL') . "'"; ?> -->
                                    </div>
                                </div>
                                
                                <!-- Address Section with UX -->
                                <div class="mb-4">
                                    <label class="form-label fw-semibold">Địa chỉ</label>
                                    
                                    <!-- Country (Fixed) -->
                                    <div class="mb-3">
                                        <label class="form-label text-muted">Quốc gia</label>
                                        <input type="text" class="form-control" value="Việt Nam" readonly>
                                        <input type="hidden" name="country" value="Vietnam">
                                    </div>
                                    
                                    <!-- Province/City -->
                                    <div class="mb-3">
                                        <label class="form-label text-muted">Tỉnh/Thành phố <span class="text-danger">*</span></label>
                                        <select class="form-select" id="provinceSelect" name="province" required>
                                            <option value="">Chọn tỉnh/thành phố...</option>
                                        </select>
                                    </div>
                                    
                                    <!-- Ward (directly under Province) -->
                                    <div class="mb-3">
                                        <label class="form-label text-muted">Phường/Xã/Thị trấn <span class="text-danger">*</span></label>
                                        <select class="form-select" id="wardSelect" name="ward" required disabled>
                                            <option value="">Chọn phường/xã/thị trấn...</option>
                                        </select>
                                    </div>
                                    
                                    <!-- Street Address -->
                                    <div class="mb-3">
                                        <label class="form-label text-muted">Số nhà, tên đường <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="street" 
                                               placeholder="Ví dụ: 123 Nguyễn Du" 
                                               value="<?php echo htmlspecialchars(($defaultAddress && isset($defaultAddress->street)) ? $defaultAddress->street : ''); ?>" required>
                                        <div class="form-text">Nhập số nhà và tên đường chi tiết</div>
                                    </div>
                                </div>
                                
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>
                                    Cập nhật thông tin
                                </button>
                            </form>
                        </div>
                        
                        <!-- Change Password Tab -->
                        <div class="tab-pane fade" id="password" role="tabpanel">
                            <h5 class="fw-bold mb-4">
                                <i class="fas fa-key text-primary me-2"></i>
                                Đổi mật khẩu
                            </h5>
                            
                            <div id="passwordAlertContainer"></div>
                            
                            <form id="passwordForm">
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Mật khẩu hiện tại *</label>
                                    <input type="password" class="form-control" name="current_password" required>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Mật khẩu mới *</label>
                                    <input type="password" class="form-control" name="new_password" minlength="6" required>
                                    <small class="text-muted">Mật khẩu phải có ít nhất 6 ký tự</small>
                                </div>
                                
                                <div class="mb-4">
                                    <label class="form-label fw-semibold">Xác nhận mật khẩu mới *</label>
                                    <input type="password" class="form-control" name="confirm_password" minlength="6" required>
                                </div>
                                
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-key me-2"></i>
                                    Đổi mật khẩu
                                </button>
                            </form>
                        </div>
                        
                        <!-- Wishlist Tab -->
                        <div class="tab-pane fade" id="wishlist" role="tabpanel">
                            <h5 class="fw-bold mb-4">
                                <i class="fas fa-heart text-primary me-2"></i>
                                Danh sách yêu thích
                            </h5>
                            
                            <div id="wishlistContainer">
                                <!-- Wishlist items sẽ được load bằng JavaScript -->
                                <div class="text-center py-5">
                                    <div class="spinner-border text-primary" role="status">
                                        <span class="visually-hidden">Đang tải...</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Orders Tab -->
                        <div class="tab-pane fade" id="orders" role="tabpanel">
                            <h5 class="fw-bold mb-4">
                                <i class="fas fa-shopping-bag text-primary me-2"></i>
                                Đơn hàng của tôi
                            </h5>
                            
                            <div class="text-center py-5">
                                <i class="fas fa-shopping-bag fa-3x text-muted mb-3"></i>
                                <h6 class="text-muted">Bạn chưa có đơn hàng nào</h6>
                                <p class="text-muted">Hãy bắt đầu mua sắm để xem đơn hàng tại đây</p>
                                <a href="<?= BASE_URL ?>/products" class="btn btn-primary">
                                    <i class="fas fa-shopping-cart me-2"></i>
                                    Mua sắm ngay
                                </a>
                            </div>
                        </div>
                        
                        <!-- Settings Tab -->
                        <div class="tab-pane fade" id="settings" role="tabpanel">
                            <h5 class="fw-bold mb-4">
                                <i class="fas fa-cog text-primary me-2"></i>
                                Cài đặt
                            </h5>
                            
                            <div class="row">
                                <div class="col-md-6 mb-4">
                                    <div class="card">
                                        <div class="card-body">
                                            <h6 class="card-title">
                                                <i class="fas fa-bell me-2"></i>
                                                Thông báo
                                            </h6>
                                            <div class="form-check form-switch mb-2">
                                                <input class="form-check-input" type="checkbox" id="emailNotifications" checked>
                                                <label class="form-check-label" for="emailNotifications">
                                                    Nhận thông báo qua email
                                                </label>
                                            </div>
                                            <div class="form-check form-switch mb-2">
                                                <input class="form-check-input" type="checkbox" id="orderUpdates" checked>
                                                <label class="form-check-label" for="orderUpdates">
                                                    Cập nhật đơn hàng
                                                </label>
                                            </div>
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" id="promotions">
                                                <label class="form-check-label" for="promotions">
                                                    Khuyến mãi và ưu đãi
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-6 mb-4">
                                    <div class="card">
                                        <div class="card-body">
                                            <h6 class="card-title">
                                                <i class="fas fa-shield-alt me-2"></i>
                                                Bảo mật
                                            </h6>
                                            <div class="mb-3">
                                                <label class="form-label">Xác thực 2 bước</label>
                                                <div class="d-flex align-items-center">
                                                    <span class="badge bg-success me-2">Đã bật</span>
                                                    <button class="btn btn-sm btn-outline-secondary">Cấu hình</button>
                                                </div>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Phiên đăng nhập</label>
                                                <div>
                                                    <small class="text-muted">Thiết bị: Chrome trên Windows</small><br>
                                                    <small class="text-muted">Lần cuối: Hôm nay lúc 14:30</small>
                                                </div>
                                            </div>
                                            <button class="btn btn-sm btn-outline-danger">Đăng xuất tất cả thiết bị</button>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-12">
                                    <div class="card">
                                        <div class="card-body">
                                            <h6 class="card-title text-danger">
                                                <i class="fas fa-exclamation-triangle me-2"></i>
                                                Vùng nguy hiểm
                                            </h6>
                                            <p class="text-muted">Các hành động này không thể hoàn tác. Hãy cân nhắc kỹ trước khi thực hiện.</p>
                                            <button class="btn btn-outline-danger">
                                                <i class="fas fa-trash me-2"></i>
                                                Xóa tài khoản
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Profile form submission
        document.getElementById('profileForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const alertContainer = document.getElementById('alertContainer');
            
            try {
                const response = await fetch('<?= BASE_URL ?>/profile/update', {
                    method: 'POST',
                    body: formData
                });
                
                const result = await response.json();
                
                showAlert(alertContainer, result.success, result.message);
                
                if (result.success) {
                    // Update user name in header if changed
                    const nameInput = document.querySelector('input[name="name"]');
                    if (nameInput.value !== '<?php echo addslashes($user->name); ?>') {
                        setTimeout(() => location.reload(), 1500);
                    }
                }
                
            } catch (error) {
                showAlert(alertContainer, false, 'Có lỗi xảy ra khi kết nối đến server!');
            }
        });
        
        // Password form submission
        document.getElementById('passwordForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const alertContainer = document.getElementById('passwordAlertContainer');
            
            // Validate password confirmation
            const newPassword = formData.get('new_password');
            const confirmPassword = formData.get('confirm_password');
            
            if (newPassword !== confirmPassword) {
                showAlert(alertContainer, false, 'Mật khẩu xác nhận không khớp!');
                return;
            }
            
            try {
                const response = await fetch('<?= BASE_URL ?>/profile/change-password', {
                    method: 'POST',
                    body: formData
                });
                
                const result = await response.json();
                
                showAlert(alertContainer, result.success, result.message);
                
                if (result.success) {
                    this.reset();
                }
                
            } catch (error) {
                showAlert(alertContainer, false, 'Có lỗi xảy ra khi kết nối đến server!');
            }
        });
        
        // Avatar upload
        document.getElementById('avatarInput').addEventListener('change', async function(e) {
            const file = e.target.files[0];
            if (!file) return;
            
            const formData = new FormData();
            formData.append('avatar', file);
            
            try {
                const response = await fetch('<?= BASE_URL ?>/profile/upload-avatar', {
                    method: 'POST',
                    body: formData
                });
                
                const result = await response.json();
                
                if (result.success) {
                    document.getElementById('profileAvatar').src = result.data.avatar_url + '?t=' + Date.now();
                    showAlert(document.getElementById('alertContainer'), true, result.message);
                } else {
                    showAlert(document.getElementById('alertContainer'), false, result.message);
                }
                
            } catch (error) {
                showAlert(document.getElementById('alertContainer'), false, 'Có lỗi xảy ra khi upload avatar!');
            }
        });
        
        // Show alert function
        function showAlert(container, success, message) {
            container.innerHTML = `
                <div class="alert alert-${success ? 'success' : 'danger'} alert-dismissible fade show" role="alert">
                    <i class="fas fa-${success ? 'check-circle' : 'exclamation-circle'} me-2"></i>
                    ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            `;
        }
        
        // Load wishlist when tab is shown
        document.getElementById('wishlist-tab').addEventListener('shown.bs.tab', function () {
            loadWishlist();
        });
        
        // Load wishlist function
        async function loadWishlist() {
            const container = document.getElementById('wishlistContainer');
            
            try {
                const response = await fetch('<?= BASE_URL ?>/wishlist', {
                    method: 'GET',
                    credentials: 'same-origin',  // Include cookies/session
                    headers: {
                        'Cache-Control': 'no-cache'
                    }
                });
                
                console.log('Response status:', response.status);
                
                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                }
                
                // Get the HTML content
                const html = await response.text();
                
                // DEBUG: Log response details
                console.log('=== WISHLIST DEBUG ===');
                console.log('Response status:', response.status);
                console.log('HTML length:', html.length);
                console.log('HTML contains "wishlist":', html.includes('wishlist'));
                console.log('HTML contains "error":', html.includes('error'));
                console.log('HTML first 500 chars:', html.substring(0, 500));
                
                // Parse the HTML to extract wishlist items
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                
                // DEBUG: Check different selectors
                console.log('Elements found:');
                console.log('- #wishlistItemsContainer:', doc.querySelector('#wishlistItemsContainer') ? 'YES' : 'NO');
                console.log('- #wishlistGrid:', doc.querySelector('#wishlistGrid') ? 'YES' : 'NO');
                console.log('- .col-lg-3:', doc.querySelectorAll('.col-lg-3').length);
                console.log('- .product-card:', doc.querySelectorAll('.product-card').length);
                console.log('- .empty-wishlist:', doc.querySelector('.empty-wishlist') ? 'YES' : 'NO');
                
                // If response is redirect (login page), try API instead
                if (html.includes('signin') || html.includes('login') || response.status === 302) {
                    console.log('Detected redirect to login, trying API approach...');
                    await loadWishlistViaAPI();
                    return;
                }
                
                // Find wishlist items in the response
                const wishlistItemsContainer = doc.querySelector('#wishlistItemsContainer');
                
                if (wishlistItemsContainer && wishlistItemsContainer.children.length > 0) {
                    // Convert wishlist items to profile format
                    const items = Array.from(wishlistItemsContainer.children);
                    let wishlistHTML = '<div class="row">';
                    
                    items.forEach(item => {
                        const name = item.querySelector('.product-name')?.textContent?.trim() || 'Sản phẩm';
                        const price = item.querySelector('.price')?.textContent?.trim() || '0₫';
                        const img = item.querySelector('img')?.src || '<?= BASE_URL ?>/public/assets/images/placeholder.svg';
                        const productId = item.dataset.productId || '';
                        const href = item.querySelector('a')?.href || '#';
                        
                        wishlistHTML += `
                            <div class="col-md-4 col-sm-6 mb-4">
                                <div class="card wishlist-card position-relative">
                                    <button class="btn-remove-wishlist" onclick="removeFromWishlist(${productId})">
                                        <i class="fas fa-times"></i>
                                    </button>
                                    <a href="${href}" style="text-decoration: none; color: inherit;">
                                        <img src="${img}" class="card-img-top" alt="${name}">
                                        <div class="card-body">
                                            <h6 class="card-title">${name}</h6>
                                            <p class="price mb-0">${price}</p>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        `;
                    });
                    
                    wishlistHTML += '</div>';
                    container.innerHTML = wishlistHTML;
                } else {
                    // Try API approach if no items found in HTML
                    console.log('No items found in HTML, trying API...');
                    await loadWishlistViaAPI();
                }
                
            } catch (error) {
                console.error('Error loading wishlist:', error);
                container.innerHTML = `
                    <div class="text-center py-5">
                        <i class="fas fa-exclamation-triangle fa-3x text-warning mb-3"></i>
                        <h6 class="text-muted">Có lỗi xảy ra khi tải danh sách yêu thích</h6>
                        <button class="btn btn-outline-primary" onclick="loadWishlist()">
                            <i class="fas fa-redo me-2"></i>
                            Thử lại
                        </button>
                    </div>
                `;
            }
        }

        // Alternative method using API endpoint
        async function loadWishlistViaAPI() {
            const container = document.getElementById('wishlistContainer');
            
            try {
                console.log('Loading wishlist via API...');
                const response = await fetch('<?= BASE_URL ?>/api/wishlist/status', {
                    credentials: 'same-origin',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                console.log('API Response status:', response.status);
                
                if (!response.ok) {
                    throw new Error(`API Error: ${response.status}`);
                }

                const data = await response.json();
                console.log('API Response data:', data);
                
                if (data.success && data.count > 0) {
                    container.innerHTML = `
                        <div class="text-center py-5">
                            <i class="fas fa-heart fa-3x text-primary mb-3"></i>
                            <h6>Bạn có ${data.count} sản phẩm yêu thích</h6>
                            <p class="text-muted">Đang tải chi tiết sản phẩm...</p>
                            <a href="<?= BASE_URL ?>/wishlist" class="btn btn-primary">
                                <i class="fas fa-eye me-2"></i>
                                Xem danh sách đầy đủ
                            </a>
                        </div>
                    `;
                } else {
                    // Empty wishlist
                    container.innerHTML = `
                        <div class="text-center py-5">
                            <i class="fas fa-heart fa-3x text-muted mb-3"></i>
                            <h6 class="text-muted">Danh sách yêu thích trống</h6>
                            <p class="text-muted">Hãy thêm những sản phẩm bạn yêu thích để xem chúng tại đây</p>
                            <a href="<?= BASE_URL ?>/products" class="btn btn-primary">
                                <i class="fas fa-shopping-cart me-2"></i>
                                Khám phá sản phẩm
                            </a>
                        </div>
                    `;
                }
            } catch (error) {
                console.error('API Error loading wishlist:', error);
                container.innerHTML = `
                    <div class="text-center py-5">
                        <i class="fas fa-exclamation-triangle fa-3x text-warning mb-3"></i>
                        <h6 class="text-muted">Không thể tải danh sách yêu thích</h6>
                        <p class="text-muted">Vui lòng thử lại sau hoặc đăng nhập lại</p>
                        <button class="btn btn-outline-primary" onclick="loadWishlist()">
                            <i class="fas fa-redo me-2"></i>
                            Thử lại
                        </button>
                        <a href="<?= BASE_URL ?>/auth/signin" class="btn btn-outline-secondary ms-2">
                            <i class="fas fa-sign-in-alt me-2"></i>
                            Đăng nhập lại
                        </a>
                    </div>
                `;
            }
        }
        
        // Remove from wishlist function
        async function removeFromWishlist(productId) {
            try {
                const response = await fetch('<?= BASE_URL ?>/wishlist/remove', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `product_id=${productId}`
                });
                
                const result = await response.json();
                
                if (result.success) {
                    // Reload wishlist to reflect changes
                    loadWishlist();
                    showAlert(document.getElementById('alertContainer'), true, result.message);
                } else {
                    showAlert(document.getElementById('alertContainer'), false, result.message);
                }
                
            } catch (error) {
                console.error('Error removing from wishlist:', error);
                showAlert(document.getElementById('alertContainer'), false, 'Có lỗi xảy ra khi xóa sản phẩm!');
            }
        }
        
        // ================ ADDRESS LOCATION HANDLERS ================
        
        // Load provinces on page load
        document.addEventListener('DOMContentLoaded', function() {
            loadProvinces();
        });
        
        // Load provinces
        async function loadProvinces() {
            try {
                const response = await fetch('<?= BASE_URL ?>/api/locations/provinces');
                const result = await response.json();
                
                if (result.success && result.data) {
                    const provinceSelect = document.getElementById('provinceSelect');
                    provinceSelect.innerHTML = '<option value="">Chọn tỉnh/thành phố...</option>';
                    
                    result.data.forEach(province => {
                        const option = document.createElement('option');
                        option.value = province.name; // Thay đổi: lưu tên thay vì code
                        option.textContent = province.name;
                        option.dataset.code = province.code; // Giữ lại code để load wards
                        option.dataset.fullName = province.full_name || province.name;
                        provinceSelect.appendChild(option);
                    });
                    
                    // Set current province if exists
                    const currentProvince = "<?php echo ($defaultAddress && isset($defaultAddress->province)) ? $defaultAddress->province : ''; ?>";
                    if (currentProvince) {
                        // Try to match by code first, then by name
                        let foundOption = Array.from(provinceSelect.options).find(option => 
                            option.value === currentProvince || 
                            option.textContent === currentProvince ||
                            (option.dataset.fullName && option.dataset.fullName === currentProvince)
                        );
                        
                        if (foundOption) {
                            provinceSelect.value = foundOption.value;
                            loadWardsByProvince(foundOption.value);
                        }
                    }
                }
            } catch (error) {
                console.error('Error loading provinces:', error);
            }
        }
        
        // Province change handler
        document.getElementById('provinceSelect').addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const provinceCode = selectedOption ? selectedOption.dataset.code : null; // Lấy code từ dataset
            const wardSelect = document.getElementById('wardSelect');
            
            // Reset wards
            wardSelect.innerHTML = '<option value="">Chọn phường/xã/thị trấn...</option>';
            
            if (provinceCode) {
                wardSelect.disabled = false;
                loadWardsByProvince(provinceCode);
            } else {
                wardSelect.disabled = true;
            }
        });
        
        // Load wards directly by province code (modern structure)
        async function loadWardsByProvince(provinceCode) {
            try {
                const response = await fetch(`<?= BASE_URL ?>/api/locations/wards?province_code=${provinceCode}`);
                const result = await response.json();
                
                if (result.success && result.data) {
                    const wardSelect = document.getElementById('wardSelect');
                    wardSelect.innerHTML = '<option value="">Chọn phường/xã/thị trấn...</option>';
                    
                    result.data.forEach(ward => {
                        const option = document.createElement('option');
                        const wardName = ward.district_name ? `${ward.name} (${ward.district_name})` : ward.name;
                        option.value = wardName; // Thay đổi: lưu tên thay vì code
                        option.textContent = wardName;
                        option.dataset.code = ward.code; // Giữ lại code nếu cần
                        option.dataset.fullName = ward.full_name || ward.name;
                        option.dataset.districtName = ward.district_name || '';
                        wardSelect.appendChild(option);
                    });
                    
                    // Set current ward if exists
                    const currentWard = "<?php echo ($defaultAddress && isset($defaultAddress->ward)) ? $defaultAddress->ward : ''; ?>";
                    if (currentWard) {
                        // Try to match by code first, then by name
                        setTimeout(() => {
                            let foundWardOption = Array.from(wardSelect.options).find(option => 
                                option.value === currentWard || 
                                option.textContent === currentWard ||
                                (option.dataset.fullName && option.dataset.fullName === currentWard)
                            );
                            
                            if (foundWardOption) {
                                wardSelect.value = foundWardOption.value;
                            }
                        }, 500); // Wait for wards to load
                    }
                }
            } catch (error) {
                console.error('Error loading wards:', error);
            }
        }
    </script>
</body>
</html>

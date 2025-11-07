<?php
$title = $data['title'] ?? 'Thanh toán';
$cartItems = $data['cartItems'] ?? [];
$cartSummary = $data['cartSummary'] ?? [];
$userInfo = $data['userInfo'] ?? null;
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?> - Jewelry Store</title>
   
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link href="<?= asset('css/css.css?v=' . time()) ?>" rel="stylesheet">
</head>
<body>
   
    <!-- Loading Modal -->
    <div class="modal fade" id="loadingModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content text-center">
                <div class="modal-body py-4">
                    <div class="spinner-border text-primary mb-3" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mb-0">Đang xử lý đơn hàng...</p>
                </div>
            </div>
        </div>
    </div>
    <style>
        :root {
            --gold: #d4af37;
            --dark-gold: #b8941f;
            --light-gold: #f0e68c;
            --cream: #f8f6f0;
            --dark-brown: #3a2f28;
            --light-gray: #f5f5f5;
        }
       
        body {
            background-color: var(--cream);
            background-image: linear-gradient(rgb(255, 255, 255), rgba(255, 255, 255, 0.755)),
                url("https://images.unsplash.com/photo-1608042314453-ae338d80c427?ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxzZWFyY2h8MjR8fGpld2Vscnl8ZW58MHx8MHx8fDA%3D&auto=format&fit=crop&q=60&w=600");
            background-size: cover;
            background-attachment: fixed;
            font-family: "Inter", "Playfair Display", serif;
            color: var(--dark-brown);
        }
       
        .checkout-header {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            padding: 1.5rem 0;
        }
       
        .step-indicator {
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 2rem 0;
        }
       
        .step {
            display: flex;
            align-items: center;
            margin: 0 1rem;
            position: relative;
        }
       
        .step:not(:last-child)::after {
            content: '';
            position: absolute;
            right: -2rem;
            top: 50%;
            transform: translateY(-50%);
            width: 2rem;
            height: 2px;
            background: var(--light-gold);
        }
       
        .step-number {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: var(--light-gray);
            color: var(--dark-brown);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            margin-right: 0.8rem;
            border: 2px solid var(--light-gold);
            transition: all 0.3s ease;
        }
       
        .step.active .step-number {
            background: var(--gold);
            color: white;
            box-shadow: 0 0 15px rgba(212, 175, 55, 0.4);
            transform: scale(1.1);
        }
       
        .step.completed .step-number {
            background: var(--dark-gold);
            color: white;
            border-color: var(--dark-gold);
        }
       
        .checkout-card {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 15px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(212, 175, 55, 0.2);
            margin-bottom: 1.5rem;
            backdrop-filter: blur(8px);
            transition: all 0.3s ease;
        }
       
        .checkout-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 40px rgba(212, 175, 55, 0.15);
        }
       
        .checkout-card .card-header {
            background: linear-gradient(45deg, var(--cream), white);
            border-bottom: 2px solid var(--light-gold);
            border-radius: 15px 15px 0 0 !important;
            padding: 1.5rem;
        }
       
        .checkout-card .card-body {
            padding: 1.8rem;
        }
       
        .section-title {
            color: var(--dark-brown);
            font-weight: 600;
            margin-bottom: 0;
            display: flex;
            align-items: center;
            font-size: 16px;
            letter-spacing: 0.5px;
        }
       
        .section-title i {
            width: 24px;
            margin-right: 0.8rem;
            color: var(--gold);
        }
       
        .payment-option {
            transition: all 0.3s ease;
            cursor: pointer;
            background: white;
            border: 2px solid rgba(212, 175, 55, 0.2) !important;
        }
       
        .payment-option:hover {
            border-color: var(--gold) !important;
            box-shadow: 0 8px 24px rgba(212, 175, 55, 0.15);
            transform: translateY(-2px);
        }
       
        .form-check-input:checked + .form-check-label .payment-option {
            border-color: var(--gold) !important;
            background: linear-gradient(45deg, var(--cream), white);
        }
       
        .form-check-input:checked {
            background-color: var(--gold);
            border-color: var(--gold);
        }
       
        #bank_info, #store_info, #addressFields {
            transition: all 0.3s ease;
        }
       
        .summary-card {
            position: sticky;
            top: 100px;
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.95), rgba(255, 255, 255, 0.98));
            border: 1px solid var(--light-gold);
        }
       
        .summary-row {
            display: flex;
            justify-content: space-between;
            padding: 0.9rem 0;
            border-bottom: 1px solid rgba(212, 175, 55, 0.1);
            color: var(--dark-brown);
            font-size: 0.95rem;
        }
       
        .summary-row:last-child {
            border-bottom: none;
        }
       
        .summary-row.total {
            font-weight: 700;
            font-size: 1.2rem;
            border-top: 2px solid var(--light-gold);
            margin-top: 1rem;
            padding-top: 1.2rem;
            color: var(--dark-brown);
        }
       
        .summary-row.total span:last-child {
            color: var(--gold);
        }
       
        .product-item {
            display: flex;
            align-items: center;
            padding: 1.2rem 0;
            border-bottom: 1px solid rgba(212, 175, 55, 0.1);
            transition: all 0.3s ease;
        }
       
        .product-item:hover {
            transform: translateX(5px);
            background: linear-gradient(90deg, transparent, rgba(212, 175, 55, 0.05));
        }
       
        .product-item:last-child {
            border-bottom: none;
        }
       
        .product-image {
            width: 70px;
            height: 70px;
            object-fit: cover;
            border-radius: 10px;
            margin-right: 1.2rem;
            border: 2px solid var(--light-gold);
            transition: all 0.3s ease;
        }
       
        .product-item:hover .product-image {
            transform: scale(1.05);
            box-shadow: 0 5px 15px rgba(212, 175, 55, 0.2);
        }
       
        .btn-checkout {
            background: var(--gold);
            border: none;
            padding: 1.2rem 2rem;
            border-radius: 10px;
            font-weight: 600;
            font-size: 1.1rem;
            width: 100%;
            transition: all 0.3s ease;
            color: white;
            text-transform: uppercase;
            letter-spacing: 1px;
            position: relative;
            overflow: hidden;
        }
       
        .btn-checkout::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: 0.5s;
        }
       
        .btn-checkout:hover {
            background: var(--dark-gold);
            transform: translateY(-3px);
            box-shadow: 0 10px 30px rgba(212, 175, 55, 0.4);
        }
       
        .btn-checkout:hover::before {
            left: 100%;
        }
       
        .form-control, .form-select {
            border: 2px solid rgba(212, 175, 55, 0.2);
            border-radius: 8px;
            padding: 0.85rem;
            transition: all 0.3s ease;
            background: rgba(255, 255, 255, 0.9);
            color: var(--dark-brown);
            font-size: 0.95rem;
        }
       
        .form-control:focus, .form-select:focus {
            border-color: var(--gold);
            box-shadow: 0 0 20px rgba(212, 175, 55, 0.2);
            background: white;
        }
       
        .form-control::placeholder {
            color: #999;
            font-style: italic;
        }
       
        .form-label {
            color: var(--dark-brown);
            font-weight: 500;
            margin-bottom: 0.5rem;
            font-size: 0.95rem;
        }
       
        .invalid-feedback {
            display: none;
            color: #dc3545;
            font-size: 0.85rem;
            margin-top: 0.4rem;
        }
       
        .valid-feedback {
            display: none;
            color: #198754;
            font-size: 0.85rem;
            margin-top: 0.4rem;
            font-weight: 500;
        }
       
        /* Show appropriate feedback based on validation state */
        .form-control.is-invalid + .invalid-feedback,
        .form-select.is-invalid + .invalid-feedback {
            display: block;
        }
       
        .form-control.is-valid ~ .valid-feedback,
        .form-select.is-valid ~ .valid-feedback {
            display: block;
        }
       
        .form-control.is-valid, .form-select.is-valid {
            border-color: #198754;
            box-shadow: 0 0 15px rgba(25, 135, 84, 0.15);
        }
       
        .form-control.is-invalid, .form-select.is-invalid {
            border-color: #dc3545;
            box-shadow: 0 0 15px rgba(220, 53, 69, 0.15);
        }
       
        @media (max-width: 768px) {
            .step-indicator {
                flex-direction: column;
                gap: 1rem;
            }
           
            .step {
                margin: 0;
            }
           
            .summary-card {
                position: static;
            }
        }
    </style>
</head>
<body>
    <!-- Include Header -->
    <?php include __DIR__ . '/../components/header.php'; ?>
   
    <div class="checkout-header">
        <div class="container">
            <!-- Breadcrumb -->
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-3">
                    <li class="breadcrumb-item"><a href="<?= url('/') ?>" class="text-decoration-none">Trang chủ</a></li>
                    <li class="breadcrumb-item"><a href="<?= url('/cart') ?>" class="text-decoration-none">Giỏ hàng</a></li>
                    <li class="breadcrumb-item active">Thanh toán</li>
                </ol>
            </nav>
           
            <h2 class="mb-0">
                <i class="fas fa-credit-card me-2 text-primary"></i>
                Thanh toán đơn hàng
            </h2>
           
            <!-- Step Indicator -->
            <div class="step-indicator">
                <div class="step completed">
                    <div class="step-number">1</div>
                    <span>Giỏ hàng</span>
                </div>
                <div class="step active">
                    <div class="step-number">2</div>
                    <span>Thanh toán</span>
                </div>
                <div class="step">
                    <div class="step-number">3</div>
                    <span>Hoàn thành</span>
                </div>
            </div>
        </div>
    </div>


    <div class="container py-4">
        <div class="row">
            <!-- Checkout Form -->
            <div class="col-lg-8">
                <form id="checkoutForm" class="needs-validation" novalidate>
                    <!-- Payment and Delivery Method -->
                    <div class="checkout-card mb-4">
                        <div class="card-header">
                            <h5 class="section-title">
                                <i class="fas fa-credit-card"></i>
                                Thanh toán và nhận hàng
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="payment-option p-3 rounded border mb-3">
                                        <input class="form-check-input" type="radio" name="payment_delivery_method"
                                               id="bank_transfer_home" value="bank_transfer_home" checked>
                                        <label class="form-check-label w-100" for="bank_transfer_home">
                                            <div class="d-flex align-items-center">
                                                <i class="fas fa-money-check-alt text-success me-3 fs-4"></i>
                                                <div>
                                                    <strong>Chuyển khoản ngân hàng</strong>
                                                    <small class="text-muted d-block">Nhận hàng tại nhà</small>
                                                </div>
                                            </div>
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="payment-option p-3 rounded border mb-3">
                                        <input class="form-check-input" type="radio" name="payment_delivery_method"
                                               id="cash_store" value="cash_store">
                                        <label class="form-check-label w-100" for="cash_store">
                                            <div class="d-flex align-items-center">
                                                <i class="fas fa-store text-warning me-3 fs-4"></i>
                                                <div>
                                                    <strong>Thanh toán tại cửa hàng</strong>
                                                    <small class="text-muted d-block">Nhận hàng tại cửa hàng</small>
                                                </div>
                                            </div>
                                        </label>
                                    </div>
                                </div>
                            </div>


                            <!-- Bank Information -->
                            <div id="bank_info" class="mt-4">
                                <div class="alert alert-info">
                                    <h6 class="mb-3"><i class="fas fa-university me-2"></i>Thông tin chuyển khoản:</h6>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <p class="mb-1"><strong>Ngân hàng:</strong> Vietcombank</p>
                                            <p class="mb-1"><strong>Số tài khoản:</strong> 1234567890</p>
                                            <p class="mb-1"><strong>Chủ tài khoản:</strong> JEWELRY STORE</p>
                                        </div>
                                        <div class="col-md-6">
                                            <p class="mb-1"><strong>Nội dung chuyển khoản:</strong></p>
                                            <code id="transfer_content">DH[MÃ ĐƠN HÀNG] [SỐ ĐIỆN THOẠI]</code>
                                            <br><small class="text-muted">Ví dụ: DH001 0987654321</small>
                                        </div>
                                    </div>
                                </div>
                            </div>


                            <!-- Store Information -->
                            <div id="store_info" class="mt-4" style="display: none;">
                                <div class="alert alert-warning">
                                    <h6 class="mb-3"><i class="fas fa-store me-2"></i>Thông tin cửa hàng:</h6>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <p class="mb-1"><strong><i class="fas fa-map-marker-alt me-2"></i>Địa chỉ:</strong></p>
                                                <p class="mb-1">123 Nguyễn Huệ, Phường Bến Nghé</p>
                                                <p class="mb-1">Quận 1, TP. Hồ Chí Minh</p>
                                            </div>
                                            <div class="mb-3">
                                                <p class="mb-1"><strong><i class="fas fa-phone me-2"></i>Hotline:</strong> 1900 1234</p>
                                                <p class="mb-1"><strong><i class="fas fa-clock me-2"></i>Giờ mở cửa:</strong> 8:00 - 22:00 (T2-CN)</p>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="bg-light p-3 rounded">
                                                <h6 class="text-success mb-2"><i class="fas fa-info-circle me-2"></i>Hướng dẫn nhận hàng:</h6>
                                                <ul class="list-unstyled mb-0">
                                                    <li class="mb-1"><i class="fas fa-check text-success me-2"></i>Mang theo CMND/CCCD</li>
                                                    <li class="mb-1"><i class="fas fa-check text-success me-2"></i>Cung cấp <strong>số điện thoại</strong> hoặc <strong>email</strong> đã đặt hàng</li>
                                                    <li class="mb-1"><i class="fas fa-check text-success me-2"></i>Xuất trình mã đơn hàng (nếu có)</li>
                                                    <li class="mb-1"><i class="fas fa-check text-success me-2"></i>Kiểm tra sản phẩm trước khi thanh toán</li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="alert alert-info mt-3 mb-0">
                                        <i class="fas fa-exclamation-circle me-2"></i>
                                        <strong>Lưu ý:</strong> Đơn hàng sẽ được giữ trong <strong>3 ngày</strong>. Vui lòng đến nhận hàng trong thời gian này.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>


                    <!-- Customer Information -->
                    <div class="checkout-card">
                        <div class="card-header">
                            <h5 class="section-title">
                                <i class="fas fa-user"></i>
                                Thông tin khách hàng
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-semibold">Họ và tên <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="name"
                                           value="<?= $userInfo ? htmlspecialchars($userInfo->name) : '' ?>"
                                           placeholder="Nhập họ và tên đầy đủ" required>
                                    <div class="invalid-feedback">Vui lòng nhập họ và tên</div>
                                    <div class="valid-feedback">Tên hợp lệ ✓</div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-semibold">Email <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control" name="email"
                                           value="<?= $userInfo ? htmlspecialchars($userInfo->email) : '' ?>"
                                           placeholder="example@email.com" required>
                                    <div class="invalid-feedback">Vui lòng nhập email hợp lệ</div>
                                    <div class="valid-feedback">Email hợp lệ ✓</div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-semibold">Số điện thoại <span class="text-danger">*</span></label>
                                    <input type="tel" class="form-control" name="phone"
                                           value="<?= $userInfo ? htmlspecialchars($userInfo->phone ?? '') : '' ?>"
                                           placeholder="0123456789" required>
                                    <div class="invalid-feedback">Vui lòng nhập số điện thoại</div>
                                    <div class="valid-feedback">Số điện thoại hợp lệ ✓</div>
                                </div>
                                <!-- Address Fields - Will be shown/hidden based on delivery method -->
                                <div id="addressFields">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-semibold">Tỉnh/Thành phố <span class="text-danger">*</span></label>
                                        <select class="form-select" id="province" name="province" required>
                                            <option value="">Chọn tỉnh/thành phố...</option>
                                        </select>
                                        <div class="invalid-feedback">Vui lòng chọn tỉnh/thành phố</div>
                                        <div class="valid-feedback">Tỉnh/Thành phố đã chọn ✓</div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-semibold">Phường/Xã <span class="text-danger">*</span></label>
                                        <select class="form-select" id="ward" name="ward" required>
                                            <option value="">Chọn phường/xã...</option>
                                        </select>
                                        <div class="invalid-feedback">Vui lòng chọn phường/xã</div>
                                        <div class="valid-feedback">Phường/Xã đã chọn ✓</div>
                                    </div>
                                    <div class="col-12 mb-3">
                                        <label class="form-label fw-semibold">Địa chỉ chi tiết <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="address"
                                               placeholder="Ví dụ: 123 Nguyễn Du" required>
                                        <div class="invalid-feedback">Vui lòng nhập địa chỉ chi tiết</div>
                                        <div class="valid-feedback">Địa chỉ hợp lệ ✓</div>
                                    </div>
                                </div>
                                <div class="col-12 mb-3">
                                    <label class="form-label fw-semibold">Ghi chú đơn hàng</label>
                                    <textarea class="form-control" name="notes" rows="2"
                                              placeholder="Ghi chú về đơn hàng, thời gian giao hàng mong muốn..."></textarea>
                                </div>
                            </div>
                        </div>
                    </div>






                    <!-- Order Notes -->
                    <div class="checkout-card">
                        <div class="card-header">
                            <h5 class="section-title">
                                <i class="fas fa-shield-alt"></i>
                                Chính sách & Bảo đảm
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4 text-center mb-3">
                                    <i class="fas fa-shipping-fast text-primary fs-2 mb-2"></i>
                                    <div class="fw-bold">Giao hàng nhanh</div>
                                    <small class="text-muted">Giao hàng trong 1-2 ngày</small>
                                </div>
                                <div class="col-md-4 text-center mb-3">
                                    <i class="fas fa-undo text-success fs-2 mb-2"></i>
                                    <div class="fw-bold">Đổi trả 30 ngày</div>
                                    <small class="text-muted">Đổi trả miễn phí trong 30 ngày</small>
                                </div>
                                <div class="col-md-4 text-center mb-3">
                                    <i class="fas fa-certificate text-warning fs-2 mb-2"></i>
                                    <div class="fw-bold">Bảo hành chính hãng</div>
                                    <small class="text-muted">Bảo hành toàn diện 2 năm</small>
                                </div>
                            </div>
                        </div>
                    </div>
           
            </div>


            <!-- Order Summary -->
            <div class="col-lg-4">
                <div class="checkout-card summary-card">
                    <div class="card-header">
                        <h5 class="section-title">
                            <i class="fas fa-receipt"></i>
                            Tóm tắt đơn hàng
                        </h5>
                    </div>
                    <div class="card-body">
                        <!-- Cart Items -->
                        <div class="order-items mb-4" style="max-height: 400px; overflow-y: auto;">
                            <?php if (!empty($cartItems)): ?>
                                <?php foreach ($cartItems as $item): ?>
                                    <div class="product-item">
                                          <?php
                                // Get primary image URL using the Product model method
                                           $productModel = new Product();
                                            $imageUrl = $productModel->getPrimaryImageUrl($item['product']->product_id);
                                        ?>
                                             <img src="<?php echo $imageUrl; ?>"
                                              alt="<?php echo htmlspecialchars($item['product']->name); ?>"
                                             class="product-image">
                                        <div class="flex-grow-1">
                                            <div class="fw-semibold mb-1"><?= htmlspecialchars($item['product']->name) ?></div>
                                            <div class="text-muted small mb-2">
                                                <?php if ($item['size']): ?>
                                                    <span class="badge bg-light text-dark me-1">Size: <?= htmlspecialchars($item['size']) ?></span>
                                                <?php endif; ?>
                                                <?php if ($item['color']): ?>
                                                    <span class="badge bg-light text-dark me-1">Màu: <?= htmlspecialchars($item['color']) ?></span>
                                                <?php endif; ?>
                                                <span class="badge bg-primary">SL: <?= $item['quantity'] ?></span>
                                            </div>
                                            <div class="fw-bold text-primary"><?= number_format($item['subtotal'], 0, ',', '.') ?>₫</div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="text-center text-muted py-4">
                                    <i class="fas fa-shopping-cart fs-2 mb-2"></i>
                                    <div>Không có sản phẩm nào</div>
                                </div>
                            <?php endif; ?>
                        </div>


                        <!-- Order Summary -->
                        <div class="order-summary">
                            <div class="summary-row">
                                <span>Tạm tính:</span>
                                <span class="fw-semibold"><?= number_format($cartSummary['subtotal'] ?? 0, 0, ',', '.') ?>₫</span>
                            </div>
                            <div class="summary-row">
                                <span>Phí vận chuyển:</span>
                                <span class="fw-semibold">
                                    <?php if (($cartSummary['shipping'] ?? 0) == 0): ?>
                                        <span class="text-success">Miễn phí</span>
                                    <?php else: ?>
                                        <?= number_format($cartSummary['shipping'], 0, ',', '.') ?>₫
                                    <?php endif; ?>
                                </span>
                            </div>
                            <div class="summary-row">
                                <span>Thuế VAT (10%):</span>
                                <span class="fw-semibold"><?= number_format($cartSummary['tax'] ?? 0, 0, ',', '.') ?>₫</span>
                            </div>
                            <div class="summary-row total">
                                <span>Tổng cộng:</span>
                                <span class="text-primary"><?= number_format($cartSummary['total'] ?? 0, 0, ',', '.') ?>₫</span>
                            </div>
                        </div>


                        <!-- Free Shipping Notice -->
                        <?php if (($cartSummary['subtotal'] ?? 0) < 500000): ?>
                            <div class="alert alert-info mt-3">
                                <i class="fas fa-truck me-2"></i>
                                <small>Mua thêm <?= number_format(500000 - ($cartSummary['subtotal'] ?? 0), 0, ',', '.') ?>₫ để được miễn phí vận chuyển!</small>
                            </div>
                        <?php endif; ?>


                        <!-- Place Order Button -->
                        <button type="submit" form="checkoutForm" class="btn btn-primary btn-checkout mt-3">
                            <i class="fas fa-lock me-2"></i>
                            Đặt hàng ngay
                        </button>
                       
                        <div class="text-center mt-3">
                            <small class="text-muted">
                                <i class="fas fa-shield-alt me-1"></i>
                                Thanh toán được bảo mật với SSL
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
       
        <?php if (($cartSummary['subtotal'] ?? 0) < 500000): ?>
            <div class="alert alert-info mt-3 mb-0">
                <small>
                    <i class="fas fa-info-circle me-1"></i>
                    Mua thêm <?= number_format(500000 - ($cartSummary['subtotal'] ?? 0)) ?>₫ để được miễn phí vận chuyển
                </small>
            </div>
        <?php endif; ?>
    </div>
</div>


<!-- Loading Modal -->
<div class="modal fade" id="loadingModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content text-center">
            <div class="modal-body py-4">
                <div class="spinner-border text-primary mb-3" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mb-0">Đang xử lý đơn hàng...</p>
            </div>
        </div>
    </div>
</div>
<style>
.payment-method .form-check-input:checked + .form-check-label .payment-option {
    border-color: #0d6efd !important;
    background-color: rgba(13, 110, 253, 0.1);
}


.payment-option {
    transition: all 0.3s ease;
    cursor: pointer;
}


.payment-option:hover {
    border-color: #0d6efd !important;
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
}


.order-items::-webkit-scrollbar {
    width: 4px;
}


.order-items::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 4px;
}


.order-items::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 4px;
}


.order-items::-webkit-scrollbar-thumb:hover {
    background: #a8a8a8;
}
</style>


<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('checkoutForm');
    const deliveryMethods = document.querySelectorAll('input[name="payment_delivery_method"]');
    const bankInfo = document.getElementById('bank_info');
    const storeInfo = document.getElementById('store_info');
    const addressFields = document.getElementById('addressFields');
    const loadingModalEl = document.getElementById('loadingModal');
    const loadingModal = loadingModalEl ? new bootstrap.Modal(loadingModalEl) : null;


    // Province and Ward selects
    const provinceSelect = document.getElementById('province');
    const wardSelect = document.getElementById('ward');
    const phoneInput = document.querySelector('input[name="phone"]');
    const transferContent = document.getElementById('transfer_content');


    // Load provinces on page load
    loadProvinces();


    // Province change handler
    if (provinceSelect && wardSelect) {
        provinceSelect.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const provinceCode = selectedOption ? selectedOption.dataset.code : null; // Lấy code từ dataset
           
            // Reset ward selection
            wardSelect.innerHTML = '<option value="">Chọn phường/xã...</option>';
            wardSelect.disabled = !provinceCode;
           
            if (provinceCode) {
                loadWards(provinceCode);
            }
        });
    }


    // Phone input change handler - update transfer content
    if (phoneInput && transferContent) {
        phoneInput.addEventListener('input', function() {
            const phone = this.value.trim();
            if (phone) {
                transferContent.textContent = `DH[MÃ ĐƠN HÀNG] ${phone}`;
            } else {
                transferContent.textContent = 'DH[MÃ ĐƠN HÀNG] [SỐ ĐIỆN THOẠI]';
            }
        });
    }


    // Delivery method change handler
    deliveryMethods.forEach(method => {
        method.addEventListener('change', function() {
            toggleDeliveryMethod(this.value);
        });
    });


    // Initialize with default selection
    const checkedMethod = document.querySelector('input[name="payment_delivery_method"]:checked');
    if (checkedMethod) {
        toggleDeliveryMethod(checkedMethod.value);
    }


    function toggleDeliveryMethod(method) {
        if (method === 'bank_transfer_home') {
            // Show bank info and address fields, hide store info
            if (bankInfo) {
                bankInfo.style.display = 'block';
            }
            if (storeInfo) {
                storeInfo.style.display = 'none';
            }
            if (addressFields) {
                addressFields.style.display = 'block';
                // Make address fields required
                addressFields.querySelectorAll('input, select').forEach(field => {
                    field.required = true;
                });
            }
        } else if (method === 'cash_store') {
            // Show store info, hide bank info and address fields
            if (bankInfo) {
                bankInfo.style.display = 'none';
            }
            if (storeInfo) {
                storeInfo.style.display = 'block';
            }
            if (addressFields) {
                addressFields.style.display = 'none';
                // Remove required from address fields
                addressFields.querySelectorAll('input, select').forEach(field => {
                    field.required = false;
                });
            }
        }
    }


    async function loadProvinces() {
        try {
            const response = await fetch('<?= url('api/locations/provinces') ?>');
            const data = await response.json();
           
            if (data.success && data.data) {
                provinceSelect.innerHTML = '<option value="">Chọn tỉnh/thành phố...</option>';
                data.data.forEach(province => {
                    const option = document.createElement('option');
                    option.value = province.name; // Thay đổi: lưu tên thay vì code
                    option.textContent = province.name;
                    option.dataset.code = province.code; // Giữ lại code để load wards
                    provinceSelect.appendChild(option);
                });
            }
        } catch (error) {
            console.error('Error loading provinces:', error);
            showNotification('Không thể tải danh sách tỉnh/thành phố', 'error');
        }
    }


    async function loadWards(provinceCode) {
        if (!provinceCode) return;
       
        try {
            wardSelect.innerHTML = '<option value="">Đang tải...</option>';
            wardSelect.disabled = true;
           
            const wardUrl = `<?= url('api/locations/wards') ?>?province=${provinceCode}`;
            const response = await fetch(wardUrl);
            const data = await response.json();
           
            if (data.success && data.data && data.data.length > 0) {
                wardSelect.innerHTML = '<option value="">Chọn phường/xã...</option>';
                data.data.forEach(ward => {
                    const option = document.createElement('option');
                    const wardName = ward.district_name ? `${ward.name} (${ward.district_name})` : ward.name;
                    option.value = wardName; // Thay đổi: lưu tên thay vì code
                    option.textContent = wardName;
                    option.dataset.code = ward.code; // Giữ lại code nếu cần
                    option.dataset.fullName = ward.full_name || ward.name;
                    option.dataset.districtName = ward.district_name || '';
                    wardSelect.appendChild(option);
                });
                wardSelect.disabled = false;
            } else {
                wardSelect.innerHTML = '<option value="">Không có dữ liệu</option>';
            }
        } catch (error) {
            console.error('Error loading wards:', error);
            wardSelect.innerHTML = '<option value="">Lỗi tải dữ liệu</option>';
            showNotification('Không thể tải danh sách phường/xã', 'error');
        }
    }


    // Real-time validation
    if (form) {
        // Add validation listeners to all form fields
        const formFields = form.querySelectorAll('input, select, textarea');
        formFields.forEach(field => {
            field.addEventListener('blur', function() {
                validateField(this);
            });
           
            field.addEventListener('input', function() {
                // Clear validation state on input
                this.classList.remove('is-valid', 'is-invalid');
            });
           
            // For select fields, validate on change
            if (field.tagName === 'SELECT') {
                field.addEventListener('change', function() {
                    validateField(this);
                });
            }
        });
       
        // Validate individual field
        function validateField(field) {
            const value = field.value.trim();
           
            // Always clear previous validation classes first
            field.classList.remove('is-valid', 'is-invalid');
           
            // Don't validate if field is empty (let them type first)
            if (!value && field.hasAttribute('required')) {
                return; // No validation display for empty required fields
            }
           
            // If field has content, validate it
            if (value) {
                let isValid = true;
               
                // Email validation
                if (field.type === 'email') {
                    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                    isValid = emailRegex.test(value);
                }
                // Phone validation
                else if (field.type === 'tel') {
                    const phoneRegex = /^[0-9]{10,11}$/;
                    isValid = phoneRegex.test(value);
                }
                // Name validation (at least 2 characters)
                else if (field.name === 'name') {
                    isValid = value.length >= 2;
                }
                // Address validation (at least 5 characters)
                else if (field.name === 'address') {
                    isValid = value.length >= 5;
                }
                // Select validation
                else if (field.tagName === 'SELECT') {
                    isValid = value !== '';
                }
               
                // Apply validation classes only if field has content
                field.classList.add(isValid ? 'is-valid' : 'is-invalid');
            }
        }
    }


    // Form submission handler
    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Custom validation for dynamic fields
            const paymentMethod = document.querySelector('input[name="payment_delivery_method"]:checked');
            
            if (!paymentMethod) {
                showNotification('Vui lòng chọn phương thức thanh toán và nhận hàng', 'error');
                e.stopPropagation();
                return;
            }


            // Handle address field validation based on payment method
            const addressFields = document.getElementById('addressFields');
            const addressInputs = addressFields ? addressFields.querySelectorAll('input, select') : [];
            let skipFormValidation = false;
            
            if (paymentMethod.value === 'cash_store') {
                // For cash_store, temporarily disable and clear address fields to avoid validation
                addressInputs.forEach(field => {
                    field.disabled = true; // Disable to skip HTML5 validation
                    field.value = ''; // Clear values so they won't be sent
                    field.classList.remove('is-invalid', 'is-valid'); // Remove validation classes
                });
                
                // Manual validation for required fields (excluding address fields)
                const requiredFields = form.querySelectorAll('input[required]:not([disabled]), select[required]:not([disabled]), textarea[required]:not([disabled])');
                let hasValidationError = false;
                
                requiredFields.forEach(field => {
                    if (!field.value.trim()) {
                        field.classList.add('is-invalid');
                        hasValidationError = true;
                    } else {
                        field.classList.remove('is-invalid');
                        field.classList.add('is-valid');
                    }
                });
                
                if (hasValidationError) {
                    // Re-enable address fields
                    addressInputs.forEach(field => {
                        field.disabled = false;
                    });
                    showNotification('Vui lòng điền đầy đủ thông tin bắt buộc', 'error');
                    form.classList.add('was-validated');
                    return;
                }
                
                skipFormValidation = true; // Skip HTML5 validation for cash_store
                
            } else if (paymentMethod.value === 'bank_transfer_home') {
                // For bank_transfer_home, ensure address fields are enabled and validate them
                addressInputs.forEach(field => {
                    field.disabled = false;
                });
                
                const province = document.getElementById('province')?.value;
                const ward = document.getElementById('ward')?.value;
                const address = document.querySelector('input[name="address"]')?.value;


                if (!province || !ward || !address?.trim()) {
                    showNotification('Vui lòng điền đầy đủ thông tin địa chỉ giao hàng', 'error');
                    e.stopPropagation();
                    form.classList.add('was-validated');
                    
                    // Highlight missing fields
                    if (!province) document.getElementById('province')?.classList.add('is-invalid');
                    if (!ward) document.getElementById('ward')?.classList.add('is-invalid');
                    if (!address?.trim()) document.querySelector('input[name="address"]')?.classList.add('is-invalid');
                    
                    return;
                }
                
                // Manual validation for all required fields
                const allRequiredFields = form.querySelectorAll('input[required], select[required], textarea[required]');
                let hasValidationError = false;
                
                allRequiredFields.forEach(field => {
                    const value = field.value.trim();
                    
                    if (!value) {
                        field.classList.add('is-invalid');
                        hasValidationError = true;
                    } else {
                        field.classList.remove('is-invalid');
                        field.classList.add('is-valid');
                    }
                });
                
                if (hasValidationError) {
                    showNotification('Vui lòng điền đầy đủ thông tin bắt buộc', 'error');
                    form.classList.add('was-validated');
                    return;
                }
                
                skipFormValidation = true; // Use manual validation instead of HTML5
            }

            // Validate form (only if not skipped)
            if (!skipFormValidation) {
                if (!form.checkValidity()) {
                    e.stopPropagation();
                    form.classList.add('was-validated');
                    return;
                }
            }


            // Show loading modal if available
            if (loadingModal) loadingModal.show();


            // Prepare form data
            const formData = new FormData(form);


            // Submit checkout
            const checkoutUrl = '<?= url('checkout/process') ?>';
            
            fetch(checkoutUrl, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                }
                
                return response.text().then(text => {
                    try {
                        return JSON.parse(text);
                    } catch (e) {
                        throw new Error('Server returned invalid JSON: ' + text.substring(0, 100));
                    }
                });
            })
            .then(data => {
                if (loadingModal) loadingModal.hide();
                
                // Re-enable address fields after request completes
                if (paymentMethod.value === 'cash_store') {
                    addressInputs.forEach(field => {
                        field.disabled = false;
                    });
                }

                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Đặt hàng thành công!',
                        text: 'Cảm ơn bạn đã đặt hàng. Chúng tôi sẽ liên hệ với bạn sớm nhất.',
                        confirmButtonText: 'Xem đơn hàng',
                        confirmButtonColor: '#0d6efd',
                        allowOutsideClick: false
                    }).then(() => {
                        if (data.data && data.data.redirect) {
                            window.location.href = data.data.redirect;
                        } else {
                            window.location.href = '<?= url('/') ?>';
                        }
                    });
                } else {
                    let errorMessage = data.message || 'Có lỗi xảy ra khi đặt hàng';
                    if (data.data && typeof data.data === 'object') {
                        errorMessage = 'Vui lòng kiểm tra lại thông tin:\n';
                        Object.values(data.data).forEach(error => {
                            errorMessage += '• ' + error + '\n';
                        });
                    }
                    Swal.fire({
                        icon: 'error',
                        title: 'Lỗi đặt hàng',
                        text: errorMessage,
                        confirmButtonText: 'Thử lại',
                        confirmButtonColor: '#dc3545'
                    });
                }
            })
            .catch(error => {
                if (loadingModal) loadingModal.hide();
                
                // Re-enable address fields on error
                if (paymentMethod.value === 'cash_store') {
                    addressInputs.forEach(field => {
                        field.disabled = false;
                    });
                }

                
                Swal.fire({
                    icon: 'error',
                    title: 'Lỗi kết nối',
                    text: 'Không thể kết nối đến máy chủ: ' + error.message,
                    confirmButtonText: 'Thử lại',
                    confirmButtonColor: '#dc3545'
                });
            });
        });
    }


    // Helper function for notifications
    function showNotification(message, type = 'info') {
        // Create notification element
        const notification = document.createElement('div');
        notification.className = `alert alert-${type === 'error' ? 'danger' : 'info'} alert-dismissible fade show position-fixed`;
        notification.style.cssText = 'top: 20px; right: 20px; z-index: 9999; max-width: 300px;';
        notification.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
       
        document.body.appendChild(notification);
       
        // Auto remove after 5 seconds
        setTimeout(() => {
            if (notification.parentNode) {
                notification.remove();
            }
        }, 5000);
    }
});
    </script>
    <?php include __DIR__ . '/../components/footer.php'; ?>


    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


</body>
</html>




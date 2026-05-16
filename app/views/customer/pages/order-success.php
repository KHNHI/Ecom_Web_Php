<?php
$title = $data['title'] ?? 'Đặt hàng thành công';
$order_id = $data['order_id'] ?? '';
?>

<div class="container-fluid px-0">
    <!-- Success Header -->
    <div class="bg-success text-white py-5">
        <div class="container text-center">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="success-icon mb-3">
                        <i class="fas fa-check-circle fa-5x"></i>
                    </div>
                    <h1 class="display-5 fw-bold mb-3">Đặt hàng thành công!</h1>
                    <p class="lead mb-0">Cảm ơn bạn đã tin tưởng và đặt hàng tại cửa hàng chúng tôi</p>
                </div>
            </div>
        </div>
    </div>

    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <!-- Order Information -->
                <div class="card shadow-sm mb-4">
                    <div class="card-body text-center p-5">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <div class="info-item">
                                    <i class="fas fa-receipt text-primary mb-2 fa-2x"></i>
                                    <h5>Mã đơn hàng</h5>
                                    <p class="fw-bold text-primary fs-4"><?= htmlspecialchars($order_id) ?></p>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="info-item">
                                    <i class="fas fa-calendar-alt text-success mb-2 fa-2x"></i>
                                    <h5>Ngày đặt hàng</h5>
                                    <p class="fw-bold"><?= date('d/m/Y H:i') ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Order Status Timeline -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-shipping-fast me-2"></i>Trạng thái đơn hàng</h5>
                    </div>
                    <div class="card-body">
                        <div class="timeline">
                            <div class="timeline-item active">
                                <div class="timeline-marker">
                                    <i class="fas fa-check"></i>
                                </div>
                                <div class="timeline-content">
                                    <h6 class="timeline-title">Đơn hàng đã được tiếp nhận</h6>
                                    <p class="timeline-text">Đơn hàng của bạn đã được ghi nhận và đang chờ xử lý</p>
                                    <small class="timeline-time text-muted"><?= date('d/m/Y H:i') ?></small>
                                </div>
                            </div>
                            
                            <div class="timeline-item">
                                <div class="timeline-marker">
                                    <i class="fas fa-box"></i>
                                </div>
                                <div class="timeline-content">
                                    <h6 class="timeline-title">Đang chuẩn bị hàng</h6>
                                    <p class="timeline-text">Chúng tôi đang chuẩn bị và đóng gói sản phẩm</p>
                                </div>
                            </div>
                            
                            <div class="timeline-item">
                                <div class="timeline-marker">
                                    <i class="fas fa-truck"></i>
                                </div>
                                <div class="timeline-content">
                                    <h6 class="timeline-title">Đang giao hàng</h6>
                                    <p class="timeline-text">Đơn hàng đang trên đường giao đến bạn</p>
                                </div>
                            </div>
                            
                            <div class="timeline-item">
                                <div class="timeline-marker">
                                    <i class="fas fa-home"></i>
                                </div>
                                <div class="timeline-content">
                                    <h6 class="timeline-title">Giao hàng thành công</h6>
                                    <p class="timeline-text">Đơn hàng đã được giao thành công</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Important Information -->
                <div class="row">
                    <div class="col-md-6 mb-4">
                        <div class="card h-100 border-info">
                            <div class="card-header bg-info text-white">
                                <h6 class="mb-0"><i class="fas fa-info-circle me-2"></i>Thông tin quan trọng</h6>
                            </div>
                            <div class="card-body">
                                <ul class="list-unstyled mb-0">
                                    <li class="mb-2">
                                        <i class="fas fa-clock text-primary me-2"></i>
                                        Thời gian giao hàng: 2-5 ngày làm việc
                                    </li>
                                    <li class="mb-2">
                                        <i class="fas fa-phone text-success me-2"></i>
                                        Hotline hỗ trợ: 1900 1234
                                    </li>
                                    <li class="mb-0">
                                        <i class="fas fa-envelope text-warning me-2"></i>
                                        Email: support@example.com
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 mb-4">
                        <div class="card h-100 border-warning">
                            <div class="card-header bg-warning text-dark">
                                <h6 class="mb-0"><i class="fas fa-exclamation-triangle me-2"></i>Lưu ý</h6>
                            </div>
                            <div class="card-body">
                                <ul class="list-unstyled mb-0">
                                    <li class="mb-2">
                                        <i class="fas fa-mobile-alt text-primary me-2"></i>
                                        Giữ máy để nhận cuộc gọi xác nhận
                                    </li>
                                    <li class="mb-2">
                                        <i class="fas fa-id-card text-info me-2"></i>
                                        Chuẩn bị CMND khi nhận hàng
                                    </li>
                                    <li class="mb-0">
                                        <i class="fas fa-money-bill-wave text-success me-2"></i>
                                        Kiểm tra hàng trước khi thanh toán
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="text-center">
                    <a href="<?= BASE_URL ?>/" class="btn btn-primary btn-lg me-3">
                        <i class="fas fa-home me-2"></i>Về trang chủ
                    </a>
                    <a href="<?= BASE_URL ?>/products" class="btn btn-outline-primary btn-lg">
                        <i class="fas fa-shopping-bag me-2"></i>Tiếp tục mua sắm
                    </a>
                </div>

                <!-- Social Sharing -->
                <div class="text-center mt-4">
                    <p class="text-muted mb-3">Chia sẻ trải nghiệm mua sắm với bạn bè:</p>
                    <div class="social-share">
                        <a href="#" class="btn btn-outline-primary btn-sm me-2">
                            <i class="fab fa-facebook-f"></i> Facebook
                        </a>
                        <a href="#" class="btn btn-outline-info btn-sm me-2">
                            <i class="fab fa-twitter"></i> Twitter
                        </a>
                        <a href="#" class="btn btn-outline-success btn-sm">
                            <i class="fab fa-whatsapp"></i> WhatsApp
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.success-icon {
    animation: checkmark 0.6s ease-in-out;
}

@keyframes checkmark {
    0% {
        transform: scale(0);
        opacity: 0;
    }
    50% {
        transform: scale(1.2);
    }
    100% {
        transform: scale(1);
        opacity: 1;
    }
}

.timeline {
    position: relative;
    padding-left: 0;
}

.timeline::before {
    content: '';
    position: absolute;
    left: 30px;
    top: 0;
    bottom: 0;
    width: 2px;
    background-color: #e9ecef;
}

.timeline-item {
    position: relative;
    padding-left: 70px;
    margin-bottom: 30px;
}

.timeline-item:last-child {
    margin-bottom: 0;
}

.timeline-marker {
    position: absolute;
    left: 18px;
    top: 0;
    width: 24px;
    height: 24px;
    border-radius: 50%;
    background-color: #e9ecef;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 12px;
    color: #6c757d;
    border: 3px solid #fff;
    box-shadow: 0 0 0 3px #e9ecef;
}

.timeline-item.active .timeline-marker {
    background-color: #28a745;
    color: white;
    box-shadow: 0 0 0 3px #28a745;
}

.timeline-item.active::before {
    content: '';
    position: absolute;
    left: 6px;
    top: 0;
    width: 36px;
    height: 2px;
    background-color: #28a745;
}

.timeline-title {
    margin-bottom: 5px;
    font-weight: 600;
}

.timeline-text {
    margin-bottom: 5px;
    color: #6c757d;
    font-size: 14px;
}

.timeline-time {
    font-size: 12px;
}

.timeline-item.active .timeline-title {
    color: #28a745;
}

.info-item {
    padding: 20px;
    text-align: center;
}

.info-item h5 {
    margin-bottom: 10px;
    color: #495057;
}

.social-share .btn {
    min-width: 100px;
}

/* Mobile Responsiveness */
@media (max-width: 768px) {
    .timeline {
        padding-left: 0;
    }
    
    .timeline::before {
        left: 15px;
    }
    
    .timeline-item {
        padding-left: 50px;
    }
    
    .timeline-marker {
        left: 3px;
        width: 20px;
        height: 20px;
        font-size: 10px;
    }
    
    .info-item {
        padding: 15px 10px;
    }
    
    .success-icon i {
        font-size: 3rem !important;
    }
    
    .display-5 {
        font-size: 2rem;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Animate timeline items
    const timelineItems = document.querySelectorAll('.timeline-item:not(.active)');
    
    // Add animation delay for future timeline items
    timelineItems.forEach((item, index) => {
        setTimeout(() => {
            item.style.opacity = '0.6';
            item.style.transform = 'translateX(10px)';
            item.style.transition = 'all 0.3s ease';
        }, (index + 1) * 200);
    });

    // Social sharing functionality
    const socialLinks = document.querySelectorAll('.social-share .btn');
    socialLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const platform = this.textContent.trim();
            const url = encodeURIComponent(window.location.href);
            const title = encodeURIComponent('Tôi vừa đặt hàng thành công tại website này!');
            
            let shareUrl = '';
            
            if (platform.includes('Facebook')) {
                shareUrl = `https://www.facebook.com/sharer/sharer.php?u=${url}`;
            } else if (platform.includes('Twitter')) {
                shareUrl = `https://twitter.com/intent/tweet?url=${url}&text=${title}`;
            } else if (platform.includes('WhatsApp')) {
                shareUrl = `https://wa.me/?text=${title}%20${url}`;
            }
            
            if (shareUrl) {
                window.open(shareUrl, '_blank', 'width=600,height=400');
            }
        });
    });

    // Show success confetti effect
    if (typeof confetti !== 'undefined') {
        confetti({
            particleCount: 100,
            spread: 70,
            origin: { y: 0.6 }
        });
    }
});
</script>

<?php include __DIR__ . '/../components/footer.php'; ?>

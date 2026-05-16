Hero Slider Section với dữ liệu động từ database
<div id="heroCarousel" class="carousel slide hero-carousel" data-bs-ride="carousel" data-bs-interval="5000">
    
    <!-- Indicators/Dots -->
    <div class="carousel-indicators">
        <?php if (!empty($data['sliders'])): ?>
            <?php foreach ($data['sliders'] as $index => $slider): ?>
                <button type="button" 
                        data-bs-target="#heroCarousel" 
                        data-bs-slide-to="<?= $index ?>" 
                        class="<?= $index === 0 ? 'active' : '' ?>"
                        aria-current="<?= $index === 0 ? 'true' : 'false' ?>"
                        aria-label="Slide <?= $index + 1 ?>">
                </button>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <!-- Slider Items -->
    <div class="carousel-inner">
        <?php if (!empty($data['sliders'])): ?>
            <?php foreach ($data['sliders'] as $index => $slider): ?>
                <div class="carousel-item <?= $index === 0 ? 'active' : '' ?>">
                    <!-- Background image được set bằng inline style từ database -->
                    <div class="hero-slide" style="background-image: url('<?= htmlspecialchars($slider->file_path) ?>');">
                        <!-- Overlay để làm tối ảnh nền -->
                        <div class="hero-overlay"></div>
                        
                        <!-- Container cho nội dung -->
                        <div class="container">
                            <div class="hero-content text-<?= htmlspecialchars($slider->metadata->text_position) ?>" 
                                 style="color: <?= htmlspecialchars($slider->metadata->text_color) ?>;">
                                
                                <!-- Tiêu đề chính -->
                                <h1 class="hero-title" data-aos="fade-up" data-aos-delay="200">
                                    <?= htmlspecialchars($slider->metadata->title) ?>
                                </h1>
                                
                                <!-- Tiêu đề phụ -->
                                <?php if (!empty($slider->metadata->subtitle)): ?>
                                    <h2 class="hero-subtitle" data-aos="fade-up" data-aos-delay="400">
                                        <?= htmlspecialchars($slider->metadata->subtitle) ?>
                                    </h2>
                                <?php endif; ?>
                                
                                <!-- Mô tả -->
                                <?php if (!empty($slider->metadata->description)): ?>
                                    <p class="hero-description" data-aos="fade-up" data-aos-delay="600">
                                        <?= htmlspecialchars($slider->metadata->description) ?>
                                    </p>
                                <?php endif; ?>
                                
                                <!-- Nút CTA -->
                                <?php if (!empty($slider->metadata->button_text) && !empty($slider->metadata->button_link)): ?>
                                    <div class="hero-actions" data-aos="fade-up" data-aos-delay="800">
                                        <a href="<?= htmlspecialchars($slider->metadata->button_link) ?>" 
                                           class="btn btn-primary btn-lg hero-cta-btn">
                                            <?= htmlspecialchars($slider->metadata->button_text) ?>
                                            <i class="fas fa-arrow-right ms-2"></i>
                                        </a>
                                    </div>
                                <?php endif; ?>
                                
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <!-- Fallback slide nếu không có dữ liệu -->
            <div class="carousel-item active">
                <div class="hero-slide" style="background-image: url('https://images.unsplash.com/photo-1515562141207-7a88fb7ce338?w=1200');">
                    <div class="hero-overlay"></div>
                    <div class="container">
                        <div class="hero-content text-center">
                            <h1 class="hero-title">Chào mừng đến với Jewelry Store</h1>
                            <p class="hero-description">Khám phá bộ sưu tập trang sức cao cấp</p>
                            <a href="<?= BASE_URL ?>/products" class="btn btn-primary btn-lg">Xem Sản Phẩm</a>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- Navigation Controls -->
    <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Previous</span>
    </button>
    <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Next</span>
    </button>
</div>

<!-- CSS bổ sung cho slider động -->
<style>
.hero-slide {
    height: 80vh;
    min-height: 600px;
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    display: flex;
    align-items: center;
    position: relative;
}

.hero-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(45deg, rgba(0,0,0,0.6) 0%, rgba(0,0,0,0.3) 100%);
    z-index: 1;
}

.hero-content {
    position: relative;
    z-index: 2;
    max-width: 600px;
}

.hero-title {
    font-size: 3.5rem;
    font-weight: 700;
    margin-bottom: 1rem;
    text-shadow: 2px 2px 4px rgba(0,0,0,0.5);
}

.hero-subtitle {
    font-size: 1.5rem;
    font-weight: 300;
    margin-bottom: 1.5rem;
    text-shadow: 1px 1px 2px rgba(0,0,0,0.5);
}

.hero-description {
    font-size: 1.1rem;
    line-height: 1.6;
    margin-bottom: 2rem;
    text-shadow: 1px 1px 2px rgba(0,0,0,0.5);
}

.hero-cta-btn {
    padding: 15px 30px;
    font-size: 1.1rem;
    border-radius: 50px;
    transition: all 0.3s ease;
}

.hero-cta-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(0,0,0,0.2);
}

/* Responsive */
@media (max-width: 768px) {
    .hero-slide {
        height: 60vh;
        min-height: 400px;
    }
    
    .hero-title {
        font-size: 2.5rem;
    }
    
    .hero-subtitle {
        font-size: 1.2rem;
    }
    
    .hero-content {
        text-align: center !important;
        padding: 0 20px;
    }
}
</style>

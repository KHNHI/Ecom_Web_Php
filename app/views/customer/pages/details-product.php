s<?php 
// Include URL helper if not already included
if (!function_exists('url')) {
    require_once __DIR__ . '/../../../../helpers/url_helper.php';
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chi Tiết Sản Phẩm</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <html xmlns:th="http://www.thymeleaf.org">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link href="<?= asset('css/css.css?v=' . time()) ?>" rel="stylesheet">
    <!-- SweetAlert2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">  
</head>
<body>
    <!-- Header -->
    <?php include __DIR__ . '/../components/header.php'; ?>
    
    <div class="main-content">
        <!-- Breadcrumb -->
        <div class="container" style="padding-top: 1rem;">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= url('') ?>" class="text-decoration-none">Trang chủ</a></li>
                    <li class="breadcrumb-item"><a href="<?= route('products') ?>" class="text-decoration-none">Trang sức</a></li>
                    <?php if (isset($product->categories) && !empty($product->categories)): ?>
                        <li class="breadcrumb-item"><a href="<?= route('products', ['category' => $product->categories[0]->category_id]) ?>" class="text-decoration-none"><?= htmlspecialchars($product->categories[0]->name) ?></a></li>
                    <?php endif; ?>
                    <li class="breadcrumb-item active"><?= isset($product) ? htmlspecialchars($product->name) : 'Chi tiết sản phẩm' ?></li>
                </ol>
            </nav>
        </div>

    <!-- Product Detail Section -->
    <?php
    // Đảm bảo data được khởi tạo đúng
    $product = isset($data['product']) ? $data['product'] : null;
    $reviewStats = (!empty($product) && !empty($product->review_stats)) ? $product->review_stats : null;
    $reviews = isset($data['reviews']) ? $data['reviews'] : [];
  $relatedProducts = $data['relatedProducts'] ?? []; // Thêm dòng này

    ?>
    
    <div class="container">
        <div class="row">
            <!-- Product Images -->
            <div class="col-lg-6">
                <div class="product-images">
                    <!-- Desktop Layout -->
                    <div class="row desktop-gallery">
                        <div class="col-2">
                            <div class="thumbnail-container">
                                <?php if (isset($product->images) && !empty($product->images)): ?>
                                    <?php foreach ($product->images as $index => $image): ?>
                                        <div class="thumbnail-item <?= $index === 0 ? 'active' : '' ?>" onclick="changeImage(this, '<?= asset($image->file_path) ?>')">
                                            <img src="<?= asset($image->file_path) ?>" alt="<?= htmlspecialchars($image->alt_text ?: $product->name) ?>">
                                        </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <!-- Fallback images if no images in database -->
                                    <div class="thumbnail-item active" onclick="changeImage(this, 'https://images.unsplash.com/photo-1603561591411-07134e71a2a9?w=500', 0)">
                                        <img src="https://images.unsplash.com/photo-1603561591411-07134e71a2a9?w=100" alt="Thumbnail 1">
                                    </div>
                                    <div class="thumbnail-item" onclick="changeImage(this, 'https://images.unsplash.com/photo-1605100804763-247f67b3557e?w=500', 1)">
                                        <img src="https://images.unsplash.com/photo-1605100804763-247f67b3557e?w=100" alt="Thumbnail 2">
                                    </div>
                                    <div class="thumbnail-item" onclick="changeImage(this, 'https://images.unsplash.com/photo-1515562141207-7a88fb7ce338?w=500', 2)">
                                        <img src="https://images.unsplash.com/photo-1515562141207-7a88fb7ce338?w=100" alt="Thumbnail 3">
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="col-10" style="padding-left: 30px;">
                            <div class="main-image">
                                <img id="mainImage" 
                                     src="<?= isset($product->primary_image) ? asset($product->primary_image->file_path) : (isset($product->images[0]) ? asset($product->images[0]->file_path) : 'https://images.unsplash.com/photo-1603561591411-07134e71a2a9?w=500') ?>" 
                                     alt="<?= isset($product) ? htmlspecialchars($product->name) : 'Product Image' ?>">
                            </div>
                        </div>
                    </div>

                    <!-- Mobile Slider Layout -->
                    <div class="mobile-gallery">
                        <div class="gallery-slider">
                            <?php
                            // Ensure we have the product images array
                            $productImages = [];
                            if (isset($product->images) && !empty($product->images)) {
                                $productImages = $product->images;
                            } elseif (isset($product->primary_image)) {
                                $productImages[] = $product->primary_image;
                            }

                            // If we still have no images, use defaults
                            if (empty($productImages)) {
                                $productImages = [
                                    (object)['file_path' => 'https://images.unsplash.com/photo-1603561591411-07134e71a2a9?w=500', 'alt_text' => 'Default Product Image 1'],
                                    (object)['file_path' => 'https://images.unsplash.com/photo-1605100804763-247f67b3557e?w=500', 'alt_text' => 'Default Product Image 2'],
                                    (object)['file_path' => 'https://images.unsplash.com/photo-1515562141207-7a88fb7ce338?w=500', 'alt_text' => 'Default Product Image 3']
                                ];
                            }

                            // Generate slider slides
                            foreach ($productImages as $index => $image):
                                $isActive = $index === 0 ? 'active' : '';
                                $altText = isset($image->alt_text) && !empty($image->alt_text) 
                                    ? htmlspecialchars($image->alt_text) 
                                    : htmlspecialchars($product->name ?? 'Product Image');
                            ?>
                                <div class="gallery-slide <?= $isActive ?>" data-index="<?= $index ?>">
                                    <img src="<?= $image->file_path ?>" 
                                         alt="<?= $altText ?>"
                                         loading="lazy">
                                </div>
                            <?php endforeach; ?>
                        </div>
                        
                        <!-- Gallery Navigation -->
                        <button class="gallery-nav prev" onclick="prevSlide()" aria-label="Previous image">
                            <i class="fas fa-chevron-left"></i>
                        </button>
                        <button class="gallery-nav next" onclick="nextSlide()" aria-label="Next image">
                            <i class="fas fa-chevron-right"></i>
                        </button>

                        <!-- Gallery Dots -->
                        <div class="gallery-dots">
                            <?php foreach ($productImages as $index => $image): ?>
                                <button class="gallery-dot <?= $index === 0 ? 'active' : '' ?>" 
                                        onclick="goToSlide(<?= $index ?>)"
                                        aria-label="Go to image <?= $index + 1 ?>"></button>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Product Info -->
            <div class="col-lg-6" style="padding-left: 30px;">
                <h1 class="product-title"><?= isset($product) ? htmlspecialchars($product->name) : 'Tên sản phẩm' ?></h1>
                
                <div class="rating-reviews d-flex align-items-center mb-3">
                    <div class="rating">
                        <?php 
                        $avgRating = ($reviewStats && isset($reviewStats->average_rating)) 
                            ? round($reviewStats->average_rating) 
                            : 5;
                        for ($i = 1; $i <= 5; $i++): ?>
                            <i class="<?= $i <= $avgRating ? 'fas' : 'far' ?> fa-star"></i>
                        <?php endfor; ?>
                    </div>
                    <span class="text-muted">(<?= ($reviewStats && isset($reviewStats->total_reviews)) ? $reviewStats->total_reviews : 0 ?> đánh giá)</span>
                    <span class="badge bg-success ms-3">Còn hàng</span>
                </div>

                <div class="price"><?= isset($product) ? number_format($product->base_price, 0, ',', '.') : '0' ?> ₫</div>

                <p class="product-description text-muted">
                    <?= isset($product) ? htmlspecialchars($product->description) : 'Mô tả sản phẩm sẽ được hiển thị ở đây.' ?>
                </p>

                <div class="product-options">
                    <?php if (isset($product->variant_options) && !empty($product->variant_options['colors'])): ?>
                    <div class="option-group">
                        <label>Loại:</label>
                        <div class="color-options type-options">
                            <?php foreach ($product->variant_options['colors'] as $index => $color): ?>
                                <button class="option-btn <?= $index === 0 ? 'active' : '' ?>" onclick="selectOption(this)">
                                    <?= htmlspecialchars($color) ?>
                                </button>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endif; ?>

                    <?php if (isset($product->variant_options) && !empty($product->variant_options['sizes'])): ?>
                    <div class="option-group">
                        <label>Size:</label>
                        <div class="size-options type-options">
                            <?php foreach ($product->variant_options['sizes'] as $index => $size): ?>
                                <button class="option-btn <?= $index === 0 ? 'active' : '' ?>" onclick="selectOption(this)">
                                    <?= htmlspecialchars($size) ?>
                                </button>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Product action row: quantity | add to cart | buy now -->
                <div class="product-actions-wrapper">
                    <div class="product-actions d-flex align-items-center w-100">
                        <div class="quantity-box me-3">
                            <input type="number" class="quantity-input form-control" value="1" min="1" id="quantityInput">
                        </div>

                        <button class="btn-add-to-cart me-3 d-flex align-items-center" onclick="addToCart()" id="addToCartBtn">
                            <i class="fas fa-shopping-cart me-2"></i>
                            <span>Thêm vào giỏ hàng</span>
                        </button>

                        <button class="btn-buy ms-auto d-flex align-items-center" onclick="buyNow()" id="buyNowBtn">
                            <span>Mua Ngay</span>
                        </button>
                    </div>
                </div>

                <div class="delivery-info">
                    <div class="delivery-item">
                        <i class="fas fa-shipping-fast delivery-icon"></i>
                        <div>
                            <strong>Miễn phí vận chuyển</strong><br>
                            <small class="text-muted">Nhập mã bưu điện để kiểm tra tình trạng giao hàng</small>
                        </div>
                    </div>
                    <div class="delivery-item">
                        <i class="fas fa-undo delivery-icon"></i>
                        <div>
                            <strong>Đổi trả miễn phí</strong><br>
                            <small class="text-muted">Đổi trả miễn phí trong 30 ngày. Chi tiết chính sách đổi trả</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Product Tabs -->
    <div class="container">
        <div class="product-tabs">
            <div class="tab-navigation">
                <button class="tab-btn active" onclick="switchTab(this, 'description')">Mô tả sản phẩm</button>
                <button class="tab-btn" onclick="switchTab(this, 'additional')">Thông tin bổ sung</button>
                <button class="tab-btn" onclick="switchTab(this, 'reviews')">Đánh giá (<?= ($reviewStats && isset($reviewStats->total_reviews)) ? $reviewStats->total_reviews : 0 ?>)</button>
            </div>

            <div class="tab-content active" id="description">
                <div class="row">
                    <div class="col-md-8">
                    
                        <?php
                        if (isset($product) && !empty($product->description)) {
                            echo '<h4>Mô tả chi tiết sản phẩm</h4>';
                            echo '<p>' . htmlspecialchars($product->description) . '</p>';
                        } else {
                            echo '<h4>Mô tả chi tiết sản phẩm</h4>';
                            echo '<p>Thông tin mô tả sẽ được cập nhật sau.</p>';
                        }?>
                    </div>
                    <div class="col-md-4">
                        <div class="inf-images">
                            <img src="<?php echo (isset($product) && isset($product->images[0])) ? asset($product->images[0]->file_path) : ''; ?>" alt="Chi tiết ">
                        </div>
                    </div>
                </div>
            </div>

            <div class="tab-content" id="additional">
                <h4>Thông tin kỹ thuật</h4>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <tbody>
                            <tr>
                                <td><strong>Chất liệu</strong></td>
                                <td>Vàng trắng 18K (75% vàng, 25% kim loại khác)</td>
                            </tr>
                            <tr>
                                <td><strong>Trọng lượng vàng</strong></td>
                                <td>8.5 gram</td>
                            </tr>
                            <tr>
                                <td><strong>Kim cương chính</strong></td>
                                <td>1.2 carat, IF, màu D, giác cắt Round Brilliant</td>
                            </tr>
                            <tr>
                                <td><strong>Kim cương phụ</strong></td>
                                <td>24 viên, tổng 0.5 carat, VS1-VS2, màu E-F</td>
                            </tr>
                            <tr>
                                <td><strong>Size có sẵn</strong></td>
                                <td>5, 6, 7, 8, 9 (có thể điều chỉnh theo yêu cầu)</td>
                            </tr>
                            <tr>
                                <td><strong>Xuất xứ</strong></td>
                                <td>Việt Nam</td>
                            </tr>
                            <tr>
                                <td><strong>Chứng nhận</strong></td>
                                <td>GIA Certificate, PNJ Warranty</td>
                            </tr>
                            <tr>
                                <td><strong>Bảo hành</strong></td>
                                <td>2 năm toàn diện</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="tab-content" id="reviews">
                <h4>Đánh giá từ khách hàng</h4>
                <?php if ($reviewStats && $reviewStats->total_reviews > 0): ?>
                <div class="row">
                    <div class="col-md-4">
                        <div class="text-center">
                            <h2 class="display-4"><?= number_format($reviewStats->average_rating, 1) ?></h2>
                            <div class="rating mb-2">
                                <?php 
                                // require_once __DIR__ . '/../../services/ReviewService.php';
                                $reviewService = new ReviewService();
                                echo $reviewService->renderStars($reviewStats->average_rating);
                                ?>
                            </div>
                            <p class="text-muted">Dựa trên <?= $reviewStats->total_reviews ?> đánh giá</p>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <?php if (!empty($data['product']->reviews)): ?>
                            <?php foreach ($data['product']->reviews as $index => $review): ?>
                            <div class="review-item <?= $index < count($data['product']->reviews) - 1 ? 'border-bottom pb-3 mb-3' : '' ?>">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <h6 class="mb-1"><?= htmlspecialchars($review->reviewer_name) ?></h6>
                                        <?php if (!empty($review->title)): ?>
                                        <p class="fw-bold text-dark mb-1">"<?= htmlspecialchars($review->title) ?>"</p>
                                        <?php endif; ?>
                                    </div>
                                    <div class="rating">
                                        <?= $reviewService->renderStars($review->rating) ?>
                                    </div>
                                </div>
                                <p class="mb-1"><?= htmlspecialchars($review->comment) ?></p>
                                <small class="text-muted"><?= $reviewService->formatReviewTime($review->created_at) ?></small>
                            </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p class="text-muted">Chưa có đánh giá nào.</p>
                        <?php endif; ?>
                    </div>
                </div>
                <?php else: ?>
                <div class="text-center py-5">
                    <i class="fas fa-star fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">Chưa có đánh giá nào</h5>
                    <p class="text-muted">Hãy là người đầu tiên đánh giá sản phẩm này!</p>
                </div>
                <?php endif; ?>
                
                <!-- Add Review Form -->
                <div class="mt-5">
                    <h5>Viết đánh giá của bạn</h5>
                    <?php if (isset($_SESSION['user_id'])): ?>
                    <div class="card">
                        <div class="card-body">
                            <form id="reviewForm" class="needs-validation" novalidate>
                                <input type="hidden" id="productId" value="<?= $data['product']->product_id ?>">
                                
                                <div class="mb-3">
                                    <label class="form-label">Đánh giá của bạn *</label>
                                    <div class="rating-input">
                                        <input type="radio" name="rating" value="5" id="star5">
                                        <label for="star5"><i class="fas fa-star"></i></label>
                                        <input type="radio" name="rating" value="4" id="star4">
                                        <label for="star4"><i class="fas fa-star"></i></label>
                                        <input type="radio" name="rating" value="3" id="star3">
                                        <label for="star3"><i class="fas fa-star"></i></label>
                                        <input type="radio" name="rating" value="2" id="star2">
                                        <label for="star2"><i class="fas fa-star"></i></label>
                                        <input type="radio" name="rating" value="1" id="star1">
                                        <label for="star1"><i class="fas fa-star"></i></label>
                                    </div>
                                    <div class="invalid-feedback">Vui lòng chọn số sao đánh giá.</div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="reviewTitle" class="form-label">Tiêu đề đánh giá *</label>
                                    <input type="text" class="form-control" id="reviewTitle" 
                                           placeholder="Nhập tiêu đề đánh giá..." maxlength="200" required>
                                    <div class="invalid-feedback">Vui lòng nhập tiêu đề đánh giá.</div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="reviewComment" class="form-label">Nhận xét của bạn *</label>
                                    <textarea class="form-control" id="reviewComment" rows="4" 
                                              placeholder="Chia sẻ trải nghiệm của bạn về sản phẩm này..." required></textarea>
                                    <div class="invalid-feedback">Vui lòng nhập nhận xét của bạn.</div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="reviewerName" class="form-label">Tên của bạn *</label>
                                    <input type="text" class="form-control" id="reviewerName" 
                                           value="<?= isset($_SESSION['user_name']) ? htmlspecialchars($_SESSION['user_name']) : '' ?>"
                                           placeholder="Nhập tên của bạn..." required>
                                    <div class="invalid-feedback">Vui lòng nhập tên của bạn.</div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="reviewerEmail" class="form-label">Email *</label>
                                    <input type="email" class="form-control" id="reviewerEmail" 
                                           value="<?= isset($_SESSION['user_email']) ? htmlspecialchars($_SESSION['user_email']) : '' ?>"
                                           placeholder="Nhập email của bạn..." required>
                                    <div class="invalid-feedback">Vui lòng nhập email hợp lệ.</div>
                                </div>
                                
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-paper-plane me-2"></i>Gửi đánh giá
                                </button>
                            </form>
                        </div>
                    </div>
                    <?php else: ?>
                    <div class="card">
                        <div class="card-body text-center py-5">
                            <i class="fas fa-user-lock fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted mb-3">Đăng nhập để đánh giá sản phẩm</h5>
                            <p class="text-muted mb-4">Bạn cần đăng nhập để có thể viết đánh giá và chia sẻ trải nghiệm về sản phẩm này.</p>
                            <div class="d-flex gap-3 justify-content-center">
                                <a href="<?= url('auth/signin') ?>" class="btn btn-primary">
                                    <i class="fas fa-sign-in-alt me-2"></i>Đăng nhập
                                </a>
                                <a href="<?= url('auth/signup') ?>" class="btn btn-outline-secondary">
                                    <i class="fas fa-user-plus me-2"></i>Đăng ký
                                </a>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Related Products -->
    <?php if (isset($relatedProducts) && !empty($relatedProducts)): ?>
    <div class="container">
        <div class="related-products">
            <h3 class="mb-4">Sản phẩm cùng Bộ sưu tập</h3>
            <div class="row">
                        <?php foreach ($relatedProducts as $relatedProduct): ?>
                            <div class="col-lg-3 col-md-6 mb-4">
                                <div class="related-product-card">
                                    <a href="<?= route('product/' . ($relatedProduct->slug ?? $relatedProduct->product_id)) ?>" style="text-decoration: none; color: inherit; display: block;">
                                        <div class="product-image">
                                            <img src="<?= $relatedProduct->primary_image ? asset($relatedProduct->primary_image->file_path) : 'https://images.unsplash.com/photo-1605100804763-247f67b3557e?w=300' ?>" 
                                                 alt="<?= htmlspecialchars($relatedProduct->name) ?>">
                                           
                                            
                                        </div>
                                        <h6 class="mt-3"><?= htmlspecialchars($relatedProduct->name) ?></h6>
                                        <div class="rating mb-2">
                                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                                <i class="fas fa-star"></i>
                                            <?php endfor; ?>
                                            <small class="text-muted ms-1">(<?= rand(50, 200) ?>)</small>
                                        </div>
                                        <div class="product-price">
                                            <span><?= number_format($relatedProduct->base_price, 0, ',', '.') ?> ₫</span>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        <?php endforeach; ?>
            </div>
        </div>
    </div>
    <?php endif; ?>


    </div> <!-- End main-content -->

<?php include __DIR__ . '/../components/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
<!-- SweetAlert2 JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
<!-- Test if JS file loads -->
<script src="<?= asset('js/product-detail.js') ?>" defer></script>

<style>
/* Navigation Spacing Fix */
body {
    padding-top: 0; /* Reset any default padding */
}

.main-content {
    margin-top: 100px; /* Space for fixed navigation */
}

/* Responsive navigation spacing */
@media (max-width: 991px) {
    .main-content {
        margin-top: 80px; /* Less space on mobile */
    }
}

/* Rating Input Styles */
.rating-input {
    display: flex;
    flex-direction: row-reverse;
    justify-content: flex-end;
}

.rating-input input[type="radio"] {
    display: none;
}

.rating-input label {
    color: #ddd;
    font-size: 1.5rem;
    cursor: pointer;
    margin-right: 5px;
    transition: color 0.2s;
}

.rating-input label:hover,
.rating-input label:hover ~ label,
.rating-input input[type="radio"]:checked ~ label {
    color: #ffc107;
}

.rating-input label:hover {
    transform: scale(1.1);
}

/* Review Cards */
.review-item {
    transition: background-color 0.2s;
    padding: 15px;
    border-radius: 8px;
}

.review-item:hover {
    background-color: #f8f9fa;
}

/* Product action/buttons layout and responsive behaviour */
.product-actions {
    display: flex;
    gap: 8px;
    justify-content: space-between;
    align-items: center;
    width: 100%;
    
}

.product-actions .quantity-box {
    flex: 0 0 auto;
}

.product-actions .quantity-input {
    width: 90px;
}

.product-actions .btn-add-to-cart {
    flex: 0 0 auto;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 10px 16px;
    border-radius: 8px;
    border: 2px solid #ffd455ff;
    background: white;
    color: #ffd455ff;
    font-weight: 600;
    font-size: 14px;
    text-transform: uppercase;
    transition: all 0.25s ease;
    width: auto;       /* bỏ width cố định */
    margin-left: auto !important; /* căn lề trái tự động */
}

.product-actions .btn-add-to-cart:hover {
    background: var(--gold);
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(212,175,55,0.18);
}

.product-actions .btn-buy {
    flex: 1 1 auto;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 12px 22px;
    border-radius: 8px;
    background: var(--gold);
    color: white;
    font-weight: 700;
    font-size: 14px;
    text-transform: uppercase;
    letter-spacing: 0.6px;
    transition: all 0.25s ease;
}

.product-actions .btn-buy:hover {
    background: var(--dark-gold);
    transform: translateY(-2px);
    box-shadow: 0 8px 24px rgba(212,175,55,0.22);
}

/* Desktop: keep all in one row; Mobile: move buy button to full-width second row */
@media (max-width: 768px) {
    .product-actions {
        flex-wrap: wrap;
    }

    /* quantity + add-to-cart stay on first row */
    .product-actions .quantity-box,
    .product-actions .btn-add-to-cart {
        order: 1;
        flex: 0 0 auto;
    }

    /* buy button below, full width */
    .product-actions .btn-buy {
        order: 2;
        flex-basis: 100%;
        width: 100%;
        margin-top: 8px;
    }

    /* keep add-to-cart button text visible on small screens */
    .product-actions .btn-add-to-cart { padding: 10px 16px; width: auto; justify-content: center; }
}

/* Tab Content Visibility */
.tab-content {
    display: none;
}

.tab-content.active {
    display: block;
}
</style>

<script>
// Gallery Slider Functions
document.addEventListener('DOMContentLoaded', function() {
    let currentSlide = 0;
    const slides = document.querySelectorAll('.gallery-slide');
    const dots = document.querySelectorAll('.gallery-dot');
    let touchStartX = 0;
    let touchEndX = 0;
    const slider = document.querySelector('.gallery-slider');

    // Touch events for mobile swipe
    if (slider) {
        slider.addEventListener('touchstart', e => {
            touchStartX = e.changedTouches[0].screenX;
        }, false);

        slider.addEventListener('touchend', e => {
            touchEndX = e.changedTouches[0].screenX;
            handleSwipe();
        }, false);
    }

    function handleSwipe() {
        const swipeThreshold = 50;
        const difference = touchStartX - touchEndX;
        
        if (Math.abs(difference) > swipeThreshold) {
            if (difference > 0) {
                // Swiped left
                nextSlide();
            } else {
                // Swiped right
                prevSlide();
            }
        }
    }

    window.showSlide = function(index) {
        if (!slides.length) return;
        
        if (index >= slides.length) index = 0;
        if (index < 0) index = slides.length - 1;
        
        // Remove active class from all slides and dots
        slides.forEach(slide => slide.classList.remove('active'));
        dots.forEach(dot => dot.classList.remove('active'));
        
        // Add active class to current slide and dot
        slides[index].classList.add('active');
        dots[index].classList.add('active');
        
        currentSlide = index;
        
        // Update thumbnail selection if on desktop
        const thumbnails = document.querySelectorAll('.thumbnail-item');
        if (thumbnails.length) {
            thumbnails.forEach(thumb => thumb.classList.remove('active'));
            if (thumbnails[index]) thumbnails[index].classList.add('active');
        }
    }

    window.nextSlide = function() {
        showSlide(currentSlide + 1);
    }

    window.prevSlide = function() {
        showSlide(currentSlide - 1);
    }

    window.goToSlide = function(index) {
        showSlide(index);
    }

    // Auto hide navigation buttons if only one slide
    if (slides.length <= 1) {
        const navButtons = document.querySelectorAll('.gallery-nav');
        navButtons.forEach(btn => btn.style.display = 'none');
        const dotsContainer = document.querySelector('.gallery-dots');
        if (dotsContainer) dotsContainer.style.display = 'none';
    }

    // Add keyboard navigation
    document.addEventListener('keydown', function(e) {
        if (e.key === 'ArrowLeft') {
            prevSlide();
        } else if (e.key === 'ArrowRight') {
            nextSlide();
        }
    });
});

// Quantity Control Functions
function updateQuantity(action) {
    const input = document.getElementById('quantityInput');
    let value = parseInt(input.value);
    
    if (action === 'increase') {
        value++;
    } else if (action === 'decrease' && value > 1) {
        value--;
    }
    
    input.value = value;
}

// Update image gallery for both mobile and desktop
function changeImage(element, imagePath, index) {
    // Update desktop view
    document.getElementById('mainImage').src = imagePath;
    document.querySelectorAll('.thumbnail-item').forEach(item => item.classList.remove('active'));
    element.classList.add('active');
    
    // Update mobile slider
    showSlide(index);
}

/**
 * Add product to cart
 */
function addToCart() {
    const productId = <?= isset($product->product_id) ? $product->product_id : 0 ?>;
    const quantity = parseInt(document.getElementById('quantityInput').value) || 1;
    const addToCartBtn = document.getElementById('addToCartBtn');
    
    // Get selected options
    const selectedColor = document.querySelector('.color-options .option-btn.active')?.textContent?.trim() || '';
    const selectedSize = document.querySelector('.size-options .option-btn.active')?.textContent?.trim() || '';
    
    // Debug logging
    console.log('Selected color:', selectedColor);
    console.log('Selected size:', selectedSize);
    console.log('Product ID:', productId);
    console.log('Quantity:', quantity);
    
    // Validate product
    if (!productId) {
        showToast('error', 'Không tìm thấy thông tin sản phẩm');
        return;
    }
    
    if (quantity < 1) {
        showToast('error', 'Số lượng phải lớn hơn 0');
        return;
    }

    // Show loading state
    const originalHtml = addToCartBtn.innerHTML;
    addToCartBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Đang thêm...';
    addToCartBtn.disabled = true;

    // Prepare form data
    const formData = new FormData();
    formData.append('product_id', productId);
    formData.append('quantity', quantity);
    formData.append('color', selectedColor);
    formData.append('size', selectedSize);

    // Submit to cart
    const cartUrl = '<?= url('cart/add') ?>';
    console.log('Cart URL:', cartUrl);
    
    fetch(cartUrl, {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => {
        console.log('Response status:', response.status);
        console.log('Response ok:', response.ok);
        
        // Check if response is ok
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        // Get response text first to debug
        return response.text().then(text => {
            console.log('Raw response:', text);
            try {
                return JSON.parse(text);
            } catch (e) {
                console.error('JSON parse error:', e);
                console.error('Raw text:', text);
                throw new Error('Invalid JSON response from server');
            }
        });
    })
    .then(data => {
        // Restore button state
        addToCartBtn.innerHTML = originalHtml;
        addToCartBtn.disabled = false;

        if (data.success) {
            // Update cart badge if function exists
            if (typeof updateCartBadge === 'function') {
                updateCartBadge();
            }
            
            // Show success message with action buttons
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'success',
                    title: 'Thêm vào giỏ hàng thành công!',
                    text: `Đã thêm ${quantity} sản phẩm vào giỏ hàng`,
                    showCancelButton: true,
                    confirmButtonText: '<i class="fas fa-shopping-cart me-1"></i>Xem giỏ hàng',
                    cancelButtonText: 'Tiếp tục mua sắm',
                    confirmButtonColor: '#28a745',
                    cancelButtonColor: '#6c757d'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = '<?= url('cart') ?>';
                    }
                });
            } else {
                // Fallback if SweetAlert2 not loaded
                alert('Thêm vào giỏ hàng thành công!');
            }
        } else {
            showToast('error', data.message || 'Có lỗi xảy ra khi thêm vào giỏ hàng');
        }
    })
    .catch(error => {
        // Restore button state
        addToCartBtn.innerHTML = originalHtml;
        addToCartBtn.disabled = false;
        
        console.error('Error:', error);
        showToast('error', 'Có lỗi kết nối xảy ra. Vui lòng thử lại.');
    });
}

/**
 * Buy now - Add to cart and redirect to checkout
 */
function buyNow() {
    const productId = <?= isset($product->product_id) ? $product->product_id : 0 ?>;
    const quantity = parseInt(document.getElementById('quantityInput').value) || 1;
    
    // Get selected options
    const selectedColor = document.querySelector('.color-options .option-btn.active')?.textContent?.trim() || '';
    const selectedSize = document.querySelector('.size-options .option-btn.active')?.textContent?.trim() || '';
    
    console.log('BuyNow - Selected Color:', selectedColor);
    console.log('BuyNow - Selected Size:', selectedSize);
    
    // Validate product
    if (!productId) {
        showToast('error', 'Không tìm thấy thông tin sản phẩm');
        return;
    }
    
    if (quantity < 1) {
        showToast('error', 'Số lượng phải lớn hơn 0');
        return;
    }

    // Show loading
    Swal.fire({
        title: 'Đang xử lý...',
        text: 'Vui lòng đợi trong giây lát',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    // Prepare form data
    const formData = new FormData();
    formData.append('product_id', productId);
    formData.append('quantity', quantity);
    formData.append('color', selectedColor);
    formData.append('size', selectedSize);

    // Use buyNow endpoint
    const buyNowUrl = '<?= url('cart/buynow') ?>';
    
    fetch(buyNowUrl, {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        Swal.close();
        
        console.log('BuyNow - Server Response:', data);
        
        if (data.success) {
            // Update cart badge
            if (typeof updateCartBadge === 'function') {
                updateCartBadge();
            }
            
            // Redirect to checkout with buy_now parameter
            console.log('BuyNow - Redirecting to checkout...');
            window.location.href = '<?= url('checkout') ?>?buy_now=1';
        } else {
            console.log('BuyNow - Error:', data.message);
            showToast('error', data.message || 'Có lỗi xảy ra khi thêm vào giỏ hàng');
        }
    })
    .catch(error => {
        Swal.close();
        console.error('Error:', error);
        showToast('error', 'Có lỗi kết nối xảy ra. Vui lòng thử lại.');
    });
}

/**
 * Show toast notification
 */
function showToast(type, message) {
    if (typeof Swal !== 'undefined') {
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer)
                toast.addEventListener('mouseleave', Swal.resumeTimer)
            }
        });

        Toast.fire({
            icon: type,
            title: message
        });
    } else {
        // Fallback if SweetAlert2 not loaded
        alert(message);
    }
}

/**
 * Select product option (color/size)
 */
function selectOption(element) {
    // Remove active class from siblings
    const siblings = element.parentElement.querySelectorAll('.option-btn');
    siblings.forEach(btn => btn.classList.remove('active'));
    
    // Add active class to clicked element
    element.classList.add('active');
    initializeReviewForm();


}

// Initialize quantity controls
document.addEventListener('DOMContentLoaded', function() {
    const quantityInput = document.getElementById('quantityInput');
    
    if (quantityInput) {
        quantityInput.addEventListener('change', function() {
            const value = parseInt(this.value);
            if (isNaN(value) || value < 1) {
                this.value = 1;
            }
        });
    }
    
    // Initialize review form
    initializeReviewForm();
});
function initializeReviewForm() {
    const reviewForm = document.getElementById('reviewForm');
    if (!reviewForm) return;

    reviewForm.addEventListener('submit', function(event) {
        event.preventDefault();
        
        // Validate form
        if (!this.checkValidity()) {
            event.stopPropagation();
            this.classList.add('was-validated');
            return;
        }

        // Check if rating is selected
        const rating = this.querySelector('input[name="rating"]:checked');
        if (!rating) {
            showToast('error', 'Vui lòng chọn số sao đánh giá');
            return;
        }

        // Get form data
        const formData = new FormData();
        formData.append('product_id', document.getElementById('productId').value);
        formData.append('rating', rating.value);
        formData.append('title', document.getElementById('reviewTitle').value.trim());
        formData.append('comment', document.getElementById('reviewComment').value.trim());

        // Show loading
        const submitBtn = this.querySelector('button[type="submit"]');
        const originalBtnText = submitBtn.innerHTML;
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Đang gửi...';

        // Submit review
        fetch('<?= url('api/reviews/add') ?>', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => {
            // Check if response is ok
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            // Check content type
            const contentType = response.headers.get('content-type');
            if (!contentType || !contentType.includes('application/json')) {
                return response.text().then(text => {
                    console.error('Non-JSON response:', text);
                    throw new Error('Server returned non-JSON response');
                });
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                // Show success message
                Swal.fire({
                    icon: 'success',
                    title: 'Thành công!',
                    text: data.message,
                    showConfirmButton: true,
                    confirmButtonText: 'OK'
                }).then(() => {
                    // Reset form
                    reviewForm.reset();
                    reviewForm.classList.remove('was-validated');
                    
                    // Reset star rating display
                    const stars = reviewForm.querySelectorAll('.rating-input label');
                    stars.forEach(star => star.style.color = '#ddd');
                });
            } else {
                // Show error message
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: data.message || 'Có lỗi xảy ra khi gửi đánh giá',
                });
                
                // Handle redirect if needed (e.g., to login)
                if (data.redirect) {
                    setTimeout(() => {
                        window.location.href = data.redirect;
                    }, 2000);
                }
            }
        })
        .catch(error => {
            console.error('Review submission error:', error);
            Swal.fire({
                icon: 'error',
                title: 'Lỗi kết nối',
                text: 'Không thể gửi đánh giá, vui lòng kiểm tra kết nối mạng.',
            });
        })
        .finally(() => {
            // Reset button
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalBtnText;
        });
    });

    // Handle star rating visual feedback
    const ratingInputs = reviewForm.querySelectorAll('input[name="rating"]');
    const ratingLabels = reviewForm.querySelectorAll('.rating-input label');
    
    ratingInputs.forEach((input, index) => {
        input.addEventListener('change', function() {
            const rating = parseInt(this.value);
            ratingLabels.forEach((label, labelIndex) => {
                if (labelIndex >= (5 - rating)) {
                    label.style.color = '#ffc107';
                } else {
                    label.style.color = '#ddd';
                }
            });
        });
    });
}

</script>


<?php
$title = $data['title'] ?? 'Danh sách yêu thích';
$wishlistItems = $data['wishlistItems'] ?? [];
$wishlist = $data['wishlist'] ?? null;
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?> - Jewelry Store</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <style>
        body {
            background-color: #f8f9fa;
        }
        
        .wishlist-header {
            background: white;
            border-bottom: 1px solid #dee2e6;
            padding: 2rem 0;
            margin-bottom: 2rem;
        }
        
        .product-card {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 12px rgba(0,0,0,0.08);
            transition: all 0.3s ease;
            position: relative;
        }
        
        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }
        
        .product-image {
            width: 100%;
            height: 250px;
            object-fit: cover;
        }
        
        .product-info {
            padding: 1.5rem;
        }
        
        .product-title {
            font-size: 1.1rem;
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 0.5rem;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        
        .product-price {
            font-size: 1.25rem;
            font-weight: 700;
            color: #e74c3c;
            margin-bottom: 1rem;
        }
        
        .btn-remove {
            position: absolute;
            top: 10px;
            right: 10px;
            width: 35px;
            height: 35px;
            border-radius: 50%;
            background: rgba(255,255,255,0.9);
            border: none;
            color: #e74c3c;
            font-size: 1.1rem;
            transition: all 0.3s ease;
        }
        
        .btn-remove:hover {
            background: #e74c3c;
            color: white;
            transform: scale(1.1);
        }
        
        .btn-add-cart {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            color: white;
            padding: 0.6rem 1.5rem;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        
        .btn-add-cart:hover {
            background: linear-gradient(135deg, #5a67d8 0%, #6b46c1 100%);
            color: white;
            transform: translateY(-2px);
        }
        
        .empty-wishlist {
            text-align: center;
            padding: 4rem 0;
        }
        
        .empty-wishlist i {
            font-size: 4rem;
            color: #bdc3c7;
            margin-bottom: 1rem;
        }
        
        .empty-wishlist h3 {
            color: #7f8c8d;
            margin-bottom: 1rem;
        }
        
        .btn-continue {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            color: white;
            padding: 0.8rem 2rem;
            border-radius: 8px;
            font-weight: 500;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s ease;
        }
        
        .btn-continue:hover {
            background: linear-gradient(135deg, #5a67d8 0%, #6b46c1 100%);
            color: white;
            transform: translateY(-2px);
        }
        
        .wishlist-actions {
            margin-bottom: 2rem;
        }
        
        .btn-clear-all {
            background: #e74c3c;
            border: none;
            color: white;
            padding: 0.6rem 1.5rem;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        
        .btn-clear-all:hover {
            background: #c0392b;
            color: white;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <?php include __DIR__ . '/../components/header.php'; ?>
    
    <!-- Wishlist Header -->
    <div class="wishlist-header">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h1 class="mb-0">
                        <i class="fas fa-heart me-2 text-danger"></i>
                        Danh sách yêu thích
                    </h1>
                    <p class="text-muted mb-0 mt-2">
                        <?= count($wishlistItems) ?> sản phẩm trong danh sách yêu thích của bạn
                    </p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Main Content -->
    <div class="container mb-5">
        <?php if (!empty($wishlistItems)): ?>
            <!-- Wishlist Actions -->
            <div class="wishlist-actions">
                <div class="row">
                    <div class="col-md-12 text-end">
                        <button class="btn btn-clear-all" onclick="clearWishlist()">
                            <i class="fas fa-trash me-2"></i>
                            Xóa tất cả
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Products Grid -->
            <div class="row" id="wishlistGrid">
                <div id="wishlistItemsContainer" style="display: none;">
                <?php foreach ($wishlistItems as $item): ?>
                    <div class="wishlist-item" data-product-id="<?= $item->product_id ?>">
                        <img src="<?= $item->primary_image ?? BASE_URL . '/public/assets/images/placeholder.jpg' ?>" alt="<?= htmlspecialchars($item->product_name) ?>">
                        <span class="product-name"><?= htmlspecialchars($item->product_name) ?></span>
                        <span class="price"><?= number_format($item->base_price, 0, ',', '.') ?>₫</span>
                        <a href="<?= BASE_URL ?>/product/<?= $item->slug ?? $item->product_id ?>"></a>
                    </div>
                <?php endforeach; ?>
                </div>
                
                <?php foreach ($wishlistItems as $item): ?>
                    <div class="col-lg-3 col-md-4 col-sm-6 mb-4" data-product-id="<?= $item->product_id ?>">
                        <div class="product-card">
                            <button class="btn-remove" onclick="removeFromWishlist(<?= $item->product_id ?>)" title="Xóa khỏi danh sách yêu thích">
                                <i class="fas fa-times"></i>
                            </button>
                            
                            <a href="<?= BASE_URL ?>/product/<?= $item->slug ?? $item->product_id ?>">
                                <img src="<?= $item->primary_image ?? BASE_URL . '/public/assets/images/placeholder.jpg' ?>" 
                                     alt="<?= htmlspecialchars($item->product_name) ?>" 
                                     class="product-image">
                            </a>
                            
                            <div class="product-info">
                                <h5 class="product-title">
                                    <a href="<?= BASE_URL ?>/product/<?= $item->slug ?? $item->product_id ?>" class="text-decoration-none">
                                        <?= htmlspecialchars($item->product_name) ?>
                                    </a>
                                </h5>
                                
                                <div class="product-price">
                                    <?= number_format($item->base_price, 0, ',', '.') ?>đ
                                </div>
                                
                                <div class="d-flex gap-2">
                                    <button class="btn btn-add-cart flex-fill" onclick="addToCart(<?= $item->product_id ?>)">
                                        <i class="fas fa-shopping-cart me-2"></i>
                                        Thêm vào giỏ
                                    </button>
                                    <a href="<?= BASE_URL ?>/product/<?= $item->slug ?? $item->product_id ?>" class="btn btn-outline-secondary">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <!-- Empty Wishlist -->
            <div class="empty-wishlist">
                <i class="fas fa-heart-broken"></i>
                <h3>Danh sách yêu thích trống</h3>
                <p class="text-muted">Bạn chưa thêm sản phẩm nào vào danh sách yêu thích.<br>Hãy khám phá các sản phẩm tuyệt vời của chúng tôi!</p>
                <a href="<?= BASE_URL ?>/products" class="btn-continue">
                    <i class="fas fa-shopping-bag me-2"></i>
                    Tiếp tục mua sắm
                </a>
            </div>
        <?php endif; ?>
    </div>
    
    <!-- Footer -->
    <?php include __DIR__ . '/../components/footer.php'; ?>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Remove product from wishlist
        async function removeFromWishlist(productId) {
            try {
                const result = await Swal.fire({
                    title: 'Xóa khỏi danh sách yêu thích?',
                    text: 'Bạn có chắc muốn xóa sản phẩm này?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Xóa',
                    cancelButtonText: 'Hủy',
                    confirmButtonColor: '#e74c3c'
                });
                
                if (!result.isConfirmed) return;
                
                const response = await fetch(BASE_URL . '/wishlist/remove', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: `product_id=${productId}`
                });
                
                const data = await response.json();
                
                if (data.success) {
                    // Remove product card from DOM
                    const productCard = document.querySelector(`[data-product-id="${productId}"]`);
                    if (productCard) {
                        productCard.style.transition = 'all 0.5s ease';
                        productCard.style.opacity = '0';
                        productCard.style.transform = 'translateY(-20px)';
                        
                        setTimeout(() => {
                            productCard.remove();
                            
                            // Check if wishlist is empty
                            const remainingCards = document.querySelectorAll('#wishlistGrid [data-product-id]');
                            if (remainingCards.length === 0) {
                                location.reload(); // Reload to show empty state
                            }
                        }, 500);
                    }
                    
                    // Update wishlist count in header
                    updateWishlistCount(data.data.wishlist_count);
                    
                    // Show success message
                    Swal.fire({
                        icon: 'success',
                        title: 'Đã xóa!',
                        text: data.message,
                        timer: 1500,
                        showConfirmButton: false
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Lỗi!',
                        text: data.message
                    });
                }
            } catch (error) {
                console.error('Remove from wishlist error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Lỗi kết nối!',
                    text: 'Không thể xóa sản phẩm khỏi danh sách yêu thích'
                });
            }
        }
        
        // Clear entire wishlist
        async function clearWishlist() {
            try {
                const result = await Swal.fire({
                    title: 'Xóa tất cả?',
                    text: 'Bạn có chắc muốn xóa tất cả sản phẩm khỏi danh sách yêu thích?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Xóa tất cả',
                    cancelButtonText: 'Hủy',
                    confirmButtonColor: '#e74c3c'
                });
                
                if (!result.isConfirmed) return;
                
                const response = await fetch(BASE_URL . '/wishlist/clear', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                
                const data = await response.json();
                
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Đã xóa tất cả!',
                        text: data.message,
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Lỗi!',
                        text: data.message
                    });
                }
            } catch (error) {
                console.error('Clear wishlist error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Lỗi kết nối!',
                    text: 'Không thể xóa danh sách yêu thích'
                });
            }
        }
        
        // Add to cart
        async function addToCart(productId) {
            try {
                const response = await fetch(BASE_URL . '/cart/add', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: `product_id=${productId}&quantity=1`
                });
                
                const data = await response.json();
                
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Đã thêm vào giỏ hàng!',
                        text: data.message,
                        timer: 1500,
                        showConfirmButton: false
                    });
                    
                    // Update cart count if available
                    if (data.data && data.data.cartTotal !== undefined) {
                        updateCartCount(data.data.cartTotal);
                    }
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Lỗi!',
                        text: data.message
                    });
                }
            } catch (error) {
                console.error('Add to cart error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Lỗi kết nối!',
                    text: 'Không thể thêm sản phẩm vào giỏ hàng'
                });
            }
        }
        
        // Update wishlist count in header
        function updateWishlistCount(count) {
            const wishlistBadge = document.querySelector('.wishlist-count');
            if (wishlistBadge) {
                wishlistBadge.textContent = count;
                wishlistBadge.style.display = count > 0 ? 'inline' : 'none';
            }
        }
        
        // Update cart count in header  
        function updateCartCount(count) {
            const cartBadge = document.querySelector('.cart-count');
            if (cartBadge) {
                cartBadge.textContent = count;
                cartBadge.style.display = count > 0 ? 'inline' : 'none';
            }
        }
    </script>
</body>
</html>

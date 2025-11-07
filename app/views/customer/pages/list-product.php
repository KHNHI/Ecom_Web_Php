<?php 
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
    <title>JEWELRY - Danh sách sản phẩm</title>
   

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <html xmlns:th="http://www.thymeleaf.org">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link href="<?= asset('css/css.css?v=' . time()) ?>" rel="stylesheet">  
    
</head>
<body>
    <!-- Header -->
    <?php include __DIR__ . '/../components/header.php'; ?>

    <!-- Banner Section -->
    <section class="banner-section" 
    style="background: url('https://images.unsplash.com/photo-1625908733471-7878cc0a230d?ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&q=80&w=2080') no-repeat center center; background-size: cover; height: 300px; position: relative;">
       
    </section>

    <!-- Main Content -->
    <div class="container">
        <div class="row gap-6">
            <!-- Sidebar Filters -->
            <div class="col-lg-3 col-md-4">
                <!-- Category Filter -->
                <div class="filter-card">
                    <div class="filter-header">
                        <i class="fas fa-list me-2"></i>Danh mục
                    </div>
                    <div class="filter-body">
                        <?php if (isset($categories) && !empty($categories)): ?>
                            <?php foreach ($categories as $category): ?>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" 
                                           value="<?= $category->category_id ?>" 
                                           id="category_<?= $category->category_id ?>"
                                           <?= (isset($filters['category']) && $filters['category'] == $category->category_id) ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="category_<?= $category->category_id ?>">
                                        <?= htmlspecialchars($category->name) ?>
                                    </label>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <!-- Fallback static categories -->
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" value="rings" id="rings">
                            <label class="form-check-label" for="rings">Nhẫn</label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" value="necklaces" id="necklaces">
                            <label class="form-check-label" for="necklaces">Dây chuyền</label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" value="earrings" id="earrings">
                            <label class="form-check-label" for="earrings">Bông tai</label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" value="bracelets" id="bracelets">
                            <label class="form-check-label" for="bracelets">Vòng tay</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="watches" id="watches">
                            <label class="form-check-label" for="watches">Đồng hồ</label>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Material Filter -->
                <div class="filter-card">
                    <div class="filter-header">
                        <i class="fas fa-gem me-2"></i>Chất liệu
                    </div>
                    <div class="filter-body">
                        <?php 
                        $materialLabels = [
                            'gold' => 'Vàng',
                            'silver' => 'Bạc', 
                            'diamond' => 'Kim cương',
                            'pearl' => 'Ngọc trai'
                        ];
                        if (!empty($data['materials'])): 
                            foreach ($data['materials'] as $index => $material): ?>
                        <div class="form-check <?= $index < count($data['materials']) - 1 ? 'mb-2' : '' ?>">
                            <input class="form-check-input" type="checkbox" value="<?= $material->material ?>" id="<?= $material->material ?>">
                            <label class="form-check-label" for="<?= $material->material ?>">
                                <?= $materialLabels[$material->material] ?? ucfirst($material->material) ?> 
                                <span class="text-muted">(<?= $material->product_count ?>)</span>
                            </label>
                        </div>
                        <?php endforeach; endif; ?>
                    </div>
                </div>



                <!-- Price Range Filter -->
                <div class="filter-card">
                    <div class="filter-header">
                        <i class="fas fa-dollar-sign me-2"></i>Mức giá
                    </div>
                    <div class="filter-body">
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="radio" name="priceRange" value="all" id="all-price" checked>
                            <label class="form-check-label" for="all-price">Tất cả các mức giá</label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="radio" name="priceRange" value="0-1000000" id="under-1m">
                            <label class="form-check-label" for="under-1m">Dưới 1,000,000₫</label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="radio" name="priceRange" value="1000000-5000000" id="1m-5m">
                            <label class="form-check-label" for="1m-5m">1,000,000₫ - 5,000,000₫</label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="radio" name="priceRange" value="5000000-10000000" id="5m-10m">
                            <label class="form-check-label" for="5m-10m">5,000,000₫ - 10,000,000₫</label>
                        </div>
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="radio" name="priceRange" value="10000000-999999999" id="over-10m">
                            <label class="form-check-label" for="over-10m">Trên 10,000,000₫</label>
                        </div>
                        <div class="price-range-inputs">
                            <input type="number" class="form-control" placeholder="Min price" id="minPrice">
                            <input type="number" class="form-control" placeholder="Max price" id="maxPrice">
                        </div>
                    </div>
                </div>

                <!-- Filter Action Buttons -->
                <div class="filter-actions mt-4">
                    <button class="btn btn-primary w-100 mb-2" id="applyFiltersBtn" onclick="applyFilters()" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none; font-weight: 500;">
                        <i class="fas fa-filter me-2"></i>Áp dụng bộ lọc
                    </button>
                    <button class="btn btn-outline-secondary w-100" id="clearFiltersBtn" onclick="clearAllFilters()" style="border: 2px solid #6c757d; font-weight: 500;">
                        <i class="fas fa-times me-2"></i>Xóa tất cả bộ lọc
                    </button>
                </div>
            </div>

            <!-- Product Section -->
            <div class="col-lg-9 col-md-8">
                <!-- Product Header -->
                <div class="product-header">
                   
                    <div class="row align-items-center mb-3">
                        <div class="results-info col-md-6">
                            <strong id="resultsCount"><?= isset($total) ? $total : 0 ?></strong> sản phẩm được tìm thấy
                            <!-- Active Filters Display -->
                            <div class="active-filters mt-2" id="activeFiltersDisplay" style="display: none;">
                                <small class="text-muted">Bộ lọc đang áp dụng:</small>
                                <div class="d-flex flex-wrap gap-1 mt-1" id="activeFilterTags">
                                    <!-- Filter tags will be generated by JavaScript -->
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <select class="form-select" id="sortSelect">
                                <option value="popular">Phổ biến nhất</option>
                                <option value="price_asc">Giá: từ thấp đến cao</option>
                                <option value="price_desc">Giá: từ cao đến thấp</option>
                                <option value="newest">Mới nhất</option>
                                <option value="rating">Được đánh giá nhiều nhất</option>
                                <option value="name_asc">Theo tên: từ A-Z</option>
                                <option value="name_desc">Theo tên: từ Z-A</option>
                            </select>
                        </div>
                    </div>

                    <!-- Active Filters -->
                                        
                </div>

                <!-- Product Grid -->
                <div class="row" id="productGrid">
                    <?php if (isset($products) && !empty($products)): ?>
                        <?php foreach ($products as $product): ?>
                            <div class="col-lg-4 col-md-6 mb-4 d-flex justify-content-center">
                                <div class="list-card-product">
                                    <a href="<?= route('product/' . ($product->slug ?? $product->product_id)) ?>" style="text-decoration: none; color: inherit; display: block;">
                                        <div class="position-relative">
                                            <img src="<?= $product->primary_image ? asset($product->primary_image->file_path) : asset('images/placeholder.svg') ?>" 
                                                 class="card-img-top" alt="<?= htmlspecialchars($product->name) ?>">
                                        </div>
                                        <div class="card-body">
                                            <h5 class="card-title mb-2"><?= htmlspecialchars($product->name) ?></h5>
                                            <div class="mb-2">
                                                <span class="price fw-bold"><?= number_format($product->base_price, 0, ',', '.') ?>₫</span>
                                            </div>
                                            <?php if (isset($product->average_rating) && $product->average_rating > 0): ?>
                                            <div class="mb-2">
                                                <span class="rating text-warning">
                                                    <?php 
                                                    $rating = round($product->average_rating);
                                                    for ($i = 1; $i <= 5; $i++) {
                                                        echo ($i <= $rating) ? '★' : '☆';
                                                    }
                                                    ?>
                                                </span>
                                                <span class="text-muted ms-1">
                                                    (<?= number_format($product->review_count ?? 0) ?>)
                                                </span>
                                            </div>
                                            <?php endif; ?>
                                            <div class="product-actions">
                                                <button class="btn btn-icon btn-sm me-2" title="Yêu thích" 
                                                        onclick="event.preventDefault(); event.stopPropagation(); toggleWishlist(<?= $product->product_id ?>)">
                                                    <i class="fa-regular fa-heart"></i>
                                                </button>
                                                <!-- <button class="btn btn-icon btn-sm" title="Thêm vào giỏ" 
                                                        onclick="event.preventDefault(); event.stopPropagation(); addToCartFromList(<?= $product->product_id ?>, '<?= htmlspecialchars($product->name) ?>')">
                                                    <i class="fa-solid fa-cart-plus"></i>
                                                </button> -->
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="col-12 text-center py-5 text-muted">
                            Không tìm thấy sản phẩm phù hợp.
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Pagination -->
                <?php if (isset($totalPages) && $totalPages > 1): ?>
                <nav aria-label="Product pagination" class="mt-4">
                    <ul class="pagination justify-content-center">
                        <li class="page-item <?= ($currentPage <= 1) ? 'disabled' : '' ?>">
                            <a class="page-link" href="?page=<?= max(1, $currentPage - 1) ?><?= isset($filters['search']) && !empty($filters['search']) ? '&search=' . urlencode($filters['search']) : '' ?>" tabindex="-1">Previous</a>
                        </li>
                        
                        <?php
                        $startPage = max(1, $currentPage - 2);
                        $endPage = min($totalPages, $currentPage + 2);
                        
                        for ($i = $startPage; $i <= $endPage; $i++):
                        ?>
                            <li class="page-item <?= ($i == $currentPage) ? 'active' : '' ?>">
                                <a class="page-link" href="?page=<?= $i ?><?= isset($filters['search']) && !empty($filters['search']) ? '&search=' . urlencode($filters['search']) : '' ?>"><?= $i ?></a>
                            </li>
                        <?php endfor; ?>
                        
                        <li class="page-item <?= ($currentPage >= $totalPages) ? 'disabled' : '' ?>">
                            <a class="page-link" href="?page=<?= min($totalPages, $currentPage + 1) ?><?= isset($filters['search']) && !empty($filters['search']) ? '&search=' . urlencode($filters['search']) : '' ?>">Next</a>
                        </li>
                    </ul>
                </nav>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <?php include __DIR__ . '/../components/footer.php'; ?>

    <!-- Bootstrap JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    
    <!-- Material Filter JS -->
    <script src="<?= asset('js/material-filter.js') ?>"></script>
    

    <script>
        // Load current filters from URL on page load
        function loadCurrentFilters() {
            const urlParams = new URLSearchParams(window.location.search);
            
            // Load categories
            const categories = urlParams.get('categories');
            if (categories) {
                const categoryIds = categories.split(',');
                categoryIds.forEach(id => {
                    const checkbox = document.querySelector(`input[id="category_${id}"]`);
                    if (checkbox) checkbox.checked = true;
                });
            }
            
            // Load materials
            const materials = urlParams.get('materials');
            if (materials) {
                const materialList = materials.split(',');
                materialList.forEach(material => {
                    const checkbox = document.querySelector(`input[value="${material}"]`);
                    if (checkbox) checkbox.checked = true;
                });
            }
        }

        /**
         * Add product to cart from product list
         */
        function addToCartFromList(productId, productName) {
            // Show loading state
            const button = event.target.closest('button');
            const originalHtml = button.innerHTML;
            button.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
            button.disabled = true;

            // Prepare cart data (default values for quick add)
            const cartData = new FormData();
            cartData.append('product_id', productId);
            cartData.append('quantity', 1);
            cartData.append('size', '');
            cartData.append('color', '');

            const cartUrl = '<?= url('cart/add') ?>';
            console.log('Cart URL:', cartUrl);
            console.log('Product ID:', productId);
            
            fetch(cartUrl, {
                method: 'POST',
                body: cartData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => {
                console.log('Response status:', response.status);
                console.log('Response headers:', response.headers);
                return response.json();
            })
            .then(data => {
                console.log('Response data:', data);
                // Restore button state
                button.innerHTML = originalHtml;
                button.disabled = false;

                if (data.success) {
                    // Update cart badge
                    if (typeof updateCartBadge === 'function') {
                        updateCartBadge();
                    }
                    
                    // Show success message
                    showToast('success', `Đã thêm "${productName}" vào giỏ hàng!`);
                } else {
                    showToast('error', data.message || 'Có lỗi xảy ra khi thêm vào giỏ hàng');
                }
            })
            .catch(error => {
                // Restore button state
                button.innerHTML = originalHtml;
                button.disabled = false;
                
                console.error('Full error details:', error);
                console.error('Error message:', error.message);
                console.error('Error stack:', error.stack);
                showToast('error', 'Có lỗi kết nối xảy ra: ' + error.message);
            });
        }

        /**
         * Toggle wishlist
         */
        async function toggleWishlist(productId) {
            const button = event.target.closest('button');
            const icon = button.querySelector('i');
            const originalColor = button.style.color;
            const wasInWishlist = icon.classList.contains('fa-solid');
            
            try {
                // Temporarily update UI for immediate feedback
                if (wasInWishlist) {
                    icon.classList.remove('fa-solid');
                    icon.classList.add('fa-regular');
                    button.style.color = '';
                } else {
                    icon.classList.remove('fa-regular');
                    icon.classList.add('fa-solid');
                    button.style.color = '#dc3545';
                }
                
                // Call API
                const response = await fetch('/Ecom_website/wishlist/toggle', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: `product_id=${productId}`
                });
                
                const data = await response.json();
                
                if (data.success) {
                    // Update UI based on server response
                    if (data.data.action === 'added') {
                        icon.classList.remove('fa-regular');
                        icon.classList.add('fa-solid');
                        button.style.color = '#dc3545';
                        showToast('success', 'Đã thêm vào danh sách yêu thích!');
                    } else {
                        icon.classList.remove('fa-solid');
                        icon.classList.add('fa-regular');
                        button.style.color = '';
                        showToast('info', 'Đã xóa khỏi danh sách yêu thích!');
                    }
                    
                    // Update wishlist count in header
                    updateWishlistCount(data.data.wishlist_count);
                } else {
                    // Revert UI changes on error
                    if (wasInWishlist) {
                        icon.classList.remove('fa-regular');
                        icon.classList.add('fa-solid');
                        button.style.color = '#dc3545';
                    } else {
                        icon.classList.remove('fa-solid');
                        icon.classList.add('fa-regular');
                        button.style.color = originalColor;
                    }
                    
                    if (data.message === 'Vui lòng đăng nhập để sử dụng danh sách yêu thích') {
                        showToast('warning', data.message);
                        // Optionally redirect to login page
                        setTimeout(() => {
                            window.location.href = '/Ecom_website/signin';
                        }, 2000);
                    } else {
                        showToast('error', data.message || 'Có lỗi xảy ra!');
                    }
                }
            } catch (error) {
                console.error('Wishlist toggle error:', error);
                // Revert UI changes on error
                if (wasInWishlist) {
                    icon.classList.remove('fa-regular');
                    icon.classList.add('fa-solid');
                    button.style.color = '#dc3545';
                } else {
                    icon.classList.remove('fa-solid');
                    icon.classList.add('fa-regular');
                    button.style.color = originalColor;
                }
                showToast('error', 'Lỗi kết nối!');
            }
        }

        /**
         * Show toast notification
         */
        function showToast(type, message) {
            // Check if SweetAlert is available
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
                // Fallback to alert
                alert(message);
            }
        }

        /**
         * Update wishlist count in header
         */
        function updateWishlistCount(count) {
            const wishlistBadge = document.querySelector('.wishlist-count');
            if (wishlistBadge) {
                wishlistBadge.textContent = count;
                wishlistBadge.style.display = count > 0 ? 'inline' : 'none';
            }
        }

        /**
         * Load wishlist status for products when page loads
         */
        async function loadWishlistStatus() {
            try {
                const response = await fetch('/Ecom_website/wishlist/status', {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                
                if (response.ok) {
                    const data = await response.json();
                    if (data.success && data.data.wishlist_items) {
                        // Update UI for products in wishlist
                        data.data.wishlist_items.forEach(productId => {
                            // Find wishlist buttons for this product
                            const buttons = document.querySelectorAll(`button[onclick*="toggleWishlist(${productId}"]`);
                            buttons.forEach(button => {
                                const icon = button.querySelector('i');
                                if (icon) {
                                    icon.classList.remove('fa-regular');
                                    icon.classList.add('fa-solid');
                                    button.style.color = '#dc3545';
                                }
                            });
                        });
                    }
                }
            } catch (error) {
                console.log('Could not load wishlist status:', error);
            }
        }

        // Handle sorting change
        function handleSortChange() {
            const sortSelect = document.getElementById('sortSelect');
            const selectedSort = sortSelect.value;
            
            // Get current URL parameters
            const urlParams = new URLSearchParams(window.location.search);
            
            // Update sort parameter
            urlParams.set('sort', selectedSort);
            
            // Reload page with new sort parameter
            window.location.href = window.location.pathname + '?' + urlParams.toString();
        }

        // Set current sort value from URL
        function setCurrentSort() {
            const urlParams = new URLSearchParams(window.location.search);
            const currentSort = urlParams.get('sort') || 'popular';
            const sortSelect = document.getElementById('sortSelect');
            if (sortSelect) {
                sortSelect.value = currentSort;
            }
        }

        // Initialize page
        document.addEventListener('DOMContentLoaded', function() {
            loadCurrentFilters();
            loadWishlistStatus();
            setCurrentSort();
            
            // Add event listener to sort select
            const sortSelect = document.getElementById('sortSelect');
            if (sortSelect) {
                sortSelect.addEventListener('change', handleSortChange);
            }
        });
    </script>
</body>
</html>
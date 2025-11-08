
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jewelry</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <html xmlns:th="http://www.thymeleaf.org">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link href="<?= asset('css/css.css?v=' . time()) ?>" rel="stylesheet">
    
    <style>
        /* Desktop categories */
        .category-item {
            cursor: pointer;
            transition: transform 0.3s ease;
        }
        
        .category-item:hover {
            transform: translateY(-5px);
        }
        
        .category-card {
            cursor: pointer;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        .category-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.15);
        }

        /* Mobile categories slider */
        .category-mobile-wrapper {
            position: relative;
            width: 100%;
            overflow: hidden;
            padding: 0 15px;
        }

        .category-mobile-slider {
            display: flex;
            transition: transform 0.3s ease-in-out;
            width: 100%;
        }

        .category-mobile-slide {
            flex: 0 0 100%;
            width: 100%;
            text-align: center;
            cursor: pointer;
        }

        .category-mobile-dots {
            display: flex;
            justify-content: center;
            gap: 8px;
            margin-top: 20px;
        }

        .category-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background-color: #ddd;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .category-dot.active {
            background-color: var(--gold);
            width: 24px;
            border-radius: 4px;
        }

        /* Touch slide animation */
        .category-mobile-slider.sliding {
            transition: none;
        }

        @media (max-width: 767px) {
            .category-mobile-slide {
                padding: 0 10px;
            }
            
            .category-circle {
                margin: 0 auto;
            }
        }
    </style>

    
    
</head>
<body>
    <!-- Header -->
    <?php include __DIR__ . '/../components/header.php'; ?>

<!-- Hero Slider -->
    <div id="heroCarousel" class="carousel slide" data-bs-ride="carousel" data-bs-interval="5000">
        <div class="carousel-indicators">
            <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="0" class="active"></button>
            <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="1"></button>
            <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="2"></button>
            <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="3"></button>
            <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="4"></button>
        </div>
        <div class="carousel-inner">
            <div class="carousel-item active">
                <div class="hero-slide">
                    <div class="row h-100 align-items-center">
                        <div class="col-md-6">
                          
                        </div>
                        <div class="col-md-6">
                            <img src="https://images.unsplash.com/photo-1515562141207-7a88fb7ce338?w=600&h=600&fit=crop" alt="Jewelry 1" class="hero-image">
                        </div>
                    </div>
                </div>
            </div>
            <div class="carousel-item">
                <div class="hero-slide">
                    <div class="row h-100 align-items-center">
                        <div class="col-md-6">
                            
                        </div>
                        <div class="col-md-6">
                            <img src="https://images.unsplash.com/photo-1611652022419-a9419f74343d?w=600&h=600&fit=crop" alt="Jewelry 2" class="hero-image">
                        </div>
                    </div>
                </div>
            </div>
            <div class="carousel-item">
                <div class="hero-slide">
                    <div class="row h-100 align-items-center">
                        <div class="col-md-6">
                           
                        </div>
                        <div class="col-md-6">
                            <img src="https://images.unsplash.com/photo-1605100804763-247f67b3557e?w=600&h=600&fit=crop" alt="Jewelry 3" class="hero-image">
                        </div>
                    </div>
                </div>
            </div>
            <div class="carousel-item">
                <div class="hero-slide">
                    <div class="row h-100 align-items-center">
                        <div class="col-md-6">
                           
                        </div>
                        <div class="col-md-6">
                            <img src="https://images.unsplash.com/photo-1599643478518-a784e5dc4c8f?w=600&h=600&fit=crop" alt="Jewelry 4" class="hero-image">
                        </div>
                    </div>
                </div>
            </div>
            <div class="carousel-item">
                <div class="hero-slide">
                    <div class="row h-100 align-items-center">
                        <div class="col-md-6">
                            
                        </div>
                        <div class="col-md-6">
                            <img src="https://images.unsplash.com/photo-1617038260897-41a1f14a8ca0?w=600&h=600&fit=crop" alt="Jewelry 5" class="hero-image">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon"></span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
            <span class="carousel-control-next-icon"></span>
        </button>
    </div>




<!-- CONTAINER -->

<!-- New Arrivals -->
<section class="container shrink my-4">
  <h2 class="section-title text-center">SẢN PHẨM MỚI NHẤT</h2>
  <div class="title-underline"></div>
  <div class="row justify-content-center" id="newArrivalsContainer">
    <!-- JS sẽ render sản phẩm ở đây -->
  </div>
  <button class="carousel-btn prev-btn" onclick="moveCarousel('newArrivals', -1)">
      <i class="fas fa-chevron-left"></i>
  </button>
  <button class="carousel-btn next-btn" onclick="moveCarousel('newArrivals', 1)">
      <i class="fas fa-chevron-right"></i>
  </button>
  <div class="text-center mt-4">
    <button class="btn btn-outline-dark" onclick="viewMoreNewArrivals()">XEM THÊM</button>
  </div>
</section>

<!-- The Most Popular -->
<section class="container shrink my-4">
  <h2 class="section-title text-center">SẢN PHẨM PHỔ BIẾN NHẤT</h2>
  <div class="title-underline"></div>
  <div class="row justify-content-center" id="popularContainer">
    <!-- JS sẽ render sản phẩm ở đây -->
  </div>
  <button class="carousel-btn prev-btn" onclick="moveCarousel('popular', -1)">
      <i class="fas fa-chevron-left"></i>
  </button>
  <button class="carousel-btn next-btn" onclick="moveCarousel('popular', 1)">
      <i class="fas fa-chevron-right"></i>
  </button>
  <div class="text-center mt-4">
    <button class="btn btn-outline-dark" onclick="viewMorePopular()">XEM THÊM</button>
  </div>
</section>

<!-- Category Section -->
    <section class="py-5 bg-light">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="section-title">DANH MỤC</h2>
                <div class="title-underline"></div>
            </div>

            <!-- Desktop View -->
            <div class="position-relative d-none d-md-block">
                <div class="d-flex justify-content-center" id="categoryCarousel">
                    <?php if (isset($categories) && !empty($categories)): ?>
                        <?php foreach ($categories as $category): ?>
                            <div class="category-item text-center mx-3" onclick="goToProducts('category', '<?= $category->category_id ?>')">
                                <div class="category-circle">
                                    <img src="<?= isset($category->banner_image) ? $category->banner_image : 'https://images.unsplash.com/photo-1599643478518-a784e5dc4c8f?w=100&h=100&fit=crop' ?>" 
                                         alt="<?= htmlspecialchars($category->category_name ?? $category->name ?? 'Category') ?>">
                                </div>
                                <p class="mt-2"><?= htmlspecialchars($category->category_name ?? $category->name ?? 'Category') ?></p>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <!-- Fallback static categories if no data from DB -->
                        <div class="category-item text-center mx-3" onclick="goToProducts('category', 'necklaces')">
                            <div class="category-circle">
                                <img src="https://images.unsplash.com/photo-1599643478518-a784e5dc4c8f?w=100&h=100&fit=crop" alt="Necklaces">
                            </div>
                            <p class="mt-2">Necklaces</p>
                        </div>
                        <div class="category-item text-center mx-3" onclick="goToProducts('category', 'earrings')">
                            <div class="category-circle">
                                <img src="https://images.unsplash.com/photo-1617038260897-41a1f14a8ca0?w=100&h=100&fit=crop" alt="Earrings">
                            </div>
                            <p class="mt-2">Earrings</p>
                        </div>
                        <div class="category-item text-center mx-3" onclick="goToProducts('category', 'rings')">
                            <div class="category-circle">
                                <img src="https://images.unsplash.com/photo-1605100804763-247f67b3557e?w=100&h=100&fit=crop" alt="Rings">
                            </div>
                            <p class="mt-2">Rings</p>
                        </div>
                        <div class="category-item text-center mx-3" onclick="goToProducts('category', 'bracelets')">
                            <div class="category-circle">
                                <img src="https://images.unsplash.com/photo-1515562141207-7a88fb7ce338?w=100&h=100&fit=crop" alt="Bracelets">
                            </div>
                            <p class="mt-2">Bracelets</p>
                        </div>
                        <div class="category-item text-center mx-3" onclick="goToProducts('category', 'anklets')">
                            <div class="category-circle">
                                <img src="https://images.unsplash.com/photo-1611652022419-a9419f74343d?w=100&h=100&fit=crop" alt="Anklets">
                            </div>
                            <p class="mt-2">Anklets</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Mobile View with Slider -->
            <div class="position-relative d-block d-md-none">
                <div class="category-mobile-wrapper">
                    <div class="category-mobile-slider" id="categoryMobileSlider">
                        <?php if (isset($categories) && !empty($categories)): ?>
                            <?php foreach ($categories as $category): ?>
                                <div class="category-mobile-slide" onclick="goToProducts('category', '<?= $category->category_id ?>')">
                                    <div class="category-circle">
                                        <img src="<?= isset($category->banner_image) ? $category->banner_image : 'https://images.unsplash.com/photo-1599643478518-a784e5dc4c8f?w=100&h=100&fit=crop' ?>" 
                                             alt="<?= htmlspecialchars($category->category_name ?? $category->name ?? 'Category') ?>">
                                    </div>
                                    <p class="mt-2"><?= htmlspecialchars($category->category_name ?? $category->name ?? 'Category') ?></p>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <!-- Fallback static categories for mobile -->
                            <div class="category-mobile-slide" onclick="goToProducts('category', 'necklaces')">
                                <div class="category-circle">
                                    <img src="https://images.unsplash.com/photo-1599643478518-a784e5dc4c8f?w=100&h=100&fit=crop" alt="Necklaces">
                                </div>
                                <p class="mt-2">Necklaces</p>
                            </div>
                            <div class="category-mobile-slide" onclick="goToProducts('category', 'earrings')">
                                <div class="category-circle">
                                    <img src="https://images.unsplash.com/photo-1617038260897-41a1f14a8ca0?w=100&h=100&fit=crop" alt="Earrings">
                                </div>
                                <p class="mt-2">Earrings</p>
                            </div>
                            <div class="category-mobile-slide" onclick="goToProducts('category', 'rings')">
                                <div class="category-circle">
                                    <img src="https://images.unsplash.com/photo-1605100804763-247f67b3557e?w=100&h=100&fit=crop" alt="Rings">
                                </div>
                                <p class="mt-2">Rings</p>
                            </div>
                            <div class="category-mobile-slide" onclick="goToProducts('category', 'bracelets')">
                                <div class="category-circle">
                                    <img src="https://images.unsplash.com/photo-1515562141207-7a88fb7ce338?w=100&h=100&fit=crop" alt="Bracelets">
                                </div>
                                <p class="mt-2">Bracelets</p>
                            </div>
                            <div class="category-mobile-slide" onclick="goToProducts('category', 'anklets')">
                                <div class="category-circle">
                                    <img src="https://images.unsplash.com/photo-1611652022419-a9419f74343d?w=100&h=100&fit=crop" alt="Anklets">
                                </div>
                                <p class="mt-2">Anklets</p>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="category-mobile-dots" id="categoryMobileDots"></div>
                </div>
            </div>
        </div>
    </section>

        <!-- Gold/Silver Section -->
    <section class="py-5">
        <div class="container">
            <div class="row">
                <div class="col-md-6 mb-4">
                    <div class="category-card gold-card" onclick="goToProducts('material', 'gold')">
                        <div class="category-overlay">
                            <h3 class="category-title">VÀNG</h3>                            
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mb-4">
                    <div class="category-card silver-card" onclick="goToProducts('material', 'silver')">
                        <div class="category-overlay">
                            <h3 class="category-title">BẠC</h3>                            
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>


    <!-- Feedback Section -->
    <section class="py-5">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="section-title">ĐÁNH GIÁ TỪ KHÁCH HÀNG</h2>
                <div class="title-underline"></div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-4">
                    <div class="feedback-card">
                        <div class="row">
                            <div class="col-4">
                                <img src="<?= asset('images/placeholder.svg') ?>" alt="Customer" class="feedback-image">
                            </div>
                            <div class="col-8">
                                <h5>ANH THƯ</h5>
                                <p class="feedback-date">TP. Hồ Chí Minh</p>
                                <p class="feedback-text">"Sản phẩm đẹp, quy trình mua hàng dễ dàng. Shop hỗ trợ khách rất nhiệt tình, sẽ ghé lại lần sau."</p>
                                <div class="rating">
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mb-4">
                    <div class="feedback-card">
                        <div class="row">
                            <div class="col-4">
                                <img src="https://images.unsplash.com/photo-1438761681033-6461ffad8d80?w=150&h=150&fit=crop" alt="Customer" class="feedback-image">
                            </div>
                            <div class="col-8">
                                <h5>MINH THƯ</h5>
                                <p class="feedback-date">TP. Hồ Chí Minh</p>
                                <p class="feedback-text">"Size nhẫn rất vừa vặn, sản phẩm thật rất giống trong ảnh. Sẽ tiếp tục ủng hộ shop."</p>
                                <div class="rating">
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="text-center">
                <button class="btn btn-outline-dark">XEM THÊM</button>
            </div>
        </div>
    </section>

    <?php include __DIR__ . '/../components/footer.php'; ?>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom JavaScript -->
    <script>
    // Mobile Categories Slider
    let categoryCurrentSlide = 0;
    let categoryTotalSlides = 0;
    let categoryStartX = 0;
    let categoryCurrentX = 0;
    let categoryIsSwiping = false;

    document.addEventListener('DOMContentLoaded', function() {
        initCategoryMobileSlider();
    });

    function initCategoryMobileSlider() {
        const slider = document.getElementById('categoryMobileSlider');
        const dotsContainer = document.getElementById('categoryMobileDots');
        
        if (!slider || !dotsContainer) return;

        const slides = slider.querySelectorAll('.category-mobile-slide');
        categoryTotalSlides = slides.length;

        // Create dots
        slides.forEach((_, index) => {
            const dot = document.createElement('div');
            dot.className = `category-dot${index === 0 ? ' active' : ''}`;
            dot.addEventListener('click', () => goToSlide(index));
            dotsContainer.appendChild(dot);
        });

        // Touch events
        slider.addEventListener('touchstart', handleTouchStart, { passive: true });
        slider.addEventListener('touchmove', handleTouchMove, { passive: true });
        slider.addEventListener('touchend', handleTouchEnd);

        updateCategorySlider();
    }

    function handleTouchStart(e) {
        categoryStartX = e.touches[0].clientX;
        categoryCurrentX = categoryStartX;
        categoryIsSwiping = true;
        const slider = document.getElementById('categoryMobileSlider');
        slider.classList.add('sliding');
    }

    function handleTouchMove(e) {
        if (!categoryIsSwiping) return;
        
        categoryCurrentX = e.touches[0].clientX;
        const diff = categoryCurrentX - categoryStartX;
        const slider = document.getElementById('categoryMobileSlider');
        
        const transform = -categoryCurrentSlide * 100 + (diff / slider.offsetWidth * 100);
        slider.style.transform = `translateX(${transform}%)`;
    }

    function handleTouchEnd() {
        if (!categoryIsSwiping) return;
        
        const diff = categoryCurrentX - categoryStartX;
        const slider = document.getElementById('categoryMobileSlider');
        
        slider.classList.remove('sliding');
        
        if (Math.abs(diff) > 50) {
            if (diff > 0 && categoryCurrentSlide > 0) {
                categoryCurrentSlide--;
            } else if (diff < 0 && categoryCurrentSlide < categoryTotalSlides - 1) {
                categoryCurrentSlide++;
            }
        }
        
        updateCategorySlider();
        categoryIsSwiping = false;
    }

    function goToSlide(index) {
        categoryCurrentSlide = index;
        updateCategorySlider();
    }

    function updateCategorySlider() {
        const slider = document.getElementById('categoryMobileSlider');
        const dots = document.querySelectorAll('.category-dot');
        
        if (!slider || !dots.length) return;

        // Update slider position
        slider.style.transform = `translateX(-${categoryCurrentSlide * 100}%)`;
        
        // Update dots
        dots.forEach((dot, index) => {
            dot.classList.toggle('active', index === categoryCurrentSlide);
        });
    }

    // Helper function to convert relative paths to absolute URLs
        function getAssetUrl(path) {
            // base path for this app (e.g. /Ecom_website or '')
            const base = "<?= getBaseUrl() ?>" || '';

            // No path -> use placeholder via server-side helper (safe)
            if (!path) return (base ? base : '') + '/public/assets/images/placeholder.svg';

            // Absolute remote URL
            if (path.startsWith('http')) return path;

            // If path already starts with app base (e.g. "/Ecom_website/...") return as-is
            if (base && path.startsWith(base)) return path;

            // If path is absolute from root (starting with '/'), prefix base
            if (path.startsWith('/')) return (base ? base : '') + path;

            // If path points to public folder already
            if (path.startsWith('public/')) return (base ? base + '/' : '/') + path;

            // Otherwise assume asset under public/assets
            return (base ? base : '') + '/public/assets/' + path.replace(/^\/+/, '');
        }
    // Server-resolved placeholder URL for JS fallbacks
    const PLACEHOLDER_URL = "<?= asset('images/placeholder.svg') ?>";
        
        document.addEventListener('DOMContentLoaded', function() {
        // Home
        document.querySelectorAll('.nav-link[href="#index"]').forEach(el => {
            el.addEventListener('click', function(e) {
                e.preventDefault();
                window.location.href = 'index.html';
            });
        });
        // Category
        document.querySelectorAll('.nav-link[href="#category"]').forEach(el => {
            el.addEventListener('click', function(e) {
                e.preventDefault();
                window.location.href = 'list-product.html';
            });
        });
        // Sign in
        document.querySelectorAll('a[href="#signin"]').forEach(el => {
            el.addEventListener('click', function(e) {
                e.preventDefault();
                window.location.href = '/Ecom_website/signin';
            });
        });
    });

        // Smooth scrolling for navigation links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        // Slider functionality
        const dots = document.querySelectorAll('.slider-dot');
        let currentSlide = 0;

        if (dots && dots.length > 0) {
            dots.forEach((dot, index) => {
                dot.addEventListener('click', () => {
                    if (dots[currentSlide]) dots[currentSlide].classList.remove('active');
                    currentSlide = index;
                    if (dots[currentSlide]) dots[currentSlide].classList.add('active');
                });
            });

            // Auto slide (optional)
            setInterval(() => {
                if (dots[currentSlide]) dots[currentSlide].classList.remove('active');
                currentSlide = (currentSlide + 1) % dots.length;
                if (dots[currentSlide]) dots[currentSlide].classList.add('active');
            }, 5000);
        }

        // Search functionality - Check if elements exist
        const searchBtn = document.querySelector('.search-btn');
        const searchInput = document.querySelector('.search-input');
        
        if (searchBtn && searchInput) {
            searchBtn.addEventListener('click', function() {
                const searchTerm = searchInput.value;
                if (searchTerm.trim()) {
                    // Redirect to products page with search parameter - Clean URL
                    window.location.href = `/Ecom_website/products?search=${encodeURIComponent(searchTerm.trim())}`;
                }
            });

            // Handle Enter key in search
            searchInput.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    searchBtn.click();
                }
            });
        }

        // Add parallax effect on scroll
        window.addEventListener('scroll', () => {
            const scrolled = window.pageYOffset;
            const decoration = document.querySelector('.hero-decoration');
            if (decoration) {
                decoration.style.transform = `rotate(${scrolled * 0.1}deg)`;
            }
        });

        /* Dữ liệu sản phẩm từ server */
        let newArrivalsData = [];
        let popularProductsData = [];

        // Fetch dữ liệu từ server
        async function fetchProductData() {
            try {
                // Fetch new arrivals - Clean URL
                const newArrivalsResponse = await fetch('/Ecom_website/api/new-arrivals?limit=7');
                const newArrivalsResult = await newArrivalsResponse.json();
                console.log('New Arrivals API Response:', newArrivalsResult);
                if (newArrivalsResult.success) {
                    newArrivalsData = newArrivalsResult.data.map(product => {
                        return {
                            id: product.product_id,
                            slug: product.slug || product.product_id,
                            name: product.name.toUpperCase(),
                            desc: product.description ? product.description.substring(0, 80) + '...' : 'No description',
                            price: parseFloat(product.base_price),
                            img: product.primary_image ? getAssetUrl(product.primary_image.file_path) : getAssetUrl("images/placeholder.svg"),
                            createdAt: product.created_at,
                            sold: Math.floor(Math.random() * 50) // Mock sold data
                        };
                    });
                    renderProducts('newArrivals');
                }

                // Fetch popular products - Clean URL
                const popularResponse = await fetch('/Ecom_website/api/popular?limit=7');
                const popularResult = await popularResponse.json();
                if (popularResult.success) {
                    popularProductsData = popularResult.data.map(product => {
                        return {
                            id: product.product_id,
                            slug: product.slug || product.product_id,
                            name: product.name.toUpperCase(),
                            desc: product.description ? product.description.substring(0, 80) + '...' : 'No description',
                            price: parseFloat(product.base_price),
                            img: product.primary_image ? getAssetUrl(product.primary_image.file_path) : getAssetUrl("images/placeholder.svg"),
                            createdAt: product.created_at,
                            sold: Math.floor(Math.random() * 50) // Mock sold data
                        };
                    });
                }

        
                renderProducts('newArrivals');
                renderProducts('popular');
                console.log('Both sections rendered!');
                
                // Load wishlist status after products are rendered
                setTimeout(() => {
                    loadWishlistStatus();
                }, 500);
            } catch (error) {
                console.error('Error fetching product data:', error);
                // Fallback to mock data if API fails
                newArrivalsData = getMockProducts();
                popularProductsData = getMockProducts();
                renderProducts('newArrivals');
                renderProducts('popular');
                
                // Load wishlist status for fallback data too
                setTimeout(() => {
                    loadWishlistStatus();
                }, 500);
            }
        }

        // Fallback mock data
        function getMockProducts() {
            return [
                {
                    id: 1,
                    name: "DIAMOND SOLITAIRE RING",
                    desc: "Crafted in 18K white gold, this solitaire ring features a brilliant-cut diamond...",
                    price: 50,
                    img: PLACEHOLDER_URL,
                    createdAt: "2025-09-18",
                    sold: 10
                },
                {
                    id: 2,
                    name: "GOLDEN EARRINGS",
                    desc: "18K gold earrings with elegant design.",
                    price: 120,
                    img: PLACEHOLDER_URL,
                    createdAt: "2025-09-17",
                    sold: 25
                },
                {
                    id: 3,
                    name: "SILVER BRACELET",
                    desc: "Sterling silver bracelet with diamond accents.",
                    price: 80,
                    img: PLACEHOLDER_URL,
                    createdAt: "2025-09-16",
                    sold: 18
                }
            ];
        }

/* Cấu hình số card hiển thị mỗi lần */
const CARDS_PER_VIEW = 3;

/* State cho carousel */
let carouselState = {
  newArrivals: 0,
  popular: 0
};

/* Hàm render sản phẩm */
function renderProducts(type, direction = null) {
  let dataSource;
  if (type === 'newArrivals') {
    dataSource = newArrivalsData;
  } else {
    dataSource = popularProductsData;
  }

  if (!dataSource || dataSource.length === 0) {
    return; // Don't render if no data
  }

  let start = carouselState[type];
  let items = [];
  for (let i = 0; i < CARDS_PER_VIEW; i++) {
    let idx = (start + i) % dataSource.length;
    let p = dataSource[idx];
            items.push(`
      <div class="col-md-4 mb-4">
        <div class="card product-card">
          <a href="/Ecom_website/product/${p.slug || p.id}" style="text-decoration: none; color: inherit;">
            <img src="${p.img}" class="card-img-top" alt="${p.name}">
            <div class="card-body text-center">
              <h5 class="card-title">${p.name}</h5>
              <p class="card-text">${p.desc}</p>
              <p class="price">${p.price.toLocaleString('vi-VN')}₫</p>
              <div class="product-actions">
              <i class="bi bi-heart" onclick="event.preventDefault(); event.stopPropagation(); toggleWishlist(${p.id}, this)" style="cursor: pointer;"></i> 
              <i class="bi bi-cart" onclick="event.preventDefault(); event.stopPropagation(); addToCartFromHome(${p.id}, '${p.name}')" style="cursor: pointer;"></i> </div>
              </div>
            </div>
          </a>
        </div>
      </div>
    `);
  }
  const container = document.getElementById(type === 'newArrivals' ? 'newArrivalsContainer' : 'popularContainer');
  container.innerHTML = items.join('');
  // Xóa cả hai class trước khi thêm mới
  container.classList.remove('product-carousel-slide-left', 'product-carousel-slide-right');
  if (direction) {
    void container.offsetWidth; // Force reflow
    if (direction === 'left') {
      container.classList.add('product-carousel-slide-left');
    } else if (direction === 'right') {
      container.classList.add('product-carousel-slide-right');
    }
  }
  
  // Load wishlist status after rendering products
  setTimeout(() => {
    loadWishlistStatus();
  }, 50);
}

function moveCarousel(type, dir) {
  let dataSource = type === 'newArrivals' ? newArrivalsData : popularProductsData;
  if (!dataSource || dataSource.length === 0) return;
  
  let sortedLength = dataSource.length;
  carouselState[type] = (carouselState[type] + dir + sortedLength) % sortedLength;
  renderProducts(type, dir === 1 ? 'right' : 'left');
}

// Khởi tạo - fetch data và render
document.addEventListener('DOMContentLoaded', function() {
  console.log('DOM loaded, starting fetchProductData...');
  fetchProductData();
});

// Function to navigate to products page with filters
function goToProducts(filterType, filterValue) {
    let url = '/Ecom_website/products';
    let params = new URLSearchParams();
    
    if (filterType === 'category') {
        params.append('category', filterValue);
    } else if (filterType === 'material') {
        params.append('materials', filterValue);
    }
    
    if (params.toString()) {
        url += '?' + params.toString();
    }
    
    window.location.href = url;
}

// Wishlist functions
async function toggleWishlist(productId, element) {
    const icon = element || event.target;
    
    try {
        // Call API first to get accurate state
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
                icon.classList.remove('bi-heart');
                icon.classList.add('bi-heart-fill');
                icon.style.color = '#dc3545';
                showToast('success', 'Đã thêm vào danh sách yêu thích!');
            } else if (data.data.action === 'removed') {
                icon.classList.remove('bi-heart-fill');
                icon.classList.add('bi-heart');
                icon.style.color = '';
                showToast('info', 'Đã xóa khỏi danh sách yêu thích!');
            }
            
            // Update wishlist count if provided
            if (data.data.wishlist_count !== undefined) {
                updateWishlistCount(data.data.wishlist_count);
            }
        } else {
            if (data.message && data.message.includes('đăng nhập')) {
                showToast('warning', data.message);
                setTimeout(() => {
                    window.location.href = '/Ecom_website/signin';
                }, 2000);
            } else {
                showToast('error', data.message || 'Có lỗi xảy ra!');
            }
        }
    } catch (error) {
        console.error('Wishlist toggle error:', error);
        showToast('error', 'Lỗi kết nối!');
    }
}

async function addToCartFromHome(productId, productName) {
    const icon = event.target;
    
    try {
        // Temporarily change icon for feedback
        const originalClass = icon.className;
        icon.className = 'bi bi-check-circle-fill';
        icon.style.color = '#28a745';
        
        const response = await fetch('/Ecom_website/cart/add', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: `product_id=${productId}&quantity=1`
        });
        
        const data = await response.json();
        
        if (data.success) {
            showToast('success', 'Đã thêm vào giỏ hàng!');
            // Update cart count if available
            if (data.data && data.data.cartTotal !== undefined) {
                updateCartCount(data.data.cartTotal);
            }
            
            // Revert icon after 1 second
            setTimeout(() => {
                icon.className = originalClass;
                icon.style.color = '';
            }, 1000);
        } else {
            // Revert icon immediately on error
            icon.className = originalClass;
            icon.style.color = '';
            showToast('error', data.message || 'Có lỗi xảy ra!');
        }
    } catch (error) {
        console.error('Add to cart error:', error);
        // Revert icon on error
        icon.className = 'bi bi-cart';
        icon.style.color = '';
        showToast('error', 'Lỗi kết nối!');
    }
}

function updateCartCount(count) {
    const cartBadge = document.querySelector('#cartBadge');
    if (cartBadge) {
        cartBadge.textContent = count;
    }
}

function updateWishlistCount(count) {
    const wishlistBadge = document.querySelector('#wishlistBadge');
    if (wishlistBadge) {
        wishlistBadge.textContent = count;
    }
}

// Load wishlist status for products when page loads
async function loadWishlistStatus() {
    try {
        console.log('Loading wishlist status...');
        const response = await fetch('/Ecom_website/wishlist/status', {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
        
        if (response.ok) {
            const data = await response.json();
            console.log('Wishlist status response:', data);
            
            if (data.success && data.data.wishlist_items && data.data.wishlist_items.length > 0) {
                console.log('Wishlist items found:', data.data.wishlist_items);
                
                // Update UI for products in wishlist
                data.data.wishlist_items.forEach(productId => {
                    console.log('Looking for icons for product ID:', productId);
                    
                    // Try different selectors
                    const selector1 = `i[onclick*="toggleWishlist(${productId},"]`;
                    const selector2 = `i[onclick*="toggleWishlist(${productId}"]`;
                    const selector3 = `.bi-heart[onclick*="${productId}"]`;
                    
                    console.log('Trying selector1:', selector1);
                    let icons = document.querySelectorAll(selector1);
                    console.log('Found icons with selector1:', icons.length);
                    
                    if (icons.length === 0) {
                        console.log('Trying selector2:', selector2);
                        icons = document.querySelectorAll(selector2);
                        console.log('Found icons with selector2:', icons.length);
                    }
                    
                    if (icons.length === 0) {
                        console.log('Trying selector3:', selector3);
                        icons = document.querySelectorAll(selector3);
                        console.log('Found icons with selector3:', icons.length);
                    }
                    
                    // Also check what's actually in the DOM
                    const allHeartIcons = document.querySelectorAll('.bi-heart');
                    console.log('Total heart icons found:', allHeartIcons.length);
                    allHeartIcons.forEach((icon, index) => {
                        console.log(`Heart icon ${index} onclick:`, icon.getAttribute('onclick'));
                    });
                    
                    icons.forEach(icon => {
                        console.log('Updating icon for product', productId);
                        icon.classList.remove('bi-heart');
                        icon.classList.add('bi-heart-fill');
                        icon.style.color = '#dc3545';
                    });
                });
            } else {
                console.log('No wishlist items or empty wishlist');
            }
        } else {
            console.log('Response not ok:', response.status);
        }
    } catch (error) {
        console.log('Could not load wishlist status:', error);
    }
}

function showToast(type, message) {
    if (typeof Swal !== 'undefined') {
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true
        });
        Toast.fire({
            icon: type,
            title: message
        });
    } else {
        alert(message);
    }
}

// View more functions
function viewMoreNewArrivals() {
    // Redirect to products page with new arrivals filter
    window.location.href = '/Ecom_website/products?sort=newest&limit=10';
}

function viewMorePopular() {
    // Redirect to products page with most popular filter  
    window.location.href = '/Ecom_website/products?sort=popular&limit=10';
}

    </script>
</body>
</html>
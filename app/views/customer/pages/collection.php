<?php
$title = $data['title'] ?? 'Collection';
$collection = $data['collection'] ?? null;
$products = $data['products'] ?? [];
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?> - Jewelry Store</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="<?= asset('css/css.css?v=' . time()) ?>" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html, body {
            height: 100vh;
            width: 100vw;
            margin: 0;
            padding: 0;
            overflow: hidden;
            position: fixed;
        }

        body {
            font-family: "Inter", sans-serif;
        }

        .container-fluid {
            padding: 0;
            margin: 0;
            width: 100%;
            height: 100%;
            overflow: hidden;
        }

        /* Hero Section */
        .hero-section {
            
            background-size: cover;
            background-position: center;
            min-height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 2rem;
            position: relative;
            margin-top: 80px;
            width: 100vw;
            margin-left: calc(50% - 50vw);
            margin-right: calc(50% - 50vw);
        }

        .hero-content {
            max-width: 800px;
            width: 100%;
            margin: 0 auto;
            padding: 0 2rem;
        }

        .hero-title {
            font-size: 3.5rem;
            font-weight: 300;
            letter-spacing: 8px;
            margin-bottom: 2rem;
            color: #3e2723;
            white-space: nowrap;
            overflow: visible;
        }

        .hero-description {
            font-size: 1.1rem;
            line-height: 1.8;
            color: #666;
            margin-bottom: 2rem;
            opacity: 1;
            transition: opacity 0.5s ease-out;
            max-width: 700px;
            margin-left: auto;
            margin-right: auto;
        }

        .hero-description.fade-out {
            opacity: 0;
        }

        .btn-view-details {
            background: transparent;
            border: 2px solid #d4af37;
            color: #d4af37;
            padding: 0.8rem 3rem;
            font-size: 1rem;
            letter-spacing: 2px;
            transition: all 0.5s ease;
            cursor: pointer;
            border-radius: 0;
            opacity: 1;
            white-space: nowrap;
        }

        .btn-view-details:hover {
            background: #d4af37;
            color: white;
        }

        /* Product Cards Section */
        .products-section {
            position: absolute;
            bottom: -100%;
            left: 0;
            right: 0;
            padding: 3rem 0;
            opacity: 0;
            transition: all 0.8s ease-out;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            z-index: 10;
            width: 100%;
            height: 100%;
            overflow: visible;
        }

        .products-section.show {
            bottom: 0;
            opacity: 1;
        }

        .product-carousel {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 2rem;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 2rem;
            overflow: visible;
            position: relative;
            z-index: 1;
        }

        .carousel-nav {
            width: 50px;
            height: 50px;
            border: 2px solid #d4af37;
            background: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s;
            flex-shrink: 0;
            color: #d4af37;
            font-size: 1.5rem;
        }

        .carousel-nav:hover {
            background: #d4af37;
            color: white;
        }

        .carousel-nav:disabled {
            opacity: 0.3;
            cursor: not-allowed;
            pointer-events: none;
        }

        .carousel-nav.disabled {
            opacity: 0.3;
            cursor: not-allowed;
            pointer-events: none;
        }

        #collectionTitle {
            text-transform: <?= $collection ? 'uppercase' : 'none' ?>;
            text-decoration: none !important;
            font-family: 'Times New Roman', Times, serif;
        }

        .cards-container {
            display: flex;
            gap: 2rem;
            overflow: visible;
            padding: 20px 10px;
            margin: -20px -10px;
            justify-content: center;
            align-items: center;
        }

        .product-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 8px 24px rgba(212, 175, 55, 0.15);
            width: 320px;
            height: 400px;
            flex-shrink: 0;
            transition: all 0.3s ease;
            border: 2px solid rgba(212, 175, 55, 0.1);
            position: relative;
            z-index: 1;
            cursor: pointer;
            overflow: hidden;
        }

        .product-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 35px rgba(212, 175, 55, 0.3);
            border-color: rgba(212, 175, 55, 0.3);
            z-index: 2;
        }

        .product-image {
            width: 100%;
            height: 200px;
            background: linear-gradient(135deg, #f5f3ed 0%, #f8f6f0 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }

        .product-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s ease;
        }

        .product-card:hover .product-image img {
            transform: scale(1.05);
        }

        .product-info {
            padding: 1.5rem;
            text-align: center;
            width: 100%;
        }

        .product-title {
            font-size: 1.4rem;
            font-weight: 600;
            margin-bottom: 1rem;
            color: #3e2723;
            letter-spacing: 1.5px;
            font-family: 'Playfair Display', serif;
            text-transform: uppercase;
        }

        .product-description {
            font-size: 1rem;
            color: #666;
            line-height: 1.6;
            margin-bottom: 0;
            display: -webkit-box;
            -webkit-line-clamp: 4;
            line-clamp: 4;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }



        @media (max-width: 1200px) {
            .hero-title {
                font-size: 2.8rem;
                letter-spacing: 6px;
            }
        }

        @media (max-width: 992px) {
            .hero-title {
                font-size: 2.5rem;
                letter-spacing: 4px;
            }
            
            .product-carousel {
                gap: 1.5rem;
            }
            
            .product-card {
                width: 260px;
            }
        }

        @media (max-width: 768px) {
            .hero-section {
                margin-top: 60px;
                min-height: 70vh;
                padding: 1.5rem;
            }
            
            .hero-content {
                padding: 0 1rem;
            }
            
            .hero-title {
                font-size: 2rem;
                letter-spacing: 3px;
                white-space: normal;
                word-wrap: break-word;
            }

            .hero-description {
                font-size: 1rem;
            }

            .product-carousel {
                gap: 1rem;
                padding: 0 1rem;
            }

            .cards-container {
                gap: 1rem;
            }

            .product-card {
                width: 300px;
            }

            .carousel-nav {
                width: 40px;
                height: 40px;
                font-size: 1.2rem;
            }
        }

        @media (max-width: 576px) {
            .hero-title {
                font-size: 1.5rem;
                letter-spacing: 2px;
            }
            
            .hero-description {
                font-size: 0.9rem;
            }

            .btn-view-details {
                padding: 0.7rem 2rem;
                font-size: 0.9rem;
            }

            .product-card {
                width: 280px;
            }

            .product-image {
                height: 180px;
            }

            .product-info {
                padding: 1rem;
            }

            .product-title {
                font-size: 1.2rem;
            }

            .product-description {
                font-size: 0.9rem;
            }
            
            .product-carousel {
                padding: 0 0.5rem;
            }
        }

        /* Mobile gallery styles */
        .mobile-gallery-wrapper {
            display: none;
            max-width: 100%;
            padding: 0 1rem;
        }

        .gallery-slider {
            position: relative;
            width: 100%;
            overflow: hidden;
        }

        .gallery-slide {
            display: none;
            width: 100%;
            box-sizing: border-box;
        }

        .gallery-slide.active {
            display: block;
        }

        .mobile-slide-card {
            width: 100%;
            margin: 0 auto;
            border-radius: 12px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.08);
            overflow: hidden;
        }

        .gallery-dots {
            display: flex;
            justify-content: center;
            gap: 8px;
            align-items: center;
        }

        .gallery-dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background: #ddd;
            border: none;
            cursor: pointer;
            transition: transform 0.2s, background 0.2s;
        }

        .gallery-dot.active {
            background: #d4af37;
            transform: scale(1.1);
        }

        @media (max-width: 768px) {
            .product-carousel { display: none !important; }
            .mobile-gallery-wrapper { display: block !important; }
            .product-card { width: 100% !important; }
        }
    </style>
</head>
<body>
    <!-- Include Header -->
    <?php include __DIR__ . '/../components/header.php'; ?>

    <!-- Hero Section -->
    <div class="container-fluid p-0">
        <section class="hero-section">
            <div class="hero-content">
                <h1 class="hero-title" id="collectionTitle"><?= $collection ? strtoupper($collection->collection_name) : 'COLLECTION' ?></h1>
                <p class="hero-description" id="collectionDescription">
                    <?= $collection ? $collection->description : 'Khám phá bộ sưu tập trang sức đẳng cấp với những thiết kế độc đáo và chất lượng hoàn hảo.' ?>
                </p>
                <button class="btn-view-details" onclick="showProducts()" type="button">Xem chi tiết</button>
            </div>

            <!-- Products Section -->
            <div class="products-section" id="productsSection">
                <div class="product-carousel">
                    <button class="carousel-nav" id="prevBtn" onclick="previousSlide()">
                        <span>‹</span>
                    </button>
                    
                    <div class="cards-container" id="cardsContainer">
                        <!-- Products will be dynamically loaded here -->
                    </div>

                    <button class="carousel-nav" id="nextBtn" onclick="nextSlide()">
                        <span>›</span>
                    </button>
                </div>
                <!-- Mobile gallery (hidden on desktop, shown on small screens) -->
                <div class="mobile-gallery-wrapper d-none" id="mobileGalleryWrapper">
                    <div class="gallery-slider" id="mobileGallery">
                        <!-- mobile slides will be injected here -->
                    </div>
                    <div class="gallery-dots text-center mt-3" id="mobileDots" role="tablist" aria-label="Chỉ mục sản phẩm">
                        <!-- dots injected here -->
                    </div>
                </div>
            </div>
        </section>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // PHP data to JavaScript
        const collectionData = <?= $collection ? json_encode($collection) : 'null' ?>;
        const productsData = <?= json_encode($products) ?>;
        
        let currentIndex = 0;
        let productsShown = false;
        const productsPerPage = 3;

        function renderProducts() {
            const container = document.getElementById('cardsContainer');
            const mobileWrapper = document.getElementById('mobileGalleryWrapper');
            const mobileGallery = document.getElementById('mobileGallery');
            const mobileDots = document.getElementById('mobileDots');

            // Reset
            container.innerHTML = '';
            if (mobileGallery) mobileGallery.innerHTML = '';
            if (mobileDots) mobileDots.innerHTML = '';

            if (!productsData || productsData.length === 0) {
                const emptyHtml = '<div class="text-center text-muted">Không có sản phẩm nào trong bộ sưu tập này.</div>';
                container.innerHTML = emptyHtml;
                if (mobileGallery) mobileGallery.innerHTML = emptyHtml;
                // Show appropriate UI
                if (mobileWrapper) mobileWrapper.classList.add('d-none');
                document.querySelector('.product-carousel').classList.remove('d-none');
                return;
            }

            const isMobile = window.innerWidth <= 768;

            // Clamp currentIndex
            if (currentIndex < 0) currentIndex = 0;
            if (currentIndex >= productsData.length) currentIndex = Math.max(0, productsData.length - 1);
            const baseUrl = '<?= BASE_URL ?>';

            if (isMobile && mobileWrapper && mobileGallery && mobileDots) {
                // Render mobile slides (one product per slide)
                productsData.forEach((product, index) => {
                    const slide = document.createElement('div');
                    slide.className = 'gallery-slide' + (index === currentIndex ? ' active' : '');
                    slide.setAttribute('data-index', index);
                    // Build image url
                    let imageUrl = baseUrl + '/public/assets/images/placeholder.svg';
                    if (product.main_image && product.main_image !== 'assets/images/placeholder.svg') {
                        if (product.main_image.startsWith('http') || product.main_image.startsWith('/')) {
                            imageUrl = product.main_image;
                        } else {
                            imageUrl = baseUrl + '/' + product.main_image;
                        }
                    } else if (product.images && product.images.trim() !== '') {
                        const imageArray = product.images.split(',');
                        const firstImage = imageArray[0].trim();
                        if (firstImage) {
                            if (firstImage.startsWith('http') || firstImage.startsWith('/')) {
                                imageUrl = firstImage;
                            } else {
                                imageUrl = baseUrl + '/' + firstImage;
                            }
                        }
                    }

                    slide.innerHTML = `
                        <div class="product-card mobile-slide-card" onclick="window.location.href='<?= BASE_URL ?>/product/${product.slug || product.product_id}'">
                            <div class="product-image">
                                <img src="${imageUrl}" alt="${product.name || 'Sản phẩm'}" onerror="this.src='${baseUrl}/public/assets/images/placeholder.svg'">
                            </div>
                            <div class="product-info">
                                <h3 class="product-title">${product.name || 'Sản phẩm'}</h3>
                                <p class="product-description">${product.description || ''}</p>
                            </div>
                        </div>
                    `;

                    mobileGallery.appendChild(slide);
                });

                // Create dots
                productsData.forEach((p, i) => {
                    const dot = document.createElement('button');
                    dot.className = 'gallery-dot' + (i === currentIndex ? ' active' : '');
                    dot.setAttribute('aria-label', `Chuyển đến sản phẩm ${i + 1}`);
                    dot.addEventListener('click', () => goToMobileSlide(i));
                    mobileDots.appendChild(dot);
                });

                // Show mobile wrapper, hide desktop carousel
                mobileWrapper.classList.remove('d-none');
                document.querySelector('.product-carousel').classList.add('d-none');

                // Touch support (swipe)
                let touchStartX = 0;
                let touchEndX = 0;
                mobileGallery.addEventListener('touchstart', e => {
                    touchStartX = e.changedTouches[0].screenX;
                }, {passive: true});
                mobileGallery.addEventListener('touchend', e => {
                    touchEndX = e.changedTouches[0].screenX;
                    const diff = touchStartX - touchEndX;
                    const threshold = 40;
                    if (Math.abs(diff) > threshold) {
                        if (diff > 0) {
                            // left swipe
                            goToMobileSlide(Math.min(currentIndex + 1, productsData.length - 1));
                        } else {
                            // right swipe
                            goToMobileSlide(Math.max(currentIndex - 1, 0));
                        }
                    }
                }, {passive: true});

                // Ensure active slide is visible
                updateMobileActive();
                return;
            }

            // Desktop / tablet behavior (grid-like carousel)
            if (mobileWrapper) mobileWrapper.classList.add('d-none');
            document.querySelector('.product-carousel').classList.remove('d-none');

            const visibleProducts = productsData.slice(currentIndex, currentIndex + productsPerPage);

            visibleProducts.forEach(product => {
                const card = document.createElement('div');
                card.className = 'product-card';
                card.style.cursor = 'pointer';
                
                // Add click event to go to product detail
                card.addEventListener('click', () => {
                    window.location.href = '<?= BASE_URL ?>/product/' + (product.slug || product.product_id);
                });

                // Get image url
                let imageUrl = baseUrl + '/public/assets/images/placeholder.svg';
                if (product.main_image && product.main_image !== 'assets/images/placeholder.svg') {
                    if (product.main_image.startsWith('http') || product.main_image.startsWith('/')) {
                        imageUrl = product.main_image;
                    } else {
                        imageUrl = baseUrl + '/' + product.main_image;
                    }
                } else if (product.images && product.images.trim() !== '') {
                    const imageArray = product.images.split(',');
                    const firstImage = imageArray[0].trim();
                    if (firstImage) {
                        if (firstImage.startsWith('http') || firstImage.startsWith('/')) {
                            imageUrl = firstImage;
                        } else {
                            imageUrl = baseUrl + '/' + firstImage;
                        }
                    }
                }

                card.innerHTML = `
                    <div class="product-image">
                        <img src="${imageUrl}" alt="${product.name || 'Sản phẩm'}" onerror="this.src='${baseUrl}/public/assets/images/placeholder.svg'">
                    </div>
                    <div class="product-info">
                        <h3 class="product-title">${product.name || 'Sản phẩm'}</h3>
                        <p class="product-description">${product.description || 'Sản phẩm chất lượng cao với thiết kế tinh tế và sang trọng.'}</p>
                    </div>
                `;
                container.appendChild(card);
            });

            updateNavigationButtons();
        }

        function updateNavigationButtons() {
            const prevBtn = document.getElementById('prevBtn');
            const nextBtn = document.getElementById('nextBtn');

            console.log('Updating navigation buttons');
            console.log('currentIndex:', currentIndex);
            console.log('productsPerPage:', productsPerPage);
            console.log('total products:', productsData ? productsData.length : 0);
            console.log('Can go prev:', currentIndex > 0);
            console.log('Can go next:', currentIndex + productsPerPage < (productsData ? productsData.length : 0));

            // Remove all disabled states first
            prevBtn.classList.remove('disabled');
            nextBtn.classList.remove('disabled');
            prevBtn.disabled = false;
            nextBtn.disabled = false;

            // Add disabled state if needed
            if (currentIndex === 0) {
                prevBtn.classList.add('disabled');
            }
            
            if (!productsData || currentIndex + productsPerPage >= productsData.length) {
                nextBtn.classList.add('disabled');
            }
        }

        /* Mobile helper: update active slide/dot */
        function updateMobileActive() {
            const slides = document.querySelectorAll('#mobileGallery .gallery-slide');
            const dots = document.querySelectorAll('#mobileDots .gallery-dot');
            slides.forEach(s => s.classList.remove('active'));
            dots.forEach(d => d.classList.remove('active'));
            if (slides[currentIndex]) slides[currentIndex].classList.add('active');
            if (dots[currentIndex]) dots[currentIndex].classList.add('active');
        }

        function goToMobileSlide(index) {
            if (!productsData || productsData.length === 0) return;
            currentIndex = index;
            updateMobileActive();
        }

        // Re-render on resize to switch between grid and mobile slider
        window.addEventListener('resize', function() {
            // small debounce
            clearTimeout(window.__collectionResizeTimer);
            window.__collectionResizeTimer = setTimeout(() => {
                renderProducts();
            }, 150);
        });

        function showProducts() {
            console.log('showProducts called');
            console.log('productsShown:', productsShown);
            console.log('productsData:', productsData);
            console.log('productsData length:', productsData ? productsData.length : 'No data');
            
            // Always show the products section regardless of data
            const description = document.getElementById('collectionDescription');
            const button = document.querySelector('.btn-view-details');
            const productsSection = document.getElementById('productsSection');
            
            // Fade out description and button
            if (description) {
                description.classList.add('fade-out');
            }
            if (button) {
                button.style.opacity = '0';
                button.style.pointerEvents = 'none';
            }
            
            setTimeout(() => {
                if (productsSection) {
                    productsSection.classList.add('show');
                }
                renderProducts(); // Render products
            }, 500);
            
            productsShown = true;
        }

        function nextSlide() {
            console.log('nextSlide called');
            console.log('currentIndex:', currentIndex);
            console.log('productsPerPage:', productsPerPage);
            console.log('productsData.length:', productsData ? productsData.length : 'No data');
            
            // Check if button is disabled
            const nextBtn = document.getElementById('nextBtn');
            if (nextBtn.classList.contains('disabled')) {
                console.log('Next button is disabled');
                return;
            }
            
            if (productsData && currentIndex + productsPerPage < productsData.length) {
                currentIndex++;
                console.log('Moving to next slide, new currentIndex:', currentIndex);
                renderProducts();
            } else {
                console.log('Cannot go to next slide - reached end');
            }
        }

        function previousSlide() {
            console.log('previousSlide called');
            console.log('currentIndex:', currentIndex);
            
            // Check if button is disabled
            const prevBtn = document.getElementById('prevBtn');
            if (prevBtn.classList.contains('disabled')) {
                console.log('Previous button is disabled');
                return;
            }
            
            if (currentIndex > 0) {
                currentIndex--;
                console.log('Moving to previous slide, new currentIndex:', currentIndex);
                renderProducts();
            } else {
                console.log('Cannot go to previous slide - already at start');
            }
        }



        // Initialize
        document.addEventListener('DOMContentLoaded', function() {
            renderProducts();
            
            // Debug info
            console.log('Collection data:', collectionData);
            console.log('Products data:', productsData);
            console.log('Products count:', productsData ? productsData.length : 0);
        });
    </script>

    <!-- Include Footer -->
    
</body>
</html>
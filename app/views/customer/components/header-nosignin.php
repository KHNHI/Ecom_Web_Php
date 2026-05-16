<!-- Navigation -->
<nav th:fragment="header" class="navbar navbar-expand-lg fixed-top">
    <div class="container">
        <!-- Logo - Link về trang chủ -->
        <a class="navbar-brand" href="<?= url('/') ?>">
            <i class="fas fa-gem me-2"></i>JEWELRY
        </a>
        
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav mx-auto">
                <!-- Home -->
                <li class="nav-item">
                    <a class="nav-link" href="<?= url('/') ?>">Home</a>
                </li>
                
                <!-- About Us -->
                <li class="nav-item">
                    <a class="nav-link" href="<?= url('/about') ?>">About us</a>
                </li>
                
                <!-- Category Dropdown -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="categoryDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        Category
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="categoryDropdown">
                        <li><a class="dropdown-item" href="<?= url('/products') ?>">All Products</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <!-- Categories sẽ được load bằng JavaScript -->
                        <div id="categoriesDropdown">
                            <li><span class="dropdown-item text-muted">Loading...</span></li>
                        </div>
                    </ul>
                </li>
                
                <!-- Collection Dropdown -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="collectionDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        Collection
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="collectionDropdown">
                        <!-- Collections sẽ được load bằng JavaScript -->
                        <div id="collectionsDropdown">
                            <li><span class="dropdown-item text-muted">Loading...</span></li>
                        </div>
                    </ul>
                </li>
            </ul>
            
            <div class="d-flex align-items-center">
                <!-- Search -->
                <div class="search-container me-3">
                    <input type="text" class="search-input" placeholder="What are you looking for?" id="headerSearch">
                    <button class="search-btn" onclick="performSearch()">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
                
                <!-- Auth Links -->
                <div class="me-3">
                    <a href="<?= BASE_URL ?>/signin" class="text-decoration-none me-3" style="color: #666;">Sign in</a>
                    <a href="<?= BASE_URL ?>/signup" class="text-decoration-none" style="color: #666;">Sign up</a>
                </div>
                
                <!-- Icons -->
                <div class="nav-icons">
                    <a href="<?= url('/wishlist') ?>" class="text-decoration-none me-2" style="color: #666;">
                        <i class="far fa-heart"></i>
                    </a>
                    <a href="<?= url('/cart') ?>" class="text-decoration-none" style="color: #666;">
                        <i class="fas fa-shopping-bag"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</nav>

<script>
// Search functionality
function performSearch() {
    const searchTerm = document.getElementById('headerSearch').value.trim();
    if (searchTerm) {
        window.location.href = `<?= url('/products') ?>?search=${encodeURIComponent(searchTerm)}`;
    }
}

// Enter key support for search
document.getElementById('headerSearch').addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        performSearch();
    }
});

// Load dynamic categories and collections for dropdowns
document.addEventListener('DOMContentLoaded', function() {
    // Load categories
    fetch('<?= url('/api/categories') ?>')
        .then(response => response.json())
        .then(data => {
            const categoriesContainer = document.getElementById('categoriesDropdown');
            categoriesContainer.innerHTML = ''; // Clear loading text
            if (data.success && data.data && data.data.length > 0) {
                data.data.forEach(category => {
                    const li = document.createElement('li');
                    li.innerHTML = `<a class="dropdown-item" href="<?= url('/products?category=') ?>${category.category_id}">${category.category_name || category.name}</a>`;
                    categoriesContainer.appendChild(li);
                });
            } else {
                 categoriesContainer.innerHTML = '<li><a class="dropdown-item text-muted" href="#">No categories found</a></li>';
            }
        })
        .catch(error => {
            console.error('Error loading categories:', error);
            const categoriesContainer = document.getElementById('categoriesDropdown');
            categoriesContainer.innerHTML = '<li><a class="dropdown-item text-danger" href="#">Error loading</a></li>';
        });

    // Load collections  
    fetch('<?= url('/api/collections') ?>')
        .then(response => response.json())
        .then(data => {
            const collectionsContainer = document.getElementById('collectionsDropdown');
            collectionsContainer.innerHTML = ''; // Clear loading text
            if (data.success && data.data && data.data.length > 0) {
                data.data.forEach(collection => {
                    const li = document.createElement('li');
                    li.innerHTML = `<a class="dropdown-item" href="<?= url('/products?collection=') ?>${collection.collection_id}">${collection.collection_name || collection.name}</a>`;
                    collectionsContainer.appendChild(li);
                });
            } else {
                collectionsContainer.innerHTML = '<li><a class="dropdown-item text-muted" href="#">No collections found</a></li>';
            }
        })
        .catch(error => {
            console.error('Error loading collections:', error);
            const collectionsContainer = document.getElementById('collectionsDropdown');
            collectionsContainer.innerHTML = '<li><a class="dropdown-item text-danger" href="#">Error loading</a></li>';
        });
});
</script>

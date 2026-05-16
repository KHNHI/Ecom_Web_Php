<?php
/**
 * ProductsController - Admin Product Management
 * Quản lý sản phẩm cho admin - Tuân thủ chuẩn MVC và OOP
 */
class ProductsController extends BaseController {
    private $productModel;
    private $categoryModel;
    private $collectionModel;
    
    // Upload configuration
    private $uploadPath;
    private $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    private $maxFileSize = 5242880; // 5MB

    public function __construct() {
        // Initialize Models - Dependency Injection pattern
        $this->productModel = $this->model('Product');
        $this->categoryModel = $this->model('Category');
        $this->collectionModel = $this->model('Collection');
                
        // Set absolute upload path
        $this->uploadPath = $_SERVER['DOCUMENT_ROOT'] . BASE_URL . '/public/uploads/products/';
    }

    /**
     * Hiển thị danh sách sản phẩm
     */
    public function index() {
        try {
            $products = $this->productModel->getAllWithDetails();
            
            // Load images từ database cho mỗi product (theo đúng schema images + image_usages)
            foreach ($products as $product) {
                $product->images = $this->productModel->getProductImages($product->product_id);
                $product->primary_image = $this->productModel->getProductPrimaryImage($product->product_id);
                
                // Fallback: Nếu không có image từ image_usages, dùng main_image column
                if (!$product->primary_image && !empty($product->main_image)) {
                    $product->primary_image = (object)[
                        'file_path' => $product->main_image,
                        'is_primary' => 1
                    ];
                }
            }
            
            $categories = $this->categoryModel->getAll();
            $collections = $this->collectionModel->getAll();

            $data = [
                'title' => 'Sản Phẩm',
                'products' => $products,
                'categories' => $categories,
                'collections' => $collections
            ];

            // Render admin page with layout
            $this->renderAdminPage('admin/pages/products', $data);

        } catch (Exception $e) {
            $this->view('admin/error', ['message' => 'Có lỗi xảy ra: ' . $e->getMessage()]);
        }
    }

    /**
     * Hiển thị form thêm sản phẩm mới
     * Method: GET
     */
    public function showAddForm() {
        try {
            // Lấy dữ liệu từ Models
            $categories = $this->categoryModel->getAll();
            $collections = $this->collectionModel->getAll();
            
            // Lấy danh sách materials từ Product Model
            $materials = $this->productModel->getAvailableMaterials();
            
            // Prepare data cho View
            $data = [
                'title' => 'Thêm Sản Phẩm Mới',
                'categories' => $categories,
                'collections' => $collections,
                'materials' => $materials,
                'pageTitle' => 'Thêm Sản Phẩm',
                'breadcrumb' => 'Home > Sản Phẩm > Thêm Mới'
            ];

            // Render view qua layout
            $this->renderAdminPage('admin/pages/add-product', $data);

        } catch (Exception $e) {
            $_SESSION['error'] = 'Có lỗi xảy ra: ' . $e->getMessage();
            $this->redirect(BASE_URL . '/admin/products');
        }
    }

    /**
     * Tạo sản phẩm mới
     * Method: POST
     */
    public function create() {
        // Chỉ chấp nhận POST request
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $_SESSION['error'] = 'Invalid request method!';
            $this->redirect(BASE_URL . '/admin/products');
            return;
        }

        try {
            // DEBUG: Log toàn bộ POST data
            error_log("=== CREATE PRODUCT - START ===");
            error_log("Full POST data: " . json_encode($_POST));
            error_log("category_id: " . (isset($_POST['category_id']) ? $_POST['category_id'] : 'NOT SET'));
            error_log("category_ids: " . (isset($_POST['category_ids']) ? json_encode($_POST['category_ids']) : 'NOT SET'));
            
            // Validate input data
            $validationErrors = $this->validateProductData($_POST);
            
            if (!empty($validationErrors)) {
                $_SESSION['error'] = implode('<br>', $validationErrors);
                $_SESSION['old_input'] = $_POST; // Giữ lại dữ liệu cũ
                $this->redirect(BASE_URL . '/admin/add-product');
                return;
            }

            // Prepare data cho Model
            $data = [
                'name' => trim($_POST['name']),
                'description' => trim($_POST['description'] ?? ''),
                'material' => $_POST['material'] ?? 'gold',
                'base_price' => floatval($_POST['base_price']),
                'sku' => trim($_POST['sku']),
                'collection_id' => !empty($_POST['collection_id']) ? intval($_POST['collection_id']) : null,
                'is_active' => isset($_POST['is_active']) ? 1 : 0
            ];

            // Tạo sản phẩm trong database
               // Prepare variants data
            $variants = [];
            if (isset($_POST['variants']) && is_array($_POST['variants'])) {
                foreach ($_POST['variants'] as $variant) {
                    if (!empty($variant['size']) && !empty($variant['color']) && 
                        isset($variant['price']) && isset($variant['stock'])) {
                        $variants[] = [
                            'size' => trim($variant['size']),
                            'color' => trim($variant['color']),
                            'price' => floatval($variant['price']),
                            'stock' => intval($variant['stock'])
                        ];
                    }
                }
            }

            // Tạo sản phẩm với variants trong database (using transaction)
            $productId = $this->productModel->createWithVariants($data, $variants);
            
            if (!$productId) {
                throw new Exception('Không thể tạo sản phẩm trong database');
            }

            // Xử lý upload images
            if (isset($_FILES['product_images']) && !empty($_FILES['product_images']['name'][0])) {
                $uploadResult = $this->handleProductImages($productId, $_FILES['product_images']);
                
                if (!$uploadResult['success']) {
                    // Nếu upload fail, log warning nhưng vẫn thành công
                    error_log('Warning: Product created but image upload failed: ' . implode(', ', $uploadResult['errors']));
                }
            }

            // Xử lý categories (many-to-many relationship)
            // Hỗ trợ cả single select (category_id) và multi-select (category_ids[])
            error_log("CREATE PRODUCT - Checking category data...");
            error_log("POST category_id: " . (isset($_POST['category_id']) ? $_POST['category_id'] : 'NOT SET'));
            error_log("POST category_id empty: " . (empty($_POST['category_id']) ? 'TRUE' : 'FALSE'));
            
            if (!empty($_POST['category_id'])) {
                // Single category from dropdown
                error_log("Using single category_id: " . $_POST['category_id']);
                $this->attachCategoriesToProduct($productId, [(int)$_POST['category_id']]);
            } elseif (!empty($_POST['category_ids']) && is_array($_POST['category_ids'])) {
                // Multiple categories (legacy support)
                error_log("Using multiple category_ids: " . json_encode($_POST['category_ids']));
                $this->attachCategoriesToProduct($productId, $_POST['category_ids']);
            } else {
                error_log("WARNING: No category selected or sent in POST data");
            }

            $_SESSION['success'] = 'Tạo sản phẩm thành công!';
            $this->redirect(BASE_URL . '/admin/products');
            
        } catch (Exception $e) {
            $_SESSION['error'] = 'Lỗi: ' . $e->getMessage();
            $_SESSION['old_input'] = $_POST;
            $this->redirect(BASE_URL . '/admin/add-product');
        }
    }

    /**
     * Cập nhật sản phẩm
     * Method: POST
     */
    public function update() {
        // Chỉ chấp nhận POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $_SESSION['error'] = 'Invalid request method!';
            $this->redirect(BASE_URL . '/admin/products');
            return;
        }

        try {
            $productId = $_GET['id'] ?? null;
            
            if (!$productId) {
                throw new Exception('Không tìm thấy ID sản phẩm');
            }

            // Validate input
            $validationErrors = $this->validateProductData($_POST);
            
            if (!empty($validationErrors)) {
                $_SESSION['error'] = implode('<br>', $validationErrors);
                $this->redirect(BASE_URL . '/admin/edit-product?id=' . $productId);
                return;
            }

            // Prepare data - CHỈ update fields được gửi lên
            $data = [
                'name' => trim($_POST['name']),
                'description' => trim($_POST['description'] ?? ''),
                'material' => $_POST['material'] ?? 'gold',
                'base_price' => floatval($_POST['base_price']),
                'sku' => trim($_POST['sku']),
                'collection_id' => !empty($_POST['collection_id']) ? intval($_POST['collection_id']) : null,
                'is_active' => isset($_POST['is_active']) ? 1 : 0
            ];

            // KHÔNG tự động update slug khi update (giữ nguyên slug cũ)
            // Chỉ update slug nếu tên thay đổi hoặc được gửi lên explicitly
            // Điều này tránh lỗi duplicate slug

            // Update product trong database
            if ($this->productModel->update($productId, $data)) {
                // Xử lý variants (Cập nhật thông minh)
                if (isset($_POST['variants']) && is_array($_POST['variants'])) {
                    $db = Database::getInstance();
                    $submittedVariantIds = [];
                    
                    // Lấy tất cả variant hiện tại của product
                    $db->query("SELECT variant_id FROM product_variants WHERE product_id = :product_id");
                    $db->bind(':product_id', $productId);
                    $existingVariants = $db->resultSet();
                    $existingIds = [];
                    if ($existingVariants) {
                        foreach ($existingVariants as $v) {
                            $existingIds[] = $v->variant_id;
                        }
                    }

                    foreach ($_POST['variants'] as $variant) {
                        if (!empty($variant['size']) && !empty($variant['color']) && 
                            isset($variant['price']) && isset($variant['stock'])) {
                            
                            if (!empty($variant['variant_id'])) {
                                // Cập nhật variant cũ
                                $db->query("UPDATE product_variants SET size = :size, color = :color, price = :price, stock = :stock WHERE variant_id = :variant_id AND product_id = :product_id");
                                $db->bind(':variant_id', $variant['variant_id']);
                                $db->bind(':product_id', $productId);
                                $db->bind(':size', trim($variant['size']));
                                $db->bind(':color', trim($variant['color']));
                                $db->bind(':price', floatval($variant['price']));
                                $db->bind(':stock', intval($variant['stock']));
                                $db->execute();
                                $submittedVariantIds[] = $variant['variant_id'];
                            } else {
                                // Thêm variant mới
                                $db->query("INSERT INTO product_variants (product_id, size, color, price, stock) VALUES (:product_id, :size, :color, :price, :stock)");
                                $db->bind(':product_id', $productId);
                                $db->bind(':size', trim($variant['size']));
                                $db->bind(':color', trim($variant['color']));
                                $db->bind(':price', floatval($variant['price']));
                                $db->bind(':stock', intval($variant['stock']));
                                $db->execute();
                            }
                        }
                    }

                    // Xóa các variant bị loại bỏ khỏi form
                    foreach ($existingIds as $eId) {
                        if (!in_array($eId, $submittedVariantIds)) {
                            try {
                                $db->query("DELETE FROM product_variants WHERE variant_id = :variant_id AND product_id = :product_id");
                                $db->bind(':variant_id', $eId);
                                $db->bind(':product_id', $productId);
                                $db->execute();
                            } catch (Exception $e) {
                                error_log("Failed to delete variant_id $eId (likely FK constraint): " . $e->getMessage());
                                // Soft delete fallback: Set stock = 0
                                $db->query("UPDATE product_variants SET stock = 0 WHERE variant_id = :variant_id");
                                $db->bind(':variant_id', $eId);
                                $db->execute();
                            }
                        }
                    }
                }
                
                // Xử lý upload images mới (nếu có)
                if (isset($_FILES['product_images']) && !empty($_FILES['product_images']['name'][0])) {
                    $uploadResult = $this->handleProductImages($productId, $_FILES['product_images']);
                    
                    if (!$uploadResult['success']) {
                        error_log('Warning: Product updated but image upload failed: ' . implode(', ', $uploadResult['errors']));
                    }
                }

                // Xử lý categories (nếu có)
                // Hỗ trợ cả single select (category_id) và multi-select (category_ids[])
                if (!empty($_POST['category_id'])) {
                    // Single category from dropdown
                    $this->attachCategoriesToProduct($productId, [(int)$_POST['category_id']]);
                } elseif (isset($_POST['category_ids']) && is_array($_POST['category_ids'])) {
                    // Multiple categories (legacy support)
                    $this->attachCategoriesToProduct($productId, $_POST['category_ids']);
                }

                $_SESSION['success'] = 'Cập nhật sản phẩm thành công!';
            } else {
                $_SESSION['error'] = 'Có lỗi xảy ra khi cập nhật sản phẩm!';
            }
            
            error_log("Product #$productId updated successfully");
            
        } catch (Exception $e) {
            $_SESSION['error'] = 'Lỗi: ' . $e->getMessage();
            error_log('ProductsController::update Error: ' . $e->getMessage());
        }
        
        $this->redirect(BASE_URL . '/admin/products');
    }

    /**
     * Hiển thị form chỉnh sửa sản phẩm
     * Sử dụng cùng view với product-details (view/edit mode)
     */
    /**
     * Hiển thị form chỉnh sửa sản phẩm
     * Tái sử dụng add-product.php với data của sản phẩm cần edit
     */
    public function showEditForm() {
        try {
            $productId = $_GET['id'] ?? null;
            
            if (!$productId) {
                throw new Exception('Không tìm thấy ID sản phẩm');
            }

            $product = $this->productModel->findById($productId);
            
            if (!$product) {
                throw new Exception('Sản phẩm không tồn tại');
            }

            // Load images và variants từ database
            $product->images = $this->productModel->getProductImages($productId);
            $product->primary_image = $this->productModel->getProductPrimaryImage($productId);
            $product->variants = $this->productModel->getProductVariants($productId);
            
            // Load categories của product này
            $productCategories = $this->productModel->getProductCategories($productId);
            $product->category_ids = array_column($productCategories, 'category_id');

            // Load data cho dropdowns
            $categories = $this->categoryModel->getAll();
            $collections = $this->collectionModel->getAll();
            $materials = $this->productModel->getAvailableMaterials();
            
            $data = [
                'title' => 'Chỉnh Sửa Sản Phẩm',
                'product' => $product,
                'categories' => $categories,
                'collections' => $collections,
                'materials' => $materials,
                'pageTitle' => 'Chỉnh Sửa Sản Phẩm',
                'breadcrumb' => 'Home > Sản Phẩm > Chỉnh Sửa',
                'isEdit' => true  // Flag để view biết đây là edit mode
            ];

            // TÁI SỬ DỤNG add-product.php - DRY Principle
            $this->renderAdminPage('admin/pages/add-product', $data);

        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            $this->redirect(BASE_URL . '/admin/products');
        }
    }

    /**
     * Hiển thị chi tiết sản phẩm (View mode)
     */
    public function showDetails() {
        try {
            $productId = $_GET['id'] ?? null;
            
            if (!$productId) {
                throw new Exception('Không tìm thấy ID sản phẩm');
            }

            $product = $this->productModel->findById($productId);
            
            if (!$product) {
                throw new Exception('Sản phẩm không tồn tại');
            }

            // Load images từ database (ĐÚNG SCHEMA: images + image_usages)
            $product->images = $this->productModel->getProductImages($productId);
            $product->primary_image = $this->productModel->getProductPrimaryImage($productId);

            // Lấy thêm data cho dropdown (nếu cần chỉnh sửa)
            $categories = $this->categoryModel->getAll();
            $collections = $this->collectionModel->getAll();
            $materials = $this->productModel->getAvailableMaterials();
            
            $data = [
                'title' => 'Chi Tiết Sản Phẩm',
                'product' => $product,
                'categories' => $categories,
                'collections' => $collections,
                'materials' => $materials,
                'pageTitle' => 'Chi Tiết Sản Phẩm',
                'breadcrumb' => 'Home > Sản Phẩm > Chi Tiết',
                'editMode' => false  // View mode only
            ];

            $this->renderAdminPage('admin/pages/product-details', $data);

        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            $this->redirect(BASE_URL . '/admin/products');
        }
    }
    
    /**
     * Xóa sản phẩm (soft delete)
     */
    /**
     * Xóa sản phẩm (soft delete - mặc định)
     */
    public function delete($id) {
        try {
            if ($this->productModel->delete($id)) {
                $_SESSION['success'] = 'Đã ẩn sản phẩm thành công!';
            } else {
                $_SESSION['error'] = 'Có lỗi xảy ra khi ẩn sản phẩm!';
            }
        } catch (Exception $e) {
            $_SESSION['error'] = 'Lỗi: ' . $e->getMessage();
        }
        
        $this->redirect(BASE_URL . '/admin/products');
    }
    
    /**
     * Xóa vĩnh viễn sản phẩm (hard delete)
     */
    public function hardDelete($id) {
        try {
            error_log("Hard deleting product ID: $id");
            
            if ($this->productModel->hardDelete($id)) {
                $_SESSION['success'] = 'Đã xóa vĩnh viễn sản phẩm thành công!';
                error_log("Product $id hard deleted successfully");
            } else {
                $_SESSION['error'] = 'Có lỗi xảy ra khi xóa sản phẩm!';
                error_log("Failed to hard delete product $id");
            }
        } catch (Exception $e) {
            $_SESSION['error'] = 'Lỗi: ' . $e->getMessage();
            error_log("Hard delete error: " . $e->getMessage());
        }
        
        $this->redirect(BASE_URL . '/admin/products');
    }

    /**
     * Xóa ảnh của sản phẩm
     * Method: POST (AJAX)
     */
    public function deleteImage() {
        header('Content-Type: application/json');
        
        try {
            $imageId = $_GET['image_id'] ?? null;
            $productId = $_GET['product_id'] ?? null;
            
            if (!$imageId || !$productId) {
                echo json_encode(['success' => false, 'message' => 'Thiếu thông tin ảnh']);
                return;
            }

            // Lấy thông tin ảnh từ database
            $db = Database::getInstance();
            $db->query("SELECT i.file_path FROM images i 
                       JOIN image_usages iu ON i.image_id = iu.image_id 
                       WHERE i.image_id = :image_id AND iu.ref_id = :product_id AND iu.ref_type = 'product'");
            $db->bind(':image_id', $imageId);
            $db->bind(':product_id', $productId);
            $image = $db->single();

            if (!$image) {
                echo json_encode(['success' => false, 'message' => 'Không tìm thấy ảnh']);
                return;
            }

            // Xóa file vật lý
            $filePath = $_SERVER['DOCUMENT_ROOT'] . BASE_URL . '/' . ltrim($image->file_path, '/');
            if (file_exists($filePath)) {
                unlink($filePath);
            }

            // Xóa trong database (image_usages và images)
            $db->query("DELETE FROM image_usages WHERE image_id = :image_id AND ref_id = :product_id AND ref_type = 'product'");
            $db->bind(':image_id', $imageId);
            $db->bind(':product_id', $productId);
            $db->execute();

            // Xóa trong bảng images nếu không còn usage nào
            $db->query("DELETE FROM images WHERE image_id = :image_id AND NOT EXISTS (SELECT 1 FROM image_usages WHERE image_id = :image_id)");
            $db->bind(':image_id', $imageId);
            $db->execute();

            echo json_encode(['success' => true, 'message' => 'Đã xóa ảnh thành công']);
            
        } catch (Exception $e) {
            error_log('deleteImage Error: ' . $e->getMessage());
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    // =================== PRIVATE HELPER METHODS (OOP Best Practice) ===================
    
    /**
     * Validate dữ liệu sản phẩm
     * @return array Mảng các lỗi validation
     */
    private function validateProductData($data) {
        $errors = [];

        // Validate tên sản phẩm
        if (empty($data['name']) || strlen(trim($data['name'])) < 3) {
            $errors[] = 'Tên sản phẩm phải có ít nhất 3 ký tự';
        }

        // Validate giá
        if (empty($data['base_price']) || !is_numeric($data['base_price']) || $data['base_price'] <= 0) {
            $errors[] = 'Giá sản phẩm phải là số dương';
        }

        // Validate SKU
        if (empty($data['sku'])) {
            $errors[] = 'SKU không được để trống';
        }

        // Validate stock (nếu có)
        if (isset($data['stock']) && (!is_numeric($data['stock']) || $data['stock'] < 0)) {
            $errors[] = 'Số lượng tồn kho phải là số không âm';
        }
             // Validate variants
        if (isset($data['variants']) && is_array($data['variants'])) {
            if (empty($data['variants'])) {
                $errors[] = 'Phải có ít nhất 1 biến thể sản phẩm';
            } else {
                foreach ($data['variants'] as $index => $variant) {
                    $variantNum = $index + 1;
                    
                    if (empty($variant['size'])) {
                        $errors[] = "Biến thể #{$variantNum}: Size không được để trống";
                    }
                    
                    if (empty($variant['color'])) {
                        $errors[] = "Biến thể #{$variantNum}: Màu sắc không được để trống";
                    }
                    
                    if (empty($variant['price']) || !is_numeric($variant['price']) || $variant['price'] <= 0) {
                        $errors[] = "Biến thể #{$variantNum}: Giá phải là số dương";
                    }
                    
                    if (!isset($variant['stock']) || !is_numeric($variant['stock']) || $variant['stock'] < 0) {
                        $errors[] = "Biến thể #{$variantNum}: Số lượng tồn kho phải là số không âm";
                    }
                }
            }
        } else {
            $errors[] = 'Phải có ít nhất 1 biến thể sản phẩm';
        }

        return $errors;
    }

    /**
     * Xử lý upload ảnh sản phẩm theo đúng database schema
     * Lưu vào tables: images + image_usages
     * @return array ['success' => bool, 'errors' => array]
     */
    private function handleProductImages($productId, $files) {
        $result = ['success' => true, 'errors' => []];
        
        try {
            // Tạo thư mục upload nếu chưa tồn tại
            $productUploadPath = $this->uploadPath . $productId . '/';
            if (!is_dir($productUploadPath)) {
                mkdir($productUploadPath, 0777, true);
            }

            $uploadedFiles = [];
            $fileCount = count($files['name']);

            for ($i = 0; $i < $fileCount; $i++) {
                if ($files['error'][$i] === UPLOAD_ERR_OK) {
                    $fileName = $files['name'][$i];
                    $fileTmpName = $files['tmp_name'][$i];
                    $fileSize = $files['size'][$i];
                    $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

                    // Validate file extension
                    if (!in_array($fileExt, $this->allowedExtensions)) {
                        $result['errors'][] = "File {$fileName}: Định dạng không hợp lệ";
                        continue;
                    }

                    // Validate file size
                    if ($fileSize > $this->maxFileSize) {
                        $result['errors'][] = "File {$fileName}: Kích thước quá lớn (max 5MB)";
                        continue;
                    }

                    // Generate unique filename
                    $newFileName = 'product_' . $productId . '_' . uniqid() . '.' . $fileExt;
                    $destination = $productUploadPath . $newFileName;

                    // Move uploaded file
                    if (move_uploaded_file($fileTmpName, $destination)) {
                         $relativePath = 'public/uploads/products/' . $productId . '/' . $newFileName;
                        $uploadedFiles[] = [
                             'path' => $relativePath, // Use relative path for database
                            'is_primary' => ($i === 0) // First image is primary
                        ];
                    } else {
                        $result['errors'][] = "Không thể upload file {$fileName}";
                    }
                }
            }

            // Save images vào database theo ĐÚNG SCHEMA (images + image_usages)
            if (!empty($uploadedFiles)) {
                foreach ($uploadedFiles as $fileData) {
                    // Sử dụng Product Model method để insert vào images + image_usages
                    $usageId = $this->productModel->addProductImage(
                        $productId, 
                        $fileData['path'], 
                        $fileData['is_primary']
                    );
                    
                    if (!$usageId) {
                        $result['errors'][] = "Không thể lưu ảnh vào database: " . $fileData['path'];
                    }
                }
                
                // Update main_image column (backward compatibility)
                $mainImagePath = $uploadedFiles[0]['path']; // Already relative path
                $this->productModel->update($productId, ['main_image' => $mainImagePath]);
            }

            if (!empty($result['errors'])) {
                $result['success'] = false;
            }

        } catch (Exception $e) {
            $result['success'] = false;
            $result['errors'][] = $e->getMessage();
            error_log('handleProductImages Error: ' . $e->getMessage());
        }

        return $result;
    }

    /**
     * Gắn categories cho product (many-to-many)
     */
    private function attachCategoriesToProduct($productId, $categoryIds) {
        try {
            error_log("=== attachCategoriesToProduct CALLED ===");
            error_log("Product ID: " . $productId);
            error_log("Category IDs: " . json_encode($categoryIds));
            
            // Xóa các liên kết cũ
            $sql = "DELETE FROM product_categories WHERE product_id = :product_id";
            $db = Database::getInstance();
            $db->query($sql);
            $db->bind(':product_id', $productId);
            $deleted = $db->execute();
            error_log("Deleted old categories, rows affected: " . ($deleted ? 'success' : 'none'));

            // Thêm các liên kết mới
            $insertCount = 0;
            foreach ($categoryIds as $categoryId) {
                error_log("Inserting product_category - Product: $productId, Category: $categoryId");
                // Bảng product_categories chỉ có 2 cột: product_id và category_id
                $sql = "INSERT INTO product_categories (product_id, category_id) 
                        VALUES (:product_id, :category_id)";
                $db->query($sql);
                $db->bind(':product_id', $productId);
                $db->bind(':category_id', $categoryId);
                $result = $db->execute();
                if ($result) {
                    $insertCount++;
                    error_log("Successfully inserted category $categoryId");
                } else {
                    error_log("Failed to insert category $categoryId");
                }
            }
            
            error_log("Total categories inserted: $insertCount");
            error_log("=== attachCategoriesToProduct COMPLETED ===");
        } catch (Exception $e) {
            error_log('ERROR in attachCategoriesToProduct: ' . $e->getMessage());
            error_log('Stack trace: ' . $e->getTraceAsString());
        }
    }

    /**
     * Redirect helper method
     */
    private function redirect($url) {
        header("Location: $url");
        exit;
    }
}
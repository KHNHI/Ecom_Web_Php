<?php
/**
 * CollectionsController - Admin Collection Management
 * Quản lý bộ sưu tập cho admin - Tuân thủ chuẩn MVC và OOP
 */
class CollectionsController extends BaseController {
    private $collectionModel;
    
    // Upload configuration
    private $uploadPath = 'public/uploads/collections/';
    private $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    private $maxFileSize = 5242880; // 5MB

    public function __construct() {
        // Dependency Injection
        $this->collectionModel = $this->model('Collection');
    }

    /**
     * Hiển thị danh sách bộ sưu tập
     * Method: GET
     */
    public function index() {
        try {
            // Lấy tất cả collections từ Model
            $collections = $this->collectionModel->getAllWithProductCount(false); // false = show all
            
            // Thêm ảnh cover cho từng collection
            foreach ($collections as $collection) {
                $coverImage = $this->collectionModel->getCollectionCoverImage($collection->collection_id);
                $collection->image_path = $coverImage ? $coverImage->file_path : null;
                $collection->cover_image = $coverImage;
            }
            
            // Calculate statistics
            $total = count($collections);
            $active = 0;
            $totalProducts = 0;
            
            foreach ($collections as $collection) {
                if ($collection->is_active) {
                    $active++;
                }
                $totalProducts += ($collection->product_count ?? 0);
            }
            
            $stats = [
                'total' => $total,
                'active' => $active,
                'total_products' => $totalProducts,
                'best_sellers' => 0 // TODO: Calculate from sales data
            ];

            $data = [
                'title' => 'Bộ Sưu Tập',
                'collections' => $collections,
                'stats' => $stats
            ];

            // Render admin page with layout
            $this->renderAdminPage('admin/pages/collections', $data);

        } catch (Exception $e) {
            $this->view('admin/error', ['message' => 'Có lỗi xảy ra: ' . $e->getMessage()]);
        }
    }

    /**
     * Hiển thị form thêm bộ sưu tập mới
     * Method: GET
     */
    public function showAddForm() {
        try {
            // Lấy old input nếu có lỗi validation
            $oldInput = $_SESSION['old_input'] ?? [];
            unset($_SESSION['old_input']);
            
            $data = [
                'title' => 'Thêm Bộ Sưu Tập Mới',
                'pageTitle' => 'Thêm Bộ Sưu Tập',
                'breadcrumb' => 'Home > Bộ Sưu Tập > Thêm Mới',
                'oldInput' => $oldInput
            ];

            // Render view qua layout
            $this->renderAdminPage('admin/pages/add-collection', $data);

        } catch (Exception $e) {
            $_SESSION['error'] = 'Có lỗi xảy ra: ' . $e->getMessage();
            $this->redirect('index.php?url=collections');
        }
    }

    /**
     * Tạo bộ sưu tập mới
     * Method: POST
     */
    public function create() {
        // Chỉ chấp nhận POST request
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $_SESSION['error'] = 'Invalid request method!';
            $this->redirect('index.php?url=collections');
            return;
        }

        try {
            // Validate input data
            $validationErrors = $this->validateCollectionData($_POST);
            
            if (!empty($validationErrors)) {
                $_SESSION['error'] = implode('<br>', $validationErrors);
                $_SESSION['old_input'] = $_POST;
                $this->redirect('index.php?url=add-collection');
                return;
            }

            $collectionName = trim($_POST['name']);

            // KIỂM TRA TÊN ĐÃ TỒN TẠI CHƯA
            $existing = $this->collectionModel->findByName($collectionName);
            
            if ($existing) {
                $_SESSION['error'] = "Bộ sưu tập '<strong>" . htmlspecialchars($collectionName) . "</strong>' đã tồn tại! Vui lòng sử dụng tên khác.";
                $_SESSION['old_input'] = $_POST;
                error_log("Collection creation failed: Name already exists - $collectionName");
                $this->redirect('index.php?url=add-collection');
                return;
            }

            // Prepare data cho Model
            $data = [
                'collection_name' => $collectionName,
                'slug' => $this->generateSlug($collectionName),
                'description' => trim($_POST['description'] ?? ''),
                'is_active' => isset($_POST['is_active']) ? 1 : 0
            ];

            // Tạo collection trong database
            $collectionId = $this->collectionModel->create($data);
            
            if (!$collectionId) {
                throw new Exception('Không thể tạo bộ sưu tập trong database');
            }

            // Xử lý upload cover image
            if (isset($_FILES['cover_image']) && $_FILES['cover_image']['error'] === UPLOAD_ERR_OK) {
                $uploadResult = $this->handleCoverImageUpload($collectionId, $_FILES['cover_image']);
                
                if (!$uploadResult['success']) {
                    error_log('Warning: Collection created but cover image upload failed: ' . implode(', ', $uploadResult['errors']));
                }
            }

            $_SESSION['success'] = 'Tạo bộ sưu tập thành công!';
            error_log("Collection created successfully with ID: $collectionId");
            
            $this->redirect('index.php?url=collections');
            
        } catch (Exception $e) {
            $_SESSION['error'] = 'Lỗi: ' . $e->getMessage();
            $_SESSION['old_input'] = $_POST;
            error_log('CollectionsController::create Error: ' . $e->getMessage());
            $this->redirect('index.php?url=add-collection');
        }
    }

    /**
     * Hiển thị form chỉnh sửa bộ sưu tập
     */
    /**
     * Hiển thị form chỉnh sửa bộ sưu tập
     * TÁI SỬ DỤNG add-collection.php (DRY Principle)
     */
    public function showEditForm() {
        try {
            $collectionId = $_GET['id'] ?? null;
            
            if (!$collectionId) {
                throw new Exception('Không tìm thấy ID bộ sưu tập');
            }

            $collection = $this->collectionModel->findById($collectionId);
            
            if (!$collection) {
                throw new Exception('Bộ sưu tập không tồn tại');
            }
            
            $data = [
                'title' => 'Chỉnh Sửa Bộ Sưu Tập',
                'collection' => $collection,
                'pageTitle' => 'Chỉnh Sửa Bộ Sưu Tập',
                'breadcrumb' => 'Home > Bộ Sưu Tập > Chỉnh Sửa',
                'isEdit' => true  // Flag để view biết đây là edit mode
            ];

            // TÁI SỬ DỤNG add-collection.php
            $this->renderAdminPage('admin/pages/add-collection', $data);

        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            $this->redirect('index.php?url=collections');
        }
    }

    /**
     * Cập nhật bộ sưu tập
     * Method: POST
     */
    public function update() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $_SESSION['error'] = 'Invalid request method!';
            $this->redirect('index.php?url=collections');
            return;
        }

        try {
            $collectionId = $_GET['id'] ?? null;
            
            if (!$collectionId) {
                throw new Exception('Không tìm thấy ID bộ sưu tập');
            }

            // Validate
            $validationErrors = $this->validateCollectionData($_POST);
            
            if (!empty($validationErrors)) {
                $_SESSION['error'] = implode('<br>', $validationErrors);
                $this->redirect('index.php?url=edit-collection&id=' . $collectionId);
                return;
            }

            $collectionName = trim($_POST['name']);

            // KIỂM TRA TÊN TRÙNG (ngoại trừ collection hiện tại)
            $existing = $this->collectionModel->findByName($collectionName, $collectionId);
            
            if ($existing) {
                $_SESSION['error'] = "Tên bộ sưu tập '<strong>" . htmlspecialchars($collectionName) . "</strong>' đã được sử dụng! Vui lòng chọn tên khác.";
                error_log("Collection update failed: Name already exists - $collectionName (excluding ID: $collectionId)");
                $this->redirect('index.php?url=edit-collection&id=' . $collectionId);
                return;
            }

            $data = [
                'collection_name' => $collectionName,
                'slug' => $this->generateSlug($collectionName),
                'description' => trim($_POST['description'] ?? ''),
                'is_active' => isset($_POST['is_active']) ? 1 : 0
            ];

            if ($this->collectionModel->update($collectionId, $data)) {
                
                // Xử lý upload ảnh mới (nếu có)
                $keepExistingImage = isset($_POST['keep_existing_image']) && $_POST['keep_existing_image'] == '1';
                $hasNewImage = !empty($_FILES['cover_image']['name']);
                
                if ($hasNewImage) {
                    // Upload ảnh mới - sẽ ghi đè lên ảnh cũ
                    $uploadResult = $this->handleCoverImageUpload($collectionId, $_FILES['cover_image']);
                    
                    if (!$uploadResult['success']) {
                        $_SESSION['error'] = 'Cập nhật thành công nhưng có lỗi khi upload ảnh: ' . implode(', ', $uploadResult['errors']);
                        $this->redirect('index.php?url=collections');
                        return;
                    }
                } elseif (!$keepExistingImage) {
                    // Người dùng đã xóa ảnh cũ và không upload ảnh mới
                    // (trường hợp này đã được xử lý bởi deleteImage() AJAX)
                }
                
                $_SESSION['success'] = 'Cập nhật bộ sưu tập thành công!';
            } else {
                $_SESSION['error'] = 'Có lỗi xảy ra khi cập nhật bộ sưu tập!';
            }
        } catch (Exception $e) {
            $_SESSION['error'] = 'Lỗi: ' . $e->getMessage();
            error_log('CollectionsController::update Error: ' . $e->getMessage());
        }
        
        $this->redirect('index.php?url=collections');
    }

    /**
     * Toggle trạng thái collection
     */
    public function toggle() {
        try {
            $collectionId = $_GET['id'] ?? null;
            
            if (!$collectionId) {
                throw new Exception('Không tìm thấy ID bộ sưu tập');
            }

            $collection = $this->collectionModel->findById($collectionId);
            
            if (!$collection) {
                throw new Exception('Bộ sưu tập không tồn tại');
            }

            $newStatus = $collection->is_active ? 0 : 1;
            $data = [
                'collection_name' => $collection->collection_name,
                'slug' => $collection->slug,
                'description' => $collection->description,
                'is_active' => $newStatus
            ];

            if ($this->collectionModel->update($collectionId, $data)) {
                $statusText = $newStatus ? 'kích hoạt' : 'vô hiệu hóa';
                $_SESSION['success'] = "Đã $statusText bộ sưu tập thành công!";
            }
        } catch (Exception $e) {
            $_SESSION['error'] = 'Lỗi: ' . $e->getMessage();
        }
        
        $this->redirect('index.php?url=collections');
    }

    /**
     * Xóa bộ sưu tập
     */
    public function delete() {
        try {
            $collectionId = $_GET['id'] ?? null;
            
            if (!$collectionId) {
                throw new Exception('Không tìm thấy ID bộ sưu tập');
            }

            if ($this->collectionModel->delete($collectionId)) {
                $_SESSION['success'] = 'Xóa bộ sưu tập thành công!';
            } else {
                $_SESSION['error'] = 'Có lỗi xảy ra khi xóa bộ sưu tập!';
            }
        } catch (Exception $e) {
            $_SESSION['error'] = 'Lỗi: ' . $e->getMessage();
        }
        
        $this->redirect('index.php?url=collections');
    }

    /**
     * Xóa ảnh của collection (AJAX)
     */
    public function deleteImage() {
        header('Content-Type: application/json');
        
        try {
            $collectionId = $_POST['collection_id'] ?? null;
            
            if (!$collectionId) {
                echo json_encode(['success' => false, 'message' => 'Không tìm thấy ID bộ sưu tập']);
                exit;
            }

            // Xóa ảnh từ bảng images và image_usages
            $result = $this->collectionModel->deleteCollectionCoverImage($collectionId);

            if ($result) {
                echo json_encode(['success' => true, 'message' => 'Xóa ảnh thành công']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Có lỗi khi xóa ảnh']);
            }

        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Lỗi: ' . $e->getMessage()]);
        }
        exit;
    }

    /**
     * Tìm kiếm bộ sưu tập theo tên (AJAX)
     */
    public function search() {
        header('Content-Type: application/json');
        
        try {
            $searchTerm = $_GET['q'] ?? '';
            
            if (strlen($searchTerm) < 2) {
                echo json_encode(['success' => false, 'message' => 'Vui lòng nhập ít nhất 2 ký tự']);
                exit;
            }

            $collections = $this->collectionModel->searchByName($searchTerm);
            
            // Thêm ảnh cover cho kết quả
            foreach ($collections as $collection) {
                $coverImage = $this->collectionModel->getCollectionCoverImage($collection->collection_id);
                $collection->image_path = $coverImage ? $coverImage->file_path : null;
            }

            echo json_encode([
                'success' => true, 
                'count' => count($collections),
                'collections' => $collections
            ]);

        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Lỗi: ' . $e->getMessage()]);
        }
        exit;
    }

    /**
     * Kiểm tra tên bộ sưu tập có tồn tại không (AJAX - cho form validation)
     */
    public function checkName() {
        header('Content-Type: application/json');
        
        try {
            $name = $_GET['name'] ?? '';
            $excludeId = $_GET['exclude_id'] ?? null;
            
            if (empty($name)) {
                echo json_encode(['exists' => false]);
                exit;
            }

            $existing = $this->collectionModel->findByName($name, $excludeId);
            
            echo json_encode([
                'exists' => $existing !== null && $existing !== false,
                'collection' => $existing ? [
                    'id' => $existing->collection_id,
                    'name' => $existing->collection_name,
                    'slug' => $existing->slug
                ] : null
            ]);

        } catch (Exception $e) {
            echo json_encode(['exists' => false, 'error' => $e->getMessage()]);
        }
        exit;
    }

    // =================== PRIVATE HELPER METHODS (OOP Best Practice) ===================
    
    /**
     * Validate dữ liệu bộ sưu tập
     * @return array Mảng các lỗi validation
     */
    private function validateCollectionData($data) {
        $errors = [];

        // Validate tên collection
        if (empty($data['name']) || strlen(trim($data['name'])) < 3) {
            $errors[] = 'Tên bộ sưu tập phải có ít nhất 3 ký tự';
        }

        return $errors;
    }

    /**
     * Xử lý upload cover image cho collection
     * ⚠️ RÀNG BUỘC: CHỈ 1 ẢNH CHO MỖI COLLECTION
     * @return array ['success' => bool, 'errors' => array]
     */
    private function handleCoverImageUpload($collectionId, $file) {
        $result = ['success' => true, 'errors' => []];
        
        try {
            // KIỂM TRA: Collection này đã có ảnh chưa
            $existingImage = $this->collectionModel->getCollectionCoverImage($collectionId);
            if ($existingImage) {
                error_log("Collection $collectionId already has a cover image. It will be replaced.");
            }
            
            // Tạo thư mục upload nếu chưa tồn tại
            $collectionUploadPath = $this->uploadPath . $collectionId . '/';
            if (!is_dir($collectionUploadPath)) {
                mkdir($collectionUploadPath, 0777, true);
            }

            $fileName = $file['name'];
            $fileTmpName = $file['tmp_name'];
            $fileSize = $file['size'];
            $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

            // Validate file extension
            if (!in_array($fileExt, $this->allowedExtensions)) {
                $result['errors'][] = "File {$fileName}: Định dạng không hợp lệ";
                $result['success'] = false;
                return $result;
            }

            // Validate file size
            if ($fileSize > $this->maxFileSize) {
                $result['errors'][] = "File {$fileName}: Kích thước quá lớn (max 5MB)";
                $result['success'] = false;
                return $result;
            }

            // Generate unique filename
            $newFileName = 'collection_cover_' . $collectionId . '.' . $fileExt;
            $destination = $collectionUploadPath . $newFileName;

            // Move uploaded file
            if (move_uploaded_file($fileTmpName, $destination)) {
                // ⚠️ addCollectionCover sẽ TỰ ĐỘNG XÓA ảnh cũ trước khi thêm mới
                $addResult = $this->collectionModel->addCollectionCover($collectionId, $destination);
                
                if (!$addResult) {
                    $result['errors'][] = "Không thể lưu thông tin ảnh vào database";
                    $result['success'] = false;
                    // Xóa file vừa upload nếu không lưu được vào DB
                    @unlink($destination);
                }
            } else {
                $result['errors'][] = "Không thể upload file {$fileName}";
                $result['success'] = false;
            }

        } catch (Exception $e) {
            $result['success'] = false;
            $result['errors'][] = $e->getMessage();
        }

        return $result;
    }

    /**
     * Generate slug from collection name
     */
    private function generateSlug($name) {
        $slug = strtolower(trim($name));
        // Remove Vietnamese accents
        $slug = $this->removeVietnameseAccents($slug);
        // Replace non-alphanumeric with dash
        $slug = preg_replace('/[^a-z0-9-]/', '-', $slug);
        // Remove multiple dashes
        $slug = preg_replace('/-+/', '-', $slug);
        return trim($slug, '-');
    }

    /**
     * Remove Vietnamese accents
     */
    private function removeVietnameseAccents($str) {
        $vietnameseMap = [
            'á' => 'a', 'à' => 'a', 'ả' => 'a', 'ã' => 'a', 'ạ' => 'a',
            'ă' => 'a', 'ắ' => 'a', 'ằ' => 'a', 'ẳ' => 'a', 'ẵ' => 'a', 'ặ' => 'a',
            'â' => 'a', 'ấ' => 'a', 'ầ' => 'a', 'ẩ' => 'a', 'ẫ' => 'a', 'ậ' => 'a',
            'đ' => 'd',
            'é' => 'e', 'è' => 'e', 'ẻ' => 'e', 'ẽ' => 'e', 'ẹ' => 'e',
            'ê' => 'e', 'ế' => 'e', 'ề' => 'e', 'ể' => 'e', 'ễ' => 'e', 'ệ' => 'e',
            'í' => 'i', 'ì' => 'i', 'ỉ' => 'i', 'ĩ' => 'i', 'ị' => 'i',
            'ó' => 'o', 'ò' => 'o', 'ỏ' => 'o', 'õ' => 'o', 'ọ' => 'o',
            'ô' => 'o', 'ố' => 'o', 'ồ' => 'o', 'ổ' => 'o', 'ỗ' => 'o', 'ộ' => 'o',
            'ơ' => 'o', 'ớ' => 'o', 'ờ' => 'o', 'ở' => 'o', 'ỡ' => 'o', 'ợ' => 'o',
            'ú' => 'u', 'ù' => 'u', 'ủ' => 'u', 'ũ' => 'u', 'ụ' => 'u',
            'ư' => 'u', 'ứ' => 'u', 'ừ' => 'u', 'ử' => 'u', 'ữ' => 'u', 'ự' => 'u',
            'ý' => 'y', 'ỳ' => 'y', 'ỷ' => 'y', 'ỹ' => 'y', 'ỵ' => 'y',
        ];
        
        return strtr($str, $vietnameseMap);
    }

    /**
     * Redirect helper method
     */
    private function redirect($url) {
        header("Location: $url");
        exit;
    }
}

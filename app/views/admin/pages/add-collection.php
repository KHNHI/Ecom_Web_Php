<?php
/**
 * Add/Edit Collection View - Pure MVC View
 * Tái sử dụng form cho cả Create và Update (DRY Principle)
 * 
 * Biến được truyền từ Controller:
 * - $title: Tiêu đề trang
 * - $pageTitle: Tiêu đề cho header
 * - $breadcrumb: Breadcrumb text
 * - $oldInput: Dữ liệu cũ nếu có lỗi validation
 * - $isEdit: true nếu đang edit, false/null nếu đang tạo mới
 * - $collection: Dữ liệu collection (chỉ có khi edit)
 */

// Kiểm tra edit mode
$isEdit = isset($isEdit) && $isEdit === true;
$collection = $collection ?? null;

// Lấy old input nếu có lỗi validation
$oldInput = $_SESSION['old_input'] ?? [];
unset($_SESSION['old_input']);

// Xác định action URL
$formAction = $isEdit 
    ? "<?= BASE_URL ?>/admin/collections?action=update&id={$collection->collection_id}"
    : "<?= BASE_URL ?>/admin/collections?action=create";
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'Thêm Bộ Sưu Tập') ?> - Trang Sức Admin</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="app/views/admin/assets/css/variables.css">
    <link rel="stylesheet" href="app/views/admin/assets/css/main.css">
    <link rel="stylesheet" href="app/views/admin/assets/css/add-collection.css">
</head>
<body>
    <script>var APP_BASE_URL = '<?= BASE_URL ?>';</script>
    <div class="admin-wrapper">
        <!-- Sidebar Component Container -->
        <div id="sidebar-container"></div>

        <!-- Main Content -->
        <div class="main-content">
            <!-- Header Component Container -->
            <div id="header-container"></div>

            <!-- Content -->
            <main class="content">
                <!-- Flash Messages -->
                <?php if (isset($_SESSION['error'])): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?= $_SESSION['error'] ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    <?php unset($_SESSION['error']); ?>
                <?php endif; ?>

                <?php if (isset($_SESSION['success'])): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?= $_SESSION['success'] ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    <?php unset($_SESSION['success']); ?>
                <?php endif; ?>

                <!-- Collection Form -->
                <form id="addCollectionForm" method="POST" action="<?= $formAction ?>" enctype="multipart/form-data">
                    <?php if ($isEdit): ?>
                        <input type="hidden" name="collection_id" value="<?= $collection->collection_id ?>">
                    <?php endif; ?>
                    
                    <div class="row">
                        <!-- Left Column - Form Fields -->
                        <div class="col-lg-8 mb-4">
                            <!-- Basic Information -->
                            <div class="card mb-4">
                                <div class="card-body">
                                    <h5 class="card-title mb-4">
                                        <?= $isEdit ? 'Chỉnh Sửa' : 'Thêm Mới' ?> Thông Tin Cơ Bản
                                    </h5>
                                    
                                    <!-- Collection Name -->
                                    <div class="form-group mb-3">
                                        <label for="name" class="form-label">Tên Bộ Sưu Tập <span class="text-danger">*</span></label>
                                        <input type="text" 
                                               class="form-control" 
                                               id="name" 
                                               name="name" 
                                               placeholder="VD: Summer Collection 2024, Tết Collection..." 
                                               value="<?= htmlspecialchars($oldInput['name'] ?? $collection->collection_name ?? '') ?>"
                                               required>
                                    </div>

                                    <!-- Slug (Auto-generated) -->
                                    <div class="form-group mb-3">
                                        <label for="slug" class="form-label">Slug (URL)</label>
                                        <input type="text" 
                                               class="form-control" 
                                               id="slug" 
                                               name="slug_preview" 
                                               placeholder="summer-collection-2024"
                                               value="<?= htmlspecialchars($collection->slug ?? '') ?>" 
                                               readonly>
                                        <small class="text-muted">Tự động tạo từ tên bộ sưu tập</small>
                                    </div>

                                    <!-- Description -->
                                    <div class="form-group mb-3">
                                        <label for="description" class="form-label">Mô Tả Ngắn</label>
                                        <textarea class="form-control" 
                                                  id="description" 
                                                  name="description" 
                                                  rows="3" 
                                                  placeholder="Mô tả ngắn về bộ sưu tập..."><?= htmlspecialchars($oldInput['description'] ?? $collection->description ?? '') ?></textarea>
                                    </div>
                                   
                                </div>
                            </div>

                            <!-- Collection Settings -->
                            <div class="card">
                                <div class="card-body">
                                   
                                    <!-- Is Active -->
                                    <div class="form-check">
                                        <?php
                                        $isActiveChecked = false;
                                        if (isset($oldInput['is_active'])) {
                                            $isActiveChecked = $oldInput['is_active'];
                                        } elseif ($isEdit && isset($collection->is_active)) {
                                            $isActiveChecked = $collection->is_active;
                                        } else {
                                            $isActiveChecked = true; // Default checked for new collection
                                        }
                                        ?>
                                        <input class="form-check-input" 
                                               type="checkbox" 
                                               id="is_active" 
                                               name="is_active" 
                                               value="1"
                                               <?= $isActiveChecked ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="is_active">
                                            Kích hoạt bộ sưu tập (Hiển thị trên website)
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Right Column - Image Upload -->
                        <div class="col-lg-4 mb-4">
                            <!-- Cover Image -->
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title mb-3">
                                        <span class="badge bg-primary me-2">Cover</span>
                                        Ảnh Bìa Collection
                                    </h5>
                                    
                                    <!-- Existing Image (Edit Mode) -->
                                    <?php if ($isEdit && !empty($collection->image_path)): ?>
                                        <div class="existing-image mb-3" id="existingImageContainer">
                                            <h6 class="fw-bold mb-2">Ảnh Hiện Tại</h6>
                                            <div class="position-relative">
                                                <img src="<?= BASE_URL ?>/<?= htmlspecialchars($collection->image_path) ?>" 
                                                     class="img-thumbnail w-100" 
                                                     alt="Collection Cover"
                                                     style="max-height: 200px; object-fit: cover;">
                                                <button type="button" 
                                                        class="btn btn-sm btn-danger position-absolute top-0 end-0 m-2" 
                                                        onclick="removeExistingCollectionImage(<?= $collection->collection_id ?>)">
                                                    <i class="fas fa-times"></i> Xóa ảnh
                                                </button>
                                            </div>
                                            <input type="hidden" name="keep_existing_image" id="keepExistingImage" value="1">
                                            <small class="text-muted d-block mt-2">
                                                💡 Upload ảnh mới để thay thế, hoặc giữ nguyên ảnh hiện tại
                                            </small>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <div class="border-2 border-dashed rounded-custom p-4 text-center upload-area" 
                                         id="coverUploadBox"
                                         style="border-color: var(--border-color); cursor: pointer; transition: all 0.3s ease;" 
                                         onclick="document.getElementById('cover_image').click()"
                                         ondragover="handleDragOver(event)" 
                                         ondragleave="handleDragLeave(event)"
                                         ondrop="handleDrop(event)">
                                        <div id="coverUploadPlaceholder">
                                            <img src="https://cdn-icons-png.flaticon.com/512/1160/1160358.png" alt="Upload" width="48" height="48" class="mb-2 opacity-50">
                                            <p class="text-muted mb-1"><?= $isEdit ? 'Upload ảnh mới (tùy chọn)' : 'Upload ảnh bìa' ?></p>
                                            <p class="text-muted small mb-0">JPG, PNG (Max 5MB)</p>
                                            <p class="text-muted small mb-0">Khuyến nghị: 1200x800px</p>
                                        </div>
                                        <div id="coverPreview" style="display: none;" class="w-100">
                                            <div class="preview-container">
                                                <img id="coverPreviewImg" src="" alt="Cover Preview" style="max-width: 100%; border-radius: 8px;">
                                                <button type="button" class="btn btn-sm btn-danger mt-2" onclick="event.stopPropagation(); removeCoverImage()">
                                                    <img src="https://cdn-icons-png.flaticon.com/512/3096/3096673.png" alt="Remove" width="14" height="14">
                                                    Xóa ảnh
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <input type="file" 
                                           id="cover_image" 
                                           name="cover_image" 
                                           accept="image/*" 
                                           style="display: none;"
                                           onchange="handleCoverImageSelect(this)">

                                    <!-- Tips -->
                                    <div class="mt-3 p-3 bg-light rounded-custom">
                                        <h6 class="fw-bold mb-2">Lưu Ý:</h6>
                                        <ul class="small text-muted mb-0">
                                            <li>Upload ảnh bìa cho bộ sưu tập</li>
                                            <li>Kích thước đề xuất: 1200x800px</li>
                                            <li>Kích thước tối đa: 5MB</li>
                                            <li>Sử dụng ảnh chất lượng cao</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex gap-2 justify-content-end">
                                <button type="button" class="btn btn-outline-secondary btn-custom px-4" onclick="window.location.href='<?= BASE_URL ?>/admin/collections'">
                                    <img src="https://cdn-icons-png.flaticon.com/512/189/189665.png" alt="Cancel" width="16" height="16" class="me-1">
                                    HỦY BỎ
                                </button>
                                <button type="submit" class="btn btn-success-custom btn-custom px-4">
                                    <img src="https://cdn-icons-png.flaticon.com/512/5610/5610944.png" alt="Save" width="16" height="16" class="me-1">
                                    <?= $isEdit ? 'CẬP NHẬT' : 'TẠO BỘ SƯU TẬP' ?>
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </main>
        </div>
    </div>

    <!-- Component Manager -->
    <script src="app/views/admin/components/component-manager.js"></script>
    
    <!-- Page Configuration -->
    <script>
        window.pageConfig = {
            sidebar: {
                brandName: 'Trang Sức',
                activePage: 'collections',
                links: {
                    dashboard: '<?= BASE_URL ?>/admin/dashboard',
                    products: '<?= BASE_URL ?>/admin/products',
                    categories: '<?= BASE_URL ?>/admin/categories',
                    collections: '<?= BASE_URL ?>/admin/collections',
                    orders: '<?= BASE_URL ?>/admin/orders',
                    customers: '<?= BASE_URL ?>/admin/customers',
                    reviews: '<?= BASE_URL ?>/admin/reviews'
                },
                categoriesTitle: 'DANH MỤC'
            },
            header: {
                title: '<?= htmlspecialchars($pageTitle ?? 'Thêm Bộ Sưu Tập') ?>',
                breadcrumb: '<?= htmlspecialchars($breadcrumb ?? 'Home > Bộ Sưu Tập > Thêm Mới') ?>'
            }
        };

        // =================== AUTO SLUG GENERATION ===================
        
        document.getElementById('name').addEventListener('input', function() {
            const name = this.value;
            const slug = generateSlug(name);
            document.getElementById('slug').value = slug;
        });

        function generateSlug(name) {
            return name
                .toLowerCase()
                .normalize('NFD')
                .replace(/[\u0300-\u036f]/g, '')
                .replace(/đ/g, 'd')
                .replace(/[^a-z0-9]+/g, '-')
                .replace(/^-+|-+$/g, '');
        }

        // =================== IMAGE UPLOAD HANDLING ===================
        
        // Remove existing collection image
        function removeExistingCollectionImage(collectionId) {
            if (!confirm('Bạn có chắc chắn muốn xóa ảnh hiện tại không?')) {
                return;
            }

            fetch(`index.php?url=collections&action=deleteImage&id=${collectionId}`, {
                method: 'POST'
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Remove image container from DOM
                    document.getElementById('existingImageContainer').remove();
                    
                    // Show success message
                    alert('Đã xóa ảnh thành công');
                } else {
                    alert('Lỗi: ' + (data.message || 'Không thể xóa ảnh'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Có lỗi xảy ra khi xóa ảnh');
            });
        }
        
        function handleCoverImageSelect(input) {
            const file = input.files[0];
            const previewContainer = document.getElementById('coverPreview');
            const placeholder = document.getElementById('coverUploadPlaceholder');
            const previewImg = document.getElementById('coverPreviewImg');
            
            // Nếu đang ở edit mode và upload ảnh mới, đánh dấu không giữ ảnh cũ
            const keepExistingImage = document.getElementById('keepExistingImage');
            if (keepExistingImage && file) {
                keepExistingImage.value = '0';
            }
            
            if (!file) {
                previewContainer.style.display = 'none';
                placeholder.style.display = 'block';
                return;
            }

            // Validate
            if (file.size > 5242880) {
                alert('Kích thước file không được vượt quá 5MB');
                input.value = '';
                return;
            }

            // Show preview
            const reader = new FileReader();
            reader.onload = function(e) {
                previewImg.src = e.target.result;
                previewContainer.style.display = 'block';
                placeholder.style.display = 'none';
            };
            reader.readAsDataURL(file);
        }

        function removeCoverImage() {
            const fileInput = document.getElementById('cover_image');
            const previewContainer = document.getElementById('coverPreview');
            const placeholder = document.getElementById('coverUploadPlaceholder');
            
            fileInput.value = '';
            previewContainer.style.display = 'none';
            placeholder.style.display = 'block';
        }

        function handleDragOver(event) {
            event.preventDefault();
            event.currentTarget.style.borderColor = 'var(--accent-color, #28a745)';
            event.currentTarget.style.backgroundColor = 'rgba(40, 167, 69, 0.1)';
        }

        function handleDragLeave(event) {
            event.currentTarget.style.borderColor = 'var(--border-color, #ddd)';
            event.currentTarget.style.backgroundColor = 'transparent';
        }

        function handleDrop(event) {
            event.preventDefault();
            event.currentTarget.style.borderColor = 'var(--border-color, #ddd)';
            event.currentTarget.style.backgroundColor = 'transparent';
            
            const files = event.dataTransfer.files;
            const fileInput = document.getElementById('cover_image');
            
            if (files.length > 0) {
                fileInput.files = files;
                handleCoverImageSelect(fileInput);
            }
        }

        // =================== FORM VALIDATION ===================
        
        document.getElementById('addCollectionForm').addEventListener('submit', function(e) {
            const name = document.getElementById('name').value.trim();
            
            if (name.length < 3) {
                e.preventDefault();
                alert('Tên bộ sưu tập phải có ít nhất 3 ký tự');
                document.getElementById('name').focus();
                return false;
            }
            
            // Show loading
            const submitBtn = this.querySelector('button[type="submit"]');
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Đang tạo...';
            }
            
            return true;
        });
    </script>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Main JS -->
    <script src="app/views/admin/assets/js/main.js"></script>
    
    <!-- Add Collection JS -->
    <script src="app/views/admin/assets/js/add-collection.js"></script>
</body>
</html>

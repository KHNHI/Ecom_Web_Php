<?php

/**
 * Product Model - Clean CRUD Operations Only
 * Following Single Responsibility Principle
 *
 * MODIFIED TO USE STORED PROCEDURES
 */
class Product extends BaseModel {
    private static $imageTableColumns = null;
    protected $table = 'products';
    protected $primaryKey = 'product_id';


   
//     // ============= CORE CRUD OPERATIONS =============
    
//     /**
//      * CREATE - Tạo sản phẩm mới
//      */
//     public function create($data) {
//         $sql = "INSERT INTO " . $this->table . " 
//                 (name, description, material, base_price, sku, slug, collection_id, is_active, created_at, updated_at) 
//                 VALUES 
//                 (:name, :description, :material, :base_price, :sku, :slug, :collection_id, :is_active, NOW(), NOW())";
        
//         // Generate slug if not provided
//         $slug = isset($data['slug']) ? $data['slug'] : $this->generateSlug($data['name']);
        
//         $this->db->query($sql);
//         $this->db->bind(':name', $data['name']);
//         $this->db->bind(':description', $data['description'] ?? '');
//         $this->db->bind(':material', $data['material'] ?? 'gold');
//         $this->db->bind(':base_price', $data['base_price']);
//         $this->db->bind(':sku', $data['sku']);
//         $this->db->bind(':slug', $slug);
//         $this->db->bind(':collection_id', $data['collection_id']);
//         $this->db->bind(':is_active', $data['is_active'] ?? 1);
        
//         if ($this->db->execute()) {
//             return $this->db->lastInsertId();
//         } else {
//             return false;
//         }
//     }

//     /**
//      * CREATE - Tạo product variants
//      */
//     public function createVariants($productId, $variants) {
//         if (empty($variants) || !is_array($variants)) {
//             return false;
//         }

//         $sql = "INSERT INTO product_variants (product_id, size, color, price, stock, created_at, updated_at) 
//                 VALUES (:product_id, :size, :color, :price, :stock, NOW(), NOW())";

//         $success = true;
//         foreach ($variants as $variant) {
//             // Validate variant data
//             if (empty($variant['size']) || empty($variant['color']) || 
//                 !isset($variant['price']) || !isset($variant['stock'])) {
//                 continue;
//             }

//             $this->db->query($sql);
//             $this->db->bind(':product_id', $productId);
//             $this->db->bind(':size', trim($variant['size']));
//             $this->db->bind(':color', trim($variant['color']));
//             $this->db->bind(':price', floatval($variant['price']));
//             $this->db->bind(':stock', intval($variant['stock']));
            
//             if (!$this->db->execute()) {
//                 $success = false;
//             }
//         }

//         return $success;
//     }

//     /**
//      * CREATE - Tạo sản phẩm với variants
//      */
//     public function createWithVariants($productData, $variants = []) {
//         // Begin transaction
//         $this->db->beginTransaction();
        
//         try {
//             // Create product first
//             $productId = $this->create($productData);
//             if (!$productId) {
//                 throw new Exception('Failed to create product');
//             }

//             // Create variants if provided
//             if (!empty($variants)) {
//                 $variantResult = $this->createVariants($productId, $variants);
//                 if (!$variantResult) {
//                     throw new Exception('Failed to create product variants');
//                 }
//             }

//             // Commit transaction
//             $this->db->commit();
//             return $productId;
            
//         } catch (Exception $e) {
//             // Rollback transaction
//             $this->db->rollback();
//             error_log('Product createWithVariants failed: ' . $e->getMessage());
//             return false;
//         }
//     }

   
//     /**
//      * READ - Lấy sản phẩm theo ID với stock từ variants
//      */
//     public function findById($id) {
//         $sql = "SELECT p.*, c.collection_name,
//                        (SELECT SUM(pv.stock) FROM product_variants pv WHERE pv.product_id = p.product_id) as total_stock
//                 FROM " . $this->table . " p 
//                 LEFT JOIN collection c ON p.collection_id = c.collection_id 
//                 WHERE p.product_id = :id";
//         $this->db->query($sql);
//         $this->db->bind(':id', $id);
//         return $this->db->single();
//     }
    
//     /**
//      * READ - Lấy sản phẩm theo slug
//      */
//     public function findBySlug($slug) {
//         $sql = "SELECT p.*, c.collection_name,
//                        (SELECT SUM(pv.stock) FROM product_variants pv WHERE pv.product_id = p.product_id) as total_stock
//                 FROM " . $this->table . " p 
//                 LEFT JOIN collection c ON p.collection_id = c.collection_id 
//                 WHERE p.slug = :slug";
//         $this->db->query($sql);
//         $this->db->bind(':slug', $slug);
//         return $this->db->single();
//     }
    
    /**
     * Get primary image URL for a product
     */
    public function getPrimaryImageUrl($productId) {
        $sql = "SELECT i.file_path 
                FROM images i 
                JOIN image_usages iu ON i.image_id = iu.image_id 
                WHERE iu.ref_type = 'product' 
                AND iu.ref_id = :product_id 
                AND iu.is_primary = 1 
                LIMIT 1";
        
        $this->db->query($sql);
        $this->db->bind(':product_id', $productId);
        $result = $this->db->single();
        
        // Return image URL or default fallback
        return $result ? $result->file_path : 'https://images.unsplash.com/photo-1599643478518-a784e5dc4c8f?w=100&h=100&fit=crop';
    }

//     /**
//      * UPDATE - Cập nhật sản phẩm (sử dụng SP)
//      */
//     public function update($id, $data) {
//         $allowedFields = ['name', 'description', 'base_price', 'sku', 'slug', 'collection_id', 'is_active', 'main_image'];
        
//         // If updating main_image only, use simple UPDATE
//         if (count($data) === 1 && isset($data['main_image'])) {
//             $sql = "UPDATE products SET main_image = :main_image WHERE product_id = :id";
//             $this->db->query($sql);
//             $this->db->bind(':id', $id);
//             $this->db->bind(':main_image', $data['main_image']);
//             return $this->db->execute();
//         }
        
//         // For full update, use stored procedure
//         $sql = "CALL sp_product_update(:id, :name, :description, :base_price, :sku, :slug, :collection_id, :is_active)";
        
//         $this->db->query($sql);
//         $this->db->bind(':id', $id);
        
//         // Bind chỉ các fields có trong $data và allowed
//         foreach ($data as $key => $value) {
//             if (in_array($key, $allowedFields) && $key !== 'main_image') {
//                 $this->db->bind(':' . $key, $value);
//             }
//         }
        
//         return $this->db->execute();
//     }
    
//     /**
//      * DELETE - Xóa sản phẩm (soft delete) (sử dụng SP)
//      */
//     public function delete($id) {
//         $sql = "CALL sp_product_delete(:id)";
//         $this->db->query($sql);
//         $this->db->bind(':id', $id);
//         return $this->db->execute();
//     }
    
//     // ============= BASIC UTILITY METHODS =============
    
//     /**
//      * Lấy tất cả sản phẩm với pagination (sử dụng SP)
//      */
//     public function getAll($limit = 10, $offset = 0) {
//         $sql = "CALL sp_product_getAll(:limit, :offset)";
        
//         $this->db->query($sql);
//         $this->db->bind(':limit', $limit);
//         $this->db->bind(':offset', $offset);
//         return $this->db->resultSet();
//     }
    
//     /**
//      * Đếm tổng số sản phẩm (sử dụng SP)
//      */
//     public function count($conditions = []) {
//         $sql = "CALL sp_product_count(:category_id)";
//         $this->db->query($sql);
        
//         $categoryId = $conditions['category_id'] ?? null;
//         $this->db->bind(':category_id', $categoryId);
        
//         $result = $this->db->single();
//         return $result ? (int)$result->total : 0;
//     }
    
//     /**
//      * UPDATE - Cập nhật stock từ variant (sử dụng SP)
//      */
//     public function updateStock($productId, $size, $color, $quantity) {
//         try {
//             $sql = "CALL sp_product_updateStock(:product_id, :size, :color, :quantity)";
//             $this->db->query($sql);
//             $this->db->bind(':product_id', $productId);
//             $this->db->bind(':size', $size);
//             $this->db->bind(':color', $color);
//             $this->db->bind(':quantity', $quantity);
            
//             $result = $this->db->execute();
//             return $result && $this->db->rowCount() > 0; // Kiểm tra có row nào được update không
//         } catch (Exception $e) {
//             error_log("Update Variant Stock Error: " . $e->getMessage());
//             return false;
//         }
//     }
    
//     /**
//      * READ - Kiểm tra stock từ variant thực tế
//      */
//     public function checkStock($productId, $size, $color, $quantity) {
//         // Check if specific variant has enough stock
//         $sql = "SELECT pv.stock
//                 FROM product_variants pv 
//                 JOIN products p ON pv.product_id = p.product_id 
//                 WHERE pv.product_id = :product_id 
//                 AND pv.size = :size 
//                 AND pv.color = :color 
//                 AND p.is_active = 1";
        
//         $this->db->query($sql);
//         $this->db->bind(':product_id', $productId);
//         $this->db->bind(':size', $size);
//         $this->db->bind(':color', $color);
//         $result = $this->db->single();
//         // Return true if variant exists and has enough stock
//         return $result && $result->stock >= $quantity;
//     }
    
//     /**
//      * READ - Lấy tất cả variants của sản phẩm (sử dụng SP)
//      */
//     public function getVariants($productId) {
//         $sql = "CALL sp_product_getVariants(:product_id)";
//         $this->db->query($sql);
//         $this->db->bind(':product_id', $productId);
//         return $this->db->resultSet();
//     }

//     /**
//      * READ - Lấy total stock của sản phẩm (sử dụng SP)
//      */
//     public function getTotalStock($productId) {
//         $sql = "CALL sp_product_getTotalStock(:product_id)";
//         $this->db->query($sql);
//         $this->db->bind(':product_id', $productId);
//         $result = $this->db->single();
//         return $result ? (int)$result->total_stock : 0;
//     }

//     /**
//      * Lấy tất cả variants có sẵn cho sản phẩm (sử dụng SP)
//      */
//     public function getAvailableVariants($productId) {
//         $sql = "CALL sp_product_getAvailableVariants(:product_id)";
//         $this->db->query($sql);
//         $this->db->bind(':product_id', $productId);
//         return $this->db->resultSet();
//     }

//     /**
//      * UPDATE - Cập nhật stock cho sản phẩm đơn giản (không có variant)
//      */
//     public function updateSimpleStock($productId, $newStock) {
//         try {
//             $sql = "UPDATE products SET stock_quantity = :new_stock WHERE product_id = :product_id";
//             $this->db->query($sql);
//             $this->db->bind(':new_stock', $newStock);
//             $this->db->bind(':product_id', $productId);
//             return $this->db->execute();
//         } catch (Exception $e) {
//             error_log("Update Simple Product Stock Error: " . $e->getMessage());
//             return false;
//         }
//     }

//     // ============= HELPER METHODS =============
    
//     /**
//      * Generate slug từ tên sản phẩm - with uniqueness check
//      */
//    private function generateSlug($name) {
//         $baseSlug = strtolower(trim($name));
//         $baseSlug = preg_replace('/[^a-z0-9-]/', '-', $baseSlug);
//         $baseSlug = preg_replace('/-+/', '-', $baseSlug);
//         $baseSlug = trim($baseSlug, '-');
        
//         // Check for uniqueness
//         $slug = $baseSlug;
//         $counter = 1;
        
//         while ($this->slugExists($slug)) {
//             $slug = $baseSlug . '-' . $counter;
//             $counter++;
//         }
        
//         return $slug;
//     }
    
//     /**
//      * Check if slug exists in database
//      */
//     private function slugExists($slug) {
//         $sql = "SELECT COUNT(*) as count FROM " . $this->table . " WHERE slug = :slug";
//         $this->db->query($sql);
//         $this->db->bind(':slug', $slug);
//         $result = $this->db->single();
//         return $result && $result->count > 0;
//     }
//     public function getTotalCount() {
//         $sql = "SELECT COUNT(*) as total FROM {$this->table}";
//         $this->db->query($sql);
//         $result = $this->db->single();
//         return $result ? (int)$result->total : 0;
//     }
//       public function getBestSellers($limit = 10) {
//         $sql = "SELECT p.*, 
//                        img.file_path as primary_image,
//                        COALESCE(SUM(oi.quantity), 0) as total_sold, 
//                        COALESCE(COUNT(oi.order_id), 0) as order_count
//                 FROM {$this->table} p 
//                 LEFT JOIN image_usages iu ON p.product_id = iu.ref_id AND iu.ref_type = 'product' AND iu.is_primary = 1
//                 LEFT JOIN images img ON iu.image_id = img.image_id
//                 LEFT JOIN order_items oi ON p.product_id = oi.product_id 
//                 LEFT JOIN orders o ON oi.order_id = o.order_id AND o.order_status IN ('paid', 'shipped', 'delivered')
//                 WHERE p.is_active = 1 
//                 GROUP BY p.product_id 
//                 ORDER BY total_sold DESC, order_count DESC 
//                 LIMIT :limit";
//         $this->db->query($sql);
//         $this->db->bind(':limit', $limit, PDO::PARAM_INT);
//         return $this->db->resultSet();
//     }
//     public function getAllWithDetails() {
//         $sql = "SELECT p.*, 
//                        c.collection_name,
//                        cat.name as category_name,
//                        COALESCE(SUM(oi.quantity), 0) as total_sold,
//                        COALESCE(COUNT(oi.order_id), 0) as order_count
//                 FROM {$this->table} p 
//                 LEFT JOIN collection c ON p.collection_id = c.collection_id 
//                 LEFT JOIN product_categories pc ON p.product_id = pc.product_id
//                 LEFT JOIN categories cat ON pc.category_id = cat.category_id
//                 LEFT JOIN order_items oi ON p.product_id = oi.product_id
//                 LEFT JOIN orders o ON oi.order_id = o.order_id AND o.order_status IN ('paid', 'shipped', 'delivered')
//                 GROUP BY p.product_id
//                 ORDER BY p.created_at DESC";
//         $this->db->query($sql);
//         return $this->db->resultSet();
//     }
//     public function getProductImages($productId) {
//         $sql = "SELECT i.*, iu.usage_id, iu.is_primary, iu.created_at as usage_created_at
//                 FROM images i 
//                 JOIN image_usages iu ON i.image_id = iu.image_id 
//                 WHERE iu.ref_type = 'product' 
//                 AND iu.ref_id = :product_id 
//                 ORDER BY iu.is_primary DESC, iu.created_at ASC";
        
//         $this->db->query($sql);
//         $this->db->bind(':product_id', $productId);
        
//         return $this->db->resultSet();
//     }
//      public function getProductPrimaryImage($productId) {
//         $sql = "SELECT i.*, iu.usage_id, iu.is_primary
//                 FROM images i 
//                 JOIN image_usages iu ON i.image_id = iu.image_id 
//                 WHERE iu.ref_type = 'product' 
//                 AND iu.ref_id = :product_id 
//                 AND iu.is_primary = 1
//                 LIMIT 1";
        
//         $this->db->query($sql);
//         $this->db->bind(':product_id', $productId);
        
//         $primaryImage = $this->db->single();
        
//         // If no primary image, get first available image
//         if (!$primaryImage) {
//             $images = $this->getProductImages($productId);
//             return $images ? $images[0] : null;
//         }
        
//         return $primaryImage;
//     }

//     /**
//      * Get available materials
//      */
//     public function getAvailableMaterials() {
//         return ['gold', 'silver', 'diamond', 'pearl'];
//     }
//      public function addProductImage($productId, $imagePath, $isPrimary = false) {
//         error_log("=== ADD PRODUCT IMAGE DEBUG ===");
//         error_log("Product ID: " . $productId);
//         error_log("Image Path: " . $imagePath);
//         error_log("Is Primary: " . ($isPrimary ? 'Yes' : 'No'));
        
//         try {
            // Get file info
//             $fileExtension = strtolower(pathinfo($imagePath, PATHINFO_EXTENSION));
//             $mimeType = $this->getMimeTypeFromExtension($fileExtension);
//             $fileName = basename($imagePath);
            
            // Get file size if file exists
//             $projectRoot = dirname(__DIR__, 2);
//             $fullPath = $projectRoot . DIRECTORY_SEPARATOR . str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $imagePath);
//             $fileSize = (is_file($fullPath) && is_readable($fullPath)) ? filesize($fullPath) : null;
            
//             error_log("File extension: " . $fileExtension);
//             error_log("MIME type: " . $mimeType);
//             error_log("File size: " . ($fileSize !== null ? $fileSize : 'UNKNOWN'));
//             error_log("Full path: " . $fullPath);
            
            // 1. Insert vào images table (tự động map theo columns thực tế)
//             $imageData = [
//                 'file_path' => $imagePath,
//                 'file_name' => $fileName,
//                 'file_type' => $mimeType,
//                 'file_size' => $fileSize,
//                 'alt_text' => $fileName,
//             ];
            
//             $availableColumns = $this->getImageTableColumns();
//             $filteredData = [];
//             foreach ($imageData as $column => $value) {
//                 if (in_array($column, $availableColumns, true)) {
                    // Nếu column là file_size nhưng chưa có giá trị, bỏ qua để tránh NULL không cần thiết
//                     if ($column === 'file_size' && $value === null) {
//                         continue;
//                     }
//                     $filteredData[$column] = $value;
//                 }
//             }
            
//             if (empty($filteredData)) {
//                 throw new Exception('Image table does not contain expected columns.');
//             }
            
//             $columns = array_keys($filteredData);
//             $placeholders = array_map(function ($col) {
//                 return ':' . $col;
//             }, $columns);
//             $sql = "INSERT INTO images (" . implode(', ', $columns) . ") VALUES (" . implode(', ', $placeholders) . ")";
            
//             $this->db->query($sql);
//             foreach ($filteredData as $column => $value) {
//                 $this->db->bind(':' . $column, $value);
//             }
            
//             if (!$this->db->execute()) {
//                 error_log('Failed to insert image into images table: ' . $imagePath);
//                 return false;
//             }
            
//             $imageId = $this->db->lastInsertId();
//             error_log("Image inserted with ID: " . $imageId);
            
            // 2. Nếu set làm primary, clear primary cũ
//             if ($isPrimary) {
//                 $this->clearPrimaryImages($productId);
//             }
            
            // 3. Insert vào image_usages table
//             $sql = "INSERT INTO image_usages (image_id, ref_type, ref_id, is_primary) 
//                     VALUES (:image_id, 'product', :ref_id, :is_primary)";
            
//             $this->db->query($sql);
//             $this->db->bind(':image_id', $imageId);
//             $this->db->bind(':ref_id', $productId);
//             $this->db->bind(':is_primary', $isPrimary ? 1 : 0);
            
//             if (!$this->db->execute()) {
//                 error_log('Failed to insert into image_usages table');
//                 return false;
//             }
            
//             $usageId = $this->db->lastInsertId();
//             error_log("Image usage created with ID: " . $usageId);
            
//             return $usageId;
            
//         } catch (Exception $e) {
//             error_log("addProductImage Exception: " . $e->getMessage());
//             return false;
//         }
//     }
    
//     private function getImageTableColumns() {
//         if (self::$imageTableColumns === null) {
//             $this->db->query('SHOW COLUMNS FROM images');
//             $columns = $this->db->resultSet();
//             self::$imageTableColumns = [];
//             if ($columns) {
//                 foreach ($columns as $column) {
//                     if (isset($column->Field)) {
//                         self::$imageTableColumns[] = $column->Field;
//                     }
//                 }
//             }
//         }
//         return self::$imageTableColumns;
//     }
    
//     private function clearPrimaryImages($productId) {
//         $sql = "UPDATE image_usages 
//                 SET is_primary = 0 
//                 WHERE ref_type = 'product' 
//                 AND ref_id = :product_id";
        
//         $this->db->query($sql);
//         $this->db->bind(':product_id', $productId);
//         $this->db->execute();
//     }
    
//     /**
//      * Get MIME type from file extension
//      */
//     private function getMimeTypeFromExtension($extension) {
//         $types = [
//             'jpg' => 'image/jpeg',
//             'jpeg' => 'image/jpeg',
//             'png' => 'image/png',
//             'gif' => 'image/gif',
//             'webp' => 'image/webp'
//         ];
        
//         return isset($types[$extension]) ? $types[$extension] : 'image/jpeg';
//     }
//      public function setImagePrimary($usageId, $productId) {
        // 1. Clear all primary flags
//         $this->clearPrimaryImages($productId);
        
        // 2. Set specified image as primary
//         $sql = "UPDATE image_usages 
//                 SET is_primary = 1 
//                 WHERE usage_id = :usage_id 
//                 AND ref_type = 'product' 
//                 AND ref_id = :product_id";
        
//         $this->db->query($sql);
//         $this->db->bind(':usage_id', $usageId);
//         $this->db->bind(':product_id', $productId);
        
//         return $this->db->execute();
//     }
 // Các trường dữ liệu trong bảng
    protected $fillable = [
        'name',
        'description',
        'material',
        'base_price',
        'sku',
        'slug',
        'collection_id',
        'is_active',
        'main_image'
    ];

    // ============= CORE CRUD OPERATIONS =============
    
    /**
     * CREATE - Tạo sản phẩm mới
     */
    public function create($data) {
        $sql = "INSERT INTO {$this->table} 
                (name, material, description, base_price, sku, slug, collection_id, is_active, created_at, updated_at) 
                VALUES (:name, :material, :description, :base_price, :sku, :slug, :collection_id, :is_active, NOW(), NOW())";
        
        // Generate unique slug (không truyền excludeId vì đây là tạo mới)
        $slug = isset($data['slug']) && !empty($data['slug']) 
                ? $data['slug'] 
                : $this->generateSlug($data['name'], null);
        
        $this->db->query($sql);
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':material', $data['material'] ?? 'gold');
        $this->db->bind(':description', $data['description']);
        $this->db->bind(':base_price', $data['base_price']);
        $this->db->bind(':sku', $data['sku'] ?? null);
        $this->db->bind(':slug', $slug);
        $this->db->bind(':collection_id', $data['collection_id'] ?? null);
        $this->db->bind(':is_active', $data['is_active'] ?? 1);
        
        return $this->db->execute() ? $this->db->lastInsertId() : false;
    }

    
    /**
     * READ - Lấy sản phẩm theo ID với stock từ variants
     */
    /**
     * Tìm sản phẩm theo ID
     * Lấy cả sản phẩm inactive để admin có thể edit
     */
    public function findById($id) {
        $sql = "SELECT p.*, 
                       COALESCE(SUM(pv.stock), 0) as stock_quantity,
                       COUNT(pv.variant_id) as variant_count
                FROM {$this->table} p 
                LEFT JOIN product_variants pv ON p.product_id = pv.product_id
                WHERE p.product_id = :id
                GROUP BY p.product_id";
        $this->db->query($sql);
        $this->db->bind(':id', $id);
        return $this->db->single();
    }
    
    /**
     * READ - Lấy sản phẩm theo slug
     */
    public function findBySlug($slug) {
        $sql = "SELECT * FROM {$this->table} WHERE slug = :slug AND is_active = 1";
        $this->db->query($sql);
        $this->db->bind(':slug', $slug);
        return $this->db->single();
    }
    
    /**
     * READ - Lấy sản phẩm theo material
     */
    public function findByMaterial($material) {
        $sql = "SELECT * FROM {$this->table} WHERE material = :material AND is_active = 1 ORDER BY created_at DESC";
        $this->db->query($sql);
        $this->db->bind(':material', $material);
        return $this->db->resultSet();
    }
    
    /**
     * UPDATE - Cập nhật sản phẩm (Flexible - chỉ update fields được truyền)
     * OOP Best Practice: Partial Update
     */
    public function update($id, $data) {
        // Build dynamic UPDATE query chỉ với fields có trong $data
        $setClauses = [];
        $allowedFields = ['name', 'material', 'description', 'base_price', 'sku', 'slug', 'collection_id', 'is_active', 'main_image'];
        
        foreach ($data as $key => $value) {
            if (in_array($key, $allowedFields)) {
                $setClauses[] = "$key = :$key";
            }
        }
        
        // Nếu không có field nào để update, return false
        if (empty($setClauses)) {
            return false;
        }
        
        // Luôn update updated_at
        $setClauses[] = "updated_at = NOW()";
        
        $sql = "UPDATE {$this->table} SET " . implode(', ', $setClauses) . " WHERE product_id = :id";
        
        $this->db->query($sql);
        $this->db->bind(':id', $id);
        
        // Bind chỉ các fields có trong $data
        foreach ($data as $key => $value) {
            if (in_array($key, $allowedFields)) {
                $this->db->bind(':' . $key, $value);
            }
        }
        
        return $this->db->execute();
    }
    
    /**
     * DELETE - Xóa sản phẩm (soft delete - chỉ ẩn sản phẩm)
     */
    public function delete($id) {
        $sql = "UPDATE {$this->table} SET is_active = 0, updated_at = NOW() WHERE product_id = :id";
        $this->db->query($sql);
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }
    
    /**
     * Xóa vĩnh viễn sản phẩm (hard delete - xóa hẳn khỏi database)
     * Cảnh báo: Sẽ xóa tất cả dữ liệu liên quan (variants, images, categories)
     */
    public function hardDelete($id) {
        try {
            $this->db->beginTransaction();
            
            // 1. Xóa product categories
            $sql = "DELETE FROM product_categories WHERE product_id = :id";
            $this->db->query($sql);
            $this->db->bind(':id', $id);
            $this->db->execute();
            
            // 2. Xóa product variants
            $sql = "DELETE FROM product_variants WHERE product_id = :id";
            $this->db->query($sql);
            $this->db->bind(':id', $id);
            $this->db->execute();
            
            // 3. Xóa image usages
            $sql = "DELETE FROM image_usages WHERE ref_id = :id AND ref_type = 'product'";
            $this->db->query($sql);
            $this->db->bind(':id', $id);
            $this->db->execute();
            
            // 4. Xóa product chính
            $sql = "DELETE FROM {$this->table} WHERE product_id = :id";
            $this->db->query($sql);
            $this->db->bind(':id', $id);
            $result = $this->db->execute();
            
            $this->db->commit();
            return $result;
            
        } catch (Exception $e) {
            $this->db->rollback();
            error_log("Hard delete product error: " . $e->getMessage());
            return false;
        }
    }
    
    // ============= BASIC UTILITY METHODS =============
    
    /**
     * Lấy tất cả sản phẩm với pagination
     */
    public function getAll($limit = 10, $offset = 0) {
        $sql = "SELECT * FROM {$this->table} WHERE is_active = 1 
                ORDER BY created_at DESC LIMIT :limit OFFSET :offset";
        
        $this->db->query($sql);
        $this->db->bind(':limit', $limit);
        $this->db->bind(':offset', $offset);
        return $this->db->resultSet();
    }
    
    /**
     * Đếm tổng số sản phẩm
     */
    public function count($conditions = []) {
        $sql = "SELECT COUNT(*) as total FROM {$this->table} WHERE is_active = 1";
        
        if (!empty($conditions['category_id'])) {
            $sql .= " AND EXISTS (SELECT 1 FROM product_categories pc WHERE pc.product_id = {$this->table}.product_id AND pc.category_id = :category_id)";
        }
        
        $this->db->query($sql);
        
        if (!empty($conditions['category_id'])) {
            $this->db->bind(':category_id', $conditions['category_id']);
        }
        
        $result = $this->db->single();
        return $result ? (int)$result->total : 0;
    }
    
    /**
     * UPDATE - Cập nhật stock từ variant
     */
    public function updateStock($productId, $size, $color, $quantity) {
        try {
            $sql = "UPDATE product_variants 
                    SET stock = stock - :quantity 
                    WHERE product_id = :product_id AND size = :size AND color = :color AND stock >= :quantity";
            $this->db->query($sql);
            $this->db->bind(':product_id', $productId);
            $this->db->bind(':size', $size);
            $this->db->bind(':color', $color);
            $this->db->bind(':quantity', $quantity);
            
            $result = $this->db->execute();
            return $result && $this->db->rowCount() > 0; // Kiểm tra có row nào được update không
        } catch (Exception $e) {
            error_log("Update Variant Stock Error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * READ - Kiểm tra stock từ variant
     * Nếu size và color là null, sẽ kiểm tra variant mặc định
     */
    public function checkStock($productId, $size, $color, $quantity) {
        try {
            // Lấy tất cả variants để debug
            $debugSql = "SELECT * FROM product_variants WHERE product_id = :product_id";
            $this->db->query($debugSql);
            $this->db->bind(':product_id', $productId);
            $allVariants = $this->db->resultSet();
            error_log("Product #$productId has " . count($allVariants) . " variants");

            // Query chính xác variant cần kiểm tra
            $sql = "SELECT pv.*, p.name as product_name 
                   FROM product_variants pv
                   JOIN products p ON p.product_id = pv.product_id
                   WHERE pv.product_id = :product_id 
                   AND (pv.size = :size OR (:size IS NULL AND pv.size IS NULL))
                   AND (pv.color = :color OR (:color IS NULL AND pv.color IS NULL))";

            $this->db->query($sql);
            $this->db->bind(':product_id', $productId);
            $this->db->bind(':size', $size);
            $this->db->bind(':color', $color);
            
            $variant = $this->db->single();
            
            // Log kết quả để debug
            if (!$variant) {
                error_log("No variant found for product #$productId with size: " . ($size ?? 'NULL') . ", color: " . ($color ?? 'NULL'));
                return false;
            }
            
            error_log("Found variant for '{$variant->product_name}' - Stock: {$variant->stock}, Requested: $quantity");
            return $variant->stock >= $quantity;

        } catch (Exception $e) {
            error_log("Check Stock Error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * READ - Lấy tất cả variants của sản phẩm
     */
    public function getVariants($productId) {
        $sql = "SELECT * FROM product_variants WHERE product_id = :product_id AND stock > 0 ORDER BY size, color";
        $this->db->query($sql);
        $this->db->bind(':product_id', $productId);
        return $this->db->resultSet();
    }

    /**
     * Lấy một variant cụ thể theo product_id, size và color
     * Trả về object variant hoặc null
     */
    public function getVariant($productId, $size = null, $color = null) {
        $sql = "SELECT pv.* FROM product_variants pv
                WHERE pv.product_id = :product_id
                AND (pv.size = :size OR (:size IS NULL AND pv.size IS NULL))
                AND (pv.color = :color OR (:color IS NULL AND pv.color IS NULL))
                LIMIT 1";
        $this->db->query($sql);
        $this->db->bind(':product_id', $productId);
        $this->db->bind(':size', $size);
        $this->db->bind(':color', $color);
        return $this->db->single();
    }
    
    /**
     * READ - Lấy total stock của sản phẩm
     */
    public function getTotalStock($productId) {
        $sql = "SELECT SUM(stock) as total_stock FROM product_variants WHERE product_id = :product_id";
        $this->db->query($sql);
        $this->db->bind(':product_id', $productId);
        $result = $this->db->single();
        return $result ? (int)$result->total_stock : 0;
    }

    /**
     * Lấy tất cả variants có sẵn cho sản phẩm
     */
    public function getAvailableVariants($productId) {
        $sql = "SELECT * FROM product_variants 
                WHERE product_id = :product_id AND stock > 0 
                ORDER BY color ASC, size ASC";
        $this->db->query($sql);
        $this->db->bind(':product_id', $productId);
        return $this->db->resultSet();
    }

    // ============= HELPER METHODS =============
    
    /**
     * Lấy tất cả sản phẩm với thông tin chi tiết
     */
    public function getAllWithDetails() {
        $sql = "SELECT p.*, 
                       c.collection_name,
                       cat.name as category_name,
                       COALESCE(SUM(oi.quantity), 0) as total_sold,
                       COALESCE(COUNT(oi.order_id), 0) as order_count
                FROM {$this->table} p 
                LEFT JOIN collection c ON p.collection_id = c.collection_id 
                LEFT JOIN product_categories pc ON p.product_id = pc.product_id
                LEFT JOIN categories cat ON pc.category_id = cat.category_id
                LEFT JOIN order_items oi ON p.product_id = oi.product_id
                LEFT JOIN orders o ON oi.order_id = o.order_id AND o.order_status IN ('paid', 'shipped', 'delivered')
                GROUP BY p.product_id
                ORDER BY p.created_at DESC";
        $this->db->query($sql);
        return $this->db->resultSet();
    }

    /**
     * Lấy sản phẩm bán chạy nhất
     */
    public function getBestSellers($limit = 10) {
        $sql = "SELECT p.*, 
                       img.file_path as primary_image,
                       COALESCE(SUM(oi.quantity), 0) as total_sold, 
                       COALESCE(COUNT(oi.order_id), 0) as order_count
                FROM {$this->table} p 
                LEFT JOIN image_usages iu ON p.product_id = iu.ref_id AND iu.ref_type = 'product' AND iu.is_primary = 1
                LEFT JOIN images img ON iu.image_id = img.image_id
                LEFT JOIN order_items oi ON p.product_id = oi.product_id 
                LEFT JOIN orders o ON oi.order_id = o.order_id AND o.order_status IN ('paid', 'shipped', 'delivered')
                WHERE p.is_active = 1 
                GROUP BY p.product_id 
                ORDER BY total_sold DESC, order_count DESC 
                LIMIT :limit";
        $this->db->query($sql);
        $this->db->bind(':limit', $limit, PDO::PARAM_INT);
        return $this->db->resultSet();
    }

    /**
     * Đếm tổng số sản phẩm
     */
    public function getTotalCount() {
        $sql = "SELECT COUNT(*) as total FROM {$this->table}";
        $this->db->query($sql);
        $result = $this->db->single();
        return $result ? (int)$result->total : 0;
    }

    /**
     * Lấy tổng doanh thu từ sản phẩm
     */
    public function getTotalRevenue() {
        $sql = "SELECT SUM(oi.total_price) as total_revenue 
                FROM order_items oi 
                JOIN orders o ON oi.order_id = o.order_id 
                WHERE o.order_status IN ('paid', 'shipped', 'delivered')";
        $this->db->query($sql);
        $result = $this->db->single();
        return $result ? (float)$result->total_revenue : 0;
    }

    /**
     * Lấy danh sách materials có sẵn
     */
    public function getAvailableMaterials() {
        return ['gold', 'silver', 'diamond', 'pearl'];
    }
    
    /**
     * Lấy tất cả variants của một sản phẩm
     */
    public function getProductVariants($productId) {
        $sql = "SELECT * FROM product_variants 
                WHERE product_id = :product_id 
                ORDER BY variant_id ASC";
        $this->db->query($sql);
        $this->db->bind(':product_id', $productId);
        return $this->db->resultSet();
    }
    
    /**
     * Lấy danh sách categories của một sản phẩm
     */
    public function getProductCategories($productId) {
        $sql = "SELECT pc.*, c.name as category_name 
                FROM product_categories pc
                LEFT JOIN categories c ON pc.category_id = c.category_id
                WHERE pc.product_id = :product_id";
        $this->db->query($sql);
        $this->db->bind(':product_id', $productId);
        return $this->db->resultSet();
    }
    
    /**
     * Lấy thống kê theo material
     */
    public function getMaterialStats() {
        $sql = "SELECT material, COUNT(*) as count, AVG(base_price) as avg_price 
                FROM {$this->table} 
                WHERE is_active = 1 
                GROUP BY material 
                ORDER BY count DESC";
        $this->db->query($sql);
        return $this->db->resultSet();
    }

    /**
     * Giảm số lượng tồn kho cho sản phẩm
     * @param int $productId
     * @param string|null $size
     * @param string|null $color 
     * @param int $quantity
     * @return bool
     */
    public function decrementStock($productId, $size, $color, $quantity) {
        try {
            $sql = "UPDATE product_variants 
                    SET stock = stock - :quantity 
                    WHERE product_id = :product_id 
                    AND size = :size 
                    AND color = :color 
                    AND stock >= :quantity";

            $this->db->query($sql);
            $this->db->bind(':product_id', $productId);
            $this->db->bind(':size', $size);
            $this->db->bind(':color', $color);
            $this->db->bind(':quantity', $quantity);
            
            $result = $this->db->execute();
            return $result && $this->db->rowCount() > 0;
        } catch (Exception $e) {
            error_log("Decrement Stock Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Generate slug từ tên sản phẩm với unique check
     * OOP Best Practice: Ensure uniqueness
     * @param string $name
     * @param int|null $excludeId ID của product hiện tại (khi update)
     * @return string
     */
    private function generateSlug($name, $excludeId = null) {
        // Clean and normalize
        $slug = strtolower(trim($name));
        
        // Remove Vietnamese accents
        $slug = $this->removeVietnameseAccents($slug);
        
        // Replace non-alphanumeric with dash
        $slug = preg_replace('/[^a-z0-9-]/', '-', $slug);
        
        // Remove multiple dashes
        $slug = preg_replace('/-+/', '-', $slug);
        
        $slug = trim($slug, '-');
        
        // If slug is empty after cleaning, generate random
        if (empty($slug)) {
            $slug = 'product-' . uniqid();
        }
        
        // Check for duplicates and add number if needed
        $originalSlug = $slug;
        $counter = 1;
        
        while ($this->slugExists($slug, $excludeId)) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }
        
        return $slug;
    }
    
    /**
     * Kiểm tra slug đã tồn tại chưa
     * @param string $slug
     * @param int|null $excludeId
     * @return bool
     */
    private function slugExists($slug, $excludeId = null) {
        $sql = "SELECT COUNT(*) as count FROM {$this->table} WHERE slug = :slug";
        
        if ($excludeId) {
            $sql .= " AND product_id != :id";
        }
        
        $this->db->query($sql);
        $this->db->bind(':slug', $slug);
        
        if ($excludeId) {
            $this->db->bind(':id', $excludeId);
        }
        
        $result = $this->db->single();
        return $result && $result->count > 0;
    }
    
    /**
     * Remove Vietnamese accents (Helper)
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

    // =================== PRODUCT IMAGE METHODS (Database Schema Compliant) ===================
    
    /**
     * Lấy tất cả ảnh của sản phẩm từ images + image_usages tables
     * Tuân thủ database schema: images JOIN image_usages
     * @param int $productId
     * @return array
     */
    public function getProductImages($productId) {
        $sql = "SELECT i.*, iu.usage_id, iu.is_primary, iu.created_at as usage_created_at
                FROM images i 
                JOIN image_usages iu ON i.image_id = iu.image_id 
                WHERE iu.ref_type = 'product' 
                AND iu.ref_id = :product_id 
                ORDER BY iu.is_primary DESC, iu.created_at ASC";
        
        $this->db->query($sql);
        $this->db->bind(':product_id', $productId);
        
        return $this->db->resultSet();
    }
    
    /**
     * Lấy ảnh chính (primary) của sản phẩm
     * @param int $productId
     * @return object|null
     */
    public function getProductPrimaryImage($productId) {
        $sql = "SELECT i.*, iu.usage_id, iu.is_primary
                FROM images i 
                JOIN image_usages iu ON i.image_id = iu.image_id 
                WHERE iu.ref_type = 'product' 
                AND iu.ref_id = :product_id 
                AND iu.is_primary = 1
                LIMIT 1";
        
        $this->db->query($sql);
        $this->db->bind(':product_id', $productId);
        
        $primaryImage = $this->db->single();
        
        // If no primary image, get first available image
        if (!$primaryImage) {
            $images = $this->getProductImages($productId);
            return $images ? $images[0] : null;
        }
        
        return $primaryImage;
    }

    /**
     * Thêm ảnh cho sản phẩm vào images + image_usages
     * @param int $productId
     * @param string $imagePath
     * @param bool $isPrimary
     * @return int|false Image usage ID hoặc false
     */
    public function addProductImage($productId, $imagePath, $isPrimary = false) {
        $fileExtension = strtolower(pathinfo($imagePath, PATHINFO_EXTENSION));
        $types = ['jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg', 'png' => 'image/png', 'gif' => 'image/gif', 'webp' => 'image/webp'];
        $mimeType = isset($types[$fileExtension]) ? $types[$fileExtension] : 'image/jpeg';

        // 1. Insert vào images table
        $sql = "INSERT INTO images (file_path, file_name, file_type, alt_text, created_at) 
                VALUES (:path, :name, :type, :alt, NOW())";
        
        $this->db->query($sql);
        $this->db->bind(':path', $imagePath);
        $this->db->bind(':name', basename($imagePath));
        $this->db->bind(':type', $mimeType);
        $this->db->bind(':alt', basename($imagePath));
        
        if (!$this->db->execute()) {
            return false;
        }
        
        $imageId = $this->db->lastInsertId();
        
        // 2. Nếu set làm primary, clear primary cũ
        if ($isPrimary) {
            $this->clearPrimaryImages($productId);
        }
        
        // 3. Insert vào image_usages table
        $sql = "INSERT INTO image_usages (image_id, ref_type, ref_id, is_primary, created_at) 
                VALUES (:image_id, 'product', :ref_id, :is_primary, NOW())";
        
        $this->db->query($sql);
        $this->db->bind(':image_id', $imageId);
        $this->db->bind(':ref_id', $productId);
        $this->db->bind(':is_primary', $isPrimary ? 1 : 0);
        
        return $this->db->execute() ? $this->db->lastInsertId() : false;
    }

    /**
     * Set ảnh làm primary image
     * @param int $usageId
     * @param int $productId
     * @return bool
     */
    public function setImagePrimary($usageId, $productId) {
        // 1. Clear all primary flags
        $this->clearPrimaryImages($productId);
        
        // 2. Set specified image as primary
        $sql = "UPDATE image_usages 
                SET is_primary = 1 
                WHERE usage_id = :usage_id 
                AND ref_type = 'product' 
                AND ref_id = :product_id";
        
        $this->db->query($sql);
        $this->db->bind(':usage_id', $usageId);
        $this->db->bind(':product_id', $productId);
        
        return $this->db->execute();
    }

    /**
     * Clear all primary flags cho product (helper method)
     * @param int $productId
     */
    private function clearPrimaryImages($productId) {
        $sql = "UPDATE image_usages 
                SET is_primary = 0 
                WHERE ref_type = 'product' 
                AND ref_id = :product_id";
        
        $this->db->query($sql);
        $this->db->bind(':product_id', $productId);
        $this->db->execute();
    }

    /**
     * Xóa ảnh của sản phẩm
     * @param int $usageId
     * @return bool
     */
    public function deleteProductImage($usageId) {
        // Get image_id first
        $sql = "SELECT image_id FROM image_usages WHERE usage_id = :usage_id";
        $this->db->query($sql);
        $this->db->bind(':usage_id', $usageId);
        $imageRecord = $this->db->single();
        
        if (!$imageRecord) return false;
        
        // Delete from image_usages
        $sql = "DELETE FROM image_usages WHERE usage_id = :usage_id";
        $this->db->query($sql);
        $this->db->bind(':usage_id', $usageId);
        $result = $this->db->execute();
        
        // Check if image is used elsewhere
        $sql = "SELECT COUNT(*) as count FROM image_usages WHERE image_id = :image_id";
        $this->db->query($sql);
        $this->db->bind(':image_id', $imageRecord->image_id);
        $usageCount = $this->db->single();
        
        // If not used elsewhere, delete from images table
        if ($usageCount && $usageCount->count == 0) {
            $sql = "DELETE FROM images WHERE image_id = :image_id";
            $this->db->query($sql);
            $this->db->bind(':image_id', $imageRecord->image_id);
            $this->db->execute();
        }
        
        return $result;
    }

    /**
     * Xóa tất cả ảnh của sản phẩm
     * @param int $productId
     * @return bool
     */
    public function deleteAllProductImages($productId) {
        $sql = "SELECT usage_id FROM image_usages 
                WHERE ref_type = 'product' AND ref_id = :product_id";
        $this->db->query($sql);
        $this->db->bind(':product_id', $productId);
        $usages = $this->db->resultSet();
        
        foreach ($usages as $usage) {
            $this->deleteProductImage($usage->usage_id);
        }
        
        return true;
    }
/**
     * CREATE - Tạo product variants
     */
    public function createVariants($productId, $variants) {
        if (empty($variants) || !is_array($variants)) {
            return false;
        }

        $sql = "INSERT INTO product_variants (product_id, size, color, price, stock, created_at, updated_at) 
                VALUES (:product_id, :size, :color, :price, :stock, NOW(), NOW())";

        $success = true;
        foreach ($variants as $variant) {
            // Validate variant data
            if (empty($variant['size']) || empty($variant['color']) || 
                !isset($variant['price']) || !isset($variant['stock'])) {
                continue;
            }

            $this->db->query($sql);
            $this->db->bind(':product_id', $productId);
            $this->db->bind(':size', trim($variant['size']));
            $this->db->bind(':color', trim($variant['color']));
            $this->db->bind(':price', floatval($variant['price']));
            $this->db->bind(':stock', intval($variant['stock']));
            
            if (!$this->db->execute()) {
                $success = false;
            }
        }

        return $success;
    }

    /**
     * CREATE - Tạo sản phẩm với variants
     */
    public function createWithVariants($productData, $variants = []) {
        // Begin transaction
        $this->db->beginTransaction();
        
        try {
            // Create product first
            $productId = $this->create($productData);
            if (!$productId) {
                throw new Exception('Failed to create product');
            }

            // Create variants if provided
            if (!empty($variants)) {
                if (!$this->createVariants($productId, $variants)) {
                    throw new Exception('Failed to create product variants');
                }
            }

            // Commit transaction
            $this->db->commit();
            return $productId;
            
        } catch (Exception $e) {
            // Rollback transaction
            $this->db->rollback();
            return false;
        }
    }
}

<?php
class Collection extends BaseModel {
    protected $table = 'collection';
    protected $primaryKey = 'collection_id';

    public function createCollection($data) {
        $this->db->query("INSERT INTO " . $this->table . " (collection_name, description, slug, is_active, created_at, updated_at) VALUES (:collection_name, :description, :slug, 1, NOW(), NOW())");
        $this->db->bind(':collection_name', $data['collection_name']);
        $this->db->bind(':description', $data['description'] ?? null);
        $this->db->bind(':slug', $this->generateSlug($data['collection_name']));
        return $this->db->execute();
    }

    public function updateCollection($id, $data) {
        $this->db->query("UPDATE " . $this->table . " SET collection_name = :collection_name, description = :description, slug = :slug, is_active = :is_active, updated_at = NOW() WHERE collection_id = :id");
        $this->db->bind(':collection_name', $data['collection_name']);
        $this->db->bind(':description', $data['description'] ?? null);
        $this->db->bind(':slug', $data['slug'] ?? $this->generateSlug($data['collection_name']));
        $this->db->bind(':is_active', $data['is_active'] ?? 1);
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }

    public function findById($id) {
        $this->db->query("SELECT * FROM " . $this->table . " WHERE collection_id = :id");
        $this->db->bind(':id', $id);
        return $this->db->single();
    }

    public function findBySlug($slug) {
        $this->db->query("SELECT * FROM " . $this->table . " WHERE slug = :slug AND is_active = 1");
        $this->db->bind(':slug', $slug);
        return $this->db->single();
    }

    public function deleteById($id) {
        // Soft delete by deactivating
        $this->db->query("UPDATE " . $this->table . " SET is_active = 0, updated_at = NOW() WHERE collection_id = :id");
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }

    public function getAllCollections($onlyActive = true) {
        $sql = "SELECT * FROM " . $this->table;
        if ($onlyActive) {
            $sql .= " WHERE is_active = 1";
        }
        $sql .= " ORDER BY collection_name ASC";
        $this->db->query($sql);
        return $this->db->resultSet();
    }


    /**
     * Get products in a collection with images
     */
    public function getCollectionProductsWithImages($collectionId, $limit = null, $offset = 0) {
        $limitClause = $limit ? "LIMIT $limit OFFSET $offset" : "";
        
        try {
            $query = "SELECT p.*, 
                             COALESCE(GROUP_CONCAT(DISTINCT i.file_path ORDER BY COALESCE(iu.is_primary, 0) DESC, iu.created_at ASC SEPARATOR ','), '') as images,
                             COALESCE(GROUP_CONCAT(DISTINCT c.name SEPARATOR ', '), 'Uncategorized') as category_name
                      FROM products p
                      LEFT JOIN image_usages iu ON p.product_id = iu.ref_id AND iu.ref_type = 'product'
                      LEFT JOIN images i ON iu.image_id = i.image_id
                      LEFT JOIN product_categories pc ON p.product_id = pc.product_id
                      LEFT JOIN categories c ON pc.category_id = c.category_id
                      WHERE p.collection_id = :collection_id 
                      AND p.is_active = 1
                      GROUP BY p.product_id
                      ORDER BY p.created_at DESC
                      $limitClause";
            
            $this->db->query($query);
            $this->db->bind(':collection_id', $collectionId);
            
            return $this->db->resultSet();
        } catch (Exception $e) {
            error_log("Collection products query error: " . $e->getMessage());
            
            // Fallback to simple query without images
            $query = "SELECT p.*, 
                             '' as images,
                             COALESCE(GROUP_CONCAT(DISTINCT c.name SEPARATOR ', '), 'Uncategorized') as category_name
                      FROM products p
                      LEFT JOIN product_categories pc ON p.product_id = pc.product_id
                      LEFT JOIN categories c ON pc.category_id = c.category_id
                      WHERE p.collection_id = :collection_id 
                      AND p.is_active = 1
                      GROUP BY p.product_id
                      ORDER BY p.created_at DESC
                      $limitClause";
            
            $this->db->query($query);
            $this->db->bind(':collection_id', $collectionId);
            
            return $this->db->resultSet();
        }
    }

    /**
     * Count products in a collection
     */
    
    private function generateSlug($name) {
        $slug = strtolower(trim($name));
        $slug = preg_replace('/[^a-z0-9-]/', '-', $slug);
        $slug = preg_replace('/-+/', '-', $slug);
        return trim($slug, '-');
    }

    // =================== COLLECTION IMAGE METHODS ===================
    
    /**
     * Get all images for a collection
     */
    public function getCollectionImages($collectionId) {
        $sql = "SELECT i.*, iu.usage_id, iu.ref_type, iu.ref_id, iu.is_primary
                FROM images i 
                JOIN image_usages iu ON i.image_id = iu.image_id 
                WHERE iu.ref_type = 'collection' 
                AND iu.ref_id = :collection_id 
                ORDER BY iu.is_primary DESC, iu.created_at ASC";
        
        $this->db->query($sql);
        $this->db->bind(':collection_id', $collectionId);
        
        return $this->db->resultSet();
    }
    
    /**
     * Get collection cover (primary image)
     */
    public function getCollectionCover($collectionId) {
        $images = $this->getCollectionImages($collectionId);
        return $images ? $images[0] : null;
    }
    

   
    
    /**
     * Delete a specific collection image by usage_id
     */
    public function deleteCollectionImage($usageId) {
        // Get image_id first
        $this->db->query("SELECT image_id FROM image_usages WHERE usage_id = :usage_id");
        $this->db->bind(':usage_id', $usageId);
        $imageRecord = $this->db->single();
        
        if (!$imageRecord) return false;
        
        // Delete from image_usages
        $this->db->query("DELETE FROM image_usages WHERE usage_id = :usage_id");
        $this->db->bind(':usage_id', $usageId);
        $result1 = $this->db->execute();
        
        // Check if image is used elsewhere
        $this->db->query("SELECT COUNT(*) as count FROM image_usages WHERE image_id = :image_id");
        $this->db->bind(':image_id', $imageRecord->image_id);
        $usage_count = $this->db->single();
        
        // If not used elsewhere, delete from images table
        if ($usage_count->count == 0) {
            $this->db->query("DELETE FROM images WHERE image_id = :image_id");
            $this->db->bind(':image_id', $imageRecord->image_id);
            $this->db->execute();
        }
        
        return $result1;
    }
    
    /**
     * Delete all images for a collection
     */
    public function deleteAllCollectionImages($collectionId) {
        $this->db->query("SELECT usage_id FROM image_usages 
                         WHERE ref_type = 'collection' AND ref_id = :collection_id");
        $this->db->bind(':collection_id', $collectionId);
        $usages = $this->db->resultSet();
        
        foreach ($usages as $usage) {
            $this->deleteCollectionImage($usage->usage_id);
        }
        
        return true;
    }
    
    /**
     * Get collections with their covers (for display purposes)
     */
    public function getAllCollectionsWithCovers($onlyActive = true) {
        $collections = $this->getAllCollections($onlyActive);
        
        foreach ($collections as $collection) {
            $collection->cover_image = $this->getCollectionCover($collection->collection_id);
        }
        
        return $collections;
    }
    
    /**
     * Get collection by slug with cover
     */
    public function getCollectionBySlugWithCover($slug) {
        $collection = $this->findBySlug($slug);
        if ($collection) {
            $collection->cover_image = $this->getCollectionCover($collection->collection_id);
        }
        return $collection;
    }

    /**
     * Lấy tất cả bộ sưu tập với số lượng sản phẩm
     */
    public function getAllWithProductCount($onlyActive = true) {
        $sql = "SELECT c.*, 
                       COALESCE(pc.product_count, 0) as product_count
                FROM {$this->table} c
                LEFT JOIN (
                    SELECT collection_id, COUNT(*) as product_count
                    FROM products
                    WHERE is_active = 1
                    GROUP BY collection_id
                ) pc ON c.collection_id = pc.collection_id";
        
        if ($onlyActive) {
            $sql .= " WHERE c.is_active = 1";
        }
        
        $sql .= " ORDER BY c.collection_name ASC";
        $this->db->query($sql);
        return $this->db->resultSet();
    }

    /**
     * Đếm tổng số bộ sưu tập
     */
    public function getTotalCount() {
        $sql = "SELECT COUNT(*) as total FROM {$this->table}";
        $this->db->query($sql);
        $result = $this->db->single();
        return $result ? (int)$result->total : 0;
    }

    /**
     * Lấy collection theo ID kèm ảnh cover
     */
    public function getById($id) {
        $collection = $this->findById($id);
        if ($collection) {
            // Lấy ảnh cover
            $coverImage = $this->getCollectionCoverImage($id);
            $collection->image_path = $coverImage ? $coverImage->file_path : null;
            $collection->cover_image = $coverImage;
        }
        return $collection;
    }

    /**
     * Kiểm tra tên bộ sưu tập đã tồn tại chưa
     */
    public function findByName($name, $excludeId = null) {
        $sql = "SELECT * FROM {$this->table} WHERE collection_name = :name";
        
        if ($excludeId) {
            $sql .= " AND collection_id != :exclude_id";
        }
        
        $this->db->query($sql);
        $this->db->bind(':name', $name);
        
        if ($excludeId) {
            $this->db->bind(':exclude_id', $excludeId);
        }
        
        return $this->db->single();
    }

    /**
     * Tìm kiếm bộ sưu tập theo tên (partial match)
     */
    public function searchByName($searchTerm) {
        $sql = "SELECT c.*, 
                       COALESCE(pc.product_count, 0) as product_count
                FROM {$this->table} c
                LEFT JOIN (
                    SELECT collection_id, COUNT(*) as product_count
                    FROM products
                    WHERE is_active = 1
                    GROUP BY collection_id
                ) pc ON c.collection_id = pc.collection_id
                WHERE c.collection_name LIKE :search_term
                ORDER BY c.collection_name ASC";
        
        $this->db->query($sql);
        $this->db->bind(':search_term', '%' . $searchTerm . '%');
        return $this->db->resultSet();
    }

    /**
     * Tạo bộ sưu tập mới
     */
    public function create($data) {
        $sql = "INSERT INTO {$this->table} (collection_name, slug, description, is_active, created_at) 
                VALUES (:name, :slug, :description, :is_active, NOW())";
        
        $this->db->query($sql);
        $this->db->bind(':name', $data['collection_name']);
        $this->db->bind(':slug', $data['slug']);
        $this->db->bind(':description', $data['description'] ?? '');
        $this->db->bind(':is_active', $data['is_active'] ?? 1);
        
        return $this->db->execute() ? $this->db->lastInsertId() : false;
    }

    /**
     * Cập nhật bộ sưu tập
     */
    public function update($id, $data) {
        $sql = "UPDATE {$this->table} 
                SET collection_name = :name, slug = :slug, description = :description, 
                    is_active = :is_active, updated_at = NOW() 
                WHERE collection_id = :id";
        
        $this->db->query($sql);
        $this->db->bind(':name', $data['collection_name']);
        $this->db->bind(':slug', $data['slug']);
        $this->db->bind(':description', $data['description'] ?? '');
        $this->db->bind(':is_active', $data['is_active'] ?? 1);
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }

    /**
     * Xóa bộ sưu tập
     * ⚠️ XÓA CẢ ẢNH LIÊN QUAN
     */
    public function delete($id) {
        try {
            // BƯỚC 1: Xóa ảnh cover trước
            $this->deleteCollectionCoverImage($id);
            
            // BƯỚC 2: Xóa collection khỏi database
            $sql = "DELETE FROM {$this->table} WHERE collection_id = :id";
            $this->db->query($sql);
            $this->db->bind(':id', $id);
            return $this->db->execute();
            
        } catch (Exception $e) {
            error_log("Error deleting collection: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Thêm cover image cho collection vào bảng images + image_usages
     * ⚠️ CHỈ CHO PHÉP 1 ẢNH - XÓA ẢNH CŨ TRƯỚC KHI THÊM MỚI
     */
    public function addCollectionCover($collectionId, $imagePath) {
        try {
            // BƯỚC 1: XÓA ẢNH CŨ (nếu có) - ĐẢM BẢO CHỈ CÓ 1 ẢNH
            $this->deleteCollectionCoverImage($collectionId);
            
            // BƯỚC 2: Thêm ảnh mới vào bảng images
            $fileName = basename($imagePath);
            $fileType = pathinfo($imagePath, PATHINFO_EXTENSION);
            
            $sql = "INSERT INTO images (file_name, file_path, file_type, alt_text, created_at) 
                    VALUES (:file_name, :file_path, :file_type, :alt_text, NOW())";
            $this->db->query($sql);
            $this->db->bind(':file_name', $fileName);
            $this->db->bind(':file_path', $imagePath);
            $this->db->bind(':file_type', $fileType);
            $this->db->bind(':alt_text', 'Collection cover image');
            
            if (!$this->db->execute()) {
                return false;
            }
            
            $imageId = $this->db->lastInsertId();
            
            // BƯỚC 3: Thêm vào bảng image_usages với is_primary = 1
            $sql = "INSERT INTO image_usages (image_id, ref_type, ref_id, is_primary, created_at) 
                    VALUES (:image_id, 'collection', :ref_id, 1, NOW())";
            $this->db->query($sql);
            $this->db->bind(':image_id', $imageId);
            $this->db->bind(':ref_id', $collectionId);
            
            return $this->db->execute();
            
        } catch (Exception $e) {
            error_log("Error adding collection cover: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Lấy ảnh cover của collection
     */
    public function getCollectionCoverImage($collectionId) {
        $sql = "SELECT i.* FROM images i
                INNER JOIN image_usages iu ON i.image_id = iu.image_id
                WHERE iu.ref_type = 'collection' 
                AND iu.ref_id = :ref_id 
                AND iu.is_primary = 1
                LIMIT 1";
        $this->db->query($sql);
        $this->db->bind(':ref_id', $collectionId);
        return $this->db->single();
    }

    /**
     * Đếm số lượng ảnh của collection
     * ⚠️ CHỈ NÊN TRẢ VỀ 1 (ràng buộc business logic)
     */
    public function countCollectionImages($collectionId) {
        $sql = "SELECT COUNT(*) as image_count FROM image_usages 
                WHERE ref_type = 'collection' AND ref_id = :ref_id";
        $this->db->query($sql);
        $this->db->bind(':ref_id', $collectionId);
        $result = $this->db->single();
        return $result ? (int)$result->image_count : 0;
    }

    /**
     * Xóa ảnh cover của collection
     * ⚠️ XÓA HOÀN TOÀN khỏi database và file vật lý
     */
    public function deleteCollectionCoverImage($collectionId) {
        try {
            // BƯỚC 1: Lấy thông tin ảnh hiện tại
            $image = $this->getCollectionCoverImage($collectionId);
            
            if (!$image) {
                return true; // Không có ảnh để xóa
            }
            
            // BƯỚC 2: Xóa file vật lý
            if (file_exists($image->file_path)) {
                @unlink($image->file_path); // @ để tránh lỗi nếu file không tồn tại
            }
            
            // BƯỚC 3: Xóa khỏi image_usages
            $sql = "DELETE FROM image_usages 
                    WHERE ref_type = 'collection' AND ref_id = :ref_id AND image_id = :image_id";
            $this->db->query($sql);
            $this->db->bind(':ref_id', $collectionId);
            $this->db->bind(':image_id', $image->image_id);
            $this->db->execute();
            
            // BƯỚC 4: Kiểm tra xem image_id này còn được dùng ở đâu không
            $sql = "SELECT COUNT(*) as usage_count FROM image_usages WHERE image_id = :image_id";
            $this->db->query($sql);
            $this->db->bind(':image_id', $image->image_id);
            $result = $this->db->single();
            
            // BƯỚC 5: Nếu không còn được dùng ở đâu, XÓA khỏi bảng images
            if ($result && $result->usage_count == 0) {
                $sql = "DELETE FROM images WHERE image_id = :image_id";
                $this->db->query($sql);
                $this->db->bind(':image_id', $image->image_id);
                $this->db->execute();
            }
            
            return true;
            
        } catch (Exception $e) {
            error_log("Error deleting collection cover: " . $e->getMessage());
            return false;
        }
    }
}
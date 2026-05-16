<?php

require_once __DIR__ . '/../models/Wishlist.php';
require_once __DIR__ . '/../../helpers/session_helper.php';

class WishlistController extends BaseController {

    private $wishlistModel;

    public function __construct() {
        $this->wishlistModel = new Wishlist();
        SessionHelper::start();
    }

    /**
     * Hiển thị trang wishlist
     */
    public function index() {
        if (!SessionHelper::isLoggedIn()) {
            header('Location: ' . BASE_URL . '/signin');
            exit;
        }

        $userId = SessionHelper::getUserId();
        
        // Lấy wishlist của user
        $wishlist = $this->wishlistModel->getUserWishlist($userId);
        $wishlistItems = [];
        
        if ($wishlist) {
            // Lấy các items trong wishlist
            $wishlistItems = $this->wishlistModel->getWishlistItems($wishlist->wishlist_id);
        }

        $this->view('customer/pages/wishlist', [
            'title' => 'Danh sách yêu thích',
            'wishlistItems' => $wishlistItems,
            'wishlist' => $wishlist
        ]);
    }

    /**
     * Thêm sản phẩm vào wishlist (AJAX)
     */
    public function add() {
        try {
            if (!SessionHelper::isLoggedIn()) {
                $this->jsonResponse(false, 'Bạn cần đăng nhập để sử dụng tính năng này', [
                    'redirect' => BASE_URL . '/signin'
                ]);
                return;
            }

            $productId = (int)($_POST['product_id'] ?? 0);
            $userId = SessionHelper::getUserId();

            if ($productId <= 0) {
                $this->jsonResponse(false, 'Dữ liệu không hợp lệ');
                return;
            }

            // Kiểm tra sản phẩm đã có trong wishlist chưa
            if ($this->wishlistModel->isProductInUserWishlist($userId, $productId)) {
                $this->jsonResponse(false, 'Sản phẩm đã có trong danh sách yêu thích');
                return;
            }

            // Thêm vào wishlist
            $result = $this->wishlistModel->addToWishlist($userId, $productId);

            if ($result) {
                $count = $this->wishlistModel->getWishlistItemCount($userId);
                $this->jsonResponse(true, 'Đã thêm vào danh sách yêu thích', [
                    'wishlist_count' => $count
                ]);
            } else {
                $this->jsonResponse(false, 'Có lỗi xảy ra khi thêm vào danh sách yêu thích');
            }

        } catch (Exception $e) {
            error_log("Wishlist Add Error: " . $e->getMessage());
            $this->jsonResponse(false, 'Có lỗi hệ thống xảy ra');
        }
    }

    /**
     * Xóa sản phẩm khỏi wishlist (AJAX)
     */
    public function remove() {
        try {
            if (!SessionHelper::isLoggedIn()) {
                $this->jsonResponse(false, 'Bạn cần đăng nhập để sử dụng tính năng này');
                return;
            }

            $productId = (int)($_POST['product_id'] ?? 0);
            $userId = SessionHelper::getUserId();

            if ($productId <= 0) {
                $this->jsonResponse(false, 'Dữ liệu không hợp lệ');
                return;
            }

            $result = $this->wishlistModel->removeFromWishlist($userId, $productId);

            if ($result) {
                $count = $this->wishlistModel->getWishlistItemCount($userId);
                $this->jsonResponse(true, 'Đã xóa khỏi danh sách yêu thích', [
                    'wishlist_count' => $count
                ]);
            } else {
                $this->jsonResponse(false, 'Có lỗi xảy ra khi xóa khỏi danh sách yêu thích');
            }

        } catch (Exception $e) {
            error_log("Wishlist Remove Error: " . $e->getMessage());
            $this->jsonResponse(false, 'Có lỗi hệ thống xảy ra');
        }
    }

    /**
     * Toggle wishlist (thêm/xóa) (AJAX)  
     */
    public function toggle() {
        try {
            if (!SessionHelper::isLoggedIn()) {
                $this->jsonResponse(false, 'Bạn cần đăng nhập để sử dụng tính năng này', [
                    'redirect' => BASE_URL . '/signin'
                ]);
                return;
            }

            $productId = (int)($_POST['product_id'] ?? 0);
            $userId = SessionHelper::getUserId();

            if ($productId <= 0) {
                $this->jsonResponse(false, 'Dữ liệu không hợp lệ');
                return;
            }

            $isInWishlist = $this->wishlistModel->isProductInUserWishlist($userId, $productId);

            if ($isInWishlist) {
                // Xóa khỏi wishlist
                $result = $this->wishlistModel->removeFromWishlist($userId, $productId);
                $message = 'Đã xóa khỏi danh sách yêu thích';
                $action = 'removed';
            } else {
                // Thêm vào wishlist
                $result = $this->wishlistModel->addToWishlist($userId, $productId);
                $message = 'Đã thêm vào danh sách yêu thích';
                $action = 'added';
            }

            if ($result) {
                $count = $this->wishlistModel->getWishlistItemCount($userId);
                $this->jsonResponse(true, $message, [
                    'action' => $action,
                    'wishlist_count' => $count,
                    'is_in_wishlist' => !$isInWishlist
                ]);
            } else {
                $this->jsonResponse(false, 'Có lỗi xảy ra khi cập nhật danh sách yêu thích');
            }

        } catch (Exception $e) {
            error_log("Wishlist Toggle Error: " . $e->getMessage());
            $this->jsonResponse(false, 'Có lỗi hệ thống xảy ra');
        }
    }

    /**
     * Lấy số lượng items trong wishlist (AJAX)
     */
    public function count() {
        try {
            if (!SessionHelper::isLoggedIn()) {
                $this->jsonResponse(true, 'Count retrieved', ['count' => 0]);
                return;
            }

            $userId = SessionHelper::getUserId();
            $count = $this->wishlistModel->getWishlistItemCount($userId);

            $this->jsonResponse(true, 'Count retrieved', ['count' => $count]);

        } catch (Exception $e) {
            error_log("Wishlist Count Error: " . $e->getMessage());
            $this->jsonResponse(false, 'Có lỗi xảy ra khi lấy thông tin wishlist');
        }
    }

    /**
     * Xóa tất cả items khỏi wishlist (AJAX)
     */
    public function clear() {
        try {
            if (!SessionHelper::isLoggedIn()) {
                $this->jsonResponse(false, 'Bạn cần đăng nhập để sử dụng tính năng này');
                return;
            }

            $userId = SessionHelper::getUserId();
            $result = $this->wishlistModel->clearWishlist($userId);

            if ($result) {
                $this->jsonResponse(true, 'Đã xóa tất cả sản phẩm khỏi danh sách yêu thích', [
                    'wishlist_count' => 0
                ]);
            } else {
                $this->jsonResponse(false, 'Có lỗi xảy ra khi xóa danh sách yêu thích');
            }

        } catch (Exception $e) {
            error_log("Wishlist Clear Error: " . $e->getMessage());
            $this->jsonResponse(false, 'Có lỗi hệ thống xảy ra');
        }
    }
    
    /**
     * Lấy danh sách ID sản phẩm trong wishlist của user (AJAX)
     */
    public function getUserWishlistStatus() {
        try {
            if (!SessionHelper::isLoggedIn()) {
                $this->jsonResponse(true, 'User not logged in', [
                    'products' => [],
                    'count' => 0
                ]);
                return;
            }

            $userId = SessionHelper::getUserId();
            $productIds = $this->wishlistModel->getUserWishlistProductIds($userId);
            $count = count($productIds);

            $this->jsonResponse(true, 'Wishlist status retrieved', [
                'products' => $productIds,
                'count' => $count
            ]);

        } catch (Exception $e) {
            error_log("Wishlist getUserWishlistStatus Error: " . $e->getMessage());
            $this->jsonResponse(false, 'Có lỗi xảy ra khi lấy trạng thái wishlist');
        }
    }

    /**
     * Lấy trạng thái wishlist cho nhiều sản phẩm (AJAX)
     */
    public function status() {
        try {
            if (!SessionHelper::isLoggedIn()) {
                $this->jsonResponse(true, 'User not logged in', ['wishlist_items' => []]);
                return;
            }

            $userId = SessionHelper::getUserId();
            $productIds = $this->wishlistModel->getUserWishlistProductIds($userId);

            $this->jsonResponse(true, 'Wishlist status retrieved', [
                'wishlist_items' => $productIds,
                'user_id' => $userId
            ]);

        } catch (Exception $e) {
            error_log("Wishlist Status Error: " . $e->getMessage());
            $this->jsonResponse(false, 'Có lỗi hệ thống xảy ra');
        }
    }
    
    /**
     * Test method để kiểm tra controller hoạt động
     */
    public function test() {
        $this->jsonResponse(true, 'WishlistController is working!', [
            'timestamp' => date('Y-m-d H:i:s'),
            'user_logged_in' => SessionHelper::isLoggedIn(),
            'user_id' => SessionHelper::isLoggedIn() ? SessionHelper::getUserId() : null
        ]);
    }
}
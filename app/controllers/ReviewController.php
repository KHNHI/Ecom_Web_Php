<?php

require_once __DIR__ . '/../models/Review.php';
require_once __DIR__ . '/../services/ReviewService.php';
require_once __DIR__ . '/../../helpers/session_helper.php';

class ReviewController extends BaseController {

    private $reviewModel;
    private $reviewService;

    public function __construct() {
        $this->reviewModel = new Review();
        $this->reviewService = new ReviewService();
        SessionHelper::start();
    }

    /**
     * Thêm review mới (AJAX)
     */
    public function add() {
        try {
            // Kiểm tra request method
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                $this->jsonResponse(false, 'Phương thức không được phép');
                return;
            }

            // Kiểm tra user đăng nhập
            if (!SessionHelper::isLoggedIn()) {
                $this->jsonResponse(false, 'Bạn cần đăng nhập để đánh giá sản phẩm', ['redirect' => BASE_URL . '/login']);
                return;
            }

            // Lấy dữ liệu từ form
            $productId = (int)($_POST['product_id'] ?? 0);
            $userId = SessionHelper::getUserId();
            $rating = (int)($_POST['rating'] ?? 0);
            $title = trim($_POST['title'] ?? '');
            $comment = trim($_POST['comment'] ?? '');

            // Validate dữ liệu
            $errors = $this->validateReviewData($productId, $rating, $title, $comment);
            if (!empty($errors)) {
                $this->jsonResponse(false, implode(', ', $errors));
                return;
            }

            // Kiểm tra user đã review sản phẩm này chưa
            if ($this->reviewModel->hasUserReviewedProduct($userId, $productId)) {
                $this->jsonResponse(false, 'Bạn đã đánh giá sản phẩm này rồi');
                return;
            }

            // Thêm review vào database
            $reviewId = $this->reviewModel->createReview($productId, $userId, $rating, $title, $comment);

            if ($reviewId) {
                $this->jsonResponse(true, 'Cảm ơn bạn đã đánh giá! Đánh giá của bạn đang chờ được duyệt.', [
                    'review_id' => $reviewId
                ]);
            } else {
                $this->jsonResponse(false, 'Có lỗi xảy ra khi gửi đánh giá. Vui lòng thử lại.');
            }

        } catch (Exception $e) {
            error_log('Review add error: ' . $e->getMessage());
            $this->jsonResponse(false, 'Lỗi hệ thống. Vui lòng thử lại sau.');
        }
    }

    /**
     * Validate dữ liệu review
     */
    private function validateReviewData($productId, $rating, $title, $comment) {
        $errors = [];

        if ($productId <= 0) {
            $errors[] = 'ID sản phẩm không hợp lệ';
        }

        if ($rating < 1 || $rating > 5) {
            $errors[] = 'Vui lòng chọn số sao từ 1-5';
        }

        if (empty($title)) {
            $errors[] = 'Vui lòng nhập tiêu đề đánh giá';
        } elseif (strlen($title) > 255) {
            $errors[] = 'Tiêu đề không được quá 255 ký tự';
        }

        if (empty($comment)) {
            $errors[] = 'Vui lòng nhập nội dung đánh giá';
        } elseif (strlen($comment) > 1000) {
            $errors[] = 'Nội dung đánh giá không được quá 1000 ký tự';
        }

        return $errors;
    }

    /**
     * Lấy reviews của sản phẩm (AJAX)
     */
    public function getProductReviews() {
        try {
            $productId = (int)($_GET['product_id'] ?? 0);
            $page = (int)($_GET['page'] ?? 1);
            $limit = 5;
            $offset = ($page - 1) * $limit;

            if ($productId <= 0) {
                $this->jsonResponse(false, 'ID sản phẩm không hợp lệ');
                return;
            }

            $reviews = $this->reviewService->getProductReviews($productId, $limit, $offset);
            $stats = $this->reviewService->getProductReviewStats($productId);

            $this->jsonResponse(true, 'Lấy đánh giá thành công', [
                'reviews' => $reviews,
                'stats' => $stats,
                'has_more' => count($reviews) === $limit
            ]);

        } catch (Exception $e) {
            error_log('Get reviews error: ' . $e->getMessage());
            $this->jsonResponse(false, 'Lỗi khi tải đánh giá');
        }
    }
}
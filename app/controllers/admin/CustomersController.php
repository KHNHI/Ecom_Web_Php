<?php
/**
 * CustomersController - Admin Customer Management
 * Quản lý khách hàng cho admin
 */
class CustomersController extends BaseController {
    private $userModel;

    public function __construct() {
        $this->userModel = $this->model('User');
    }

    /**
     * Hiển thị danh sách khách hàng
     */
    public function index() {
        try {
            // Lấy danh sách users với thống kê đơn hàng
            $users = $this->userModel->getAllWithOrderCount(false);
            
            // Tính toán thống kê
            $totalUsers = $this->userModel->getTotalCount();
            $activeUsers = $this->userModel->getActiveCount();
            $usersWithOrders = $this->userModel->getUsersWithOrdersCount();
            $newUsersThisMonth = $this->userModel->getNewUsersThisMonthCount();
            
            $stats = [
                'total' => $totalUsers,
                'active' => $activeUsers,
                'with_orders' => $usersWithOrders,
                'new_this_month' => $newUsersThisMonth
            ];

            $data = [
                'title' => 'Khách Hàng',
                'users' => $users,
                'stats' => $stats,
                'message' => $_SESSION['success'] ?? null,
                'error' => $_SESSION['error'] ?? null
            ];

            // Clear flash messages
            unset($_SESSION['success'], $_SESSION['error']);

            // Render admin page with layout
            $this->renderAdminPage('admin/pages/customers', $data);
        } catch (Exception $e) {
            $this->view('admin/error', ['message' => 'Có lỗi xảy ra: ' . $e->getMessage()]);
        }
    }

    /**
     * Tạo khách hàng mới
     */
    /**
     * Hiển thị form chỉnh sửa khách hàng
     * TÁI SỬ DỤNG add-customer.php (DRY Principle)
     */
    public function showEditForm() {
        try {
            $customerId = $_GET['id'] ?? null;
            
            if (!$customerId) {
                throw new Exception('Không tìm thấy ID khách hàng');
            }

            $customer = $this->userModel->findById($customerId);
            
            if (!$customer) {
                throw new Exception('Khách hàng không tồn tại');
            }

            // Lấy địa chỉ mặc định (nếu có) - street, ward, province, postal_code
            $defaultAddress = $this->userModel->getDefaultAddress($customerId);
            if ($defaultAddress) {
                // Mapping fields từ database
                $customer->address_street = $defaultAddress->street ?? '';
                $customer->province = $defaultAddress->province ?? '';
                $customer->ward = $defaultAddress->ward ?? '';
                $customer->postal_code = $defaultAddress->postal_code ?? '';
                $customer->full_name = $defaultAddress->full_name ?? $customer->name;
                $customer->address_phone = $defaultAddress->phone ?? $customer->phone;
            } else {
                // Nếu không có địa chỉ, khởi tạo empty
                $customer->address_street = '';
                $customer->province = '';
                $customer->ward = '';
                $customer->postal_code = '';
            }
            
            $data = [
                'title' => 'Chỉnh Sửa Khách Hàng',
                'customer' => $customer,
                'pageTitle' => 'Chỉnh Sửa Khách Hàng',
                'breadcrumb' => 'Home > Khách Hàng > Chỉnh Sửa',
                'isEdit' => true  // Flag để view biết đây là edit mode
            ];

            // TÁI SỬ DỤNG add-customer.php
            $this->renderAdminPage('admin/pages/add-customer', $data);

        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            header('Location: ' . BASE_URL . '/admin/customers');
            exit;
        }
    }

    /**
     * Cập nhật khách hàng
     */
    public function update() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $_SESSION['error'] = 'Invalid request method!';
            header('Location: ' . BASE_URL . '/admin/customers');
            exit;
        }

        try {
            $customerId = $_GET['id'] ?? null;
            
            if (!$customerId) {
                throw new Exception('Không tìm thấy ID khách hàng');
            }

            // Cập nhật thông tin user
            $userData = [
                'name' => trim($_POST['name']),
                'phone' => trim($_POST['phone'] ?? ''),
                'is_active' => isset($_POST['is_active']) ? 1 : 0
            ];

            // Cập nhật password nếu có
            if (!empty($_POST['password'])) {
                $userData['password_hash'] = password_hash($_POST['password'], PASSWORD_DEFAULT);
            }

            // Cập nhật role_id nếu có
            if (!empty($_POST['role_id'])) {
                $userData['role_id'] = intval($_POST['role_id']);
            }

            $userUpdated = $this->userModel->updateProfile($customerId, $userData);

            // Cập nhật địa chỉ (nếu có)
            if (!empty($_POST['address_street']) || !empty($_POST['province']) || !empty($_POST['ward'])) {
                $addressData = [
                    
                    'street' => trim($_POST['address_street'] ?? ''),
                    'ward' => trim($_POST['ward'] ?? ''),
                    'province' => trim($_POST['province'] ?? ''),
                    'country' => trim($_POST['country'] ?? 'Việt Nam'),
                    'postal_code' => trim($_POST['postal_code'] ?? ''),
                    'is_default' => 1
                ];

                $this->userModel->saveUserAddress($customerId, $addressData);
            }

            if ($userUpdated) {
                $_SESSION['success'] = 'Cập nhật khách hàng thành công!';
            } else {
                $_SESSION['error'] = 'Có lỗi xảy ra khi cập nhật khách hàng!';
            }
        } catch (Exception $e) {
            $_SESSION['error'] = 'Lỗi: ' . $e->getMessage();
        }
        
        header('Location: ' . BASE_URL . '/admin/customers');
        exit;
    }

    /**
     * Xóa khách hàng
     */
    public function delete() {
        try {
            $customerId = $_GET['id'] ?? null;
            
            if (!$customerId) {
                throw new Exception('Không tìm thấy ID khách hàng');
            }

            if ($this->userModel->delete($customerId)) {
                $_SESSION['success'] = 'Xóa khách hàng thành công!';
            } else {
                $_SESSION['error'] = 'Có lỗi xảy ra khi xóa khách hàng!';
            }
        } catch (Exception $e) {
            $_SESSION['error'] = 'Lỗi: ' . $e->getMessage();
        }
        
        header('Location: ' . BASE_URL . '/admin/customers');
        exit;
    }

    /**
     * Toggle trạng thái active của khách hàng
     */
    public function toggle() {
        try {
            $customerId = $_GET['id'] ?? null;
            
            if (!$customerId) {
                throw new Exception('Không tìm thấy ID khách hàng');
            }
            
            if ($this->userModel->toggleActive($customerId)) {
                $_SESSION['success'] = 'Cập nhật trạng thái khách hàng thành công!';
            } else {
                $_SESSION['error'] = 'Có lỗi xảy ra khi cập nhật trạng thái!';
            }
        } catch (Exception $e) {
            $_SESSION['error'] = 'Lỗi: ' . $e->getMessage();
        }
        
        header('Location: ' . BASE_URL . '/admin/customers');
        exit;
    }

    /**
     * Xem chi tiết khách hàng
     */
    public function show($id) {
        try {
            $user = $this->userModel->getById($id);
            
            if (!$user) {
                $_SESSION['error'] = 'Không tìm thấy khách hàng!';
                $this->index();
                return;
            }

            $data = [
                'title' => 'Chi tiết khách hàng',
                'user' => $user
            ];
            
            // Load customer-details content with data
            ob_start();
            include __DIR__ . '/../../views/admin/pages/customer-details.php';
            $content = ob_get_clean();
            $data['content'] = $content;
            extract($data);
            include __DIR__ . '/../../views/admin/layouts/main.php';
        } catch (Exception $e) {
            $_SESSION['error'] = 'Lỗi: ' . $e->getMessage();
            $this->index();
        }
    }
}
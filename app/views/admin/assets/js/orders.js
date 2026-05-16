/**
 * Orders Page JavaScript - MVC Compliant
 * Tách riêng JavaScript từ view (Separation of Concerns)
 */

// =================== ORDER MANAGEMENT FUNCTIONS ===================

/**
 * Xem chi tiết đơn hàng
 */
function viewOrderDetails(orderId) {
    window.location.href = APP_BASE_URL + '/admin/order-details?id=' + orderId;
}

/**
 * Chỉnh sửa đơn hàng - điều hướng đến trang chi tiết để chỉnh sửa
 */
function editOrder(orderId) {
    console.log('editOrder called with orderId:', orderId);
    window.location.href = APP_BASE_URL + '/admin/order-details?id=' + orderId;
}

/**
 * Xóa vĩnh viễn đơn hàng (hard delete)
 */
function deleteOrder(orderId) {
    // Tạo modal Bootstrap để xác nhận
    const modalHtml = `
        <div class="modal fade" id="deleteOrderModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Xác Nhận Xóa Đơn Hàng</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <p><strong>⚠️ CẢNH BÁO:</strong> Bạn đang thực hiện xóa VĨNH VIỄN đơn hàng!</p>
                        <p>Đơn hàng #${orderId} và tất cả dữ liệu liên quan sẽ bị xóa hoàn toàn khỏi hệ thống.</p>
                        <p class="text-danger">Hành động này KHÔNG THỂ hoàn tác!</p>
                        
                        <div class="form-check mt-3">
                            <input class="form-check-input" type="checkbox" id="confirmDeleteCheckbox">
                            <label class="form-check-label" for="confirmDeleteCheckbox">
                                Tôi hiểu và chấp nhận xóa vĩnh viễn đơn hàng này
                            </label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                        <button type="button" class="btn btn-danger" id="confirmDeleteBtn" disabled onclick="confirmDeleteOrder(${orderId})">
                            Xóa Vĩnh Viễn
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    // Xóa modal cũ nếu có
    const oldModal = document.getElementById('deleteOrderModal');
    if (oldModal) {
        oldModal.remove();
    }
    
    // Thêm modal mới
    document.body.insertAdjacentHTML('beforeend', modalHtml);
    
    // Enable/disable button dựa trên checkbox
    const checkbox = document.getElementById('confirmDeleteCheckbox');
    const confirmBtn = document.getElementById('confirmDeleteBtn');
    checkbox.addEventListener('change', function() {
        confirmBtn.disabled = !this.checked;
    });
    
    // Hiển thị modal
    const modal = new bootstrap.Modal(document.getElementById('deleteOrderModal'));
    modal.show();
}

/**
 * Confirm xóa đơn hàng sau khi check checkbox
 */
function confirmDeleteOrder(orderId) {
    window.location.href = APP_BASE_URL + '/admin/orders?action=hardDelete&id=' + orderId;
}

// =================== STATUS UPDATE FUNCTIONS ===================

/**
 * Cập nhật trạng thái thanh toán
 */
function updatePaymentStatus(orderId, newStatus) {
    const selectElement = event.target;
    const originalValue = selectElement.getAttribute('data-original');
    const statusText = newStatus === 'paid' ? 'Đã thanh toán' : 'Chưa thanh toán';
    
    if (confirm(`Bạn có chắc chắn muốn thay đổi trạng thái thanh toán thành "${statusText}"?`)) {
        // Disable dropdown để tránh click lại
        selectElement.disabled = true;
        selectElement.style.opacity = '0.6';
        
        // Tạo form và submit
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = APP_BASE_URL + '/admin/orders?action=updatePayment&id=' + orderId;
        
        const statusInput = document.createElement('input');
        statusInput.type = 'hidden';
        statusInput.name = 'payment_status';
        statusInput.value = newStatus;
        
        form.appendChild(statusInput);
        document.body.appendChild(form);
        form.submit();
    } else {
        // Revert về giá trị cũ
        selectElement.value = originalValue;
    }
}

/**
 * Cập nhật trạng thái đơn hàng
 */
function updateOrderStatus(orderId, newStatus) {
    const selectElement = event.target;
    const originalValue = selectElement.getAttribute('data-original');
    const statusTexts = {
        'pending': 'Chờ xác nhận',
        'confirmed': 'Đã xác nhận',
        'shipping': 'Đang giao hàng',
        'delivered': 'Đã giao hàng',
        'cancelled': 'Đã hủy'
    };
    
    const statusText = statusTexts[newStatus] || newStatus;
    
    if (confirm(`Bạn có chắc chắn muốn thay đổi trạng thái đơn hàng thành "${statusText}"?`)) {
        // Disable dropdown
        selectElement.disabled = true;
        selectElement.style.opacity = '0.6';
        
        // Tạo form và submit
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = APP_BASE_URL + '/admin/orders?action=updateOrder&id=' + orderId;
        
        const statusInput = document.createElement('input');
        statusInput.type = 'hidden';
        statusInput.name = 'order_status';
        statusInput.value = newStatus;
        
        form.appendChild(statusInput);
        document.body.appendChild(form);
        form.submit();
    } else {
        // Revert
        selectElement.value = originalValue;
    }
}

// =================== INITIALIZATION ===================

document.addEventListener('DOMContentLoaded', function() {
    console.log('Orders page JavaScript loaded');
    
    // Store original values for dropdowns
    document.querySelectorAll('.badge-select').forEach(select => {
        select.setAttribute('data-original', select.value);
        select.addEventListener('focus', function() {
            this.setAttribute('data-original', this.value);
        });
    });
    
    // Test functions
    console.log('editOrder function:', typeof editOrder);
    console.log('viewOrderDetails function:', typeof viewOrderDetails);
});

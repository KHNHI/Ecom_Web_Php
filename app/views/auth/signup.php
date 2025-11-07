<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng ký - JEWELRY</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --gold: #d4af37;
            --dark-gold: #b8941f;
            --light-gold: #f0e68c;
            --cream: #f8f6f0;
            --dark-brown: #3a2f28;
        }

        body {
            background: var(--cream);
            min-height: 100vh;
            font-family: "Inter", "Playfair Display", serif;
            color: var(--dark-brown);
        }
        
        .auth-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;

        }
        
        .auth-card {
            background: white;
            border-radius: 0;
            box-shadow: 0 20px 40px rgba(58, 47, 40, 0.08);
            overflow: hidden;
            max-width: 1000px;
            width: 100%;
        }
        
        .auth-left {
            background: var(--dark-brown);
            color: white;
            padding: 60px 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            text-align: center;
            position: relative;
            overflow: hidden;
            height: 100%;
        }

        .auth-left::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, var(--dark-brown) 0%, var(--dark-gold) 100%);
            opacity: 0.9;
            z-index: 1;
        }

        .auth-left > div {
            position: relative;
            z-index: 2;
        }
        
        .auth-right {
            padding: 60px 40px;
            background: white;
        }
        
        .auth-title {
            font-family: "Playfair Display", serif;
            font-size: 2.8rem;
            font-weight: 400;
            margin-bottom: 1rem;
            color: var(--gold);
            letter-spacing: 1px;
        }
        
        .auth-subtitle {
            font-size: 1rem;
            opacity: 0.95;
            margin-bottom: 2.5rem;
            line-height: 1.6;
        }
        
        .form-label {
            font-weight: 500;
            color: var(--dark-brown);
            margin-bottom: 8px;
            font-size: 0.9rem;
            letter-spacing: 0.5px;
        }
        
        .form-control {
            border-radius: 0;
            border: 1px solid #e1e5e9;
            padding: 12px 16px;
            font-size: 0.95rem;
            transition: all 0.3s ease;
            background: var(--cream);
        }
        
        .form-control:focus {
            border-color: var(--gold);
            box-shadow: none;
            background: white;
        }
        
        .password-field {
            position: relative;
        }
        
        .password-toggle {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: var(--dark-brown);
            opacity: 0.6;
            transition: opacity 0.3s ease;
        }

        .password-toggle:hover {
            opacity: 1;
        }
        
        .btn-primary {
            background: var(--gold);
            border: none;
            border-radius: 0;
            padding: 14px 30px;
            font-weight: 500;
            font-size: 0.95rem;
            width: 100%;
            margin-top: 1.5rem;
            transition: all 0.3s ease;
            letter-spacing: 1px;
            text-transform: uppercase;
        }
        
        .btn-primary:hover {
            background: var(--dark-gold);
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(184, 148, 31, 0.15);
        }

        .btn-outline-light {
            border-radius: 0;
            border-width: 1px;
            padding: 12px 30px;
            font-weight: 500;
            font-size: 0.9rem;
            letter-spacing: 1px;
            transition: all 0.3s ease;
        }

        .btn-outline-light:hover {
            background: var(--gold);
            border-color: var(--gold);
            color: var(--dark-brown);
        }

        .btn-outline-gold {
            border: 1px solid var(--gold);
            color: var(--dark-brown);
            padding: 12px 30px;
            font-weight: 500;
            font-size: 0.9rem;
            letter-spacing: 1px;
            transition: all 0.3s ease;
            background: transparent;
        }

        .btn-outline-gold:hover {
            background: var(--gold);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(184, 148, 31, 0.15);
        }
        
        .auth-link {
            color: var(--gold);
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        
        .auth-link:hover {
            color: var(--dark-gold);
        }

        .terms-text {
            font-size: 0.85rem;
            color: var(--dark-brown);
            opacity: 0.8;
        }
        
        .terms-text a {
            color: var(--gold);
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .terms-text a:hover {
            color: var(--dark-gold);
        }
        
        .alert {
            border-radius: 0;
            border: none;
            margin-bottom: 1.5rem;
            font-size: 0.9rem;
        }
        
        .fa-gem {
            color: var(--gold);
            margin-bottom: 1.5rem;
        }

        .form-check-input:checked {
            background-color: var(--gold);
            border-color: var(--gold);
        }

        @media (max-width: 992px) {
            .auth-card {
                margin: 20px;
            }
            
            .auth-left, .auth-right {
                padding: 40px 30px;
            }
        }
        
        @media (max-width: 768px) {
            .auth-left {
                display: none; /* Hide left panel on mobile */
            }
            
            .auth-right {
                padding: 30px 20px;
            }
            
            .auth-title {
                font-size: 2.2rem;
            }

            .auth-subtitle {
                font-size: 0.95rem;
            }

            /* Show signup prompt on mobile */
            .signup-prompt-mobile {
                display: block !important;
                margin-top: 2rem;
                text-align: center;
            }
        }

        /* Hide mobile signup prompt on desktop */
        .signup-prompt-mobile {
            display: none;
        }
    </style>
</head>
<body>
    <div class="auth-container">
        <div class="auth-card">
            <div class="row g-0">
                <div class="col-lg-5">
                    <div class="auth-left">
                        <div>
                            <i class="fas fa-gem mb-3" style="font-size: 3rem;"></i>
                            <h1 class="auth-title">JEWELRY</h1>
                            <p class="auth-subtitle">Tham gia cộng đồng yêu thích trang sức cao cấp</p>
                            <div class="mt-4">
                                <p class="mb-2">Đã có tài khoản?</p>
                                <a href="<?= BASE_URL ?>/signin" class="btn btn-outline-light">
                                    <i class="fas fa-sign-in-alt me-2"></i>Đăng nhập
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-7">
                    <div class="auth-right">
                        <div class="text-center mb-4">
                            <h2 class="fw-bold text-dark">Đăng ký tài khoản</h2>
                            <p class="text-muted">Tạo tài khoản để trải nghiệm đầy đủ</p>
                        </div>
                        
                        <!-- Alert Messages -->
                        <div id="alert-container"></div>
                        
                        <form id="signupForm">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="name" class="form-label">
                                            <i class="fas fa-user me-2"></i>Họ và tên
                                        </label>
                                        <input type="text" class="form-control" id="name" name="name" placeholder="Nhập họ và tên">
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="phone" class="form-label">
                                            <i class="fas fa-phone me-2"></i>Số điện thoại
                                        </label>
                                        <input type="tel" class="form-control" id="phone" name="phone" placeholder="Nhập số điện thoại">
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="email" class="form-label">
                                    <i class="fas fa-envelope me-2"></i>Email Address *
                                </label>
                                <input type="email" class="form-control" id="email" name="email" placeholder="Nhập email của bạn" required>
                                <div class="invalid-feedback"></div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="password" class="form-label">
                                    <i class="fas fa-lock me-2"></i>Password *
                                </label>
                                <div class="password-field">
                                    <input type="password" class="form-control" id="password" name="password" placeholder="Ít nhất 6 ký tự" required>
                                    <i class="fas fa-eye password-toggle" onclick="togglePassword('password')"></i>
                                </div>
                                <div class="invalid-feedback"></div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="confirm_password" class="form-label">
                                    <i class="fas fa-lock me-2"></i>Confirm Password *
                                </label>
                                <div class="password-field">
                                    <input type="password" class="form-control" id="confirm_password" name="confirm_password" placeholder="Nhập lại mật khẩu" required>
                                    <i class="fas fa-eye password-toggle" onclick="togglePassword('confirm_password')"></i>
                                </div>
                                <div class="invalid-feedback"></div>
                            </div>
                            
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" id="terms_check" name="terms_check" required>
                                <label class="form-check-label terms-text" for="terms_check">
                                    Tôi đồng ý với <a href="#" class="auth-link">Điều khoản sử dụng</a> và <a href="#" class="auth-link">Chính sách bảo mật</a>
                                </label>
                                <div class="invalid-feedback"></div>
                            </div>
                            
                            <button type="submit" class="btn btn-primary" id="submitBtn">
                                <i class="fas fa-user-plus me-2"></i>Đăng ký
                                <span class="spinner-border spinner-border-sm ms-2 d-none" id="loadingSpinner"></span>
                            </button>
                        </form>

                        <!-- Mobile login prompt - only shows on small screens -->
                        <div class="signup-prompt-mobile">
                            <p class="mb-2">Đã có tài khoản?</p>
                            <a href="<?= BASE_URL ?>/signin" class="btn btn-outline-gold">
                                <i class="fas fa-sign-in-alt me-2"></i>Đăng nhập
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Toggle password visibility
        function togglePassword(fieldId) {
            const passwordField = document.getElementById(fieldId);
            const toggleIcon = passwordField.nextElementSibling;
            
            if (passwordField.type === 'password') {
                passwordField.type = 'text';
                toggleIcon.classList.remove('fa-eye');
                toggleIcon.classList.add('fa-eye-slash');
            } else {
                passwordField.type = 'password';
                toggleIcon.classList.remove('fa-eye-slash');
                toggleIcon.classList.add('fa-eye');
            }
        }

        // Show alert message
        function showAlert(message, type = 'danger') {
            const alertContainer = document.getElementById('alert-container');
            const alertHTML = `
                <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                    ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            `;
            alertContainer.innerHTML = alertHTML;
        }

        // Clear form validation
        function clearValidation() {
            document.querySelectorAll('.is-invalid').forEach(field => {
                field.classList.remove('is-invalid');
            });
        }

        // Show field error
        function showFieldError(fieldName, message) {
            const field = document.getElementById(fieldName);
            const feedback = field.nextElementSibling;
            
            field.classList.add('is-invalid');
            if (feedback && feedback.classList.contains('invalid-feedback')) {
                feedback.textContent = message;
            }
        }

        // Client-side validation
        function validateForm() {
            clearValidation();
            let isValid = true;
            
            const email = document.getElementById('email').value.trim();
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirm_password').value;
            const termsCheck = document.getElementById('terms_check').checked;
            
            // Email validation
            if (!email) {
                showFieldError('email', 'Email là bắt buộc');
                isValid = false;
            } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
                showFieldError('email', 'Email không hợp lệ');
                isValid = false;
            }
            
            // Password validation
            if (!password) {
                showFieldError('password', 'Mật khẩu là bắt buộc');
                isValid = false;
            } else if (password.length < 6) {
                showFieldError('password', 'Mật khẩu phải có ít nhất 6 ký tự');
                isValid = false;
            }
            
            // Confirm password validation
            if (!confirmPassword) {
                showFieldError('confirm_password', 'Xác nhận mật khẩu là bắt buộc');
                isValid = false;
            } else if (password !== confirmPassword) {
                showFieldError('confirm_password', 'Mật khẩu xác nhận không khớp');
                isValid = false;
            }
            
            // Terms validation
            if (!termsCheck) {
                showFieldError('terms_check', 'Vui lòng đồng ý với điều khoản sử dụng');
                isValid = false;
            }
            
            return isValid;
        }

        // Handle form submission
        document.getElementById('signupForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            if (!validateForm()) {
                return;
            }
            
            const submitBtn = document.getElementById('submitBtn');
            const loadingSpinner = document.getElementById('loadingSpinner');
            
            // Show loading
            submitBtn.disabled = true;
            loadingSpinner.classList.remove('d-none');
            
            const formData = new FormData(this);
            
            try {
                // Get base path dynamically
                const basePath = window.location.pathname.includes('/Ecom_website/') ? '/Ecom_website' : '';
                const signupUrl = basePath + '/auth/signup';
                
                const response = await fetch(signupUrl, {
                    method: 'POST',
                    body: formData
                });
                
                const result = await response.json();
                
                if (result.success) {
                    showAlert('Đăng ký thành công! Vui lòng kiểm tra email để xác thực tài khoản.', 'success');
                    // Reset form after successful registration
                    this.reset();
                } else {
                    if (result.data && typeof result.data === 'object') {
                        // Handle field-specific errors
                        Object.keys(result.data).forEach(fieldName => {
                            showFieldError(fieldName, result.data[fieldName]);
                        });
                    } else {
                        showAlert(result.message);
                    }
                }
            } catch (error) {
                console.error('Error:', error);
                showAlert('Có lỗi xảy ra khi kết nối đến server!');
            } finally {
                // Hide loading
                submitBtn.disabled = false;
                loadingSpinner.classList.add('d-none');
            }
        });

        // Auto-hide alerts after 5 seconds
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(() => {
                const alerts = document.querySelectorAll('.alert');
                alerts.forEach(alert => {
                    if (alert.classList.contains('show')) {
                        const bsAlert = new bootstrap.Alert(alert);
                        bsAlert.close();
                    }
                });
            }, 5000);
        });
    </script>
</body>
</html>
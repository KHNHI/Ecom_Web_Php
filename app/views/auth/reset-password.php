<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đặt lại mật khẩu - Jewelry Store</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
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
            height: 100%;
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
        
        .alert {
            border-radius: 0;
            border: none;
            margin-bottom: 1.5rem;
            font-size: 0.9rem;
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
        
        .auth-links {
            text-align: center;
            margin-top: 2rem;
        }
        
        .loading {
            opacity: 0.6;
            pointer-events: none;
        }

        .password-requirements {
            background: rgba(58, 47, 40, 0.03);
            border-radius: 0;
            padding: 1.25rem;
            margin-bottom: 1.5rem;
            font-size: 0.95rem;
            border: 1px solid rgba(212, 175, 55, 0.2);
            font-family: 'Inter', sans-serif;
        }
        
        .password-requirements h6 {
            color: var(--dark-brown);
            font-family: 'Inter', sans-serif;
            font-weight: 600;
            margin-bottom: 0.75rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.9rem;
            letter-spacing: 0.5px;
        }
        
        .password-requirements ul {
            margin: 0;
            padding-left: 1.5rem;
            color: var(--dark-brown);
            opacity: 0.8;
        }
        
        .password-requirements li {
            margin: 0.5rem 0;
            font-size: 0.9rem;
        }

        .fa-gem {
            color: var(--gold);
            margin-bottom: 1.5rem;
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
                            <p class="auth-subtitle">Khám phá bộ sưu tập trang sức cao cấp của chúng tôi</p>
                            <div class="mt-4">
                                <p class="mb-2">Chưa có tài khoản?</p>
                                <a href="<?= BASE_URL ?>/signup" class="btn btn-outline-light">
                                    <i class="fas fa-user-plus me-2"></i>Đăng ký ngay
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-7">
                    <div class="auth-right">
                        <div class="text-center mb-4">
                            <h2 class="fw-bold text-dark">Đặt lại mật khẩu</h2>
                            <p class="text-muted">Nhập mật khẩu mới cho tài khoản của bạn</p>
                        </div>
                        
                        <div id="alertContainer"></div>
                        
                        <div class="password-requirements mb-4">
                            <h6><i class="fas fa-info-circle me-2"></i>Yêu cầu mật khẩu:</h6>
                            <ul>
                                <li>Ít nhất 6 ký tự</li>
                                <li>Nên chứa chữ hoa, chữ thường và số</li>
                                <li>Không sử dụng mật khẩu dễ đoán</li>
                            </ul>
                        </div>
                        
                        <form id="resetPasswordForm">
                            <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
                            
                            <div class="mb-3">
                                <label for="password" class="form-label">
                                    <i class="fas fa-lock me-2"></i>Mật khẩu mới
                                </label>
                                <div class="password-field">
                                    <input type="password" class="form-control" id="password" name="password" placeholder="Nhập mật khẩu mới" required minlength="6">
                                    <i class="fas fa-eye password-toggle" onclick="togglePassword('password')"></i>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="confirm_password" class="form-label">
                                    <i class="fas fa-lock me-2"></i>Xác nhận mật khẩu
                                </label>
                                <div class="password-field">
                                    <input type="password" class="form-control" id="confirm_password" name="confirm_password" placeholder="Xác nhận mật khẩu" required minlength="6">
                                    <i class="fas fa-eye password-toggle" onclick="togglePassword('confirm_password')"></i>
                                </div>
                            </div>
                            
                            <button type="submit" class="btn btn-primary" id="submitBtn">
                                <span class="spinner-border spinner-border-sm me-2 d-none" id="spinner"></span>
                                <i class="fas fa-save me-2"></i>Đặt lại mật khẩu
                            </button>
                        </form>
                        
                        <div class="auth-links">
                            <a href="<?php echo url('signin'); ?>" class="auth-link">
                                <i class="fas fa-arrow-left me-2"></i>Quay lại đăng nhập
                            </a>
                        </div>

                        <!-- Mobile signup prompt - only shows on small screens -->
                        <div class="signup-prompt-mobile">
                            <p class="mb-2">Chưa có tài khoản?</p>
                            <a href="<?= BASE_URL ?>/signup" class="btn btn-outline-gold">
                                <i class="fas fa-user-plus me-2"></i>Đăng ký ngay
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

        document.getElementById('resetPasswordForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const form = this;
            const submitBtn = document.getElementById('submitBtn');
            const spinner = document.getElementById('spinner');
            const alertContainer = document.getElementById('alertContainer');
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirm_password').value;
            
            // Validate passwords match
            if (password !== confirmPassword) {
                alertContainer.innerHTML = `
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        Mật khẩu xác nhận không khớp!
                    </div>
                `;
                return;
            }
            
            // Show loading state
            form.classList.add('loading');
            spinner.classList.remove('d-none');
            
            try {
                const formData = new FormData(form);
                
                const response = await fetch('<?php echo url('auth/reset-password'); ?>', {
                    method: 'POST',
                    body: formData
                });
                
                const result = await response.json();
                
                // Show alert
                alertContainer.innerHTML = `
                    <div class="alert alert-${result.success ? 'success' : 'danger'}">
                        <i class="fas fa-${result.success ? 'check-circle' : 'exclamation-circle'} me-2"></i>
                        ${result.message}
                    </div>
                `;
                
                // Redirect to login if successful
                if (result.success) {
                    setTimeout(() => {
                        window.location.href = '<?php echo url('signin'); ?>';
                    }, 2000);
                }
                
            } catch (error) {
                alertContainer.innerHTML = `
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        Có lỗi xảy ra khi kết nối đến server!
                    </div>
                `;
            } finally {
                // Hide loading state
                form.classList.remove('loading');
                spinner.classList.add('d-none');
            }
        });
        
        // Real-time password confirmation validation
        document.getElementById('confirm_password').addEventListener('input', function() {
            const password = document.getElementById('password').value;
            const confirmPassword = this.value;
            
            if (confirmPassword && password !== confirmPassword) {
                this.setCustomValidity('Mật khẩu xác nhận không khớp');
                this.style.borderColor = '#dc3545';
            } else {
                this.setCustomValidity('');
                this.style.borderColor = '';
            }
        });
    </script>
</body>
</html>

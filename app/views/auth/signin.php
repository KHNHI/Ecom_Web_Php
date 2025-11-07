<?php require_once __DIR__ . '/../../../helpers/url_helper.php'; ?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập - JEWELRY</title>
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
        
        .auth-link {
            color: var(--gold);
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        
        .auth-link:hover {
            color: var(--dark-gold);
        }
        
        .forgot-password {
            text-align: right;
            margin-top: 8px;
        }
        
        .forgot-password a {
            color: var(--dark-brown);
            text-decoration: none;
            font-size: 0.85rem;
            opacity: 0.8;
            transition: all 0.3s ease;
        }

        .forgot-password a:hover {
            color: var(--gold);
            opacity: 1;
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
                                <a href="<?= url('signup') ?>" class="btn btn-outline-light">
                                    <i class="fas fa-user-plus me-2"></i>Đăng ký ngay
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-7">
                    <div class="auth-right">
                        <div class="text-center mb-4">
                            <h2 class="fw-bold text-dark">Đăng nhập</h2>
                            <p class="text-muted">Chào mừng bạn trở lại!</p>
                        </div>
                        
                        <!-- Alert Messages -->
                        <div id="alert-container"></div>
                        
                        <form id="signinForm">
                            <div class="mb-3">
                                <label for="email" class="form-label">
                                    <i class="fas fa-envelope me-2"></i>Email Address
                                </label>
                                <input type="email" class="form-control" id="email" name="email" placeholder="Nhập email của bạn" required>
                                <div class="invalid-feedback"></div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="password" class="form-label">
                                    <i class="fas fa-lock me-2"></i>Password
                                </label>
                                <div class="password-field">
                                    <input type="password" class="form-control" id="password" name="password" placeholder="Nhập mật khẩu" required>
                                    <i class="fas fa-eye password-toggle" onclick="togglePassword()"></i>
                                </div>
                                <div class="invalid-feedback"></div>
                                <div class="forgot-password">
                                    <a href="<?php echo url('forgot-password'); ?>">Quên mật khẩu?</a>
                                </div>
                            </div>
                            
                            <button type="submit" class="btn btn-primary" id="submitBtn">
                                <i class="fas fa-sign-in-alt me-2"></i>Đăng nhập
                                <span class="spinner-border spinner-border-sm ms-2 d-none" id="loadingSpinner"></span>
                            </button>
                        </form>

                        <!-- Mobile signup prompt - only shows on small screens -->
                        <div class="signup-prompt-mobile">
                            <p class="mb-2">Chưa có tài khoản?</p>
                            <a href="<?= url('signup') ?>" class="btn btn-outline-gold">
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
        function togglePassword() {
            const passwordField = document.getElementById('password');
            const toggleIcon = document.querySelector('.password-toggle');
            
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

        // Handle form submission
        document.getElementById('signinForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            clearValidation();
            
            const submitBtn = document.getElementById('submitBtn');
            const loadingSpinner = document.getElementById('loadingSpinner');
            
            // Show loading
            submitBtn.disabled = true;
            loadingSpinner.classList.remove('d-none');
            
            const formData = new FormData(this);
            
            try {
                console.log('Sending request to:', '<?= url('auth/signin') ?>');
                const response = await fetch('<?= url('auth/signin') ?>', {
                    method: 'POST',
                    body: formData
                });
                
                console.log('Response status:', response.status);
                console.log('Response headers:', response.headers);
                
                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                }
                
                const responseText = await response.text();
                console.log('Raw response length:', responseText.length);
                console.log('Raw response content:', responseText);
                console.log('Response starts with:', responseText.substring(0, 50));
                console.log('Response ends with:', responseText.substring(responseText.length - 50));
                
                // Check if response is empty
                if (!responseText.trim()) {
                    throw new Error('Server returned empty response');
                }
                
                let result;
                try {
                    result = JSON.parse(responseText);
                    console.log('Parsed JSON result:', result);
                } catch (parseError) {
                    console.error('JSON parse error:', parseError);
                    console.error('Failed to parse:', responseText);
                    throw new Error('Server returned invalid JSON. Raw response: ' + responseText);
                }
                
                if (result.success) {
                    showAlert('Đăng nhập thành công! Đang chuyển hướng...', 'success');
                    setTimeout(() => {
                        if (result.data && result.data.redirect) {
                            window.location.href = result.data.redirect;
                        } else {
                            window.location.href = '<?= url('/') ?>';
                        }
                    }, 1500);
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
                console.error('Full error details:', error);
                console.error('Error message:', error.message);
                console.error('Error type:', error.name);
                showAlert('Lỗi: ' + error.message);
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
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quên mật khẩu - Jewelry Store</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
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
            width: 100%;
            max-width: 1000px;
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
        
        .auth-links {
            text-align: center;
            margin-top: 2rem;
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
        
        .loading {
            opacity: 0.6;
            pointer-events: none;
        }
        
        .text-muted {
            color: var(--dark-brown) !important;
            opacity: 0.7;
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
                            <h2 class="fw-bold text-dark">Quên mật khẩu</h2>
                            <p class="text-muted">Nhập email để nhận link đặt lại mật khẩu</p>
                        </div>
                        
                        <div id="alertContainer"></div>
                        
                        <form id="forgotPasswordForm">
                            <div class="mb-3">
                                <label for="email" class="form-label">
                                    <i class="fas fa-envelope me-2"></i>Email Address
                                </label>
                                <input type="email" class="form-control" id="email" name="email" placeholder="Nhập email của bạn" required>
                            </div>
                            
                            <button type="submit" class="btn btn-primary" id="submitBtn">
                                <span class="spinner-border spinner-border-sm me-2 d-none" id="spinner"></span>
                                <i class="fas fa-paper-plane me-2"></i>Gửi link đặt lại
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
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('forgotPasswordForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const form = this;
            const submitBtn = document.getElementById('submitBtn');
            const spinner = document.getElementById('spinner');
            const alertContainer = document.getElementById('alertContainer');
            
            // Show loading state
            form.classList.add('loading');
            spinner.classList.remove('d-none');
            
            try {
                const formData = new FormData(form);
                
                const response = await fetch('<?php echo url('auth/forgot-password'); ?>', {
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
                
                // Reset form if successful
                if (result.success) {
                    form.reset();
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
    </script>
</body>
</html>

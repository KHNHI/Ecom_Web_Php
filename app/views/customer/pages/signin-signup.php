<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In / Sign Up</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../../assets/css.css">
    <style>
        body::before {
            content: "";
            position: fixed;
            top: 0; left: 0; right: 0; bottom: 0;
            z-index: -1;
            background-image: url('https://images.unsplash.com/photo-1611652022419-a9419f74343d?q=80&w=688&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D');
            background-size: cover;
            background-position: center;
            opacity: 0.22;
            pointer-events: none;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
<nav th:fragment="header" class="navbar navbar-expand-lg fixed-top">
    <div class="container">
        <a class="navbar-brand" href="#"><i class="fas fa-gem me-2"></i>JEWELRY</a>
        
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav mx-auto">
                <li class="nav-item">
                    <a class="nav-link" href="#home">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#about">About us</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#category">Category</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#collection">Collection</a>
                </li>
            </ul>
            
            <div class="d-flex align-items-center">
                <!-- Search -->
                <div class="search-container me-3">
                    <input type="text" class="search-input" placeholder="What are you looking for?">
                    <button class="search-btn">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
                
                <!-- Auth Links -->
                <div class="me-3">
                    <a href="#signin" class="text-decoration-none me-3" style="color: #666;">Sign in</a>
                    <!-- <a href="#signup" class="text-decoration-none" style="color: #666;">Sign up</a> -->
                </div>
                
                <!-- Icons -->
                <div class="nav-icons">
                    <i class="far fa-heart"></i>
                    <i class="fas fa-shopping-bag"></i>
                </div>
            </div>
        </div>
    </div>
</nav>

    <div class="auth-container">
        
        <div class="auth-content">
            <!-- Sign In / Sign Up Screen -->
            <div id="authScreen" class="auth-screen active">
                <ul class="nav nav-tabs auth-nav-tabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="auth-signin-tab" data-bs-toggle="tab" data-screen="signin">Sign In</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="auth-signup-tab" data-bs-toggle="tab" data-screen="signup">Sign Up</button>
                    </li>
                </ul>

                <!-- Sign Up Form -->
                <div id="auth-signupForm" class="auth-tab-pane">
                    <form id="auth-signupFormElement">
                        <div class="mb-3">
                            <label class="form-label">Name</label>
                            <input type="text" class="form-control" id="auth-signup_name" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email Address</label>
                            <input type="email" class="form-control" id="auth-signup_email" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <div class="auth-password-field">
                                <input type="password" class="form-control" id="auth-signup_password" placeholder="8+ characters" required>
                                <i class="fas fa-eye auth-password-toggle" onclick="togglePassword('auth-signup_password', this)"></i>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Confirm Password</label>
                            <div class="auth-password-field">
                                <input type="password" class="form-control" id="auth-signup_confirm_password" required>
                                <i class="fas fa-eye auth-password-toggle" onclick="togglePassword('auth-signup_confirm_password', this)"></i>
                            </div>
                        </div>
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" id="auth-terms_check" required>
                            <label class="form-check-label auth-terms-text">
                                Are you agree to Clicon <a href="#" class="auth-terms-link">Terms of Condition</a> and <a href="#" class="auth-terms-link">Privacy Policy</a>
                            </label>
                        </div>
                        <button type="submit" class="btn auth-btn-primary text-white">
                            SIGN UP <i class="fas fa-arrow-right ms-2"></i>
                        </button>
                    </form>
                    <div class="auth-divider">or</div>
                    <button class="auth-social-btn">
                        <i class="fab fa-google"></i> Sign up with Google
                    </button>
                    <button class="auth-social-btn">
                        <i class="fab fa-apple"></i> Sign up with Apple
                    </button>
                </div>

                <!-- Sign In Form -->
                <div id="auth-signinForm" class="auth-tab-pane" style="display: none;">
                    <form id="auth-signinFormElement">
                        <div class="mb-3">
                            <label class="form-label">Email Address</label>
                            <input type="email" class="form-control" id="auth-signin_email" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <a href="#" class="auth-forgot-link" onclick="showScreen('auth-forgotScreen')">Forget Password</a>
                            <div class="auth-password-field">
                                <input type="password" class="form-control" id="auth-signin_password" required>
                                <i class="fas fa-eye auth-password-toggle" onclick="togglePassword('auth-signin_password', this)"></i>
                            </div>
                        </div>
                        <button type="submit" class="btn auth-btn-primary text-white">
                            SIGN IN <i class="fas fa-arrow-right ms-2"></i>
                        </button>
                    </form>
                    <div class="auth-divider">or</div>
                    <button class="auth-social-btn">
                        <i class="fab fa-google"></i> Login with Google
                    </button>
                    <button class="auth-social-btn">
                        <i class="fab fa-apple"></i> Login with Apple
                    </button>
                </div>
            </div>

            <!-- Forget Password Screen -->
            <div id="auth-forgotScreen" class="auth-screen">
                <h4 class="title">Forget Password</h4>
                <p class="auth-placeholder-text">Enter the email address or mobile phone number associated with your Clicon account.</p>
                
                <form id="auth-forgotFormElement">
                    <div class="mb-3">
                        <label class="form-label">Email Address</label>
                        <input type="email" class="form-control" id="auth-forgot_email" required>
                    </div>
                    <button type="submit" class="btn auth-btn-primary text-white">
                        SEND CODE <i class="fas fa-arrow-right ms-2"></i>
                    </button>
                </form>

                <div class="auth-help-text">
                    Already have account? <a href="#" class="auth-back-link" onclick="showScreen('authScreen'); switchTab('signin')">Sign In</a><br>
                    Don't have account? <a href="#" class="auth-back-link" onclick="showScreen('authScreen'); switchTab('signup')">Sign Up</a>
                </div>                
            </div>

            <!-- Verify Email Screen -->
            <div id="auth-verifyScreen" class="auth-screen">
                <h4 class="mb-3">Verify Your Email Address</h4>
                <p class="auth-placeholder-text">Nam ultricies lectus a risus blandit elementum. Quisque arcu arcu, tristique a eu diam.</p>
                
                <form id="auth-verifyFormElement">
                    <div class="mb-3">
                        <label class="form-label">Verification Code</label>
                        <input type="text" class="form-control" id="auth-verify_code" placeholder="Enter 6-digit code" required>
                        <a href="#" class="auth-forgot-link mt-2" onclick="resendCode()">Resend Code</a>
                    </div>
                    <button type="submit" class="btn btn-primary">
                        VERIFY ME <i class="fas fa-arrow-right ms-2"></i>
                    </button>
                </form>
            </div>

            <!-- Reset Password Screen -->
            <div id="auth-resetScreen" class="auth-screen">
                <h4 class="title-auth">Reset Password</h4>
                <p class="auth-placeholder-text">Duis sagittis molestie tellus, at euismod sapien pellaque quis. Fusce lorem nunc, fringilla sit amet nunc.</p>
                
                <form id="auth-resetFormElement">
                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <div class="auth-password-field">
                            <input type="password" class="form-control" id="auth-reset_password" placeholder="8+ characters" required>
                            <i class="fas fa-eye auth-password-toggle" onclick="togglePassword('auth-reset_password', this)"></i>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Confirm Password</label>
                        <div class="auth-password-field">
                            <input type="password" class="form-control" id="auth-reset_confirm_password" required>
                            <i class="fas fa-eye auth-password-toggle" onclick="togglePassword('auth-reset_confirm_password', this)"></i>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">
                        RESET PASSWORD <i class="fas fa-arrow-right ms-2"></i>
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Toggle password visibility
        function togglePassword(inputId, icon) {
            const input = document.getElementById(inputId);
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }

        // Show specific screen
        function showScreen(screenId) {
            document.querySelectorAll('.auth-screen').forEach(screen => {
                screen.classList.remove('active');
            });
            document.getElementById(screenId).classList.add('active');
        }

        // Switch between Sign In and Sign Up tabs
        function switchTab(tab) {
            if (tab === 'signin') {
                document.getElementById('auth-signin-tab').classList.add('active');
                document.getElementById('auth-signup-tab').classList.remove('active');
                document.getElementById('auth-signinForm').style.display = 'block';
                document.getElementById('auth-signupForm').style.display = 'none';
            } else {
                document.getElementById('auth-signup-tab').classList.add('active');
                document.getElementById('auth-signin-tab').classList.remove('active');
                document.getElementById('auth-signupForm').style.display = 'block';
                document.getElementById('auth-signinForm').style.display = 'none';
            }
        }

        // Tab click handlers
        document.getElementById('auth-signin-tab').addEventListener('click', () => switchTab('signin'));
        document.getElementById('auth-signup-tab').addEventListener('click', () => switchTab('signup'));

        // Sign Up Form Handler
        document.getElementById('auth-signupFormElement').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const name = document.getElementById('auth-signup_name').value;
            const email = document.getElementById('auth-signup_email').value;
            const password = document.getElementById('auth-signup_password').value;
            const confirmPassword = document.getElementById('auth-signup_confirm_password').value;
            const termsAccepted = document.getElementById('auth-terms_check').checked;

            if (password !== confirmPassword) {
                alert('Passwords do not match!');
                return;
            }

            if (!termsAccepted) {
                alert('Please accept the Terms and Conditions');
                return;
            }

            // Send data to backend
            fetch('signup.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    name: name,
                    email: email,
                    password: password
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Registration successful! Please verify your email.');
                    showScreen('auth-verifyScreen');
                } else {
                    alert(data.message || 'Registration failed!');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred. Please try again.');
            });
        });

        // Sign In Form Handler
        document.getElementById('auth-signinFormElement').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const email = document.getElementById('auth-signin_email').value;
            const password = document.getElementById('auth-signin_password').value;

            // Send data to backend
            fetch('signin.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    email: email,
                    password: password
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Login successful!');
                    window.location.href = 'dashboard.php';
                } else {
                    alert(data.message || 'Login failed!');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred. Please try again.');
            });
        });

        // Forgot Password Form Handler
        document.getElementById('auth-forgotFormElement').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const email = document.getElementById('auth-forgot_email').value;

            // Send reset code to email
            fetch('forgot_password.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    email: email
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Verification code sent to your email!');
                    showScreen('auth-verifyScreen');
                } else {
                    alert(data.message || 'Failed to send code!');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred. Please try again.');
            });
        });

        // Verify Code Form Handler
        document.getElementById('auth-verifyFormElement').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const code = document.getElementById('auth-verify_code').value;

            // Verify the code
            fetch('verify_code.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    code: code
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Code verified successfully!');
                    showScreen('auth-resetScreen');
                } else {
                    alert(data.message || 'Invalid code!');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred. Please try again.');
            });
        });

        // Reset Password Form Handler
        document.getElementById('auth-resetFormElement').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const password = document.getElementById('auth-reset_password').value;
            const confirmPassword = document.getElementById('auth-reset_confirm_password').value;

            if (password !== confirmPassword) {
                alert('Passwords do not match!');
                return;
            }

            // Reset password
            fetch('reset_password.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    password: password
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Password reset successfully! Please login.');
                    showScreen('authScreen');
                    switchTab('signin');
                } else {
                    alert(data.message || 'Failed to reset password!');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred. Please try again.');
            });
        });

        // Resend verification code
        function resendCode() {
            const email = document.getElementById('auth-forgot_email').value;
            
            fetch('resend_code.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    email: email
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('New code sent to your email!');
                } else {
                    alert(data.message || 'Failed to resend code!');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred. Please try again.');
            });
        }
    </script>
    </script>
    <!-- ...existing code... -->
    <?php include __DIR__ . '/../components/footer.php'; ?>
<script>
    // ...existing JS code...

    // Sự kiện chuyển trang cho navbar
    document.addEventListener('DOMContentLoaded', function() {
        // Home
        document.querySelectorAll('.nav-link[href="#home"]').forEach(el => {
            el.addEventListener('click', function(e) {
                e.preventDefault();
                window.location.href = 'index.html';
            });
        });
        // Category
        document.querySelectorAll('.nav-link[href="#category"]').forEach(el => {
            el.addEventListener('click', function(e) {
                e.preventDefault();
                window.location.href = 'list-product.html';
            });
        });
        // Sign in
        document.querySelectorAll('a[href="#signin"]').forEach(el => {
            el.addEventListener('click', function(e) {
                e.preventDefault();
                window.location.href = 'signin-signup.html';
            });
        });
    });

    // ...existing JS code...
</script>
<!-- ...existing code... -->
</body>
</html>

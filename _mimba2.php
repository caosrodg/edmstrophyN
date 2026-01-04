<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EDMS Trophy Login</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
   <link rel="stylesheet" href="admin.css">
</head>
<body>
    <!-- Pre-loader -->
    <div id="preloader">
        <div class="loader-content">
            <div class="loader-logo">
                <i class="fas fa-trophy"></i>
            </div>
            <div class="loader-text">EDM's Trophy</div>
            <div class="loader-bars">
                <div class="loader-bar"></div>
                <div class="loader-bar"></div>
                <div class="loader-bar"></div>
                <div class="loader-bar"></div>
                <div class="loader-bar"></div>
                <div class="loader-bar"></div>
            </div>
            <div class="loading-text">Loading System...</div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="login-container">
        <!-- Left Side - Welcome Section -->
        <div class="login-left" style="background-color: lightyellow;">
            <div class="logo-icon">
                <i class="fas fa-trophy"></i>
            </div>
            <div class="welcome-text">
                <h1>EDMS Trophy</h1>
                <p>Sports Management System</p>
            </div>
            
            <div class="features">
                <div class="feature">
                    <i class="fas fa-medal"></i>
                    <p>Track Results</p>
                </div>
                <div class="feature">
                    <i class="fas fa-chart-line"></i>
                    <p>Live Updates</p>
                </div>
                <div class="feature">
                    <i class="fas fa-users"></i>
                    <p>Team Management</p>
                </div>
                <div class="feature">
                    <i class="fas fa-award"></i>
                    <p>Achievement Tracking</p>
                </div>
            </div>
        </div>

        <!-- Right Side - Login Form -->
        <div class="login-right">
            <div class="login-header">
                <h2>Welcome</h2>
                <p>Sign in to your account</p>
            </div>

            <?php if ($login_error): ?>
                <div class="error-message">
                    <i class="fas fa-exclamation-circle"></i>
                    <?php echo $login_error; ?>
                </div>
            <?php endif; ?>

            <form action="index.php" method="POST">
                <div class="form-group">
                    <label for="employment_number">
                        <i class="fas fa-id-card"></i> Employment Number
                    </label>
                    <div style="position: relative;">
                        <i class="fas fa-user input-icon"></i>
                        <input type="text" id="employment_number" name="employment_number" required placeholder="Enter your employment number">
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="password">
                        <i class="fas fa-lock"></i> Password
                    </label>
                    <div style="position: relative;">
                        <i class="fas fa-key input-icon"></i>
                        <input type="password" id="password" name="password" required placeholder="Enter your password">
                    </div>
                </div>
                
                <button type="submit" name="login" class="login-button">
                    <i class="fas fa-sign-in-alt"></i> Login to Dashboard
                </button>
                
                
            </form>
            <div class="form-group">
            <br/> <br/>
<br/>
            <ul>Applinc-Technology &copy;</ul>
        </div>
        </div>
    </div>

    <script>
        // Hide preloader when page is fully loaded
        window.addEventListener('load', function() {
            setTimeout(function() {
                const preloader = document.getElementById('preloader');
                preloader.style.opacity = '0';
                setTimeout(function() {
                    preloader.style.display = 'none';
                }, 500);
            }, 1500); // Show loader for 1.5 seconds
        });
    </script>
</body>
</html>
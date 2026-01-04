<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$user_role = $_SESSION['user_role'] ?? 'Viewer';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - EDMS Trophy</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
     <link rel="stylesheet" href="dash.css">
</head>
<body>
    <!-- Loader -->
    <div id="loader">
        <div class="loader-content">
            <div class="loader-icon">
                <i class="fas fa-trophy"></i>
            </div>
            <div class="loader-text">EDM's Trophy</div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="container">
        <h2><i class="fas fa-trophy"></i> EDMS Trophy Dashboard</h2>

        <div class="user-info">
            <p>Welcome, <strong><?php echo htmlspecialchars($_SESSION['full_name']); ?></strong>!</p>
            <p>Employment Number: <?php echo htmlspecialchars($_SESSION['employment_number']); ?></p>
            <p>Your Role: <strong><?php echo htmlspecialchars($user_role); ?></strong></p>
        </div>

        <div class="dashboard-buttons">
            <?php if ($user_role === 'Admin'): ?>
                <a href="add_update_modal.php">
                    <i class="fas fa-user-shield"></i> Manage Data (Admin)
                </a>
            <?php endif; ?>

            <?php if (in_array($user_role, ['Admin', 'Data Officer'])): ?>
                <a href="add_update_modal.php">
                    <i class="fas fa-database"></i> Manage Data (Data Officer)
                </a>
                <a href="view_results.php">
                    <i class="fas fa-chart-line"></i> View Results
                </a>
            <?php endif; ?>

            <a href="view_trophy_progress.php">
                <i class="fas fa-medal"></i> View Trophy Progress
            </a>
        </div>

        <a href="logout.php" class="logout-link">
            <i class="fas fa-sign-out-alt"></i> Logout
        </a>
    </div>

    <!-- Footer -->
    <div class="footer">
        <div class="footer-brand">
            <i class="fas fa-code"></i>
            <span>Applinc-Tecnologies</span>
        </div>
        <p>Sports Management System &copy; <?php echo date('Y'); ?></p>
    </div>

    <script>
        window.addEventListener('load', function() {
            setTimeout(function() {
                document.getElementById('loader').style.opacity = '0';
                setTimeout(function() {
                    document.getElementById('loader').style.display = 'none';
                }, 500);
            }, 1000);
        });
    </script>
</body>
</html>
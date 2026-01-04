<?php
session_start();

// Database connection
include 'config.php'; // Include the centralized config file
$login_error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login'])) {
    $employment_number = $conn->real_escape_string($_POST['employment_number']);
    $user_password_plain = $_POST['password'];

    $sql = "SELECT id, employment_number, password, full_name, user_role FROM users WHERE employment_number = '$employment_number'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        if (password_verify($user_password_plain, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['employment_number'] = $user['employment_number'];
            $_SESSION['full_name'] = $user['full_name'];
            $_SESSION['user_role'] = $user['user_role'];
            header("Location: dashboard.php");
            exit();
        } else {
            $login_error = "Invalid employment number or password.";
        }
    } else {
        $login_error = "Invalid employment number or password.";
    }
}

if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}

$conn->close();
?>
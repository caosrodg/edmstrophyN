<?php
session_start();
include 'config.php'; // Include your database connection

// Check if user is logged in and has the 'Admin' role
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'Admin') {
    header("Location: index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $full_name = trim($_POST['full_name'] ?? '');
    $employment_number = trim($_POST['employment_number'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone_number = trim($_POST['phone_number'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if (empty($full_name) || empty($employment_number) || empty($password) || empty($confirm_password)) {
        set_message("Please fill in all required fields.", "error");
        header("Location: manage_data.php");
        exit();
    }

    if ($password !== $confirm_password) {
        set_message("Passwords do not match.", "error");
        header("Location: manage_data.php");
        exit();
    }

    // Hash the password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $user_role = 'Data Officer'; // New officers are 'Data Officer'

    // Check if employment number or email already exists
    $stmt_check = $conn->prepare("SELECT id FROM users WHERE employment_number = ? OR email = ?");
    $stmt_check->bind_param("ss", $employment_number, $email);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();

    if ($result_check->num_rows > 0) {
        set_message("An account with this employment number or email already exists.", "error");
        $stmt_check->close();
        header("Location: manage_data.php");
        exit();
    }
    $stmt_check->close();

    // Insert new data officer
    $stmt = $conn->prepare("INSERT INTO users (full_name, employment_number, email, phone_number, password, user_role) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $full_name, $employment_number, $email, $phone_number, $hashed_password, $user_role);

    if ($stmt->execute()) {
        set_message("New Data Officer added successfully!");
    } else {
        set_message("Error adding Data Officer: " . $stmt->error, "error");
    }

    $stmt->close();
    $conn->close();
    header("Location: manage_data.php");
    exit();
} else {
    header("Location: manage_data.php"); // Redirect if accessed directly
    exit();
}
?>
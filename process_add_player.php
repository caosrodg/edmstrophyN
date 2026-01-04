<?php
session_start();
include 'config.php'; // Include your database connection

// Check if user is logged in and has appropriate role
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['user_role'], ['Admin', 'Data Officer'])) {
    header("Location: index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $full_name = trim($_POST['full_name'] ?? '');
    $date_of_birth = $_POST['date_of_birth'] ?? null;
    $gender = $_POST['gender'] ?? null;
    $phone_number = trim($_POST['phone_number'] ?? '');
    $department_id = $_POST['department_id'] ?? null;
    $playing_position = trim($_POST['playing_position'] ?? '');

    if (empty($full_name) || empty($gender) || empty($department_id)) {
        set_message("Please fill in all required fields: Full Name, Gender, and Department.", "error");
        header("Location: manage_data.php");
        exit();
    }

    // Prepare for insertion
    $stmt = $conn->prepare("INSERT INTO players (full_name, date_of_birth, gender, phone_number, department_id, playing_position) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssis", $full_name, $date_of_birth, $gender, $phone_number, $department_id, $playing_position);

    if ($stmt->execute()) {
        set_message("New Player '" . htmlspecialchars($full_name) . "' added successfully!");
    } else {
        set_message("Error adding Player: " . $stmt->error, "error");
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
<?php
session_start();
include 'config.php'; // Include your database connection

// Check if user is logged in and has appropriate role
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['user_role'], ['Admin', 'Data Officer'])) {
    header("Location: index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $event_name = trim($_POST['event_name'] ?? '');
    $sport_id = $_POST['sport_id'] ?? null;
    $department_id = $_POST['department_id'] ?? null;
    $event_date = $_POST['event_date'] ?? null;
    $location = trim($_POST['location'] ?? '');
    $created_by_user_id = $_SESSION['user_id']; // The user creating the event

    if (empty($event_name) || empty($sport_id) || empty($department_id) || empty($event_date)) {
        set_message("Please fill in all required fields: Event Name, Sport, Department, and Event Date.", "error");
        header("Location: manage_data.php");
        exit();
    }

    // Prepare for insertion
    $stmt = $conn->prepare("INSERT INTO events (event_name, sport_id, department_id, event_date, location, created_by_user_id) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("siissi", $event_name, $sport_id, $department_id, $event_date, $location, $created_by_user_id);

    if ($stmt->execute()) {
        set_message("New Event '" . htmlspecialchars($event_name) . "' added successfully!");
    } else {
        set_message("Error adding Event: " . $stmt->error, "error");
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
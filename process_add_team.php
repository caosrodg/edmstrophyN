<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user_id']) || !in_array($_SESSION['user_role'], ['Admin', 'Data Officer'])) {
    $_SESSION['message'] = "Access denied.";
    $_SESSION['message_type'] = 'error';
    header("Location: add_update_modal.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $team_name = trim($_POST['name']);
    $department_id = $_POST['department_id'];
    $is_relay_team = isset($_POST['is_relay_team']) ? 1 : 0;

    // Validate inputs
    if (empty($team_name) || empty($department_id)) {
        $_SESSION['message'] = "Please fill in all required fields.";
        $_SESSION['message_type'] = 'error';
        header("Location: add_update_modal.php");
        exit();
    }

    // Check if team already exists
    $check_sql = "SELECT id FROM teams WHERE name = ? AND department_id = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("si", $team_name, $department_id);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();

    if ($check_result->num_rows > 0) {
        $_SESSION['message'] = "A team with this name already exists in the selected department.";
        $_SESSION['message_type'] = 'error';
    } else {
        // Insert new team with relay flag
        $insert_sql = "INSERT INTO teams (name, department_id, is_relay_team) VALUES (?, ?, ?)";
        $insert_stmt = $conn->prepare($insert_sql);
        $insert_stmt->bind_param("sii", $team_name, $department_id, $is_relay_team);

        if ($insert_stmt->execute()) {
            $_SESSION['message'] = "Team added successfully!" . ($is_relay_team ? " (Relay Team)" : "");
            $_SESSION['message_type'] = 'success';
        } else {
            $_SESSION['message'] = "Error adding team: " . $conn->error;
            $_SESSION['message_type'] = 'error';
        }
        $insert_stmt->close();
    }
    $check_stmt->close();
    $conn->close();
    
    header("Location: add_update_modal.php");
    exit();
}
?>
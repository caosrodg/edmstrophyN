<?php
session_start();
include 'config.php'; // Include your database connection

// Check if user is logged in and has appropriate role
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['user_role'], ['Admin', 'Data Officer'])) {
    header("Location: index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $event_id = $_POST['event_id'] ?? null;
    $player_id = $_POST['player_id'] ?? null;
    $team_id = $_POST['team_id'] ?? null;
    $score_value = trim($_POST['score_value'] ?? '');
    $rank = $_POST['rank'] ?? null;
    $points_awarded = $_POST['points_awarded'] ?? null;
    $is_final = isset($_POST['is_final']) ? 1 : 0;
    $recorded_by_user_id = $_SESSION['user_id'];

    if (empty($event_id) || empty($score_value)) {
        set_message("Please select an Event and provide a Score/Time/Distance.", "error");
        header("Location: manage_data.php");
        exit();
    }

    // Determine if the event is individual or team-based from the database
    $stmt_event_type = $conn->prepare("SELECT is_individual FROM sports s JOIN events e ON e.sport_id = s.id WHERE e.id = ?");
    $stmt_event_type->bind_param("i", $event_id);
    $stmt_event_type->execute();
    $result_event_type = $stmt_event_type->get_result();
    if ($result_event_type->num_rows === 0) {
        set_message("Invalid Event selected.", "error");
        $stmt_event_type->close();
        header("Location: manage_data.php");
        exit();
    }
    $event_type_row = $result_event_type->fetch_assoc();
    $is_individual_event = (bool)$event_type_row['is_individual'];
    $stmt_event_type->close();

    if ($is_individual_event) {
        if (empty($player_id)) {
            set_message("Please select a Player for this individual event.", "error");
            header("Location: manage_data.php");
            exit();
        }
        $team_id = null; // Ensure team_id is null for individual events
    } else { // Team event
        if (empty($team_id)) {
            set_message("Please select a Team for this team event.", "error");
            header("Location: manage_data.php");
            exit();
        }
        $player_id = null; // Ensure player_id is null for team events
    }

    // Validate score_value can be converted to decimal
    if (!is_numeric($score_value)) {
        set_message("Score/Time/Distance must be a numeric value.", "error");
        header("Location: manage_data.php");
        exit();
    }
    $score_value = (float)$score_value; // Cast to float for database

    // Insert or update score
    // For simplicity, we'll always insert a new score entry.
    // A more advanced system might check for existing scores for the same player/team in the same event
    // and offer to update or create a new entry (e.g., if multiple attempts are allowed).
    $stmt = $conn->prepare("INSERT INTO scores (event_id, player_id, team_id, score_value, `rank`, points_awarded, recorded_by_user_id, is_final) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("iiidiiii", $event_id, $player_id, $team_id, $score_value, $rank, $points_awarded, $recorded_by_user_id, $is_final);

    if ($stmt->execute()) {
        $participant_name = $is_individual_event ? "Player" : "Team";
        set_message("Score for " . $participant_name . " updated successfully!");
    } else {
        set_message("Error updating score: " . $stmt->error, "error");
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
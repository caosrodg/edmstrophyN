<?php
session_start();
// Redirect to login if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

require_once 'config.php'; // Include your database connection file (config.php now)

$user_role = $_SESSION['user_role'] ?? 'Viewer'; // Default to Viewer if not set

// Fetch all events for display
$events = [];
try {
    // MySQLi prepared statement
    $stmt = $conn->prepare("SELECT e.id, e.event_name, e.event_date, e.location, s.name AS sport_name, d.name AS department_name
                            FROM events e
                            JOIN sports s ON e.sport_id = s.id
                            JOIN departments d ON e.department_id = d.id
                            ORDER BY e.event_date DESC, e.event_name ASC");

    if ($stmt === false) {
        throw new Exception("Prepare failed: " . $conn->error);
    }

    $stmt->execute();

    // Get results from MySQLi statement
    $result = $stmt->get_result(); // This gets a mysqli_result object
    while ($row = $result->fetch_assoc()) { // Use fetch_assoc() to get rows
        $events[] = $row;
    }
    $stmt->close(); // Close the statement
} catch (Exception $e) { // Catch any general exception during database operations
    echo "Error fetching events: " . $e->getMessage();
}

// Function to fetch scores for a given event ID using MySQLi
function getScoresForEvent($conn, $event_id) {
    $scores = [];
    try {
        $stmt = $conn->prepare("SELECT sc.score_value, sc.rank, sc.points_awarded,
                                        p.full_name AS player_name,
                                        t.name AS team_name
                                FROM scores sc
                                LEFT JOIN players p ON sc.player_id = p.id
                                LEFT JOIN teams t ON sc.team_id = t.id
                                WHERE sc.event_id = ?
                                ORDER BY sc.rank ASC, sc.score_value DESC");

        if ($stmt === false) {
            throw new Exception("Prepare failed: " . $conn->error);
        }

        $stmt->bind_param('i', $event_id); // 'i' for integer
        $stmt->execute();

        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $scores[] = $row;
        }
        $stmt->close();
    } catch (Exception $e) {
        echo "Error fetching scores: " . $e->getMessage();
    }
    return $scores;
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Results - EDMS Trophy</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
            color: #333;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        h2 {
            color: #333;
            border-bottom: 2px solid #eee;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .event-card {
            background-color: #f9f9f9;
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 15px;
            margin-bottom: 20px;
        }
        .event-card h3 {
            margin-top: 0;
            color: #007bff;
        }
        .event-card p {
            margin: 5px 0;
        }
        .event-card .scores-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        .event-card .scores-table th,
        .event-card .scores-table td {
            border: 1px solid #eee;
            padding: 8px;
            text-align: left;
        }
        .event-card .scores-table th {
            background-color: #e9ecef;
            font-weight: bold;
        }
        .no-results {
            text-align: center;
            padding: 20px;
            color: #666;
        }
        .back-link {
            display: inline-block;
            margin-top: 20px;
            color: #007bff;
            text-decoration: none;
        }
        .back-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>View Event Results</h2>

        <p>Welcome, <strong><?php echo htmlspecialchars($_SESSION['full_name']); ?></strong>! Here are the latest event results.</p>

        <?php if (empty($events)): ?>
            <p class="no-results">No events found yet. Check back later!</p>
        <?php else: ?>
            <?php foreach ($events as $event): ?>
                <div class="event-card">
                    <h3><?php echo htmlspecialchars($event['event_name']); ?> - <?php echo htmlspecialchars($event['sport_name']); ?></h3>
                    <p><strong>Department:</strong> <?php echo htmlspecialchars($event['department_name']); ?></p>
                    <p><strong>Date:</strong> <?php echo htmlspecialchars($event['event_date']); ?></p>
                    <p><strong>Location:</strong> <?php echo htmlspecialchars($event['location'] ?? 'N/A'); ?></p>

                    <h4>Scores:</h4>
                    <?php
                        $scores = getScoresForEvent($conn, $event['id']);
                        if (empty($scores)):
                    ?>
                        <p>No scores recorded for this event yet.</p>
                    <?php else: ?>
                        <table class="scores-table">
                            <thead>
                                <tr>
                                    <th>Rank</th>
                                    <th>Competitor</th>
                                    <th>Score Value</th>
                                    <th>Points Awarded</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($scores as $score): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($score['rank'] ?? 'N/A'); ?></td>
                                        <td>
                                            <?php
                                                // Check if player_name is set and not empty, otherwise check team_name
                                                if (isset($score['player_name']) && $score['player_name']) {
                                                    echo htmlspecialchars($score['player_name']) . " (Player)";
                                                } elseif (isset($score['team_name']) && $score['team_name']) {
                                                    echo htmlspecialchars($score['team_name']) . " (Team)";
                                                } else {
                                                    echo "N/A";
                                                }
                                            ?>
                                        </td>
                                        <td><?php echo htmlspecialchars($score['score_value']); ?></td>
                                        <td><?php echo htmlspecialchars($score['points_awarded'] ?? '0'); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>

        <a href="dashboard.php" class="back-link">← Back to Dashboard</a>
    </div>
</body>
</html>
<?php
session_start();
// Redirect to login if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

require_once 'config.php'; // Include your database connection file (config.php)

$user_role = $_SESSION['user_role'] ?? 'Viewer'; // Default to Viewer if not set

$trophy_progress = [];

try {
    // SQL query to calculate total points for each department
    // We join scores -> events -> departments
    $sql = "SELECT
                d.id AS department_id,
                d.name AS department_name,
                SUM(s.points_awarded) AS total_points
            FROM
                departments d
            LEFT JOIN
                events e ON d.id = e.department_id
            LEFT JOIN
                scores s ON e.id = s.event_id
            GROUP BY
                d.id, d.name
            ORDER BY
                total_points DESC, d.name ASC"; // Order by total points (desc) then department name

    $stmt = $conn->prepare($sql);

    if ($stmt === false) {
        throw new Exception("Prepare failed: " . $conn->error);
    }

    $stmt->execute();

    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        // Ensure total_points is treated as a number, defaulting to 0 if NULL
        $row['total_points'] = $row['total_points'] ?? 0;
        $trophy_progress[] = $row;
    }
    $stmt->close();

} catch (Exception $e) {
    echo "Error fetching trophy progress: " . $e->getMessage();
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trophy Progress - EDMS Trophy</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
            color: #333;
        }
        .container {
            max-width: 960px;
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
        .trophy-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .trophy-table th,
        .trophy-table td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }
        .trophy-table th {
            background-color: #007bff;
            color: white;
            font-weight: bold;
        }
        .trophy-table tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        .trophy-table tr:hover {
            background-color: #ddd;
        }
        .trophy-table td:nth-child(1) { /* Rank column */
            text-align: center;
            font-weight: bold;
        }
        .trophy-table td:nth-child(3) { /* Total Points column */
            text-align: center;
            font-weight: bold;
            color: #28a745; /* Green for points */
        }
        .no-progress {
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
        <h2>Overall Trophy Progress (Department Leaderboard)</h2>

        <p>This shows the total points accumulated by each department across all events.</p>

        <?php if (empty($trophy_progress)): ?>
            <p class="no-progress">No trophy progress to display yet. Events and scores need to be recorded.</p>
        <?php else: ?>
            <table class="trophy-table">
                <thead>
                    <tr>
                        <th>Rank</th>
                        <th>Department</th>
                        <th>Total Points</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $rank = 1;
                    foreach ($trophy_progress as $dept):
                    ?>
                        <tr>
                            <td><?php echo $rank++; ?></td>
                            <td><?php echo htmlspecialchars($dept['department_name']); ?></td>
                            <td><?php echo htmlspecialchars($dept['total_points']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>

        <a href="dashboard.php" class="back-link">← Back to Dashboard</a>
    </div>
</body>
</html>
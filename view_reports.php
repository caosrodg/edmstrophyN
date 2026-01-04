<?php
session_start();

// Redirect to login if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$user_full_name = $_SESSION['full_name'] ?? 'Guest';
$user_role = $_SESSION['user_role'] ?? 'Viewer';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports - EDMS Trophy</title>
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
            text-align: center;
        }
        h2 {
            color: #333;
            border-bottom: 2px solid #eee;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        p {
            margin-bottom: 20px;
        }
        .back-link {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }
        .back-link:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Reports and Leaderboards</h2>
        <p>This page is under construction. Here you will find various reports, leaderboards, and analytics related to the EDMS Trophy events.</p>
        <p>Currently logged in as: <strong><?php echo htmlspecialchars($user_full_name); ?> (<?php echo htmlspecialchars($user_role); ?>)</strong></p>

        <!-- Example of where reports might go -->
        <h3>Overall Department Standings (Coming Soon)</h3>
        <p>Display points accumulated by each department.</p>

        <h3>Event-specific Results (Coming Soon)</h3>
        <p>Detailed results for each event, including scores, ranks, and participants.</p>

        <a href="dashboard.php" class="back-link">Back to Dashboard</a>
    </div>
</body>
</html>
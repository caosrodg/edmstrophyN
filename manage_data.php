<?php
// Disable error reporting
error_reporting(0);
ini_set('display_errors', 0);

session_start();

// Database connection details - MODIFIED TO USE config.php
include 'config.php'; // Include the centralized config file

// Redirect to login if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

// Fetch user role from session
$user_role = $_SESSION['user_role'] ?? 'Viewer'; // Default to Viewer if not set

// Fetch departments for dropdowns
$departments = [];
$dept_result = $conn->query("SELECT id, name FROM departments ORDER BY name");
if ($dept_result) {
    while ($row = $dept_result->fetch_assoc()) {
        $departments[] = $row;
    }
}

// Fetch sports for dropdowns
$sports = [];
$sport_result = $conn->query("SELECT id, name, type, unit, is_individual FROM sports ORDER BY name");
if ($sport_result) {
    while ($row = $sport_result->fetch_assoc()) {
        $sports[] = $row;
    }
}

// Fetch events for dropdowns
$events = [];
// More complex query to get event details including sport type and department name for display
$event_sql = "SELECT e.id, e.event_name, e.event_date, s.name AS sport_name, d.name AS department_name, d.id AS department_id, s.is_individual 
              FROM events e
              JOIN sports s ON e.sport_id = s.id
              JOIN departments d ON e.department_id = d.id
              ORDER BY e.event_date DESC, e.event_name ASC";
$event_result = $conn->query($event_sql);
if ($event_result) {
    while ($row = $event_result->fetch_assoc()) {
        $events[] = $row;
    }
}
// Fetch players for dropdowns (for update scores, etc.)
$players = [];
$player_result = $conn->query("SELECT id, full_name, department_id FROM players ORDER BY full_name");
if ($player_result) {
    while ($row = $player_result->fetch_assoc()) {
        $players[] = $row;
    }
}

// Fetch teams for dropdowns (for update scores, etc.)
$teams = [];
$team_result = $conn->query("SELECT id, name, department_id FROM teams ORDER BY name");
if ($team_result) {
    while ($row = $team_result->fetch_assoc()) {
        $teams[] = $row;
    }
}


// Handle success/error messages from processing scripts
$message = $_SESSION['message'] ?? '';
$message_type = $_SESSION['message_type'] ?? ''; // 'success' or 'error'

// Clear session messages after displaying
if (isset($_SESSION['message'])) {
    unset($_SESSION['message']);
    unset($_SESSION['message_type']);
}

// Close connection at the end of the script
// Note: For processing scripts, the connection will be closed after their logic.
// Here, we close it after fetching data for dropdowns.
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Data - EDMS Trophy</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="stylo.css">
</head>
<body>

    <div class="container">
        <h2><i class="fas fa-trophy"></i> Manage EDMS Trophy Data</h2>
        <p>This page allows authorized users to add/update various data points.</p>

        <?php if ($message): ?>
            <div class="message <?php echo $message_type; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <button id="openModalBtn" class="open-modal-button">
            <i class="fas fa-database"></i> Open Data Management Panel
        </button>

        <a href="dashboard.php" class="back-to-dashboard">
            <i class="fas fa-arrow-left"></i> Back to Dashboard
        </a>
    </div>

    <!-- Footer -->
    <div class="footer">
        <div class="footer-brand">
            <i class="fas fa-code"></i>
           <span>Applinc-technologies.</span>
        </div>
        <p>Sports Management System &copy; <?php echo date('Y'); ?></p>
    </div>

    <!-- The Modal -->
    <div id="myModal" class="modal">
        <!-- Modal content -->
        <div class="modal-content">
            <span class="close-button" onclick="closeModal()">&times;</span>
            <h2><i class="fas fa-cogs"></i> Data Management</h2>

            <div class="tab-buttons">
                <?php if ($user_role === 'Admin'): ?>
                    <button class="tab-button" id="tabAddOfficer" onclick="openTab(event, 'AddOfficer')">
                        <i class="fas fa-user-plus"></i> Add Data Officer
                    </button>
                <?php endif; ?>
                <?php if (in_array($user_role, ['Admin', 'Data Officer'])): ?>
                    <button class="tab-button" id="tabAddPlayer" onclick="openTab(event, 'AddPlayer')">
                        <i class="fas fa-user"></i> Add Player
                    </button>
                    <button class="tab-button" id="tabAddTeam" onclick="openTab(event, 'AddTeam')">
                        <i class="fas fa-users"></i> Add Team
                    </button>
                    <button class="tab-button" id="tabAddEvent" onclick="openTab(event, 'AddEvent')">
                        <i class="fas fa-calendar-plus"></i> Add Event
                    </button>
                    <button class="tab-button" id="tabUpdateScores" onclick="openTab(event, 'UpdateScores')">
                        <i class="fas fa-edit"></i> Update Scores
                    </button>
                <?php endif; ?>
            </div>

            <?php if ($user_role === 'Admin'): ?>
            <div id="AddOfficer" class="tab-content">
                <h3><i class="fas fa-user-plus"></i> Add New Data Officer</h3>
                <form action="process_add_data_officer.php" method="POST">
                    <div class="form-group">
                        <label for="officer_full_name"><i class="fas fa-user"></i> Full Name</label>
                        <input type="text" id="officer_full_name" name="full_name" required>
                    </div>
                    <div class="form-group">
                        <label for="officer_employment_number"><i class="fas fa-id-card"></i> Employment Number</label>
                        <input type="text" id="officer_employment_number" name="employment_number" required>
                    </div>
                    <div class="form-group">
                        <label for="officer_email"><i class="fas fa-envelope"></i> Email (Optional)</label>
                        <input type="email" id="officer_email" name="email">
                    </div>
                    <div class="form-group">
                        <label for="officer_phone"><i class="fas fa-phone"></i> Phone Number (Optional)</label>
                        <input type="text" id="officer_phone" name="phone_number">
                    </div>
                    <div class="form-group">
                        <label for="officer_password"><i class="fas fa-lock"></i> Password</label>
                        <input type="password" id="officer_password" name="password" required>
                    </div>
                    <div class="form-group">
                        <label for="officer_confirm_password"><i class="fas fa-lock"></i> Confirm Password</label>
                        <input type="password" id="officer_confirm_password" name="confirm_password" required>
                    </div>
                    <div class="form-actions">
                        <button type="submit"><i class="fas fa-save"></i> Add Data Officer</button>
                    </div>
                </form>
            </div>
            <?php endif; ?>

            <?php if (in_array($user_role, ['Admin', 'Data Officer'])): ?>
            <div id="AddPlayer" class="tab-content">
                <h3><i class="fas fa-user"></i> Add New Player</h3>
                <form action="process_add_player.php" method="POST">
                    <div class="form-group">
                        <label for="player_full_name"><i class="fas fa-user"></i> Full Name</label>
                        <input type="text" id="player_full_name" name="full_name" required>
                    </div>
                    <div class="form-group">
                        <label for="player_dob"><i class="fas fa-calendar"></i> Date of Birth</label>
                        <input type="date" id="player_dob" name="date_of_birth">
                    </div>
                    <div class="form-group">
                        <label for="player_gender"><i class="fas fa-venus-mars"></i> Gender</label>
                        <select id="player_gender" name="gender" required>
                            <option value="">Select Gender</option>
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                           
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="player_phone"><i class="fas fa-phone"></i> Phone Number (Optional)</label>
                        <input type="text" id="player_phone" name="phone_number">
                    </div>
                    <div class="form-group">
                        <label for="player_department"><i class="fas fa-building"></i> Department</label>
                        <select id="player_department" name="department_id" required>
                            <option value="">Select Department</option>
                            <?php foreach ($departments as $dept): ?>
                                <option value="<?php echo htmlspecialchars($dept['id']); ?>">
                                    <?php echo htmlspecialchars($dept['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="player_position"><i class="fas fa-running"></i> Playing Position (Optional)</label>
                        <input type="text" id="player_position" name="playing_position">
                    </div>
                    <div class="form-actions">
                        <button type="submit"><i class="fas fa-save"></i> Add Player</button>
                    </div>
                </form>
            </div>

            <div id="AddTeam" class="tab-content">
                <h3><i class="fas fa-users"></i> Add New Team</h3>
                <form action="process_add_team.php" method="POST">
                    <div class="form-group">
                        <label for="team_name"><i class="fas fa-users"></i> Team Name</label>
                        <input type="text" id="team_name" name="name" required>
                    </div>
                    <div class="form-group">
                        <label for="team_department"><i class="fas fa-building"></i> Department</label>
                        <select id="team_department" name="department_id" required>
                            <option value="">Select Department</option>
                            <?php foreach ($departments as $dept): ?>
                                <option value="<?php echo htmlspecialchars($dept['id']); ?>">
                                    <?php echo htmlspecialchars($dept['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <!-- ADD THIS NEW FIELD FOR RELAY TEAMS -->
                    <div class="form-group">
                        <input type="checkbox" id="is_relay_team" name="is_relay_team" value="1">
                        <label for="is_relay_team"><i class="fas fa-running"></i> This is a Relay Race Team</label>
                    </div>
                    <div class="form-actions">
                        <button type="submit"><i class="fas fa-save"></i> Add Team</button>
                    </div>
                </form>
            </div>

            <div id="AddEvent" class="tab-content">
                <h3><i class="fas fa-calendar-plus"></i> Add New Event</h3>
                <form action="process_add_event.php" method="POST">
                    <div class="form-group">
                        <label for="event_name_input"><i class="fas fa-tag"></i> Event Name</label>
                        <input type="text" id="event_name_input" name="event_name" required>
                    </div>
                    <!-- In the Add Event form, you could add logic to show relay-specific options -->
                    <div class="form-group">
                        <label for="event_sport"><i class="fas fa-baseball-ball"></i> Sport</label>
                        <select id="event_sport" name="sport_id" required onchange="checkIfRelaySport(this)">
                            <option value="">Select Sport</option>
                            <?php foreach ($sports as $sport): ?>
                                <option value="<?php echo htmlspecialchars($sport['id']); ?>" 
                                        data-is-relay="<?php echo $sport['is_relay'] ? '1' : '0'; ?>">
                                    <?php echo htmlspecialchars($sport['name'] . " (" . $sport['type'] . ", " . ($sport['is_individual'] ? "Individual" : "Team") . ")"); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="event_department"><i class="fas fa-building"></i> Department</label>
                        <select id="event_department" name="department_id" required>
                            <option value="">Select Department</option>
                            <?php foreach ($departments as $dept): ?>
                                <option value="<?php echo htmlspecialchars($dept['id']); ?>">
                                    <?php echo htmlspecialchars($dept['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="event_date_input"><i class="fas fa-calendar-day"></i> Event Date</label>
                        <input type="date" id="event_date_input" name="event_date" required>
                    </div>
                    <div class="form-group">
                        <label for="event_location"><i class="fas fa-map-marker-alt"></i> Location (Optional)</label>
                        <input type="text" id="event_location" name="location">
                    </div>
                    <div class="form-actions">
                        <button type="submit"><i class="fas fa-save"></i> Add Event</button>
                    </div>
                </form>
            </div>

            <div id="UpdateScores" class="tab-content">
                <h3><i class="fas fa-edit"></i> Update Player/Team Scores</h3>
                <form action="process_update_scores.php" method="POST">
                    <div class="form-group">
                        <label for="score_event"><i class="fas fa-calendar-check"></i> Event</label>
                        <select id="score_event" name="event_id" required onchange="updateScoreForm()">
                            <option value="">Select Event</option>
                            <?php foreach ($events as $event): ?>
                                <option
                                    value="<?php echo htmlspecialchars($event['id']); ?>"
                                    data-is-individual="<?php echo htmlspecialchars($event['is_individual']); ?>"
                                    data-department-id="<?php echo htmlspecialchars($event['department_id']); ?>">
                                    <?php echo htmlspecialchars($event['event_name'] . " - " . $event['department_name'] . " (" . $event['event_date'] . ")"); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="score_player_id"><i class="fas fa-user"></i> Player</label>
                        <select id="score_player_id" name="player_id" disabled>
                            <option value="">Select Player</option>
                            <?php foreach ($players as $player): ?>
                                <option
                                    value="<?php echo htmlspecialchars($player['id']); ?>"
                                    data-department-id="<?php echo htmlspecialchars($player['department_id']); ?>">
                                    <?php echo htmlspecialchars($player['full_name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="score_team_id"><i class="fas fa-users"></i> Team</label>
                        <select id="score_team_id" name="team_id" disabled>
                            <option value="">Select Team</option>
                            <?php foreach ($teams as $team): ?>
                                <option
                                    value="<?php echo htmlspecialchars($team['id']); ?>"
                                    data-department-id="<?php echo htmlspecialchars($team['department_id']); ?>">
                                    <?php echo htmlspecialchars($team['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="score_value"><i class="fas fa-chart-line"></i> Score/Time/Distance</label>
                        <input type="text" id="score_value" name="score_value" placeholder="e.g., 10.5 (seconds), 5.2 (meters), 3 (goals)" required>
                    </div>
                    <div class="form-group">
                        <label for="score_rank"><i class="fas fa-trophy"></i> Rank (Optional)</label>
                        <input type="number" id="score_rank" name="rank">
                    </div>
                    <div class="form-group">
                        <label for="score_points"><i class="fas fa-star"></i> Points Awarded (Optional)</label>
                        <input type="number" id="score_points" name="points_awarded">
                    </div>
                    <div class="form-group">
                        <input type="checkbox" id="is_final_score" name="is_final" value="1">
                        <label for="is_final_score"><i class="fas fa-flag-checkered"></i> Final Score</label>
                    </div>
                    <div class="form-actions">
                        <button type="submit"><i class="fas fa-paper-plane"></i> Submit Score</button>
                    </div>
                </form>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
        // Get the modal
        var modal = document.getElementById("myModal");
        var openModalBtn = document.getElementById("openModalBtn");
        var closeButton = document.querySelector(".close-button");

        // When the user clicks the button, open the modal
        openModalBtn.onclick = function() {
            modal.style.display = "flex";
            // Open default tab on modal open
            initializeDefaultTab();
        }

        // When the user clicks on <span> (x) or outside the modal, close it
        function closeModal() {
            modal.style.display = "none";
        }
        closeButton.onclick = closeModal;

        window.onclick = function(event) {
            if (event.target == modal) {
                closeModal();
            }
        }

        // Function to open specific tabs within the modal
        function openTab(evt, tabName) {
            var i, tabcontent, tabbuttons;

            // Get all elements with class="tab-content" and hide them
            tabcontent = document.getElementsByClassName("tab-content");
            for (i = 0; i < tabcontent.length; i++) {
                tabcontent[i].style.display = "none";
            }

            // Get all elements with class="tab-button" and remove the class "active"
            tabbuttons = document.getElementsByClassName("tab-button");
            for (i = 0; i < tabbuttons.length; i++) {
                tabbuttons[i].className = tabbuttons[i].className.replace(" active", "");
            }

            // Show the current tab, and add an "active" class to the button that opened the tab
            document.getElementById(tabName).style.display = "block";
            evt.currentTarget.className += " active";

            // If it's the UpdateScores tab, trigger its dynamic logic
            if (tabName === 'UpdateScores') {
                updateScoreForm();
            }
        }

        function initializeDefaultTab() {
            var defaultTabId = '';
            var defaultTabButtonId = '';

            // Determine the default tab based on user role
            <?php if ($user_role === 'Admin'): ?>
                defaultTabId = 'AddOfficer';
                defaultTabButtonId = 'tabAddOfficer';
            <?php elseif (in_array($user_role, ['Data Officer'])): ?>
                defaultTabId = 'AddPlayer';
                defaultTabButtonId = 'tabAddPlayer';
            <?php endif; ?>

            if (defaultTabId && document.getElementById(defaultTabId)) {
                // Manually trigger the tab opening
                var defaultButton = document.getElementById(defaultTabButtonId);
                if (defaultButton) {
                    openTab({currentTarget: defaultButton}, defaultTabId);
                } else {
                    // Fallback if the specific button isn't found
                    document.getElementById(defaultTabId).style.display = 'block';
                    // Find the first available button and make it active
                    var firstAvailableButton = document.querySelector('.tab-buttons .tab-button');
                    if(firstAvailableButton) {
                        firstAvailableButton.classList.add('active');
                    }
                }
            } else {
                 // If no specific default, just make the first visible tab active
                var firstVisibleTabButton = document.querySelector('.tab-buttons .tab-button');
                if (firstVisibleTabButton) {
                    firstVisibleTabButton.click(); // Simulate a click to open it
                }
            }
        }
        
        // --- Dynamic Score Update Form Logic ---
        function updateScoreForm() {
            var eventSelect = document.getElementById('score_event');
            var selectedOption = eventSelect.options[eventSelect.selectedIndex];
            var isIndividual = selectedOption.dataset.isIndividual === '1'; // "1" for true, "0" for false
            var departmentId = selectedOption.dataset.departmentId;

            var playerSelect = document.getElementById('score_player_id');
            var teamSelect = document.getElementById('score_team_id');

            // Reset and disable both initially
            playerSelect.value = '';
            teamSelect.value = '';
            playerSelect.disabled = true;
            teamSelect.disabled = true;
            playerSelect.removeAttribute('required');
            teamSelect.removeAttribute('required');

            // Filter players/teams based on selected event's department
            filterParticipantsByDepartment(departmentId);

            if (eventSelect.value === '') {
                // No event selected, disable everything
                return;
            }

            if (isIndividual) {
                playerSelect.disabled = false;
                playerSelect.setAttribute('required', 'required');
                teamSelect.disabled = true;
            } else { // Team-based event
                playerSelect.disabled = true;
                teamSelect.disabled = false;
                teamSelect.setAttribute('required', 'required');
            }
        }

        function filterParticipantsByDepartment(departmentId) {
            var allPlayers = document.querySelectorAll('#score_player_id option');
            var allTeams = document.querySelectorAll('#score_team_id option');

            allPlayers.forEach(function(option) {
                if (option.value === '') { // Keep "Select Player" option
                    option.style.display = '';
                    return;
                }
                if (option.dataset.departmentId == departmentId) {
                    option.style.display = '';
                } else {
                    option.style.display = 'none';
                }
            });

            allTeams.forEach(function(option) {
                if (option.value === '') { // Keep "Select Team" option
                    option.style.display = '';
                    return;
                }
                if (option.dataset.departmentId == departmentId) {
                    option.style.display = '';
                } else {
                    option.style.display = 'none';
                }
            });
        }
        function checkIfRelaySport(selectElement) {
    const selectedOption = selectElement.options[selectElement.selectedIndex];
    const isRelay = selectedOption.dataset.isRelay === '1';
    
    // You can show/hide relay-specific options here
    if (isRelay) {
        // Show relay-specific instructions or options
        console.log("This is a relay sport");
    }
}

        // Initialize default tab on page load (if modal is opened via a direct link)
        document.addEventListener('DOMContentLoaded', initializeDefaultTab);

    </script>

</body>
</html>
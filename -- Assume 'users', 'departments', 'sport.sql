-- Assume 'users', 'departments', 'sports', 'teams', 'events' tables are already created.
-- If not, create them first as per previous instructions.

-- Drop scores table if it exists and references participants (or players)
DROP TABLE IF EXISTS scores;
-- Drop participants table if it exists, as we are replacing it with 'players'
DROP TABLE IF EXISTS participants;


-- 4. Players Table (Replaces Participants Table)
CREATE TABLE players (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    date_of_birth DATE,
    gender ENUM('Male', 'Female', 'Other'),
    Section ENUM('Primary', 'Secondary'),
    phone_number VARCHAR(20),
    department_id INT NOT NULL, -- Links player to their department/school section
    employment_number VARCHAR(50) UNIQUE, -- If linking to user's login employment number
    playing_position VARCHAR(50), -- e.g., 'Striker', 'Goalkeeper', 'Center'
    FOREIGN KEY (department_id) REFERENCES departments(id) ON DELETE CASCADE
    -- If you want to link to the users table's employment_number for actual users:
    -- FOREIGN KEY (employment_number) REFERENCES users(employment_number) ON DELETE SET NULL
);


-- 6. Scores Table (Updated to reference players.id instead of participants.id)
CREATE TABLE scores (
    id INT AUTO_INCREMENT PRIMARY KEY,
    event_id INT NOT NULL,
    team_id INT, -- Nullable for individual sports
    player_id INT, -- NEW: References players.id for individual sports/participants
    score_value DECIMAL(10,2) NOT NULL,
    `rank` INT, -- Rank within this specific event (backticks are important!)
    recorded_by_user_id INT NOT NULL, -- The user who entered/updated the score
    recorded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    is_final BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE,
    FOREIGN KEY (team_id) REFERENCES teams(id) ON DELETE SET NULL,
    FOREIGN KEY (player_id) REFERENCES players(id) ON DELETE SET NULL, -- NEW Foreign Key
    FOREIGN KEY (recorded_by_user_id) REFERENCES users(id) ON DELETE RESTRICT
);
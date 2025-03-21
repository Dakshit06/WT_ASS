<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database configuration
$servername = "127.0.0.1";
$port = 3306; // Changed from 3307 to default MySQL port
$username = "root";
$password = "";
$dbname = "mentorship_db";

try {
    // Test if MySQL service is running first
    $connection_test = @mysqli_connect($servername, $username, $password, '', $port);
    if (!$connection_test) {
        throw new Exception("Unable to connect to MySQL. Please verify:<br>
            1. XAMPP/MySQL service is running<br>
            2. MySQL is using port $port (check xampp/mysql/bin/my.ini)<br>
            3. No firewall is blocking MySQL<br>
            Error: " . mysqli_connect_error());
    }
    mysqli_close($connection_test);
    
    // Create connection with timeout setting
    $conn = new mysqli();
    $conn->options(MYSQLI_OPT_CONNECT_TIMEOUT, 5);
    $conn->real_connect($servername, $username, $password, "", $port);
    
    // Enable error reporting for MySQL
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    
    // Check connection
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }
    
    // Create database if it doesn't exist
    $create_db = "CREATE DATABASE IF NOT EXISTS $dbname";
    if (!$conn->query($create_db)) {
        throw new Exception("Error creating database: " . $conn->error);
    }
    
    // Select the database
    $conn->select_db($dbname);
    
    // Array of table creation queries
    $tables = array();
    
    // Users table
    $tables[] = "CREATE TABLE IF NOT EXISTS users (
        id INT(11) AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        email VARCHAR(100) UNIQUE NOT NULL,
        password VARCHAR(255) NOT NULL,
        role ENUM('admin', 'mentor', 'mentee') NOT NULL,
        skills TEXT NOT NULL,
        department VARCHAR(50) NOT NULL,
        availability VARCHAR(50) NOT NULL,
        profile_image VARCHAR(255) DEFAULT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    
    // Meetings table
    $tables[] = "CREATE TABLE IF NOT EXISTS meetings (
        id INT(11) AUTO_INCREMENT PRIMARY KEY,
        mentor_id INT(11) NOT NULL,
        mentee_id INT(11) NOT NULL,
        title VARCHAR(255) NOT NULL,
        description TEXT,
        meeting_date DATETIME NOT NULL,
        status ENUM('scheduled', 'completed', 'cancelled') DEFAULT 'scheduled',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (mentor_id) REFERENCES users(id),
        FOREIGN KEY (mentee_id) REFERENCES users(id)
    )";
    
    // Matches table
    $tables[] = "CREATE TABLE IF NOT EXISTS matches (
        id INT(11) AUTO_INCREMENT PRIMARY KEY,
        mentor_id INT(11) NOT NULL,
        mentee_id INT(11) NOT NULL,
        status ENUM('active', 'inactive') DEFAULT 'active',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (mentor_id) REFERENCES users(id),
        FOREIGN KEY (mentee_id) REFERENCES users(id)
    )";
    
    // Add new tables
    $tables[] = "CREATE TABLE IF NOT EXISTS certifications (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        title VARCHAR(255) NOT NULL,
        issuer VARCHAR(255) NOT NULL,
        issue_date DATE NOT NULL,
        expiry_date DATE,
        file_path VARCHAR(255),
        verified BOOLEAN DEFAULT false,
        FOREIGN KEY (user_id) REFERENCES users(id)
    )";

    $tables[] = "CREATE TABLE IF NOT EXISTS learning_paths (
        id INT AUTO_INCREMENT PRIMARY KEY,
        mentor_id INT NOT NULL,
        mentee_id INT NOT NULL,
        title VARCHAR(255) NOT NULL,
        description TEXT,
        objectives JSON,
        duration VARCHAR(50),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (mentor_id) REFERENCES users(id),
        FOREIGN KEY (mentee_id) REFERENCES users(id)
    )";

    $tables[] = "CREATE TABLE IF NOT EXISTS learning_resources (
        id INT AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(255) NOT NULL,
        description TEXT,
        type ENUM('document', 'video', 'link') NOT NULL,
        file_path VARCHAR(255),
        access_level ENUM('public', 'private', 'restricted') DEFAULT 'public',
        created_by INT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (created_by) REFERENCES users(id)
    )";

    $tables[] = "CREATE TABLE IF NOT EXISTS achievements (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        title VARCHAR(255) NOT NULL,
        description TEXT,
        points INT DEFAULT 0,
        badge_icon VARCHAR(255),
        earned_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id)
    )";

    // Add feedback table
    $tables[] = "CREATE TABLE IF NOT EXISTS feedback (
        id INT AUTO_INCREMENT PRIMARY KEY,
        mentor_id INT NOT NULL,
        mentee_id INT NOT NULL,
        rating INT NOT NULL CHECK (rating BETWEEN 1 AND 5),
        feedback TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (mentor_id) REFERENCES users(id) ON DELETE CASCADE,
        FOREIGN KEY (mentee_id) REFERENCES users(id) ON DELETE CASCADE
    )";
    
    // Execute table creation queries
    foreach ($tables as $query) {
        if (!$conn->query($query)) {
            throw new Exception("Error creating table: " . $conn->error);
        }
    }
    
    // Insert sample data if tables are empty
    $result = $conn->query("SELECT COUNT(*) as count FROM users");
    $count = $result->fetch_assoc()['count'];
    
    if ($count == 0) {
        // Sample mentors
        $sample_mentors = [
            [
                'Amit Kumar',
                'amit@test.com',
                'mentor',
                'PHP,JavaScript,Python',
                'IT',
                'https://img.freepik.com/free-photo/young-indian-businessman-formal-wear-standing-with-his-arms-crossed_1340-586.jpg'
            ],
            [
                'Priya Shah',
                'priya@test.com',
                'mentor',
                'HR Management,Leadership',
                'HR',
                'https://img.freepik.com/free-photo/young-indian-woman-professional-executive-portrait_23-2148834713.jpg'
            ],
            [
                'Rajesh Patel',
                'rajesh@test.com',
                'mentor',
                'Marketing Strategy,Digital Marketing',
                'Marketing',
                'https://img.freepik.com/free-photo/indian-businessman-manager-portrait_23-2147654359.jpg'
            ]
        ];
        
        // Sample mentees with profile images
        $sample_mentees = [
            [
                'Neha Singh',
                'neha@test.com',
                'mentee',
                'Web Development,React',
                'IT',
                'https://img.freepik.com/free-photo/young-indian-woman-professional_23-2147654374.jpg'
            ],
            [
                'Suresh Kumar',
                'suresh@test.com',
                'mentee',
                'Project Management',
                'IT',
                'https://img.freepik.com/free-photo/young-indian-professional-man_23-2147654362.jpg'
            ],
            [
                'Meera Reddy',
                'meera@test.com',
                'mentee',
                'Content Marketing',
                'Marketing',
                'https://img.freepik.com/free-photo/indian-business-woman-portrait_23-2147654343.jpg'
            ]
        ];
        
        $password = password_hash('test123', PASSWORD_DEFAULT);
        
        foreach (array_merge($sample_mentors, $sample_mentees) as $user) {
            $stmt = $conn->prepare("INSERT INTO users (name, email, password, role, skills, department, availability, profile_image) 
                                  VALUES (?, ?, ?, ?, ?, ?, 'full-time', ?)");
            $stmt->bind_param("sssssss", $user[0], $user[1], $password, $user[2], $user[3], $user[4], $user[5]);
            $stmt->execute();
            
            if ($user[2] === 'mentor') {
                $mentor_id = $conn->insert_id;
                // Create sample matches and meetings for each mentor
                $result = $conn->query("SELECT id FROM users WHERE role = 'mentee' LIMIT 1");
                if ($mentee = $result->fetch_assoc()) {
                    // Create match
                    $conn->query("INSERT INTO matches (mentor_id, mentee_id, status) 
                                VALUES ($mentor_id, {$mentee['id']}, 'active')");
                    
                    // Create meeting
                    $future_date = date('Y-m-d H:i:s', strtotime('+1 week'));
                    $conn->query("INSERT INTO meetings (mentor_id, mentee_id, title, description, meeting_date) 
                                VALUES ($mentor_id, {$mentee['id']}, 'Initial Meeting', 'First mentorship session', '$future_date')");
                }
            }
        }
    }
    
    // Add connection status check
    if (isset($conn) && $conn->ping()) {
        // Connection is good, add to error log
        error_log("Database connection established successfully");
    } else {
        error_log("Database connection failed: " . ($conn->error ?? "Unknown error"));
    }
    
} catch (Exception $e) {
    die("<div style='margin:20px; padding:20px; border:1px solid #dc3545; border-radius:5px; color:#dc3545;'>
        <h4>Database Connection Error</h4>
        <p>" . $e->getMessage() . "</p>
        <hr>
        <h5>Troubleshooting Steps:</h5>
        <ol>
            <li>Open XAMPP Control Panel</li>
            <li>Click 'Start' next to MySQL</li>
            <li>Check MySQL port in xampp/mysql/bin/my.ini</li>
            <li>Default port should be 3306</li>
            <li>Restart MySQL service if you made changes</li>
        </ol>
        </div>");
}
?>

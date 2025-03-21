<?php
session_start();
include 'config/db.php';

if (isset($_POST['query'])) {
    $query = base64_decode($_POST['query']);
    // Whitelist allowed queries for security
    $allowed_queries = [
        'SELECT COUNT(*) FROM users WHERE role = "mentor"',
        'SELECT COUNT(*) FROM users WHERE role = "mentee"',
        'SELECT COUNT(*) FROM meetings WHERE status = "scheduled"',
        'SELECT COUNT(*) FROM matches WHERE status = "active"'
    ];
    
    if (in_array($query, $allowed_queries)) {
        $result = $conn->query($query);
        echo $result->fetch_row()[0];
    }
}
?>

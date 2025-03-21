<?php
session_start();
include 'config/db.php';
include 'includes/security.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        Security::verifyCSRFToken($_POST['csrf_token']);
        
        $mentor_id = (int)$_POST['mentor_id'];
        $mentee_id = (int)$_POST['mentee_id'];
        $rating = (int)$_POST['rating'];
        $feedback = Security::sanitizeInput($_POST['feedback']);
        
        $stmt = $conn->prepare("INSERT INTO feedback (mentor_id, mentee_id, rating, feedback) 
                               VALUES (?, ?, ?, ?)");
        $stmt->bind_param("iiis", $mentor_id, $mentee_id, $rating, $feedback);
        
        if ($stmt->execute()) {
            // Update mentor's rating
            $conn->query("UPDATE users SET 
                         rating = (SELECT AVG(rating) FROM feedback WHERE mentor_id = $mentor_id) 
                         WHERE id = $mentor_id");
            
            echo json_encode(['success' => true]);
        }
    } catch (Exception $e) {
        http_response_code(400);
        echo json_encode(['error' => $e->getMessage()]);
    }
}

<?php
class Notifications {
    private $conn;
    
    public function __construct($conn) {
        $this->conn = $conn;
    }
    
    public function createNotification($user_id, $type, $message) {
        $stmt = $this->conn->prepare("INSERT INTO notifications (user_id, type, message) VALUES (?, ?, ?)");
        $stmt->bind_param("iss", $user_id, $type, $message);
        return $stmt->execute();
    }
    
    public function getUnreadCount($user_id) {
        $stmt = $this->conn->prepare("SELECT COUNT(*) FROM notifications WHERE user_id = ? AND is_read = 0");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_row()[0];
    }
}

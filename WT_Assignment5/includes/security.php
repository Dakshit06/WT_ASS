<?php
class Security {
    public static function sanitizeInput($data) {
        return htmlspecialchars(strip_tags(trim($data)));
    }
    
    public static function validateFileUpload($file, $allowed_types = ['jpg', 'jpeg', 'png', 'pdf']) {
        $filename = $file['name'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        
        if (!in_array($ext, $allowed_types)) {
            throw new Exception("File type not allowed");
        }
        
        if ($file['size'] > 5242880) { // 5MB
            throw new Exception("File size too large");
        }
        
        return true;
    }
    
    public static function generateCSRFToken() {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }
    
    public static function verifyCSRFToken($token) {
        if (!isset($_SESSION['csrf_token']) || $token !== $_SESSION['csrf_token']) {
            throw new Exception("Invalid CSRF token");
        }
        return true;
    }
}

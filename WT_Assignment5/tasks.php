<?php
session_start();
include 'config/db.php';
include 'includes/header.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$role = $_SESSION['role'];
$user_id = $_SESSION['user_id'];

// Handle task operations (create, update, delete)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'create':
                $stmt = $conn->prepare("INSERT INTO tasks (mentor_id, mentee_id, title, description, due_date, priority) 
                                      VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("iissss", $_SESSION['user_id'], $_POST['mentee_id'], 
                                $_POST['title'], $_POST['description'], $_POST['due_date'], $_POST['priority']);
                $stmt->execute();
                break;
                
            case 'update_status':
                $stmt = $conn->prepare("UPDATE tasks SET status = ? WHERE id = ?");
                $stmt->bind_param("si", $_POST['status'], $_POST['task_id']);
                $stmt->execute();
                break;
        }
    }
}

// Fetch tasks based on role
$tasks_sql = $role === 'mentor' 
    ? "SELECT t.*, u.name as mentee_name FROM tasks t JOIN users u ON t.mentee_id = u.id WHERE t.mentor_id = ?"
    : "SELECT t.*, u.name as mentor_name FROM tasks t JOIN users u ON t.mentor_id = u.id WHERE t.mentee_id = ?";

$stmt = $conn->prepare($tasks_sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$tasks = $stmt->get_result();
?>

<div class="container py-5">
    <!-- Task management interface implementation -->
</div>

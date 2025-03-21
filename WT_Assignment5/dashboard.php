<?php
session_start();
include 'config/db.php';
include 'includes/header.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'];

// Get user stats based on role
if ($role === 'mentee') {
    $stats = $conn->prepare("
        SELECT 
            COUNT(CASE WHEN t.status = 'completed' THEN 1 END) as completed_tasks,
            COUNT(CASE WHEN t.status = 'in_progress' THEN 1 END) as ongoing_tasks,
            COUNT(CASE WHEN g.status = 'completed' THEN 1 END) as achieved_goals,
            COUNT(m.id) as total_meetings
        FROM users u 
        LEFT JOIN tasks t ON u.id = t.mentee_id
        LEFT JOIN goals g ON u.id = g.mentee_id
        LEFT JOIN meetings m ON u.id = m.mentee_id
        WHERE u.id = ?
    ");
} else if ($role === 'mentor') {
    $stats = $conn->prepare("
        SELECT 
            COUNT(DISTINCT m.mentee_id) as active_mentees,
            COUNT(t.id) as assigned_tasks,
            COUNT(CASE WHEN t.status = 'completed' THEN 1 END) as completed_tasks,
            COUNT(me.id) as scheduled_meetings
        FROM users u 
        LEFT JOIN matches m ON u.id = m.mentor_id
        LEFT JOIN tasks t ON u.id = t.mentor_id
        LEFT JOIN meetings me ON u.id = me.mentor_id
        WHERE u.id = ? AND m.status = 'active'
    ");
}

$stats->bind_param("i", $user_id);
$stats->execute();
$stats_result = $stats->get_result()->fetch_assoc();
?>

<div class="container py-5">
    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Progress Overview</h5>
                </div>
                <div class="card-body">
                    <canvas id="progressChart"></canvas>
                </div>
            </div>
            
            <!-- Add more dashboard sections -->
        </div>
        
        <div class="col-lg-4">
            <!-- Add sidebar widgets -->
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Add charts and analytics
</script>

<?php include 'includes/footer.php'; ?>

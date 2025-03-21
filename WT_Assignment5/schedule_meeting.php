<?php
session_start();
include 'config/db.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $mentor_id = (int)$_POST['mentor_id'];
    $mentee_id = (int)$_POST['mentee_id'];
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $meeting_date = mysqli_real_escape_string($conn, $_POST['meeting_date']);

    $sql = "INSERT INTO meetings (mentor_id, mentee_id, title, description, meeting_date) 
            VALUES (?, ?, ?, ?, ?)";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iisss", $mentor_id, $mentee_id, $title, $description, $meeting_date);
    
    if ($stmt->execute()) {
        $_SESSION['success'] = "Meeting scheduled successfully";
        header("Location: index.php");
        exit;
    }
}

// Get list of mentors and mentees
$mentors = $conn->query("SELECT id, name FROM users WHERE role = 'mentor'");
$mentees = $conn->query("SELECT id, name FROM users WHERE role = 'mentee'");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Schedule Meeting - Mentee Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h3 class="mb-0">Schedule Meeting</h3>
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <div class="mb-3">
                                <label class="form-label">Mentor</label>
                                <select name="mentor_id" class="form-select" required>
                                    <?php while($mentor = $mentors->fetch_assoc()): ?>
                                        <option value="<?php echo $mentor['id']; ?>">
                                            <?php echo htmlspecialchars($mentor['name']); ?>
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Mentee</label>
                                <select name="mentee_id" class="form-select" required>
                                    <?php while($mentee = $mentees->fetch_assoc()): ?>
                                        <option value="<?php echo $mentee['id']; ?>">
                                            <?php echo htmlspecialchars($mentee['name']); ?>
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Title</label>
                                <input type="text" name="title" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Description</label>
                                <textarea name="description" class="form-control" rows="3"></textarea>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Meeting Date & Time</label>
                                <input type="datetime-local" name="meeting_date" class="form-control" required>
                            </div>
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary">Schedule Meeting</button>
                                <a href="index.php" class="btn btn-secondary">Back</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

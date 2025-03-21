<?php
session_start();
include 'config/db.php';
$pageTitle = 'Find Mentors - Mentee Management System';
include 'includes/header.php';

// Handle filters
$department = isset($_GET['department']) ? mysqli_real_escape_string($conn, $_GET['department']) : '';
$skills = isset($_GET['skills']) ? mysqli_real_escape_string($conn, $_GET['skills']) : '';
$availability = isset($_GET['availability']) ? mysqli_real_escape_string($conn, $_GET['availability']) : '';

// Build query
$where = ["role = 'mentor'"];
if ($department) $where[] = "department = '$department'";
if ($skills) $where[] = "skills LIKE '%$skills%'";
if ($availability) $where[] = "availability = '$availability'";

$sql = "SELECT * FROM users WHERE " . implode(' AND ', $where);
$mentors = $conn->query($sql);

// Get unique departments for filter
$departments = $conn->query("SELECT DISTINCT department FROM users WHERE role = 'mentor'");
?>

<div class="container py-5">
    <!-- Search and Filters -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Department</label>
                    <select name="department" class="form-select">
                        <option value="">All Departments</option>
                        <?php while($dept = $departments->fetch_assoc()): ?>
                            <option value="<?php echo htmlspecialchars($dept['department']); ?>" 
                                    <?php echo $department === $dept['department'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($dept['department']); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Skills</label>
                    <input type="text" name="skills" class="form-control" 
                           value="<?php echo htmlspecialchars($skills); ?>" 
                           placeholder="Enter skills...">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Availability</label>
                    <select name="availability" class="form-select">
                        <option value="">Any Availability</option>
                        <option value="full-time" <?php echo $availability === 'full-time' ? 'selected' : ''; ?>>Full Time</option>
                        <option value="part-time" <?php echo $availability === 'part-time' ? 'selected' : ''; ?>>Part Time</option>
                        <option value="weekends" <?php echo $availability === 'weekends' ? 'selected' : ''; ?>>Weekends Only</option>
                    </select>
                </div>
                <div class="col-12">
                    <button type="submit" class="btn btn-primary">Filter</button>
                    <a href="mentors.php" class="btn btn-secondary">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Mentors Grid -->
    <div class="row g-4">
        <?php if ($mentors->num_rows > 0): ?>
            <?php while($mentor = $mentors->fetch_assoc()): ?>
                <div class="col-md-3">
                    <div class="card h-100 shadow-sm mentor-card">
                        <img src="<?php echo $mentor['profile_image'] ?? 'https://img.freepik.com/free-photo/professional-profile_23-2147654319.jpg'; ?>" 
                             class="card-img-top" 
                             alt="<?php echo htmlspecialchars($mentor['name']); ?>"
                             onerror="this.src='https://img.freepik.com/free-photo/professional-profile_23-2147654319.jpg'">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($mentor['name']); ?></h5>
                            <p class="card-text text-muted"><?php echo htmlspecialchars($mentor['department']); ?></p>
                            <p class="small text-muted mb-2">
                                <i class="bi bi-clock"></i> <?php echo ucfirst($mentor['availability']); ?>
                            </p>
                            <div class="mb-3">
                                <?php foreach(array_slice(explode(',', $mentor['skills']), 0, 3) as $skill): ?>
                                    <span class="skill-badge"><?php echo htmlspecialchars(trim($skill)); ?></span>
                                <?php endforeach; ?>
                            </div>
                            <div class="d-grid">
                                <a href="view.php?id=<?php echo $mentor['id']; ?>" 
                                   class="btn btn-outline-primary">View Profile</a>
                                <?php if (isset($_SESSION['user_id']) && $_SESSION['role'] === 'mentee'): ?>
                                    <a href="schedule_meeting.php?mentor_id=<?php echo $mentor['id']; ?>" 
                                       class="btn btn-primary mt-2">Schedule Meeting</a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="col-12 text-center">
                <p class="text-muted">No mentors found matching your criteria.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>

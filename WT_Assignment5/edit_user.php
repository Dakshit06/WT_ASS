<?php
session_start();
include 'config/db.php';

// Check admin access
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: index.php');
    exit;
}

$id = (int)$_GET['id'];
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

if (!$user) {
    header('Location: manage_users.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $role = mysqli_real_escape_string($conn, $_POST['role']);
    $skills = mysqli_real_escape_string($conn, $_POST['skills']);
    $department = mysqli_real_escape_string($conn, $_POST['department']);
    $availability = mysqli_real_escape_string($conn, $_POST['availability']);

    // Handle image upload
    $profile_image = $user['profile_image'];
    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $filename = $_FILES['profile_image']['name'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        
        if (in_array($ext, $allowed)) {
            $new_filename = uniqid() . "." . $ext;
            $target_file = "uploads/" . $new_filename;
            
            if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $target_file)) {
                // Delete old image if exists
                if ($profile_image && file_exists($profile_image)) {
                    unlink($profile_image);
                }
                $profile_image = $target_file;
            }
        }
    }

    $sql = "UPDATE users SET name=?, email=?, role=?, skills=?, department=?, availability=?, profile_image=? WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssssi", $name, $email, $role, $skills, $department, $availability, $profile_image, $id);
    
    if ($stmt->execute()) {
        $_SESSION['success'] = "User updated successfully";
        header('Location: manage_users.php');
        exit;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit User - <?php echo htmlspecialchars($user['name']); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                        <h3 class="mb-0">Edit User</h3>
                        <a href="manage_users.php" class="btn btn-light btn-sm">Back to List</a>
                    </div>
                    <div class="card-body">
                        <form method="POST" enctype="multipart/form-data">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Name</label>
                                    <input type="text" name="name" class="form-control" 
                                           value="<?php echo htmlspecialchars($user['name']); ?>" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Email</label>
                                    <input type="email" name="email" class="form-control" 
                                           value="<?php echo htmlspecialchars($user['email']); ?>" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Role</label>
                                    <select name="role" class="form-select" required>
                                        <option value="admin" <?php echo $user['role'] === 'admin' ? 'selected' : ''; ?>>Admin</option>
                                        <option value="mentor" <?php echo $user['role'] === 'mentor' ? 'selected' : ''; ?>>Mentor</option>
                                        <option value="mentee" <?php echo $user['role'] === 'mentee' ? 'selected' : ''; ?>>Mentee</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Department</label>
                                    <select name="department" class="form-select" required>
                                        <?php
                                        $departments = ['IT', 'HR', 'Marketing', 'Finance'];
                                        foreach ($departments as $dept):
                                        ?>
                                            <option value="<?php echo $dept; ?>" 
                                                    <?php echo $user['department'] === $dept ? 'selected' : ''; ?>>
                                                <?php echo $dept; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Skills</label>
                                    <input type="text" name="skills" class="form-control" 
                                           value="<?php echo htmlspecialchars($user['skills']); ?>" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Availability</label>
                                    <select name="availability" class="form-select" required>
                                        <option value="full-time" <?php echo $user['availability'] === 'full-time' ? 'selected' : ''; ?>>Full Time</option>
                                        <option value="part-time" <?php echo $user['availability'] === 'part-time' ? 'selected' : ''; ?>>Part Time</option>
                                        <option value="weekends" <?php echo $user['availability'] === 'weekends' ? 'selected' : ''; ?>>Weekends Only</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Profile Image</label>
                                    <input type="file" name="profile_image" class="form-control" accept="image/*">
                                </div>
                                <?php if ($user['profile_image']): ?>
                                    <div class="col-12">
                                        <img src="<?php echo htmlspecialchars($user['profile_image']); ?>" 
                                             class="rounded" style="max-height: 100px" alt="Current profile image">
                                    </div>
                                <?php endif; ?>
                                <div class="col-12">
                                    <button type="submit" class="btn btn-primary">Update User</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

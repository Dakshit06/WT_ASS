<?php
session_start();
include 'config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    
    // Check if email already exists
    $check_email = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $check_email->bind_param("s", $email);
    $check_email->execute();
    $result = $check_email->get_result();
    
    if ($result->num_rows > 0) {
        $error = "Email address is already registered. Please use a different email or login.";
    } else {
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $role = mysqli_real_escape_string($conn, $_POST['role']);
        $skills = mysqli_real_escape_string($conn, $_POST['skills']);
        $department = mysqli_real_escape_string($conn, $_POST['department']);
        $availability = mysqli_real_escape_string($conn, $_POST['availability']);
        
        // Initialize profile_image as null
        $profile_image = null;
        
        // Handle image upload
        if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] == 0) {
            $allowed = ['jpg', 'jpeg', 'png', 'gif'];
            $filename = $_FILES['profile_image']['name'];
            $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
            
            if (in_array($ext, $allowed)) {
                $target_dir = "uploads/";
                if (!file_exists($target_dir)) {
                    mkdir($target_dir, 0777, true);
                }
                
                $new_filename = uniqid() . "." . $ext;
                $target_file = $target_dir . $new_filename;
                
                if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $target_file)) {
                    $profile_image = $target_file;
                }
            }
        }

        try {
            $stmt = $conn->prepare("INSERT INTO users (name, email, password, role, skills, department, availability, profile_image) 
                                  VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            
            if ($stmt === false) {
                throw new Exception("Error preparing statement: " . $conn->error);
            }

            if (!$stmt->bind_param("ssssssss", $name, $email, $password, $role, $skills, $department, $availability, $profile_image)) {
                throw new Exception("Error binding parameters: " . $stmt->error);
            }

            if (!$stmt->execute()) {
                throw new Exception($stmt->error);
            }

            $_SESSION['success'] = "Registration successful. Please login.";
            header("Location: login.php");
            exit;
            
        } catch (Exception $e) {
            $error = "Registration failed: " . $e->getMessage();
        } finally {
            if (isset($stmt)) {
                $stmt->close();
            }
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Register - Mentee Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h3 class="mb-0">Register</h3>
                    </div>
                    <div class="card-body">
                        <?php if (isset($error)): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <?php echo $error; ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        <?php endif; ?>
                        
                        <form method="POST" class="needs-validation" enctype="multipart/form-data" novalidate>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Name</label>
                                    <input type="text" name="name" class="form-control" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Email</label>
                                    <input type="email" name="email" class="form-control" required 
                                           pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$">
                                    <div class="invalid-feedback">
                                        Please enter a valid email address
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Password</label>
                                    <input type="password" name="password" class="form-control" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Role</label>
                                    <select name="role" class="form-select" required>
                                        <option value="mentor">Mentor</option>
                                        <option value="mentee">Mentee</option>
                                    </select>
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Skills</label>
                                    <input type="text" name="skills" class="form-control" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Department</label>
                                    <select name="department" class="form-select" required>
                                        <option value="IT">Information Technology</option>
                                        <option value="HR">Human Resources</option>
                                        <option value="Marketing">Marketing</option>
                                        <option value="Finance">Finance</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Availability</label>
                                    <select name="availability" class="form-select" required>
                                        <option value="full-time">Full Time</option>
                                        <option value="part-time">Part Time</option>
                                        <option value="weekends">Weekends Only</option>
                                    </select>
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Profile Image</label>
                                    <input type="file" name="profile_image" class="form-control" accept="image/*">
                                    <div class="form-text">Allowed formats: JPG, JPEG, PNG, GIF</div>
                                </div>
                                <div class="col-12">
                                    <button type="submit" class="btn btn-primary">Register</button>
                                    <a href="login.php" class="btn btn-link">Already have an account?</a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
    // Add client-side email validation
    document.querySelector('form').addEventListener('submit', function(event) {
        const emailInput = document.querySelector('input[type="email"]');
        const emailPattern = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
        
        if (!emailPattern.test(emailInput.value)) {
            event.preventDefault();
            emailInput.classList.add('is-invalid');
        }
    });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

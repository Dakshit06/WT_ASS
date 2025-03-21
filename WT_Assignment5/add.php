<?php
include 'config/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $role = mysqli_real_escape_string($conn, $_POST['role']);
    $skills = mysqli_real_escape_string($conn, $_POST['skills']);
    $department = mysqli_real_escape_string($conn, $_POST['department']);
    $availability = mysqli_real_escape_string($conn, $_POST['availability']);
    
    $sql = "INSERT INTO users (name, email, role, skills, department, availability) 
            VALUES ('$name', '$email', '$role', '$skills', '$department', '$availability')";
    
    if ($conn->query($sql) === TRUE) {
        header("Location: index.php");
    } else {
        $error = "Error: " . $conn->error;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Add New User</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container py-5">
        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                <h2 class="mb-0">Add New User</h2>
            </div>
            <div class="card-body">
                <?php if (isset($error)) { ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php } ?>
                <form method="POST" class="needs-validation" novalidate>
                    <div class="mb-3">
                        <label class="form-label">Full Name:</label>
                        <input type="text" class="form-control" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email:</label>
                        <input type="email" class="form-control" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Role:</label>
                        <select class="form-select" name="role" required>
                            <option value="">Select Role</option>
                            <option value="mentor">Mentor</option>
                            <option value="mentee">Mentee</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Skills:</label>
                        <input type="text" class="form-control" name="skills" required>
                        <div class="form-text">Enter skills separated by commas</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Department:</label>
                        <select class="form-select" name="department" required>
                            <option value="">Select Department</option>
                            <option value="IT">Information Technology</option>
                            <option value="HR">Human Resources</option>
                            <option value="Marketing">Marketing</option>
                            <option value="Finance">Finance</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Availability:</label>
                        <select class="form-select" name="availability" required>
                            <option value="full-time">Full Time</option>
                            <option value="part-time">Part Time</option>
                            <option value="weekends">Weekends Only</option>
                        </select>
                    </div>
                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <a href="index.php" class="btn btn-secondary me-md-2">Back</a>
                        <button type="submit" class="btn btn-primary">Save User</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Form validation
        (function () {
            'use strict'
            var forms = document.querySelectorAll('.needs-validation')
            Array.prototype.slice.call(forms).forEach(function (form) {
                form.addEventListener('submit', function (event) {
                    if (!form.checkValidity()) {
                        event.preventDefault()
                        event.stopPropagation()
                    }
                    form.classList.add('was-validated')
                }, false)
            })
        })()
    </script>
</body>
</html>

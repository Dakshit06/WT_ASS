<?php
include 'config/db.php';

$id = (int)$_GET['id'];
$sql = "SELECT * FROM manatees WHERE id=$id";
$result = $conn->query($sql);
$manatee = $result->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $age = (int)$_POST['age'];
    $weight = (float)$_POST['weight'];
    $gender = mysqli_real_escape_string($conn, $_POST['gender']);
    $location = mysqli_real_escape_string($conn, $_POST['location']);
    
    $sql = "UPDATE manatees SET name='$name', age='$age', weight='$weight', gender='$gender', location='$location' WHERE id=$id";
    
    if ($conn->query($sql) === TRUE) {
        header("Location: index.php");
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Edit Manatee</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <h1>Edit Manatee</h1>
        <form method="POST">
            <div class="form-group">
                <label>Name:</label>
                <input type="text" name="name" value="<?php echo $manatee['name']; ?>" required>
            </div>
            <div class="form-group">
                <label>Age:</label>
                <input type="number" name="age" value="<?php echo $manatee['age']; ?>" required>
            </div>
            <div class="form-group">
                <label>Weight (kg):</label>
                <input type="number" step="0.1" name="weight" value="<?php echo htmlspecialchars($manatee['weight']); ?>" required>
            </div>
            <div class="form-group">
                <label>Gender:</label>
                <select name="gender" required>
                    <option value="Male" <?php echo $manatee['gender'] == 'Male' ? 'selected' : ''; ?>>Male</option>
                    <option value="Female" <?php echo $manatee['gender'] == 'Female' ? 'selected' : ''; ?>>Female</option>
                    <option value="Unknown" <?php echo $manatee['gender'] == 'Unknown' ? 'selected' : ''; ?>>Unknown</option>
                </select>
            </div>
            <div class="form-group">
                <label>Location:</label>
                <input type="text" name="location" value="<?php echo $manatee['location']; ?>" required>
            </div>
            <button type="submit" class="btn">Update</button>
            <a href="index.php" class="btn">Back</a>
        </form>
    </div>
</body>
</html>

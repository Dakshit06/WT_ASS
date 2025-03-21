<?php
session_start();
include 'config/db.php';

$id = (int)$_GET['id'];
$sql = "SELECT * FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    header('Location: index.php');
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>View Profile - <?php echo htmlspecialchars($user['name']); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container py-5">
        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                <h3 class="mb-0">User Profile</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 text-center mb-4">
                        <?php if ($user['profile_image']): ?>
                            <img src="<?php echo htmlspecialchars($user['profile_image']); ?>" 
                                 class="img-fluid rounded-circle mb-3" style="max-width: 200px;">
                        <?php else: ?>
                            <img src="https://img.freepik.com/free-photo/professional-profile_23-2147654319.jpg" 
                                 class="img-fluid rounded-circle mb-3" style="max-width: 200px;">
                        <?php endif; ?>
                    </div>
                    <div class="col-md-8">
                        <h4><?php echo htmlspecialchars($user['name']); ?></h4>
                        <p class="badge bg-<?php echo $user['role'] === 'mentor' ? 'primary' : 'success'; ?>">
                            <?php echo ucfirst($user['role']); ?>
                        </p>
                        <hr>
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
                                <p><strong>Department:</strong> <?php echo htmlspecialchars($user['department']); ?></p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Availability:</strong> <?php echo htmlspecialchars($user['availability']); ?></p>
                                <p><strong>Member Since:</strong> <?php echo date('F j, Y', strtotime($user['created_at'])); ?></p>
                            </div>
                        </div>
                        <div class="mt-3">
                            <h5>Skills</h5>
                            <?php
                            $skills = explode(',', $user['skills']);
                            foreach ($skills as $skill): ?>
                                <span class="badge bg-secondary me-1"><?php echo htmlspecialchars(trim($skill)); ?></span>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                <div class="mt-4 text-end">
                    <a href="index.php" class="btn btn-secondary">Back</a>
                    <?php if (isset($_SESSION['user_id']) && ($_SESSION['user_id'] == $user['id'] || $_SESSION['role'] == 'admin')): ?>
                        <a href="edit.php?id=<?php echo $user['id']; ?>" class="btn btn-primary">Edit Profile</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

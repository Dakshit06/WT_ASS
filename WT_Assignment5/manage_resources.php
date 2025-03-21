<?php
session_start();
include 'config/db.php';
include 'includes/security.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        Security::verifyCSRFToken($_POST['csrf_token']);
        
        // Handle file upload
        if (isset($_FILES['resource_file'])) {
            Security::validateFileUpload($_FILES['resource_file']);
        }
        
        $title = Security::sanitizeInput($_POST['title']);
        $description = Security::sanitizeInput($_POST['description']);
        $type = Security::sanitizeInput($_POST['type']);
        $access_level = Security::sanitizeInput($_POST['access_level']);
        
        // File upload handling
        $file_path = null;
        if (isset($_FILES['resource_file']) && $_FILES['resource_file']['error'] === 0) {
            $upload_dir = 'uploads/resources/';
            $file_name = uniqid() . '_' . basename($_FILES['resource_file']['name']);
            $file_path = $upload_dir . $file_name;
            
            if (!move_uploaded_file($_FILES['resource_file']['tmp_name'], $file_path)) {
                throw new Exception("Failed to upload file");
            }
        }
        
        $stmt = $conn->prepare("INSERT INTO learning_resources (title, description, type, file_path, access_level, created_by) 
                               VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssi", $title, $description, $type, $file_path, $access_level, $_SESSION['user_id']);
        $stmt->execute();
        
        $_SESSION['success'] = "Resource added successfully";
        header('Location: manage_resources.php');
        exit;
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

$resources = $conn->query("SELECT * FROM learning_resources WHERE created_by = {$_SESSION['user_id']}");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Manage Learning Resources</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">
</head>
<body>
    <div class="container py-5">
        <div class="row mb-4">
            <div class="col-md-8">
                <h2><i class="bi bi-book"></i> Learning Resources</h2>
            </div>
            <div class="col-md-4 text-end">
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addResourceModal">
                    <i class="bi bi-plus-lg"></i> Add Resource
                </button>
            </div>
        </div>

        <div class="row">
            <?php while($resource = $resources->fetch_assoc()): ?>
                <div class="col-md-4 mb-4">
                    <div class="card h-100">
                        <div class="card-header bg-light">
                            <h5 class="card-title mb-0">
                                <i class="bi bi-<?php echo $resource['type'] === 'document' ? 'file-text' : 
                                    ($resource['type'] === 'video' ? 'play-circle' : 'link'); ?>"></i>
                                <?php echo htmlspecialchars($resource['title']); ?>
                            </h5>
                        </div>
                        <div class="card-body">
                            <p class="card-text"><?php echo htmlspecialchars($resource['description']); ?></p>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="badge bg-<?php echo $resource['access_level'] === 'public' ? 'success' : 
                                    ($resource['access_level'] === 'private' ? 'danger' : 'warning'); ?>">
                                    <?php echo ucfirst($resource['access_level']); ?>
                                </span>
                                <div class="btn-group">
                                    <a href="<?php echo htmlspecialchars($resource['file_path']); ?>" 
                                       class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-download"></i> Download
                                    </a>
                                    <button class="btn btn-sm btn-outline-danger" 
                                            onclick="deleteResource(<?php echo $resource['id']; ?>)">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </div>

    <!-- Add Resource Modal -->
    <div class="modal fade" id="addResourceModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="csrf_token" value="<?php echo Security::generateCSRFToken(); ?>">
                    <div class="modal-header">
                        <h5 class="modal-title">Add New Resource</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <!-- Form fields for resource addition -->
                        <div class="mb-3">
                            <label class="form-label">Title</label>
                            <input type="text" name="title" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-control" rows="3" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Type</label>
                            <select name="type" class="form-select" required>
                                <option value="document">Document</option>
                                <option value="video">Video</option>
                                <option value="link">Link</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Access Level</label>
                            <select name="access_level" class="form-select" required>
                                <option value="public">Public</option>
                                <option value="private">Private</option>
                                <option value="restricted">Restricted</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">File</label>
                            <input type="file" name="resource_file" class="form-control">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save Resource</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function deleteResource(id) {
            if (confirm('Are you sure you want to delete this resource?')) {
                // Add delete functionality via AJAX
            }
        }
    </script>
</body>
</html>

<?php
$pageTitle = "Edit Application";
require_once 'includes/header.php';

// Require login
requireLogin();

$user = getCurrentUser();
$userId = getCurrentUserId();

// Check if user data is valid
if (!$user || !$userId) {
    redirect('login.php');
}

$applicationId = $_GET['id'] ?? null;
if (!$applicationId || !userOwnsApplication($userId, $applicationId)) {
    setFlashMessage('Application not found or access denied.', 'error');
    redirect('applications.php');
}

$application = getApplicationById($applicationId);
if (!$application) {
    setFlashMessage('Application not found.', 'error');
    redirect('applications.php');
}

$message = '';
$messageType = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $companyName = sanitizeInput($_POST['company_name'] ?? '');
    $jobTitle = sanitizeInput($_POST['job_title'] ?? '');
    $jobDescription = sanitizeInput($_POST['job_description'] ?? '');
    $applicationDate = $_POST['application_date'] ?? '';
    $status = $_POST['status'] ?? 'applied';
    $salaryRange = sanitizeInput($_POST['salary_range'] ?? '');
    $jobLocation = sanitizeInput($_POST['job_location'] ?? '');
    $jobUrl = sanitizeInput($_POST['job_url'] ?? '');
    $contactPerson = sanitizeInput($_POST['contact_person'] ?? '');
    $contactEmail = sanitizeInput($_POST['contact_email'] ?? '');
    $contactPhone = sanitizeInput($_POST['contact_phone'] ?? '');
    $followUpDate = $_POST['follow_up_date'] ?? '';
    $notes = sanitizeInput($_POST['notes'] ?? '');
    
    // Validate required fields
    if (empty($companyName) || empty($jobTitle) || empty($applicationDate)) {
        $message = 'Please fill in all required fields.';
        $messageType = 'error';
    } else {
        try {
            $pdo = getDBConnection();
            
            // Log status change if status changed
            if ($application['status'] != $status) {
                logStatusChange($applicationId, $application['status'], $status, 'Status updated via edit form');
            }
            
            $sql = "UPDATE job_applications SET company_name = ?, job_title = ?, job_description = ?, application_date = ?, status = ?, salary_range = ?, job_location = ?, job_url = ?, contact_person = ?, contact_email = ?, contact_phone = ?, follow_up_date = ?, notes = ? WHERE id = ? AND user_id = ?";
            $stmt = $pdo->prepare($sql);
            $result = $stmt->execute([
                $companyName, $jobTitle, $jobDescription, $applicationDate, $status,
                $salaryRange, $jobLocation, $jobUrl, $contactPerson, $contactEmail, $contactPhone,
                $followUpDate ?: null, $notes, $applicationId, $userId
            ]);
            
            if ($result) {
                setFlashMessage('Application updated successfully!', 'success');
                redirect('applications.php');
            } else {
                $message = 'Failed to update application. Please try again.';
                $messageType = 'error';
            }
        } catch (Exception $e) {
            $message = 'An error occurred. Please try again.';
            $messageType = 'error';
        }
    }
}
?>

<div class="container">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1>
                    <i class="fas fa-edit me-2"></i>Edit Application
                </h1>
                <a href="applications.php" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Back to Applications
                </a>
            </div>
        </div>
    </div>
    
    <?php if ($message): ?>
    <div class="alert alert-<?php echo $messageType == 'error' ? 'danger' : 'success'; ?> alert-dismissible fade show" role="alert">
        <?php echo htmlspecialchars($message); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>
    
    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    <form method="POST" action="">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="company_name" class="form-label">Company Name *</label>
                                <input type="text" class="form-control" id="company_name" name="company_name" value="<?php echo htmlspecialchars($application['company_name']); ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="job_title" class="form-label">Job Title *</label>
                                <input type="text" class="form-control" id="job_title" name="job_title" value="<?php echo htmlspecialchars($application['job_title']); ?>" required>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="job_description" class="form-label">Job Description</label>
                            <textarea class="form-control" id="job_description" name="job_description" rows="3"><?php echo htmlspecialchars($application['job_description']); ?></textarea>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="application_date" class="form-label">Application Date *</label>
                                <input type="date" class="form-control" id="application_date" name="application_date" value="<?php echo $application['application_date']; ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="status" class="form-label">Status</label>
                                <select class="form-select" id="status" name="status">
                                    <?php foreach (getAllStatuses() as $statusValue => $statusName): ?>
                                    <option value="<?php echo $statusValue; ?>" <?php echo $application['status'] == $statusValue ? 'selected' : ''; ?>>
                                        <?php echo $statusName; ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="salary_range" class="form-label">Salary Range</label>
                                <input type="text" class="form-control" id="salary_range" name="salary_range" value="<?php echo htmlspecialchars($application['salary_range']); ?>" placeholder="e.g., $50,000 - $70,000">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="job_location" class="form-label">Job Location</label>
                                <input type="text" class="form-control" id="job_location" name="job_location" value="<?php echo htmlspecialchars($application['job_location']); ?>" placeholder="e.g., New York, NY">
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="job_url" class="form-label">Job URL</label>
                            <input type="url" class="form-control" id="job_url" name="job_url" value="<?php echo htmlspecialchars($application['job_url']); ?>" placeholder="https://example.com/job-posting">
                        </div>
                        
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="contact_person" class="form-label">Contact Person</label>
                                <input type="text" class="form-control" id="contact_person" name="contact_person" value="<?php echo htmlspecialchars($application['contact_person']); ?>">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="contact_email" class="form-label">Contact Email</label>
                                <input type="email" class="form-control" id="contact_email" name="contact_email" value="<?php echo htmlspecialchars($application['contact_email']); ?>">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="contact_phone" class="form-label">Contact Phone</label>
                                <input type="tel" class="form-control" id="contact_phone" name="contact_phone" value="<?php echo htmlspecialchars($application['contact_phone']); ?>">
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="follow_up_date" class="form-label">Follow-up Date</label>
                            <input type="date" class="form-control" id="follow_up_date" name="follow_up_date" value="<?php echo $application['follow_up_date']; ?>">
                        </div>
                        
                        <div class="mb-3">
                            <label for="notes" class="form-label">Notes</label>
                            <textarea class="form-control" id="notes" name="notes" rows="3"><?php echo htmlspecialchars($application['notes']); ?></textarea>
                        </div>
                        
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Update Application
                            </button>
                            <a href="applications.php" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-2"></i>Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-info-circle me-2"></i>Application Info
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <small class="text-muted">Created</small><br>
                        <strong><?php echo formatDate($application['created_at']); ?></strong>
                    </div>
                    <div class="mb-3">
                        <small class="text-muted">Last Updated</small><br>
                        <strong><?php echo formatDate($application['updated_at']); ?></strong>
                    </div>
                    <div class="mb-3">
                        <small class="text-muted">Current Status</small><br>
                        <span class="badge <?php echo getStatusBadgeClass($application['status']); ?>">
                            <?php echo getStatusDisplayName($application['status']); ?>
                        </span>
                    </div>
                    <div class="mb-3">
                        <small class="text-muted">Days Since Application</small><br>
                        <strong><?php echo daysSinceApplication($application['application_date']); ?> days</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?> 
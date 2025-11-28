<?php
$pageTitle = "Add Application";
require_once 'includes/header.php';

// Require login
requireLogin();

$user = getCurrentUser();
$userId = getCurrentUserId();

// Check if user data is valid
if (!$user || !$userId) {
    redirect('login.php');
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
            $sql = "INSERT INTO job_applications (user_id, company_name, job_title, job_description, application_date, status, salary_range, job_location, job_url, contact_person, contact_email, contact_phone, follow_up_date, notes) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            $result = $stmt->execute([
                $userId, $companyName, $jobTitle, $jobDescription, $applicationDate, $status,
                $salaryRange, $jobLocation, $jobUrl, $contactPerson, $contactEmail, $contactPhone,
                $followUpDate ?: null, $notes
            ]);
            
            if ($result) {
                setFlashMessage('Application added successfully!', 'success');
                redirect('applications.php');
            } else {
                $message = 'Failed to add application. Please try again.';
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
            <h1 class="mb-4">
                <i class="fas fa-plus me-2"></i>Add New Application
            </h1>
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
                                <input type="text" class="form-control" id="company_name" name="company_name" value="<?php echo htmlspecialchars($_POST['company_name'] ?? ''); ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="job_title" class="form-label">Job Title *</label>
                                <input type="text" class="form-control" id="job_title" name="job_title" value="<?php echo htmlspecialchars($_POST['job_title'] ?? ''); ?>" required>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="job_description" class="form-label">Job Description</label>
                            <textarea class="form-control" id="job_description" name="job_description" rows="3"><?php echo htmlspecialchars($_POST['job_description'] ?? ''); ?></textarea>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="application_date" class="form-label">Application Date *</label>
                                <input type="date" class="form-control" id="application_date" name="application_date" value="<?php echo $_POST['application_date'] ?? date('Y-m-d'); ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="status" class="form-label">Status</label>
                                <select class="form-select" id="status" name="status">
                                    <?php foreach (getAllStatuses() as $statusValue => $statusName): ?>
                                    <option value="<?php echo $statusValue; ?>" <?php echo ($_POST['status'] ?? 'applied') == $statusValue ? 'selected' : ''; ?>>
                                        <?php echo $statusName; ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="salary_range" class="form-label">Salary Range</label>
                                <input type="text" class="form-control" id="salary_range" name="salary_range" value="<?php echo htmlspecialchars($_POST['salary_range'] ?? ''); ?>" placeholder="e.g., $50,000 - $70,000">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="job_location" class="form-label">Job Location</label>
                                <input type="text" class="form-control" id="job_location" name="job_location" value="<?php echo htmlspecialchars($_POST['job_location'] ?? ''); ?>" placeholder="e.g., New York, NY">
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="job_url" class="form-label">Job URL</label>
                            <input type="url" class="form-control" id="job_url" name="job_url" value="<?php echo htmlspecialchars($_POST['job_url'] ?? ''); ?>" placeholder="https://example.com/job-posting">
                        </div>
                        
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="contact_person" class="form-label">Contact Person</label>
                                <input type="text" class="form-control" id="contact_person" name="contact_person" value="<?php echo htmlspecialchars($_POST['contact_person'] ?? ''); ?>">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="contact_email" class="form-label">Contact Email</label>
                                <input type="email" class="form-control" id="contact_email" name="contact_email" value="<?php echo htmlspecialchars($_POST['contact_email'] ?? ''); ?>">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="contact_phone" class="form-label">Contact Phone</label>
                                <input type="tel" class="form-control" id="contact_phone" name="contact_phone" value="<?php echo htmlspecialchars($_POST['contact_phone'] ?? ''); ?>">
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="follow_up_date" class="form-label">Follow-up Date</label>
                            <input type="date" class="form-control" id="follow_up_date" name="follow_up_date" value="<?php echo $_POST['follow_up_date'] ?? ''; ?>">
                        </div>
                        
                        <div class="mb-3">
                            <label for="notes" class="form-label">Notes</label>
                            <textarea class="form-control" id="notes" name="notes" rows="3"><?php echo htmlspecialchars($_POST['notes'] ?? ''); ?></textarea>
                        </div>
                        
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Add Application
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
                        <i class="fas fa-info-circle me-2"></i>Tips
                    </h5>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2">
                            <i class="fas fa-check text-success me-2"></i>
                            Fill in as much information as possible
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check text-success me-2"></i>
                            Set follow-up dates to track responses
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check text-success me-2"></i>
                            Add notes for important details
                        </li>
                        <li>
                            <i class="fas fa-check text-success me-2"></i>
                            Update status as your application progresses
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?> 
<?php
$pageTitle = "View Application";
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
?>

<div class="container">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1>
                    <i class="fas fa-eye me-2"></i>Application Details
                </h1>
                <div class="d-flex gap-2">
                    <a href="edit_application.php?id=<?php echo $application['id']; ?>" class="btn btn-primary">
                        <i class="fas fa-edit me-2"></i>Edit
                    </a>
                    <a href="applications.php" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Back to Applications
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-lg-8">
            <!-- Main Application Details -->
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-briefcase me-2"></i>Job Information
                    </h5>
                    <span class="badge <?php echo getStatusBadgeClass($application['status']); ?> fs-6">
                        <?php echo getStatusDisplayName($application['status']); ?>
                    </span>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold text-muted">Company Name</label>
                            <p class="mb-0 fs-5"><?php echo htmlspecialchars($application['company_name']); ?></p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold text-muted">Job Title</label>
                            <p class="mb-0 fs-5"><?php echo htmlspecialchars($application['job_title']); ?></p>
                        </div>
                    </div>
                    
                    <?php if ($application['job_description']): ?>
                    <div class="mb-3">
                        <label class="form-label fw-bold text-muted">Job Description</label>
                        <p class="mb-0"><?php echo nl2br(htmlspecialchars($application['job_description'])); ?></p>
                    </div>
                    <?php endif; ?>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold text-muted">Application Date</label>
                            <p class="mb-0"><?php echo formatDate($application['application_date']); ?></p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold text-muted">Days Since Application</label>
                            <p class="mb-0"><?php echo daysSinceApplication($application['application_date']); ?> days</p>
                        </div>
                    </div>
                    
                    <?php if ($application['salary_range']): ?>
                    <div class="mb-3">
                        <label class="form-label fw-bold text-muted">Salary Range</label>
                        <p class="mb-0"><?php echo htmlspecialchars($application['salary_range']); ?></p>
                    </div>
                    <?php endif; ?>
                    
                    <?php if ($application['job_location']): ?>
                    <div class="mb-3">
                        <label class="form-label fw-bold text-muted">Job Location</label>
                        <p class="mb-0">
                            <i class="fas fa-map-marker-alt text-muted me-2"></i>
                            <?php echo htmlspecialchars($application['job_location']); ?>
                        </p>
                    </div>
                    <?php endif; ?>
                    
                    <?php if ($application['job_url']): ?>
                    <div class="mb-3">
                        <label class="form-label fw-bold text-muted">Job URL</label>
                        <p class="mb-0">
                            <a href="<?php echo htmlspecialchars($application['job_url']); ?>" target="_blank" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-external-link-alt me-1"></i>View Job Posting
                            </a>
                        </p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Contact Information -->
            <?php if ($application['contact_person'] || $application['contact_email'] || $application['contact_phone']): ?>
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-address-book me-2"></i>Contact Information
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <?php if ($application['contact_person']): ?>
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold text-muted">Contact Person</label>
                            <p class="mb-0"><?php echo htmlspecialchars($application['contact_person']); ?></p>
                        </div>
                        <?php endif; ?>
                        
                        <?php if ($application['contact_email']): ?>
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold text-muted">Contact Email</label>
                            <p class="mb-0">
                                <a href="mailto:<?php echo htmlspecialchars($application['contact_email']); ?>">
                                    <?php echo htmlspecialchars($application['contact_email']); ?>
                                </a>
                            </p>
                        </div>
                        <?php endif; ?>
                        
                        <?php if ($application['contact_phone']): ?>
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold text-muted">Contact Phone</label>
                            <p class="mb-0">
                                <a href="tel:<?php echo htmlspecialchars($application['contact_phone']); ?>">
                                    <?php echo htmlspecialchars($application['contact_phone']); ?>
                                </a>
                            </p>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endif; ?>
            
            <!-- Follow-up Information -->
            <?php if ($application['follow_up_date']): ?>
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-calendar me-2"></i>Follow-up Information
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold text-muted">Follow-up Date</label>
                        <p class="mb-0">
                            <?php echo formatDate($application['follow_up_date']); ?>
                            <?php if (isFollowUpOverdue($application['follow_up_date'])): ?>
                            <span class="badge bg-danger ms-2">Overdue</span>
                            <?php endif; ?>
                        </p>
                    </div>
                </div>
            </div>
            <?php endif; ?>
            
            <!-- Notes -->
            <?php if ($application['notes']): ?>
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-sticky-note me-2"></i>Notes
                    </h5>
                </div>
                <div class="card-body">
                    <p class="mb-0"><?php echo nl2br(htmlspecialchars($application['notes'])); ?></p>
                </div>
            </div>
            <?php endif; ?>
        </div>
        
        <div class="col-lg-4">
            <!-- Application Timeline -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-clock me-2"></i>Timeline
                    </h5>
                </div>
                <div class="card-body">
                    <div class="application-timeline">
                        <div class="timeline-item">
                            <div class="d-flex justify-content-between">
                                <span class="fw-bold">Application Submitted</span>
                                <small class="text-muted"><?php echo formatDate($application['application_date']); ?></small>
                            </div>
                        </div>
                        
                        <div class="timeline-item">
                            <div class="d-flex justify-content-between">
                                <span class="fw-bold">Last Updated</span>
                                <small class="text-muted"><?php echo formatDate($application['updated_at']); ?></small>
                            </div>
                        </div>
                        
                        <?php if ($application['follow_up_date']): ?>
                        <div class="timeline-item">
                            <div class="d-flex justify-content-between">
                                <span class="fw-bold">Follow-up Due</span>
                                <small class="text-muted"><?php echo formatDate($application['follow_up_date']); ?></small>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <!-- Quick Actions -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-bolt me-2"></i>Quick Actions
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="edit_application.php?id=<?php echo $application['id']; ?>" class="btn btn-primary">
                            <i class="fas fa-edit me-2"></i>Edit Application
                        </a>
                        <?php if ($application['job_url']): ?>
                        <a href="<?php echo htmlspecialchars($application['job_url']); ?>" target="_blank" class="btn btn-outline-primary">
                            <i class="fas fa-external-link-alt me-2"></i>View Job Posting
                        </a>
                        <?php endif; ?>
                        <a href="applications.php" class="btn btn-outline-secondary">
                            <i class="fas fa-list me-2"></i>View All Applications
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?> 
<?php
$pageTitle = "Applications";
require_once 'includes/header.php';

// Require login
requireLogin();

$user = getCurrentUser();
$userId = getCurrentUserId();

// Check if user data is valid
if (!$user || !$userId) {
    redirect('login.php');
}

// Get filter parameters
$statusFilter = $_GET['status'] ?? null;
$searchQuery = $_GET['search'] ?? '';

// Get applications
$applications = getUserApplications($userId, $statusFilter);

// Filter by search query if provided
if (!empty($searchQuery)) {
    $applications = array_filter($applications, function($app) use ($searchQuery) {
        return stripos($app['company_name'], $searchQuery) !== false ||
               stripos($app['job_title'], $searchQuery) !== false ||
               stripos($app['job_location'], $searchQuery) !== false;
    });
}

// Handle application deletion
if (isset($_POST['delete_id']) && userOwnsApplication($userId, $_POST['delete_id'])) {
    try {
        $pdo = getDBConnection();
        $stmt = $pdo->prepare("DELETE FROM job_applications WHERE id = ? AND user_id = ?");
        if ($stmt->execute([$_POST['delete_id'], $userId])) {
            setFlashMessage('Application deleted successfully!', 'success');
            redirect('applications.php');
        }
    } catch (Exception $e) {
        setFlashMessage('Failed to delete application.', 'error');
    }
}
?>

<div class="container">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1>
                    <i class="fas fa-list me-2"></i>Applications
                </h1>
                <a href="add_application.php" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Add Application
                </a>
            </div>
        </div>
    </div>
    
    <!-- Filters -->
    <div class="row mb-4">
        <div class="col-md-8">
            <form method="GET" action="" class="d-flex gap-2">
                <input type="text" class="form-control" name="search" placeholder="Search companies, titles, or locations..." value="<?php echo htmlspecialchars($searchQuery); ?>">
                <button type="submit" class="btn btn-outline-primary">
                    <i class="fas fa-search"></i>
                </button>
            </form>
        </div>
        <div class="col-md-4">
            <select class="form-select" onchange="window.location.href='applications.php?status=' + this.value + '&search=<?php echo urlencode($searchQuery); ?>'">
                <option value="">All Statuses</option>
                <?php foreach (getAllStatuses() as $statusValue => $statusName): ?>
                <option value="<?php echo $statusValue; ?>" <?php echo $statusFilter == $statusValue ? 'selected' : ''; ?>>
                    <?php echo $statusName; ?>
                </option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>
    
    <?php if (empty($applications)): ?>
    <div class="row">
        <div class="col-12">
            <div class="text-center py-5">
                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                <h4 class="text-muted">No applications found</h4>
                <p class="text-muted">
                    <?php if (!empty($searchQuery) || !empty($statusFilter)): ?>
                        Try adjusting your search criteria or 
                    <?php endif; ?>
                    <a href="add_application.php">add your first application</a>
                </p>
            </div>
        </div>
    </div>
    <?php else: ?>
    
    <!-- Applications List -->
    <div class="row">
        <?php foreach ($applications as $app): ?>
        <div class="col-lg-6 col-xl-4 mb-4">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span class="badge <?php echo getStatusBadgeClass($app['status']); ?>">
                        <?php echo getStatusDisplayName($app['status']); ?>
                    </span>
                    <div class="dropdown">
                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            <i class="fas fa-ellipsis-v"></i>
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="edit_application.php?id=<?php echo $app['id']; ?>">
                                <i class="fas fa-edit me-2"></i>Edit
                            </a></li>
                            <li><a class="dropdown-item" href="view_application.php?id=<?php echo $app['id']; ?>">
                                <i class="fas fa-eye me-2"></i>View Details
                            </a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="POST" action="" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this application?')">
                                    <input type="hidden" name="delete_id" value="<?php echo $app['id']; ?>">
                                    <button type="submit" class="dropdown-item text-danger">
                                        <i class="fas fa-trash me-2"></i>Delete
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="card-body">
                    <h5 class="card-title"><?php echo htmlspecialchars($app['job_title']); ?></h5>
                    <h6 class="card-subtitle mb-2 text-muted"><?php echo htmlspecialchars($app['company_name']); ?></h6>
                    
                    <div class="row mb-3">
                        <div class="col-6">
                            <small class="text-muted">Applied</small><br>
                            <strong><?php echo formatDate($app['application_date']); ?></strong>
                        </div>
                        <div class="col-6">
                            <small class="text-muted">Days Ago</small><br>
                            <strong><?php echo daysSinceApplication($app['application_date']); ?></strong>
                        </div>
                    </div>
                    
                    <?php if ($app['job_location']): ?>
                    <p class="card-text mb-2">
                        <i class="fas fa-map-marker-alt text-muted me-2"></i>
                        <?php echo htmlspecialchars($app['job_location']); ?>
                    </p>
                    <?php endif; ?>
                    
                    <?php if ($app['salary_range']): ?>
                    <p class="card-text mb-2">
                        <i class="fas fa-dollar-sign text-muted me-2"></i>
                        <?php echo htmlspecialchars($app['salary_range']); ?>
                    </p>
                    <?php endif; ?>
                    
                    <?php if ($app['follow_up_date']): ?>
                    <p class="card-text mb-2">
                        <i class="fas fa-calendar text-muted me-2"></i>
                        Follow-up: <?php echo formatDate($app['follow_up_date']); ?>
                        <?php if (isFollowUpOverdue($app['follow_up_date'])): ?>
                        <span class="badge bg-danger ms-2">Overdue</span>
                        <?php endif; ?>
                    </p>
                    <?php endif; ?>
                    
                    <?php if ($app['notes']): ?>
                    <p class="card-text">
                        <small class="text-muted"><?php echo truncateText($app['notes'], 100); ?></small>
                    </p>
                    <?php endif; ?>
                </div>
                <div class="card-footer">
                    <div class="d-flex justify-content-between align-items-center">
                        <small class="text-muted">
                            Updated <?php echo formatDate($app['updated_at']); ?>
                        </small>
                        <?php if ($app['job_url']): ?>
                        <a href="<?php echo htmlspecialchars($app['job_url']); ?>" target="_blank" class="btn btn-sm btn-outline-primary">
                            <i class="fas fa-external-link-alt me-1"></i>View Job
                        </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    
    <!-- Results Summary -->
    <div class="row mt-4">
        <div class="col-12">
            <p class="text-muted">
                Showing <?php echo count($applications); ?> application<?php echo count($applications) != 1 ? 's' : ''; ?>
                <?php if (!empty($searchQuery) || !empty($statusFilter)): ?>
                (filtered)
                <?php endif; ?>
            </p>
        </div>
    </div>
    
    <?php endif; ?>
</div>

<?php require_once 'includes/footer.php'; ?> 
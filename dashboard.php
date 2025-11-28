<?php
$pageTitle = "Dashboard";
require_once 'includes/header.php';

// Require login
requireLogin();

$user = getCurrentUser();
$userId = getCurrentUserId();

// Check if user data is valid
if (!$user || !$userId) {
    // Redirect to login if user data is invalid
    redirect('login.php');
}

// Get application statistics
$stats = getApplicationStats($userId);

// Get recent applications
$recentApplications = getUserApplications($userId, null, 5);

// Get upcoming follow-ups
$upcomingFollowUps = getUpcomingFollowUps($userId, 7);
?>

<div class="container">
    <div class="row">
        <div class="col-12">
            <h1 class="mb-4">
                <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                <small class="text-muted">Welcome back, <?php echo htmlspecialchars($user['first_name']); ?>!</small>
            </h1>
        </div>
    </div>
    
    <!-- Statistics Cards -->
    <div class="row">
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="stat-card total">
                <h3><?php echo $stats['total']; ?></h3>
                <p>Total Applications</p>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="stat-card applied">
                <h3><?php echo $stats['applied']; ?></h3>
                <p>Applied</p>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="stat-card interview">
                <h3><?php echo $stats['interview_scheduled'] + $stats['interviewed']; ?></h3>
                <p>Interviews</p>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="stat-card offer">
                <h3><?php echo $stats['offer_received']; ?></h3>
                <p>Offers</p>
            </div>
        </div>
    </div>
    
    <div class="row">
        <!-- Quick Actions -->
        <div class="col-lg-4 mb-4">
            <div class="quick-actions">
                <h5 class="mb-3">
                    <i class="fas fa-bolt me-2"></i>Quick Actions
                </h5>
                <div class="d-grid gap-2">
                    <a href="add_application.php" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>Add New Application
                    </a>
                    <a href="applications.php" class="btn btn-outline-primary">
                        <i class="fas fa-list me-2"></i>View All Applications
                    </a>
                    <a href="applications.php?status=interview_scheduled" class="btn btn-outline-warning">
                        <i class="fas fa-calendar me-2"></i>Upcoming Interviews
                    </a>
                    <a href="applications.php?status=offer_received" class="btn btn-outline-success">
                        <i class="fas fa-handshake me-2"></i>Pending Offers
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Application Status Breakdown -->
        <div class="col-lg-8 mb-4">
            <div class="dashboard-card">
                <h5 class="mb-3">
                    <i class="fas fa-chart-pie me-2"></i>Application Status Breakdown
                </h5>
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-2">
                            <div class="d-flex justify-content-between">
                                <span>Applied</span>
                                <span class="badge bg-primary"><?php echo $stats['applied']; ?></span>
                            </div>
                            <div class="progress progress-thin">
                                <div class="progress-bar bg-primary" style="width: <?php echo $stats['total'] > 0 ? ($stats['applied'] / $stats['total']) * 100 : 0; ?>%"></div>
                            </div>
                        </div>
                        <div class="mb-2">
                            <div class="d-flex justify-content-between">
                                <span>Interview Scheduled</span>
                                <span class="badge bg-warning"><?php echo $stats['interview_scheduled']; ?></span>
                            </div>
                            <div class="progress progress-thin">
                                <div class="progress-bar bg-warning" style="width: <?php echo $stats['total'] > 0 ? ($stats['interview_scheduled'] / $stats['total']) * 100 : 0; ?>%"></div>
                            </div>
                        </div>
                        <div class="mb-2">
                            <div class="d-flex justify-content-between">
                                <span>Interviewed</span>
                                <span class="badge bg-info"><?php echo $stats['interviewed']; ?></span>
                            </div>
                            <div class="progress progress-thin">
                                <div class="progress-bar bg-info" style="width: <?php echo $stats['total'] > 0 ? ($stats['interviewed'] / $stats['total']) * 100 : 0; ?>%"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-2">
                            <div class="d-flex justify-content-between">
                                <span>Offer Received</span>
                                <span class="badge bg-success"><?php echo $stats['offer_received']; ?></span>
                            </div>
                            <div class="progress progress-thin">
                                <div class="progress-bar bg-success" style="width: <?php echo $stats['total'] > 0 ? ($stats['offer_received'] / $stats['total']) * 100 : 0; ?>%"></div>
                            </div>
                        </div>
                        <div class="mb-2">
                            <div class="d-flex justify-content-between">
                                <span>Rejected</span>
                                <span class="badge bg-danger"><?php echo $stats['rejected']; ?></span>
                            </div>
                            <div class="progress progress-thin">
                                <div class="progress-bar bg-danger" style="width: <?php echo $stats['total'] > 0 ? ($stats['rejected'] / $stats['total']) * 100 : 0; ?>%"></div>
                            </div>
                        </div>
                        <div class="mb-2">
                            <div class="d-flex justify-content-between">
                                <span>Withdrawn</span>
                                <span class="badge bg-secondary"><?php echo $stats['withdrawn']; ?></span>
                            </div>
                            <div class="progress progress-thin">
                                <div class="progress-bar bg-secondary" style="width: <?php echo $stats['total'] > 0 ? ($stats['withdrawn'] / $stats['total']) * 100 : 0; ?>%"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <!-- Recent Applications -->
        <div class="col-lg-8 mb-4">
            <div class="dashboard-card">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0">
                        <i class="fas fa-clock me-2"></i>Recent Applications
                    </h5>
                    <a href="applications.php" class="btn btn-sm btn-outline-primary">View All</a>
                </div>
                
                <?php if (empty($recentApplications)): ?>
                    <div class="text-center py-4">
                        <i class="fas fa-briefcase fa-3x text-muted mb-3"></i>
                        <h6 class="text-muted">No applications yet</h6>
                        <p class="text-muted">Start tracking your job applications by adding your first one.</p>
                        <a href="add_application.php" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>Add Application
                        </a>
                    </div>
                <?php else: ?>
                    <?php foreach ($recentApplications as $application): ?>
                    <div class="card application-card <?php echo $application['status']; ?> mb-3">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="card-title mb-1"><?php echo htmlspecialchars($application['job_title']); ?></h6>
                                    <p class="card-text text-muted mb-2"><?php echo htmlspecialchars($application['company_name']); ?></p>
                                    <div class="application-meta">
                                        <i class="fas fa-calendar"></i>
                                        Applied <?php echo formatDate($application['application_date']); ?>
                                        <?php if ($application['follow_up_date']): ?>
                                            <span class="ms-3">
                                                <i class="fas fa-bell"></i>
                                                Follow-up <?php echo formatDate($application['follow_up_date']); ?>
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="text-end">
                                    <span class="badge status-badge <?php echo getStatusBadgeClass($application['status']); ?>">
                                        <?php echo getStatusDisplayName($application['status']); ?>
                                    </span>
                                    <div class="mt-2">
                                        <a href="edit_application.php?id=<?php echo $application['id']; ?>" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Upcoming Follow-ups -->
        <div class="col-lg-4 mb-4">
            <div class="dashboard-card">
                <h5 class="mb-3">
                    <i class="fas fa-bell me-2"></i>Upcoming Follow-ups
                </h5>
                
                <?php if (empty($upcomingFollowUps)): ?>
                    <div class="text-center py-3">
                        <i class="fas fa-check-circle fa-2x text-success mb-2"></i>
                        <p class="text-muted mb-0">No upcoming follow-ups</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($upcomingFollowUps as $followUp): ?>
                    <div class="follow-up-item <?php echo isFollowUpOverdue($followUp['follow_up_date']) ? 'overdue' : (date('Y-m-d') == $followUp['follow_up_date'] ? 'today' : ''); ?>">
                        <h6 class="mb-1"><?php echo htmlspecialchars($followUp['company_name']); ?></h6>
                        <p class="mb-1 text-muted"><?php echo htmlspecialchars($followUp['job_title']); ?></p>
                        <small class="follow-up-date">
                            <?php 
                            if (isFollowUpOverdue($followUp['follow_up_date'])) {
                                echo '<i class="fas fa-exclamation-triangle text-danger me-1"></i>Overdue: ';
                            } elseif (date('Y-m-d') == $followUp['follow_up_date']) {
                                echo '<i class="fas fa-clock text-warning me-1"></i>Today: ';
                            } else {
                                echo '<i class="fas fa-calendar text-info me-1"></i>';
                            }
                            echo formatDate($followUp['follow_up_date']); 
                            ?>
                        </small>
                    </div>
                    <?php endforeach; ?>
                    
                    <div class="mt-3">
                        <a href="applications.php?follow_up=upcoming" class="btn btn-sm btn-outline-primary w-100">
                            View All Follow-ups
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- Recent Activity Summary -->
    <div class="row">
        <div class="col-12">
            <div class="dashboard-card">
                <h5 class="mb-3">
                    <i class="fas fa-chart-line me-2"></i>Activity Summary
                </h5>
                <div class="row text-center">
                    <div class="col-md-3">
                        <h4 class="text-primary"><?php echo $stats['recent']; ?></h4>
                        <p class="text-muted mb-0">Applications in last 30 days</p>
                    </div>
                    <div class="col-md-3">
                        <h4 class="text-warning"><?php echo $stats['upcoming_followups']; ?></h4>
                        <p class="text-muted mb-0">Follow-ups this week</p>
                    </div>
                    <div class="col-md-3">
                        <h4 class="text-success"><?php echo $stats['offer_received']; ?></h4>
                        <p class="text-muted mb-0">Active offers</p>
                    </div>
                    <div class="col-md-3">
                        <h4 class="text-info"><?php echo $stats['interview_scheduled'] + $stats['interviewed']; ?></h4>
                        <p class="text-muted mb-0">Interview opportunities</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>


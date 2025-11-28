<?php
// Common functions for the Job Application Tracker CMS

// Start session if not already started
function startSession() {
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
}

// Check if user is logged in
function isLoggedIn() {
    startSession();
    return isset($_SESSION['user_id']);
}

// Get current user ID
function getCurrentUserId() {
    startSession();
    return isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
}

// Get current user info
function getCurrentUser() {
    if (!isLoggedIn()) {
        return null;
    }
    
    $userId = getCurrentUserId();
    if (!$userId) {
        return null;
    }
    
    try {
        $pdo = getDBConnection();
        $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        $user = $stmt->fetch();
        
        return $user ?: null;
    } catch (Exception $e) {
        return null;
    }
}

// Get base URL for redirects
function getBaseUrl() {
    return defined('BASE_URL') ? BASE_URL : 'http://localhost/job_tracker_cms';
}

// Redirect helper function - ensures redirects use base URL (no port)
function redirect($path) {
    // Remove leading slash if present
    $path = ltrim($path, '/');
    
    // If path is already a full URL, use it as is
    if (filter_var($path, FILTER_VALIDATE_URL)) {
        header('Location: ' . $path);
        exit();
    }
    
    // Otherwise, construct URL from base URL
    $baseUrl = getBaseUrl();
    $url = rtrim($baseUrl, '/') . '/' . $path;
    header('Location: ' . $url);
    exit();
}

// Redirect to login if not authenticated
function requireLogin() {
    if (!isLoggedIn()) {
        redirect('login.php');
    }
}

// Sanitize input data
function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Validate email format
function isValidEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

// Hash password
function hashPassword($password) {
    return password_hash($password, PASSWORD_DEFAULT);
}

// Verify password
function verifyPassword($password, $hash) {
    return password_verify($password, $hash);
}

// Format date for display
function formatDate($date) {
    return date('F j, Y', strtotime($date));
}

// Format date for input fields
function formatDateForInput($date) {
    return date('Y-m-d', strtotime($date));
}

// Get flash message
function getFlashMessage() {
    startSession();
    if (isset($_SESSION['flash_message'])) {
        $message = $_SESSION['flash_message'];
        unset($_SESSION['flash_message']);
        return $message;
    }
    return null;
}

// Set flash message
function setFlashMessage($message, $type = 'info') {
    startSession();
    $_SESSION['flash_message'] = [
        'message' => $message,
        'type' => $type
    ];
}

// Get application status badge class
function getStatusBadgeClass($status) {
    switch ($status) {
        case 'applied':
            return 'bg-primary';
        case 'interview_scheduled':
            return 'bg-warning';
        case 'interviewed':
            return 'bg-info';
        case 'offer_received':
            return 'bg-success';
        case 'rejected':
            return 'bg-danger';
        case 'withdrawn':
            return 'bg-secondary';
        default:
            return 'bg-light text-dark';
    }
}

// Get application status display name
function getStatusDisplayName($status) {
    switch ($status) {
        case 'applied':
            return 'Applied';
        case 'interview_scheduled':
            return 'Interview Scheduled';
        case 'interviewed':
            return 'Interviewed';
        case 'offer_received':
            return 'Offer Received';
        case 'rejected':
            return 'Rejected';
        case 'withdrawn':
            return 'Withdrawn';
        default:
            return ucfirst($status);
    }
}

// Get all application statuses
function getAllStatuses() {
    return [
        'applied' => 'Applied',
        'interview_scheduled' => 'Interview Scheduled',
        'interviewed' => 'Interviewed',
        'offer_received' => 'Offer Received',
        'rejected' => 'Rejected',
        'withdrawn' => 'Withdrawn'
    ];
}

// Get user's applications
function getUserApplications($userId, $status = null, $limit = null) {
    $pdo = getDBConnection();
    
    $sql = "SELECT * FROM job_applications WHERE user_id = ?";
    $params = [$userId];
    
    if ($status) {
        $sql .= " AND status = ?";
        $params[] = $status;
    }
    
    $sql .= " ORDER BY application_date DESC";
    
    if ($limit) {
        $sql .= " LIMIT " . (int)$limit;
    }
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

// Get application by ID
function getApplicationById($id) {
    $pdo = getDBConnection();
    $stmt = $pdo->prepare("SELECT * FROM job_applications WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch();
}

// Check if user owns application
function userOwnsApplication($userId, $applicationId) {
    $pdo = getDBConnection();
    $stmt = $pdo->prepare("SELECT id FROM job_applications WHERE id = ? AND user_id = ?");
    $stmt->execute([$applicationId, $userId]);
    return $stmt->fetch() !== false;
}

// Get application statistics for user
function getApplicationStats($userId) {
    $pdo = getDBConnection();
    
    $stats = [];
    
    // Total applications
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM job_applications WHERE user_id = ?");
    $stmt->execute([$userId]);
    $stats['total'] = $stmt->fetchColumn();
    
    // Applications by status
    $statuses = ['applied', 'interview_scheduled', 'interviewed', 'offer_received', 'rejected', 'withdrawn'];
    foreach ($statuses as $status) {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM job_applications WHERE user_id = ? AND status = ?");
        $stmt->execute([$userId, $status]);
        $stats[$status] = $stmt->fetchColumn();
    }
    
    // Recent applications (last 30 days)
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM job_applications WHERE user_id = ? AND application_date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)");
    $stmt->execute([$userId]);
    $stats['recent'] = $stmt->fetchColumn();
    
    // Upcoming follow-ups
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM job_applications WHERE user_id = ? AND follow_up_date >= CURDATE() AND follow_up_date <= DATE_ADD(CURDATE(), INTERVAL 7 DAY)");
    $stmt->execute([$userId]);
    $stats['upcoming_followups'] = $stmt->fetchColumn();
    
    return $stats;
}

// Get upcoming follow-ups
function getUpcomingFollowUps($userId, $days = 7) {
    $pdo = getDBConnection();
    $stmt = $pdo->prepare("SELECT * FROM job_applications WHERE user_id = ? AND follow_up_date >= CURDATE() AND follow_up_date <= DATE_ADD(CURDATE(), INTERVAL ? DAY) ORDER BY follow_up_date ASC");
    $stmt->execute([$userId, $days]);
    return $stmt->fetchAll();
}

// Log status change
function logStatusChange($applicationId, $oldStatus, $newStatus, $notes = '') {
    $pdo = getDBConnection();
    $stmt = $pdo->prepare("INSERT INTO application_status_history (application_id, old_status, new_status, notes) VALUES (?, ?, ?, ?)");
    return $stmt->execute([$applicationId, $oldStatus, $newStatus, $notes]);
}

// Truncate text
function truncateText($text, $length = 100, $suffix = '...') {
    if (strlen($text) <= $length) {
        return $text;
    }
    
    return substr($text, 0, $length) . $suffix;
}

// Calculate days since application
function daysSinceApplication($applicationDate) {
    $date1 = new DateTime($applicationDate);
    $date2 = new DateTime();
    $diff = $date2->diff($date1);
    return $diff->days;
}

// Check if follow-up is overdue
function isFollowUpOverdue($followUpDate) {
    if (!$followUpDate) return false;
    return strtotime($followUpDate) < strtotime('today');
}
?>


<?php
// Database configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'job_tracker_cms');
define('DB_USER', 'root');
define('DB_PASS', '');

// Base URL configuration - ensures redirects go to http://localhost/project_name (no port)
define('BASE_URL', 'http://localhost/job_tracker_cms');

// Create database connection
function getDBConnection() {
    try {
        $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4", DB_USER, DB_PASS);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        return $pdo;
    } catch(PDOException $e) {
        die("Connection failed: " . $e->getMessage());
    }
}

// Test database connection
function testDBConnection() {
    try {
        $pdo = getDBConnection();
        return true;
    } catch(Exception $e) {
        return false;
    }
}
?>
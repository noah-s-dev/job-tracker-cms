<?php
require_once 'includes/functions.php';

startSession();

// Destroy session
session_destroy();

// Set logout message
startSession();
setFlashMessage("You have been logged out successfully.", "info");

// Redirect to home page
redirect('index.php');
?>


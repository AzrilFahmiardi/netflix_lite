<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']) && isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
}

// Redirect to login page if not authenticated
function requireLogin() {
    if (!isLoggedIn()) {
        header("Location: login.php");
        exit;
    }
}

// Get current user ID
function getCurrentUserId() {
    return isLoggedIn() ? $_SESSION['user_id'] : null;
}


// Get current user's full name
function getCurrentUserName() {
    if (!isLoggedIn()) return null;
    
    // If name is stored in session, return it
    if (isset($_SESSION['name'])) {
        return $_SESSION['name'];
    }
    
    // If name is not in session but we have first_name and last_name
    if (isset($_SESSION['first_name']) && isset($_SESSION['last_name'])) {
        return $_SESSION['first_name'] . ' ' . $_SESSION['last_name'];
    }
    
    // Fallback to username if no name available
    return $_SESSION['username'];
}
?>

<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function isLoggedIn() {
    return isset($_SESSION['user_id']) && isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
}

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
    
    if (isset($_SESSION['name'])) {
        return $_SESSION['name'];
    }
    
    if (isset($_SESSION['first_name']) && isset($_SESSION['last_name'])) {
        return $_SESSION['first_name'] . ' ' . $_SESSION['last_name'];
    }
    
    return $_SESSION['username'];
}
?>

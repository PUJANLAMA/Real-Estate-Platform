<?php
require_once "../config/db.php";

// Verify CSRF token for logout
if (isset($_GET['token']) && verifyCSRFToken($_GET['token'])) {
    // Clear session data
    $_SESSION = array();
    
    // Destroy session cookie
    if (isset($_COOKIE[session_name()])) {
        setcookie(session_name(), '', time() - 3600, '/');
    }
    
    // Destroy session
    session_destroy();
    
    // Start new session for message
    session_start();
    redirect('login.php', 'You have been logged out successfully.', 'success');
} else {
    redirect('index.php', 'Invalid logout request.', 'error');
}
?>

<?php
// Start session globally
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Check if user has required role
function hasRole($role) {
    if (!isLoggedIn()) {
        return false;
    }
    return $_SESSION['user_role'] === $role || $_SESSION['user_role'] === 'admin';
}

// Require login
function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: /retail_bi_dashboard_php/index.php');
        exit;
    }
}

// Require specific role
function requireRole($role) {
    requireLogin();
    if (!hasRole($role)) {
        http_response_code(403);
        die('Unauthorized access');
    }
}

// Get current user
function getCurrentUser() {
    if (!isLoggedIn()) {
        return null;
    }
    return [
        'id' => $_SESSION['user_id'],
        'name' => $_SESSION['user_name'],
        'email' => $_SESSION['user_email'],
        'role' => $_SESSION['user_role'],
        'company_id' => $_SESSION['company_id']
    ];
}

// Sanitize input
function sanitize($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

// Redirect helper
function redirect($url) {
    header("Location: $url");
    exit;
}

// Get flash message
function getFlashMessage($key) {
    if (isset($_SESSION[$key])) {
        $message = $_SESSION[$key];
        unset($_SESSION[$key]);
        return $message;
    }
    return null;
}

// Set flash message
function setFlashMessage($key, $message) {
    $_SESSION[$key] = $message;
}
?>
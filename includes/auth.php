
<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/functions.php';

// Login function
function login($email, $password) {
    $db = getDBConnection();
    $stmt = $db->prepare("
        SELECT u.*, r.name as role_name, c.id as company_id 
        FROM users u
        JOIN roles r ON u.role_id = r.id
        LEFT JOIN companies c ON u.company_id = c.id
        WHERE u.email = ? AND u.is_active = 1
    ");
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_role'] = $user['role_name'];
        $_SESSION['company_id'] = $user['company_id'];
        return true;
    }
    return false;
}

// Register function
function register($name, $email, $password, $company_id, $phone = null) {
    $db = getDBConnection();
    
    // Check if email exists
    $stmt = $db->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        return ['success' => false, 'message' => 'Email already exists'];
    }
    
    // Get company role ID
    $stmt = $db->prepare("SELECT id FROM roles WHERE name = 'company'");
    $stmt->execute();
    $role_id = $stmt->fetchColumn();
    
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    
    $stmt = $db->prepare("
        INSERT INTO users (name, email, password, role_id, company_id, phone)
        VALUES (?, ?, ?, ?, ?, ?)
    ");
    
    try {
        $stmt->execute([$name, $email, $hashed_password, $role_id, $company_id, $phone]);
        return ['success' => true, 'message' => 'Registration successful'];
    } catch (PDOException $e) {
        return ['success' => false, 'message' => 'Registration failed: ' . $e->getMessage()];
    }
}

// Logout function
function logout() {
    session_destroy();
    redirect('/retail_bi_dashboard_php/index.php');
}
?>
<?php
require_once __DIR__ . '/../../../includes/auth.php';
require_once __DIR__ . '/../../../config/database.php';
$error = '';
$success = '';

// If already logged in, redirect to dashboard
if (isLoggedIn()) {
    if ($_SESSION['user_role'] === 'admin') {
        header('Location: /retail_bi_dashboard_php/app/views/admin/dashboard.php');
    } else {
        header('Location: /retail_bi_dashboard_php/app/views/dashboard/index.php');
    }
    exit;
}

// Get companies for dropdown
$db = getDBConnection();
$stmt = $db->query("SELECT id, name FROM companies WHERE is_active = 1 ORDER BY name");
$companies = $stmt->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitize($_POST['name']);
    $email = sanitize($_POST['email']);
    $password = $_POST['password'];
    $password_confirm = $_POST['password_confirm'];
    $company_id = $_POST['company_id'];
    $phone = sanitize($_POST['phone'] ?? '');
    
    // Validation
    if (empty($name) || empty($email) || empty($password) || empty($company_id)) {
        $error = 'All required fields must be filled';
    } elseif ($password !== $password_confirm) {
        $error = 'Passwords do not match';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters';
    } else {
        $result = register($name, $email, $password, $company_id, $phone);
        
        if ($result['success']) {
            // Auto login after registration
            if (login($email, $password)) {
                header('Location: /retail_bi_dashboard_php/app/views/dashboard/index.php');
                exit;
            }
        } else {
            $error = $result['message'];
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - BI Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
    <div class="min-h-screen flex items-center justify-center py-12 px-4">
        <div class="max-w-md w-full space-y-8">
            <div>
                <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                    Create your account
                </h2>
                <p class="mt-2 text-center text-sm text-gray-600">
                    Already have an account? <a href="/retail_bi_dashboard_php/app/views/auth/login.php" class="font-medium text-blue-600">
                        Sign in
                    </a>
                </p>
            </div>
            
            <?php if ($error): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                <?= $error ?>
            </div>
            <?php endif; ?>
            
            <?php if ($success): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                <?= $success ?>
            </div>
            <?php endif; ?>
            
            <form class="mt-8 space-y-6" method="POST">
                <div class="space-y-4">
                    <!-- Name -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700">Full Name *</label>
                        <input id="name" name="name" type="text" required 
                               value="<?= isset($_POST['name']) ? htmlspecialchars($_POST['name']) : '' ?>"
                               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    
                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700">Email *</label>
                        <input id="email" name="email" type="email" required 
                               value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>"
                               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    
                    <!-- Phone -->
                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700">Phone</label>
                        <input id="phone" name="phone" type="text" 
                               value="<?= isset($_POST['phone']) ? htmlspecialchars($_POST['phone']) : '' ?>"
                               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    
                    <!-- Company -->
                    <div>
                        <label for="company_id" class="block text-sm font-medium text-gray-700">Company *</label>
                        <select id="company_id" name="company_id" required 
                                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Select a company</option>
                            <?php foreach ($companies as $company): ?>
                                <option value="<?= $company['id'] ?>" 
                                        <?= (isset($_POST['company_id']) && $_POST['company_id'] == $company['id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($company['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <!-- Password -->
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700">Password *</label>
                        <input id="password" name="password" type="password" required minlength="6"
                               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        <p class="mt-1 text-xs text-gray-500">Minimum 6 characters</p>
                    </div>
                    
                    <!-- Password Confirm -->
                    <div>
                        <label for="password_confirm" class="block text-sm font-medium text-gray-700">Confirm Password *</label>
                        <input id="password_confirm" name="password_confirm" type="password" required minlength="6"
                               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    </div>
                </div>
                
                <button type="submit" 
                        class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700">
                    Create Account
                </button>
            </form>
            
            <div class="text-center text-xs text-gray-500">
                By registering, you agree to create a company account
            </div>
        </div>
    </div>
</body>
</html>
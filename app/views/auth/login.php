<?php
require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../../includes/functions.php';
require_once __DIR__ . '/../../../includes/auth.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitize($_POST['email']);
    $password = $_POST['password'];
    
    if (login($email, $password)) {
        if ($_SESSION['user_role'] === 'admin') {
            redirect('/retail_bi_dashboard_php/app/views/admin/dashboard.php');
        } else {
            redirect('/retail_bi_dashboard_php/app/views/dashboard/index.php');
        }
    } else {
        $error = 'Invalid email or password';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - BI Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
    <div class="min-h-screen flex items-center justify-center py-12 px-4">
        <div class="max-w-md w-full space-y-8">
            <div>
                <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">Sign in</h2>
                <p class="mt-2 text-center text-sm text-gray-600">
                    Or <a href="/retail_bi_dashboard_php/app/views/auth/register.php" class="font-medium text-blue-600">create account</a>
                </p>
            </div>
            <?php if ($error): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded"><?= $error ?></div>
            <?php endif; ?>
            <form class="mt-8 space-y-6" method="POST">
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Email</label>
                        <input name="email" type="email" required class="mt-1 block w-full px-3 py-2 border rounded-md">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Password</label>
                        <input name="password" type="password" required class="mt-1 block w-full px-3 py-2 border rounded-md">
                    </div>
                </div>
                <button type="submit" class="w-full py-2 px-4 bg-blue-600 text-white rounded-md hover:bg-blue-700">Sign in</button>
            </form>
            <div class="text-center text-xs text-gray-600">
                Admin: admin@retailbi.com / password<br>
                Company: yassinebenmohamed@company.com / password
            </div>
        </div>
    </div>
</body>
</html>
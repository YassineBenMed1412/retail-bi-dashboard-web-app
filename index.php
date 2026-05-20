<?php
require_once __DIR__ . '/includes/functions.php';

// If logged in, redirect to dashboard
if (isLoggedIn()) {
    if ($_SESSION['user_role'] === 'admin') {
        redirect('/retail_bi_dashboard_php/app/views/admin/dashboard.php');
    } else {
        redirect('/retail_bi_dashboard_php/app/views/dashboard/index.php');
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Retail BI Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-blue-600 to-purple-700">
    <div class="min-h-screen container mx-auto px-4 py-16">
        <div class="text-center text-white">
            <h1 class="text-5xl font-bold mb-6">Retail BI Dashboard</h1>
            <p class="text-xl mb-8">Track Sales, Products, Customers & Revenue in Real-Time</p>
            
            <div class="flex justify-center gap-4">
                <a href="/retail_bi_dashboard_php/app/views/auth/login.php" 
                   class="bg-white text-blue-600 px-8 py-3 rounded-lg font-semibold hover:bg-gray-100">
                    Login
                </a>
                <a href="/retail_bi_dashboard_php/app/views/auth/register.php" 
                   class="bg-transparent border-2 border-white text-white px-8 py-3 rounded-lg font-semibold hover:bg-white hover:text-blue-600">
                    Register
                </a>
            </div>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mt-16">
            <div class="bg-white bg-opacity-10 backdrop-blur-lg p-6 rounded-lg">
                <div class="text-4xl mb-4">📊</div>
                <h3 class="text-xl font-bold mb-2">Real-Time Analytics</h3>
                <p>Track KPIs, sales trends, and revenue with interactive charts</p>
            </div>
            
            <div class="bg-white bg-opacity-10 backdrop-blur-lg p-6 rounded-lg">
                <div class="text-4xl mb-4">📦</div>
                <h3 class="text-xl font-bold mb-2">Product Management</h3>
                <p>Manage inventory, categories, and stock levels easily</p>
            </div>
            
            <div class="bg-white bg-opacity-10 backdrop-blur-lg p-6 rounded-lg">
                <div class="text-4xl mb-4">📄</div>
                <h3 class="text-xl font-bold mb-2">Export Reports</h3>
                <p>Generate PDF and Excel reports for your business</p>
            </div>
        </div>
    </div>
</body>
</html>
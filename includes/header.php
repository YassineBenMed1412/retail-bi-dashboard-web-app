<?php
if (!isset($user)) {
    $user = getCurrentUser();
}
if (!isset($isAdmin)) {
    $isAdmin = ($user && $user['role'] === 'admin');
}

// Function to check if a navigation item is active
function isNavActive($path) {
    return strpos($_SERVER['REQUEST_URI'], $path) !== false ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/20' : 'text-slate-400 hover:bg-slate-800/60 hover:text-white';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Retail BI - Premium Analytics</title>
    <!-- Modern Premium Font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }
        /* Custom scrollbar for premium touch */
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        ::-webkit-scrollbar-track {
            background: transparent;
        }
        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 4px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
    </style>
</head>
<body class="bg-slate-50/50 text-slate-800">
    <div class="flex h-screen overflow-hidden">
        
        <!-- Sidebar -->
        <aside class="w-64 bg-slate-900 border-r border-slate-800 text-white flex-shrink-0 flex flex-col justify-between transition-all duration-300">
            <div>
                <!-- Sidebar Header -->
                <div class="p-6 border-b border-slate-800/80 flex items-center space-x-3">
                    <div class="h-9 w-9 rounded-xl bg-gradient-to-tr from-indigo-500 to-violet-600 flex items-center justify-center shadow-lg shadow-indigo-500/30">
                        <span class="text-white font-extrabold text-lg">R</span>
                    </div>
                    <div>
                        <h1 class="text-lg font-bold tracking-tight bg-gradient-to-r from-white via-slate-100 to-slate-300 bg-clip-text text-transparent">Retail BI</h1>
                        <span class="text-xs font-semibold px-2 py-0.5 rounded-full bg-indigo-500/10 text-indigo-400 border border-indigo-500/20">
                            <?= htmlspecialchars($user['role'] ?? 'Guest') ?>
                        </span>
                    </div>
                </div>
                
                <!-- Sidebar Navigation -->
                <nav class="p-4 space-y-1.5">
                    <a href="/retail_bi_dashboard_php/app/views/dashboard/index.php" 
                       class="flex items-center space-x-3 px-4 py-3 rounded-xl font-medium transition-all duration-200 <?= isNavActive('/views/dashboard/') ?>">
                        <span class="text-lg">📊</span>
                        <span>Dashboard</span>
                    </a>
                    <a href="/retail_bi_dashboard_php/app/views/products/index.php" 
                       class="flex items-center space-x-3 px-4 py-3 rounded-xl font-medium transition-all duration-200 <?= isNavActive('/views/products/') ?>">
                        <span class="text-lg">📦</span>
                        <span>Products</span>
                    </a>
                    <a href="/retail_bi_dashboard_php/app/views/sales/index.php" 
                       class="flex items-center space-x-3 px-4 py-3 rounded-xl font-medium transition-all duration-200 <?= isNavActive('/views/sales/') ?>">
                        <span class="text-lg">💰</span>
                        <span>Sales</span>
                    </a>
                    <a href="/retail_bi_dashboard_php/app/views/customers/index.php" 
                       class="flex items-center space-x-3 px-4 py-3 rounded-xl font-medium transition-all duration-200 <?= isNavActive('/views/customers/') ?>">
                        <span class="text-lg">👥</span>
                        <span>Customers</span>
                    </a>
                    <?php if ($isAdmin): ?>
                    <a href="/retail_bi_dashboard_php/app/views/admin/dashboard.php" 
                       class="flex items-center space-x-3 px-4 py-3 rounded-xl font-medium transition-all duration-200 <?= isNavActive('/views/admin/') ?>">
                        <span class="text-lg">⚙️</span>
                        <span>Admin</span>
                    </a>
                    <?php endif; ?>
                </nav>
            </div>
            
            <!-- Sidebar Footer User Profile Info -->
            <div class="p-4 border-t border-slate-800/80 bg-slate-950/40">
                <div class="flex items-center space-x-3">
                    <div class="h-9 w-9 rounded-lg bg-slate-800 border border-slate-700 flex items-center justify-center font-bold text-indigo-400">
                        <?= strtoupper(substr($user['name'] ?? 'G', 0, 1)) ?>
                    </div>
                    <div class="truncate">
                        <p class="text-sm font-semibold text-slate-200 truncate"><?= htmlspecialchars($user['name'] ?? 'Guest') ?></p>
                        <p class="text-xs text-slate-500 truncate"><?= htmlspecialchars($user['email'] ?? '') ?></p>
                    </div>
                </div>
            </div>
        </aside>
        
        <!-- Main Content Wrapper -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Header -->
            <header class="bg-white/80 backdrop-blur-md border-b border-slate-200/80 sticky top-0 z-30">
                <div class="flex items-center justify-between px-8 py-4">
                    <div class="flex items-center space-x-2">
                        <span class="text-slate-400 font-medium">Workspace</span>
                        <span class="text-slate-300">/</span>
                        <span class="text-slate-800 font-semibold">Overview</span>
                    </div>
                    
                    <div class="flex items-center space-x-4">
                        <div class="hidden sm:flex items-center space-x-2 bg-slate-100/80 px-3 py-1.5 rounded-lg border border-slate-200/60">
                            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                            <span class="text-xs font-semibold text-slate-600">Live Sync Enabled</span>
                        </div>
                        
                        <div class="h-6 w-px bg-slate-200"></div>
                        
                        <a href="/retail_bi_dashboard_php/includes/logout.php" 
                           class="flex items-center space-x-1.5 px-3 py-1.5 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-600 font-semibold text-sm transition-colors duration-150">
                            <span>Logout</span>
                            <span>→</span>
                        </a>
                    </div>
                </div>
            </header>
            
            <!-- Main Content Scroll Area -->
            <main class="flex-1 overflow-y-auto p-8">
                <?php if (isset($_SESSION['success'])): ?>
                <div class="bg-emerald-50 border border-emerald-200/60 text-emerald-800 px-4 py-3 rounded-xl mb-6 shadow-sm flex items-center space-x-2 text-sm">
                    <span class="text-lg">✅</span>
                    <span><?= $_SESSION['success'] ?></span>
                </div>
                <?php unset($_SESSION['success']); endif; ?>
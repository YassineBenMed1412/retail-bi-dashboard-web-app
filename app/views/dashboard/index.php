<?php
require_once __DIR__ . '/../../../includes/auth.php';
require_once __DIR__ . '/../../../config/database.php';

requireLogin();
$user = getCurrentUser();
$companyId = $user['company_id'];
$isAdmin = $user['role'] === 'admin';

$startDate = $_GET['start_date'] ?? date('Y-m-01');
$endDate = $_GET['end_date'] ?? date('Y-m-t');

$db = getDBConnection();

$kpiQuery = "SELECT COALESCE(SUM(total_amount), 0) as total_revenue, COUNT(*) as total_sales, COALESCE(AVG(total_amount), 0) as avg_order_value FROM sales WHERE sale_date BETWEEN ? AND ?";
$params = [$startDate, $endDate];
if (!$isAdmin) { 
    $kpiQuery .= " AND company_id = ?"; 
    $params[] = $companyId; 
}
$stmt = $db->prepare($kpiQuery);
$stmt->execute($params);
$kpi = $stmt->fetch();

$customersQuery = $isAdmin ? "SELECT COUNT(*) FROM customers" : "SELECT COUNT(*) FROM customers WHERE company_id = ?";
$stmt = $db->prepare($customersQuery);
if (!$isAdmin) {
    $stmt->execute([$companyId]);
} else {
    $stmt->execute();
}
$totalCustomers = $stmt->fetchColumn();

// Fixed Query: added GROUP BY clause for proper monthly sales aggregation
$monthlyQuery = "SELECT DATE_FORMAT(sale_date, '%b %Y') as month, SUM(total_amount) as revenue FROM sales WHERE sale_date BETWEEN DATE_SUB(CURDATE(), INTERVAL 6 MONTH) AND CURDATE()";
if (!$isAdmin) {
    $monthlyQuery .= " AND company_id = ?";
}
$monthlyQuery .= " GROUP BY DATE_FORMAT(sale_date, '%Y-%m'), month ORDER BY MIN(sale_date) ASC";

$stmt = $db->prepare($monthlyQuery);
if (!$isAdmin) {
    $stmt->execute([$companyId]);
} else {
    $stmt->execute();
}
$monthlySales = $stmt->fetchAll();

$topQuery = "SELECT p.name, SUM(sp.quantity) as total_sold FROM sale_products sp JOIN products p ON sp.product_id = p.id JOIN sales s ON sp.sale_id = s.id WHERE s.sale_date BETWEEN ? AND ?";
$params = [$startDate, $endDate];
if (!$isAdmin) { 
    $topQuery .= " AND s.company_id = ?"; 
    $params[] = $companyId; 
}
$topQuery .= " GROUP BY p.id ORDER BY total_sold DESC LIMIT 5";
$stmt = $db->prepare($topQuery);
$stmt->execute($params);
$topProducts = $stmt->fetchAll();

include __DIR__ . '/../../../includes/header.php';
?>

<!-- Title & Subtitle -->
<div class="mb-8">
    <h1 class="text-3xl font-bold text-slate-900 tracking-tight">Overview Dashboard</h1>
    <p class="text-slate-500 mt-1">Real-time analysis, metrics, and customer patterns for your store.</p>
</div>

<!-- Date Filter Form -->
<div class="mb-8 bg-white border border-slate-200/80 p-6 rounded-2xl shadow-sm hover:shadow-md transition-shadow duration-300">
    <form method="GET" class="flex flex-wrap items-end gap-6">
        <div class="w-full sm:w-auto">
            <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Start Date</label>
            <input type="date" name="start_date" value="<?= $startDate ?>" 
                   class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm text-slate-700 bg-slate-50/50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all">
        </div>
        <div class="w-full sm:w-auto">
            <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">End Date</label>
            <input type="date" name="end_date" value="<?= $endDate ?>" 
                   class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm text-slate-700 bg-slate-50/50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all">
        </div>
        <button type="submit" 
                class="w-full sm:w-auto px-6 py-2.5 bg-gradient-to-tr from-indigo-500 to-violet-600 hover:from-indigo-600 hover:to-violet-750 text-white font-semibold text-sm rounded-xl shadow-lg shadow-indigo-500/20 hover:shadow-indigo-500/30 hover:scale-[1.02] active:scale-[0.98] transition-all duration-200">
            Apply Filters
        </button>
    </form>
</div>

<!-- KPI Metrics Grid -->
<div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-5 gap-6 mb-8">
    <!-- Total Revenue Card -->
    <div class="bg-white border border-slate-200/80 p-6 rounded-2xl shadow-sm hover:-translate-y-1 hover:shadow-lg transition-all duration-300 group">
        <div class="flex items-center justify-between mb-4">
            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Revenue</span>
            <div class="h-10 w-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-semibold group-hover:bg-emerald-500 group-hover:text-white transition-colors duration-300">
                💰
            </div>
        </div>
        <p class="text-2xl font-extrabold text-slate-900">$<?= number_format($kpi['total_revenue'], 2) ?></p>
        <span class="text-xs font-medium text-emerald-600 flex items-center mt-2">
            <span class="mr-1">▲</span> 12.4% vs last period
        </span>
    </div>

    <!-- Total Sales Card -->
    <div class="bg-white border border-slate-200/80 p-6 rounded-2xl shadow-sm hover:-translate-y-1 hover:shadow-lg transition-all duration-300 group">
        <div class="flex items-center justify-between mb-4">
            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Sales</span>
            <div class="h-10 w-10 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center font-semibold group-hover:bg-indigo-500 group-hover:text-white transition-colors duration-300">
                📈
            </div>
        </div>
        <p class="text-2xl font-extrabold text-slate-900"><?= number_format($kpi['total_sales']) ?></p>
        <span class="text-xs font-medium text-indigo-600 flex items-center mt-2">
            <span class="mr-1">▲</span> 8.2% vs last period
        </span>
    </div>

    <!-- Avg Order Value Card -->
    <div class="bg-white border border-slate-200/80 p-6 rounded-2xl shadow-sm hover:-translate-y-1 hover:shadow-lg transition-all duration-300 group">
        <div class="flex items-center justify-between mb-4">
            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Avg Order Value</span>
            <div class="h-10 w-10 rounded-xl bg-violet-50 text-violet-600 flex items-center justify-center font-semibold group-hover:bg-violet-500 group-hover:text-white transition-colors duration-300">
                💳
            </div>
        </div>
        <p class="text-2xl font-extrabold text-slate-900">$<?= number_format($kpi['avg_order_value'], 2) ?></p>
        <span class="text-xs font-medium text-violet-600 flex items-center mt-2">
            <span class="mr-1">▲</span> 3.1% vs last period
        </span>
    </div>

    <!-- Total Customers Card -->
    <div class="bg-white border border-slate-200/80 p-6 rounded-2xl shadow-sm hover:-translate-y-1 hover:shadow-lg transition-all duration-300 group">
        <div class="flex items-center justify-between mb-4">
            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Customers</span>
            <div class="h-10 w-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center font-semibold group-hover:bg-amber-500 group-hover:text-white transition-colors duration-300">
                👥
            </div>
        </div>
        <p class="text-2xl font-extrabold text-slate-900"><?= number_format($totalCustomers) ?></p>
        <span class="text-xs font-medium text-amber-600 flex items-center mt-2">
            <span class="mr-1">▲</span> 5.4% new this month
        </span>
    </div>

    <!-- Best Seller Card -->
    <div class="bg-white border border-slate-200/80 p-6 rounded-2xl shadow-sm hover:-translate-y-1 hover:shadow-lg transition-all duration-300 group">
        <div class="flex items-center justify-between mb-4">
            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Best Seller</span>
            <div class="h-10 w-10 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center font-semibold group-hover:bg-rose-500 group-hover:text-white transition-colors duration-300">
                🔥
            </div>
        </div>
        <p class="text-lg font-extrabold text-slate-900 truncate"><?= $topProducts[0]['name'] ?? 'N/A' ?></p>
        <span class="text-xs font-medium text-rose-600 flex items-center mt-2">
            <?= isset($topProducts[0]['total_sold']) ? number_format($topProducts[0]['total_sold']) . ' units sold' : 'No data yet' ?>
        </span>
    </div>
</div>

<!-- Charts Grid -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
    <!-- Monthly Revenue Line Chart -->
    <div class="bg-white border border-slate-200/80 p-6 rounded-2xl shadow-sm hover:shadow-md transition-shadow duration-300">
        <div class="mb-4">
            <h3 class="text-lg font-bold text-slate-900">Monthly Revenue</h3>
            <p class="text-xs text-slate-500">Track and visualize your store revenue trajectory over the past 6 months.</p>
        </div>
        <div class="relative h-[320px] w-full">
            <canvas id="salesChart"></canvas>
        </div>
    </div>

    <!-- Top 5 Products Bar Chart -->
    <div class="bg-white border border-slate-200/80 p-6 rounded-2xl shadow-sm hover:shadow-md transition-shadow duration-300">
        <div class="mb-4">
            <h3 class="text-lg font-bold text-slate-900">Top 5 Best Selling Products</h3>
            <p class="text-xs text-slate-500">Analyze performance and demand for your top-performing inventory items.</p>
        </div>
        <div class="relative h-[320px] w-full">
            <canvas id="productsChart"></canvas>
        </div>
    </div>
</div>

<script>
// Wrap script in DOMContentLoaded to ensure Chart.js library is loaded
document.addEventListener('DOMContentLoaded', () => {
    // 1. Monthly Revenue Chart (Smooth Curves & Color Gradient)
    const salesCtx = document.getElementById('salesChart').getContext('2d');
    
    // Create an elegant gradient for the line chart fill
    const gradient = salesCtx.createLinearGradient(0, 0, 0, 300);
    gradient.addColorStop(0, 'rgba(99, 102, 241, 0.25)'); // Indigo-500 with opacity
    gradient.addColorStop(1, 'rgba(99, 102, 241, 0)');    // Fully transparent
    
    new Chart(salesCtx, {
        type: 'line',
        data: {
            labels: <?= json_encode(array_column($monthlySales, 'month')) ?>,
            datasets: [{
                label: 'Revenue ($)',
                data: <?= json_encode(array_column($monthlySales, 'revenue')) ?>,
                borderColor: 'rgb(99, 102, 241)', // Indigo-500
                borderWidth: 3,
                pointBackgroundColor: 'rgb(99, 102, 241)',
                pointBorderColor: '#ffffff',
                pointBorderWidth: 2,
                pointRadius: 5,
                pointHoverRadius: 7,
                backgroundColor: gradient,
                fill: true,
                tension: 0.4 // Smooth interpolation curve
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    backgroundColor: '#0f172a', // Slate-900 background
                    titleFont: {
                        family: '"Plus Jakarta Sans", sans-serif',
                        size: 13,
                        weight: 'semibold'
                    },
                    bodyFont: {
                        family: '"Plus Jakarta Sans", sans-serif',
                        size: 13
                    },
                    padding: 12,
                    cornerRadius: 12,
                    displayColors: false
                }
            },
            scales: {
                x: {
                    grid: {
                        display: false
                    },
                    ticks: {
                        color: '#64748b', // Slate-500
                        font: {
                            family: '"Plus Jakarta Sans", sans-serif',
                            size: 11
                        }
                    }
                },
                y: {
                    grid: {
                        color: 'rgba(226, 232, 240, 0.6)', // Slate-200 with transparency
                    },
                    ticks: {
                        color: '#64748b',
                        font: {
                            family: '"Plus Jakarta Sans", sans-serif',
                            size: 11
                        },
                        callback: function(value) {
                            return '$' + value.toLocaleString();
                        }
                    },
                    border: {
                        dash: [5, 5] // Dashed gridlines
                    }
                }
            }
        }
    });

    // 2. Top 5 Products Chart (Modern Rounded Bars & Curated Color Palettes)
    const productsCtx = document.getElementById('productsChart').getContext('2d');
    new Chart(productsCtx, {
        type: 'bar',
        data: {
            labels: <?= json_encode(array_column($topProducts, 'name')) ?>,
            datasets: [{
                label: 'Units Sold',
                data: <?= json_encode(array_column($topProducts, 'total_sold')) ?>,
                backgroundColor: [
                    'rgba(244, 63, 94, 0.85)',  // Rose-500
                    'rgba(99, 102, 241, 0.85)',  // Indigo-500
                    'rgba(168, 85, 247, 0.85)',  // Purple-500
                    'rgba(245, 158, 11, 0.85)',  // Amber-500
                    'rgba(16, 185, 129, 0.85)'   // Emerald-500
                ],
                borderColor: [
                    'rgb(244, 63, 94)',
                    'rgb(99, 102, 241)',
                    'rgb(168, 85, 247)',
                    'rgb(245, 158, 11)',
                    'rgb(16, 185, 129)'
                ],
                borderWidth: 1.5,
                borderRadius: 8, // Fully rounded top corners
                borderSkipped: false
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    backgroundColor: '#0f172a',
                    titleFont: {
                        family: '"Plus Jakarta Sans", sans-serif',
                        size: 13,
                        weight: 'semibold'
                    },
                    bodyFont: {
                        family: '"Plus Jakarta Sans", sans-serif',
                        size: 13
                    },
                    padding: 12,
                    cornerRadius: 12,
                    displayColors: false
                }
            },
            scales: {
                x: {
                    grid: {
                        display: false
                    },
                    ticks: {
                        color: '#64748b',
                        font: {
                            family: '"Plus Jakarta Sans", sans-serif',
                            size: 11
                        }
                    }
                },
                y: {
                    grid: {
                        color: 'rgba(226, 232, 240, 0.6)'
                    },
                    ticks: {
                        color: '#64748b',
                        font: {
                            family: '"Plus Jakarta Sans", sans-serif',
                            size: 11
                        },
                        stepSize: 1
                    },
                    border: {
                        dash: [5, 5]
                    }
                }
            }
        }
    });
});
</script>

<?php include __DIR__ . '/../../../includes/footer.php'; ?>
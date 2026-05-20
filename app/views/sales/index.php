<?php
require_once __DIR__ . '/../../../includes/auth.php';
requireLogin();
$user = getCurrentUser();
require_once __DIR__ . '/../../../config/database.php';

$companyId = $user['company_id'];
$isAdmin = $user['role'] === 'admin';

$db = getDBConnection();

$query = "SELECT s.*, c.name as customer_name FROM sales s LEFT JOIN customers c ON s.customer_id = c.id";
$params = [];
if (!$isAdmin) { 
    $query .= " WHERE s.company_id = ?"; 
    $params[] = $companyId; 
}
$query .= " ORDER BY s.sale_date DESC";
$stmt = $db->prepare($query);
$stmt->execute($params);
$sales = $stmt->fetchAll();

include __DIR__ . '/../../../includes/header.php';
?>

<!-- Title Header & Actions -->
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
    <div>
        <h1 class="text-3xl font-bold text-slate-900 tracking-tight">Sales</h1>
        <p class="text-slate-500 mt-1">Track and manage invoices and customer orders.</p>
    </div>
    <a href="/retail_bi_dashboard_php/app/views/sales/create.php" 
       class="w-full sm:w-auto text-center bg-gradient-to-tr from-indigo-500 to-violet-600 hover:from-indigo-600 hover:to-violet-750 text-white font-semibold text-sm rounded-xl px-5 py-2.5 shadow-lg shadow-indigo-500/20 hover:shadow-indigo-500/30 hover:scale-[1.02] active:scale-[0.98] transition-all duration-200">
        + Add Sale
    </a>
</div>

<!-- Table Section -->
<div class="bg-white border border-slate-200/80 rounded-2xl shadow-sm overflow-hidden hover:shadow-md transition-shadow duration-300">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-slate-100">
            <thead class="bg-slate-50/70 border-b border-slate-200/60">
                <tr>
                    <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Invoice</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Date</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Customer</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Amount</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                <?php if (empty($sales)): ?>
                <tr>
                    <td colspan="5" class="px-6 py-8 text-center text-sm text-slate-400 font-medium">
                        No sales recorded yet. Click "+ Add Sale" to get started!
                    </td>
                </tr>
                <?php endif; ?>
                <?php foreach ($sales as $sale): ?>
                <tr class="hover:bg-slate-50/30 transition-colors duration-150">
                    <td class="px-6 py-4 font-semibold text-slate-800 text-sm font-mono"><?= htmlspecialchars($sale['invoice_number']) ?></td>
                    <td class="px-6 py-4 text-slate-600 text-sm"><?= htmlspecialchars($sale['sale_date']) ?></td>
                    <td class="px-6 py-4 text-slate-700 text-sm font-medium"><?= htmlspecialchars($sale['customer_name'] ?? 'Walk-in Customer') ?></td>
                    <td class="px-6 py-4 text-slate-850 text-sm font-extrabold">$<?= number_format($sale['total_amount'], 2) ?></td>
                    <td class="px-6 py-4 text-sm">
                        <?php if ($sale['status'] === 'completed'): ?>
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-600 border border-emerald-100">
                            Completed
                        </span>
                        <?php elseif ($sale['status'] === 'pending'): ?>
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-amber-50 text-amber-600 border border-amber-100 animate-pulse">
                            Pending
                        </span>
                        <?php else: ?>
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-slate-100 text-slate-600 border border-slate-200">
                            <?= htmlspecialchars($sale['status']) ?>
                        </span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php include __DIR__ . '/../../../includes/footer.php'; ?>
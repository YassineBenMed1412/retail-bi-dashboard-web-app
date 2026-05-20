<?php
require_once __DIR__ . '/../../../includes/auth.php';
requireLogin();
$user = getCurrentUser();
require_once __DIR__ . '/../../../config/database.php';

$companyId = $user['company_id'];
$isAdmin = $user['role'] === 'admin';

$db = getDBConnection();

$query = "SELECT p.*, c.name as category_name, com.name as company_name FROM products p JOIN categories c ON p.category_id = c.id JOIN companies com ON p.company_id = com.id";
$params = [];
if (!$isAdmin) { 
    $query .= " WHERE p.company_id = ?"; 
    $params[] = $companyId; 
}
$stmt = $db->prepare($query);
$stmt->execute($params);
$products = $stmt->fetchAll();

include __DIR__ . '/../../../includes/header.php';
?>

<!-- Title Header & Actions -->
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
    <div>
        <h1 class="text-3xl font-bold text-slate-900 tracking-tight">Products</h1>
        <p class="text-slate-500 mt-1">Manage and track your product inventory details.</p>
    </div>
    <a href="/retail_bi_dashboard_php/app/views/products/create.php" 
       class="w-full sm:w-auto text-center bg-gradient-to-tr from-indigo-500 to-violet-600 hover:from-indigo-600 hover:to-violet-750 text-white font-semibold text-sm rounded-xl px-5 py-2.5 shadow-lg shadow-indigo-500/20 hover:shadow-indigo-500/30 hover:scale-[1.02] active:scale-[0.98] transition-all duration-200">
        + Add Product
    </a>
</div>

<!-- Table Section -->
<div class="bg-white border border-slate-200/80 rounded-2xl shadow-sm overflow-hidden hover:shadow-md transition-shadow duration-300">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-slate-100">
            <thead class="bg-slate-50/70 border-b border-slate-200/60">
                <tr>
                    <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Name</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">SKU</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Price</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Stock</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Category</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                <?php if (empty($products)): ?>
                <tr>
                    <td colspan="6" class="px-6 py-8 text-center text-sm text-slate-400 font-medium">
                        No products available yet. Click "+ Add Product" to get started!
                    </td>
                </tr>
                <?php endif; ?>
                <?php foreach ($products as $product): ?>
                <tr class="hover:bg-slate-50/30 transition-colors duration-150">
                    <td class="px-6 py-4 font-semibold text-slate-800 text-sm"><?= htmlspecialchars($product['name']) ?></td>
                    <td class="px-6 py-4 text-slate-600 text-sm font-mono"><?= htmlspecialchars($product['sku']) ?></td>
                    <td class="px-6 py-4 text-slate-800 text-sm font-medium">$<?= number_format($product['price'], 2) ?></td>
                    <td class="px-6 py-4 text-slate-600 text-sm">
                        <?php if ($product['stock_quantity'] <= 10): ?>
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-rose-50 text-rose-600 border border-rose-100">
                            <?= $product['stock_quantity'] ?> (Low Stock)
                        </span>
                        <?php else: ?>
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-600 border border-emerald-100">
                            <?= $product['stock_quantity'] ?>
                        </span>
                        <?php endif; ?>
                    </td>
                    <td class="px-6 py-4 text-slate-600 text-sm"><?= htmlspecialchars($product['category_name']) ?></td>
                    <td class="px-6 py-4 text-sm">
                        <a href="/retail_bi_dashboard_php/app/views/products/edit.php?id=<?= $product['id'] ?>" 
                           class="inline-flex items-center px-3 py-1 bg-slate-50 hover:bg-slate-100 text-indigo-600 font-semibold rounded-lg text-xs border border-slate-200/60 transition-colors duration-150">
                            Edit
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php include __DIR__ . '/../../../includes/footer.php'; ?>
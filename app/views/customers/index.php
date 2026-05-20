<?php
require_once __DIR__ . '/../../../includes/auth.php';
requireLogin();
$user = getCurrentUser();
require_once __DIR__ . '/../../../config/database.php';

$companyId = $user['company_id'];
$isAdmin = $user['role'] === 'admin';

$db = getDBConnection();

$query = "SELECT * FROM customers";
$params = [];
if (!$isAdmin) { 
    $query .= " WHERE company_id = ?"; 
    $params[] = $companyId; 
}
$stmt = $db->prepare($query);
$stmt->execute($params);
$customers = $stmt->fetchAll();

include __DIR__ . '/../../../includes/header.php';
?>

<!-- Title Header & Actions -->
<div class="mb-8">
    <h1 class="text-3xl font-bold text-slate-900 tracking-tight">Customers</h1>
    <p class="text-slate-500 mt-1">View, track, and manage customer contact profiles.</p>
</div>

<!-- Table Section -->
<div class="bg-white border border-slate-200/80 rounded-2xl shadow-sm overflow-hidden hover:shadow-md transition-shadow duration-300">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-slate-100">
            <thead class="bg-slate-50/70 border-b border-slate-200/60">
                <tr>
                    <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Name</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Email</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Phone</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                <?php if (empty($customers)): ?>
                <tr>
                    <td colspan="3" class="px-6 py-8 text-center text-sm text-slate-400 font-medium">
                        No customer profiles added yet.
                    </td>
                </tr>
                <?php endif; ?>
                <?php foreach ($customers as $customer): ?>
                <tr class="hover:bg-slate-50/30 transition-colors duration-150">
                    <td class="px-6 py-4 font-semibold text-slate-800 text-sm"><?= htmlspecialchars($customer['name']) ?></td>
                    <td class="px-6 py-4 text-slate-600 text-sm"><?= htmlspecialchars($customer['email'] ?? 'N/A') ?></td>
                    <td class="px-6 py-4 text-slate-650 text-sm font-mono"><?= htmlspecialchars($customer['phone'] ?? 'N/A') ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php include __DIR__ . '/../../../includes/footer.php'; ?>
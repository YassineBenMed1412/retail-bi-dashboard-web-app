<?php
require_once __DIR__ . '/../../../includes/auth.php';
requireRole('admin');
$user = getCurrentUser();
include __DIR__ . '/../../../includes/header.php';
?>
<div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded mb-6">
    <strong>Admin Panel:</strong> Full access to all companies data
</div>
<h1 class="text-3xl font-bold mb-6">Admin Dashboard</h1>
<div class="bg-white p-6 rounded-lg shadow">
    <h2 class="text-xl font-semibold mb-4">Admin Features Coming Soon</h2>
    <p>Manage users, companies, and view all reports</p>
</div>
<?php include __DIR__ . '/../../../includes/footer.php'; ?>
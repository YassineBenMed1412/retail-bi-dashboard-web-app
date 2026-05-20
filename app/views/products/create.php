<?php
require_once __DIR__ . '/../../../includes/auth.php';
requireLogin();
$user = getCurrentUser();
require_once __DIR__ . '/../../../config/database.php';

$db = getDBConnection();
$categories = $db->query("SELECT id, name FROM categories")->fetchAll();
$company_id = $user['company_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $db->prepare("INSERT INTO products (company_id, category_id, name, sku, price, stock_quantity) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$company_id, $_POST['category_id'], $_POST['name'], $_POST['sku'], $_POST['price'], $_POST['stock_quantity']]);
    $_SESSION['success'] = 'Product created!';
    redirect('/retail_bi_dashboard_php/app/views/products/index.php');
}

include __DIR__ . '/../../../includes/header.php';
?>
<h1 class="text-2xl font-bold mb-6">Add Product</h1>
<form method="POST" class="bg-white p-6 rounded-lg shadow max-w-md">
    <div class="mb-4">
        <label class="block mb-2">Name</label>
        <input name="name" required class="w-full border px-3 py-2 rounded">
    </div>
    <div class="mb-4">
        <label class="block mb-2">SKU</label>
        <input name="sku" required class="w-full border px-3 py-2 rounded">
    </div>
    <div class="mb-4">
        <label class="block mb-2">Price</label>
        <input name="price" type="number" step="0.01" required class="w-full border px-3 py-2 rounded">
    </div>
    <div class="mb-4">
        <label class="block mb-2">Stock</label>
        <input name="stock_quantity" type="number" required class="w-full border px-3 py-2 rounded">
    </div>
    <div class="mb-4">
        <label class="block mb-2">Category</label>
        <select name="category_id" required class="w-full border px-3 py-2 rounded">
            <?php foreach ($categories as $cat): ?>
            <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded">Create</button>
    <a href="/retail_bi_dashboard_php/app/views/products/index.php" class="ml-4">Cancel</a>
</form>
<?php include __DIR__ . '/../../../includes/footer.php'; ?>
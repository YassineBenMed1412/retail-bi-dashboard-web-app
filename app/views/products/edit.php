<?php
require_once __DIR__ . '/../../../includes/auth.php';
requireLogin();
$user = getCurrentUser();
require_once __DIR__ . '/../../../config/database.php';

$db = getDBConnection();
$categories = $db->query("SELECT id, name FROM categories")->fetchAll();
$company_id = $user['company_id'];
$isAdmin = $user['role'] === 'admin';

$id = $_GET['id'] ?? null;
if (!$id) {
    redirect('/retail_bi_dashboard_php/app/views/products/index.php');
}

// Fetch product details
$query = "SELECT * FROM products WHERE id = ?";
$params = [$id];
if (!$isAdmin) {
    $query .= " AND company_id = ?";
    $params[] = $company_id;
}
$stmt = $db->prepare($query);
$stmt->execute($params);
$product = $stmt->fetch();

if (!$product) {
    $_SESSION['error'] = 'Product not found!';
    redirect('/retail_bi_dashboard_php/app/views/products/index.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $db->prepare("UPDATE products SET category_id = ?, name = ?, sku = ?, price = ?, stock_quantity = ? WHERE id = ?");
    $stmt->execute([
        $_POST['category_id'], 
        $_POST['name'], 
        $_POST['sku'], 
        $_POST['price'], 
        $_POST['stock_quantity'],
        $id
    ]);
    $_SESSION['success'] = 'Product updated!';
    redirect('/retail_bi_dashboard_php/app/views/products/index.php');
}

include __DIR__ . '/../../../includes/header.php';
?>
<h1 class="text-2xl font-bold mb-6">Edit Product</h1>
<form method="POST" class="bg-white p-6 rounded-lg shadow max-w-md">
    <div class="mb-4">
        <label class="block mb-2">Name</label>
        <input name="name" required value="<?= htmlspecialchars($product['name']) ?>" class="w-full border px-3 py-2 rounded">
    </div>
    <div class="mb-4">
        <label class="block mb-2">SKU</label>
        <input name="sku" required value="<?= htmlspecialchars($product['sku']) ?>" class="w-full border px-3 py-2 rounded">
    </div>
    <div class="mb-4">
        <label class="block mb-2">Price</label>
        <input name="price" type="number" step="0.01" required value="<?= htmlspecialchars($product['price']) ?>" class="w-full border px-3 py-2 rounded">
    </div>
    <div class="mb-4">
        <label class="block mb-2">Stock</label>
        <input name="stock_quantity" type="number" required value="<?= htmlspecialchars($product['stock_quantity']) ?>" class="w-full border px-3 py-2 rounded">
    </div>
    <div class="mb-4">
        <label class="block mb-2">Category</label>
        <select name="category_id" required class="w-full border px-3 py-2 rounded">
            <?php foreach ($categories as $cat): ?>
            <option value="<?= $cat['id'] ?>" <?= $cat['id'] == $product['category_id'] ? 'selected' : '' ?>><?= htmlspecialchars($cat['name']) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded">Update</button>
    <a href="/retail_bi_dashboard_php/app/views/products/index.php" class="ml-4">Cancel</a>
</form>
<?php include __DIR__ . '/../../../includes/footer.php'; ?>

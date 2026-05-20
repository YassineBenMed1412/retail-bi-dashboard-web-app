<?php
require_once __DIR__ . '/../../../includes/auth.php';
requireLogin();
$user = getCurrentUser();
require_once __DIR__ . '/../../../config/database.php';

$db = getDBConnection();
$company_id = $user['company_id'];
$isAdmin = $user['role'] === 'admin';

// Fetch customers and products for selection
$customersQuery = "SELECT id, name FROM customers";
$productsQuery = "SELECT id, name, price, stock_quantity FROM products";
$params = [];

if (!$isAdmin) {
    $customersQuery .= " WHERE company_id = ?";
    $productsQuery .= " WHERE company_id = ?";
    $params[] = $company_id;
}

$stmt = $db->prepare($customersQuery);
$stmt->execute($params);
$customers = $stmt->fetchAll();

$stmt = $db->prepare($productsQuery);
$stmt->execute($params);
$products = $stmt->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $customer_id = $_POST['customer_id'] ?: null;
    $product_id = $_POST['product_id'];
    $quantity = (int)$_POST['quantity'];
    $payment_method = $_POST['payment_method'] ?: 'cash';
    
    // Fetch product details
    $stmt = $db->prepare("SELECT price, stock_quantity FROM products WHERE id = ?");
    $stmt->execute([$product_id]);
    $prod = $stmt->fetch();
    
    if ($prod && $quantity > 0) {
        $unit_price = $prod['price'];
        $subtotal = $unit_price * $quantity;
        
        // Begin Transaction
        $db->beginTransaction();
        try {
            $invoice_number = 'INV-' . time() . '-' . rand(100, 999);
            
            // Insert Sale
            $stmt = $db->prepare("INSERT INTO sales (company_id, customer_id, user_id, invoice_number, sale_date, total_amount, payment_method, status) VALUES (?, ?, ?, ?, CURDATE(), ?, ?, 'completed')");
            $stmt->execute([
                $company_id ?: 1, // Fallback for admin if null company
                $customer_id,
                $user['id'],
                $invoice_number,
                $subtotal,
                $payment_method
            ]);
            
            $sale_id = $db->lastInsertId();
            
            // Insert Sale Product
            $stmt = $db->prepare("INSERT INTO sale_products (sale_id, product_id, quantity, unit_price, subtotal) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$sale_id, $product_id, $quantity, $unit_price, $subtotal]);
            
            // Update stock
            $stmt = $db->prepare("UPDATE products SET stock_quantity = stock_quantity - ? WHERE id = ?");
            $stmt->execute([$quantity, $product_id]);
            
            $db->commit();
            $_SESSION['success'] = 'Sale added successfully!';
            redirect('/retail_bi_dashboard_php/app/views/sales/index.php');
        } catch (Exception $e) {
            $db->rollBack();
            $error = 'Failed to record sale: ' . $e->getMessage();
        }
    } else {
        $error = 'Invalid product or quantity';
    }
}

include __DIR__ . '/../../../includes/header.php';
?>
<h1 class="text-2xl font-bold mb-6">Add Sale</h1>

<?php if (isset($error)): ?>
<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<form method="POST" class="bg-white p-6 rounded-lg shadow max-w-md">
    <div class="mb-4">
        <label class="block mb-2">Customer</label>
        <select name="customer_id" class="w-full border px-3 py-2 rounded">
            <option value="">Guest (No Registered Customer)</option>
            <?php foreach ($customers as $cust): ?>
            <option value="<?= $cust['id'] ?>"><?= htmlspecialchars($cust['name']) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    
    <div class="mb-4">
        <label class="block mb-2">Product *</label>
        <select name="product_id" required class="w-full border px-3 py-2 rounded">
            <?php foreach ($products as $prod): ?>
            <option value="<?= $prod['id'] ?>"><?= htmlspecialchars($prod['name']) ?> ($<?= number_format($prod['price'], 2) ?> - Stock: <?= $prod['stock_quantity'] ?>)</option>
            <?php endforeach; ?>
        </select>
    </div>
    
    <div class="mb-4">
        <label class="block mb-2">Quantity *</label>
        <input type="number" name="quantity" min="1" required value="1" class="w-full border px-3 py-2 rounded">
    </div>
    
    <div class="mb-4">
        <label class="block mb-2">Payment Method</label>
        <select name="payment_method" class="w-full border px-3 py-2 rounded">
            <option value="cash">Cash</option>
            <option value="card">Card</option>
            <option value="transfer">Bank Transfer</option>
        </select>
    </div>
    
    <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded">Record Sale</button>
    <a href="/retail_bi_dashboard_php/app/views/sales/index.php" class="ml-4">Cancel</a>
</form>

<?php include __DIR__ . '/../../../includes/footer.php'; ?>

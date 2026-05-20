<?php
require_once __DIR__ . '/config/database.php';

try {
    $db = getDBConnection();
    echo "<h1 style='color: green;'>✅ SUCCESS!</h1>";
    echo "<p>Database connected!</p>";
    $stmt = $db->query("SELECT COUNT(*) as count FROM users");
    $result = $stmt->fetch();
    echo "<p>Users: <strong>{$result['count']}</strong></p>";
} catch (Exception $e) {
    echo "<h1 style='color: red;'>❌ FAILED!</h1>";
    echo "<p>Error: " . $e->getMessage() . "</p>";
}
?>
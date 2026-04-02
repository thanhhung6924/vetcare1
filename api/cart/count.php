<?php
ini_set('display_errors', 0);
error_reporting(E_ALL);

ob_start();

session_start();

// Dùng DOCUMENT_ROOT sẽ luôn đúng bất kể file api nằm ở đâu
$db_path = $_SERVER['DOCUMENT_ROOT'] . '/includes/db.php';
if (file_exists($db_path)) {
    require_once $db_path;
} else {
    require_once '../../includes/db.php'; 
}

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    if (ob_get_length()) ob_clean();
    echo json_encode([
        'count' => 0,
        'items' => [],
        'total' => 0
    ]);
    exit;
}

$user_id = $_SESSION['user_id'];

// Lấy giỏ hàng hiện tại
$cart_query = "SELECT o.order_id, o.total, oi.product_id 
               FROM orders o 
               LEFT JOIN order_items oi ON o.order_id = oi.order_id 
               WHERE o.user_id = ? AND o.status = 'cart'";

$stmt = $conn->prepare($cart_query);

if (!$stmt) {
    if (ob_get_length()) ob_clean();
    echo json_encode([
        'count' => 0,
        'items' => [],
        'total' => 0,
        'error' => 'Database query failed' 
    ]);
    exit;
}

$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();

$items = [];
$total = 0;

while ($row = $result->fetch_assoc()) {
    if ($row['product_id']) {
        $items[$row['product_id']] = true; 
    }
    $total = $row['total'];
}
if (ob_get_length()) ob_clean(); 

echo json_encode([
    'count' => count($items), 
    'items' => array_keys($items),
    'total' => $total ?? 0
]);

<?php
session_start();
require_once '../includes/db.php';

if (!isset($_SESSION['user_id']) || !isset($_POST['product_id'])) {
    echo json_encode(['success' => false, 'message' => 'Lỗi xác thực']); exit();
}

$user_id = $_SESSION['user_id'];
$product_id = (int)$_POST['product_id'];

// 1. Tìm đơn hàng 'cart' hiện tại
$stmt = $conn->prepare("SELECT order_id FROM orders WHERE user_id = ? AND status = 'cart' LIMIT 1");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$order = $stmt->get_result()->fetch_assoc();
$order_id = $order['order_id'];

// 2. Lấy giá sản phẩm
$stmt = $conn->prepare("SELECT price FROM products WHERE product_id = ?");
$stmt->bind_param("i", $product_id);
$stmt->execute();
$product = $stmt->get_result()->fetch_assoc();
$price = $product['price'];

// 3. Check xem có trong giỏ chưa, có thì tăng qty, chưa thì insert
$stmt = $conn->prepare("SELECT item_id, quantity FROM order_items WHERE order_id = ? AND product_id = ?");
$stmt->bind_param("ii", $order_id, $product_id);
$stmt->execute();
$item = $stmt->get_result()->fetch_assoc();

if ($item) {
    $new_qty = $item['quantity'] + 1;
    $stmt = $conn->prepare("UPDATE order_items SET quantity = ? WHERE item_id = ?");
    $stmt->bind_param("ii", $new_qty, $item['item_id']);
} else {
    $stmt = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, unit_price) VALUES (?, ?, 1, ?)");
    $stmt->bind_param("iid", $order_id, $product_id, $price);
}

echo json_encode(['success' => $stmt->execute()]);

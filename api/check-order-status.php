<?php
require_once '../includes/db.php';
$order_id = (int)$_GET['order_id'];
$query = "SELECT status FROM orders WHERE order_id = $order_id";
$result = $conn->query($query);
$row = $result->fetch_assoc();
echo json_encode(['status' => $row['status']]);

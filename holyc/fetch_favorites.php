<?php
session_start();
include 'db.php';

// Check if user is logged in
if (!isset($_SESSION['id'])) {
    echo json_encode(['success' => false, 'error' => 'User not logged in']);
    exit();
}

$userId = $_SESSION['id'];

// Query to fetch user favorites
$sql = "SELECT add_product.product_name, add_product.description, add_product.product_price 
        FROM favorites 
        JOIN add_product ON favorites.product_id = add_product.id 
        WHERE favorites.user_id = '$userId'";

$result = $conn->query($sql);

if ($result) {
    $favorites = [];
    while ($row = $result->fetch_assoc()) {
        $favorites[] = $row;
    }

    echo json_encode(['success' => true, 'data' => $favorites]);
} else {
    echo json_encode(['success' => false, 'error' => $conn->error]);
}
?>

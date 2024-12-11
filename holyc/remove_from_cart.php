<?php
session_start();
include 'db.php';

// Ensure only authorized buyers can remove items from their cart
if (!isset($_SESSION['id']) || $_SESSION['role'] !== 'buyer') {
    header("Location: login.php");
    exit;
}

// Handle form submission to remove an item
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cart_item_id = intval($_POST['cart_item_id']);
    $buyer_id = $_SESSION['id'];

    $delete_query = $conn->prepare("DELETE FROM cart WHERE id = ? AND buyer_id = ?");
    $delete_query->bind_param("ii", $cart_item_id, $buyer_id);

    if ($delete_query->execute()) {
        $_SESSION['success_message'] = "Item removed from your cart.";
    } else {
        $_SESSION['error_message'] = "Error removing item.";
    }

    header("Location: view_cart.php");
    exit;
}
?>

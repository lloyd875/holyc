<?php
session_start();
include 'db.php';

// Ensure only authorized buyers can add to their cart
if (!isset($_SESSION['id']) || $_SESSION['role'] !== 'buyer') {
    header("Location: login.php");
    exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $buyer_id = $_SESSION['id'];
    $product_id = intval($_POST['product_id']);
    $quantity = intval($_POST['quantity']);

    if ($quantity <= 0) {
        $_SESSION['error_message'] = "Invalid quantity.";
        header("Location: buyer.php");
        exit;
    }

    // Check if product is already in the cart
    $check_query = $conn->prepare("SELECT * FROM cart WHERE buyer_id = ? AND product_id = ?");
    $check_query->bind_param("ii", $buyer_id, $product_id);
    $check_query->execute();
    $result = $check_query->get_result();

    if ($result->num_rows > 0) {
        // Update the existing cart entry
        $update_query = $conn->prepare("UPDATE cart SET quantity = quantity + ? WHERE buyer_id = ? AND product_id = ?");
        $update_query->bind_param("iii", $quantity, $buyer_id, $product_id);
    } else {
        // Insert a new item into the cart
        $insert_query = $conn->prepare("INSERT INTO cart (buyer_id, product_id, quantity) VALUES (?, ?, ?)");
        $insert_query->bind_param("iii", $buyer_id, $product_id, $quantity);
    }

    if ($result->num_rows > 0) {
        $update_query->execute();
    } else {
        $insert_query->execute();
    }

    $_SESSION['success_message'] = "Product added to cart successfully!";
    header("Location: buyer.php");
} else {
    header("Location: buyer.php");
}
?>

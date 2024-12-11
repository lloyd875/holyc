<?php
session_start();
include 'db.php';

// Ensure only authorized buyers can access this functionality
if (!isset($_SESSION['id']) || $_SESSION['role'] !== 'buyer') {
    header("Location: login.php");
    exit;
}

// Check if the order ID is set
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id'])) {
    $order_id = intval($_POST['order_id']);

    // Update the order status to 'Received' in the database
    $updateQuery = $conn->prepare("UPDATE orders SET order_status = 'Received' WHERE id = ?");
    $updateQuery->bind_param("i", $order_id);

    if ($updateQuery->execute()) {
        $_SESSION['success_message'] = "Order received successfully.";
    } else {
        $_SESSION['error_message'] = "Failed to process the order. Please try again.";
    }

    // Redirect back to the main cart page
    header("Location: view_cart.php");
    exit;
} else {
    $_SESSION['error_message'] = "Invalid request.";
    header("Location: view_cart.php");
    exit;
}
?>

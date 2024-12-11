<?php
session_start();
include 'db.php';

// Ensure the user is logged in and is a seller
if (!isset($_SESSION['id']) || $_SESSION['role'] !== 'seller') {
    header("Location: login.php");
    exit;
}

// Check if the order ID is provided
if (isset($_GET['id'])) {
    $order_id = $_GET['id'];

    // Prepare and execute the query to approve the order
    $updateOrderQuery = "UPDATE orders SET order_status = 'Approved' WHERE id = ?";
    $stmt = $conn->prepare($updateOrderQuery);
    $stmt->bind_param("i", $order_id);

    if ($stmt->execute()) {
        // Redirect with a success message
        header("Location: orders.php?message=Order approved successfully.");
    } else {
        // Redirect with an error message
        header("Location: orders.php?error=Failed to approve the order.");
    }
} else {
    // Redirect if no ID is provided
    header("Location: orders.php?error=Invalid request.");
}

$conn->close();
exit;
?>

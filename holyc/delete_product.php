<?php
session_start();
include 'db.php';

// Ensure only authorized users can delete
if (!isset($_SESSION['id']) || $_SESSION['role'] !== 'seller') {
    header("Location: login.php");
    exit;
}

// Handle Delete
if (isset($_GET['id'])) {
    $product_id = intval($_GET['id']);
    $delete_query = $conn->prepare("DELETE FROM add_product WHERE id = ?");
    $delete_query->bind_param("i", $product_id);

    if ($delete_query->execute()) {
        $_SESSION['success_message'] = "Product deleted successfully.";
        header("Location: seller.php");
    } else {
        $_SESSION['error_message'] = "Error deleting product.";
        header("Location: seller.php");
    }
} else {
    $_SESSION['error_message'] = "Invalid request.";
    header("Location: seller.php");
}
?>

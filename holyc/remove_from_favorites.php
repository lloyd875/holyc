<?php
session_start();
include 'db.php';

// Check if user is logged in
if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit();
}

// Handle the form submission
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['product_id'])) {
    $userId = $_SESSION['id'];
    $productId = $conn->real_escape_string($_POST['product_id']);

    // Remove the product from favorites
    $sql = "DELETE FROM favorites WHERE user_id = '$userId' AND product_id = '$productId'";
    
    if ($conn->query($sql) === TRUE) {
        header("Location: buyer.php?message=removed_from_favorites");
    } else {
        header("Location: buyer.php?error=failed_to_remove");
    }
} else {
    header("Location: index.php");
}
?>
